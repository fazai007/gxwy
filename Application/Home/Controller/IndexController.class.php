<?php
namespace Home\Controller;

use Common\Model\UserModel;
use Think\Controller;
use Common\Lib\Sms\Sms;

class IndexController extends Controller {

    /**
     *  登录首页
     * @author 牛青旺
     */
    public function index(){
        $is_wechat = is_wechat();
        
        if($is_wechat) {
            $this->display('index');
        }else{
            $this->display('wbIndex');
        }
    }

    /**
     * 登录注册
     * @author 牛青旺
     */
    public function login()
    {
        $mobile = I('mobile');
        $verifyCode = I('verify_code');
        $smsCode = session('sms_code');
        $sessionMobile = session('sms_mobile');
        $userObj = new UserModel();

        if($mobile != $sessionMobile) {
            $this->ajaxReturn(array('code' => 1, 'msg' => '接收验证码手机号错误'));
        }
        if($verifyCode != $smsCode) {
            $this->ajaxReturn(array('code' => 1, 'msg' => '验证码错误'));
        }

        $where = 'mobile = "' . $mobile . '"';
        $userInfo = $userObj->getUserInfo($where, 'user_id, mobile');
        if(!$userInfo) {
            $ip = get_client_ip();
            $now_time = time();
            $userData = [
                'mobile' => $mobile,
                'reg_time' => $now_time,
                'reg_ip' => $ip,
                'last_login_time' => $now_time,
                'last_login_ip' => $ip
            ];
            $userId = $userObj->addUser($userData);
        }else{
            $userId = $userInfo['user_id'];
        }
        session('user_id', $userId);
        $this->ajaxReturn(array('code' => 0, 'msg' => '登录成功'));
    }

    public function sendSms()
    {
        $mobile = I("post.mobile");
        $sms = new Sms(C('accountSid'), C('accountToken'), C('appId'), C('tempId'));
        if (session('sms_code')) {
            $this->ajaxReturn(['status' => false, 'msg' => '请不要频繁发送']);
        }
        $info = $sms->mSendSms($mobile);
        if ($info['status']) {
            session('sms_code', $info['content'][1], $info['content'][0]);
            session('sms_mobile', $mobile, $info['content'][0]);
            $this->ajaxReturn($info);
        } else {
            $this->ajaxReturn($info);
        }
    }
    
    /**
     * 模型测试
     * @author 牛青旺
     */
    public function testModel()
    {
        $userObj = new UserModel();
        $userList = $userObj->getUserList('nickname, sex, province', '', 'reg_time DESC');
        dump($userList);
    }
}