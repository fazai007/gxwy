<?php

namespace Wx\Controller;

class MoneyController extends BaseController {

    protected $user_money_model;

    public function __construct() {
        parent::__construct();
        $this->user_money_model = D('Wx/UserMoney');
    }

    /**
     * 云币
     */
    public function index() {
        $seo = set_seo('云币');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Money/index');
    }

    /**
     * 收支明细
     */
    public function detail() {
        $this->assign('type', 1);
        $seo = set_seo('收支明细');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Money/detail');
    }

    /**
     * 异步获取云币记录
     */
    public function getListData() {
        if (IS_GET) {
            $type = I('get.type', 1, 'intval');
            $page = I('get.page', 1, 'intval');
            $data_list = $this->user_money_model->getPageList($this->userinfo['id'], $type, $page, 10);
            foreach ($data_list['root'] as &$v) {
                if ($v['money'] > 0) {
                    $v['money'] = '+' . $v['money'];
                }
                $v['create_time'] = date('Y-m-d', strtotime($v['create_time']));
            }
            unset($v);
            $this->success($data_list);
        }
    }

    /**
     * 申请提现
     */
    public function withdraw() {
        if (IS_POST) {
            $user_withdraw_model = D('Wx/UserWithdraw');
            $data = $user_withdraw_model->create();
            if ($data) {
                $money = $data['money'];
                $min_withdraw_money = $GLOBALS['SYSTEM']['withdraw_config']['min_withdraw_money'];
                if ($money < $min_withdraw_money) {
                    $this->error('提现金额必须大于等于' . $min_withdraw_money . '元');
                }
                if ($money > $this->userinfo['money']) {
                    $this->error('当前最多可提现' . $this->userinfo['money'] . '元');
                }
                $order_no = build_order_no();
                $data['user_id'] = $this->userinfo['id'];
                $data['withdraw_no'] = $order_no;
                $now_time = date('Y-m-d H:i:s');
                $user_withdraw_log_model = M('user_withdraw_log');
                $user_withdraw_model->startTrans();
                try {
                    //添加提现记录
                    $order_id = $user_withdraw_model->add($data);
                    //添加提现记录日志
                    $data = array(
                        'order_id' => $order_id,
                        'type' => 1,
                        'operator_id' => $this->userinfo['id'],
                        'operator_name' => $this->userinfo['nickname'],
                        'content' => '提交成功',
                        'create_time' => $now_time
                    );
                    $user_withdraw_log_model->add($data);
                    $balance = $this->userinfo['money'] - $money;
                    //更新用户云币
                    $data = array(
                        'money' => $balance
                    );
                    M('user_account')->where("user_id={$this->userinfo['id']}")->save($data);
                    //添加云币记录
                    $data = array(
                        'user_id' => $this->userinfo['id'],
                        'type' => 3,
                        'order_money' => $money,
                        'money' => $money,
                        'balance' => $balance,
                        'data_from' => 2,
                        'data_id' => $order_id,
                        'title' => '申请提现',
                        'description' => '申请提现',
                        'create_time' => $now_time
                    );
                    $this->user_money_model->add($data);
                    $user_withdraw_model->commit();
                    $this->success('提交成功');
                } catch (Exception $ex) {
                    $user_withdraw_model->rollback();
                    $this->error('提交失败');
                }
            } else {
                $this->error($user_withdraw_model->getError());
            }
        }
        $seo = set_seo('申请提现');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Money/withdraw');
    }

}
