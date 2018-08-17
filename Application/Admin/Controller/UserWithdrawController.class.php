<?php

namespace Admin\Controller;

class UserWithdrawController extends BaseController {

    protected $user_withdraw_model;
    protected $user_withdraw_log_model;
    protected $payment_type_list;
    protected $online_type_list;

    public function __construct() {
        parent::__construct();
        $this->user_withdraw_model = D('Admin/UserWithdraw');
        $this->user_withdraw_log_model = M('user_withdraw_log');
        $this->payment_type_list = [
            1 => '线上付款',
            2 => '线下付款'
        ];
        $this->assign('payment_type_list', $this->payment_type_list);
        $this->online_type_list = [
            1 => '支付宝',
            2 => '微信',
            3 => '微信企业付款'
        ];
        $this->assign('online_type_list', $this->online_type_list);
    }

    /**
     * 用户提现
     */
    public function index($field_name = 'user_id', $keyword = '', $status = -1, $start_date = '', $end_date = '') {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['eq', $keyword];
        }
        $this->assign('keyword', $keyword);
        if ($status > -1) {
            switch ($status) {
                case 1:
                    $map['review_status'] = ['eq', 1];
                    break;
                case 2:
                    $map['review_status'] = ['eq', 2];
                    break;
                case 3:
                    $map['review_status'] = ['eq', 3];
                    $map['payment_status'] = ['eq', 1];
                    break;
                case 4:
                    $map['review_status'] = ['eq', 3];
                    $map['payment_status'] = ['eq', 2];
                    break;
                case 5:
                    $map['review_status'] = ['eq', 3];
                    $map['payment_status'] = ['eq', 3];
                    break;
            }
        }
        $this->assign('status', $status);
        if ($start_date && $end_date) {
            $map['create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $page_size = C('PAGE_SIZE');
        $count = $this->user_withdraw_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $withdraw_list = $this->user_withdraw_model
                ->where($map)
                ->order('create_time desc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('withdraw_list', $withdraw_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 提现详情
     */
    public function detail($id) {
        $user_withdraw = $this->user_withdraw_model->find($id);
        $this->assign('user_withdraw', $user_withdraw);
        $withdraw_log_list = $this->user_withdraw_log_model
                ->field('operator_name,content,create_time')
                ->where("order_id={$id}")
                ->order('create_time asc')
                ->select();
        $this->assign('withdraw_log_list', $withdraw_log_list);
        $this->display();
    }

    /**
     * 提现审核
     */
    public function review($id) {
        $user_withdraw = $this->user_withdraw_model
                ->field('id,user_id,money,review_status')
                ->find($id);
        if (IS_POST) {
            if (!$user_withdraw) {
                $this->error('提现记录不存在');
            }
            if ($user_withdraw['review_status'] != 1) {
                $this->error('提现记录已审核');
            }
            $validate = [
                ['review_status', 'require', '请选择审核状态', 1, 'regex', 2],
                ['review_status', [2, 3], '审核状态错误', 1, 'in', 2]
            ];
            $this->user_withdraw_model->setProperty("_validate", $validate);
            $auto = [];
            $this->user_withdraw_model->setProperty("_auto", $auto);
            $data = $this->user_withdraw_model->create($_POST);
            if (!$data) {
                $this->error($this->user_withdraw_model->getError());
            }
            $now_time = date('Y-m-d H:i:s');
            $data['review_time'] = $now_time;
            $this->user_withdraw_model->startTrans();
            try {
                //更新提现记录
                $this->user_withdraw_model->save($data);
                //添加提现记录日志
                if ($data['review_status'] == 2) {
                    $content = '审核拒绝';
                } elseif ($data['review_status'] == 3) {
                    $content = '审核通过';
                }
                $data = [
                    'order_id' => $id,
                    'type' => 2,
                    'operator_id' => session('admin_id'),
                    'operator_name' => session('admin_name'),
                    'content' => $content,
                    'create_time' => $now_time
                ];
                $this->user_withdraw_log_model->add($data);
                //审核拒绝，退还提现金额
                if ($data['review_status'] == 2) {
                    //更新用户资金
                    $user_account_model = M('user_account');
                    $user_account = $user_account_model->field('id,money')
                            ->where("user_id={$user_withdraw['user_id']}")
                            ->find();
                    $balance = $user_account['money'] + $user_withdraw['money'];
                    $data = ['money' => $balance];
                    $user_account_model->where("user_id={$user_withdraw['user_id']}")->save($data);
                    //添加资金记录
                    $user_money_model = M('user_money');
                    $data = [
                        'user_id' => $user_withdraw['user_id'],
                        'type' => 4,
                        'order_money' => $user_withdraw['money'],
                        'money' => $user_withdraw['money'],
                        'balance' => $balance,
                        'data_from' => 2,
                        'data_id' => $user_withdraw['id'],
                        'title' => '提现退还',
                        'description' => '提现审核拒绝退还',
                        'create_time' => $now_time
                    ];
                    $user_money_model->add($data);
                }
                $this->user_withdraw_model->commit();
                $this->success('操作成功');
            } catch (Exception $ex) {
                $this->user_withdraw_model->rollback();
                $this->error('操作失败');
            }
        }
        $this->assign('user_withdraw', $user_withdraw);
        $this->display();
    }

    /**
     * 提现付款
     */
    public function payment($id) {
        $user_withdraw = $this->user_withdraw_model
                ->alias('uw')
                ->join(C('DB_PREFIX') . 'user u on u.id=uw.user_id', 'LEFT')
                ->field('uw.id,uw.user_id,uw.withdraw_no,uw.money,uw.review_status,uw.payment_status,u.openid')
                ->where("uw.id={$id}")
                ->find();
        if (IS_POST) {
            if (!$user_withdraw) {
                $this->error('提现记录不存在');
            }
            if ($user_withdraw['review_status'] != 3) {
                $this->error('提现记录审核中或已拒绝');
            }
            if ($user_withdraw['payment_status'] == 3) {
                $this->error('提现记录已付款成功');
            }
            $validate = [
                ['payment_type', 'require', '请选择付款方式', 1, 'regex', 2],
                ['payment_type', [1, 2], '付款方式错误', 1, 'in', 2],
                ['payment_money', 'require', '请输入付款金额', 1, 'regex', 2],
                ['payment_money', '/^[0-9]+(.[0-9]{1,2})?$/', '付款金额格式错误', 1, 'regex', 2],
                ['payment_no', 'require', '请输入付款流水号', 1, 'regex', 2],
                ['payment_time', 'require', '请选择付款时间', 1, 'regex', 2]
            ];
            $this->user_withdraw_model->setProperty("_validate", $validate);
            $auto = [];
            $this->user_withdraw_model->setProperty("_auto", $auto);
            $data = $this->user_withdraw_model->create($_POST);
            if (!$data) {
                $this->error($this->user_withdraw_model->getError());
            }
            if ($data['payment_type'] == 1 && $data['online_type'] == 3) {
                $pay_result = mch_pay($user_withdraw['withdraw_no'], $user_withdraw['openid'], $user_withdraw['money'], '提现付款');
                if ($pay_result['status']) {
                    $data['payment_status'] = 3;
                    $data['payment_no'] = $pay_result['payment_no'];
                    $data['payment_time'] = $pay_result['payment_time'];
                    $pay_result_msg = '付款成功';
                } else {
                    $data['payment_status'] = 2;
                    $pay_result_msg = '付款失败：' . $pay_result['msg'];
                }
            } else {
                $data['payment_status'] = 3;
                $pay_result_msg = '付款成功';
            }
            $now_time = date('Y-m-d H:i:s');
            $this->user_withdraw_model->startTrans();
            try {
                //更新提现记录
                $this->user_withdraw_model->save($data);
                //添加提现记录日志
                $data = [
                    'order_id' => $id,
                    'type' => 2,
                    'operator_id' => session('admin_id'),
                    'operator_name' => session('admin_name'),
                    'content' => '提现' . $pay_result_msg,
                    'create_time' => $now_time
                ];
                $this->user_withdraw_log_model->add($data);
                $this->user_withdraw_model->commit();
                $this->success('操作成功');
            } catch (Exception $ex) {
                $this->user_withdraw_model->rollback();
                $this->error('操作失败');
            }
        }
        $this->assign('user_withdraw', $user_withdraw);
        $this->display();
    }

    /**
     * 批量更新
     */
    public function batchUpdate($ids = [], $_action = '') {
        //审核
        if ($_action == 'review') {
            if (IS_POST) {
                if (!$ids) {
                    $this->error('请选择需要操作的数据');
                }
                $ids = is_array($ids) ? implode(',', $ids) : $ids;
                $review_status = I('post.review_status', 0, 'intval');
                if (!$review_status) {
                    $this->error('请选择审核状态');
                }
                if ($review_status != 2 && $review_status != 3) {
                    $this->error('审核状态错误');
                }
                $review_remark = I('post.review_remark', '', 'string');
                $user_withdraw_list = $this->user_withdraw_model
                        ->field('id,user_id,money')
                        ->where("id in ({$ids}) and review_status=1")
                        ->select();
                if (!$user_withdraw_list) {
                    $this->error('没有符合条件的提现记录');
                }
                $now_time = date('Y-m-d H:i:s');
                if ($review_status == 2) {
                    $content = '审核拒绝';
                } elseif ($review_status == 3) {
                    $content = '审核通过';
                }
                $user_account_model = M('user_account');
                $user_money_model = M('user_money');
                $this->user_withdraw_model->startTrans();
                try {
                    foreach ($user_withdraw_list as $v) {
                        $data = [
                            'review_status' => $review_status,
                            'review_time' => $now_time,
                            'review_remark' => $review_remark
                        ];
                        //更新提现记录
                        $this->user_withdraw_model->where("id={$v['id']}")->save($data);
                        //添加提现记录日志
                        $data = [
                            'order_id' => $v['id'],
                            'type' => 2,
                            'operator_id' => session('admin_id'),
                            'operator_name' => session('admin_name'),
                            'content' => $content,
                            'create_time' => $now_time
                        ];
                        $this->user_withdraw_log_model->add($data);
                        //审核拒绝，退还提现金额
                        if ($review_status == 2) {
                            //更新用户资金
                            $user_account = $user_account_model->field('id,money')
                                    ->where("user_id={$v['user_id']}")
                                    ->find();
                            $balance = $user_account['money'] + $v['money'];
                            $data = [
                                'money' => $balance
                            ];
                            $user_account_model->where("user_id={$v['user_id']}")->save($data);
                            //添加资金记录
                            $data = [
                                'user_id' => $v['user_id'],
                                'type' => 4,
                                'order_money' => $v['money'],
                                'money' => $v['money'],
                                'balance' => $balance,
                                'data_from' => 2,
                                'data_id' => $v['id'],
                                'title' => '提现退还',
                                'description' => '提现审核拒绝退还',
                                'create_time' => $now_time
                            ];
                            $user_money_model->add($data);
                        }
                    }
                    $this->user_withdraw_model->commit();
                    $this->success('操作成功');
                } catch (Exception $ex) {
                    $this->user_withdraw_model->rollback();
                    $this->error('操作失败');
                }
            }
            $this->assign('ids', $ids);
            $this->display('UserWithdraw/batchReview');
        }
        //付款
        elseif ($_action == 'payment') {
            if (IS_POST) {
                if (!$ids) {
                    $this->error('请选择需要操作的数据');
                }
                $ids = is_array($ids) ? implode(',', $ids) : $ids;
                $payment_type = I('post.payment_type', 0, 'intval');
                if (!$payment_type) {
                    $this->error('请选择付款方式');
                }
                if ($payment_type != 1 && $payment_type != 2) {
                    $this->error('付款方式错误');
                }
                $online_type = I('post.online_type', 0, 'intval');
                $payment_no = I('post.payment_no', '', 'string');
                if (!$payment_no) {
                    $this->error('请输入付款流水号');
                }
                $payment_time = I('post.payment_time', '', 'string');
                if (!$payment_time) {
                    $this->error('请选择付款时间');
                }
                $payment_remark = I('post.payment_remark', '', 'string');
                $user_withdraw_list = $this->user_withdraw_model
                        ->alias('uw')
                        ->join(C('DB_PREFIX') . 'user u on u.id=uw.user_id', 'LEFT')
                        ->field('uw.id,uw.user_id,uw.withdraw_no,uw.money,u.openid')
                        ->where("uw.id in ({$ids}) and uw.review_status=3 and uw.payment_status<>3")
                        ->select();
                if (!$user_withdraw_list) {
                    $this->error('没有符合条件的提现记录');
                }
                $now_time = date('Y-m-d H:i:s');
                foreach ($user_withdraw_list as $v) {
                    $data = [
                        'payment_type' => $payment_type,
                        'online_type' => $online_type,
                        'payment_money' => $v['money'],
                        'payment_no' => $payment_no,
                        'payment_time' => $payment_time,
                        'payment_remark' => $payment_remark
                    ];
                    if ($payment_type == 1 && $online_type == 3) {
                        $pay_result = mch_pay($v['withdraw_no'], $v['openid'], $v['money'], '提现付款');
                        if ($pay_result['status']) {
                            $data['payment_status'] = 3;
                            $data['payment_no'] = $pay_result['payment_no'];
                            $data['payment_time'] = $pay_result['payment_time'];
                            $pay_result_msg = '付款成功';
                        } else {
                            $data['payment_status'] = 2;
                            $pay_result_msg = '付款失败：' . $pay_result['msg'];
                        }
                    } else {
                        $data['payment_status'] = 3;
                        $pay_result_msg = '付款成功';
                    }
                    //更新提现记录
                    $this->user_withdraw_model->where("id={$v['id']}")->save($data);
                    //添加提现记录日志
                    $data = [
                        'order_id' => $v['id'],
                        'type' => 2,
                        'operator_id' => session('admin_id'),
                        'operator_name' => session('admin_name'),
                        'content' => '提现' . $pay_result_msg,
                        'create_time' => $now_time
                    ];
                    $this->user_withdraw_log_model->add($data);
                }
                $this->success('操作成功');
            }
            $this->assign('ids', $ids);
            $this->display('UserWithdraw/batchPayment');
        }
    }

    /**
     * 用户提现设置
     */
    public function setting() {
        $system_model = M('system');
        if (IS_POST) {
            $withdraw_config = I('post.');
            $data['value'] = serialize($withdraw_config);
            if ($system_model->where("name='withdraw_config'")->save($data) !== false) {
                $this->success('提交成功');
            } else {
                $this->error('提交失败');
            }
        }
        $withdraw_config = $system_model->field('value')->where("name='withdraw_config'")->find();
        $withdraw_config = unserialize($withdraw_config['value']);
        $this->assign('withdraw_config', $withdraw_config);
        $this->display();
    }

}
