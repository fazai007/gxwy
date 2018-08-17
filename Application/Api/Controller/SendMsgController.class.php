<?php

namespace Api\Controller;

use Think\Controller;

class SendMsgController extends Controller {

    /**
     * 获取access token
     * @return type
     */
    public static function getAccessToken() {
        vendor('Wechat.TPWechat');
        $wechat_options = get_wechat_options();
        $tp_wechat = new \TPWechat($wechat_options);
        return $tp_wechat->checkAuth();
    }

    /**
     * 发送文本信息
     * @param string $openid
     * @param string $content
     * @return type
     */
    public static function text($openid, $content) {
        vendor('Wechat.TPWechat');
        $wechat_options = get_wechat_options();
        $tp_wechat = new \TPWechat($wechat_options);
        $data = [
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content' => $content
            ]
        ];
        $result = $tp_wechat->sendCustomMessage($data);
        return json_encode($result);
        /*
          $token = self::getAccessToken();
          $data = '{"touser":"' . $openid . '","msgtype":"text","text":{"content":"' . $content . '"}}';
          $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $token;
          return self::apiNoticeIncrement($url, $data);
         */
    }

    /**
     * 发送图片消息
     * @param string $openid
     * @param int $media_id
     * @return type
     */
    public static function img($openid, $media_id) {
        vendor('Wechat.TPWechat');
        $wechat_options = get_wechat_options();
        $tp_wechat = new \TPWechat($wechat_options);
        $data = [
            'touser' => $openid,
            'msgtype' => 'image',
            'image' => [
                'media_id' => $media_id
            ]
        ];
        $result = $tp_wechat->sendCustomMessage($data);
        return json_encode($result);
        /*
          $token = self::getAccessToken();
          $data = '{"touser":"' . $openid . '","msgtype":"image","image":{"media_id":"' . $mediaId . '"}}';
          $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $token;
          return self::apiNoticeIncrement($url, $data);
         */
    }

    /**
     * 发送图文消息
     * @param string $openid
     * @param array $articles
     * @return type
     */
    public static function news($openid, $articles) {
        vendor('Wechat.TPWechat');
        $wechat_options = get_wechat_options();
        $tp_wechat = new \TPWechat($wechat_options);
        $data = [
            'touser' => $openid,
            'msgtype' => 'news',
            'news' => [
                'articles' => $articles
            ]
        ];
        $result = $tp_wechat->sendCustomMessage($data);
        return json_encode($result);
        /*
          $token = self::getAccessToken();
          $data = '{"touser":"' . $openid . '","msgtype":"image","image":{"media_id":"' . $mediaId . '"}}';
          $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $token;
          return self::apiNoticeIncrement($url, $data);
         */
    }
    
    /**
     * 发送海报
     * @param string $openid
     */
    public static function sendPoster($openid) {
        $user_id = M('user')->where("openid='{$openid}'")->getField('id');
        $poster_path = '/Uploads/poster/poster_' . $user_id . '.jpg';
        if (!file_exists('.' . $poster_path)) {
            UtilityController::createPosterPic($user_id);
        }
        $dir = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . $poster_path;
        $media_id = UtilityController::updateImage($dir);
        self::img($openid, $media_id);
    }

    /**
     * 重新发送海报
     * @param string $openid
     */
    public static function reSendPoster($openid) {
        $user_id = M('user')->where("openid='{$openid}'")->getField('id');
        $poster_path = '/Uploads/poster/poster_' . $user_id . '.jpg';
        if (!file_exists('.' . $poster_path)) {
            UtilityController::createPosterPic($user_id);
        }
        $dir = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . $poster_path;
        $media_id = UtilityController::updateImage($dir);
        self::img($openid, $media_id);
    }
    
    public static function apiNoticeIncrement($url, $data) {
        $urlarr = parse_url($url);
        $errno = "";
        $errstr = "";
        $transports = "";
        if ($urlarr["scheme"] == "https") {
            $transports = "ssl://";
            $urlarr["port"] = "443";
        } else {
            $transports = "tcp://";
            $urlarr["port"] = "8004";
        }
        $newurl = $transports . $urlarr['host'];
        $fp = @fsockopen($newurl, $urlarr['port'], $errno, $errstr, 60);
        if (!$fp) {
            die("ERROR: $errno - $errstr<br />\n");
        } else {
            fputs($fp, "POST " . $urlarr["path"] . "?" . $urlarr["query"] . " HTTP/1.1\r\n");
            fputs($fp, "Host: " . $urlarr["host"] . "\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($data) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $data . "\r\n\r\n");
            while (!feof($fp)) {
                $receive[] = @fgets($fp, 1024);
            }
            fclose($fp);
            $result = $receive[count($receive) - 1];
            return $result;
        }
    }
    
    public function curlSendPoster() {
        $openid = $_GET['openid'];
        self::sendPoster($openid);
    }

    public function reCurlSendPoster() {
        $openid = $_GET['openid'];
        self::reSendPoster($openid);
    }

    public function httpSendPoster() {
        $openid = $_GET['openid'];
        self::sendPoster($openid);
        echo '发送成功';
    }

    public function reHttpSendPoster() {
        $openid = $_GET['openid'];
        self::reSendPoster($openid);
        echo '重新生成并发送成功';
    }

}
