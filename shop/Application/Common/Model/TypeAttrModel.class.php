<?php


namespace Common\Model;


class TypeAttrModel extends BaseModel {
    protected $pk = 'taid';
    protected $tableName = 'type_attr';
    protected $_validate = [
        ['taname','require','类型属性名不能为空！'],
        ['tavalue','require','类型属性值不能为空！'],
    ];
}