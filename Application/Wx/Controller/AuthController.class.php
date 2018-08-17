<?php

namespace Wx\Controller;

use Think\Controller;

class AuthController extends Controller {

    protected $user_model;
    protected $user_account_model;

    public function __construct() {
        parent::__construct();
        $is_wechat = is_wechat();
        if (!$is_wechat) {
            exit('请在微信客户端打开链接');
        }
        $this->user_model = D('Wx/User');
        $this->user_account_model = D('Wx/UserAccount');
    }

    /**
     * 用户授权
     */
    public function index($recommender_id = 0) {
        vendor('Wechat.TPWechat');
        $wechat_options = get_wechat_options();
        $tp_wechat = new \TPWechat($wechat_options);
        $http = is_ssl() ? 'https://' : 'http://';
        $redirect_url = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $wx_auth_redirect_url = C('WX_AUTH_REDIRECT_URL') . '?redirect_url=' . $redirect_url;
        if (!isset($_GET['code'])) {
            redirect($wx_auth_redirect_url);
        } else {
            $oauth_access_token = $tp_wechat->getOauthAccessToken();
            if (!$oauth_access_token) {
                redirect($wx_auth_redirect_url);
            } else {
                $openid = $oauth_access_token['openid'];
                $userinfo = $this->user_model->getUserInfoByOpenid($openid);
                $now_time = date('Y-m-d H:i:s');
                $ip = get_client_ip();
                $recommender_userinfo = $this->user_model->getUserInfoById($recommender_id);
                if (!$recommender_userinfo || $recommender_userinfo['level_id'] == 0) {
                    $recommender_id = 0;
                }
                if (!$userinfo) {
                    $wx_userinfo = $tp_wechat->getOauthUserinfo($oauth_access_token['access_token'], $openid);
                    if (!$wx_userinfo) {
                        redirect($wx_auth_redirect_url);
                    } else {
                        $this->user_model->startTrans();
                        try {
                            //添加用户信息
                            $data = [
                                'openid' => $wx_userinfo['openid'],
                                'nickname' => filter_nickname($wx_userinfo['nickname']),
                                'sex' => $wx_userinfo['sex'],
                                'province' => $wx_userinfo['province'],
                                'city' => $wx_userinfo['city'],
                                'country' => $wx_userinfo['country'],
                                'avatar' => $wx_userinfo['headimgurl'],
                                'unionid' => isset($wx_userinfo['unionid']) ? $wx_userinfo['unionid'] : '',
                                'recommender_id' => $recommender_id,
                                'reg_time' => $now_time,
                                'reg_ip' => $ip,
                                'last_login_time' => $now_time,
                                'last_login_ip' => $ip
                            ];
                            $user_id = $this->user_model->add($data);
                            //添加用户账户
                            $data = [
                                'user_id' => $user_id
                            ];
                            $this->user_account_model->add($data);
                            //添加用户登录日志
                            $data = array(
                                'user_id' => $user_id,
                                'login_time' => $now_time,
                                'login_ip' => $ip
                            );
                            M('user_login_log')->add($data);
                            //有推荐人
                            if ($recommender_id != 0) {
                                $reward_rule = get_system('reward_rule');
                                $invite_integral = $reward_rule['invite_integral'];
                                if ($reward_rule['invite_open'] && $invite_integral > 0) {
                                    $account_info = $this->user_account_model->getAccountInfoByUserId($recommender_id);
                                    $balance = $account_info['integral'] + $invite_integral;
                                    //添加积分记录
                                    $data = [
                                        'user_id' => $recommender_id,
                                        'type' => 3,
                                        'integral' => $invite_integral,
                                        'balance' => $balance,
                                        'title' => '邀请好友成功',
                                        'description' => '邀请好友成功',
                                        'create_time' => $now_time
                                    ];
                                    M('user_integral')->add($data);
                                    //更新用户积分
                                    $data = [
                                        'integral' => $balance,
                                        'total_integral' => $account_info['total_integral'] + $invite_integral
                                    ];
                                    $this->user_account_model->where("user_id={$recommender_id}")->save($data);
                                }
                            }
                            $this->user_model->commit();
                            //设置登录状态
                            $this->user_model->autoLogin($user_id);
                        } catch (Exception $ex) {
                            $this->user_model->rollback();
                            redirect($wx_auth_redirect_url);
                        }
                    }
                } else {
                    $this->user_model->startTrans();
                    try {
                        $user_id = $userinfo['id'];
                        //更新用户信息
                        $user_data = [
                            'last_login_time' => $now_time,
                            'last_login_ip' => $ip
                        ];
                        if ($userinfo['recommender_id'] == 0 && $userinfo['level_id'] == 0 && $recommender_id != 0 && $userinfo['id'] != $recommender_id) {
                            $reward_rule = get_system('reward_rule');
                            $invite_integral = $reward_rule['invite_integral'];
                            if ($reward_rule['invite_open'] && $invite_integral > 0) {
                                $account_info = $this->user_account_model->getAccountInfoByUserId($recommender_id);
                                $balance = $account_info['integral'] + $invite_integral;
                                //添加积分记录
                                $data = [
                                    'user_id' => $recommender_id,
                                    'type' => 3,
                                    'integral' => $invite_integral,
                                    'balance' => $balance,
                                    'title' => '邀请好友成功',
                                    'description' => '邀请好友成功',
                                    'create_time' => $now_time
                                ];
                                M('user_integral')->add($data);
                                //更新用户积分
                                $data = [
                                    'integral' => $balance,
                                    'total_integral' => $account_info['total_integral'] + $invite_integral
                                ];
                                $this->user_account_model->where("user_id={$recommender_id}")->save($data);
                            }
                            $user_data['recommender_id'] = $recommender_id;
                        }
                        $this->user_model->where("id={$user_id}")->save($user_data);
                        //添加用户登录日志
                        $data = [                            
                            'user_id' => $user_id,
                            'login_time' => $now_time,
                            'login_ip' => $ip
                        ];
                        M('user_login_log')->add($data);
                        $this->user_model->commit();
                        //设置登录状态
                        $this->user_model->autoLogin($user_id);
                    } catch (Exception $ex) {
                        $this->user_model->rollback();
                        redirect($wx_auth_redirect_url);
                    }
                }
            }
        }
        $userinfo = $this->user_model->getUserInfoById($user_id);
        if (!$userinfo) {
            redirect($wx_auth_redirect_url);
        }
        $back_url = Cookie('__forward__') ? : U('Wx/Index/index');
        redirect($back_url);
    }

}
