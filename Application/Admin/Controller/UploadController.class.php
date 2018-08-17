<?php

namespace Admin\Controller;

use Think\Controller;

class UploadController extends Controller {

    public function __construct() {
        parent::__construct();
        if (!session('?admin_id')) {
            $result = [
                'code' => 0,
                'msg'  => '未登录'
            ];
            $this->ajaxReturn($result);
        }
    }

    /**
     * 上传文件
     */
    public function upload() {
        $file = $_FILES['file'];
        if (empty($file)) {
            $result = [
                'code' => 0,
                'msg'  => '未上传文件或超出服务器上传限制'
            ];
            $this->ajaxReturn($result);
        }
        $upload_config = C('UPLOAD_CONFIG');
        preg_match('/(\d+)(\w+)/', $upload_config['max_size'], $matches);
        $type = strtolower($matches[2]);
        $type_dict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $max_size = (int) $upload_config['max_size'] * pow(1024, isset($type_dict[$type]) ? $type_dict[$type] : 0);
        $suffix = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';
        $mime_type_arr = explode(',', strtolower($upload_config['mime_type']));
        $type_arr = explode('/', $file['type']);
        //验证文件后缀
        if ($upload_config['mime_type'] !== '*' && (!in_array($suffix, $mime_type_arr) || (stripos($type_arr[0] . '/', $upload_config['mime_type']) !== false && (!in_array($file['type'], $mime_type_arr) && !in_array($type_arr[0] . '/*', $mime_type_arr))))) {
            $result = [
                'code' => 0,
                'msg'  => '上传文件格式受限制'
            ];
            $this->ajaxReturn($result);
        }
        $root_path = '/Uploads/';
        $config = [
            'maxSize' => $max_size,
            'exts' => $mime_type_arr,
            'subName' => ['date', 'Ymd'],
            'rootPath' => '.' . $root_path
        ];
        $upload = new \Think\Upload($config);
        $info = $upload->upload();
        if (!$info) {
            $result = [
                'code' => 0,
                'msg'  => $upload->getError()
            ];
            $this->ajaxReturn($result);
        }
        $save_path = $root_path . $info['file']['savepath'] . $info['file']['savename'];
        $image_width = $image_height = 0;
        if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
            $image_info = getimagesize('.' . $save_path);
            $image_width = isset($image_info[0]) ? $image_info[0] : $image_width;
            $image_height = isset($image_info[1]) ? $image_info[1] : $image_height;
        }
        $data = [
            'admin_id' => (int) session('admin_id'),
            'user_id' => 0,
            'file_size' => $info['file']['size'],
            'image_width' => $image_width,
            'image_height' => $image_height,
            'image_type' => $suffix,
            'image_frames' => 0,
            'mime_type' => $info['file']['type'],
            'url' => $save_path,
            'upload_time' => date('Y-m-d H:i:s'),
            'storage' => 'local',
            'sha1' => $info['file']['sha1']
        ];
        M('attachment')->add(array_filter($data));
        $result = [
            'code' => 1,
            'msg'  => '上传成功',
            'data' => [
                'url' => $save_path
            ]
        ];
        $this->ajaxReturn($result);
    }

}
