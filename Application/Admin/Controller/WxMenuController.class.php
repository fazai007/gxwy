<?php

namespace Admin\Controller;

class WxMenuController extends BaseController {

    protected $wx_menu_model;

    public function __construct() {
        parent::__construct();
        $this->wx_menu_model = D('Admin/WxMenu');
    }

    /**
     * 微信自定义菜单
     */
    public function index() {
        $wx_menu_list = $this->wx_menu_model
                ->order('sort asc,id asc')
                ->select();
        $wx_menu_level_list = array2level($wx_menu_list);
        $this->assign('wx_menu_level_list', $wx_menu_level_list);
        $this->display();
    }

    /**
     * 添加自定义菜单
     */
    public function add($pid = 0) {
        if (IS_POST) {
            $data = $this->wx_menu_model->create();
            if ($data) {
                $data['link'] = htmlspecialchars_decode($data['link']);
                $result = $this->wx_menu_model->add($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->wx_menu_model->getError());
            }
        }
        $this->assign('pid', $pid);
        $top_wx_menu_list = $this->wx_menu_model
                ->where("pid=0")
                ->order("sort asc,id asc")
                ->select();
        $this->assign('top_wx_menu_list', $top_wx_menu_list);
        $this->display();
    }

    /**
     * 编辑自定义菜单
     */
    public function edit($id) {
        if (IS_POST) {
            $data = $this->wx_menu_model->create();
            if ($data) {
                $data['link'] = htmlspecialchars_decode($data['link']);
                $result = $this->wx_menu_model->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->wx_menu_model->getError());
            }
        }
        $wx_menu = $this->wx_menu_model->find($id);
        $this->assign('wx_menu', $wx_menu);
        $top_wx_menu_list = $this->wx_menu_model
                ->where("pid=0")
                ->order("sort asc,id asc")
                ->select();
        $this->assign('top_wx_menu_list', $top_wx_menu_list);
        $this->display();
    }

    /**
     * 删除自定义菜单
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要操作的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $sub_menu = $this->wx_menu_model->where("pid in ({$ids})")->find();
        if (!empty($sub_menu)) {
            $this->error('此菜单下存在子菜单，不可删除');
        }
        if ($this->wx_menu_model->delete($ids)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
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
            $result = $this->wx_menu_model->where("id in ({$ids})")->setField('status', $status);
            if ($result !== false) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        } else {
            $this->error('未知操作');
        }
    }

    /**
     * 生成自定义菜单
     */
    public function create() {
        $wx_menu_list = $this->wx_menu_model
                ->where('pid=0 and status=1')
                ->limit(3)
                ->order('sort asc,id asc')
                ->select();
        $data = [];
        foreach ($wx_menu_list as $v) {
            $sub_wx_menu_list = $this->wx_menu_model
                    ->where("pid={$v['id']} and status=1")
                    ->limit(5)
                    ->order('sort asc,id asc')
                    ->select();
            //子菜单
            if ($sub_wx_menu_list) {
                $sub_data = [];
                foreach ($sub_wx_menu_list as $val) {
                    if ($val['link']) {
                        $sub_data[] = [
                            'type' => 'view',
                            'name' => $val['name'],
                            'url' => $val['link']
                        ];
                    } else {
                        $sub_data[] = [
                            'type' => 'click',
                            'name' => $val['name'],
                            'key' => $val['keyword']
                        ];
                    }
                }
                $data['button'][] = [
                    'type' => $v['link'] ? 'view' : 'click',
                    'name' => $v["name"],
                    'sub_button' => $sub_data
                ];
            } else {
                if ($v['link']) {
                    $data['button'][] = [
                        'type' => 'view',
                        'name' => $v['name'],
                        'url' => $v['link']
                    ];
                } else {
                    $data['button'][] = [
                        'type' => 'click',
                        'name' => $v['name'],
                        'key' => $v['keyword']
                    ];
                }
            }
        }
        vendor('Wechat.TPWechat');
        $wechat_options = [
            'token' => $GLOBALS['SYSTEM']['wx_config']['token'],
            'encodingaeskey' => $GLOBALS['SYSTEM']['wx_config']['encoding_aes_key'],
            'appid' => $GLOBALS['SYSTEM']['wx_config']['app_id'],
            'appsecret' => $GLOBALS['SYSTEM']['wx_config']['app_secret']
        ];
        $tp_wechat = new \TPWechat($wechat_options);
        $result = $tp_wechat->createMenu($data);
        if ($result) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败：' . $tp_wechat->errMsg);
        }
    }

}
