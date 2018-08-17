<?php

namespace Api\Controller;

use Think\Controller;

class WechatController extends Controller {

    public function index() {
        vendor('Wechat.TPWechat');
        $wechat_options = get_wechat_options();
        $tp_wechat = new \TPWechat($wechat_options);
        //明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
//        $tp_wechat->valid();exit();
        $rev_data = $tp_wechat->getRev()->getRevData();
        list($content, $type) = $this->reply($rev_data);
        switch ($type) {
            case 'null':
                break;
            case 'text':
                $tp_wechat->text($content)->reply();
                break;
            case 'image':
                $tp_wechat->image($content)->reply();
                break;
            case 'news':
                $tp_wechat->news($content)->reply();
                break;
            case 'transfer_customer_service':
                $tp_wechat->transfer_customer_service()->reply();
                break;
        }
        exit();
    }

    /**
     * 微信授权跳转
     */
    public function req() {
        vendor('Wechat.TPWechat');
        $wechat_options = get_wechat_options();
        $tp_wechat = new \TPWechat($wechat_options);
        $callback = domain() . '/index.php/Api/Wechat/rtn?jump=' . $_GET['redirect_url'];
        $jump_url = $tp_wechat->getOauthRedirect($callback);
        redirect($jump_url);
    }

    /**
     * 微信授权回调
     */
    public function rtn() {
        $jump_url = '';
        if (count(explode('?', $_GET['jump'])) > 1) {
            $jump_url = $_GET['jump'] . '&code=' . $_GET['code'];
        } else {
            $jump_url = $_GET['jump'] . '?code=' . $_GET['code'];
        }
        redirect($jump_url);
    }

