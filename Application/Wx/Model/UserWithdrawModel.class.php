<?php

namespace Wx\Model;

class UserWithdrawModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_withdraw';

    /**
     * 自动验证规则
     */
    protected $_validate = array(
        array('alipay_account', 'require', '请填写支付宝账号', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('name', 'require', '请填写姓名', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('money', 'require', '请填写提现金额', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('money', '/^[1-9]\d*$/', '提现金额必须是正整数', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT)
    );

    /**
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'get_date_time', self::MODEL_INSERT, 'function')
    );
    
}
