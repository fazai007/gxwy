<?php

namespace Admin\Controller;

class WxConfigController extends BaseController {

    protected $system_model;
    
    public function __construct() {
        parent::__construct();
        $this->system_model = M('system');
    }

    /**
     * 公众号管理
     */
    public function index() {
        if(IS_POST){
            $wx_config = I('post.');
            $data = [
                'value' => serialize($wx_config)
            ];
            $result = $this->system_model->where("name='wx_config'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $wx_config = $this->system_model->field('value')->where("name='wx_config'")->find();
        $wx_config = unserialize($wx_config['value']);
        $wx_conf = C('WX_CONFIG');
        $wx_config['url'] = $wx_conf['url'];
        $this->assign('wx_config', $wx_config);
        $this->display();
    }
    
    /**
     * 微信菜单管理
     * 推广二维码管理
     * 回复设置    关注时回复、关键字回复、默认回复
     * 消息素材管理 文本、单图文、多图文
     * 分享内容设置
     * 模板消息设置
     * 粉丝列表  
     * 一键关注设置
     * 客服管理
     */
    
}
