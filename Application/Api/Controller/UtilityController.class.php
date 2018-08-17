<?php

namespace Api\Controller;

use Think\Controller;
use Think\Image;

class UtilityController extends Controller {

    /**
     * 生成二维码
     * @param int $user_id 用户id
     */
    public static function createQrcode($user_id) {
        $file_name = './Uploads/poster/qrcode_' . $user_id . '.jpg';
        if ($user_id < 100000) {//生成微信二维码
            vendor('Wechat.TPWechat');
            $wechat_options = get_wechat_options();
            $tp_wechat = new \TPWechat($wechat_options);
            $result = $tp_wechat->getQRCode($user_id, 1);
            if ($result) {
                $scene = $tp_wechat->getQRUrl($result['ticket']);
                $file = fopen($scene, 'rb');
                if ($file) {
                    $newf = fopen($file_name, 'wb');
                    if ($newf) {
                        while (!feof($file)) {
                            fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                        }
                    }
                    if ($file) {
                        fclose($file);
                    }
                    if ($newf) {
                        fclose($newf);
                    }
                }
            }
        } else {//生成链接二维码
            vendor('qrcode.phpqrcode');
            $error_correction_level = 'L';
            $matrix_point_size = 14;
            $value = domain() . '/index.php/Wx/Index/index/recommender_id/' . $user_id . '.html';
            \QRcode::jpg($value, $file_name, $error_correction_level, $matrix_point_size, 2);
            $image = new Image();
            $image->open($file_name);
            $image->thumb(430, 430)->save($file_name);
        }
    }

    /**
     * 生成头像
     * @param int $user_id 用户id
     * @param string $avatar 用户头像url
     */
    public static function createAvatar($user_id, $avatar) {
        $file_name = './Uploads/poster/avatar_' . $user_id . '.jpg';
        if (!file_exists($file_name) && $avatar) {
            $file = fopen($avatar, 'rb');
            if ($file) {
                $new_file = fopen($file_name, 'wb');
                if ($new_file) {
                    while (!feof($file)) {
                        fwrite($new_file, fread($file, 1024 * 8), 1024 * 8);
                    }
                }
                if ($file) {
                    fclose($file);
                }
                if ($new_file) {
                    fclose($new_file);
                }
            }
        }
    }

    /**
     * 将二维码添加到海报
     * @param int $user_id 用户id
     */
    public static function addQrcodeToPosterPic($user_id) {
        $dst = './Public/Wx/images/poster.jpg';
        //得到原始图片信息
        $dst_image = imagecreatefromjpeg($dst);
        //水印图像
        $src = './Uploads/poster/qrcode_' . $user_id . '.jpg';
        $src_image = imagecreatefromjpeg($src);
        $thumb = imagecreatetruecolor(290, 290);
        // Resize
        imagecopyresized($thumb, $src_image, 0, 0, 0, 0, 290, 290, 430, 430);
        //水印透明度
        $alpha = 100;
        //合并水印图片
        //imagecopymerge(原圖Resource, 浮水印圖Resource, 浮水印要放的目標位置x, 浮水印要放的目標位置y, 0, 0, 浮水印圖的寬度, 浮水印圖的高度, alpha transparency);
        imagecopymerge($dst_image, $thumb, 232, 618, 0, 0, 290, 290, $alpha);
        //输出合并后水印图片
        imagejpeg($dst_image, './Uploads/poster/poster_' . $user_id . '.jpg');
    }

