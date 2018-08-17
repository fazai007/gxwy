<?php

namespace Wx\Controller;

class OrderController extends BaseController {

    protected $order_model;

    public function __construct() {
        parent::__construct();
        $this->order_model = D('Wx/Order');
    }

    /**
     * 在线支付
     */
    public function payment() {
        $goods_id = I('goods_id', 0, 'intval');
        $goods = D('Wx/Goods')->getDetail($goods_id);
        if (!$goods) {
            $this->alert('商品不存在或已下架');
        }
        $this->assign('goods', $goods);
        $goods_tag_list = [];
        if ($goods['tag']) {
            $goods_tag_data = M('goods_tag')->field('name')
                    ->where("id in ({$goods['tag']})")
                    ->select();
            foreach ($goods_tag_data as $v) {
                $goods_tag_list[] = $v['name'];
            }
        }
        $this->assign('goods_tag_list', $goods_tag_list);
        $payment_list = get_payment_list();
        $this->assign('payment_list', $payment_list);
        $seo = set_seo('在线支付');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Order/payment');
    }

    /**
     * 创建订单
     */
    public function create() {
        if (IS_POST) {
            $goods_model = D('Wx/Goods');
            $goods_id = I('post.goods_id', 0, 'intval');
            $goods = $goods_model->getDetail($goods_id);
            if (!$goods) {
                $this->error('商品不存在或已下架');
            }
            if ($goods['integral_select'] == 1 || $goods['integral_select'] == 3) {
                $this->error('商品不支持直接购买');
            }
            $user_goods_model = M('user_goods');
            $user_goods_ids = $user_goods_model->where("user_id={$this->userinfo['id']}")->getField('goods_id');
            $user_goods_ids_arr = $user_goods_ids ? explode(',', $user_goods_ids) : [];
            //购买过此商品或免费并填写过资料或会免
            if (in_array($goods_id, $user_goods_ids_arr) || ($goods['sale_price'] == 0 && $this->userinfo['name'] && $this->userinfo['mobile']) || ($this->userinfo['level_id'] > 0 && $goods['member_sale_price'] == 0)) {
                $this->error('商品可直接查看，无需购买');
            }
            $payment_type = I('post.payment_type', '');
            $payment_list = get_payment_list();
            if (!$payment_type || !array_key_exists($payment_type, $payment_list)) {
                $this->error('请选择支付方式');
            }
            $goods_tag = '';
            if ($goods['tag']) {
                $goods_tag_data = M('goods_tag')->field('name')
                        ->where("id in ({$goods['tag']})")
                        ->select();
                $goods_tag_list = [];
                foreach ($goods_tag_data as $v) {
                    $goods_tag_list[] = $v['name'];
                }
                $goods_tag = $goods_tag_list ? implode(',', $goods_tag_list) : '';
            }
            $sale_price = $this->userinfo['level_id'] > 0 ? $goods['member_sale_price'] : $goods['sale_price'];
            $goods_amount = $sale_price;
            //总金额
            $total_amount = $goods_amount;
            //需付款金额
            $need_pay_amount = $goods_amount;
            //佣金金额
            $commission = 0;
            if ($goods['is_fy']) {
                $commission = $goods_amount;
            }
            //生成订单号
            $order_no = build_order_no();
            //创建数据
            $data = [
                'order_no' => $order_no,
                'user_id' => $this->userinfo['id'],
                'user_level' => $this->userinfo['level_id'],
                'recommender_id' => $this->userinfo['recommender_id'],
                'goods_amount' => $goods_amount,
                'goods_count' => 1,
                'total_amount' => $total_amount,
                'need_pay_amount' => $need_pay_amount,
                'commission' => $commission,
                'payment_type' => $payment_type,
                'remark' => '',
                'create_time' => get_date_time()
            ];
            $order_goods_model = M('order_goods');
            $this->order_model->startTrans();
            try {
                //添加订单
                $order_id = $this->order_model->add($data);
                //添加订单日志
                $data = [
                    'order_id' => $order_id,
                    'type' => 1,
                    'operator_id' => $this->userinfo['id'],
                    'operator_name' => $this->userinfo['nickname'],
                    'content' => '订单创建成功',
                    'create_time' => get_date_time()
                ];
                M('order_log')->add($data);
                //添加订单商品
                $data = [
                    'order_id' => $order_id,
                    'goods_id' => $goods['id'],
                    'num' => 1,
                    'name' => $goods['name'],
                    'thumb' => $goods['thumb'],
                    'unit' => $goods['unit'],
                    'tag' => $goods_tag,
                    'code' => $goods['code'],
                    'market_price' => $goods['market_price'],
                    'sale_price' => $sale_price,
                    'is_fy' => $goods['is_fy']
                ];
                $order_goods_model->add($data);
                $this->order_model->commit();
            } catch (Exception $ex) {
                $this->order_model->rollback();
                $this->error('订单创建失败');
            }
            //返回数据
            $return_data = [
                'payment_type' => $payment_type,
                'payment_status' => true
            ];
            //创建付款信息
            //支付宝支付
            if ($payment_type == 'alipay') {
                $return_data['payment_status'] = false;
            }
            //微信支付
            else if ($payment_type == 'wxpay') {
                if (!$this->userinfo['openid']) {
                    $return_data['payment_status'] = false;
                } else {
                    //初始化jsApiPay参数
                    $notify_url = U('Api/WxPay/notify', '', true, true);
                    $wxpay_config = [
                        'APPID' => $GLOBALS['SYSTEM']['wxpay_config']['app_id'],
                        'MCHID' => $GLOBALS['SYSTEM']['wxpay_config']['mch_id'],
                        'KEY' => $GLOBALS['SYSTEM']['wxpay_config']['pay_sign_key'],
                        'APPSECRET' => $GLOBALS['SYSTEM']['wxpay_config']['app_secret'],
                        'NOTIFY_URL' => $notify_url,
                        'SSLCERT_PATH' => $GLOBALS['SYSTEM']['wxpay_config']['apiclient_cert'],
                        'SSLKEY_PATH' => $GLOBALS['SYSTEM']['wxpay_config']['apiclient_key']
                    ];
                    \WxPay\lib\WxPayConfig::setConfig($wxpay_config);
                    //初始化JsApiPay
                    $tools = new \WxPay\lib\JsApiPay();
                    $wxpay_api = new \WxPay\lib\WxPayApi();
                    //统一下单
                    $input = new \WxPay\database\WxPayUnifiedOrder();
                    $input->SetBody($order_no);
                    //$input->SetAttach();
                    $input->setOutTradeNo($order_no);
                    $input->setTotalFee($need_pay_amount * 100);
                    //$input->setTimeStart();
                    //$input->setTimeExpire();
                    $input->setNotifyUrl($notify_url);
                    $input->setTradeType('JSAPI');
                    $input->setOpenid($this->userinfo['openid']);
                    $wxpay_result = $wxpay_api->unifiedOrder($input);
                    if ($wxpay_result['return_code'] == 'SUCCESS' && $wxpay_result['result_code'] == 'SUCCESS') {
                        $return_data['payment_info'] = json_decode($tools->getJsApiParameters($wxpay_result), true);
                    } else {
                        $return_data['payment_status'] = false;
                    }
                }
            }
            $this->success($return_data, U('Wx/Goods/item',['id'=>$goods_id]));
        }
    }

