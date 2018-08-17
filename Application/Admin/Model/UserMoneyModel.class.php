<?php

namespace Admin\Model;

class UserMoneyModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_money';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['money', 'require', '请输入调整金额', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT],
        ['money', '/^-?(?!0(\\d|\\.0+$|$))\\d+(\\.\\d{1,2})?$/', '调整金额格式错误', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function']
    ];

}
