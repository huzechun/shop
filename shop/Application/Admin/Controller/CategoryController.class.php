<?php


namespace Admin\Controller;


use Common\Controller\AdminController;
use Common\Model\CategoryModel;
use Common\Model\TypeModel;

class CategoryController extends AdminController {
    //处理编辑和添加顶级分类
    public function handler() {
        $cid = i('get.cid');
        if (IS_POST) {
            $data = i('post.');
            if ($cid) {
                $data['cid'] = $cid;
            }
            $this->store(new CategoryModel(), $data, function () {
                $this->success('操作成功！', u('Category/lists'));
                die;
            });
        }
        //如果是编辑操作，获取旧数据
        if ($cid) {
            $oldData = (new CategoryModel())->where('cid =' . $cid)->find();
            $this->assign('oldData', $oldData);
        }
        //获取所有类型数据(这里改为顶级分类不需要设置类型tid,默认tid为0)
        //$data = (new TypeModel())->select();
        //注入页面
        $this->assign('typeData', $data);
        //载入模板
        $this->display();
    }

    //添加子分类
    public function addSonCate() {
        if (IS_POST) {
            $data = i('post.');
            $this->store(new CategoryModel(), $data, function () {
                $this->success('操作成功！', u('category/lists'),1);
                die;
            });
        }
        //获取父类id
        $pid = i('get.pid');
        //获取父类数据
        $faterData = (new CategoryModel())->where('cid =' . $pid)->find();
        //获取类型数据
        $typeData = (new TypeModel())->select();
        //注入数据
        $this->assign('faterData', $faterData);
        $this->assign('typeData', $typeData);
        //载入模板
        $this->display();
    }

    //编辑子分类
    public function editSonCate() {
        $cid = i('get.cid');
        if (IS_POST) {
            $data = i('post.');
            $data['cid'] = $cid;
            $this->store(new CategoryModel(),$data,function(){
                $this->success('操作成功！',u('lists'));die;
            });
        }
        //获取cid
        //获取旧数据
        $oldData = (new CategoryModel())->where('cid=' . $cid)->find();
        //获取分类数据
        $cateData = (new CategoryModel())->getCateData($cid);
        //获取类型数据
        $typeData = (new TypeModel())->select();
        //注入数据
        $this->assign('cateData',$cateData);
        $this->assign('oldData', $oldData);
        $this->assign('typeData', $typeData);
        //载入模板
        $this->display();
    }

    //显示所有分类
    public function lists() {
        //获取分类数据
        $data = (new CategoryModel())->getAll();
        //注入数据
        $this->assign('cate', $data);
        //载入模板
        $this->display();
    }

    public function del() {
        if ((new CategoryModel())->del()) {
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }
    }
}