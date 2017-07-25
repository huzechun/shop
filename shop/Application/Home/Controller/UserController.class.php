<?php


namespace Home\Controller;


use Common\Model\GoodsModel;
use Common\Model\HomeUserModel;
use Think\Controller;
use Think\Verify;

class UserController extends Controller {
    private $HomeUserModel;
    public function __construct() {
        parent::__construct();
        $this->HomeUserModel = new HomeUserModel();
    }

    public function login(){
        if(IS_POST){
            print_r($_POST);
            if($this->HomeUserModel->login()){
                //登录成功需要判断session中是否有购物车数据，如果有，则压入当前用户的购物车数据表
                if(session('cart.goods')){
                    $this->updateCart();
                }
                $this->redirect('index/index');
            }else{
                $this->error($this->HomeUserModel->getError());die;
            }
        }
        $this->display();
    }
    //更新购物车
    public function updateCart(){
        $data = session('cart.goods');
        foreach($data as $k=>$v){
            $uid = session('home.uid');
            $options = implode(' ',$v['options']);
            $user_cart = M('user_cart');
            //判断即将加入购物车的商品是否在用户的购物车中已经有相同规格的相同商品
            $oldData = $user_cart->where("gid = {$v['id']} and options= '{$options}' and uid = $uid")
                ->find();
            if(!$oldData){
                //如果不是在已经有相同规格的相同商品的情况下，则直接将数据插入购物车表
                $pic = (new GoodsModel())->where("gid = {$v['id']}")->getField('pic');
                $data = [
                    'name'=>$v['name'],
                    'num'=>$v['num'],
                    'price'=>$v['price'],
                    'total'=>$v['total'],
                    'options'=>$options,
                    'pic'=>$pic,
                    'uid'=>session('home.uid'),
                    'gid'=>$v['id']
                ];
                $user_cart->add($data);
            }else{
                //如果已经有相同规格的相同商品，则需要更新数据库中已经有的那条数据
                $num = $oldData['num']+$v['num'];
                $map = [
                    'num' => $num,
                    'total'=> $num*$oldData['price']
                ];
                $user_cart->where("ucid = {$oldData['ucid']}")->save($map);
            }
        }

    }
    public function loginOut(){
        session('home',null);
        //退出登录的时候需要将session中的购物车数据库清空
        session('cart',null);
        $this->redirect('index/index');
    }
    public function reg(){
        if(IS_POST){
            if($this->HomeUserModel->reg()){
                $this->success('注册成功！请先登录！',u("login"));die;
            }else{
                $this->error($this->HomeUserModel->getError());die;
            }
        }
        $this->display();
    }
    public function code(){
        $Verify = new Verify();
        $Verify->fontSize = 30;
        $Verify->length   = 4;
        $Verify->entry();
    }
}