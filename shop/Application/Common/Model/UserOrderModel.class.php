<?php


namespace Common\Model;


class UserOrderModel extends BaseModel {
    protected $pk = 'oid';
    protected $tableName = 'user_order';
    protected $_validate = [
    ];

    public function addOrder() {
        //1、先添加订单表数据
        //定义要添加的数据
        $data = [
            'total_price' => session('order.total'),
            'aid' => i('post.order_aid'),
            'uid' => session('home.uid'),
            'time' => time(),
            'status' => '已付款',
        ];
        //判断是否需要开发票
        if(i('post.invoice')==1){
            if(i('post.p_or_c')=='个人'){
                $data['note'] = '个人发票';
            }else{
                $data['note'] = '单位发票，单位名称：'.i('post.danweiname');
            }
        }else{
            $data['note'] = '';
        }
        //定义订单号
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $data['order_number'] = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        //插入数据
        $oid = $this->add($data);
        //2、添加订单列表数据
        $orderData = session('order.order');
        //将订单列表的数据循环添加进数据库
        foreach($orderData as $k=>$v){
            $data = [
                'gid' => $v['gid'],
                'num'=>$v['num'],
                'price'=>$v['price'],
                'total'=>$v['total'],
                'oid'=>$oid,
                'note'=>''
            ];
            (new UserOrderListModel())->add($data);
        }
        //3、更新购物车中的数据（将已经付款的订单列去除）
        $ucids = session('selected_sids');
        foreach($ucids as $k=>$v){
            M('user_cart')->delete($v);
        }
        return true;
    }
}