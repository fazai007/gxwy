<?php

namespace Admin\Model;

class UserModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['nickname', 'require', '请输入昵称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sex', 'require', '请选择性别', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['sex', array(0, 1, 2), '性别错误', self::MUST_VALIDATE, 'in', self::MODEL_BOTH],
        ['avatar', 'require', '请上传头像', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['name', 'require', '请输入姓名', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['mobile', 'require', '请输入手机号码', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH],
        ['mobile', '/^134[0-8]\d{7}$|^13[^4]\d{8}$|^14[5-9]\d{8}$|^15[^4]\d{8}$|^16[6]\d{8}$|^17[0-8]\d{8}$|^18[\d]{9}$|^19[8,9]\d{8}$/', '手机号码格式错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [
        ['create_time', 'get_date_time', self::MODEL_INSERT, 'function']
    ];

    /**
     * 获取用户信息
     * @param int $id 用户id
     */
    public function getLevelDetail($id) {
        return $this->alias('u')
                        ->join(C('DB_PREFIX') . 'user_level ul on ul.id=u.level_id', 'LEFT')
                        ->join(C('DB_PREFIX') . 'user_account ua on ua.user_id=u.id', 'LEFT')
                        ->field('u.id,u.openid,u.nickname,u.recommender_id,u.level_id,ul.name level_name,ul.direct_commission_percent,ul.team_commission_percent,ul.get_commission_number,ua.money,ua.total_money,ua.total_sales')
                        ->where("u.id={$id}")
                        ->find();
    }
    
    /**
     * 获取团队人员id
     * @param int $id 用户id
     * @param int $get_commission_number 可得佣金层数/团队统计层数
     * @param boolean $has_direct 是否包含直接团队
     * @param boolean $has_group 是否分组
     * @return array
     */
    public function getTeamUserIds($id, $get_commission_number, $has_direct, $has_group) {
        $team_user_ids = $this->getDistributors([$id], 1, $get_commission_number);
        if (!$has_direct) {
            unset($team_user_ids[1]);
        }
        if ($has_group) {
            return $team_user_ids;
        }
        $new_team_user_ids = [];
        foreach ($team_user_ids as $v) {
            foreach ($v as $val) {
                $new_team_user_ids[] = $val;
            }
        }
        return $new_team_user_ids;
    }

    /**
     * 获取团队人员id
     * @param array $recommender_ids 推荐人用户id数组
     * @param int $num 初始级数
     * @param int $limit_num 返回级数
     * @param array 返回用户id数组
     * @return array $ids 下几级用户id数组
     */
    public function getDistributors($recommender_ids, $num, $limit_num, &$ids = []) {
        if ($recommender_ids) {
            $recommender_ids = implode(',', $recommender_ids);
            $new_recommender_ids = [];
            $user_list = $this->field('id')->where("recommender_id in ({$recommender_ids}) and level_id>0")->select();
            foreach ($user_list as $v) {
                $ids[$num][] = $v['id'];
                $new_recommender_ids[] = $v['id'];
            }
            $num++;
            if ($num <= $limit_num) {
                $this->getDistributors($new_recommender_ids, $num, $limit_num, $ids);
            }
        }
        return $ids;
    }

}
