<?php

namespace Admin\Model;

class AdminUserModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'admin_user';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['username', 'require', '请输入用户名', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['username', '', '用户名已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH],
        ['password', 'require', '请输入密码', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT],
        ['password', '6,30', '密码必须6到30位', self::MUST_VALIDATE, 'length', self::MODEL_INSERT],
        ['password', 'confirm_password', '两次输入密码不一致', self::MUST_VALIDATE, 'confirm', self::MODEL_INSERT],
        ['password', 'require', '请输入密码', self::VALUE_VALIDATE, 'regex', self::MODEL_UPDATE],
        ['password', '6,30', '密码必须6到30位', self::VALUE_VALIDATE, 'length', self::MODEL_UPDATE],
        ['password', 'confirm_password', '两次输入密码不一致', self::VALUE_VALIDATE, 'confirm', self::MODEL_UPDATE],
        ['confirm_password', 'require', '请输入重复密码', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT],
        ['confirm_password', '6,30', '密码必须6到30位', self::MUST_VALIDATE, 'length', self::MODEL_INSERT],
        ['confirm_password', 'password', '两次输入密码不一致', self::MUST_VALIDATE, 'confirm', self::MODEL_INSERT],
        ['confirm_password', 'require', '请输入重复密码', self::VALUE_VALIDATE, 'regex', self::MODEL_UPDATE],
        ['confirm_password', '6,30', '密码必须6到30位', self::VALUE_VALIDATE, 'length', self::MODEL_UPDATE],
        ['confirm_password', 'password', '两次输入密码不一致', self::VALUE_VALIDATE, 'confirm', self::MODEL_UPDATE],
        ['status', 'require', '请选择状态', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', [0, 1], '状态错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH],
        ['group_id', 'require', '请选择所属权限组', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function']
    ];

}
