<?php


namespace Admin\Controller;


use Common\Controller\AdminController;
use Common\Model\TypeAttrModel;

class TypeAttrController extends AdminController {
    public function handler() {
        //获取类型属性id
        $taid = i('get.taid');
        if (IS_POST) {
            //获取表单数据
            $data = i('post.');
            //判断是否是编辑操作
            if ($taid) {
                $data['taid'] = $taid;
            }
            //执行添加或编辑操作
            $this->store(new TypeAttrModel(), $data,function(){
                //执行回调
                $this->success('操作成功！',u('admin/typeAttr/lists',['tid'=>i('get.tid')]));die;
            });
        }
        if($taid){
            //如果是编辑操作则获取旧数据
            $type_attr = (new TypeAttrModel())->find($taid);
            $this->assign('attr',$type_attr);
        }
        //显示页面
        $this->display();
    }

    public function lists() {
        //获取tid
        $tid = i('get.tid');
        //获取对应类型id的属性数据
        $data = (new TypeAttrModel())->where('tid = '.$tid)->select();
        //将数据注入模板
        $this->assign('attr',$data);
        //显示页面
        $this->display();
    }
    public function del(){
        //获取要删除的类型属性id
        $taid = i('get.taid');
        //判断删除操作是否成功
        if((new TypeAttrModel())->where('taid = '.$taid)->delete()){
            $this->success('操作成功！');die;
        }
        //删除失败返回列表页
        $this->error('操作失败！');die;
    }
}