<?php

namespace Admin\Controller;

class PromotionController extends BaseController {

    protected $system_model;
    
    public function __construct() {
        parent::__construct();
        $this->system_model = M('system');
    }
    
    /**
     * 奖励规则
     */
    public function rewardRule() {
        if(IS_POST){
            $reward_rule = I('post.');
            $data = [
                'value' => serialize($reward_rule)
            ];
            $result = $this->system_model->where("name='reward_rule'")->save($data);
            if ($result !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $reward_rule = $this->system_model->field('value')->where("name='reward_rule'")->find();
        $reward_rule = unserialize($reward_rule['value']);
        $this->assign('reward_rule', $reward_rule);
        $this->display();
    }

}
