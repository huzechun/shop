<?php


namespace Common\Model;


class RoleModel extends BaseModel
{

    protected $pk = 'id';
    protected $tableName = 'role';
    protected $_validate =[

        ['role_name','require','请填角色限名称！'],
        ['role_name','','该角色已存在',0,'unique',1]
//        ['pri_url','require','请填写权限URL！'],
       /* ['module_name','require','请填写控制器名称！'],
        ['action_name','require','请填写方法名称！'],
        ['action_name','','该权限的方法已存在',0,'unique'],*/
    ];

    public function adrole($data){

        if(!$this->create()){
            return false;
        }

        $this->add($data);
        return true;
    }

}