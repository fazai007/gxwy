<?php

namespace Admin\Model;

class GoodsCateModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'goods_cate';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['name', 'require', '请输入分类名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['short_name', 'require', '请输入分类简称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['pid', 'require', '请选择上级分类', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', 'require', '请选择是否显示', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['status', [0, 1], '显示状态错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH],
        ['sort', '/^[1-9]\d*|0$/', '排序必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];
    
    /**
     * 迭代获取子分类
     */
    public function getChild($ids = [], $pids = []) {
        $goods_cate_list = $this->field('id')->where("pid in (" . implode(',', $pids) . ")")->select();
        if (count($goods_cate_list) > 0) {
            $cids = [];
            foreach ($goods_cate_list as $v) {
                $cids[] = $v['id'];
            }
            $ids = array_merge($ids, $cids);
            return $this->getChild($ids, $cids);
        } else {
            return $ids;
        }
    }

}
