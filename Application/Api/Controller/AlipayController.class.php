<?php
    
namespace Api\Controller;

use Think\Controller;

class AlipayController extends Controller {
    
    /**
     * 支付结果同步回调
     */
    public function response() {
        vendor('Alipay.lib.alipay_notify#class');
        $alipay_config = C('ALIPAY_CONFIG');
        $alipay_notify = new \AlipayNotify($alipay_config);
        // 验证支付数据
        $verify_result = $alipay_notify->verifyReturn();
        if ($verify_result) {
            //写入支付日志
            $data = [
                'content' => json_encode($_GET)
            ];
            M('alipay_log')->add($data);
            $trade_status = $_GET['trade_status'];
            if ($trade_status == 'TRADE_SUCCESS' || $trade_status == 'TRADE_FINISHED') {
                //订单号
                $order_no = $_GET['out_trade_no'];
                //付款金额
                $pay_amount = $_GET['total_fee'];
                //付款时间
                $pay_time = $_GET['notify_time'];
                //在线交易流水号
                $trade_no = $_GET['trade_no'];
            }
        }
        redirect(U('Wx/Order/payment', '', true, true));
    }

    /**
     * 支付结果异步回调
     */
    public function notify() {
        vendor('Alipay.lib.alipay_notify#class');
        $alipay_config = C('ALIPAY_CONFIG');
        $alipay_notify = new \AlipayNotify($alipay_config);
        // 验证支付数据
        $verify_result = $alipay_notify->verifyNotify();
        if ($verify_result) {
            //写入支付日志
            $data = [
                'content' => json_encode($_POST)
            ];
            M('alipay_log')->add($data);
            $trade_status = $_POST['trade_status'];
            if ($trade_status == 'TRADE_SUCCESS' || $trade_status == 'TRADE_FINISHED') {
                //订单号
                $order_no = $_POST['out_trade_no'];
                //付款金额
                $pay_amount = $_POST['total_fee'];
                //付款时间
                $pay_time = $_POST['gmt_payment'];
                //在线交易流水号
                $trade_no = $_POST['trade_no'];
            }
            echo 'success';
        } else {
            echo 'fail';
        }
    }

}
