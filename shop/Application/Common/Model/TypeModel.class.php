<?php


namespace Common\Model;


class TypeModel extends BaseModel {
    protected $pk = 'tid';
    protected $tableName = 'type';
    protected $_validate = [
        ['tname','require','类型名称不能为空！'],
    ];
}