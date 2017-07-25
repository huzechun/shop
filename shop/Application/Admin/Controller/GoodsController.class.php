<?php


namespace Admin\Controller;


use Common\Controller\AdminController;
use Common\Model\GoodsAttrModel;
use Common\Model\GoodsModel;
use Common\Model\TypeAttrModel;
use Org\Util\Data;

class GoodsController extends AdminController {
    //添加商品
    public function addGoods() {
        if (IS_POST) {
            $res = (new GoodsModel())->addGoods();
            if ($res['status'] == 'error') {
                $this->error($res['message']); die;

            } else {
                $this->success('操作成功！',u('lists'));die;
            }
        }
        //获取所有分类和品牌
        $this->getCateAndBrand();
        $this->display();
    }
    //编辑商品
    public function editGoods(){
        if(IS_POST){
            $res = (new GoodsModel())->editGoods();
            if ($res['status'] == 'error') {
                $this->error($res['message']); die;
            } else {
                $this->success('操作成功！',u('lists'));die;
            }
        }
        //获取所有分类和品牌
        $this->getCateAndBrand();
        $gid = i('get.gid');
        //1.获取商品旧数据
        $oldData = m('goods')->alias('g')->join('shop_goods_detail d on g.gid=d.gid')->where("g.gid=$gid")->find();
        $oldData['big_pic'] = explode(',',$oldData['big_pic']);
        $this->assign('oldData',$oldData);
        //2.在类型属性中获取所有属性数据
        $attrData = (new TypeAttrModel())->where("tid = {$oldData['tid']} and tatype = 0")->select();
        foreach($attrData as $k => $v){
            $attrData[$k]['tavalue'] = explode('|',$v['tavalue']);
        }
        $this->assign('attrData',$attrData);
        //3.获取所有商品属性
        $hasAttr = (new GoodsAttrModel())->where("gid={$gid}")->getField('gaid,gavalue');
        $this->assign('hasAttr',$hasAttr);
        //4.获取规格属性
        $specData = (new GoodsAttrModel())->alias('ga')->join('shop_type_attr ta on ga.taid = ta.taid')->where("ga.gid = {$gid} and ta.tatype = 1")->select();
        //p($specData);die;
        foreach($specData as $k =>$v){
            $specData[$k]['tavalue'] = explode('|',$v['tavalue']);
        }
        $this->assign('specData',$specData);
        $this->display();
    }
    //获取所有分类和品牌函数
    public function getCateAndBrand(){
        //获取所有分类
        $cateData = m('category')->select();
        $cateData = (new Data())->tree($cateData, 'cname');
        //获取所有品牌
        $brandData = m('brand')->order('sort desc')->select();
        $this->assign('cateData', $cateData);
        $this->assign('brandData', $brandData);
    }
    public function lists() {
        $m = M('goods');
        $count = $m->count();
        $p = getpage($count, 10);
        $list = $m->field(true)->order('gid asc')->limit($p->firstRow, $p->listRows)->select();
        $this->assign('goods', $list); // 赋值数据集
        $this->assign('page', $p->show()); // 赋值分页输出
        $this->display();
    }

    //处理ajax
    public function ajaxGetAttrList() {
        //获取ajax传过来的tid
        if (IS_AJAX) {
            $tid = i('post.tid');
            $data = m('type_attr')->where("tid=$tid")->select();
            //echo json_encode($data);die;
            //采用thinkphp自带的ajax返回函数
            $this->ajaxReturn($data);
        } else {
            $this->error('非法请求!');
        }
    }

    public function ajaxUploadListPic() {
        $info = $this->upload('goodsListPic');
        $path = 'Uploads' . $info['savepath'] . $info['savename'];
        echo $path;
    }
    public function ajaxUploadDetailPic() {
        $info = $this->upload('goodsDetailPic');
        $path = 'Uploads' . $info['savepath'] . $info['savename'];
        echo $path;
    }

    public function ajaxDelPic() {
        if (IS_AJAX) {
            //获取图片路径
            $path = i('post.path');
            //判断该图片是否是大图
            $dirname = dirname($path);
            $filename = basename($path);
            if(strrchr($filename,'_')){
                $filename = ltrim(strrchr($filename,'_'),'_');
                $arr[] = $dirname.'/'.$filename;
                $arr[] = $dirname.'/small_'.$filename;
                $arr[] = $dirname.'/medium_'.$filename;
                $arr[] = $dirname.'/big_'.$filename;
                foreach($arr as $k=>$v){
                    if(file_exists($v)){
                        unlink($v);
                    }
                }
            }
            if (file_exists($path)) {
                unlink($path);
            }
        } else {
            $this->error('非法请求！');
        }
    }
    public function del(){
        $res = (new GoodsModel())->del();
        if ($res['status'] == 'error') {
            $this->error($res['message']); die;
        } else {
            $this->success('操作成功！');die;
        }
    }
}