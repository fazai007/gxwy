<?php

namespace Wx\Controller;

class CartController extends BaseController {

    protected $goods_model;

    public function __construct() {
        parent::__construct();
        $this->goods_model = D('Wx/Goods');
    }

    /**
     * 购物车
     */
    public function index() {
        $seo = set_seo('购物车');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }
    
}
