<?php

namespace Admin\Model;

class AdvertModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'advert';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['name', 'require', '请输入广告名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['position_id', 'require', '请选择广告位', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sort', '/^[1-9]\d*|0$/', '排序必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

}
