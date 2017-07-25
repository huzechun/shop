<?php


namespace Home\Controller;


use Common\Controller\HomeController;
use Common\Model\GoodsModel;
use Common\Model\UserAddressModel;
use Common\Model\UserOrderListModel;
use Common\Model\UserOrderModel;

class OrderController extends HomeController {
    //我的订单页面
    public function index(){
        $headConf = [
            'title' => '我的订单',
            'css'=>['index'],
            'js' => ['list']
        ];
        $this->assign('headConf',$headConf);
        if(IS_POST){
            //走到这里说明订单下单并且已经付款完毕,现在将订单添加到数据库
            $orderModel = new UserOrderModel();
            if($orderModel->addOrder()){
                $this->success('操作成功！',u('index'));die;
            }else{
                $this->error($orderModel->getError());die;
            }
        }
        $uid = session('home.uid');
        //获取所有订单数据
        $orderData = (new UserOrderListModel())->alias('ol')->join('shop_user_order o on ol.oid=o.oid')->join('shop_goods g on ol.gid=g.gid')->where("o.uid = $uid")->select();
        //重组数据
        $temp = [];
        foreach($orderData as $k=>$v){
            $temp[$v['oid']][] = $v;
        }
        $this->assign('orderData',$temp);
        $this->display();
    }
    //地址管理页面
    public function manageAddress(){
        $headConf = [
            'title' => '确认下单',
            'css'=>['account'],
            'js' => ['list','account','area']
        ];
        $uid = session('home.uid');
        $this->assign('headConf',$headConf);
        //获取该用户的所有地址
        $addressData = (new UserAddressModel())->where("uid = $uid")->select();
        $this->assign('addressData',$addressData);
        $this->display();
    }
    //确认下单页面
    public function sendOrder(){
        $headConf = [
            'title' => '确认下单',
            'css'=>['account'],
            'js' => ['list','account','area']
        ];
        $this->assign('headConf',$headConf);

        $uid = session('home.uid');
        //获取该用户的所有地址
        $addressData = (new UserAddressModel())->where("uid = $uid")->select();
        $this->assign('addressData',$addressData);
        //获取该用户的默认地址
        $default_address = (new UserAddressModel())->where("uid = $uid and status = 1")->find();
        $this->assign('default_address',$default_address);
        //获取该用户的所有订单
        $order = session('order');
        $this->assign('order',$order);
        $this->display();
    }


    //将选中的订单数据进行处理然后跳转到下单页面
    public function getCartData(){
        if(IS_POST){
            $ucids = i('post.ucids');
            //这里先将这个数据存入session，方便订单结账后更新购物车
            session('selected_sids',$ucids);
            //重组$ucids数组
            $order = [];
            $user_cart = M('user_cart');
            foreach($ucids as $k=>$v){
                $order['order'][$k] = $user_cart->find($v);
            }
            //将订单列表的总商品数和合计价格压入数组
            $order['total_rows'] = 0;
            $order['total'] = 0;
            foreach($order['order'] as $k=>$v){
                $order['total_rows'] += $v['num'];
                $order['total'] += $v['total'];
            }
            //将数据存入session中，作为中转使用
            session('order',$order);
            $this->redirect('sendOrder');
        }else{
            $this->redirect('cart/index');
        }

    }


    //异步删除订单
    public function ajaxDelOrder(){
        if(IS_AJAX){
            //删除订单表中的数据
            $oid = i('post.oid');
            $res= (new UserOrderModel())->delete($oid);
            //删除订单列表中的数据
            (new UserOrderListModel())->where("oid =$oid ")->delete();
            if($res){
                echo 1;
            }else{
                echo 0;
            }
        }
    }

    //这里是异步对收货地址的增删改查
    //异步添加收货地址信息
    public function ajaxAddAddress(){
        if(IS_AJAX){
            $data = $_POST;
            $data['uid'] = session('home.uid');
            $data['status'] = 1;
            //处理city数组成字符串
            $data['city'] = implode(' ',$data['city']);
            //添加地址
            $UserAddressModel = new UserAddressModel();
            $res = $UserAddressModel->addAddress($data);
            //将表单数据处理
            $_POST['city'] = $data['city'];
            $_POST['aid'] = $res;
            if($res){
                echo json_encode($_POST,JSON_UNESCAPED_UNICODE);
            }else{
                echo 0;
            }
        }
    }
    //异步编辑收货地址信息
    public  function ajaxEditAddress(){
        if(IS_AJAX){
            $data = $_POST;
            //获取用户id
            $data['uid'] = session('home.uid');
            //标记地址被选择
            $data['status'] = 1;
            //处理city数组成字符串
            $data['city'] = implode(' ',$data['city']);
            //修改地址
            $UserAddressModel = new UserAddressModel();
            $res = $UserAddressModel->saveAddress($data);
            //处理表单数据
            $_POST['city'] = $data['city'];
            if($res){
                echo json_encode($_POST,JSON_UNESCAPED_UNICODE);
            }else{
                echo 0;
            }
        }
    }
    //异步修改用户默认收货地址
    public function ajaxUpdateSelectedAd(){
        if(IS_AJAX){
            $aid = $_POST['aid'];
            $uid = session('home.uid');
            //将该用户的所有地址数据的status改为0
            $UserAddressModel = (new UserAddressModel());
            $UserAddressModel->where("uid = $uid")->setField('status',0);
            //将当前选中地址改为默认地址
            $UserAddressModel->where("uid = $uid and aid = $aid")->setField('status',1);
            if($UserAddressModel){
                echo 1;
            }else{
                echo 0;
            }
        }
    }
    //异步删除用户收货地址
    public function ajaxDelAddress(){
        $aid = i('post.aid');
        $res = (new UserAddressModel())->delete($aid);
        if($res){
            echo 1;
        }else{
            echo 0;
        }
    }
}