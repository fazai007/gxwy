<?php
namespace Home\Controller;

use Common\Model\CoverLetterTypeModel;
use Common\Model\EmailTypeModel;
use Common\Model\ResumeTypeModel;
use Think\Controller;

class ResumeController extends Controller {

    /**
     * 简历模板
     * @author 牛青旺
     */
    public function index(){
        $type = I('type');
        if(!$type) {
            $type = 'resume';
        }

        $fields = 'type_name, zh_img, en_img, describe';
        $where = 'is_del = 0';
        switch($type) {
            case 'resume':
                $resumeTypeObj = new ResumeTypeModel();
                $templateList = $resumeTypeObj->getResumeTypeList($fields, $where);
                break;

            case 'cover_letter':
                $coverLetterTypeObj = new CoverLetterTypeModel();
                $templateList = $coverLetterTypeObj->getCoverLetterTypeList($fields, $where);
                break;
            
            case 'email':
                $emailTypeObj = new EmailTypeModel();
                $templateList = $emailTypeObj->getEmailTypeList($fields, $where);
                break;
        }


        #echo $type;
        #dump($templateList);die;

        $this->assign('templateList', $templateList);
        $this->display();
    }
}