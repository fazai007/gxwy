<?php

namespace Admin\Controller;

class WxAutoReplyController extends BaseController {

    protected $wx_auto_reply_model;
    protected $wx_auto_reply_content_model;

    public function __construct() {
        parent::__construct();
        $this->wx_auto_reply_model = D('Admin/WxAutoReply');
        $this->wx_auto_reply_content_model = D('Admin/WxAutoReplyContent');
    }

    /**
     * 自动回复
     */
    public function index($keyword = '') {
        $map = [];
        if ($keyword) {
            $map['keyword'] = ['like', "%{$keyword}%"];
        }
        $this->assign('keyword', $keyword);
        $page_size = C('PAGE_SIZE');
        $count = $this->wx_auto_reply_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $wx_auto_reply_list = $this->wx_auto_reply_model
                ->where($map)
                ->order('id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('wx_auto_reply_list', $wx_auto_reply_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加关键字
     */
    public function add() {
        if (IS_POST) {
            $data = $this->wx_auto_reply_model->create();
            if ($data) {
                $result = $this->wx_auto_reply_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->wx_auto_reply_model->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑关键字
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->wx_auto_reply_model->create();
            if ($data) {
                $result = $this->wx_auto_reply_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->wx_auto_reply_model->getError());
            }
        }
        $wx_auto_reply = $this->wx_auto_reply_model->find($id);
        $this->assign('wx_auto_reply', $wx_auto_reply);
        $this->display();
    }

    /**
     * 删除关键字
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $this->wx_auto_reply_model->startTrans();
        try {
            $this->wx_auto_reply_model->delete($ids);
            $this->wx_auto_reply_content_model->where("auto_reply_id in ({$ids})")->delete();
            $this->wx_auto_reply_model->commit();
            $this->success('删除成功');
        } catch (Exception $ex) {
            $this->wx_auto_reply_model->rollback();
            $this->error('删除失败');
        }
    }

}
