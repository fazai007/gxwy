<?php

namespace Wx\Controller;

class IndexController extends BaseController {
    
    protected $goods_type_list;
    
    public function __construct() {
        parent::__construct();
        $this->goods_type_list = [
            'video' => '视频',
            'voice' => '音频',
            'article' => '图文'
        ];
        $this->assign('goods_type_list', $this->goods_type_list);
    }

    /**
     * 首页
     */
    public function index() {
        $banner = D('Wx/AdvertPosition')->getList(2);
        $this->assign('banner', $banner);
        $this->assign('type', 1);
        $single_page_list = M('single_page')
                ->field('id,title,thumb')
                ->where('id in (3,4,5) and status=1')
                ->order('id asc')
                ->select();
        $this->assign('single_page_list', $single_page_list);
        $seo = set_seo('李歌情感大讲堂');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id'], '李歌情感大讲堂', '真情告白，欢迎关注李歌情感大讲堂！');
        $this->assign('wx_share', $wx_share);
        $this->display();
    }
    
}
