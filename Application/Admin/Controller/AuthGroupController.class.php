<?php

namespace Admin\Controller;

class AuthGroupController extends BaseController {

    protected $auth_group_model;
    protected $auth_rule_model;

    public function __construct() {
        parent::__construct();
        $this->auth_group_model = D('Admin/AuthGroup');
        $this->auth_rule_model = M('auth_rule');
    }

    /**
     * 权限组
     */
    public function index() {
        $map = [];
        $page_size = C('PAGE_SIZE');
        $count = $this->auth_group_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $auth_group_list = $this->auth_group_model
                ->where($map)
                ->order('id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('auth_group_list', $auth_group_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加权限组
     */
    public function add() {
        if (IS_POST) {
            $data = $this->auth_group_model->create();
            if ($data) {
                $result = $this->auth_group_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->auth_group_model->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑权限组
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->auth_group_model->create();
            if ($data['id'] == 1 && $data['status'] != 1) {
                $this->error('超级管理组不可禁用');
            }
            if ($data) {
                $result = $this->auth_group_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->auth_group_model->getError());
            }
        }
        $auth_group = $this->auth_group_model->find($id);
        $this->assign('auth_group', $auth_group);
        $this->display();
    }

    /**
     * 删除权限组
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        if ((is_array($ids) && in_array(1, $ids)) || (!is_array($ids) && $ids == 1)) {
            $this->error('超级管理组不可删除');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $result = $this->auth_group_model->delete($ids);
        if ($result) {
            $this->success('删除成功');
        } else {
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
            $this->error('超级管理组不可操作');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        //状态切换
        if ($_action == 'statusToggle') {
            $status = I('get.status', -1, 'intval');
            if (!in_array($status, [0, 1])) {
                $this->error('参数错误');
            }
            $result = $this->auth_group_model->where("id in ({$ids})")->setField('status', $status);
            if ($result !== false) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        } else {
            $this->error('未知操作');
        }
    }

    /**
     * 授权
     */
    public function auth($id, $auth_rule_ids = '') {
        if (IS_POST) {
            if ($id) {
                $group_data['id'] = $id;
                $group_data['rules'] = is_array($auth_rule_ids) ? implode(',', $auth_rule_ids) : '';
                if ($this->auth_group_model->save($group_data) !== false) {
                    $this->success('授权成功');
                } else {
                    $this->error('授权失败');
                }
            }
        }
        $this->assign('id', $id);
        $this->display();
    }

    /**
     * AJAX获取规则数据
     */
    public function getJson($id) {
        $auth_group_data = $this->auth_group_model->find($id);
        $auth_rules = explode(',', $auth_group_data['rules']);
        $auth_rule_list = $this->auth_rule_model->field('id,pid,title')->order('sort asc,id asc')->select();
        foreach ($auth_rule_list as $key => $value) {
            in_array($value['id'], $auth_rules) && $auth_rule_list[$key]['checked'] = true;
        }
        $this->success($auth_rule_list);
    }

}
