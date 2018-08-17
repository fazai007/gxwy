<?php

namespace Admin\Model;

class GoodsModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'goods';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['type', 'require', '请选择商品类型', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['type', ['video', 'voice', 'article'], '商品类型错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH],
        ['name', 'require', '请输入商品名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['cate_id', 'require', '请选择商品分类', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['basic_sales', '/^[1-9]\d*|0$/', '基础销量必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['basic_praise', '/^[1-9]\d*|0$/', '基础点击数必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['basic_share', '/^[1-9]\d*|0$/', '基础分享数必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['shelf_life', '/^[1-9]\d*|0$/', '保质期天数必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['stock', 'require', '请输入总库存', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['stock', '/^[1-9]\d*|0$/', '总库存必须是整数，且不能为负数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['stock_warn', 'require', '请输入库存预警', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['stock_warn', '/^[1-9]\d*|0$/', '库存预警必须是整数，且不能为负数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sort', '/^[1-9]\d*|0$/', '排序必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['market_price', '/^[0-9]+(.[0-9]{1,2})?$/', '市场价格格式错误', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sale_price', 'require', '请输入销售价格', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sale_price', '/^[0-9]+(.[0-9]{1,2})?$/', '销售价格格式错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['member_sale_price', 'require', '请输入会员价格', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['member_sale_price', '/^[0-9]+(.[0-9]{1,2})?$/', '会员价格格式错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['cost_price', '/^[0-9]+(.[0-9]{1,2})?$/', '成本价格格式错误', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
//        ['purchase_sum', '/^[1-9]\d*|0$/', '每人限购必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
//        ['min_buy', '/^[1-9]\d*$/', '最少购买数必须是大于0的整数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function'],
        ['update_time', 'get_date_time', self::MODEL_BOTH, 'function']
    ];

}
