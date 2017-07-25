<?php

namespace Home\Controller;


use Common\Model\CategoryModel;
use Common\Model\GoodsModel;
use Org\Util\Data;
use Think\Controller;

class IndexController extends Controller {
    public function index(){
        //定义头信息和js、css等
        $headConf = [
            'title'=>'商城首页',
            'css'=>['index'],
            'js'=>['index']
        ];
        $this->assign('headConf',$headConf);
        $data = (new Data())->channelLevel((new CategoryModel())->order('cid asc')->select());

        foreach($data as $k=>$v){
            foreach($v['_data'] as $kk=>$vv){
                if(!$vv['_data']){
                    //如果三级分类不存在，则读取10条二级分类下的商品代替
                    $data[$k]['_data'][$kk]['_data'] = (new GoodsModel())->where("cid = {$vv['cid']}")->limit(10)->select();
                    $data[$k]['_data'][$kk]['tenData'] = 1;
                }
            }
        }
        //print_r($data);
        $this->assign('data',$data);
        $goodsModel = (new GoodsModel());
        //定义随机商品数据
        $randData = $goodsModel->order('rand()')->limit(5)->select();
        $this->assign('randData',$randData);
        //定义 tip5商品数据
        $tipsData = $goodsModel->limit(6,5)->select();
        $this->assign('tipsData',$tipsData);
        //定义新品数据
        $newData = $goodsModel->order('rand()')->limit(25)->select();
        $this->assign('newData',$newData);
        //360楼层数据-左
        $data360_l = $goodsModel->where('cid = 217')->order('gid desc')->limit(6)->select();
        $this->assign('data360_l',$data360_l);
        //360楼层数据-右
        $data360_r = $goodsModel->where('cid = 217')->order('rand()')->limit(8)->select();
        //print_r($data360_r);
        $this->assign('data360_r',$data360_r);

        //魅族数据-左
        $meizu_l = $goodsModel->where('cid = 9')->order('gid desc')->limit(6)->select();
        $this->assign('meizu_l',$meizu_l);
        //魅族数据-右
        $meizu_r = $goodsModel->where('cid = 9')->order('rand()')->limit(8)->select();
        //print_r($meizu_r);
        $this->assign('meizu_r',$meizu_r);

        //小米数据-左
        $mi_l = $goodsModel->where('cid = 216')->order('gid desc')->limit(6)->select();
        $this->assign('mi_l',$mi_l);
        //小米数据-右
        $mi_r = $goodsModel->where('cid = 216')->order('rand()')->limit(8)->select();
        $this->assign('mi_r',$mi_r);


        //搜索功能
        $keywords = (new GoodsModel())->field('gname')->select();
        //dump($keywords);
        $this->assign('keyword',$keywords);

        $this->display();
    }
}