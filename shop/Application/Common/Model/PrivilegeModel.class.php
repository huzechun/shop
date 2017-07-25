<?php


namespace Common\Model;


class PrivilegeModel extends BaseModel
{

    protected $pk = 'id';
    protected $tableName = 'privilege';
    protected $_validate =[

        ['pri_name','require','请填写权限名称！'],
        ['pri_url','require','请填写权限URL！'],
       /* ['module_name','require','请填写控制器名称！'],
        ['action_name','require','请填写方法名称！'],
        ['action_name','','该权限的方法已存在',0,'unique'],*/
    ];

    public function addPrivilege($data){

        if(!$this->create()){
            return false;
        }

        $this->add($data);
        return true;
    }

}