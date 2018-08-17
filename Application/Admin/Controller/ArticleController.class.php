<?php

namespace Admin\Controller;

class ArticleController extends BaseController {

    protected $article_model;
    protected $article_cate_model;

    public function __construct() {
        parent::__construct();
        $this->article_model = D('Admin/Article');
        $this->article_cate_model = D('Admin/ArticleCate');
        $article_cate_list = $this->article_cate_model
                ->order('sort asc,id asc')
                ->select();
        $article_cate_level_list = array2level($article_cate_list);
        $this->assign('article_cate_level_list', $article_cate_level_list);
    }

    /**
     * 文章管理
     */
    public function index($field_name = 'a.title', $keyword = '', $cate_id = 0, $status = -1) {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['like', "%{$keyword}%"];
        }
        $this->assign('keyword', $keyword);
        if ($cate_id > 0) {
            //获取所有子分类
            $cate_ids = $this->article_cate_model->getChild([$cate_id], [$cate_id]);
            $map['a.cate_id'] = ['in', $cate_ids];
        }
        $this->assign('cate_id', $cate_id);
        if ($status > -1) {
            $map['a.status'] = ['eq', $status];
        }
        $this->assign('status', $status);
        $page_size = C('PAGE_SIZE');
        $count = $this->article_model->alias('a')->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $article_list = $this->article_model
                ->alias('a')
                ->join(C('DB_PREFIX') . "article_cate ac on ac.id=a.cate_id", 'LEFT')
                ->field('a.id,a.sort,a.title,a.create_time,ac.name cate_name')
                ->where($map)
                ->order('a.sort asc,a.id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('article_list', $article_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加文章
     */
    public function add() {
        if (IS_POST) {
            $data = $this->article_model->create();
            if ($data) {
                $result = $this->article_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->article_model->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑文章
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->article_model->create();
            if ($data) {
                $result = $this->article_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->article_model->getError());
            }
        }
        $article = $this->article_model->find($id);
        $this->assign('article', $article);
        $this->display();
    }

    /**
     * 删除文章
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $result = $this->article_model->delete($ids);
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}
