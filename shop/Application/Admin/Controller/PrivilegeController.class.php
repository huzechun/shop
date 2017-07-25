<?php namespace Admin\Controller;

use Common\Controller\AdminController;
use Common\Model\PrivilegeModel;

class PrivilegeController extends AdminController
{

    // 添加权限
    public function handler(){

        $id = I('get.id');

        if(IS_POST){

            $data = I('post.');

            //判断是否编辑操作
            if($id){
               $data['id'] = $id ;
            }
            //进行添加或是编辑操作
            $this->store(new PrivilegeModel() , $data , function(){
                $this->success('操作成功!' , u('admin/privilege/index'));die;
            });

        }

        //如果是编辑操作则获取旧数据
        if($id){

            $privilege = new PrivilegeModel();

            $data = $privilege->find($id);

            //dump($data);

            $this->assign('data' , $data) ;

         }

         $this->display();
    }

    // 编辑权限
    public function index(){

        //获取旧数据
        $data = (new PrivilegeModel()) ->select();

        $this->assign('data',$data);

        $this->display();
    }

    public function del(){

        //获取id
        $id = i('get.id');

        $privilege = (new PrivilegeModel()) ->where('id = ' . $id)->delete() ;

        if(!$privilege) {

            $this->success('操作失败',u('admin/privilege/index'));die;
        }
        else{

            $this->success('操作成功',u('admin/privilege/index'));die;
        }

    }

}