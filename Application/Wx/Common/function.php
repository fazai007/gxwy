<?php

/**
 * 检测用户是否登录
 * @param int $recommender_id 推荐人id
 * @return type int 0：未登录，大于0：当前登录用户ID
 */
function is_user_login($recommender_id) {
    return D('Wx/User')->isLogin($recommender_id);
}

/**
 * 获取支付方式
 */
function get_payment_list(){
    $payment_config_list = C('PAYMENT_LIST');
    $payment_list = [];
    foreach ($payment_config_list as $k => $v) {
        $payment_config = $GLOBALS['SYSTEM'][$k . '_config'];
        if ($v['status'] && $payment_config && isset($payment_config['is_use']) && $payment_config['is_use']) {
            $payment_list[$k] = $v;
        }
    }
    return $payment_list;
}
