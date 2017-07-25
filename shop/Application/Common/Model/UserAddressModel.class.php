<?php


namespace Common\Model;


class UserAddressModel extends BaseModel {
    protected $pk = 'aid';
    protected $tableName = 'user_address';
    protected $_validate = [
        ['ac_name','require','请填写收货人姓名！'],
        ['p_address','require','请填写详细地址！'],
        ['postcode','require','请填写邮编！'],
        ['mobile','require','请填写手机号！'],
    ];
    public function addAddress($data){
        $uid = session('home.uid');
        //验证表单
        if(!$this->create($data)){
            return false;
        }
        //将其他所有地址的选中状态设置为0
        $this->where("uid = $uid")->setField('status',0);
        return $this->add($data);
    }
    public function saveAddress($data){
        $uid = session('home.uid');
        //验证表单
        if(!$this->create()){
            return false;
        }
        //将其他所有地址的选中状态设置为0
        $this->where("uid = $uid")->setField('status',0);
        return $this->save($data);
    }
}