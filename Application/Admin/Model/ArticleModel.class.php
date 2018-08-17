<?php

namespace Admin\Model;

class ArticleModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'article';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['title', 'require', '请输入文章标题', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['cate_id', 'require', '请选择文章分类', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sort', '/^[1-9]\d*|0$/', '排序必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['click_count', '/^[1-9]\d*|0$/', '点击量必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['comment_count', '/^[1-9]\d*|0$/', '评论数必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH],
        ['share_count', '/^[1-9]\d*|0$/', '分享数必须是整数，且不能为负数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function'],
        ['update_time', 'get_date_time', self::MODEL_BOTH, 'function']
    ];

}
