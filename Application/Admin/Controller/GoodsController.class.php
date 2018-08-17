<?php

namespace Admin\Controller;

class GoodsController extends BaseController {

    protected $goods_model;
    protected $goods_cate_model;
    protected $goods_tag_model;
    protected $goods_picture_model;
    protected $goods_type_list;

    public function __construct() {
        parent::__construct();
        $this->goods_model = D('Admin/Goods');
        $this->goods_cate_model = D('Admin/GoodsCate');
        $this->goods_tag_model = D('Admin/GoodsTag');
        $this->goods_picture_model = M('goods_picture');
        $goods_cate_list = $this->goods_cate_model->order('sort asc,id asc')->select();
        $goods_cate_level_list = array2level($goods_cate_list);
        $this->assign('goods_cate_level_list', $goods_cate_level_list);
        $this->goods_type_list = [
            'video' => '视频',
            'voice' => '音频',
            'article' => '图文'
        ];
        $this->assign('goods_type_list', $this->goods_type_list);
    }

    /**
     * 商品列表
     */
    public function index($field_name = 'name', $keyword = '', $cate_id = 0, $status = 1) {
        $map = [];
        $this->assign('field_name', $field_name);
        if ($keyword) {
            $map[$field_name] = ['like', "%{$keyword}%"];
        }
        $this->assign('keyword', $keyword);
        if ($cate_id > 0) {
            //获取所有子分类
            $cate_ids = $this->goods_cate_model->getChild([$cate_id], [$cate_id]);
            $map['cate_id'] = ['in', $cate_ids];
        }
        $this->assign('cate_id', $cate_id);
        if ($status > -1) {
            switch ($status) {
                case 1:
                    $map['is_sale'] = ['eq', 1];
                    $map['is_delete'] = ['eq', 0];
                    break;
                case 2:
                    $map['is_sale'] = ['eq', 0];
                    $map['is_delete'] = ['eq', 0];
                    break;
                case 3:
                    $map['is_sale'] = ['eq', 1];
                    $map['is_delete'] = ['eq', 0];
                    $map['stock_warn'] = ['gt', 0];
                    $map['stock_warn'] = ['gt', 'stock'];
                    break;
                case 4:
                    $map['is_delete'] = ['eq', 1];
                    break;
            }
        }
        $this->assign('status', $status);
        $page_size = C('PAGE_SIZE');
        $count = $this->goods_model->where($map)->count();
        $pager = new \Think\Page($count, $page_size);
        $show = $pager->show();
        $goods_list = $this->goods_model
                ->field('id,type,name,code,stock,stock_warn,is_sale,sale_price,member_sale_price,thumb,create_time,sales_count,praise_count,sort')
                ->where($map)
                ->order('sort asc,id asc')
                ->limit($pager->first_row . ',' . $pager->list_rows)
                ->select();
        $this->assign('goods_list', $goods_list);
        $this->assign('page', $show);
        $nav_list = [
            1 => '出售中',
            2 => '已下架',
            3 => '库存预警',
            4 => '回收站',
        ];
        $this->assign('nav_list', $nav_list);
        if ($status == 4) {
            $this->display('Goods/recycle');
        } else {
            $this->display();
        }
    }

    /**
     * 发布商品
     */
    public function add() {
        if (IS_POST) {
            $photo = I('post.photo');
            $tag = I('post.tag');
            $data = $this->goods_model->create();
            if ($data) {
                $this->goods_model->startTrans();
                try {
                    $data['tag'] = $tag ? implode(',', $tag) : '';
                    $goods_id = $this->goods_model->add($data);
                    if ($photo) {
                        foreach ($photo as $v) {
                            $data = [
                                'goods_id' => $goods_id,
                                'picture' => $v
                            ];
                            $this->goods_picture_model->add($data);
                        }
                    }
                    $this->goods_model->commit();
                    $this->success('保存成功');
                } catch (Exception $ex) {
                    $this->goods_model->rollback();
                    $this->error('保存失败');
                }
            } else {
                $this->error($this->goods_model->getError());
            }
        }
        $goods_tag_list = $this->goods_tag_model
                ->field('id,name')
                ->order('sort asc,id asc')
                ->select();
        $this->assign('goods_tag_list', $goods_tag_list);
        $this->display();
    }

    /**
     * 编辑商品
     */
    public function edit($id) {
        $goods = $this->goods_model->find($id);
        if (IS_POST) {
            $photo = I('post.photo');
            $tag = I('post.tag');
            $data = $this->goods_model->create();
            if ($data) {
                $this->goods_model->startTrans();
                try {
                    $data['tag'] = $tag ? implode(',', $tag) : '';
                    $this->goods_model->save($data);
                    $this->goods_picture_model->where("goods_id={$id}")->delete();
                    if ($photo) {
                        foreach ($photo as $v) {
                            $data = [
                                'goods_id' => $id,
                                'picture' => $v
                            ];
                            $this->goods_picture_model->add($data);
                        }
                    }
                    $this->goods_model->commit();
                    $this->success('更新成功');
                } catch (Exception $ex) {
                    $this->goods_model->rollback();
                    $this->error('更新失败');
                }
            } else {
                $this->error($this->goods_model->getError());
            }
        }
        $goods['tag'] = $goods['tag'] ? explode(',', $goods['tag']) : [];
        $this->assign('goods', $goods);
        $goods_picture_list = $this->goods_picture_model->where("goods_id={$id}")->select();
        $this->assign('goods_picture_list', $goods_picture_list);
        $goods_tag_list = $this->goods_tag_model
                ->field('id,name')
                ->order('sort asc,id asc')
                ->select();
        $this->assign('goods_tag_list', $goods_tag_list);
        $this->display();
    }

    /**
     * 删除商品
     */
    public function delete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $result = $this->goods_model->where("id in ({$ids})")->setField('is_delete', 1);
        if ($result !== false) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 回收站删除
     */
    public function recycleDelete($ids = []) {
        if (!$ids) {
            $this->error('请选择需要删除的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $result = $this->goods_model->delete($ids);
        if ($result !== false) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 批量更新
     */
    public function batchUpdate($ids = [], $_action = '') {
        if (!$ids) {
            $this->error('请选择需要操作的数据');
        }
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        //上架/下架
        if ($_action == 'isSaleToggle') {
            $is_sale = I('get.is_sale', -1, 'intval');
            if (!in_array($is_sale, [0, 1])) {
                $this->error('参数错误');
            }
            $operation = '';
            if ($is_sale == 0) {
                $operation = '下架';
            } elseif ($is_sale == 1) {
                $operation = '上架';
            }
            $result = $this->goods_model->where("id in ({$ids})")->setField('is_sale', $is_sale);
            if ($result !== false) {
                $this->success('商品' . $operation . '成功');
            } else {
                $this->error('商品' . $operation . '失败');
            }
        }
        //推荐
        elseif ($_action == 'isRecToggle') {
            $is_rec = I('get.is_rec', -1, 'intval');
            if (!in_array($is_rec, [0, 1])) {
                $this->error('参数错误');
            }
            $result = $this->goods_model->where("id in ({$ids})")->setField('is_rec', $is_rec);
            if ($result !== false) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }
        //回收站恢复
        elseif ($_action == 'regainDelete') {
            $result = $this->goods_model->where("id in ({$ids})")->setField('is_delete', 0);
            if ($result !== false) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }
    }

}
