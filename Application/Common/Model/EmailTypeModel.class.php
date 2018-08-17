<?php
/**
 * 模型模型类
 * table_name = xilu_email_type
 * py_key = email_type_id
 */

 namespace Common\Model;

 use Think\Model;

class EmailTypeModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化模型id
     */
    public function EmailTypeModel()
    {
        parent::__construct('email_type');
    }

    /**
     * 获取模型信息
     * @author 姜伟
     * @param int $email_type_id 模型id
     * @param string $fields 要获取的字段名
     * @return array 模型基本信息
     * @todo 根据where查询条件查找模型表中的相关数据并返回
     */
    public function getEmailTypeInfo($where, $fields = '')
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
    public function editEmailType($where='',$arr)
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
    public function addEmailType($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除模型
     * @author 姜伟
     * @param int $email_type_id 模型ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo is_del设为1 || 真删除
     */
    public function delEmailType($email_type_id,$opt = false)
    {
        if (!is_numeric($email_type_id)) return false;
        if($opt)
        {
            return $this->where('email_type_id = ' . $email_type_id)->delete();
        }else{
           return $this->where('email_type_id = ' . $email_type_id)->save(array('is_del' => 1));
        }
        
    }

    /**
     * 根据where子句获取模型数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的模型数量
     * @todo 根据where子句获取模型数量
     */
    public function getEmailTypeNum($where = '')
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
    public function getEmailTypeList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getEmailTypeField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取模型列表页数据信息列表
     * @author 姜伟
     * @param array $EmailType_list
     * @return array $EmailType_list
     * @todo 根据传入的$EmailType_list获取更详细的模型列表页数据信息列表
     */
    public function getListData($EmailType_list)
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
    public function getAllEmailType($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

}
