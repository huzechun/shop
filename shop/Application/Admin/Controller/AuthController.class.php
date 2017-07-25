<?php


namespace Admin\Controller;


use Common\Controller\AdminController;
use Common\Model\AuthModel;

class AuthController  extends AdminController {
    //权限列表
    public function index(){
        $authData = (new AuthModel())->select();
        $this->assign('authData',$authData);
        $this->display();
    }
    //添加权限
    public function addAuth(){
        if(IS_POST){
            $auth = new AuthModel();
            $res = $auth->addAuth($_POST);
           if($res){
                $this->success('操作成功！');die;
           }else{
               $this->error($auth->getError());die;
           }
        }
        $this->display();
    }
    //删除权限
    public function delAuth(){

    }
    //编辑权限
    public function edit(){

    }
}