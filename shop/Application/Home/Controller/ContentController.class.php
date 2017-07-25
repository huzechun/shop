<?php


namespace Home\Controller;


use Common\Model\BrandModel;
use Common\Model\GoodsAttrModel;
use Common\Model\GoodsListModel;
use Common\Model\GoodsModel;
use Org\Util\Cart;
use Think\Controller;

class ContentController extends Controller {
    public function index() {
        $headConf = [
            'title' => '商品详情',
            'css'=>['index'],
            'js' => ['list']
        ];
        $this->assign('headConf', $headConf);
        $gid = i('get.gid');
        //获取商品数据
        $goodsData = (new GoodsModel())->alias('g')->join("shop_goods_detail d on g.gid=d.gid")->join("shop_brand b on g.bid=b.bid")->where("g.gid=$gid")->find();
        $goodsData['small_pic'] = explode(',', $goodsData['small_pic']);
        $goodsData['medium_pic'] = explode(',', $goodsData['medium_pic']);
        $goodsData['big_pic'] = explode(',', $goodsData['big_pic']);
        //获取规格数据并重组
        $specData = (new GoodsAttrModel())->alias('ga')->join("shop_type_attr ta on ga.taid=ta.taid")->where("ta.tatype=1 and ga.gid=$gid")->select();
        $temp = [];
        foreach ($specData as $k => $v) {
            $temp[$v['taname']][] = $v;
        }
        //获取商品属性数据
        $attrData = (new GoodsAttrModel())->alias('ga')->join("shop_type_attr ta on ga.taid=ta.taid")->where("ta.tatype=0 and ga.gid=$gid")->select();
        $this->assign('attrData',$attrData);
        $this->assign('goodsData', $goodsData);
        $this->assign('specData', $temp);
        $this->display();
    }

    //异步获取库存
    public function ajaxGetTotal() {
        if(IS_AJAX){
            $gid = $_POST['gid'];
            $combine = rtrim($_POST['combine'], ',');
            $total = (new GoodsListModel())->where("gid=$gid and combine='{$combine}'")->getField('total');
            if($total){
                echo $total;
            }else{
                echo 0;
            }
        }
    }
    //异步添加商品到购物车
    public function ajaxAddToCart() {
        if(IS_AJAX){
            $gid = $_POST['gid'];
            $num = $_POST['num'];
            $options = $_POST['options'];
            //根据gid获取商品数据
            $oneGoodsData = (new GoodsModel())->where("gid = $gid")->find();
            $data = [
                'id' => $gid, // 商品 ID
                'name' => $oneGoodsData['gname'],// 商品名称
                'num' => $num, // 商品数量
                'price' => $oneGoodsData['shopprice'], // 商品价格
                'options' => $options// 其他参数如价格、颜色、可以为数组或字符串
            ];
            (new Cart())->add($data);


            //判断是否是登录状态，如果是则将该条商品数据插入购物车数据表
            if(session('home.uid')){
                $uid = session('home.uid');
                $options = implode(' ',$options);
                $user_cart = M('user_cart');
                //判断即将加入购物车的商品是否在数据库已经有相同规格的相同商品
                $oldData = $user_cart->where("gid = $gid and options= '{$options}' and uid = $uid")
                    ->find();
                if(!$oldData){
                    //如果不是在已经有相同规格的相同商品的情况下，则直接将数据插入购物车表
                    $data = [
                        'name'=>$oneGoodsData['gname'],
                        'num'=>$num,
                        'price'=>$oneGoodsData['shopprice'],
                        'total'=>$num*$oneGoodsData['shopprice'],
                        'options'=>$options,
                        'pic'=>$oneGoodsData['pic'],
                        'uid'=>session('home.uid'),
                        'gid'=>$gid
                    ];
                    $user_cart->add($data);
                }else{
                    //如果已经有相同规格的相同商品，则需要更新数据库中已经有的那条数据
                    $num = $oldData['num']+$num;
                    $map = [
                        'num' => $num,
                        'total'=> $num*$oldData['price']
                    ];
                    $user_cart->where("ucid = {$oldData['ucid']}")->save($map);
                }

            }

            echo 1;
        }
    }
}