    private function reply($data) {
        $system = get_system();
        $wechat_options = [
            'token' => $system['wx_config']['token'],
            'encodingaeskey' => $system['wx_config']['encoding_aes_key'],
            'appid' => $system['wx_config']['app_id'],
            'appsecret' => $system['wx_config']['app_secret']
        ];
        //接收消息-接收事件推送
        if ($data['MsgType'] == 'event') {
            //点击菜单拉取消息时的事件推送
            if ($data['Event'] == 'CLICK') {
                $data['Content'] = $data['EventKey'];
                return ['', 'null'];
            }
            //点击菜单跳转链接时的事件推送
            elseif ($data['Event'] == 'VIEW') {
                //设置的跳转URL
                $data['Content'] = $data['EventKey'];
                return ['', 'null'];
            }
            //用户未关注时，进行关注后的事件推送
            elseif ($data['Event'] == 'subscribe') {
                $event_key = explode('_', $data['EventKey']);
                $openid = $data['FromUserName'];
                $user_model = M('user');
                $user_account_model = M('user_account');
                $user_integral_model = M('user_integral');
                $userinfo = $user_model->where("openid='{$openid}'")->find();
                vendor('Wechat.TPWechat');
                $tp_wechat = new \TPWechat($wechat_options);
                $wx_userinfo = $tp_wechat->getUserInfo($openid);
                //扫描关注
                if ($event_key[0] == 'qrscene') {
                    $recommender_id = $event_key[1];
                    $recommender_userinfo = $user_model->where("id={$recommender_id}")->find();
                    if (!$recommender_userinfo || $recommender_userinfo['level_id'] == 0) {
                        $recommender_id = 0;
                    }
                    if (!$userinfo) {
                        //添加用户信息
                        $my_data = [
                            'openid' => $openid,
                            'nickname' => filter_nickname($wx_userinfo['nickname']),
                            'sex' => $wx_userinfo['sex'],
                            'province' => $wx_userinfo['province'],
                            'city' => $wx_userinfo['city'],
                            'country' => $wx_userinfo['country'],
                            'avatar' => $wx_userinfo['headimgurl'],
                            'unionid' => isset($wx_userinfo['unionid']) ? $wx_userinfo['unionid'] : '',
                            'subscribe' => $wx_userinfo['subscribe'],
                            'subscribe_time' => date('Y-m-d H:i:s', $wx_userinfo['subscribe_time']),
                            'recommender_id' => $recommender_id,
                            'create_time' => get_date_time()
                        ];
                        $user_id = $user_model->add($my_data);
                        //添加用户账户
                        $my_data = [
                            'user_id' => $user_id
                        ];
                        $user_account_model->add($my_data);
                        if ($recommender_id != 0) {
                            $invite_integral = $system['reward_rule']['invite_integral'];
                            if ($system['reward_rule']['invite_open'] && $invite_integral > 0) {
                                $account_info = $user_account_model->where("user_id={$recommender_id}")->find();
                                $balance = $account_info['integral'] + $invite_integral;
                                //添加积分记录
                                $my_data = [
                                    'user_id' => $recommender_id,
                                    'type' => 3,
                                    'integral' => $invite_integral,
                                    'balance' => $balance,
                                    'title' => '邀请好友成功',
                                    'description' => '邀请好友成功',
                                    'create_time' => get_date_time()
                                ];
                                $user_integral_model->add($my_data);
                                //更新用户积分
                                $my_data = [
                                    'integral' => $balance,
                                    'total_integral' => $account_info['total_integral'] + $invite_integral
                                ];
                                $user_account_model->where("user_id={$recommender_id}")->save($my_data);
                            }
                        }
                    } else {
                        $user_data = [
                            'subscribe' => $wx_userinfo['subscribe'],
                            'subscribe_time' => date('Y-m-d H:i:s', $wx_userinfo['subscribe_time'])
                        ];
                        if ($userinfo['recommender_id'] == 0 && $userinfo['level_id'] == 0 && $recommender_id != 0 && $userinfo['id'] != $recommender_id) {
                            $invite_integral = $system['reward_rule']['invite_integral'];
                            if ($system['reward_rule']['invite_open'] && $invite_integral > 0) {
                                $account_info = $user_account_model->where("user_id={$recommender_id}")->find();
                                $balance = $account_info['integral'] + $invite_integral;
                                //添加积分记录
                                $my_data = [
                                    'user_id' => $recommender_id,
                                    'type' => 3,
                                    'integral' => $invite_integral,
                                    'balance' => $balance,
                                    'title' => '邀请好友成功',
                                    'description' => '邀请好友成功',
                                    'create_time' => get_date_time()
                                ];
                                $user_integral_model->add($my_data);
                                //更新用户积分
                                $my_data = [
                                    'integral' => $balance,
                                    'total_integral' => $account_info['total_integral'] + $invite_integral
                                ];
                                $user_account_model->where("user_id={$recommender_id}")->save($my_data);
                            }
                            $user_data['recommender_id'] = $recommender_id;
                        }
                        $user_model->where("openid='{$openid}'")->save($user_data);
                    }
                }
                //公众号关注
                else {
                    if (!$userinfo) {
                        //添加用户信息
                        $my_data = [
                            'openid' => $openid,
                            'nickname' => filter_nickname($wx_userinfo['nickname']),
                            'sex' => $wx_userinfo['sex'],
                            'province' => $wx_userinfo['province'],
                            'city' => $wx_userinfo['city'],
                            'country' => $wx_userinfo['country'],
                            'avatar' => $wx_userinfo['headimgurl'],
                            'unionid' => isset($wx_userinfo['unionid']) ? $wx_userinfo['unionid'] : '',
                            'subscribe' => $wx_userinfo['subscribe'],
                            'subscribe_time' => date('Y-m-d H:i:s', $wx_userinfo['subscribe_time']),
                            'recommender_id' => 0,
                            'create_time' => get_date_time()
                        ];
                        $user_id = $user_model->add($my_data);
                        //添加用户账户
                        $my_data = [
                            'user_id' => $user_id
                        ];
                        $user_account_model->add($my_data);
                    } else {
                        $my_data = [
                            'subscribe' => $wx_userinfo['subscribe'],
                            'subscribe_time' => date('Y-m-d H:i:s', $wx_userinfo['subscribe_time'])
                        ];
                        $user_model->where("openid='{$openid}'")->save($my_data);
                    }
                }
                $return_str = '您好，' . $wx_userinfo['nickname'] . '。欢迎关注' . $system['wx_config']['name'] . '！' . htmlspecialchars_decode($system['wx_config']['subscribe_reply']);
                return [$return_str, 'text'];
            }
            //用户已关注时的事件推送
            elseif ($data['Event'] == 'SCAN') {
                $openid = $data['FromUserName'];
                $recommender_id = $data['EventKey'];
                $user_model = M('user');
                $user_account_model = M('user_account');
                $user_integral_model = M('user_integral');
                $userinfo = $user_model->where("openid='{$openid}'")->find();
                vendor('Wechat.TPWechat');
                $tp_wechat = new \TPWechat($wechat_options);
                $wx_userinfo = $tp_wechat->getUserInfo($openid);
                $recommender_userinfo = $user_model->where("id={$recommender_id}")->find();
                if (!$recommender_userinfo || $recommender_userinfo['level_id'] == 0) {
                    $recommender_id = 0;
                }
                if (!$userinfo) {
                    //添加用户信息
                    $my_data = [
                        'openid' => $openid,
                        'nickname' => filter_nickname($wx_userinfo['nickname']),
                        'sex' => $wx_userinfo['sex'],
                        'province' => $wx_userinfo['province'],
                        'city' => $wx_userinfo['city'],
                        'country' => $wx_userinfo['country'],
                        'avatar' => $wx_userinfo['headimgurl'],
                        'unionid' => isset($wx_userinfo['unionid']) ? $wx_userinfo['unionid'] : '',
                        'subscribe' => $wx_userinfo['subscribe'],
                        'subscribe_time' => date('Y-m-d H:i:s', $wx_userinfo['subscribe_time']),
                        'recommender_id' => $recommender_id,
                        'create_time' => get_date_time()
                    ];
                    $user_id = $user_model->add($my_data);
                    //添加用户账户
                    $my_data = [
                        'user_id' => $user_id
                    ];
                    $user_account_model->add($my_data);
                    if ($recommender_id != 0) {
                        $invite_integral = $system['reward_rule']['invite_integral'];
                        if ($system['reward_rule']['invite_open'] && $invite_integral > 0) {
                            $account_info = $user_account_model->where("user_id={$recommender_id}")->find();
                            $balance = $account_info['integral'] + $invite_integral;
                            //添加积分记录
                            $my_data = [
                                'user_id' => $recommender_id,
                                'type' => 3,
                                'integral' => $invite_integral,
                                'balance' => $balance,
                                'title' => '邀请好友成功',
                                'description' => '邀请好友成功',
                                'create_time' => get_date_time()
                            ];
                            $user_integral_model->add($my_data);
                            //更新用户积分
                            $my_data = [
                                'integral' => $balance,
                                'total_integral' => $account_info['total_integral'] + $invite_integral
                            ];
                            $user_account_model->where("user_id={$recommender_id}")->save($my_data);
                        }
                    }
                } else {
                    $user_data = [
                        'subscribe' => $wx_userinfo['subscribe'],
                        'subscribe_time' => date('Y-m-d H:i:s', $wx_userinfo['subscribe_time'])
                    ];
                    if ($userinfo['recommender_id'] == 0 && $userinfo['level_id'] == 0 && $recommender_id != 0 && $userinfo['id'] != $recommender_id) {
                        $invite_integral = $system['reward_rule']['invite_integral'];
                        if ($system['reward_rule']['invite_open'] && $invite_integral > 0) {
                            $account_info = $user_account_model->where("user_id={$recommender_id}")->find();
                            $balance = $account_info['integral'] + $invite_integral;
                            //添加积分记录
                            $my_data = [
                                'user_id' => $recommender_id,
                                'type' => 3,
                                'integral' => $invite_integral,
                                'balance' => $balance,
                                'title' => '邀请好友成功',
                                'description' => '邀请好友成功',
                                'create_time' => get_date_time()
                            ];
                            $user_integral_model->add($my_data);
                            //更新用户积分
                            $my_data = [
                                'integral' => $balance,
                                'total_integral' => $account_info['total_integral'] + $invite_integral
                            ];
                            $user_account_model->where("user_id={$recommender_id}")->save($my_data);
                        }
                        $user_data['recommender_id'] = $recommender_id;
                    }
                    $user_model->where("openid='{$openid}'")->save($user_data);
                }
                $return_str = '您好，' . $wx_userinfo['nickname'] . '。欢迎关注' . $system['wx_config']['name'] . '！' . htmlspecialchars_decode($system['wx_config']['subscribe_reply']);
                return [$return_str, 'text'];
            }
            //取消关注事件推送
            elseif ($data['Event'] == 'unsubscribe') {
                $openid = $data['FromUserName'];
                M('user')->where("openid='{$openid}'")->setField('subscribe', 0);
                return ['', 'null'];
            }
            //上报地理位置事件
            elseif ($data['Event'] == 'LOCATION') {
                return ['', 'null'];
            } else {
                return ['', 'null'];
            }
        }
        //接收普通消息 - 文本消息
        elseif ($data['MsgType'] == 'text') {
            $openid = $data['FromUserName'];
            vendor('Wechat.TPWechat');
            $tp_wechat = new \TPWechat($wechat_options);
            //$wx_userinfo = $tp_wechat->getUserInfo($openid);
            $wx_auto_reply = M('wx_auto_reply')
                    ->field('id,reply_all')
                    ->where("(match_type=0 and keyword like '%{$data['Content']}%') or (match_type=1 and keyword='{$data['Content']}')")
                    ->order('id desc')
                    ->find();
            if ($wx_auto_reply) {
                if ($wx_auto_reply['reply_all']) {
                    $wx_auto_reply_content_list = M('wx_auto_reply_content')
                            ->where("auto_reply_id={$wx_auto_reply['id']}")
                            ->order('sort asc')
                            ->select();
                    if ($wx_auto_reply_content_list) {
                        $cdn_url = get_cdn_url();
                        foreach ($wx_auto_reply_content_list as $v) {
                            //文字
                            if ($v['type'] == 1) {
                                $send_data = [
                                    'touser' => $openid,
                                    'msgtype' => 'text',
                                    'text' => [
                                        'content' => htmlspecialchars_decode($v['content'])
                                    ]
                                ];
                                $tp_wechat->sendCustomMessage($send_data);
                            }
                            //图片
                            elseif ($v['type'] == 2) {
                                $media = get_image_url($v['image'], $cdn_url);
                                $media_data = [
                                    "media" => '@' . $media
                                ];
                                $media_result = $tp_wechat->uploadMedia($media_data, 'image');
                                if ($media_result) {
                                    $send_data = [
                                        'touser' => $openid,
                                        'msgtype' => 'image',
                                        'image' => [
                                            'media_id' => $media_result['media_id']
                                        ]
                                    ];
                                    $tp_wechat->sendCustomMessage($send_data);
                                }
                            }
                            //图文
                            elseif ($v['type'] == 3) {
                                $articles[] = [
                                    'title' => $v['title'],
                                    'description' => $v['content'],
                                    'url' => $v['link'],
                                    'picurl' => get_image_url($v['image'], $cdn_url)
                                ];
                                $send_data = [
                                    'touser' => $openid,
                                    'msgtype' => 'news',
                                    'news' => [
                                        'articles' => $articles
                                    ]
                                ];
                                $tp_wechat->sendCustomMessage($send_data);
                            }
                        }
                    } else {
                        $send_data = [
                            'touser' => $openid,
                            'msgtype' => 'text',
                            'text' => [
                                'content' => htmlspecialchars_decode($system['wx_config']['default_reply'])
                            ]
                        ];
                        $tp_wechat->sendCustomMessage($send_data);
                        return ['1', 'transfer_customer_service'];
                    }
                } else {
                    $wx_auto_reply_content = M('wx_auto_reply_content')
                            ->where("auto_reply_id={$wx_auto_reply['id']}")
                            ->order('rand()')
                            ->find();
                    if ($wx_auto_reply_content) {
                        $cdn_url = get_cdn_url();
                        //文字
                        if ($wx_auto_reply_content['type'] == 1) {
                            return [htmlspecialchars_decode($wx_auto_reply_content['content']), 'text'];
                        }
                        //图片
                        elseif ($wx_auto_reply_content['type'] == 2) {
                            $media = get_image_url($wx_auto_reply_content['image'], $cdn_url);
                            $media_data = [
                                'media' => '@' . $media
                            ];
                            $media_result = $tp_wechat->uploadMedia($media_data, 'image');
                            if ($media_result) {
                                return [$media_result['media_id'], 'image'];
                            }
                        }
                        //图文
                        elseif ($wx_auto_reply_content['type'] == 3) {
                            $articles[] = [
                                'Title' => $wx_auto_reply_content['title'],
                                'Description' => $wx_auto_reply_content['content'],
                                'Url' => $wx_auto_reply_content['link'],
                                'PicUrl' => get_image_url($wx_auto_reply_content['image'], $cdn_url)
                            ];
                            return [$articles, 'news'];
                        }
                    } else {
                        $send_data = [
                            'touser' => $openid,
                            'msgtype' => 'text',
                            'text' => [
                                'content' => htmlspecialchars_decode($system['wx_config']['default_reply'])
                            ]
                        ];
                        $tp_wechat->sendCustomMessage($send_data);
                        return ['1', 'transfer_customer_service'];
                    }
                }
            } else {
                $send_data = [
                    'touser' => $openid,
                    'msgtype' => 'text',
                    'text' => [
                        'content' => htmlspecialchars_decode($system['wx_config']['default_reply'])
                    ]
                ];
                $tp_wechat->sendCustomMessage($send_data);
                return ['1', 'transfer_customer_service'];
            }
        }
        //接收普通消息 - 图片消息
        elseif ($data['MsgType'] == 'image') {
            return ['1', 'transfer_customer_service'];
        }
        //接收普通消息 - 语音消息
        elseif ($data['MsgType'] == 'voice') {
            return ['1', 'transfer_customer_service'];
        }
        //接收普通消息 - 视频消息
        elseif ($data['MsgType'] == 'video') {
            return ['1', 'transfer_customer_service'];
        }
        //接收普通消息 - 小视频消息
        elseif ($data['MsgType'] == 'shortvideo') {
            return ['1', 'transfer_customer_service'];
        }
        //接收普通消息 - 地理位置消息
        elseif ($data['MsgType'] == 'location') {
            return ['1', 'transfer_customer_service'];
        }
        //接收普通消息 - 链接消息
        elseif ($data['MsgType'] == 'link') {
            return ['1', 'transfer_customer_service'];
        } else {
            return ['1', 'transfer_customer_service'];
        }
    }

}
