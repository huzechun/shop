<?php


namespace Common\Model;


class BrandModel extends BaseModel {
    protected $pk = 'bid';
    protected $tableName = 'brand';
    protected $_validate = [
        ['bname', 'require', '请设置品牌名称！'],
    ];
}