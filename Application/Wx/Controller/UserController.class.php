<?php

namespace Wx\Controller;

class UserController extends BaseController {

    protected $user_model;

    public function __construct() {
        parent::__construct();
        $this->user_model = D('Wx/User');
    }

    /**
     * 我的
     */
    public function index() {
        $sex_arr = ['未知', '男', '女'];
        $sex = isset($sex_arr[$this->userinfo['sex']]) ? $sex_arr[$this->userinfo['sex']] : '未知';
        $this->assign('sex', $sex);
        $seo = set_seo('我的');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

    /**
     * 
     */
    public function getUserData() {
        if (IS_GET) {
            $team_user_ids = $this->user_model->getTeamUserIds($this->userinfo['id'], $this->userinfo['level_id'], true, false);
            $user_data = [
                'money' => $this->userinfo['money'],
                'integral' => $this->userinfo['integral'],
                'team_count' => count($team_user_ids)
            ];
            $this->success($user_data);
        }
    }

    /**
     * 个人信息
     */
    public function profile() {
        $seo = set_seo('个人信息');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

    /**
     * 消息反馈
     */
    public function feedback() {
        $seo = set_seo('消息反馈');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

    /**
     * 开通会员
     */
    public function kaitong() {
        $protocol = D('Wx/SinglePage')->getDetail(1);
        $this->assign('protocol', $protocol);
        $seo = set_seo('开通会员');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

    /**
     * 推广名片
     */
    public function poster() {
        $seo = set_seo('李歌情感大讲堂');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

    /**
     * 获取推广名片
     */
    public function getPoster() {
        if (IS_GET) {
            $poster_path = '/Uploads/poster/poster_' . $this->userinfo['id'] . '.jpg';
            if (!file_exists('.' . $poster_path)) {
                \Api\Controller\UtilityController::createPosterPic($this->userinfo['id']);
            }
            $this->success(__ROOT__ . $poster_path);
        }
    }

    /**
     * 在线留言
     */
    public function message() {
        $seo = set_seo('在线留言');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

    /**
     * 在线留言提交
     */
    public function messageSave() {
        if (IS_POST) {
            $message_model = D('Wx/Message');
            $data = $message_model->create();
            if ($data) {
                $data['user_id'] = $this->userinfo['id'];
                $result = $message_model->add($data);
                if ($result) {
                    $this->success('提交成功', U('Wx/User/index'));
                } else {
                    $this->error('提交失败');
                }
            } else {
                $this->error($message_model->getError());
            }
        }
    }

    /**
     * 免费视频资料保存
     */
    public function profileSave() {
        if (IS_POST) {
            $name = I('post.name', '');
            if (!$name) {
                $this->error('请填写姓名');
            }
            $mobile = I('post.mobile', '');
            if (!$mobile || !is_mobile_format($mobile)) {
                $this->error('请填写正确的手机号');
            }
            $data = [
                'name' => $name,
                'mobile' => $mobile
            ];
            //更新用户信息
            $result = $this->user_model->where("id={$this->userinfo['id']}")->save($data);
            if ($result !== false) {
                $this->success('提交成功');
            } else {
                $this->error('提交失败');
            }
        }
    }

}
