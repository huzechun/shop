<?php


namespace Common\Model;


class GoodsAttrModel extends BaseModel {
    protected $pk = 'gaid';
    protected $tableName = 'goods_attr';
    protected $_validate = [
        //['gname', 'require', '请设置商品名称！'],
    ];
}