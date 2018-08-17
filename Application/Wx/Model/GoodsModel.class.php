<?php

namespace Wx\Model;

class GoodsModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'goods';

    /**
     * 自动验证规则
     */
    protected $_validate = [];

    /**
     * 自动完成规则
     */
    protected $_auto = [];
    
    /**
     * 获取商品列表
     * @param int $cate_id 分类id
     * @param int $page 当前页码
     * @param int $page_size 每页条数
     * @return array
     */
    public function getPageList($cate_id, $page, $page_size) {
        $sql = "select id,type,name,thumb 
                from __PREFIX__goods 
                where cate_id={$cate_id} and is_sale=1 and is_delete=0 and integral_select<>3 
                order by sort asc,id asc";
        return $this->pageQuery($sql, $page, $page_size);
    }
    
    /**
     * 获取商品列表
     * @param int $page 当前页码
     * @param int $page_size 每页条数
     * @return array
     */
    public function getIndexPageList($page, $page_size) {
        $sql = "select id,type,name,thumb 
                from __PREFIX__goods 
                where type='video' and is_sale=1 and sale_price=0 and is_delete=0 and integral_select<>3 
                order by sort asc,id asc";
        return $this->pageQuery($sql, $page, $page_size);
    }
    
    /**
     * 获取商品详情
     * @param int $id 商品id
     * @return array
     */
    public function getDetail($id) {
        return $this->where("id={$id} and is_sale=1 and is_delete=0")->find($id);
    }
    
    /**
     * 获取积分商品列表
     * @param int $cate_id 分类id
     * @param int $page 当前页码
     * @param int $page_size 每页条数
     * @return array
     */
    public function getIntegralPageList($cate_id, $page, $page_size) {
        $sql = "select id,type,name,thumb,integral_price 
                from __PREFIX__goods 
                where cate_id={$cate_id} and is_sale=1 and is_delete=0 and integral_select<>0 
                order by sort asc,id asc";
        return $this->pageQuery($sql, $page, $page_size);
    }
    
}
