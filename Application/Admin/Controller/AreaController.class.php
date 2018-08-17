<?php

namespace Admin\Controller;

class AreaController extends BaseController {

    protected $area_model;

    public function __construct() {
        parent::__construct();
        $this->area_model = D('Admin/Area');
    }

    /**
     * 地区管理
     */
    public function index($pid = 0) {
        $map = [];
        $map['pid'] = ['eq', $pid];
        $page_size = C('PAGE_SIZE');
        $count = $this->area_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $area_list = $this->area_model
                ->where($map)
                ->order('sort asc,id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('area_list', $area_list);
        $this->assign('page', $show);
        $area = $this->area_model->field('id,pid,name')->find($pid);
        if ($area['pid'] == 0) {
            $parent_area = ['id' => 0, 'pid' => 0, 'name' => '一级地区'];
        } else {
            $parent_area = $this->area_model->field('id,pid,name')->find($area['pid']);
        }
        $this->assign('pid', $pid);
        $this->assign('parent_area', $parent_area);
        $this->display();
    }

    /**
     * 添加地区
     */
    public function add($pid = 0) {
        if (IS_POST) {
            $data = $this->area_model->create();
            if ($data) {
                $result = $this->area_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->area_model->getError());
            }
        }
        if ($pid == 0) {
            $parent_area = ['id' => 0, 'pid' => 0, 'name' => '一级地区'];
        } else {
            $parent_area = $this->area_model->field('id,pid,name')->find($pid);
        }
        $this->assign('parent_area', $parent_area);
        $this->display();
    }

    /**
     * 编辑地区
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->area_model->create();
            if ($data) {
                $result = $this->area_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->area_model->getError());
            }
        }
        $area = $this->area_model->find($id);
        $this->assign('area', $area);
        if ($area['pid'] == 0) {
            $parent_area = ['id' => 0, 'pid' => 0, 'name' => '一级地区'];
        } else {
            $parent_area = $this->area_model->field('id,pid,name')->find($area['pid']);
        }
        $this->assign('parent_area', $parent_area);
        $this->display();
    }

    /**
     * 删除地区
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $sub_area = $this->area_model->where("pid in ({$ids})")->find();
        if (!empty($sub_area)) {
            $this->error('此地区下存在子地区，不可删除');
        }
        $result = $this->area_model->delete($ids);
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * AJAX获取地区数据
     */
    public function getJson($pid) {
        $area_list = $this->area_model->field('id,name')->where("pid={$pid}")->order('sort asc,id asc')->select();
        $this->success($area_list);
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
            $result = $this->area_model->where("id in ({$ids})")->setField('status', $status);
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
