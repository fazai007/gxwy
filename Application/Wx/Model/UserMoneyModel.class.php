<?php

namespace Wx\Model;

class UserMoneyModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_money';

    /**
     * 自动验证规则
     */
    protected $_validate = [];

    /**
     * 自动完成规则
     */
    protected $_auto = [];
    
    /**
     * 获取云币记录列表
     * @param int $user_id 用户id
     * @param int $type 1：获取明细，2：提现明细
     * @param int $page 当前页码
     * @param int $page_size 每页条数
     * @return array
     */
    public function getPageList($user_id, $type, $page, $page_size) {
        $sql = "select type,money,balance,title,description,create_time 
                from __PREFIX__user_money 
                where user_id={$user_id}";
        switch ($type) {
            case 1:
                $sql .= " and type not in (3,4)";
                break;
            case 2:
                $sql .= " and type in (3,4)";
                break;
        }
        $sql .= " order by create_time desc";
        return $this->pageQuery($sql, $page, $page_size);
    }

}
