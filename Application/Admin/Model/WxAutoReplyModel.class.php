<?php

namespace Admin\Model;

class WxAutoReplyModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'wx_auto_reply';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['keyword', 'require', '请输入关键字', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['match_type', [0, 1], '请选择匹配类型', self::MUST_VALIDATE, 'in', self::MODEL_BOTH],
        ['reply_all', [0, 1], '请选择是否回复全部', self::MUST_VALIDATE, 'in', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function']
    ];

}
