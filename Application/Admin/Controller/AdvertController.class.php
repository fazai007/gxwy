<?php

namespace Admin\Controller;

class AdvertController extends BaseController {

    protected $advert_model;
    protected $advert_cate_model;

    public function __construct() {
        parent::__construct();
        $this->advert_model = D('Admin/Advert');
        $this->advert_position_model = D('Admin/AdvertPosition');
        $advert_position_list = $this->advert_position_model
                ->order('id asc')
                ->select();
        $this->assign('advert_position_list', $advert_position_list);
    }

    /**
     * 广告管理
     */
    public function index($field_name = 'a.name', $keyword = '', $position_id = 0) {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['like', "%{$keyword}%"];
        }
        $this->assign('keyword', $keyword);
        if ($position_id > 0) {
            $map['a.position_id'] = ['eq', $position_id];
        }
        $this->assign('position_id', $position_id);
        $page_size = C('PAGE_SIZE');
        $count = $this->advert_model->alias('a')->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $advert_list = $this->advert_model
                ->alias('a')
                ->join(C('DB_PREFIX') . "advert_position ap on ap.id=a.position_id", 'LEFT')
                ->field('a.id,a.sort,a.name,ap.name position_name,a.image,a.link')
                ->where($map)
                ->order('a.sort asc,a.id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('advert_list', $advert_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加广告
     */
    public function add() {
        if (IS_POST) {
            $data = $this->advert_model->create();
            if ($data) {
                $result = $this->advert_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->advert_model->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑广告
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->advert_model->create();
            if ($data) {
                $result = $this->advert_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->advert_model->getError());
            }
        }
        $advert = $this->advert_model->find($id);
        $this->assign('advert', $advert);
        $this->display();
    }

    /**
     * 删除广告
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $result = $this->advert_model->delete($ids);
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}
