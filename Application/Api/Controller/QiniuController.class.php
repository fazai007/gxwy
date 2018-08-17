<?php

namespace Api\Controller;

use Think\Controller;

class QiniuController extends Controller {

    public function notify() {
        $upload_config = get_system('upload_config');
        vendor('Qiniu/QiniuAuth');
        $qiniu_auth = new \QiniuAuth($upload_config['access_key'], $upload_config['secret_key']);
        $content_type = 'application/x-www-form-urlencoded';
        $authorization = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$authorization && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        }
        $url = $upload_config['notify_url'];
        $body = file_get_contents('php://input');
        $ret = $qiniu_auth->verifyCallback($content_type, $authorization, $url, $body);
        if ($ret) {
            parse_str($body, $arr);
            $admin_id = isset($arr['admin']) ? $arr['admin'] : 0;
            $user_id = isset($arr['user']) ? $arr['user'] : 0;
            $image_info = json_decode($arr['imageInfo'], TRUE);
            $data = [
                'admin_id' => (int) $admin_id,
                'user_id' => (int) $user_id,
                'file_size' => $arr['filesize'],
                'image_width' => isset($image_info['width']) ? $image_info['width'] : 0,
                'image_height' => isset($image_info['height']) ? $image_info['height'] : 0,
                'image_type' => isset($image_info['format']) ? $image_info['format'] : '',
                'image_frames' => 1,
                'mime_type' => 'image/' . (isset($image_info['format']) ? $image_info['format'] : ''),
                'url' => '/' . $arr['key'],
                'upload_time' => date('Y-m-d H:i:s'),
                'storage' => 'qiniu'
            ];
            M('attachment')->add(array_filter($data));
            echo json(['ret' => 'success', 'code' => 1, 'data' => ['url' => $data['url']]]);
            exit();
        }
        echo json(['ret' => 'failed']);
        exit();
    }

}
