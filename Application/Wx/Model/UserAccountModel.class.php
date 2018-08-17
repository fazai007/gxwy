<?php

namespace Wx\Model;

class UserAccountModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_account';

    /**
     * 自动验证规则
     */
    protected $_validate = [];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

    /**
     * 获取用户账户信息
     * @param int $user_id 用户id
     * @return array
     */
    public function getAccountInfoByUserId($user_id) {
        return $this->where("user_id={$user_id}")->find();
    }

    /**
     * 获取总收入
     * @param array $user_ids 用户id
     * @return int
     */
    public function getTotalMoneySum($user_ids) {
        return $this->where("user_id in (" . implode(',', $user_ids) . ")")->sum('total_money');
    }

}
