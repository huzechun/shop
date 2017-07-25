<?php


namespace Common\Model;


use Org\Util\Data;

class CategoryModel extends BaseModel {
    protected $pk = 'cid';
    protected $tableName = 'category';
    protected $_validate = [
        ['cname', 'require', '请设置分类名称！'],
    ];

    public function getAll() {
        return (new Data())->tree($this->order('sort asc')->select(), 'cname');
    }

    public function del() {
        $cid = i('get.cid');
        //获取要删除的cid的pid
        $pid = $this->where('cid=' . $cid)->getField('pid');
        //将该分类的所有子分类的pid改为该分类的pid
        $this->where('pid=' . $cid)->save(['pid' => $pid]);
        //删除该分类
        $re = $this->where('cid=' . $cid)->delete();
        if ($re) {
            return true;
        } else {
            return false;
        }
    }

    public function getCateData($cid) {
        //获取cid下的所有子cid
        $cids = $this->getSon($cid,$this->select());
        //在子cid集合中加上自己
        $cids[] = $cid;
        //获取除了自己和子cid的所有分类
        $map['cid']  = array('not in',$cids);
        return $this->where($map)->select();

    }

    public function getSon($cid, $data) {
        static $temp = [];
        foreach ($data as $k => $v) {
            if ($cid == $v['pid']) {
                $temp[] = $v['cid'];
                $this->getSon($v['cid'], $data);
            }
        }
        return $temp;
    }
}