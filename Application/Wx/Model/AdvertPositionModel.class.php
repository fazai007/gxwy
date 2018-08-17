<?php

namespace Wx\Model;

class AdvertPositionModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'advert_position';

    /**
     * 自动验证规则
     */
    protected $_validate = [];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

    /**
     * 获取广告列表
     * @param int $position_id 广告位id
     * @return array
     */
    public function getList($position_id) {
        $advert_position = $this->field('id,name,status')->find($position_id);
        if (!$advert_position || !$advert_position['status']) {
            return $advert_position;
        }
        $advert_position['advert_list'] = M('advert')->field('id,name,image,link')
                ->where("position_id={$position_id}")
                ->order('sort asc,id asc')
                ->select();
        return $advert_position;
    }
    
    /**
     * 获取广告列表
     * @param int $position_id 广告位id
     * @return array
     */
    public function getSingle($position_id) {
        $advert_position = $this->field('id,name,status')->find($position_id);
        if (!$advert_position || !$advert_position['status']) {
            return $advert_position;
        }
        $advert_position['advert_list'] = M('advert')->field('id,name,image,link')
                ->where("position_id={$position_id}")
                ->order('sort asc,id asc')
                ->find();
        return $advert_position;
    }

}
