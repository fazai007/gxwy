<?php

namespace Admin\Model;

class AuthGroupModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'auth_group';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['title', 'require', '请输入名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', 'require', '请选择状态', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', [0, 1], '状态错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

}
