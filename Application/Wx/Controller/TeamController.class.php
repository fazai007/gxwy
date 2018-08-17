<?php

namespace Wx\Controller;

class TeamController extends BaseController {

    protected $user_model;

    public function __construct() {
        parent::__construct();
        $this->user_model = D('Wx/User');
    }

    /**
     * 下家
     */
    public function index() {
        $this->assign('type', 1);
        $team_user_ids = $this->user_model->getTeamUserIds($this->userinfo['id'], $this->userinfo['level_id'], true, false);
        $this->assign('team_count', count($team_user_ids));
        $team_total_money = 0;
        if ($team_user_ids) {
            $team_total_money = D('Wx/UserAccount')->getTotalMoneySum($team_user_ids);
            $team_total_money = $team_total_money ? : 0;
        }
        $this->assign('team_total_money', $team_total_money);
        $seo = set_seo('下家');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Team/index');
    }
    
    /**
     * 异步获取团队列表
     */
    public function getListData(){
        if (IS_GET) {
            $type = I('get.type', 1, 'intval');
            $page = I('get.page', 1, 'intval');
            $data_list = $this->user_model->getTeamPageList($this->userinfo['id'], $this->userinfo['level_id'], $type, $page, 10);
            foreach ($data_list['root'] as &$v) {
                $v['avatar'] = get_user_avatar($v['avatar']);
                $v['join_time'] = date('Y-m-d', strtotime($v['join_time']));
            }
            unset($v);
            $this->success($data_list);
        }
    }

}
