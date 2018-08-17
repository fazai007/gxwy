<?php

namespace Admin\Controller;

class UserController extends BaseController {

    protected $user_model;
    protected $user_level_model;
    protected $user_log_model;
    protected $sex_list;

    public function __construct() {
        parent::__construct();
        $this->user_model = D('Admin/User');
        $this->user_level_model = D('Admin/UserLevel');
        $this->user_log_model = M('user_log');
        $user_level_list = $this->user_level_model
                ->field('id,name')
                ->order('id asc')
                ->select();
        $this->assign('user_level_list', $user_level_list);
        $this->sex_list = ['未知', '男', '女'];
        $this->assign('sex_list', $this->sex_list);
    }

    /**
     * 用户管理
     */
    public function index($field_name = 'u.id', $keyword = '', $level_id = -1, $start_date = '', $end_date = '', $status = -1, $account_status = -1) {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['like', "%{$keyword}%"];
        }
        $this->assign('keyword', $keyword);
        if ($level_id > -1) {
            $map['u.level_id'] = ['eq', $level_id];
        }
        $this->assign('level_id', $level_id);
        if ($start_date && $end_date) {
            $map['u.create_time'] = ['between', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']];
        } else if ($start_date) {
            $map['u.create_time'] = ['egt', $start_date . ' 00:00:00'];
        } else if ($end_date) {
            $map['u.create_time'] = ['elt', $end_date . ' 23:59:59'];
        }
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        if ($status > -1) {
            $map['u.status'] = ['eq', $status];
        }
        $this->assign('status', $status);
        if ($account_status > -1) {
            $map['u.account_status'] = ['eq', $account_status];
        }
        $this->assign('account_status', $account_status);
        $page_size = C('PAGE_SIZE');
        $count = $this->user_model
                ->alias('u')
                ->where($map)
                ->count('u.id');
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $user_list = $this->user_model
                ->alias('u')
                ->join(C('DB_PREFIX') . 'user_level ul on ul.id=u.level_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'user_account ua on ua.user_id=u.id', 'LEFT')
                ->join(C('DB_PREFIX') . 'user ru on ru.id=u.recommender_id', 'LEFT')
                ->field('u.*,ul.name level_name,ua.integral,ua.money,ru.avatar recommender_avatar,ru.nickname recommender_nickname')
                ->where($map)
                ->order('u.id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('user_list', $user_list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 用户详情
     */
    public function detail($id) {
        $userinfo = $this->user_model
                ->alias('u')
                ->join(C('DB_PREFIX') . 'user_level ul on ul.id=u.level_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'user_account ua on ua.user_id=u.id', 'LEFT')
                ->field('u.*,ul.name level_name,ua.integral,ua.money')
                ->where("u.id={$id}")
                ->find();
        $this->assign('userinfo', $userinfo);
        $user_log_data = $this->user_log_model
                ->field('operator_name,content,create_time')
                ->where("user_id={$id}")
                ->order('create_time asc')
                ->select();
        $list_rows = 10;
        $total_rows = count($user_log_data);
        $total_page = ceil($total_rows / $list_rows);
        $user_log_list = [
            'list_rows' => $list_rows,
            'total_rows' => $total_rows,
            'total_page' => $total_page,
            'data' => $user_log_data
        ];
        $this->assign('user_log_list', $user_log_list);
        $this->display();
    }

    /**
     * 修改等级
     */
    public function editLevel($id) {
        $userinfo = $this->user_model
                ->alias('u')
                ->join(C('DB_PREFIX') . 'user_level ul on ul.id=u.level_id', 'LEFT')
                ->field('u.id,u.level_id,ul.name level_name')
                ->where("u.id={$id}")
                ->find();
        if (IS_POST) {
            if (!$userinfo) {
                $this->error('用户不存在');
            }
            $level_id = I('post.level_id', 0, 'intval');
            $user_level = $this->user_level_model->where("id={$level_id}")->find();
            if (!$user_level && $level_id != 0) {
                $this->error('请选择等级');
            }
            $level_name = $level_id == 0 ? '普通用户' : $user_level['name'];
            $this->user_model->startTrans();
            try {
                $this->user_model->where("id={$id}")->setField('level_id', $level_id);
                //添加用户日志
                $data = [
                    'user_id' => $id,
                    'type' => 2,
                    'operator_id' => session('admin_id'),
                    'operator_name' => session('admin_name'),
                    'content' => '用户等级由' . $userinfo['level_name'] . '修改为' . $level_name,
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $this->user_log_model->add($data);
                $this->user_model->commit();
                $this->success('操作成功');
            } catch (Exception $ex) {
                $this->user_model->rollback();
                $this->error('操作失败');
            }
        }
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    /**
     * 修改推荐人
     */
    public function editRecommender($id) {
        $userinfo = $this->user_model
                ->field('id,recommender_id')
                ->find($id);
        if (IS_POST) {
            if (!$userinfo) {
                $this->error('用户不存在');
            }
            $recommender_id = I('post.recommender_id', 0, 'intval');
            if ($recommender_id != 0) {
                $recommender_userinfo = $this->user_model->where("id={$recommender_id}")->find();
                if (!$recommender_userinfo) {
                    $this->error('推荐人不存在');
                }
                if ($id == $recommender_id) {
                    $this->error('推荐人不能为用户自己');
                }
                if (!$recommender_userinfo['status']) {
                    $this->error('推荐人账号已禁用');
                }
            }
            $this->user_model->startTrans();
            try {
                $this->user_model->where("id={$id}")->setField('recommender_id', $recommender_id);
                //添加用户日志
                $data = [
                    'user_id' => $id,
                    'type' => 2,
                    'operator_id' => session('admin_id'),
                    'operator_name' => session('admin_name'),
                    'content' => '用户上级由' . $userinfo['recommender_id'] . '修改为' . $recommender_id,
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $this->user_log_model->add($data);
                $this->user_model->commit();
                $this->success('操作成功');
            } catch (Exception $ex) {
                $this->user_model->rollback();
                $this->error('操作失败');
            }
        }
        $this->assign('userinfo', $userinfo);
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
            $content = '';
            if ($status == 1) {
                $content = '用户状态设置为启用';
            } elseif ($status == 0) {
                $content = '用户状态设置为禁用';
            }
            $ids_arr = explode(',', $ids);
            $now_time = date('Y-m-d H:i:s');
            $this->user_model->startTrans();
            try {
                foreach ($ids_arr as $v) {
                    $this->user_model->where("id={$v}")->setField('status', $status);
                    //添加用户日志
                    $data = [
                        'user_id' => $v,
                        'type' => 2,
                        'operator_id' => session('admin_id'),
                        'operator_name' => session('admin_name'),
                        'content' => $content,
                        'create_time' => $now_time
                    ];
                    $this->user_log_model->add($data);
                }
                $this->user_model->commit();
                $this->success('操作成功');
            } catch (Exception $ex) {
                $this->user_model->rollback();
                $this->error('操作失败');
            }
        } else {
            $this->error('未知操作');
        }
    }

}
