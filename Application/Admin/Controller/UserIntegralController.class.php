<?php

namespace Admin\Controller;

class UserIntegralController extends BaseController {

    protected $user_integral_model;
    protected $user_account_model;
    protected $type_list;

    public function __construct() {
        parent::__construct();
        $this->user_integral_model = D('Admin/UserIntegral');
        $this->user_account_model = D('Admin/UserAccount');
        $this->type_list = [
            1 => '签到',
            2 => '分享',
            3 => '邀请好友',
            4 => '消费',
            5 => '积分兑换',
            6 => '调整'
        ];
        $this->assign('type_list', $this->type_list);
    }

     /**
     * 积分明细
     */
    public function detail($user_id = 0, $type = -1, $start_date = '', $end_date = '') {
        $map['ui.user_id'] = ['eq', $user_id];
        $this->assign('user_id', $user_id);
        if ($type > -1) {
            $map['ui.type'] = ['eq', $type];
        }
        $this->assign('type', $type);
        if ($start_date && $end_date) {
            $map['ui.create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['ui.create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['ui.create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $page_size = C('PAGE_SIZE');
        $count = $this->user_integral_model
                ->alias('ui')
                ->join(C('DB_PREFIX') . 'user u on u.id=ui.user_id', 'LEFT')
                ->where($map)
                ->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $integral_list = $this->user_integral_model
                ->alias('ui')
                ->join(C('DB_PREFIX') . 'user u on u.id=ui.user_id', 'LEFT')
                ->field('ui.*,u.nickname')
                ->where($map)
                ->order('ui.create_time desc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('integral_list', $integral_list);
        $this->assign('page', $show);
        $this->display();
    }
    
     /**
     * 积分调整
     */
    public function recharge($user_id = 0) {
        $user_account = $this->user_account_model->where("user_id={$user_id}")->field('user_id,integral,total_integral')->find();
        if (IS_POST) {
            if (!$user_account) {
                $this->error('用户不存在');
            }
            $data = $this->user_integral_model->create();
            if ($data) {
                if ($data['integral'] == 0) {
                    $this->error('调整积分只能填写非零整数');
                }
                $integral = $data['integral'];
                $balance = $user_account['integral'] + $integral;
                $data['user_id'] = $user_id;
                $data['type'] = 6;
                $data['balance'] = $balance;
                $data['title'] = '积分调整';
                $data['description'] = $data['description'] ? : '积分调整';
                $this->user_integral_model->startTrans();
                try {
                    $this->user_integral_model->add($data);
                    $data = [
                        'integral' => $balance
                    ];
                    if ($integral > 0) {
                        $data['total_integral'] = $user_account['total_integral'] + $integral;
                    }
                    $this->user_account_model->where("user_id={$user_id}")->save($data);
                    $this->user_integral_model->commit();
                    $this->success('操作成功');
                } catch (Exception $ex) {
                    $this->user_integral_model->rollback();
                    $this->success('操作失败');
                }
            } else {
                $this->error($this->user_integral_model->getError());
            }
        }
        $this->assign('user_account', $user_account);
        $this->display();
    }
    
    /**
     * 积分管理
     */
    public function index($field_name = 'ui.user_id', $keyword = '', $type = -1, $start_date = '', $end_date = '') {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['eq', $keyword];
        }
        $this->assign('keyword', $keyword);
        if ($type > -1) {
            $map['ui.type'] = ['eq', $type];
        }
        $this->assign('type', $type);
        if ($start_date && $end_date) {
            $map['ui.create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['ui.create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['ui.create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $page_size = C('PAGE_SIZE');
        $count = $this->user_integral_model
                ->alias('ui')
                ->join(C('DB_PREFIX') . 'user u on u.id=ui.user_id', 'LEFT')
                ->where($map)
                ->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $integral_list = $this->user_integral_model
                ->alias('ui')
                ->join(C('DB_PREFIX') . 'user u on u.id=ui.user_id', 'LEFT')
                ->field('ui.*,u.nickname')
                ->where($map)
                ->order('ui.create_time desc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('integral_list', $integral_list);
        $this->assign('page', $show);
        $this->display();
    }
    
}
