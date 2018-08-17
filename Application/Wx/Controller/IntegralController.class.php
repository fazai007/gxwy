<?php

namespace Wx\Controller;

class IntegralController extends BaseController {

    protected $user_integral_model;

    public function __construct() {
        parent::__construct();
        $this->user_integral_model = D('Wx/UserIntegral');
    }
    
    /**
     * 积分
     */
    public function index() {
        $this->assign('type', 1);
        $seo = set_seo('积分');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Integral/index');
    }
    
    /**
     * 异步获取积分记录
     */
    public function getListData(){
        if (IS_GET) {
            $type = I('get.type', 1, 'intval');
            $page = I('get.page', 1, 'intval');
            $data_list = $this->user_integral_model->getPageList($this->userinfo['id'], $type, $page, 10);
            foreach ($data_list['root'] as &$v) {
                if ($v['integral'] > 0) {
                    $v['integral'] = '+' . $v['integral'];
                }
                $v['create_time'] = date('Y-m-d', strtotime($v['create_time']));
            }
            unset($v);
            $this->success($data_list);
        }
    }

    /**
     * 签到
     */
    public function sign() {
        if (IS_POST) {
            if (!$GLOBALS['SYSTEM']['reward_rule']['sign_open']) {
                $this->error('签到活动已结束');
            }
            $sign_count = $this->user_integral_model->hasSign($this->userinfo['id']);
            if ($sign_count > 0) {
                $this->error('今日已签到');
            }
            $sign_integral = $GLOBALS['SYSTEM']['reward_rule']['sign_integral'];
            $this->user_integral_model->startTrans();
            try {
                $balance = $this->userinfo['integral'] + $sign_integral;
                //添加积分记录
                $data = [
                    'user_id' => $this->userinfo['id'],
                    'type' => 1,
                    'integral' => $sign_integral,
                    'balance' => $balance,
                    'title' => '签到成功',
                    'description' => '签到送积分',
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $this->user_integral_model->add($data);
                //更新用户积分
                $data = [
                    'integral' => $balance,
                    'total_integral' => $this->userinfo['total_integral'] + $sign_integral
                ];
                M('user_account')->where("user_id={$this->userinfo['id']}")->save($data);
                $this->user_integral_model->commit();
                $this->success(['integral' => $balance, 'sign_integral' => $sign_integral]);
            } catch (Exception $ex) {
                $this->user_integral_model->rollback();
                $this->error('签到失败');
            }
        }
    }

}
