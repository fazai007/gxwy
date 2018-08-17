<?php

namespace Admin\Model;

class UserWithdrawModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_withdraw';

    /**
     * 自动验证规则
     */
    protected $_validate = [];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

}
