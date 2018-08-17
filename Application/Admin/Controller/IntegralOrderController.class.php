<?php

namespace Admin\Controller;

class IntegralOrderController extends BaseController {

    protected $integral_order_model;

    public function __construct() {
        parent::__construct();
        $this->integral_order_model = D('Admin/IntegralOrder');
        
    }

    /**
     * 订单列表
     */
    public function index($field_name = 'io.order_no', $keyword = '', $start_date = '', $end_date = '') {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['like', "%{$keyword}%"];
        }
        $this->assign('keyword', $keyword);
        if ($start_date && $end_date) {
            $map['io.create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['io.create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['io.create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $page_size = C('PAGE_SIZE');
        $count = $this->integral_order_model
                ->alias('io')
                ->where($map)
                ->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $order_list = $this->integral_order_model
                ->alias('io')
                ->join(C('DB_PREFIX') . 'user u on u.id=io.user_id', 'LEFT')
                ->field('io.*,u.nickname user_nickname')
                ->where($map)
                ->order('io.create_time desc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('order_list', $order_list);
        $this->assign('page', $show);
        $this->display();
    }
    
    /**
     * 订单详情
     */
    public function detail($id) {
        $orderinfo = $this->integral_order_model
                ->alias('io')
                ->join(C('DB_PREFIX') . 'user u on u.id=io.user_id', 'LEFT')
                ->field('io.*,u.nickname user_nickname')
                ->where("io.id={$id}")
                ->find();
        $this->assign('orderinfo', $orderinfo);
        $this->display();
    }
    
     /**
     * 订单备注
     */
    public function sellerMemo($id = 0) {
        $orderinfo = $this->integral_order_model->where("id={$id}")->field('id,seller_memo')->find();
        if (IS_POST) {
            if (!$orderinfo) {
                $this->error('订单不存在');
            }
            $seller_memo = I('post.seller_memo', '');
            $result = $this->integral_order_model->where("id={$id}")->setField('seller_memo', $seller_memo);
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
