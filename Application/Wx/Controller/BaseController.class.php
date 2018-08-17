<?php

namespace Wx\Controller;

use Think\Controller;

class BaseController extends Controller {

    protected $userinfo;

    public function __construct() {
        parent::__construct();
        $this->checkWx();
        //输出当前请求控制器
        $this->assign('controller', CONTROLLER_NAME);
        //输出当前请求方法
        $this->assign('action', ACTION_NAME);
        //获取系统设置
        $GLOBALS['SYSTEM'] = get_system();
        $upload_config = C('UPLOAD_CONFIG');
        if ($GLOBALS['SYSTEM']['upload_config']['type'] == 'qiniu') {
            $qiniu_config = $GLOBALS['SYSTEM']['upload_config'];
            $upload_config = array_merge($upload_config, $qiniu_config);
        } else {
            $upload_config['cdn_url'] = $upload_config['cdn_url'] ? $upload_config['cdn_url'] : __ROOT__;
        }
        //配置信息
        $my_config = [
            'upload' => $upload_config
        ];
        if (!defined('__CDN__')) {
            define('__CDN__', $upload_config['cdn_url']);
        }
        C('TMPL_PARSE_STRING', ['__CDN__' => $upload_config['cdn_url']]);
        //渲染配置信息
        $this->assign('my_config', $my_config);
        $this->assign('SYSTEM', $GLOBALS['SYSTEM']);
        //推荐人
        $recommender_id = I('recommender_id', 0, 'intval');
        //判断登录
        $user_id = $this->checkLogin($recommender_id);
        $this->userinfo = D('Wx/User')->getAllUserInfoById($user_id);
        $this->assign('userinfo', $this->userinfo);
        //初始化微信
        vendor('Wechat.TPWechat');
        $wechat_options = [
            'token' => $GLOBALS['SYSTEM']['wx_config']['token'],
            'encodingaeskey' => $GLOBALS['SYSTEM']['wx_config']['encoding_aes_key'],
            'appid' => $GLOBALS['SYSTEM']['wx_config']['app_id'],
            'appsecret' => $GLOBALS['SYSTEM']['wx_config']['app_secret']
        ];
        $tp_wechat = new \TPWechat($wechat_options);
        $http = is_ssl() ? 'https://' : 'http://';
        $redirect_url = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        //微信js
        $js_sign_package = $tp_wechat->getJsSign($redirect_url);
        $this->assign('js_sign_package', $js_sign_package);
    }

    /**
     * 微信检查
     */
    protected function checkWx() {
        $is_wechat = is_wechat();
        if (!$is_wechat) {
            if (IS_AJAX) {
                $this->error('请在微信客户端打开链接');
            } else {
                exit('请在微信客户端打开链接');
            }
        }
    }

    /**
     * 登录检查
     * @param int $recommender_id 推荐人id
     * @return type
     */
    protected function checkLogin($recommender_id) {
        $user_id = is_user_login($recommender_id);
        if ($user_id == 0) {
            if (IS_AJAX) {
                $this->error('请先授权登录');
            } else {
                Cookie('__forward__', $_SERVER['REQUEST_URI']);
                $this->redirect('Wx/Auth/index', ['recommender_id' => $recommender_id]);
            }
        } else if ($user_id == -1) {
            if (IS_AJAX) {
                $this->error('账号已禁用');
            } else {
                $this->display('Public/disabled');
                exit();
            }
        } else {
            return $user_id;
        }
    }

    /**
     * 404
     */
    protected function show404() {
        $this->display('Public/404');
        exit();
    }
    
    /**
     * 警告提示
     * @param string $message 提示消息
     * @param string $jump_url 跳转链接
     */
    protected function alert($message, $jump_url = '') {
        $this->assign('message', $message);
        $this->assign('jump_url', $jump_url);
        $this->display('Public/alert');
        exit;
    }

}
