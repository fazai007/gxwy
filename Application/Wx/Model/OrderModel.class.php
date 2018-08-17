<?php

namespace Wx\Model;

class OrderModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'order';

    /**
     * 自动验证规则
     */
    protected $_validate = [];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

    /**
     * 获取订单列表
     * @param int $user_id 用户id
     * @param int $status 1：未付款，2：已付款
     * @param int $page 当前页码
     * @param int $page_size 每页条数
     * @return array
     */
    public function getPageList($user_id, $status, $page, $page_size) {
        $sql = "select id,order_no,create_time,payment_status 
                from __PREFIX__order 
                where user_id={$user_id}";
        switch ($status) {
            case 1:
                $sql .= " and payment_status=0";
                break;
            case 2:
                $sql .= " and payment_status=1";
                break;
        }
        $sql .= " order by create_time desc";
        return $this->pageQuery($sql, $page, $page_size);
    }
    
    /**
     * 获取订单详情
     * @param int $id 订单id
     * @param int $user_id 用户id
     * @return array
     */
    public function getDetail($id, $user_id) {
        return $this->where("id={$id} and user_id={$user_id}")->find();
    }

}
