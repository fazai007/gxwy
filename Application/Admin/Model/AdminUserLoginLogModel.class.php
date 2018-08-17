<?php

namespace Admin\Model;

class AdminUserLoginLogModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'admin_user_login_log';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['user_id', 'require', '管理员id不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['login_time', 'get_date_time', self::MODEL_INSERT, 'function'],
        ['login_ip', 'get_client_ip', self::MODEL_INSERT, 'function']
    ];

}
