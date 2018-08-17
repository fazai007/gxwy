<?php

namespace Admin\Controller;

use Common\Model\ModuleFieldModel;
use Common\Model\ModuleModel;

class ModuleController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 模块列表
     * @author 牛青旺
     */
    public function ModuleList()
    {
        $moduleObj = new ModuleModel();
        
        //构建分页信息
        $page_size = C('PAGE_SIZE');
        $count = $moduleObj->getModuleNum();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        
        //获取列表数据
        $moduleList = $moduleObj->getModuleList('', '');
        $moduleList = $moduleObj->getModuleFieldList($moduleList);
        #dump($moduleList[0]['module_files']);die;

        $this->assign('moduleList', $moduleList);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 新增模块分类
     * @author 牛青旺
     */
    public function addModule()
    {
        if (IS_POST) {
            $moduleObj = new ModuleModel();
            $data = $moduleObj->create();
            if ($data) {
                $result = $moduleObj->addModule($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($moduleObj->getError());
            }
        }
        $this->display();
    }

    /**
     * 编辑简分类
     * @param $id
     * @author 牛青旺
     */
    public function editModule($id = 0) {
        $moduleObj = new ModuleModel();
        if (IS_POST) {
            $data = $moduleObj->create();
            if ($data) {
                $result = $moduleObj->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($moduleObj->getError());
            }
        }
        $moduleInfo = $moduleObj->getModuleInfo('module_id = ' . $id);
        
        $this->assign('moduleInfo', $moduleInfo);
        $this->display();
    }

    /**
     * 删除模块
     * @param array $ids
     * @author 牛青旺
     */
    public function deleteModule($ids = []) {
        $moduleObj = new ModuleModel();
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }

        //真删除
        $result = $moduleObj->delModule(intval($ids), true);
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 新增模块字段分类
     * @author 牛青旺
     */
    public function addModuleField()
    {
        $moduleId = I('id');
        $moduleFieldObj = new ModuleFieldModel();
        if (IS_POST) {
            $data = $moduleFieldObj->create();
            if ($data) {
                $result = $moduleFieldObj->addModuleField($data);
                if ($result) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($moduleFieldObj->getError());
            }
        }
        
        $fieldType = $moduleFieldObj->getFieldType();
        
        $this->assign('fieldType', $fieldType);
        $this->assign('moduleId', $moduleId);
        $this->display();
    }

    /**
     * 编辑模块字段
     * @author 牛青旺
     */
    public function editModuleField()
    {
        $moduleFieldId = I('id');
        $moduleFieldObj = new ModuleFieldModel();
        if (IS_POST) {
            $data = $moduleFieldObj->create();
            if ($data) {
                $result = $moduleFieldObj->save($data);
                if ($result !== false) {
                    $this->success('更新成功', U('Admin/Module/moduleList'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($moduleFieldObj->getError());
            }
        }
        $moduleFieldInfo = $moduleFieldObj->getModuleFieldInfo('module_field_id = ' . $moduleFieldId);
        $fieldType = $moduleFieldObj->getFieldType();
        #echo $moduleFieldObj->getLastSql();
        #dump($moduleFieldInfo);die;

        $this->assign('fieldType', $fieldType);
        $this->assign('moduleFieldInfo', $moduleFieldInfo);
        $this->display();
    }

    /**
     * 删除模块字段
     * @author 牛青旺
     */
    public function deleteModuleField() {
        $id = I('id');
        $moduleFieldObj = new ModuleFieldModel();
        if (!$id) {
            $this->error('请选择需要删除的数据');
        }

        //真删除
        $result = $moduleFieldObj->delModuleField(intval($id));
        if ($result) {
            $this->success('删除成功', U('Admin/Module/moduleList'));
        } else {
            $this->error('删除失败');
        }
    }
    

}
