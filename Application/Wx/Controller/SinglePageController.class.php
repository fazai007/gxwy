<?php

namespace Wx\Controller;

class SinglePageController extends BaseController {

    protected $single_page_model;

    public function __construct() {
        parent::__construct();
        $this->single_page_model = D('Wx/SinglePage');
    }

    /**
     * 关于我们
     */
    public function aboutUs() {
        $yunke_count = M('user')->where('level_id>0')->count('id');
        $yunke_count = $yunke_count + $GLOBALS['SYSTEM']['visit_config']['member_count'];
        $yunke_count_str = (string) $yunke_count;
        $zero_len = 6 - strlen($yunke_count_str);
        if ($zero_len > 0) {
            for ($i = 0; $i < $zero_len; $i++) {
                $yunke_count_str = '0' . $yunke_count_str;
            }
        }
        $yunke_count_arr = [];
        for ($i = 0; $i < strlen($yunke_count_str); $i++) {
            $yunke_count_arr[] = substr($yunke_count_str, $i, 1);
        }
        $this->assign('yunke_count_arr', $yunke_count_arr);
        $seo = set_seo('关于我们');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id'], '关于我们');
        $this->assign('wx_share', $wx_share);
        $this->display();
    }
    
    /**
     * 单页详情
     */
    public function detail($id = 0) {
        $single_page = $this->single_page_model->getDetail($id);
        if (!$single_page) {
            $this->alert('信息不存在或已删除');
        }
        $this->assign('single_page', $single_page);
        $seo = set_seo($single_page['title']);
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id'], $single_page['title'], $single_page['description']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

}
