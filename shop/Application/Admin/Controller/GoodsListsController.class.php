<?php


namespace Admin\Controller;


use Common\Controller\AdminController;
use Common\Model\GoodsAttrModel;
use Common\Model\GoodsListModel;
use Common\Model\TypeAttrModel;

class GoodsListsController extends AdminController {
    public function goodsLists() {
        if (IS_POST) {
            //调用模型中的handler方法
            $res = (new GoodsListModel())->handler();
            if ($res['status'] == 'success') {
                $this->success($res['message'], u('goodsLists', ['gid' => i('get.gid')]));
                die;
            } else {
                $this->error($res['message']);
                die;
            }
        }
        //1.获取列表中表单的数据
        //获取商品规格数据，数据来源需要类型属性表和商品属性表，然后进行数据重组
        $gid = $_GET['gid'];
        $data = (new GoodsAttrModel())->alias('ga')->join('shop_type_attr ta on ga.taid=ta.taid')->where("ga.gid={$gid} and ta.tatype=1")->select();
        $temp = '';
        foreach ($data as $k => $v) {
            $temp[$v['taname']][] = $v;
        }
        //获取规格数组并分配到页面
        $this->assign('spec', array_keys($temp));
        //将所有规格数据分配到页面
        $this->assign('specData', $temp);

        //2.显示货品列表，获取所有货品数据
        $goodsLists = (new GoodsListModel())->where("gid={$gid}")->select();
        //处理数据方便数据在页面显示
        foreach ($goodsLists as $k => $v) {
            $goodsLists[$k]['combine'] = explode(',', $v['combine']);
            foreach ($goodsLists[$k]['combine'] as $kk => $vv) {
                $goodsLists[$k]['combine'][$kk] = (new GoodsAttrModel())->where("gaid = $vv")->getField('gavalue');
            }
        }
        $this->assign('goodsLists', $goodsLists);
        $this->display();
    }

    public function del() {
        //获取要删除的glid和删除完成返回列表所需要的gid
        $gid = i('get.gid');
        $glid = i('get.glid');
        //删除所有数据
        (new GoodsListModel())->delete($glid);
        $this->success('操作成功！', u('goodsLists', ['gid' => $gid]));
    }

    //ajax 判断所选下拉规格组合是否已经存在
    public function ajaxCheckSpec() {
        if (IS_AJAX) {
            $gid = i('get.gid');
            $combine = trim(implode(',', $_POST));
            $res = (new GoodsListModel())->where("gid={$gid} and combine='{$combine}'")->find();
            if ($res) {
                echo 0;
            } else {
                echo 1;
            }
        }
    }

    //ajax 异步修改货品数据
    public function ajaxEditForm() {
        if (IS_AJAX) {
            $data = $_POST;
            (new GoodsListModel())->save($data);
            echo json_encode($_POST);
        }
    }
}