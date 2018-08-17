<?php
/**
 * 模型模型类
 * table_name = xilu_module
 * py_key = module_id
 */

 namespace Common\Model;

 use Think\Model;

class ModuleModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化模型id
     */
    public function ModuleModel()
    {
        parent::__construct('module');
    }

    /**
     * 获取模型信息
     * @author 姜伟
     * @param int $module_id 模型id
     * @param string $fields 要获取的字段名
     * @return array 模型基本信息
     * @todo 根据where查询条件查找模型表中的相关数据并返回
     */
    public function getModuleInfo($where, $fields = '')
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
    public function editModule($where='',$arr)
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
    public function addModule($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除模型
     * @author 姜伟
     * @param int $module_id 模型ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delModule($module_id,$opt = false)
    {
        if (!is_numeric($module_id)) return false;
        if($opt)
        {
            return $this->where('module_id = ' . $module_id)->delete();
        }else{
           return $this->where('module_id = ' . $module_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取模型数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的模型数量
     * @todo 根据where子句获取模型数量
     */
    public function getModuleNum($where = '')
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
    public function getModuleList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getModuleField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取模型列表页数据信息列表
     * @author 姜伟
     * @param array $Module_list
     * @return array $Module_list
     * @todo 根据传入的$Module_list获取更详细的模型列表页数据信息列表
     */
    public function getListData($Module_list)
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
    public function getAllModule($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

    /**
     * 获取模块字段
     * @param $moduleList
     * @return mixed
     * @author 牛青旺
     */
    public function getModuleFieldList($moduleList)
    {
        $moduleFieldObj = new ModuleFieldModel();
        foreach($moduleList as $key => $module) {
            $moduleFieldList = $moduleFieldObj->getModuleFieldList('module_field_id, name', 'is_del = 0 AND module_id = ' . $module['module_id']);
            $moduleList[$key]['module_files'] = $moduleFieldList;
        }
        return $moduleList;
    }

}
