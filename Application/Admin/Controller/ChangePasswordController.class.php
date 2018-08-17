<?php

namespace Admin\Controller;

class ChangePasswordController extends BaseController {

    /**
     * 修改密码
     */
    public function index() {
        if (IS_POST) {
            $admin_id = session('admin_id');
            $data = I('post.');
            $admin_user_model = M('admin_user');
            $admin_user = $admin_user_model->find($admin_id);
            if ($admin_user['password'] == md5($data['old_password'] . C('SALT'))) {
                if ($data['password'] == $data['confirm_password']) {
                    $new_password = md5($data['password'] . C('SALT'));
                    $res = $admin_user_model->where("id={$admin_id}")->setField('password', $new_password);
                    if ($res !== false) {
                        $this->success('修改成功');
                    } else {
                        $this->error('修改失败');
                    }
                } else {
                    $this->error('两次密码输入不一致');
                }
            } else {
                $this->error('原密码不正确');
            }
        }
        $this->display('BasicConfig/changePassword');
    }

}
