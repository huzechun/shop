<?php


namespace Common\Model;


class UserOrderListModel extends BaseModel {
    protected $pk = 'olid';
    protected $tableName = 'user_order_list';
    protected $_validate = [
        //['password', 'require', '密码不能为空！'],
    ];
}