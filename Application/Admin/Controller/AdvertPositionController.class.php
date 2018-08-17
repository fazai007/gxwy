<?php

namespace Admin\Controller;

class AdvertPositionController extends BaseController {

    protected $advert_position_model;

    public function __construct() {
        parent::__construct();
        $this->advert_position_model = D('Admin/AdvertPosition');
    }

    /**
     * 广告位管理
     */
    public function index() {
        $map = [];
        $page_size = C('PAGE_SIZE');
        $count = $this->advert_position_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $advert_position_list = $this->advert_position_model
                ->where($map)
                ->order('id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('advert_position_list', $advert_position_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加广告位
     */
    public function add() {
        if (IS_POST) {
            $data = $this->advert_position_model->create();
            if ($data) {
                $result = $this->advert_position_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->advert_position_model->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑广告位
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->advert_position_model->create();
            if ($data) {
                $result = $this->advert_position_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->advert_position_model->getError());
            }
        }
        $advert_position = $this->advert_position_model->find($id);
        $this->assign('advert_position', $advert_position);
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
            $result = $this->advert_position_model->where("id in ({$ids})")->setField('status', $status);
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
