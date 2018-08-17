<?php

namespace Admin\Model;

class AreaModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'area';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['name', 'require', '请输入地区名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['pid', 'require', '请选择上级地区', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sort', '/^[1-9]\d*|0$/', '排序必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', 'require', '请选择是否显示', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', [0, 1], '显示状态错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

}
