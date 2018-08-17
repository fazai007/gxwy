<?php

namespace Admin\Controller;

use Common\Model\SystemModel;
use Think\Controller;
use Think\Auth;

class BaseController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->getMenu();
        //获取系统设置
        $GLOBALS['SYSTEM'] = get_system();
        $upload_config = C('UPLOAD_CONFIG');
        if ($GLOBALS['SYSTEM']['upload_config']['type'] == 'qiniu') {
            $qiniu_config = $GLOBALS['SYSTEM']['upload_config'];
            $policy = [
                'saveKey' => ltrim($qiniu_config['save_key'], '/')
            ];
            //如果启用服务端回调
            if ($qiniu_config['notify_enabled']) {
                $policy = array_merge($policy, [
                    'callbackUrl' => $qiniu_config['notify_url'],
                    'callbackBody' => 'filename=$(fname)&key=$(key)&imageInfo=$(imageInfo)&filesize=$(fsize)&admin=$(x:admin)&user=$(x:user)'
                ]);
            }
            vendor('Qiniu/QiniuAuth');
            $qiniu_auth = new \QiniuAuth($qiniu_config['access_key'], $qiniu_config['secret_key']);
            $multipart['token'] = $qiniu_auth->uploadToken($qiniu_config['bucket'], null, $qiniu_config['expire'], $policy);
            $multipart['admin'] = (int) session('admin_id');
            $multipart['user'] = 0;
            $qiniu_config['multipart'] = $multipart;
            $upload_config = array_merge($upload_config, $qiniu_config);
        } else {
            $upload_config['cdn_url'] = $upload_config['cdn_url'] ? : __ROOT__;
        }
        //配置信息
        $my_config = [
            'upload' => $upload_config,
            'module_name' => MODULE_NAME,
            'controller_name' => CONTROLLER_NAME,
            'action_name' => ACTION_NAME,
            'cur_controller' => MODULE_NAME . '/' . CONTROLLER_NAME,
            'public_path' => __ROOT__ . '/Public'
        ];
        if (!defined('__CDN__')) {
            define('__CDN__', $upload_config['cdn_url']);
        }
        C('TMPL_PARSE_STRING', ['__CDN__' => $upload_config['cdn_url']]);
        //渲染配置信息
        $this->assign('my_config', $my_config);
        $this->assign('SYSTEM', $GLOBALS['SYSTEM']);
        $this->assign('admin_name', session('admin_name'));
        $this->assign('admin_avatar', session('admin_avatar'));
        $this->assign('today_date', date('Y-m-d'));

    }

    /**
     * 权限检查
     */
    protected function checkAuth() {
        if (!session('?admin_id')) {
            if (IS_AJAX) {
                $this->error('请先登录');
            } else {
                $this->redirect('Admin/Login/index');
            }
        }
        // 排除权限
        $not_check = ['Admin/Index/index', 'Admin/Index/getTask', 'Admin/AuthGroup/getJson', 'Admin/BasicConfig/clear', 'Admin/Area/getJson'];
        if (!in_array(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME, $not_check)) {
            $auth = new Auth();
            $admin_id = session('admin_id');
            if (!$auth->check(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME, $admin_id) && $admin_id != 1) {
                $this->error('没有权限，请联系管理员');
            }
        }
    }

    /**
     * 获取侧边栏菜单
     */
    protected function getMenu() {
        $menu = [];
        $admin_id = session('admin_id');
        $auth = new Auth();
        $auth_rule_list = M('auth_rule')->where("status=1 and display=1")->order("sort asc,id asc")->select();
        foreach ($auth_rule_list as $value) {
            if ($auth->check($value['name'], $admin_id) || $admin_id == 1) {
                $menu[] = $value;
            }
        }
        $menu = !empty($menu) ? array2tree($menu) : [];
        $this->assign('menu', $menu);
    }

}
