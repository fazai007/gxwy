<?php

namespace Admin\Controller;

class AdminUserController extends BaseController {

    protected $admin_user_model;
    protected $auth_group_model;
    protected $auth_group_access_model;

    public function __construct() {
        parent::__construct();
        $this->admin_user_model = D('Admin/AdminUser');
        $this->auth_group_model = D('Admin/AuthGroup');
        $this->auth_group_access_model = M('auth_group_access');
    }

    /**
     * 管理员管理
     */
    public function index() {
        $map = [];
        $page_size = C('PAGE_SIZE');
        $count = $this->admin_user_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $admin_user_list = $this->admin_user_model
                ->where($map)
                ->order('id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('admin_user_list', $admin_user_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加管理员
     */
    public function add() {
        if (IS_POST) {
            $group_id = I('post.group_id', 0, 'intval');
            $data = $this->admin_user_model->create();
            if ($data) {
                $this->admin_user_model->startTrans();
                try {
                    $data['password'] = md5($data['password'] . C('SALT'));
                    $user_id = $this->admin_user_model->add($data);
                    $auth_group_access['user_id'] = $user_id;
                    $auth_group_access['group_id'] = $group_id;
                    $this->auth_group_access_model->add($auth_group_access);
                    $this->admin_user_model->commit();
                    $this->success('保存成功');
                } catch (Exception $ex) {
                    $this->admin_user_model->rollback();
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->admin_user_model->getError());
            }
        }
        $auth_group_list = $this->auth_group_model->field('id,title')->select();
        $this->assign('auth_group_list', $auth_group_list);
        $this->display();
    }

    /**
     * 编辑管理员
     */
    public function edit($id) {
        if (IS_POST) {
            $group_id = I('post.group_id', 0, 'intval');
            $data = $this->admin_user_model->create();
            if ($data['id'] == 1 && $data['status'] != 1) {
                $this->error('默认管理员不可禁用');
            }
            if ($data) {
                $this->admin_user_model->startTrans();
                try {
                    if ($data['password']) {
                        $data['password'] = md5($data['password'] . C('SALT'));
                    } else {
                        unset($data['password']);
                    }
                    $this->admin_user_model->save($data);
                    $auth_group_access['user_id'] = $id;
                    $auth_group_access['group_id'] = $group_id;
                    $this->auth_group_access_model->where("user_id={$id}")->save($auth_group_access);
                    $this->admin_user_model->commit();
                    $this->success('更新成功');
                } catch (Exception $ex) {
                    $this->admin_user_model->rollback();
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->admin_user_model->getError());
            }
        }
        $admin_user = $this->admin_user_model->find($id);
        $auth_group_list = $this->auth_group_model->field('id,title')->select();
        $auth_group_access = $this->auth_group_access_model->where("user_id={$id}")->find();
        $admin_user['group_id'] = $auth_group_access['group_id'];
        $this->assign('admin_user', $admin_user);
        $this->assign('auth_group_list', $auth_group_list);
        $this->display();
    }

    /**
     * 删除管理员
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        if ((is_array($ids) && in_array(1, $ids)) || (!is_array($ids) && $ids == 1)) {
            $this->error('默认管理员不可删除');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $this->admin_user_model->startTrans();
        try {
            $this->admin_user_model->delete($ids);
            $this->auth_group_access_model->where("user_id in ({$ids})")->delete();
            $this->admin_user_model->commit();
            $this->success('删除成功');
        } catch (Exception $ex) {
            $this->admin_user_model->rollback();
            $this->error('删除失败');
        }
    }

    /**
     * 批量更新
     */
    public function batchUpdate($ids = [], $_action = '') {
        if (!$ids) {
            $this->error('请选择需要操作的数据');
        }
        if ((is_array($ids) && in_array(1, $ids)) || (!is_array($ids) && $ids == 1)) {
            $this->error('默认管理员不可操作');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        //状态切换
        if ($_action == 'statusToggle') {
            $status = I('get.status', -1, 'intval');
            if (!in_array($status, [0, 1])) {
                $this->error('参数错误');
            }
            $result = $this->admin_user_model->where("id in ({$ids})")->setField('status', $status);
            if ($result !== false) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        } else {
            $this->error('未知操作');
        }
    }

}
