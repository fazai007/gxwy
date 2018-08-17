<?php

namespace Admin\Controller;

class TradeConfigController extends BaseController {

    protected $system_model;

    public function __construct() {
        parent::__construct();
        $this->system_model = M('system');
    }

    /**
     * 交易设置
     */
    public function index() {
        if (IS_POST) {
            $trade_config = I('post.');
            $data = [
                'value' => serialize($trade_config)
            ];
            $result = $this->system_model->where("name='trade_config'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $trade_config = $this->system_model->field('value')->where("name='trade_config'")->find();
        $trade_config = unserialize($trade_config['value']);
        $this->assign('trade_config', $trade_config);
        $order_delivery_complete_time_list = [
            0 => '立即',
            1 => '1天',
            2 => '2天',
            3 => '3天',
            4 => '4天',
            5 => '5天',
            6 => '6天',
            7 => '7天'
        ];
        $this->assign('order_delivery_complete_time_list', $order_delivery_complete_time_list);
        $shopping_back_integral_list = [
            1 => '订单已完成',
            2 => '已收货',
            3 => '支付完成'
        ];
        $this->assign('shopping_back_integral_list', $shopping_back_integral_list);
        $this->display();
    }

    /**
     * 支付配置
     */
    public function paymentConfig() {
        $payment_config = C('PAYMENT_LIST');
        $payment_list = [];
        foreach ($payment_config as $k => $v) {
            if ($v['status']) {
                $config = $this->system_model->field('value')->where("name='{$k}_config'")->find();
                $config = $config['value'] ? unserialize($config['value']) : [];
                $payment_list[$k] = array_merge($v, $config);
            }
        }
        $this->assign('payment_list', $payment_list);
        $this->display();
    }
    
    /**
     * 修改配置
     */
    public function editPaymentConfig($type) {
        $payment_config = $this->system_model->field('value')->where("name='{$type}_config'")->find();
        if (IS_POST) {
            if (!$payment_config) {
                $this->error('支付方式不存在');
            }
            $post_payment_config = I('post.');
            $data = [
                'value' => serialize($post_payment_config)
            ];
            $result = $this->system_model->where("name='{$type}_config'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $this->assign('type', $type);
        $payment_config = unserialize($payment_config['value']);
        $this->assign('payment_config', $payment_config);
        $this->display();
    }
    
    /**
     * 状态切换
     */
    public function isUseToggle($type, $is_use) {
        $config = $this->system_model->field('value')->where("name='{$type}_config'")->find();
        if (!$config) {
            $this->error('支付方式不存在');
        }
        if ($is_use != 0 && $is_use != 1) {
            $this->error('参数错误');
        }
        $config = unserialize($config['value']);
        $config['is_use'] = $is_use;
        $data = [
            'value' => serialize($config)
        ];
        $result = $this->system_model->where("name='{$type}_config'")->save($data);
        if ($result !== false) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

}
