<?php

namespace WxPay\lib;

/**
 * 微信支付API异常类
 *
 * Class WxPayException
 * @package \WxPay\lib
 * @author goldeagle
 */
class WxPayException extends \Exception {

    public function errorMessage() {
        return $this->getMessage();
    }

}
