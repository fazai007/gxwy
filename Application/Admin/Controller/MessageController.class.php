<?php

namespace Admin\Controller;

class MessageController extends BaseController {

    protected $message_model;

    public function __construct() {
        parent::__construct();
        $this->message_model = D('Admin/Message');
    }

    /**
     * 用户留言
     */
    public function index($start_date = '', $end_date = '') {
        $map = [];
        if ($start_date && $end_date) {
            $map['m.create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['m.create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['m.create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $page_size = C('PAGE_SIZE');
        $count = $this->message_model->alias('m')->where($map)->count('m.id');
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $message_list = $this->message_model
                ->alias('m')
                ->join(C('DB_PREFIX') . "user u on u.id=m.user_id", 'LEFT')
                ->field('m.*,u.avatar,u.nickname')
                ->where($map)
                ->order('m.create_time desc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('message_list', $message_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 删除留言
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        if ($this->message_model->delete($ids)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    
}
