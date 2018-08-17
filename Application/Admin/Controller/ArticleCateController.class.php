<?php

namespace Admin\Controller;

class ArticleCateController extends BaseController {

    protected $article_cate_model;

    public function __construct() {
        parent::__construct();
        $this->article_cate_model = D('Admin/ArticleCate');
        $article_cate_list = $this->article_cate_model
                ->order('sort asc,id asc')
                ->select();
        $article_cate_level_list = array2level($article_cate_list);
        $this->assign('article_cate_level_list', $article_cate_level_list);
    }

    /**
     * 文章分类
     */
    public function index() {
        $this->display();
    }

    /**
     * 添加分类
     */
    public function add($pid = 0) {
        if (IS_POST) {
            $data = $this->article_cate_model->create();
            if ($data) {
                $result = $this->article_cate_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->article_cate_model->getError());
            }
        }
        $this->assign('pid', $pid);
        $this->display();
    }

    /**
     * 编辑分类
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->article_cate_model->create();
            if ($data) {
                $result = $this->article_cate_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->article_cate_model->getError());
            }
        }
        $article_cate = $this->article_cate_model->find($id);
        $this->assign('article_cate', $article_cate);
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
        $sub_cate = $this->article_cate_model->where("pid in ({$ids})")->find();
        if (!empty($sub_cate)) {
            $this->error('此分类下存在子分类，不可删除');
        }
        $result = $this->article_cate_model->delete($ids);
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    
}
