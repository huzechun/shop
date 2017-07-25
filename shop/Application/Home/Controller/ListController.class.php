<?php


namespace Home\Controller;


use Common\Model\BrandModel;
use Common\Model\CategoryModel;
use Common\Model\GoodsAttrModel;
use Common\Model\GoodsModel;
use Common\Model\TypeAttrModel;
use Think\Controller;

class ListController extends Controller {
    public function index(){
        $headConf= [
            'title'=>'分类列表',
            'css'=>['index'],
            'js'=>['list']
        ];
        $this->assign('headConf',$headConf);

        //获取cid
        $cid = i('get.cid');
        //1、面包屑
        //点击获取该分类下的所有父级分类
        $faterCates = $this->getFather((new CategoryModel())->select(),$cid);
        //print_r($faterCates);
        //TRY 品牌
        //$brandCates = $this->getFather((new BrandModel())->select(),$cid);
        //dump($brandCates);
        $faterCates = array_reverse($faterCates);
        //print_r($faterCates);
        $this->assign('faterCates',$faterCates);
        //2、获取该分类下的所有商品gid
        //先获取该分类下的所有子分类id的集合 cids
        $cids = $this->getSonCid((new CategoryModel())->select(),$cid);
        //print_r($cids);
        $cids[] = $cid;
        //再根据cids获取该分类下的所有商品gid
        $gids = (new GoodsModel())->where("cid in (".implode(',',$cids).")")->getField('gid',true);


        if($gids){
            //获取所有商品属性
            $attrData = (new GoodsAttrModel())->where("gid in (".implode(',',$gids).")")->group('gavalue')->select();
            //进行数组重组(注意重组的数组必须要有数字索引)
            $temp = [];
            foreach($attrData as $k=>$v){
                $temp[$v['taid']][] = $v;
            }
            $finalTemp = [];
            foreach($temp as $k=>$v){
                $finalTemp[] = [
                    'name'=> (new TypeAttrModel())->where("taid = $k")->getField('taname'),
                    'value'=> $v
                ];
            }
            //p($finalTemp);
            $this->assign('attrData',$finalTemp);

            //进行筛选
            //定义筛选需要的变量param
            $param = isset($_GET['param'])?explode('_',$_GET['param']):array_fill(0,count($temp),0);
            $this->assign('param',$param);


            //进行筛选功能处理
            $filterGids = [];
            foreach($param as $k=>$v){
                if($v){
                $gavalue = (new GoodsAttrModel())->where("gaid = $v")->getField('gavalue');
                $filterGids[] = (new GoodsAttrModel())->where("gavalue = '{$gavalue}'")->getField('gid',true);
            }

            }

            if($filterGids){
                //如果有筛选，则求交集
                $finalGids = $filterGids[0];
                foreach($filterGids as $k=>$v){
                    $finalGids = array_intersect($finalGids,$v);
                }
                //还需要跟gids求一次交集，因为finalgids是从商品属性表通过gavalue（短袖）筛选出来的，有可能不属于当前分类
                $finalGids = array_intersect($finalGids,$gids);
            }else{
                //没有进行任何筛选，则直接为gids
                $finalGids = $gids;
            }
        }else{
            $finalGids = [];
        }

        //根据finalGids获取商品
        if($finalGids){
            $goodsData = (new GoodsModel())->where("gid in (".implode(',',$finalGids).")")->select();
        }else{
            $goodsData = [];
        }
        $this->assign('goodsData',$goodsData);
        $this->display();

    }
    public function getFather($data,$cid){
        static $temp = [];
        foreach($data as $k=>$v){
            if($v['cid'] == $cid){
                $temp[] = $v;
                $this->getFather($data,$v['pid']);
            }
        }
        return $temp;
        //print_r($temp);
    }

    public function getSonCid($data,$cid){
        static $temp = [];
        foreach($data as $k=>$v){
            if($v['pid'] == $cid){
                $temp [] = $v['cid'];
                $this->getSonCid($data,$v['cid']);
            }
        }
        return $temp;
    }
}