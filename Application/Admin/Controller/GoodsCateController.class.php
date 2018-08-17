<?php

namespace Admin\Controller;

class GoodsCateController extends BaseController {

    protected $goods_cate_model;

    public function __construct() {
        parent::__construct();
        $this->goods_cate_model = D('Admin/GoodsCate');
        $goods_cate_list = $this->goods_cate_model->order('sort asc,id asc')->select();
        $goods_cate_level_list = array2level($goods_cate_list);
        $this->assign('goods_cate_level_list', $goods_cate_level_list);
    }

    /**
     * 商品分类
     */
    public function index() {
        $this->display();
    }

    /**
     * 添加分类
     */
    public function add() {
        if (IS_POST) {
            $data = $this->goods_cate_model->create();
            if ($data) {
                $result = $this->goods_cate_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->goods_cate_model->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑分类
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->goods_cate_model->create();
            if ($data) {
                $result = $this->goods_cate_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->goods_cate_model->getError());
            }
        }
        $goods_cate = $this->goods_cate_model->find($id);
        $this->assign('goods_cate', $goods_cate);
        $this->display();
    }

    /**
     * 删除分类
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $sub_cate = $this->goods_cate_model->where("pid in ({$ids})")->find();
        if (!empty($sub_cate)) {
            $this->error('此分类下存在子分类，不可删除');
        }
        $result = $this->goods_cate_model->delete($ids);
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
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        //状态切换
        if ($_action == 'statusToggle') {
            $status = I('get.status', -1, 'intval');
            if (!in_array($status, [0, 1])) {
                $this->error('参数错误');
            }
            $result = $this->goods_cate_model->where("id in ({$ids})")->setField('status', $status);
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
