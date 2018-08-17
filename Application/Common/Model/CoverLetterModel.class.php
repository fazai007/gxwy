<?php
/**
 * 模型模型类
 * table_name = xilu_cover_letter
 * py_key = cover_letter_id
 */

 namespace Common\Model;

 use Think\Model;

class CoverLetterModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化模型id
     */
    public function CoverLetterModel()
    {
        parent::__construct('cover_letter');
    }

    /**
     * 获取模型信息
     * @author 姜伟
     * @param int $cover_letter_id 模型id
     * @param string $fields 要获取的字段名
     * @return array 模型基本信息
     * @todo 根据where查询条件查找模型表中的相关数据并返回
     */
    public function getCoverLetterInfo($where, $fields = '')
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
    public function editCoverLetter($where='',$arr)
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
    public function addCoverLetter($arr)
    {
        if (!is_array($arr)) return false;

		$arr['add_time'] = time();

        return $this->add($arr);
    }

    /**
     * 删除模型
     * @author 姜伟
     * @param int $cover_letter_id 模型ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delCoverLetter($cover_letter_id,$opt = false)
    {
        if (!is_numeric($cover_letter_id)) return false;
        if($opt)
        {
            return $this->where('cover_letter_id = ' . $cover_letter_id)->delete();
        }else{
           return $this->where('cover_letter_id = ' . $cover_letter_id)->save(array('is_del' => 1));
        }
        
    }

    /**
     * 根据where子句获取模型数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的模型数量
     * @todo 根据where子句获取模型数量
     */
    public function getCoverLetterNum($where = '')
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
    public function getCoverLetterList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getCoverLetterField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取模型列表页数据信息列表
     * @author 姜伟
     * @param array $CoverLetter_list
     * @return array $CoverLetter_list
     * @todo 根据传入的$CoverLetter_list获取更详细的模型列表页数据信息列表
     */
    public function getListData($CoverLetter_list)
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
    public function getAllCoverLetter($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

}
