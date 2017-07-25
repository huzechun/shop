<?php


namespace Common\Model;


class GoodsDetail extends BaseModel {
    protected $pk = 'did';
    protected $tableName = 'goods_detail';
    protected $_validate = [
        ['intro','require','请填写商品详情！'],
        ['service','require','请填写售后服务！'],
    ];
}