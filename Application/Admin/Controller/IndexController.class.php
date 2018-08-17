<?php

namespace Admin\Controller;

class IndexController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 后台首页
     */
    public function index() {
        //系统概览
        $version = M()->query('select version() as ver');
        $config = [
            'url' => $_SERVER['HTTP_HOST'],
            'document_root' => $_SERVER['DOCUMENT_ROOT'],
            'server_os' => PHP_OS,
            'server_port' => $_SERVER['SERVER_PORT'],
            'server_soft' => $_SERVER['SERVER_SOFTWARE'],
            'php_version' => PHP_VERSION,
            'mysql_version' => $version[0]['ver'],
            'max_upload_size' => ini_get('upload_max_filesize')
        ];
        $this->assign('config', $config);
        //今日
        $today = date('Y-m-d');
        //昨日
        $yesterday = date("Y-m-d", strtotime($today) - 86400);
        //本周
        $this_week_start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")));
        $this_week_end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y")));
        //本月
        $this_month_start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y")));
        $this_month_end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("t"), date("Y")));
        //所有用户
        $user_model = M('user');
        $total_user_count['total'] = $user_model
                ->count('id');
        $total_user_count['today'] = $user_model
                ->where("reg_time>='{$today} 00:00:00' and reg_time<='{$today} 23:59:59'")
                ->count('id');
        $total_user_count['yesterday'] = $user_model
                ->where("reg_time>='{$yesterday} 00:00:00' and reg_time<='{$yesterday} 23:59:59'")
                ->count('id');
        $total_user_count['this_week'] = $user_model
                ->where("reg_time>='{$this_week_start}' and reg_time<='{$this_week_end}'")
                ->count('id');
        $total_user_count['this_month'] = $user_model
                ->where("reg_time>='{$this_month_start}' and reg_time<='{$this_month_end}'")
                ->count('id');
        $this->assign('total_user_count', $total_user_count);
        $user_level_model = M('user_level');
        $user_level_list = $user_level_model->field('id,name')->order('id asc')->select();
        //普通用户
        $level0_user_count['total'] = $user_model
                ->where('level_id=0')
                ->count('id');
        $level0_user_count['today'] = $user_model
                ->where("level_id=0 and reg_time>='{$today} 00:00:00' and reg_time<='{$today} 23:59:59'")
                ->count('id');
        $level0_user_count['yesterday'] = $user_model
                ->where("level_id=0 and reg_time>='{$yesterday} 00:00:00' and reg_time<='{$yesterday} 23:59:59'")
                ->count('id');
        $level0_user_count['this_week'] = $user_model
                ->where("level_id=0 and reg_time>='{$this_week_start}' and reg_time<='{$this_week_end}'")
                ->count('id');
        $level0_user_count['this_month'] = $user_model
                ->where("level_id=0 and reg_time>='{$this_month_start}' and reg_time<='{$this_month_end}'")
                ->count('id');
        $this->assign('level0_user_count', $level0_user_count);
        foreach ($user_level_list as &$v) {
            $v['total'] = $user_model
                    ->where("level_id={$v['id']}")
                    ->count('id');
            $v['today'] = $user_model
                    ->where("level_id={$v['id']} and reg_time>='{$today} 00:00:00' and reg_time<='{$today} 23:59:59'")
                    ->count('id');
            $v['yesterday'] = $user_model
                    ->where("level_id={$v['id']} and reg_time>='{$yesterday} 00:00:00' and reg_time<='{$yesterday} 23:59:59'")
                    ->count('id');
            $v['this_week'] = $user_model
                    ->where("level_id={$v['id']} and reg_time>='{$this_week_start}' and reg_time<='{$this_week_end}'")
                    ->count('id');
            $v['this_month'] = $user_model
                    ->where("level_id={$v['id']} and reg_time>='{$this_month_start}' and reg_time<='{$this_month_end}'")
                    ->count('id');
        }
        unset($v);
        $this->assign('user_level_list', $user_level_list);
        $this->display();
    }

    /**
     * 获取xxx数量
     */
    public function getTask() {
        if (IS_POST) {
            $data = [];
            $this->success($data);
        }
    }

}
