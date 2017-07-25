<?php


namespace Common\Model;


class GoodsListModel extends BaseModel {
    protected $pk = 'glid';
    protected $tableName = 'goods_lists';
    protected $_validate = [
        ['total','require','请填写库存！'],
        ['gnumber','require','请填写货号！'],
    ];
    public function handler(){
        //验证下拉是否被选中
        foreach(i('post.combine') as $k=>$v){
            if($v==0){
                $this->error= '请选择'.$k.'!';
                return ['status'=>'error','message'=>$this->getError()];
            }
        }
        //验证库存和货号是否填写
        if(!$this->create()){
            return ['status'=>'error','message'=>$this->getError()];
        }
        //处理combine数据
        $this->data['combine'] = implode(',',$this->data['combine']);
        //加入gid字段数据
        $this->data['gid'] = i('get.gid');
        //添加货品
        return $this->store($this->data);

    }
}