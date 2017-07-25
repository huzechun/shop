<?php


namespace Admin\Controller;


use Common\Controller\AdminController;
use Common\Model\TypeAttrModel;
use Common\Model\TypeModel;

class TypeController extends AdminController {
    public function handler() {
        //获取tid
        $tid = i('get.tid');
        if (IS_POST) {
            //获取post表单数据
            $data = i('post.');
            //判断是否是编辑操作
            if ($tid) {
                $data['tid'] = $tid;
            }
            //进行添加或者编辑操作
            $this->store(new TypeModel(), $data, function () {
                $this->success('操作成功！', u('admin/type/lists'));
                die;
            });
        }
        if ($tid) {
            //如果是编辑操作获取旧数据
            $data = (new TypeModel())->find($tid);
            $this->assign('type', $data);
        }
        //显示页面
        $this->display();
    }

    public function lists() {
        //获取旧数据
        $type = (new TypeModel())->select();
        //将旧数据注入
        $this->assign('type', $type);
        //显示页面
        $this->display();
    }

    public function del() {
        //获取tid
        $tid = i('get.tid');
        //先删除类型
        if((new TypeModel())->where('tid = ' . $tid)->delete()){
            //确保类型删除成功后再删除该类型对应的所有的属性
            (new TypeAttrModel())->where('tid = ' . $tid)->delete();
            $this->success('操作成功！', u('admin/type/lists'));die;
        }else{
            $this->success('操作失败！', u('admin/type/lists'));die;
        }
        //删除失败返回列表
    }

}