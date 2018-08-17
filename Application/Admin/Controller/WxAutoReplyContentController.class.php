<?php

namespace Admin\Controller;

class WxAutoReplyContentController extends BaseController {

    protected $wx_auto_reply_content_model;
    protected $wx_auto_replu_content_types;

    public function __construct() {
        parent::__construct();
        $this->wx_auto_reply_content_model = D('Admin/WxAutoReplyContent');
        $this->wx_auto_replu_content_types = [1 => '文字', 2 => '图片', 3 => '图文'];
        $this->assign('wx_auto_replu_content_types', $this->wx_auto_replu_content_types);
    }

    /**
     * 微信自动回复内容
     */
    public function index($auto_reply_id) {
        $map = [];
        if ($auto_reply_id) {
            $map['auto_reply_id'] = ['eq', $auto_reply_id];
        }
        $this->assign('auto_reply_id', $auto_reply_id);
        $page_size = C('PAGE_SIZE');
        $count = $this->wx_auto_reply_content_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $wx_auto_reply_content_list = $this->wx_auto_reply_content_model
                ->where($map)
                ->order('sort')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('wx_auto_reply_content_list', $wx_auto_reply_content_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 添加自动回复内容
     */
    public function add($auto_reply_id) {
        if (IS_POST) {
            $data = $this->wx_auto_reply_content_model->create();
            if ($data) {
                $result = $this->wx_auto_reply_content_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->wx_auto_reply_content_model->getError());
            }
        }
        $this->assign('auto_reply_id', $auto_reply_id);
        $this->display();
    }

    /**
     * 编辑自动回复内容
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->wx_auto_reply_content_model->create();
            if ($data) {
                $result = $this->wx_auto_reply_content_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->wx_auto_reply_content_model->getError());
            }
        }
        $wx_auto_reply_content = $this->wx_auto_reply_content_model->find($id);
        $this->assign('wx_auto_reply_content', $wx_auto_reply_content);
        $this->display();
    }

    /**
     * 删除自动回复内容
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $result = $this->wx_auto_reply_content_model->delete($ids);
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}
