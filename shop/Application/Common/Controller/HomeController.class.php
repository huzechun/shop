<?php


namespace Common\Controller;


class HomeController extends BaseController {
    public function __construct() {
        parent::__construct();
        //判断是否登录
        if(!session('home.uid')){
            $this->error('请先登录！',u("user/login"));
        }
    }
}