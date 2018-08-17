<?php
/**
 * 模型模型类
 * table_name = xilu_resume_type_module
 * py_key = resume_type_module_id
 */

 namespace Common\Model;

 use Think\Model;

class ResumeTypeModuleModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化模型id
     */
    public function ResumeTypeModuleModel()
    {
        parent::__construct('resume_type_module');
    }

    /**
     * 获取模型信息
     * @author 姜伟
     * @param int $resume_type_module_id 模型id
     * @param string $fields 要获取的字段名
     * @return array 模型基本信息
     * @todo 根据where查询条件查找模型表中的相关数据并返回
     */
    public function getResumeTypeModuleInfo($where, $fields = '')
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
    public function editResumeTypeModule($where='',$arr)
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
    public function addResumeTypeModule($arr)
    {
        if (!is_array($arr)) return false;

		$arr['add_time'] = time();

        return $this->add($arr);
    }

    /**
     * 批量新增简历模板模块
     * @param $resumeTypeId
     * @param $moduleIdArr
     * @return bool
     * @author 牛青旺
     */
    public function addBatchResumeTypeModule($resumeTypeId, $moduleIdArr)
    {
        if(!$resumeTypeId || empty($moduleIdArr)) {
            return false;
        }

        if(is_array($moduleIdArr)) {
            foreach ($moduleIdArr as $moduleId) {
                if(!$moduleId) {
                    continue;
                }
                $data = [
                    'resume_type_id' => $resumeTypeId,
                    'module_id' => $moduleId,
                    'is_default' => 0
                ];
                $this->addResumeTypeModule($data);
            }
        }

    }

    /**
     * 删除模型
     * @author 姜伟
     * @param int $resume_type_module_id 模型ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delResumeTypeModule($resume_type_module_id, $opt = false)
    {
        if (!is_numeric($resume_type_module_id)) return false;
        if($opt)
        {
            return $this->where('resume_type_module_id = ' . $resume_type_module_id)->delete();
        }else{
           return $this->where('resume_type_module_id = ' . $resume_type_module_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 条件删除数据
     * @param $where
     * @param bool $opt
     * @return bool|mixed
     * @author 牛青旺
     */
    public function delWhereResumeTypeModule($where, $opt = false)
    {
        if($opt)
        {
            return $this->where($where)->delete();
        }else{
            return $this->where($where)->save(array('is_del' => 1));
        }
    }

    /**
     * 根据where子句获取模型数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的模型数量
     * @todo 根据where子句获取模型数量
     */
    public function getResumeTypeModuleNum($where = '')
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
    public function getResumeTypeModuleList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getResumeTypeModuleField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取模型列表页数据信息列表
     * @author 姜伟
     * @param array $ResumeTypeModule_list
     * @return array $ResumeTypeModule_list
     * @todo 根据传入的$ResumeTypeModule_list获取更详细的模型列表页数据信息列表
     */
    public function getListData($ResumeTypeModule_list)
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
    public function getAllResumeTypeModule($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

}
