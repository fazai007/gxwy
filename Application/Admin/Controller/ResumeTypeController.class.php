<?php

namespace Admin\Controller;

use Common\Model\ModuleModel;
use Common\Model\ResumeTypeModel;
use Common\Model\ResumeTypeModuleModel;

class ResumeTypeController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 简历模板列表
     * @author 牛青旺
     */
    public function ResumeTypeList()
    {
        $resumeTypeObj = new ResumeTypeModel();
        
        //构建分页信息
        $page_size = C('PAGE_SIZE');
        $count = $resumeTypeObj->getResumeTypeNum();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        
        //获取列表数据
        $resumeTypeList = $resumeTypeObj->getResumeTypeList('resume_type_id, type_name, describe', '');
        #dump($resumeTypeList);die;

        $this->assign('resumeTypeList', $resumeTypeList);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 新增简历模板分类
     * @author 牛青旺
     */
    public function addResumeType()
    {
        if (IS_POST) {
            $resumeTypeObj = new ResumeTypeModel();
            $resumeTypeModuleObj = new ResumeTypeModuleModel();
            $data = $resumeTypeObj->create();
            $moduleId = I('moduleId');
            if ($data) {
                $result = $resumeTypeObj->addResumeType($data);
                if ($result) {
                    $resumeTypeModuleObj->addBatchResumeTypeModule($result, $moduleId);
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($resumeTypeObj->getError());
            }
        }

        //获取所有模块
        $moduleObj = new ModuleModel();
        $moduleList = $moduleObj->getModuleList();
        #$moduleList = $moduleObj->getModuleFieldList($moduleList);
        #dump($moduleList[0]);die;
        
        $this->assign('moduleList', $moduleList);
        $this->display();
    }

    /**
     * 编辑简分类
     * @param $id
     * @author 牛青旺
     */
    public function editResumeType($id = 0) {
        $resumeTypeObj = new ResumeTypeModel();
        $resumeTypeModuleObj = new ResumeTypeModuleModel();
        if (IS_POST) {
            $data = $resumeTypeObj->create();
            $moduleId = I('moduleId');
            if ($data) {
                $result = $resumeTypeObj->save($data);
                if ($result !== false) {

                    //更新模板模块
                    $resumeTypeModuleObj->delWhereResumeTypeModule('resume_type_id = ' . $data['resume_type_id'], true);
                    $resumeTypeModuleObj->addBatchResumeTypeModule($data['resume_type_id'], $moduleId);
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($resumeTypeObj->getError());
            }
        }
        $resumeTypeInfo = $resumeTypeObj->getResumeTypeInfo('resume_type_id = ' . $id);
        $resumeTypeModuleList = $resumeTypeModuleObj->getResumeTypeModuleList('resume_type_module_id, module_id', 'resume_type_id = ' . $id);
        #dump($resumeTypeModuleList);die;

        //获取所有模块
        $moduleObj = new ModuleModel();
        $moduleList = $moduleObj->getModuleList();

        $this->assign('moduleList', $moduleList);
        $this->assign('resumeTypeModuleList', $resumeTypeModuleList);
        $this->assign('resumeTypeInfo', $resumeTypeInfo);
        $this->display();
    }

    /**
     * 删除简历模板
     * @param array $ids
     * @author 牛青旺
     */
    public function deleteResumeType($ids = []) {
        $resumeTypeObj = new ResumeTypeModel();
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }


        $result = $resumeTypeObj->delResumeType(intval($ids), true);
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


}
