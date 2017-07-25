<?php


namespace Admin\Controller;


use Common\Controller\BaseController;
use Common\Model\UserModel;
use Think\Verify;

class UserController extends BaseController {
    public function login() {
        //判断是否已经登录
        if(session('admin.uid')){
            $this->redirect('admin/index/index');
        }
        //print_r($_POST);
        if (IS_POST) {
            //创建用户模型类
            $userModel = new UserModel();
            //通过调用用户模型类中方法进行判断
            if($userModel->isLogin()){
                //登录成功直接跳转后台页面

                $this->redirect('admin/index/index');
            }else{
                //登录失败显示错误并返回
                $this->error($userModel->getError());
            }
        }
        //载入模板
        $this->display();
    }
    public function loginOut(){
        //清除php中的session变量
        session_unset();
        //销毁session文件
        session_destroy();
        //重定向到登录页
        $this->redirect('admin/user/login');
    }
    public function code() {
        //创建验证类对象
        $verity = new Verify();
        $verity->fontSize = 30;
        //$verity->useNoise = false;
        $verity->length   = 2;
        //获取验证码图形
        $verity->entry();
    }
}