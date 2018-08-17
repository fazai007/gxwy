<?php

namespace Admin\Controller;

use Common\Model\ModuleModel;
use Common\Model\EmailTypeModel;

class EmailTypeController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 简历模板列表
     * @author 牛青旺
     */
    public function EmailTypeList()
    {
        $emailTypeObj = new EmailTypeModel();
        
        //构建分页信息
        $page_size = C('PAGE_SIZE');
        $count = $emailTypeObj->getEmailTypeNum();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        
        //获取列表数据
        $where = 'is_del = 0';
        $emailTypeList = $emailTypeObj->getEmailTypeList('email_type_id, type_name, language, content', $where);
        #dump($emailTypeList);die;

        $this->assign('emailTypeList', $emailTypeList);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 新增简历模板分类
     * @author 牛青旺
     */
    public function addEmailType()
    {
        if (IS_POST) {
            $emailTypeObj = new EmailTypeModel();
            $data = $emailTypeObj->create();
            if ($data) {
                $result = $emailTypeObj->addEmailType($data);
                if ($result) {
                    $this->success('保存成功', U('Admin/EmailType/EmailTypeList'));
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($emailTypeObj->getError());
            }
        }

        $this->display();
    }

    /**
     * 编辑简模板
     * @param $id
     * @author 牛青旺
     */
    public function editEmailType($id = 0) {
        $emailTypeObj = new EmailTypeModel();
        if (IS_POST) {
            $data = $emailTypeObj->create();
            if ($data) {
                $result = $emailTypeObj->save($data);
                if ($result !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($emailTypeObj->getError());
            }
        }
        $emailTypeInfo = $emailTypeObj->getEmailTypeInfo('email_type_id = ' . $id);
        #dump($emailTypeInfo);die;

        $this->assign('emailTypeInfo', $emailTypeInfo);
        $this->display();
    }

    /**
     * 删除简历模板
     * @param array $id
     * @author 牛青旺
     */
    public function deleteEmailType($id = []) {
        $emailTypeObj = new EmailTypeModel();
        if (!$id) {
            $this->error('请选择需要删除的数据');
        }


        $result = $emailTypeObj->delEmailType(intval($id));
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


}
