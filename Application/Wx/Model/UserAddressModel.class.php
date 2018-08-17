<?php

namespace Wx\Model;

class UserAddressModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user_address';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['name', 'require', '请填写收货人', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['mobile', 'require', '请填写手机号码', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['mobile', '/^134[0-8]\d{7}$|^13[^4]\d{8}$|^14[5-9]\d{8}$|^15[^4]\d{8}$|^16[6]\d{8}$|^17[0-8]\d{8}$|^18[\d]{9}$|^19[8,9]\d{8}$/', '请填写正确的手机号码', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['address', 'require', '请填写详细地址', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];
    
    /**
     * 获取用户收货地址
     * @param int $id 
     * @param int $user_id 用户id
     * @return array
     */
    public function getDetail($id, $user_id){
        $map = [
            'id' => ['eq', $id],
            'user_id' => ['eq', $user_id]
        ];
        return $this->where($map)->find();
    }
    
    /**
     * 获取收货地址列表
     * @param int $user_id 用户id
     * @param int $page 当前页码
     * @param int $page_size 每页条数
     * @return array
     */
    public function getPageList($user_id, $page, $page_size) {
        $sql = "select id,name,mobile,address,is_default 
                from __PREFIX__user_address 
                where user_id={$user_id} 
                order by is_default desc, id asc";
        return $this->pageQuery($sql, $page, $page_size);
    }
    
}
