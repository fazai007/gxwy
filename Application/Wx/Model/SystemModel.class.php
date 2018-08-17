<?php

namespace Wx\Model;

class SystemModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'system';

    /**
     * 自动验证规则
     */
    protected $_validate = [];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

    /**
     * 获取配置
     * @param string $name
     * @return array
     */
    public function getConfigByName($name) {
        $map['name'] = ['eq', $name];
        $value = $this->where($map)->getField('value');
        return unserialize($value);
    }

}
