<?php

namespace Wx\Controller;

class IntegralOrderController extends BaseController {

    protected $integral_order_model;

    public function __construct() {
        parent::__construct();
        $this->integral_order_model = D('Wx/IntegralOrder');
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
            if ($goods['integral_select'] == 1) {
                $this->error('商品不支持积分兑换');
            }
            $user_goods_model = M('user_goods');
            $user_goods = $user_goods_model->where("user_id={$this->userinfo['id']}")->find();
            $user_goods_ids_arr = $user_goods ? explode(',', $user_goods['goods_id']) : [];
            //购买过此商品
            if (in_array($goods_id, $user_goods_ids_arr)) {
                $this->error('商品可直接查看，无需兑换');
            }
            if ($this->userinfo['integral'] < $goods['integral_price']) {
                $this->error('抱歉，您的积分不足以兑换此商品');
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
            //生成订单号
            $order_no = build_order_no();
            //创建数据
            $data = [
                'order_no' => $order_no,
                'user_id' => $this->userinfo['id'],
                'goods_id' => $goods['id'],
                'goods_name' => $goods['name'],
                'goods_thumb' => $goods['thumb'],
                'goods_unit' => $goods['unit'],
                'goods_tag' => $goods_tag,
                'goods_code' => $goods['code'],
                'goods_integral' => $goods['integral_price'],
                'remark' => '',
                'create_time' => get_date_time()
            ];
            $this->integral_order_model->startTrans();
            try {
                //添加订单
                $order_id = $this->integral_order_model->add($data);
                $balance = $this->userinfo['integral'] - $goods['integral_price'];
                //添加积分记录
                $data = [
                    'user_id' => $this->userinfo['id'],
                    'type' => 5,
                    'integral' => -$goods['integral_price'],
                    'balance' => $balance,
                    'data_from' => 2,
                    'data_id' => $order_id,
                    'title' => '积分兑换商品',
                    'description' => '积分兑换商品',
                    'create_time' => get_date_time()
                ];
                M('user_integral')->add($data);
                //更新用户积分
                $data = [
                    'integral' => $balance
                ];
                M('user_account')->where("user_id={$this->userinfo['id']}")->save($data);
                //组合用户商品数组
                if (!in_array($goods['id'], $user_goods_ids_arr)) {
                    $user_goods_ids_arr[] = $goods['id'];
                }
                $user_goods_ids = $user_goods_ids_arr ? implode(',', $user_goods_ids_arr) : '';
                //添加或更新用户商品
                if (!$user_goods) {
                    $data = [
                        'user_id' => $this->userinfo['id'],
                        'goods_id' => $user_goods_ids
                    ];
                    $user_goods_model->add($data);
                } else {
                    $user_goods_model->where("user_id={$this->userinfo['id']}")->setField('goods_id', $user_goods_ids);
                }
                $this->integral_order_model->commit();
                $this->success('兑换成功');
            } catch (Exception $ex) {
                $this->integral_order_model->rollback();
                $this->error('兑换失败');
            }
        }
    }

    /**
     * 积分订单
     */
    public function index() {
        $seo = set_seo('积分订单');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/IntegralOrder/index');
    }

    /**
     * 异步获取订单列表
     */
    public function getListData() {
        if (IS_GET) {
            $page = I('get.page', 1, 'intval');
            $data_list = $this->integral_order_model->getPageList($this->userinfo['id'], $page, 10);
            foreach ($data_list['root'] as &$v) {
                $v['href'] = U('Wx/Goods/iitem', ['id' => $v['goods_id']]);
                $tag_list = $v['goods_tag'] ? explode(',', $v['goods_tag']) : [];
                $v['goods_tag'] = $tag_list ? implode('<i></i>', $tag_list) : '';
            }
            unset($v);
            $this->success($data_list);
        }
    }

}
