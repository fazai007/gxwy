<?php

namespace Admin\Controller;

class UserMoneyController extends BaseController {

    protected $user_money_model;
    protected $user_account_model;
    protected $type_list;

    public function __construct() {
        parent::__construct();
        $this->user_money_model = D('Admin/UserMoney');
        $this->user_account_model = D('Admin/UserAccount');
        $this->type_list = [
            1 => '直推佣金',
            2 => '团队佣金',
            3 => '提现',
            4 => '提现退还',
            5 => '调整'
        ];
        $this->assign('type_list', $this->type_list);
    }
    
     /**
     * 云币明细
     */
    public function detail($user_id = 0, $type = -1, $start_date = '', $end_date = '') {
        $map['um.user_id'] = ['eq', $user_id];
        $this->assign('user_id', $user_id);
        if ($type > -1) {
            $map['um.type'] = ['eq', $type];
        }
        $this->assign('type', $type);
        if ($start_date && $end_date) {
            $map['um.create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['um.create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['um.create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $page_size = C('PAGE_SIZE');
        $count = $this->user_money_model
                ->alias('um')
                ->join(C('DB_PREFIX') . 'user u on u.id=um.user_id', 'LEFT')
                ->where($map)
                ->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $money_list = $this->user_money_model
                ->alias('um')
                ->join(C('DB_PREFIX') . 'user u on u.id=um.user_id', 'LEFT')
                ->field('um.*,u.nickname')
                ->where($map)
                ->order('um.create_time desc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('money_list', $money_list);
        $this->assign('page', $show);
        $this->display();
    }
    
     /**
     * 云币调整
     */
    public function recharge($user_id = 0) {
        $user_account = $this->user_account_model->where("user_id={$user_id}")->field('user_id,money,total_money')->find();
        if (IS_POST) {
            if (!$user_account) {
                $this->error('用户不存在');
            }
            $data = $this->user_money_model->create();
            if ($data) {
                $money = $data['money'];
                $balance = $user_account['money'] + $money;
                $data['user_id'] = $user_id;
                $data['type'] = 5;
                $data['balance'] = $balance;
                $data['title'] = '云币调整';
                $data['description'] = $data['description'] ? : '云币调整';
                $this->user_money_model->startTrans();
                try {
                    $this->user_money_model->add($data);
                    $data = [
                        'money' => $balance
                    ];
                    if ($money > 0) {
                        $data['total_money'] = $user_account['total_money'] + $money;
                    }
                    $this->user_account_model->where("user_id={$user_id}")->save($data);
                    $this->user_money_model->commit();
                    $this->success('操作成功');
                } catch (Exception $ex) {
                    $this->user_money_model->rollback();
                    $this->success('操作失败');
                }
            } else {
                $this->error($this->user_money_model->getError());
            }
        }
        $this->assign('user_account', $user_account);
        $this->display();
    }
    
    /**
     * 云币管理
     */
    public function index($field_name = 'um.user_id', $keyword = '', $type = -1, $start_date = '', $end_date = '') {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['eq', $keyword];
        }
        $this->assign('keyword', $keyword);
        if ($type > -1) {
            $map['um.type'] = ['eq', $type];
        }
        $this->assign('type', $type);
        if ($start_date && $end_date) {
            $map['um.create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['um.create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['um.create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $page_size = C('PAGE_SIZE');
        $count = $this->user_money_model
                ->alias('um')
                ->join(C('DB_PREFIX') . 'user u on u.id=um.user_id', 'LEFT')
                ->where($map)
                ->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $money_list = $this->user_money_model
                ->alias('um')
                ->join(C('DB_PREFIX') . 'user u on u.id=um.user_id', 'LEFT')
                ->field('um.*,u.nickname')
                ->where($map)
                ->order('um.create_time desc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('money_list', $money_list);
        $this->assign('page', $show);
        $this->display();
    }

}
