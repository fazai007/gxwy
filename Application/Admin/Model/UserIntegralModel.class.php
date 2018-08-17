<?php

namespace Admin\Model;

class UserIntegralModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_integral';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['integral', 'require', '请输入调整积分', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT],
        ['integral', '/^-?[1-9]\d*$/', '调整积分只能填写非零整数', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function']
    ];

}
