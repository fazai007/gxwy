<?php

namespace Wx\Model;

class SinglePageModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'single_page';

    /**
     * 自动验证规则
     */
    protected $_validate = array(
    );

    /**
     * 自动完成规则
     */
    protected $_auto = array(
    );

    /**
     * 获取单页详情
     * @param int $id 单页id
     * @return array
     */
    public function getDetail($id) {
        return $this->where("id={$id} and status=1")->find();
    }

}
