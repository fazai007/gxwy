<?php

namespace Admin\Model;

class UserLevelModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_level';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['name', 'require', '请输入等级名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['direct_commission_percent', 'require', '请输入直推佣金比例', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['direct_commission_percent', '/^[1-9]\d*|0$/', '直推佣金比例只能填写0或正整数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['team_commission_percent', 'require', '请输入团队佣金比例', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['team_commission_percent', '/^[1-9]\d*|0$/', '团队佣金比例只能填写0或正整数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        //['get_commission_number', 'require', '请输入可得佣金层数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        //['get_commission_number', '/^[1-9]\d*|0$/', '可得佣金层数只能填写0或正整数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['level_up_direct_user_number', 'require', '请输入直推人数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['level_up_direct_user_number', '/^[1-9]\d*|0$/', '直推人数只能填写0或正整数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['level_up_team_user_number', 'require', '请输入团队人数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['level_up_team_user_number', '/^[1-9]\d*|0$/', '团队人数只能填写0或正整数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

    public function getList() {
        $user_level_data = $this->field('id,name,team_commission_percent,get_commission_number')->select();
        $user_level_list = [];
        foreach ($user_level_data as $v) {
            $user_level_list[$v['id']] = $v;
        }
        return $user_level_list;
    }

}
