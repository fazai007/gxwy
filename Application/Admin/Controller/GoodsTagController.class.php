<?php

namespace Admin\Controller;

class GoodsTagController extends BaseController {

    protected $goods_tag_model;

    public function __construct() {
        parent::__construct();
        $this->goods_tag_model = D('Admin/GoodsTag');
    }

    /**
     * 商品标签
     */
    public function index() {
        $map = [];
        $page_size = C('PAGE_SIZE');
        $count = $this->goods_tag_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $goods_tag_list = $this->goods_tag_model
                ->field('id,name,sort')
                ->where($map)
                ->order('sort asc,id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('goods_tag_list', $goods_tag_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加标签
     */
    public function add() {
        if (IS_POST) {
            $data = $this->goods_tag_model->create();
            if ($data) {
                $result = $this->goods_tag_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->goods_tag_model->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑标签
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->goods_tag_model->create();
            if ($data) {
                $result = $this->goods_tag_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->goods_tag_model->getError());
            }
        }
        $goods_tag = $this->goods_tag_model->find($id);
        $this->assign('goods_tag', $goods_tag);
        $this->display();
    }

    /**
     * 删除标签
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $result = $this->goods_tag_model->delete($ids);
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    
}
