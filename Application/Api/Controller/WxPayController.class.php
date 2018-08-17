<?php

namespace Api\Controller;

use Think\Controller;

class WxPayController extends Controller {

    /**
     * 订单
     */
    public function notify() {
        $raw_xml = file_get_contents('php://input');
        //获取微信支付配置信息
        $wxpay_config = get_system('wxpay_config');
        //jsApiPay初始化参数
        $notify_url = U('Api/WxPay/notify', '', true, true);
        $config = [
            'APPID' => $wxpay_config['app_id'],
            'MCHID' => $wxpay_config['mch_id'],
            'KEY' => $wxpay_config['pay_sign_key'],
            'APPSECRET' => $wxpay_config['app_secret'],
            'NOTIFY_URL' => $notify_url,
            'SSLCERT_PATH' => $wxpay_config['apiclient_cert'],
            'SSLKEY_PATH' => $wxpay_config['apiclient_key']
        ];
        \WxPay\lib\WxPayConfig::setConfig($config);
        $notify = new \WxPay\database\WxPayNotifyCallBack();
        $notify->handle(false);
        $res = $notify->getValues();
        if ($res['return_code'] === 'SUCCESS' && $res['return_msg'] === 'OK') {
            libxml_disable_entity_loader(true);
            $ret = json_decode(json_encode(simplexml_load_string($raw_xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            //写入支付日志
            $data = [
                'content' => json_encode($ret)
            ];
            M('wxpay_log')->add($data);
            //订单号
            $order_no = $ret['out_trade_no'];
            //付款金额
            $payment_amount = $ret['total_fee'] / 100;
            //付款时间
            $payment_time = $ret['time_end'];
            //在线交易流水号
            $trade_no = $ret['transaction_id'];
            //获取订单信息
            $order_model = M('order');
            $orderinfo = $order_model
                    ->field('id,user_id,recommender_id,need_pay_amount')
                    ->where("order_no='{$order_no}' and payment_status=0")
                    ->find();
            if ($orderinfo && $orderinfo['need_pay_amount'] == $payment_amount) {
                $user_model = M('user');
                $userinfo = $user_model->field('id,openid,nickname,level_id')
                        ->where("id={$orderinfo['user_id']}")
                        ->find();
                $order_model->startTrans();
                try {
                    //更新订单信息
                    $data = [
                        'payment_type' => 'wxpay',
                        'payment_status' => 1,
                        'payment_amount' => $payment_amount,
                        'payment_time' => $payment_time,
                        'trade_no' => $trade_no
                    ];
                    $order_model->where("id={$orderinfo['id']}")->save($data);
                    //添加订单日志
                    $data = [
                        'order_id' => $orderinfo['id'],
                        'type' => 1,
                        'operator_id' => $orderinfo['user_id'],
                        'operator_name' => $userinfo ? $userinfo['nickname'] : '',
                        'content' => '订单付款成功',
                        'create_time' => get_date_time()
                    ];
                    M('order_log')->add($data);
                    if ($userinfo) {
                        $user_account_model = M('user_account');
                        //积分奖励
                        $reward_rule = get_system('reward_rule');
                        $consume_integral = floor($reward_rule['consume_integral'] * $orderinfo['need_pay_amount']);
                        if ($reward_rule['consume_open'] && $consume_integral > 0) {
                            $account_info = $user_account_model->where("user_id={$userinfo['id']}")->find();
                            $balance = $account_info['integral'] + $consume_integral;
                            //添加积分记录
                            $data = [
                                'user_id' => $userinfo['id'],
                                'type' => 4,
                                'integral' => $consume_integral,
                                'balance' => $balance,
                                'data_from' => 1,
                                'data_id' => $orderinfo['id'],
                                'title' => '购买商品',
                                'description' => '购买商品成功',
                                'create_time' => get_date_time()
                            ];
                            M('user_integral')->add($data);
                            //更新用户积分
                            $data = [
                                'integral' => $balance,
                                'total_integral' => $account_info['total_integral'] + $consume_integral
                            ];
                            $user_account_model->where("user_id={$userinfo['id']}")->save($data);
                        }
                        $user_account_model->where("user_id={$userinfo['id']}")->setInc('total_consume', $orderinfo['need_pay_amount']);
                    }
                    //推荐人返佣
                    if ($orderinfo['commission'] > 0 && $orderinfo['recommender_id']) {
                        return_commission($orderinfo['recommender_id'], $orderinfo['commission'], 1, $orderinfo['id']);
                    }
                    $user_goods_model = M('user_goods');
                    $user_goods = $user_goods_model->where("user_id={$orderinfo['user_id']}")->find();
                    $user_goods_ids_arr = $user_goods ? explode(',', $user_goods['goods_id']) : [];
                    $goods_model = M('goods');
                    $order_goods = M('order_goods')
                            ->field('goods_id,num')
                            ->where("order_id={$orderinfo['id']}")
                            ->select();
                    foreach ($order_goods as $v) {
                        //更新商品销量
                        $goods_model->where("id={$v['goods_id']}")->setInc('sales_count', $v['num']);
                        //组合用户商品数组
                        if (!in_array($v['goods_id'], $user_goods_ids_arr)) {
                            $user_goods_ids_arr[] = $v['goods_id'];
                        }
                    }
                    $user_goods_ids = $user_goods_ids_arr ? implode(',', $user_goods_ids_arr) : '';
                    //添加或更新用户商品
                    if (!$user_goods) {
                        $data = [
                            'user_id' => $orderinfo['user_id'],
                            'goods_id' => $user_goods_ids
                        ];
                        $user_goods_model->add($data);
                    } else {
                        $user_goods_model->where("user_id={$orderinfo['user_id']}")->setField('goods_id', $user_goods_ids);
                    }
                    $order_model->commit();
                } catch (Exception $ex) {
                    $order_model->rollback();
                }
            }
        }
    }

    /**
     * 会员订单
     */
    public function notifyM() {
        $raw_xml = file_get_contents('php://input');
        //获取微信支付配置信息
        $wxpay_config = get_system('wxpay_config');
        //jsApiPay初始化参数
        $notify_url = U('Api/WxPay/notifyM', '', true, true);
        $config = [
            'APPID' => $wxpay_config['app_id'],
            'MCHID' => $wxpay_config['mch_id'],
            'KEY' => $wxpay_config['pay_sign_key'],
            'APPSECRET' => $wxpay_config['app_secret'],
            'NOTIFY_URL' => $notify_url,
            'SSLCERT_PATH' => $wxpay_config['apiclient_cert'],
            'SSLKEY_PATH' => $wxpay_config['apiclient_key']
        ];
        \WxPay\lib\WxPayConfig::setConfig($config);
        $notify = new \WxPay\database\WxPayNotifyCallBack();
        $notify->handle(false);
        $res = $notify->getValues();
        if ($res['return_code'] === 'SUCCESS' && $res['return_msg'] === 'OK') {
            libxml_disable_entity_loader(true);
            $ret = json_decode(json_encode(simplexml_load_string($raw_xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            //写入支付日志
            $data = [
                'content' => json_encode($ret)
            ];
            M('wxpay_log')->add($data);
            //订单号
            $order_no = $ret['out_trade_no'];
            //付款金额
            $payment_amount = $ret['total_fee'] / 100;
            //付款时间
            $payment_time = $ret['time_end'];
            //在线交易流水号
            $trade_no = $ret['transaction_id'];
            //获取订单信息
            $order_model = M('member_order');
            $orderinfo = $order_model
                    ->field('id,user_id,recommender_id,need_pay_amount')
                    ->where("order_no='{$order_no}' and payment_status=0")
                    ->find();
            if ($orderinfo && $orderinfo['need_pay_amount'] == $payment_amount) {
                $user_model = M('user');
                $userinfo = $user_model->field('id,openid,nickname,level_id')
                        ->where("id={$orderinfo['user_id']}")
                        ->find();
                $order_model->startTrans();
                try {
                    //更新订单信息
                    $data = [
                        'payment_type' => 'wxpay',
                        'payment_status' => 1,
                        'payment_amount' => $payment_amount,
                        'payment_time' => $payment_time,
                        'trade_no' => $trade_no
                    ];
                    $order_model->where("id={$orderinfo['id']}")->save($data);
                    //添加订单日志
                    $data = [
                        'order_id' => $orderinfo['id'],
                        'type' => 1,
                        'operator_id' => $orderinfo['user_id'],
                        'operator_name' => $userinfo ? $userinfo['nickname'] : '',
                        'content' => '订单付款成功',
                        'create_time' => get_date_time()
                    ];
                    M('member_order_log')->add($data);
                    if ($userinfo) {
                        $user_account_model = M('user_account');
                        //如果用户没有等级
                        if ($userinfo['level_id'] == 0) {
                            //更新用户等级
                            $data = [
                                'level_id' => 1,
                                'join_time' => get_date_time()
                            ];
                            $user_model->where("id={$userinfo['id']}")->save($data);
                            //添加用户日志
                            $data = [
                                'user_id' => $userinfo['id'],
                                'type' => 1,
                                'operator_id' => $userinfo['id'],
                                'operator_name' => $userinfo['nickname'],
                                'content' => '用户开通会员，等级由普通用户提升为云客',
                                'create_time' => get_date_time()
                            ];
                            M('user_log')->add($data);
                        }
                        //积分奖励
                        $reward_rule = get_system('reward_rule');
                        $consume_integral = floor($reward_rule['consume_integral'] * $orderinfo['need_pay_amount']);
                        if ($reward_rule['consume_open'] && $consume_integral > 0) {
                            $account_info = $user_account_model->where("user_id={$userinfo['id']}")->find();
                            $balance = $account_info['integral'] + $consume_integral;
                            //添加积分记录
                            $data = [
                                'user_id' => $userinfo['id'],
                                'type' => 4,
                                'integral' => $consume_integral,
                                'balance' => $balance,
                                'data_from' => 3,
                                'data_id' => $orderinfo['id'],
                                'title' => '开通会员',
                                'description' => '开通会员成功',
                                'create_time' => get_date_time()
                            ];
                            M('user_integral')->add($data);
                            //更新用户积分
                            $data = [
                                'integral' => $balance,
                                'total_integral' => $account_info['total_integral'] + $consume_integral
                            ];
                            $user_account_model->where("user_id={$userinfo['id']}")->save($data);
                        }
                        $user_account_model->where("user_id={$userinfo['id']}")->setInc('total_consume', $orderinfo['need_pay_amount']);
                    }
                    if ($orderinfo['recommender_id']) {
                        //推荐人返佣
                        return_commission($orderinfo['recommender_id'], $orderinfo['need_pay_amount'], 3, $orderinfo['id']);
                        //推荐人升级
                        update_level($orderinfo['recommender_id']);
                    }
                    $order_model->commit();
                } catch (Exception $ex) {
                    $order_model->rollback();
                }
            }
        }
    }

}
