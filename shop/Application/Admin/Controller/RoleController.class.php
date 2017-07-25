<?php namespace Admin\Controller;

use Common\Controller\AdminController;
use Common\Model\PrivilegeModel;
use Common\Model\RoleModel;

class RoleController extends AdminController
{

    // 添加权限
    public function addRole(){

        //所有权限数据
        $data  = (new PrivilegeModel())->select();
        $this->assign('data',$data);

        //dump($data);



        // 添加勾选的权限
        if(IS_POST){

            $info = I('post.');

           // dump($info);
            //die;

            //存角色表数据

            $this->(store(new RoleModel(),$data , function(){

                $this->getError();
            }));




            //角色id
            $role = M('role');
            $role_name = $info['role_name'];
            $roid['role_name'] = $role_name ;
            $role_id = $role->field('id')->where($roid)->find();


            //存角色权限表数据
            $info_prid = I('post.pri_id');

            if($info_prid) {

                $role_pri = M('RolePrivilege');

                foreach ($info_prid as $k => $v) {

                    $role_pri->add(['role_id' => $role_id['id'], 'pri_id' => $v]);

                }

                if($role_pri)   echo "<script> alert('操作成功！')</script>";
                else            echo "<script> alert('操作失败！')</script>";
            }

        }

         $this->display();
    }

    // 角色列表
    public function index(){

    //获取旧数据
            /*        SELECT
            a.role_name , GROUP_CONCAT(c.pri_name) pri_name
            from shop_role a left join shop_role_privilege b
            on a.id = b.role_id
            left join shop_privilege c on b.pri_id = c.id
            group by a.id*/

       $data = (new RoleModel())->field('a.role_name ,a.id, group_concat(c.pri_name) pri_name')->alias('a')
           ->join('join shop_role_privilege b  on a.id = b.role_id
                   left join shop_privilege c on b.pri_id = c.id
                   group by a.id')->select();

        $this->assign('data',$data);

        $this->display();
    }

    //编辑角色

    public function editrole(){

        //所有权限数据
        $pri_data = (new PrivilegeModel())->select();
        $this->assign('pri_data',$pri_data);

        //dump($pri_data);

        $role_id = I('get.id');

        //选中id角色的角色名称
        $role_name = (new RoleModel())->where("id = {$role_id}")->find();
        $this->assign('role_name',$role_name);
        //dump($role_name);

        //被选择的角色所拥有的权限id
        $data = (new RoleModel())->field('group_concat(b.pri_id) pri_id')->alias('a')
            ->join('shop_role_privilege b on a.id = b.role_id')
            ->where("a.id = {$role_id}")
            ->find();

        $this->assign('pri_id',$data['pri_id']);

        //dump($data['pri_id']);

        /***
         *    权限编辑
         *
         *   修改权限(修改复选框)先将原来的选中权限全部清除，
         *   再添加修改后的选中权限
         *
         */

           if(IS_POST){

                $role_pri = M('RolePrivilege');

                //清除已有的权限
                $role_pri->where("role_id = {$role_id}") ->delete();

                // 添加提交的权限id
                $sbmt_prid = I('post.');

                $id_data = $sbmt_prid['$v']["'id'"];

                $role_id = I('get.id');

                //dump($sbmt_prid);
               // dump($sbmt_prid['$v']["'id'"]);
               // dump($role_id);

                foreach ($id_data as $k => $v) {

                    $role_pri->add(['role_id' => $role_id, 'pri_id' => $v]);

                }

                if($role_pri) {
                    $this->success('操作成功!', u('index', ['id' => $role_id])) ; die;
                }

                else{
                    $this->success('操作失败!'); die;
                }

           }


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