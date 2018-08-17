<?php

namespace Admin\Model;

class MenuModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'auth_rule';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['title', 'require', '请输入菜单名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['pid', 'require', '请选择上级菜单', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['name', 'require', '请输入控制器方法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sort', '/^[1-9]\d*|0$/', '排序必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', 'require', '请选择是否显示', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', [0, 1], '显示状态错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH]
            
            
            
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

}
