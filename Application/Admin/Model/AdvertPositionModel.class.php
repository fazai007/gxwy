<?php

namespace Admin\Model;

class AdvertPositionModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'advert_position';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['name', 'require', '请输入名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', 'require', '请选择是否启用', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', [0, 1], '启用状态错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH],
        
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

}
