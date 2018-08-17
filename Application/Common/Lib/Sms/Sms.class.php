<?php

/*
*短信类
 */
namespace Common\Lib\Sms;
#use Common\Lib\Sms\Rest;

class Sms extends Rest {

    private $ServerIP = 'sandboxapp.cloopen.com';
    private $ServerPort = '8883';
    private $SoftVersion = '2013-12-26';
    public $identifyArr = array(
        'code' => '' , //验证码模板
    );
    //主帐号
    protected $accountSid;
    //主帐号Token
    protected $accountToken;
    //应用Id
    protected $appId;

    function __construct($accountSid,$accountToken,$appId,$tempId) {

        $this->accountSid = $accountSid;
        $this->accountToken = $accountToken;
        $this->appId = $appId;
        $this->identifyArr['code'] = $tempId;
        $this->Init($this->ServerIP, $this->ServerPort, $this->SoftVersion);
    }

    function sms($accountSid,$accountToken,$appId,$tempId) {
        $this->accountSid = $accountSid;
        $this->accountToken = $accountToken;
        $this->appId = $appId;
        $this->identifyArr['code'] = $tempId;
        $this->__construct($this->ServerIP, $this->ServerPort, $this->SoftVersion);
    }

    /**
     * 初始化类
     * @param string $ServerIP 请求地址，格式如下，不需要写https://
     * @param string $ServerPort 请求端口
     * @param string $SoftVersion REST版本号
     */
    function Init($ServerIP, $ServerPort, $SoftVersion) {
        parent::__construct($ServerIP, $ServerPort, $SoftVersion);
        $this->setAccount($this->accountSid, $this->accountToken);
        $this->setAppId($this->appId);
    }

    /**
     * 发送模板短信
     *
     * @param string $mobile 手机号码
     *
     * @return array
     */
    public function mSendSms($mobile) {

        $tempId = $this->identifyArr['code'];
        $return_info = array('status' => false,'content' => '');
        $data = array(
            '0' => rand(1000,9999),
            '1' => 10
        );

        // 发送模板短信
        $result = $this->sendTemplateSMS($mobile, $data, $tempId);

        if ($result == NULL) {
            return $return_info;
        }
        if ($result->statusCode != 0) {
            return $return_info;
        } else {
            $validate_time = time() + 600;
            $return_info['status'] = true;
            $return_info['content'][0] = $validate_time;
            $return_info['content'][1] = $data[0];
            return $return_info;
        }

    }


}
?>