    /**
     * 将头像添加到海报
     * @param int $user_id 用户id
     * @param string $avatar 头像图片路径
     */
    public static function addAvatarToPosterPic($user_id, $avatar) {
        if ($avatar && file_exists($avatar)) {
            $dst = './Uploads/poster/poster_' . $user_id . '.jpg';
            //得到原始图片信息
            $dst_image = imagecreatefromjpeg($dst);
            //水印图像
            $src = $avatar;
            $image = new Image();
            $image->open($src);
            $image->thumb(110, 110)->save($src);
            $src_image = imagecreatefromjpeg($src);
            //水印透明度
            $alpha = 100;
            //合并水印图片
            //imagecopymerge(原圖Resource, 浮水印圖Resource, 浮水印要放的目標位置x, 浮水印要放的目標位置y, 0, 0, 浮水印圖的寬度, 浮水印圖的高度, alpha transparency);
            imagecopymerge($dst_image, $src_image, 311, 270, 0, 0, 110, 110, $alpha);
            //输出合并后水印图片
            imagejpeg($dst_image, './Uploads/poster/poster_' . $user_id . '.jpg');
        }
    }

    /**
     * 将文本添加到海报
     * @param string $nickname 昵称
     * @param string $sex 性别
     * @param string $province 省份
     * @param int $user_id 用户id
     */
    public static function addTextToPosterPic($nickname, $sex, $province, $user_id) {
        $sex_list = [0 => '未知', 1 => '男', 2 => '女'];
        $sex = isset($sex_list[$sex]) ? $sex_list[$sex] : '未知';
        $image = new Image();
        $font = './Public/Common/font/msyh.ttf';
        $dst = './Uploads/poster/poster_' . $user_id . '.jpg';
        $image->open($dst)->text($nickname, $font, 36, '#dcc694', \Think\Image::IMAGE_WATER_NORTHWEST, [302, 402])->save($dst);
        $image->open($dst)->text($sex . ' | ' . $province, $font, 22, '#dcc694', \Think\Image::IMAGE_WATER_NORTHWEST, [300, 456])->save($dst);
    }

    /**
     * 生成海报
     * @param int $user_id 用户id
     * @return string
     */
    public static function createPosterPic($user_id) {
        $userinfo = M('user')->field('id,nickname,avatar,sex,province')->find($user_id);
        $save_path = './Uploads/poster';
        if (!file_exists($save_path)) {
            mkdir($save_path);
        }
        self::createAvatar($user_id, $userinfo['avatar']);
        self::createQrcode($user_id);
        self::addQrcodeToPosterPic($user_id);
        self::addAvatarToPosterPic($user_id, './Uploads/poster/avatar_' . $user_id . '.jpg');
        self::addTextToPosterPic($userinfo['nickname'], $userinfo['sex'], $userinfo['province'], $user_id);
        return true;
    }

    /**
     * 上传图片
     * @param string $local_image_url 本地图片链接
     * @return string
     */
    public static function updateImage($local_image_url) {
        $data = [
            'media' => '@' . $local_image_url
        ];
        vendor('Wechat.TPWechat');
        $wechat_options = get_wechat_options();
        $tp_wechat = new \TPWechat($wechat_options);
        $result = $tp_wechat->uploadMedia($data, 'image');
        return $result['media_id'];
    }

    /**
     * 发送海报
     * @param string $openid
     * @return string
     */
    public static function sendPoster($openid) {
        $url = domain() . '/index.php/Api/SendMsg/curlSendPoster?openid=' . $openid;
        $fp = fopen('getPoster.log', 'a');
        fwrite($fp, strval(date('Y-m-d H:i:s', time())) . '---------' . $url . '----------' . $openid . '\r\n');
        fclose($fp);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOSIGNAL, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_exec($curl);
        curl_close($curl);
        return 'sucess';
    }

    /**
     * 重新发送海报
     * @param string $openid
     * @return string
     */
    public static function reSendPoster($openid) {
        $url = domain() . '/index.php/Api/SendMsg/reCurlSendPoster?openid=' . $openid;
        $fp = fopen('getPoster.log', 'a');
        fwrite($fp, strval(date('Y-m-d H:i:s', time())) . '-------------------' . $openid . '\r\n');
        fclose($fp);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOSIGNAL, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_exec($curl);
        curl_close($curl);
        return 'sucess';
    }

}
