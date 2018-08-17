<?php

namespace Admin\Model;

class WxAutoReplyContentModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'wx_auto_reply_content';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['auto_reply_id', 'require', '请选择自动回复关键字', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['type', 'require', '请选择回复类型', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sort', '/^[1-9]\d*|0$/', '排序必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function']
    ];

}
