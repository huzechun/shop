<?php


namespace Common\Model;


class AuthModel extends BaseModel {
    protected $pk = 'id';
    protected $tableName = 'auth_rule';
    protected $_validate = [
        ['title', 'require', '请设置权限名称！'],
        ['name', '', '该权限已经存在！',0,'unique'],
    ];
    public function AddAuth($data){
        if(!$this->create()){
            return false;
        }
        $this->add($data);
        return true;
    }
}