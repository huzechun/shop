<?php
namespace Admin\Controller;
use Common\Controller\AdminController;
use Common\Model\GoodsModel;

class IndexController extends AdminController {
    public function index(){
        //获取用户信息
    	$this->display();
    }
}