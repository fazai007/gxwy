<?php

namespace Admin\Controller;

class SinglePageController extends BaseController {

    protected $single_page_model;

    public function __construct() {
        parent::__construct();
        $this->single_page_model = D('Admin/SinglePage');
    }

    /**
     * 单页管理
     */
    public function index($field_name = 'title', $keyword = '', $status = -1) {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['like', "%{$keyword}%"];
        }
        $this->assign('keyword', $keyword);
        if ($status > -1) {
            $map['status'] = ['eq', $status];
        }
        $this->assign('status', $status);
        $page_size = C('PAGE_SIZE');
        $count = $this->single_page_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $single_page_list = $this->single_page_model
                ->where($map)
                ->order('sort asc,id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('single_page_list', $single_page_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加单页
     */
    public function add() {
        if (IS_POST) {
            $data = $this->single_page_model->create();
            if ($data) {
                $result = $this->single_page_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->single_page_model->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑单页
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->single_page_model->create();
            if ($data) {
                $result = $this->single_page_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->single_page_model->getError());
            }
        }
        $single_page = $this->single_page_model->find($id);
        $this->assign('single_page', $single_page);
        $this->display();
    }

    /**
     * 批量更新
     */
    public function batchUpdate($ids = [], $_action = '') {
        if (!$ids) {
            $this->error('请选择需要操作的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        //状态切换
        if ($_action == 'statusToggle') {
            $status = I('get.status', -1, 'intval');
            if (!in_array($status, [0, 1])) {
                $this->error('参数错误');
            }
            $result = $this->single_page_model->where("id in ({$ids})")->setField('status', $status);
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
