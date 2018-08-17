<?php

namespace Wx\Controller;

class AddressController extends BaseController {

    protected $user_address_model;

    public function __construct() {
        parent::__construct();
        $this->user_address_model = D('Wx/UserAddress');
    }

    /**
     * 地址管理
     */
    public function index($back_value = 0) {
        $back_url = $back_value ? U('Wx/Order/confirm') : U('Wx/Address/index');
        Cookie('__address_back__', $back_url);
        $seo = set_seo('地址管理');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Address/index');
    }

    /**
     * 新增地址
     */
    public function add() {
        $seo = set_seo('新增地址');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Address/add');
    }

    /**
     * 编辑地址
     */
    public function edit() {
        $id = I('id', 0, 'intval');
        $user_address = $this->user_address_model->getDetail($id, $this->userinfo['id']);
        if (!$user_address) {
            $this->alert('收货地址不存在');
        }
        $this->assign('user_address', $user_address);
        $seo = set_seo('编辑地址');
        $this->assign('seo', $seo);
        $wx_share = set_wx_share($this->userinfo['id']);
        $this->assign('wx_share', $wx_share);
        $this->display('User/Address/edit');
    }

    /**
     * 保存地址
     */
    public function save() {
        if (IS_POST) {
            $data = $this->user_address_model->create();
            if ($data) {
                $is_default = isset($data['is_default']) ? 1 : 0;
                //编辑
                if (isset($data['id'])) {
                    $id = $data['id'];
                    $user_address = $this->user_address_model->getDetail($data['id'], $this->userinfo['id']);
                    if (!$user_address) {
                        $this->error('收货地址信息不存在');
                    }
                    $this->user_address_model->startTrans();
                    try {
                        $this->user_address_model->save($data);
                        if ($is_default) {
                            $this->user_address_model->where("id<>{$id} and user_id={$this->userinfo['id']}")
                                    ->setField('is_default', 0);
                        }
                        $this->user_address_model->commit();
                        $back_url = Cookie('__address_back__');
                        $this->success('保存成功', $back_url);
                    } catch (Exception $ex) {
                        $this->user_address_model->rollback();
                        $this->error('保存失败');
                    }
                }
                //新增
                else {
                    $data['user_id'] = $this->userinfo['id'];
                    $this->user_address_model->startTrans();
                    try {
                        $id = $this->user_address_model->add($data);
                        if ($is_default) {
                            $this->user_address_model->where("id<>{$id} and user_id={$this->userinfo['id']}")
                                    ->setField('is_default', 0);
                        }
                        $this->user_address_model->commit();
                        $this->success('保存成功', Cookie('__address_back__'));
                    } catch (Exception $ex) {
                        $this->user_address_model->rollback();
                        $this->error('保存失败');
                    }
                }
            } else {
                $this->error($this->user_address_model->getError());
            }
        }
    }

    /**
     * 异步获取收货地址列表
     */
    public function getListData() {
        if (IS_GET) {
            $page = I('get.page', 1, 'intval');
            $data_list = $this->user_address_model->getPageList($this->userinfo['id'], $page, 10);
            foreach ($data_list['root'] as &$v) {
                $v['href'] = U('Wx/Address/edit', ['id' => $v['id']]);
            }
            unset($v);
            $this->success($data_list);
        }
    }

    /**
     * 设为默认地址
     */
    public function setDefault($id = 0) {
        if (IS_POST) {
            $this->user_address_model->startTrans();
            try {
                $this->user_address_model
                        ->where("id={$id} and user_id={$this->userinfo['id']}")
                        ->setField('is_default', 1);
                $this->user_address_model
                        ->where("id<>{$id} and user_id={$this->userinfo['id']}")
                        ->setField('is_default', 0);
                $this->user_address_model->commit();
                $back_url = Cookie('__address_back__');
                $this->success('设置成功', $back_url);
            } catch (Exception $ex) {
                $this->user_address_model->rollback();
                $this->error('设置失败');
            }
        }
    }

    /**
     * 删除
     */
    public function delete($id = 0) {
        if (IS_POST) {
            $result = $this->user_address_model
                    ->where("id={$id} and user_id={$this->userinfo['id']}")
                    ->delete();
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

}
