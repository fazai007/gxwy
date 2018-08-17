<?php

namespace Admin\Controller;

use Common\Model\ModuleModel;
use Common\Model\CoverLetterTypeModel;

class CoverLetterTypeController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 简历模板列表
     * @author 牛青旺
     */
    public function CoverLetterTypeList()
    {
        $coverLetterTypeObj = new CoverLetterTypeModel();
        
        //构建分页信息
        $page_size = C('PAGE_SIZE');
        $count = $coverLetterTypeObj->getCoverLetterTypeNum();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        
        //获取列表数据
        $where = 'is_del = 0';
        $coverLetterTypeList = $coverLetterTypeObj->getCoverLetterTypeList('cover_letter_type_id, type_name, language, content', $where);
        #dump($coverLetterTypeList);die;

        $this->assign('coverLetterTypeList', $coverLetterTypeList);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 新增简历模板分类
     * @author 牛青旺
     */
    public function addCoverLetterType()
    {
        if (IS_POST) {
            $coverLetterTypeObj = new CoverLetterTypeModel();
            $data = $coverLetterTypeObj->create();
            if ($data) {
                $result = $coverLetterTypeObj->addCoverLetterType($data);
                if ($result) {
                    $this->success('保存成功', U('Admin/CoverLetterType/CoverLetterTypeList'));
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($coverLetterTypeObj->getError());
            }
        }

        $this->display();
    }

    /**
     * 编辑简模板
     * @param $id
     * @author 牛青旺
     */
    public function editCoverLetterType($id = 0) {
        $coverLetterTypeObj = new CoverLetterTypeModel();
        if (IS_POST) {
            $data = $coverLetterTypeObj->create();
            if ($data) {
                $result = $coverLetterTypeObj->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($coverLetterTypeObj->getError());
            }
        }
        $coverLetterTypeInfo = $coverLetterTypeObj->getCoverLetterTypeInfo('cover_letter_type_id = ' . $id);
        #dump($coverLetterTypeInfo);die;

        $this->assign('coverLetterTypeInfo', $coverLetterTypeInfo);
        $this->display();
    }

    /**
     * 删除简历模板
     * @param array $id
     * @author 牛青旺
     */
    public function deleteCoverLetterType($id = []) {
        $coverLetterTypeObj = new CoverLetterTypeModel();
        if (!$id) {
            $this->error('请选择需要删除的数据');
        }


        $result = $coverLetterTypeObj->delCoverLetterType(intval($id));
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


}
