<?php
/**
 * 模型模型类
 * table_name = xilu_feedback
 * py_key = feedback_id
 */

 namespace Common\Model;

 use Think\Model;

class FeedbackModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化模型id
     */
    public function FeedbackModel()
    {
        parent::__construct('feedback');
    }

    /**
     * 获取模型信息
     * @author 姜伟
     * @param int $feedback_id 模型id
     * @param string $fields 要获取的字段名
     * @return array 模型基本信息
     * @todo 根据where查询条件查找模型表中的相关数据并返回
     */
    public function getFeedbackInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改模型信息
     * @author 姜伟
     * @param array $arr 模型信息数组
     * @return boolean 操作结果
     * @todo 修改模型信息
     */
    public function editFeedback($where='',$arr)
    {
        if (!is_array($arr)) return false;

        //$arr['last_edit_time'] = time();
        //$arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加模型
     * @author 姜伟
     * @param array $arr 模型信息数组
     * @return boolean 操作结果
     * @todo 添加模型
     */
    public function addFeedback($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除模型
     * @author 姜伟
     * @param int $feedback_id 模型ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delFeedback($feedback_id,$opt = false)
    {
        if (!is_numeric($feedback_id)) return false;
        if($opt)
        {
            return $this->where('feedback_id = ' . $feedback_id)->delete();
        }else{
           return $this->where('feedback_id = ' . $feedback_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取模型数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的模型数量
     * @todo 根据where子句获取模型数量
     */
    public function getFeedbackNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询模型信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 模型基本信息
     * @todo 根据SQL查询字句查询模型信息
     */
    public function getFeedbackList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getFeedbackField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取模型列表页数据信息列表
     * @author 姜伟
     * @param array $Feedback_list
     * @return array $Feedback_list
     * @todo 根据传入的$Feedback_list获取更详细的模型列表页数据信息列表
     */
    public function getListData($Feedback_list)
    {
        
    }

    /**
     * 根据where子句查询模型信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 模型基本信息
     * @todo 根据SQL查询字句查询模型信息
     */
    public function getAllFeedback($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

}
