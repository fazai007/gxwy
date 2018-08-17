<?php

namespace Wx\Model;

class MessageModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'message';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['mobile', 'require', '请输入手机号码', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT],
        ['mobile', '/^134[0-8]\d{7}$|^13[^4]\d{8}$|^14[5-9]\d{8}$|^15[^4]\d{8}$|^16[6]\d{8}$|^17[0-8]\d{8}$|^18[\d]{9}$|^19[8,9]\d{8}$/', '手机号码格式错误', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT],
        ['content', 'require', '请填写留言内容', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function']
    ];
    
}
