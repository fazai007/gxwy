<?php

namespace Wx\Model;

class UserIntegralModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_integral';

    /**
     * 自动验证规则
     */
    protected $_validate = [];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

    /**
     * 获取用户今日是否已签到
     * @param int $user_id 用户id
     * @return int
     */
    public function hasSign($user_id) {
        $today = date('Y-m-d');
        $map = [
            'user_id' => ['eq', $user_id],
            'type' => ['eq', 1],
            'create_time' => ['between', [$today . ' 00:00:00', $today . ' 23:59:59']]
        ];
        return $this->where($map)->count('id');
    }
    
    /**
     * 获取积分记录列表
     * @param int $user_id 用户id
     * @param int $type 1：支出，2：收入
     * @param int $page 当前页码
     * @param int $page_size 每页条数
     * @return array
     */
    public function getPageList($user_id, $type, $page, $page_size) {
        $sql = "select type,integral,balance,title,description,create_time 
                from __PREFIX__user_integral 
                where user_id={$user_id}";
        switch ($type) {
            case 1:
                $sql .= " and integral<0";
                break;
            case 2:
                $sql .= " and integral>0";
                break;
        }
        $sql .= " order by create_time desc";
        return $this->pageQuery($sql, $page, $page_size);
    }

}
