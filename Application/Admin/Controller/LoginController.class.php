<?php

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller {

    /**
     * 后台登录
     */
    public function index() {
        $this->display();
    }
    
    /**
     * 登录验证
     */
    public function login(){
        if (IS_POST) {
            $username = I('post.username', '', 'string');
            if (!$username) {
                $this->error('请输入用户名');
            }
            $password = I('post.password', '', 'string');
            if (!$password) {
                $this->error('请输入密码');
            }
            $verify = I('post.verify', '', 'string');
            if (!$verify) {
                $this->error('请输入验证码');
            }
            if (!$this->checkVerify()) {
                $this->error('验证码错误');
            }
            $admin_user_model = M('admin_user');
            $admin_user = $admin_user_model->where("username='{$username}' and password='" . md5($password . C('SALT')) . "'")->find();
            if(!$admin_user){
                $this->error('用户名或密码错误');
            }
            if($admin_user['status'] != 1){
                $this->error('当前用户已禁用');
            }
            //更新登录信息
            $data = [
                'last_login_time' => date('Y-m-d H:i:s'),
                'last_login_ip' => get_client_ip()
            ];
            $admin_user_model->where("id={$admin_user['id']}")->save($data);
            //添加登录日志
            $admin_user_login_log_model = D('Admin/AdminUserLoginLog');
            $data = $admin_user_login_log_model->create(['user_id' => $admin_user['id']]);
            $admin_user_login_log_model->add($data);
            session('admin_id', $admin_user['id']);
            session('admin_name', $admin_user['username']);
            session('admin_avatar', $admin_user['avatar']);
            $this->success('登录成功', U('Admin/Index/index'));
        }
    }
    
    /**
     * 锁屏
     */
    public function lock(){
        if (IS_POST) {
            session('admin_id', null);
            session('admin_name', null);
            session('admin_avatar', null);
            $this->success('锁屏成功');
        }
    }
    
    /**
     * 解锁
     */
    public function unLock(){
        if (IS_POST) {
            $username = I('post.username', '', 'string');
            $password = I('post.password', '', 'string');
            if (!$password) {
                $this->error('请输入密码');
            }
            $admin_user_model = M('admin_user');
            $admin_user = $admin_user_model->where("username='{$username}' and password='" . md5($password . C('SALT')) . "'")->find();
            if(!$admin_user){
                $this->error('用户名或密码错误');
            }
            if($admin_user['status'] != 1){
                $this->error('当前用户已禁用');
            }
            session('admin_id', $admin_user['id']);
            session('admin_name', $admin_user['username']);
            session('admin_avatar', $admin_user['avatar']);
            $this->success('解锁成功');
        }
    }

    /**
     * 退出登录
     */
    public function logout() {
        session('admin_id', null);
        session('admin_name', null);
        session('admin_avatar', null);
        $this->success('退出成功', U('Admin/Login/index'));
    }
    
    /**
     * 产生验证码
     */
    public function getVerify() {
        $Verify = new \Think\Verify();
        $Verify->length = 4;
        $Verify->entry();
    }

    /**
     * 验证验证码
     */
    public function checkVerify() {
        $verify = new \Think\Verify();
        return $verify->check(I('post.verify'));
    }

}
