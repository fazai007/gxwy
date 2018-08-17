<?php

namespace Wx\Controller;

class MemberOrderController extends BaseController {

    protected $member_order_model;

    public function __construct() {
        parent::__construct();
        $this->member_order_model = D('Wx/MemberOrder');
    }
    
    /**
     * 验证付款
     */
    public function check() {
        if (IS_POST) {
            if ($this->userinfo['level_id'] > 0) {
                $this->error('您已开通会员');
            }
            $this->success('', U('Wx/MemberOrder/payment'));
        }
    }
    
    /**
     * 在线支付
     */
    public function payment() {
        $payment_list = get_payment_list();
        $this->assign('payment_list', $payment_list);
        $seo = set_seo('在线支付');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/MemberOrder/payment');
    }
    
    /**
     * 创建订单
     */
    public function create() {
        if (IS_POST) {
            $name = I('post.name', '');
            if (!$name) {
                $this->error('请填写姓名');
            }
            $mobile = I('post.mobile', '');
            if (!$mobile || !is_mobile_format($mobile)) {
                $this->error('请填写正确的手机号');
            }
            $payment_type = I('post.payment_type', '');
            $payment_list = get_payment_list();
            if (!$payment_type || !array_key_exists($payment_type, $payment_list)) {
                $this->error('请选择支付方式');
            }
            $member_price = $GLOBALS['SYSTEM']['reg_and_visit']['member_price'];
            //总金额
            $total_amount = $member_price;
            //需付款金额
            $need_pay_amount = $member_price;
            //生成订单号
            $order_no = build_order_no();
            $now_time = date('Y-m-d H:i:s');
            //创建数据
            $data = [
                'order_no' => $order_no,
                'user_id' => $this->userinfo['id'],
                'user_level' => $this->userinfo['level_id'],
                'recommender_id' => $this->userinfo['recommender_id'],
                'total_amount' => $total_amount,
                'need_pay_amount' => $need_pay_amount,
                'name' => $name,
                'mobile' => $mobile,
                'payment_type' => $payment_type,
                'create_time' => $now_time
            ];
            $this->member_order_model->startTrans();
            try {
                //添加订单
                $order_id = $this->member_order_model->add($data);
                //添加订单日志
                $data = [
                    'order_id' => $order_id,
                    'type' => 1,
                    'operator_id' => $this->userinfo['id'],
                    'operator_name' => $this->userinfo['nickname'],
                    'content' => '订单创建成功',
                    'create_time' => $now_time
                ];
                M('member_order_log')->add($data);
                $data = [
                    'name' => $name,
                    'mobile' => $mobile
                ];
                //更新用户信息
                M('user')->where("id={$this->userinfo['id']}")->save($data);
                $this->member_order_model->commit();
            } catch (Exception $ex) {
                $this->member_order_model->rollback();
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
                    $notify_url = U('Api/WxPay/notifyM', '', true, true);
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
            $this->success($return_data, U('Wx/Index/index'));
        }
    }

}
