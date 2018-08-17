<?php

namespace Admin\Model;

class SinglePageModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'single_page';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['title', 'require', '请输入标题', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sort', '/^[1-9]\d*|0$/', '排序必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', 'require', '请选择是否显示', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', [0, 1], '显示状态错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function'],
        ['update_time', 'get_date_time', self::MODEL_BOTH, 'function']
    ];

}
