<?php

namespace Wx\Model;

class UserModel extends BaseModel {

    /**
     * 数据库表名
     */
    protected $tableName = 'user';

    /**
     * 自动验证规则
     */
    protected $_validate = [
        ['name', 'require', '请填写真实姓名', self::MUST_VALIDATE, 'regex', self::MODEL_UPDATE],
        ['mobile', 'require', '请填写手机号', self::MUST_VALIDATE, 'regex', self::MODEL_UPDATE],
        ['mobile', '/^134[0-8]\d{7}$|^13[^4]\d{8}$|^14[5-9]\d{8}$|^15[^4]\d{8}$|^16[6]\d{8}$|^17[0-8]\d{8}$|^18[\d]{9}$|^19[8,9]\d{8}$/', '请填写正确的手机号', self::MUST_VALIDATE, 'regex', self::MODEL_UPDATE],
        ['wx_number', 'require', '请填写微信号', self::MUST_VALIDATE, 'regex', self::MODEL_UPDATE]
    ];

    /**
     * 自动完成规则
     */
    protected $_auto = [];

    /**
     * 设置登录状态
     * @param int $id 用户id
     * @return boolean
     */
    public function autoLogin($id) {
        $auth = ['id' => $id];
        cookie('user_auth', auth_code(json_encode($auth), 'ENCODE'), 3600 * 24 * 365);
        cookie('user_auth_sign', auth_code($this->dataAuthSign($auth), 'ENCODE'), 3600 * 24 * 365);
        return true;
    }

    /**
     * 数据签名认证
     * @param  array  $data 被认证的数据
     * @return string       签名
     */
    public function dataAuthSign($data) {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array) $data;
        }
        ksort($data); //排序
        $code = http_build_query($data); // url编码并生成query字符串
        $sign = sha1($code);  // 生成签名
        return $sign;
    }

    /**
     * 检测用户是否登录
     * @param int $recommender_id 推荐人id
     * @return int 0：未登录，-1：禁用，大于0：当前登录用户id
     */
    public function isLogin($recommender_id) {
        $user_auth = json_decode(auth_code(cookie('user_auth')), true);
        if (empty($user_auth)) {
            return 0;
        } else {
            if (auth_code(cookie('user_auth_sign')) == $this->dataAuthSign($user_auth)) {
                $userinfo = $this->getUserInfoById($user_auth['id']);
                if (!$userinfo) {
                    cookie('user_auth', null);
                    cookie('user_auth_sign', null);
                    return 0;
                }
                if (!$userinfo['status']) {
                    return -1;
                }
                $recommender_userinfo = $this->getUserInfoById($recommender_id);
                if (!$recommender_userinfo || $recommender_userinfo['level_id'] == 0) {
                    $recommender_id = 0;
                }
                if ($userinfo['recommender_id'] == 0 && $userinfo['level_id'] == 0 && $recommender_id != 0 && $userinfo['id'] != $recommender_id) {
                    $reward_rule = get_system('reward_rule');
                    $invite_integral = $reward_rule['invite_integral'];
                    if ($reward_rule['invite_open'] && $invite_integral > 0) {
                        $user_account_model = M('user_account');
                        $account_info = $user_account_model->where("user_id={$recommender_id}")->find();
                        $balance = $account_info['integral'] + $invite_integral;
                        //添加积分记录
                        $data = [
                            'user_id' => $recommender_id,
                            'type' => 3,
                            'integral' => $invite_integral,
                            'balance' => $balance,
                            'title' => '邀请好友成功',
                            'description' => '邀请好友成功',
                            'create_time' => date('Y-m-d H:i:s')
                        ];
                        M('user_integral')->add($data);
                        //更新用户积分
                        $data = [
                            'integral' => $balance,
                            'total_integral' => $account_info['total_integral'] + $invite_integral
                        ];
                        $user_account_model->where("user_id={$recommender_id}")->save($data);
                    }
                    $this->where("id={$userinfo['id']}")->setField('recommender_id', $recommender_id);
                }
                return $userinfo['id'];
            } else {
                return 0;
            }
        }
    }

    /**
     * 通过openid获取用户信息
     * @param string $openid
     * @return array
     */
    public function getUserInfoByOpenid($openid) {
        return $this->where("openid='{$openid}'")->find();
    }

    /**
     * 通过id获取用户信息
     * @param int $id 用户id
     * @return array
     */
    public function getUserInfoById($id) {
        return $this->where("id={$id}")->find();
    }

    /**
     * 通过id获取用户所有信息
     * @param int $id 用户id
     * @return array
     */
    public function getAllUserInfoById($id) {
        return $this->alias('u')
                        ->join(C('DB_PREFIX') . 'user_account ua on ua.user_id=u.id', 'LEFT')
                        ->join(C('DB_PREFIX') . 'user_level ul on ul.id=u.level_id', 'LEFT')
                        ->field('u.*,ua.integral,ua.total_integral,ua.money,ua.total_money,ua.total_consume,ul.name level_name,ul.icon level_icon')
                        ->where("u.id={$id}")
                        ->find();
    }

    /**
     * 获取团队人员id
     * @param int $id 用户id
     * @param int $level_id 用户等级
     * @param boolean $has_direct 是否包含直接团队
     * @param boolean $has_group 是否分组
     * @return array
     */
    public function getTeamUserIds($id, $level_id, $has_direct, $has_group) {
        if ($level_id == 0) {
            return [];
        }
        $get_commission_number = M('user_level')->where("id={$level_id}")->getField('get_commission_number');
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

    /**
     * 获取下家列表
     * @param int $id 用户id
     * @param int $level_id 用户等级
     * @param int $type 1：直属团队，2：间接团队
     * @param int $page 当前页码
     * @param int $page_size 每页条数
     * @return array
     */
    public function getTeamPageList($id, $level_id, $type, $page, $page_size) {
        $sql = "select u.nickname,u.avatar,u.join_time,ua.total_money,ua.total_sales 
                from __PREFIX__user u 
                left join __PREFIX__user_account ua on ua.user_id=u.id 
                where u.level_id>0";
        switch ($type) {
            case 1:
                $sql .= " and u.recommender_id={$id}";
                break;
            case 2:
                $team_user_ids = $this->getTeamUserIds($id, $level_id, false, false);
                $team_user_ids_str = $team_user_ids ? implode(',', $team_user_ids) : 0;
                $sql .= " and u.id in ({$team_user_ids_str})";
                break;
        }
        $sql .= " order by u.join_time desc";
        return $this->pageQuery($sql, $page, $page_size);
    }

}
