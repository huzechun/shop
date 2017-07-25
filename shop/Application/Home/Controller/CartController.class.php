<?php


namespace Home\Controller;


use Common\Model\GoodsModel;
use Org\Util\Cart;
use Think\Controller;

class CartController extends Controller {
    public function index() {
        $headConf = [
            'title' => '购物车',
            'css'=>['index'],
            'js' => ['list']
        ];
        $this->assign('headConf', $headConf);
        //这里需要判断是否登录
        if(!session('home.uid')){
            //如果是游客则读取session中购物车的数据
            $data = $_SESSION['cart'] ? $_SESSION['cart']['goods'] : [];
            foreach ($data as $k => $v) {
                $data[$k]['pic'] = (new GoodsModel())->where("gid = {$v['id']}")->getField('pic');
            }
            //获取购物车总商品数和总价格(这里不能直接获取_SESSION['cart']['total_rows'])
            $total_rows = 0;
            $total = 0;
            foreach($data as $k =>$v){
                $total_rows += $v['num'];
                $total += $v['total'];
            }
        }else{
            //如果是登录了的会员，则从数据库中读取该会员的购物车信息
            //获取用户id
            $uid = session('home.uid');
            //获取该用户的所有购物车数据
            $user_cart = M('user_cart');
            $data = $user_cart->where("uid = $uid")->select();
            //获取购物车总商品数和总价格
            $total_rows = 0;
            $total = 0;
            foreach($data as $k =>$v){
                $total_rows += $v['num'];
                $total += $v['total'];
            }
        }
        //分配数据
        $this->assign('total_rows', $total_rows);
        $this->assign('total', $total);
        $this->assign('data', $data);
        $this->display();
    }
    //异步获取购物车数据数量
    public function ajaxGetCartNum(){
        if(IS_AJAX){
            //判断是否登录
            $uid = session('home.uid');
            if($uid){
                //如果登录了，则获取该用户下的购物车数据数量
                $count = M('user_cart')->where("uid = $uid")->count();
            }else{
                //如果没有登录，则获取session中购物车数据的数量
                if(isset($_SESSION['cart']['goods'])){
                    $count = count($_SESSION['cart']['goods']);
                }else{
                    $count = 0;
                }
            }
            echo $count;
        }
    }
    //异步删除购物车数据（hover出现下拉我的购物车模块）
    public function ajaxDelCartData(){
        if(IS_AJAX){
            //判断是否登录
            $uid = session('home.uid');
            if($uid){
                $ucid = i('post.ucid');
                M('user_cart')->delete($ucid);
                echo 1;
            }else{
                $sid = i('post.sid');
                unset($_SESSION['cart']['goods'][$sid]);
                echo 1;
            }
        }
    }
    //异步获取购物车数据（hover出现下拉我的购物车模块）
    public function ajaxGetCartData(){
        if(IS_AJAX){
            //判断是否登录，如果是，则获取该用户的所有购物车数据
            $uid = session('home.uid');
            if($uid){
                $data = M('user_cart')->where("uid = $uid")->select();
                if($data){
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                }else{
                    echo 0;
                }
            }else{
                //如果是游客，则获取session中的购物车数据
                $data = session('cart.goods');
                //session中是没有pic的，需要重新压入
                foreach($data as $k=>$v){
                    $data[$k]['pic'] = (new GoodsModel())->where("gid = {$v['id']}")->getField('pic');
                }
                if($data){
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                }else{
                    echo 0;
                }
            }
        }
    }
    //异步更新购物车
    public function ajaxUpdateCart() {
        if(IS_AJAX){
            $num = $_POST['num'];
            $sid = $_POST['sid'];
            $ucid = $_POST['ucid'];
            $data = [
                'sid' => $sid,
                'num' => $num,
            ];
            (new Cart())->update($data);

            //判断是否是登录状态，如果是，则需要更新该用户的购物车数据
            $uid = session('home.uid');
            $user_cart = M('user_cart');
            if($uid){
                $price = $user_cart->where("ucid = $ucid")->getField('price');
                $total = $num*$price;
                $data = [
                    'ucid'=>$ucid,
                    'num'=>$num,
                    'total'=>$total
                ];
                $user_cart->save($data);
            }

        }
    }
    //异步删除购物车中的一条数据
    public function ajaxDelCartGoods(){
        if(IS_AJAX){
            $del_sid = $_POST['sid'];
            $del_ucid = $_POST['ucid'];
            (new Cart())->del($del_sid);

            //判断是否是登录状态，如果是，则需要更新该用户的购物车数据
            $uid = session('home.uid');
            $user_cart = M('user_cart');
            if($uid){
                $user_cart->delete($del_ucid);
            }
        }
    }
}