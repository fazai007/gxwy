<?php

namespace Admin\Controller;

class BasicConfigController extends BaseController {

    protected $system_model;
    
    public function __construct() {
        parent::__construct();
        $this->system_model = M('system');
    }

    /**
     * 网站设置
     */
    public function webConfig() {
        if(IS_POST){
            $web_config = I('post.');
            $web_config['web_url'] = htmlspecialchars_decode($web_config['web_url']);
            $web_config['web_gov_record_url'] = htmlspecialchars_decode($web_config['web_gov_record_url']);
            $web_config['third_count'] = htmlspecialchars_decode($web_config['third_count']);
            $data = [
                'value' => serialize($web_config)
            ];
            $result = $this->system_model->where("name='web_config'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $web_config = $this->system_model->field('value')->where("name='web_config'")->find();
        $web_config = unserialize($web_config['value']);
        $this->assign('web_config', $web_config);
        $this->display();
    }
    
    /**
     * SEO设置
     */
    public function seoConfig() {
        if(IS_POST){
            $seo_config = I('post.');
            $seo_config['other'] = htmlspecialchars_decode($seo_config['other']);
            $data = [
                'value' => serialize($seo_config)
            ];
            $result = $this->system_model->where("name='seo_config'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $seo_config = $this->system_model->field('value')->where("name='seo_config'")->find();
        $seo_config = unserialize($seo_config['value']);
        $this->assign('seo_config', $seo_config);
        $this->display();
    }
    
    /**
     * 版权
     */
    public function copyright() {
        if(IS_POST){
            $copyright = I('post.');
            $copyright['link'] = htmlspecialchars_decode($copyright['link']);
            $copyright['description'] = htmlspecialchars_decode($copyright['description']);
            $data = [
                'value' => serialize($copyright)
            ];
            $result = $this->system_model->where("name='copyright'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $copyright = $this->system_model->field('value')->where("name='copyright'")->find();
        $copyright = unserialize($copyright['value']);
        $this->assign('copyright', $copyright);
        $this->display();
    }
    
    /**
     * 运营
     */
    public function visitConfig() {
        if(IS_POST){
            $visit_config = I('post.');
            $data = [
                'value' => serialize($visit_config)
            ];
            $result = $this->system_model->where("name='visit_config'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $visit_config = $this->system_model->field('value')->where("name='visit_config'")->find();
        $visit_config = unserialize($visit_config['value']);
        $this->assign('visit_config', $visit_config);
        $this->display();
    }
    
    /**
     * 注册与访问
     */
    public function regAndVisit() {
        if(IS_POST){
            $reg_and_visit = I('post.');
            $data = [
                'value' => serialize($reg_and_visit)
            ];
            $result = $this->system_model->where("name='reg_and_visit'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $reg_and_visit = $this->system_model->field('value')->where("name='reg_and_visit'")->find();
        $reg_and_visit = unserialize($reg_and_visit['value']);
        $this->assign('reg_and_visit', $reg_and_visit);
        $this->display();
    }
    
    /**
     * 上传设置
     */
    public function uploadConfig() {
        if(IS_POST){
            $upload_config = I('post.');
            $upload_config['upload_url'] = htmlspecialchars_decode($upload_config['upload_url']);
            $upload_config['cdn_url'] = htmlspecialchars_decode($upload_config['cdn_url']);
            $upload_config['notify_url'] = htmlspecialchars_decode($upload_config['notify_url']);
            $data = [
                'value' => serialize($upload_config)
            ];
            $result = $this->system_model->where("name='upload_config'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $upload_config = $this->system_model->field('value')->where("name='upload_config'")->find();
        $upload_config = unserialize($upload_config['value']);
        $this->assign('upload_config', $upload_config);
        $upload_url_list = [
            '华东' => 'https://upload.qiniup.com',
            '华北' => 'https://upload-z1.qiniup.com',
            '华南' => 'https://upload-z2.qiniup.com',
            '北美' => 'https://upload-na0.qiniup.com',
            '东南亚' => 'https://upload-as0.qiniup.com'
        ];
        $this->assign('upload_url_list', $upload_url_list);
        $this->display();
    }
    
    /**
     * 第三方登录
     */
    public function partyLogin() {
        $this->display();
    }
    
    /**
     * 通知系统
     */
    public function notify() {
        $this->display();
    }
    
    /**
     * 数据库设置
     */
    public function databaseConfig() {
        if(IS_POST){
            $database_config = I('post.');
            $data = [
                'value' => serialize($database_config)
            ];
            $result = $this->system_model->where("name='database_config'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $database_config = $this->system_model->field('value')->where("name='database_config'")->find();
        $database_config = unserialize($database_config['value']);
        $this->assign('database_config', $database_config);
        $this->display();
    }
    
    /**
     * 清除缓存
     */
    public function clear() {
        if (delete_dir_file(RUNTIME_DIR_PATH)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

}
