<?php

namespace Admin\Controller;

class OrderController extends BaseController {

    protected $order_model;
    protected $order_log_model;
    protected $payment_list;

    public function __construct() {
        parent::__construct();
        $this->order_model = D('Admin/Order');
        $this->order_log_model = M('order_log');
        $this->payment_list = C('PAYMENT_LIST');
        $this->assign('payment_list', $this->payment_list);
        
    }

    /**
     * 订单列表
     */
    public function index($field_name = 'o.order_no', $keyword = '', $start_date = '', $end_date = '', $status = -1) {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['like', "%{$keyword}%"];
        }
        $this->assign('keyword', $keyword);
        if ($start_date && $end_date) {
            $map['o.create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['o.create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['o.create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        if ($status > -1) {
            switch ($status){
                case 1:
                    $map['o.payment_status'] = ['eq', 0];
                    break;
                case 2:
                    $map['o.payment_status'] = ['eq', 1];
                    break;
            }
        }
        $this->assign('status', $status);
        $page_size = C('PAGE_SIZE');
        $count = $this->order_model
                ->alias('o')
                ->where($map)
                ->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $order_list = $this->order_model
                ->alias('o')
                ->join(C('DB_PREFIX') . 'user u on u.id=o.user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'user ru on ru.id=o.recommender_id', 'LEFT')
                ->field('o.*,u.nickname user_nickname,ru.nickname recommender_nickname')
                ->where($map)
                ->order('o.create_time desc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('order_list', $order_list);
        $this->assign('page', $show);
        $nav_list = [
            -1 => '全部',
            1 => '待付款',
            2 => '已付款'
        ];
        $this->assign('nav_list', $nav_list);
        $this->display();
    }
    
    /**
     * 订单详情
     */
    public function detail($id) {
        $orderinfo = $this->order_model
                ->alias('o')
                ->join(C('DB_PREFIX') . 'user u on u.id=o.user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'user ru on ru.id=o.recommender_id', 'LEFT')
                ->field('o.*,u.nickname user_nickname,ru.nickname recommender_nickname')
                ->where("o.id={$id}")
                ->find();
        $this->assign('orderinfo', $orderinfo);
        $order_goods_list = M('order_goods')
                ->where("order_id={$id}")
                ->select();
        $this->assign('order_goods_list', $order_goods_list);
        $order_log_list = $this->order_log_model
                ->field('operator_name,content,create_time')
                ->where("order_id={$id}")
                ->order('create_time asc')
                ->select();
        $this->assign('order_log_list', $order_log_list);
        $commission_log_list = M('user_money')
                ->alias('um')
                ->join(C('DB_PREFIX') . 'user u on u.id=um.user_id', 'LEFT')
                ->field('um.user_id,um.money,um.description,um.create_time,u.nickname')
                ->where("um.data_from=1 and um.data_id={$id}")
                ->order('um.create_time asc')
                ->select();
        $this->assign('commission_log_list', $commission_log_list);
        $this->display();
    }
    
     /**
     * 订单备注
     */
    public function sellerMemo($id = 0) {
        $orderinfo = $this->order_model->where("id={$id}")->field('id,seller_memo')->find();
        if (IS_POST) {
            if (!$orderinfo) {
                $this->error('订单不存在');
            }
            $seller_memo = I('post.seller_memo', '');
            $result = $this->order_model->where("id={$id}")->setField('seller_memo', $seller_memo);
            if ($result !== false) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }
        $this->assign('orderinfo', $orderinfo);
        $this->display();
    }

}
