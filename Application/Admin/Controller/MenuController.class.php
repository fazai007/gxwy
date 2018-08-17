<?php

namespace Admin\Controller;

class MenuController extends BaseController {

    protected $auth_rule_model;

    public function __construct() {
        parent::__construct();
        $this->auth_rule_model = D('Admin/Menu');
        $admin_menu_list = $this->auth_rule_model->order("sort asc,id asc")->select();
        $admin_menu_level_list = array2level($admin_menu_list);
        $this->assign('admin_menu_level_list', $admin_menu_level_list);
    }

    /**
     * 后台菜单
     */
    public function index() {
        $this->display();
    }

    /**
     * 添加菜单
     */
    public function add($pid = 0) {
        if (IS_POST) {
            $data = $this->auth_rule_model->create();
            if ($data) {
                $result = $this->auth_rule_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->auth_rule_model->getError());
            }
        }
        $this->assign('pid', $pid);
        $this->display();
    }

    /**
     * 编辑菜单
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->auth_rule_model->create();
            if ($data) {
                $result = $this->auth_rule_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->auth_rule_model->getError());
            }
        }
        $admin_menu = $this->auth_rule_model->find($id);
        $this->assign('admin_menu', $admin_menu);
        $this->display();
    }

    /**
     * 删除菜单
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $sub_menu = $this->auth_rule_model->where("pid in ({$ids})")->find();
        if (!empty($sub_menu)) {
            $this->error('此菜单下存在子菜单，不可删除');
        }
        $result = $this->auth_rule_model->delete($ids);
        if ($result) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 批量更新
     */
    public function batchUpdate($ids = [], $_action = '') {
        if (!$ids) {
            $this->error('请选择需要操作的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        //显示切换
        if ($_action == 'statusToggle') {
            $status = I('get.status', -1, 'intval');
            if (!in_array($status, [0, 1])) {
                $this->error('参数错误');
            }
            $result = $this->auth_rule_model->where("id in ({$ids})")->setField('status', $status);
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
