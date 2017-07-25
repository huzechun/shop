<?php


namespace Admin\Controller;


use Common\Controller\AdminController;

class CommonController extends AdminController {
    public function sort(){
        //获取排序数组
        $arr = i('post.');
        //获取表名
        $tableName = $arr['table'];
        //获取主键名
        $pk = m($tableName)->getPk();
        //更新每一条数据的sort值
        foreach ($arr['sort'] as $k=>$v){
            m($tableName)->where($pk.'='.$k)->save(['sort'=>$v]);
        }
        //成功重定向lists方法
        $this->redirect('admin/'.$tableName.'/lists');
    }
}