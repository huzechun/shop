<?php

namespace Common\Model;


class GoodsModel extends BaseModel {
    protected $pk = 'gid';
    protected $tableName = 'goods';
    protected $_validate = [
        ['cid', 'require', '请选择分类！'],
        ['bid', 'require', '请选择品牌！'],
        ['gname', 'require', '请设置商品名称！'],
        ['gnumber', 'require', '请设置商品货号！'],
        ['unit', 'require', '请设置商品单位！'],
        ['marketprice', 'require', '请设置商品市场价格！'],
        ['shopprice', 'require', '请设置商品商城价格！'],
        ['pic', 'require', '请设置商品列表图片！'],
        ['click', 'require', '请设置商品点击次数！'],
        ['total', 'require', '请设置商品库存！'],
    ];

    public function addGoods() {
        //商品表的验证
        if (!$this->create()) {
            return ['status' => 'error', 'message' => $this->getError()];
        }
        //商品详情表的验证
        if(!i('post.goodsDetailPics')){
            return ['status' => 'error', 'message' => '请上传商品图册！'];
        }
        $goodsDetail = (new GoodsDetail());
        if(!$goodsDetail->create()){
            return ['status' => 'error', 'message' => $goodsDetail->getError()];
        }
        //判断是否是顶级分类
        $cid = i('post.cid');
        $tid = (new CategoryModel())->where("cid=$cid")->getField('tid');
        if($tid == 0){
            return ['status'=>'error','message'=>'顶级分类不能添加商品！'];
        }

        //1.商品表添加
        $data = [
            'gname' => i('post.gname'),
            'sname' => i('post.sname'),
            'gnumber' => i('post.gnumber'),
            'unit' => i('post.unit'),
            'marketprice' => i('post.marketprice'),
            'shopprice' => i('post.shopprice'),
            'pic' => i('post.pic'),
            'click' => i('post.click'),
            'time' => time(),
            'uid' => session('admin.uid'),//aid后台管理员id，我这里没有登录，写死1，从session里面取出来即可
            'bid' => i('post.bid'),
            'cid' => i('post.cid'),
            'tid' => i('post.tid'),
            'total' => i('post.total'),
        ];
        //添加商品并获取新添加的商品id
        $gid = $this->add($data);

        //2.商品详情表的添加
        $goodsDetailPics = i('post.goodsDetailPics');
        $image = new \Think\Image();
        $small = '';
        $medium = '';
        $big = '';
        foreach ($goodsDetailPics as $k => $v) {
            if(!file_exists($v)){
                return ['status'=>'error','message'=>'请确保商品详情图片存在！'];
            }
            $image->open($v);

            $small_pic = dirname($v) . '/small_' . basename($v);
            $medium_pic = dirname($v) . '/medium_' . basename($v);
            $big_pic = dirname($v) . '/big_' . basename($v);
            $image->thumb(800, 800)->save($big_pic);
            $image->thumb(400, 400)->save($medium_pic);
            $image->thumb(80, 80)->save($small_pic);
            $small .= $small_pic . ',';
            $medium .= $medium_pic . ',';
            $big .= $big_pic . ',';
        }
        $data = [
            'small_pic' => rtrim($small, ','),
            'medium_pic' => rtrim($medium, ','),
            'big_pic' => rtrim($big, ','),
            'gid'=>$gid,
            'intro'=>i('post.intro'),
            'service'=>i('post.service')
        ];
        (new GoodsDetail())->add($data);
        //3.商品属性表的添加
        //属性类型数据的添加
        $attr_arr = i('post.attr');
        $goods_attr = new GoodsAttrModel();
        foreach($attr_arr as $k=>$v){
            //如果属性为空则跳过
            if(empty($v)) continue;
            $data = [
                'taid'=>$k,
                'gavalue'=>$v,
                'gid'=>$gid,
                'addva'=>''
            ];
            $goods_attr->add($data);
        }
        ////规格类型数据的添加
        $attr_spec = i('post.spec');
        foreach($attr_spec as $k=>$v){
            foreach($v['specAttr'] as $kk=>$vv){
                $data=[
                    'taid'=>$k,
                    'gavalue'=>$vv,
                    'gid'=>$gid,
                    'addva'=>$v['addValue'][$kk]
                ];
                $goods_attr->add($data);
            }
        }
        return ['status' => 'success', 'message' => '操作成功！'];
    }
    public function editGoods(){
        //商品表的验证
        if (!$this->create()) {
            return ['status' => 'error', 'message' => $this->getError()];
        }
        //商品详情表的验证
        if(!i('post.goodsDetailPics')){
            return ['status' => 'error', 'message' => '请上传商品图册！'];
        }
        $goodsDetail = (new GoodsDetail());
        if(!$goodsDetail->create()){
            return ['status' => 'error', 'message' => $goodsDetail->getError()];
        }
        //判断是否是顶级分类
        $cid = i('post.cid');
        $tid = (new CategoryModel())->where("cid=$cid")->getField('tid');
        if($tid == 0){
            return ['status'=>'error','message'=>'顶级分类不能添加商品！'];
        }
        //获取要修改的gid
        $gid = i('get.gid');
        //1.商品表修改
        $data = [
            'gid' => $gid,
            'gname' => i('post.gname'),
            'sname' => i('post.sname'),
            'gnumber' => i('post.gnumber'),
            'unit' => i('post.unit'),
            'marketprice' => i('post.marketprice'),
            'shopprice' => i('post.shopprice'),
            'pic' => i('post.pic'),
            'click' => i('post.click'),
            'time' => time(),
            'uid' => session('admin.uid'),//aid后台管理员id，我这里没有登录，写死1，从session里面取出来即可
            'bid' => i('post.bid'),
            'cid' => i('post.cid'),
            'tid' => i('post.tid'),
            'total' => i('post.total'),
        ];
        //修改商品
        $res = $this->save($data);
        //2.商品详情表的修改
        $goodsDetailPics = i('post.goodsDetailPics');
        $image = new \Think\Image();
        $small = '';
        $medium = '';
        $big = '';
        foreach ($goodsDetailPics as $k => $v) {
            if(!file_exists($v)){
                return ['status'=>'error','message'=>'请确保商品图册图片存在！！'];
            }
            $image->open($v);
            $basename = basename($v);
            $dirname = dirname($v);
            //判断是否存在big_前缀,如果是，则去掉前缀
            if(strrchr($basename,'_')){
                $basename = ltrim(strrchr($basename,'_'),'_');
                $v = $dirname.'/'.$basename;
            }
            $small_pic = dirname($v) . '/small_' . basename($v);
            $medium_pic = dirname($v) . '/medium_' . basename($v);
            $big_pic = dirname($v) . '/big_' . basename($v);
            $image->thumb(800, 800)->save($big_pic);
            $image->thumb(400, 400)->save($medium_pic);
            $image->thumb(80, 80)->save($small_pic);
            $small .= $small_pic . ',';
            $medium .= $medium_pic . ',';
            $big .= $big_pic . ',';
        }
        //获取要修改的商品详情表的id
        //$did = (new GoodsDetail())->where("gid ={$gid}")->getField('did');
        $did = (new GoodsDetail())->getFieldBygid($gid,'did');
        $data = [
            'did' => $did,
            'small_pic' => rtrim($small, ','),
            'medium_pic' => rtrim($medium, ','),
            'big_pic' => rtrim($big, ','),
            'gid'=>$gid,
            'intro'=>i('post.intro'),
            'service'=>i('post.service')
        ];

        (new GoodsDetail())->save($data);
        //3.商品属性表的修改（思路，先删除，再添加）
        //先删除该商品对应的所有商品属性
        $goods_attr = new GoodsAttrModel();
        //$gaids = $goods_attr->where("gid = {$gid}")->order('gaid asc')->getField('gaid',true);
        $goods_attr->where("gid = {$gid}")->delete();
        //商品属性表中属性数据的修改
        $attr_arr = i('post.attr');
        foreach($attr_arr as $k=>$v){
            //如果属性为空则跳过
            if(empty($v)) continue;
            $data = [
                'taid'=>$k,
                'gavalue'=>$v,
                'gid'=>$gid,
                'addva'=>''
            ];
            $goods_attr->add($data);
        }
        //商品属性表中规格数据的修改
        $attr_spec = i('post.spec');
        foreach($attr_spec as $k=>$v){
            foreach($v['specAttr'] as $kk=>$vv){
                $data=[
                    'taid'=>$k,
                    'gavalue'=>$vv,
                    'gid'=>$gid,
                    'addva'=>$v['addValue'][$kk]
                ];
                $goods_attr->add($data);
            }
        }
        return ['status' => 'success', 'message' => '操作成功！'];
    }
    public function del(){
        //获取要删除的商品id
        $gid = i('get.gid');
        //1.删除商品表中的数据
        $pic_path = (new GoodsModel())->getFieldBygid($gid,'pic');
        //删除商品列表展示图片
        if(file_exists($pic_path)){
            unlink($pic_path);
        }
        (new GoodsModel())->delete($gid);
        //2.删除商品详情表中的数据
        $detail_pic = (new GoodsDetail())->getFieldBygid($gid,'big_pic');
        //获取商品详情中所有大图
        $arr = explode(',',$detail_pic);
        $dirname = dirname($arr[0]);
        //根据大图数组来获取原图数组
        foreach($arr as $k=>$v){
            //将数组中的每一项big_*.jpg形式转化为*.jpg
            $arr[$k] = ltrim(strrchr(basename($v),'_'),'_');
        }
        //再根据原图数据将所有原图，小图，中图，大图删除
        foreach($arr as $k=>$v){
            $tmp_arr[] = $dirname.'/'.$v;
            $tmp_arr[] = $dirname.'/small_'.$v;
            $tmp_arr[] = $dirname.'/medium_'.$v;
            $tmp_arr[] = $dirname.'/big_'.$v;
            foreach($tmp_arr as $kk => $vv){
                if(file_exists($vv)){
                    unlink($vv);
                }
            }
        }
        (new GoodsDetail())->delete($gid);
        //3.删除商品属性表中的数据
        (new GoodsAttrModel())->where("gid = $gid")->delete();
        return ['status' => 'success', 'message' => '操作成功！'];
    }

}