    /**
     * 我的订单
     */
    public function index() {
        $this->assign('status', 1);
        $seo = set_seo('我的订单');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Order/index');
    }

    /**
     * 异步获取订单列表
     */
    public function getListData() {
        if (IS_GET) {
            $status = I('get.status', 1, 'intval');
            $page = I('get.page', 1, 'intval');
            $data_list = $this->order_model->getPageList($this->userinfo['id'], $status, $page, 10);
            $order_goods_model = M('order_goods');
            foreach ($data_list['root'] as &$v) {
                $goods_list = $order_goods_model
                        ->where("order_id={$v['id']}")
                        ->select();
                foreach ($goods_list as &$val) {
                    $val['href'] = U('Wx/Goods/item', ['id' => $val['goods_id']]);
                    $tag_list = $val['tag'] ? explode(',', $val['tag']) : [];
                    $val['tag'] = $tag_list ? implode('<i></i>', $tag_list) : '';
                }
                unset($val);
                $v['goods_list'] = $goods_list;
            }
            unset($v);
            $this->success($data_list);
        }
    }

    /**
     * 验证付款
     */
    public function checkPayment($id = 0) {
        if (IS_POST) {
            $orderinfo = $this->order_model->getDetail($id, $this->userinfo['id']);
            if (!$orderinfo) {
                $this->error('订单不存在或已删除');
            }
            if ($orderinfo['payment_status']) {
                $this->error('订单已支付');
            }
            //创建付款信息
            //支付宝支付
            if ($orderinfo['payment_type'] == 'alipay') {
                $this->error('暂不支持支付宝支付');
            }
            //微信支付
            else if ($orderinfo['payment_type'] == 'wxpay') {
                if (!$this->userinfo['openid']) {
                    $this->error('支付失败');
                }
                //初始化jsApiPay参数
                $notify_url = U('Api/WxPay/notify', '', true, true);
                $wxpay_config = [
                    'APPID' => $GLOBALS['SYSTEM']['wxpay_config']['app_id'],
                    'MCHID' => $GLOBALS['SYSTEM']['wxpay_config']['mch_id'],
                    'KEY' => $GLOBALS['SYSTEM']['wxpay_config']['pay_sign_key'],
                    'APPSECRET' => $GLOBALS['SYSTEM']['wxpay_config']['app_secret'],
                    'NOTIFY_URL' => $notify_url,
                    'SSLCERT_PATH' => $GLOBALS['SYSTEM']['wxpay_config']['apiclient_cert'],
                    'SSLKEY_PATH' => $GLOBALS['SYSTEM']['wxpay_config']['apiclient_key']
                ];
                \WxPay\lib\WxPayConfig::setConfig($wxpay_config);
                //初始化JsApiPay
                $tools = new \WxPay\lib\JsApiPay();
                $wxpay_api = new \WxPay\lib\WxPayApi();
                //统一下单
                $input = new \WxPay\database\WxPayUnifiedOrder();
                $input->SetBody($orderinfo['order_no']);
                //$input->SetAttach();
                $input->setOutTradeNo($orderinfo['order_no']);
                $input->setTotalFee($orderinfo['need_pay_amount'] * 100);
                //$input->setTimeStart();
                //$input->setTimeExpire();
                $input->setNotifyUrl($notify_url);
                $input->setTradeType('JSAPI');
                $input->setOpenid($this->userinfo['openid']);
                $wxpay_result = $wxpay_api->unifiedOrder($input);
                if ($wxpay_result['return_code'] == 'SUCCESS' && $wxpay_result['result_code'] == 'SUCCESS') {
                    $payment_info = json_decode($tools->getJsApiParameters($wxpay_result), true);
                } else {
                    $this->error('支付失败');
                }
            }
            $this->success(['payment_type' => $orderinfo['payment_type'], 'payment_info' => $payment_info], U('Wx/Order/index'));
        }
    }

}
