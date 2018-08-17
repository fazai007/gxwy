<?php

namespace Admin\Controller;

class UserLevelController extends BaseController {

    protected $user_level_model;
    protected $user_model;

    public function __construct() {
        parent::__construct();
        $this->user_level_model = D('Admin/UserLevel');
        $this->user_model = D('Admin/User');
    }

    /**
     * 用户等级
     */
    public function index() {
        $map = [];
        $page_size = C('PAGE_SIZE');
        $count = $this->user_level_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $user_level_list = $this->user_level_model
                ->where($map)
                ->order('id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        foreach ($user_level_list as &$v) {
            $v['user_count'] = $this->user_model->where("level_id={$v['id']}")->count('id');
        }
        unset($v);
        $this->assign('user_level_list', $user_level_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 编辑用户等级
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->user_level_model->create();
            if ($data) {
                $result = $this->user_level_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->user_level_model->getError());
            }
        }
        $user_level = $this->user_level_model->find($id);
        $this->assign('user_level', $user_level);
        $this->display();
    }

}
