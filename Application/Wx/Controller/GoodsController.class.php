<?php

namespace Wx\Controller;

class GoodsController extends BaseController {

    protected $goods_model;
    protected $goods_type_list;

    public function __construct() {
        parent::__construct();
        $this->goods_model = D('Wx/Goods');
        $this->goods_type_list = [
            'video' => '视频',
            'voice' => '音频',
            'article' => '图文'
        ];
        $this->assign('goods_type_list', $this->goods_type_list);
    }

    /**
     * 课程
     */
    public function index() {
        $goods_cate_list = M('goods_cate')->field('id,name')
                ->where('pid=0 and status=1')
                ->order('sort asc,id asc')
                ->select();
        $this->assign('goods_cate_list', $goods_cate_list);
        $first_goods_cate = reset($goods_cate_list);
        $this->assign('cate_id', $first_goods_cate['id']);
        $seo = set_seo('课程');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

    /**
     * 异步获取商品列表
     */
    public function getListData() {
        if (IS_GET) {
            $cate_id = I('get.cate_id', 0, 'intval');
            $page = I('get.page', 1, 'intval');
            $data_list = $this->goods_model->getPageList($cate_id, $page, 10);
            foreach ($data_list['root'] as &$v) {
                $v['type_name'] = isset($this->goods_type_list[$v['type']]) ? $this->goods_type_list[$v['type']] : '';
                $v['href'] = U('Wx/Goods/item', ['id' => $v['id']]);
            }
            unset($v);
            $this->success($data_list);
        }
    }

    /**
     * 首页异步获取商品列表
     */
    public function getIndexListData() {
        if (IS_GET) {
            $page = I('get.page', 1, 'intval');
            $data_list = $this->goods_model->getIndexPageList($page, 10);
            foreach ($data_list['root'] as &$v) {
                $v['type_name'] = isset($this->goods_type_list[$v['type']]) ? $this->goods_type_list[$v['type']] : '';
                $v['href'] = U('Wx/Goods/item', ['id' => $v['id']]);
            }
            unset($v);
            $this->success($data_list);
        }
    }

    /*
     * 商品详情
     */
    public function item() {
        $id = I('id', 0, 'intval');
        $goods = $this->goods_model->getDetail($id);
        if (!$goods) {
            $this->alert('商品不存在或已下架');
        }
        $this->assign('goods', $goods);
        //更新点击数
        $this->goods_model->where("id={$id}")->setInc('praise_count');
        $seo = set_seo($goods['name']);
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id'], $goods['name'], $goods['introduction'], $goods['thumb']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }

    /**
     * 
     */
    public function checkVideo() {
        if (IS_POST) {
            $id = I('post.id', 0, 'intval');
            $goods = $this->goods_model->getDetail($id);
            if (!$goods) {
                $this->error('参数错误');
            }
            $user_goods_model = M('user_goods');
            $user_goods = $user_goods_model->where("user_id={$this->userinfo['id']}")->find();
            $user_goods_ids_arr = $user_goods ? explode(',', $user_goods['goods_id']) : [];
            //购买过此商品或免费并填写过资料或会免
            if (in_array($id, $user_goods_ids_arr) || ($goods['sale_price'] == 0 && $this->userinfo['name'] && $this->userinfo['mobile']) || ($this->userinfo['level_id'] > 0 && $goods['member_sale_price'] == 0)) {
                $this->success(1);
            }
            //免费提示完善资料
            if ($goods['sale_price'] == 0) {
                $this->success(2);
            }
            //会免提示购买会员
            if ($goods['member_sale_price'] == 0) {
                $this->success(3);
            }
            //需要购买
            $this->success(4);
        }
    }

    /**
     * 
     */
    public function checkBuy() {
        if (IS_POST) {
            $id = I('post.id', 0, 'intval');
            $goods = $this->goods_model->getDetail($id);
            if (!$goods) {
                $this->error('参数错误');
            }
            $user_goods_model = M('user_goods');
            $user_goods = $user_goods_model->where("user_id={$this->userinfo['id']}")->find();
            $user_goods_ids_arr = $user_goods ? explode(',', $user_goods['goods_id']) : [];
            if (in_array($id, $user_goods_ids_arr) || ($goods['sale_price'] == 0 && $this->userinfo['name'] && $this->userinfo['mobile']) || ($this->userinfo['level_id'] > 0 && $goods['member_sale_price'] == 0)) {
                $this->error('商品可直接查看，无需购买');
            }
            $this->success('success', U('Wx/Order/payment',['goods_id'=>$id]));
        }
    }

    /**
     * 积分商城
     */
    public function integral() {
        $goods_cate_list = M('goods_cate')->field('id,name')
                ->where('pid=0 and status=1')
                ->order('sort asc,id asc')
                ->select();
        $this->assign('goods_cate_list', $goods_cate_list);
        $first_goods_cate = reset($goods_cate_list);
        $this->assign('cate_id', $first_goods_cate['id']);
        $seo = set_seo('积分商城');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }
    
    /**
     * 首页异步获取积分商城商品列表
     */
    public function getIntegralListData() {
        if (IS_GET) {
            $cate_id = I('get.cate_id', 0, 'intval');
            $page = I('get.page', 1, 'intval');
            $data_list = $this->goods_model->getIntegralPageList($cate_id, $page, 10);
            foreach ($data_list['root'] as &$v) {
                $v['type_name'] = isset($this->goods_type_list[$v['type']]) ? $this->goods_type_list[$v['type']] : '';
                $v['href'] = U('Wx/Goods/iitem', ['id' => $v['id']]);
            }
            unset($v);
            $this->success($data_list);
        }
    }
    
    /*
     * 积分商品详情
     */
    public function iitem() {
        $id = I('id', 0, 'intval');
        $goods = $this->goods_model->getDetail($id);
        if (!$goods) {
            $this->alert('商品不存在或已下架');
        }
        $this->assign('goods', $goods);
        //更新点击数
        $this->goods_model->where("id={$id}")->setInc('praise_count');
        $seo = set_seo($goods['name']);
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id'], $goods['name'], $goods['introduction'], $goods['thumb']);
        $this->assign('wx_share', $wx_share);
        $this->display();
    }
    
    /**
     * 
     */
    public function checkVideo2() {
        if (IS_POST) {
            $id = I('post.id', 0, 'intval');
            $goods = $this->goods_model->getDetail($id);
            if (!$goods) {
                $this->error('参数错误');
            }
            $user_goods_model = M('user_goods');
            $user_goods = $user_goods_model->where("user_id={$this->userinfo['id']}")->find();
            $user_goods_ids_arr = $user_goods ? explode(',', $user_goods['goods_id']) : [];
            //购买过此商品
            if (in_array($id, $user_goods_ids_arr)) {
                $this->success(1);
            }
            //需要购买
            $this->success(2);
        }
    }
    
}
