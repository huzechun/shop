<?php


namespace Admin\Controller;


use Common\Controller\AdminController;
use Common\Model\BrandModel;

class BrandController extends AdminController {
    public function handler() {
        $bid = i('get.bid');
        if (IS_POST) {
            //获取post数据
            $data = i('post.');
            if ($bid) {
                //编辑操作
                $data['bid'] = $bid;
                if ($_FILES['logo']['name']) {
                    //执行上传并获取上传信息，上传到Uploads中的logo文件夹下面
                    $arr = $this->upload('logo');
                    //获取上传文件的路径
                    $filepath = 'Uploads' . $arr['savepath'] . $arr['savename'];
                    $data['logo'] = $filepath;
                    //然后需要删除原来的图片
                    $oldPic = (new BrandModel())->where('bid=' . $bid)->getField('logo');
                    if (file_exists($oldPic)) {
                        unlink($oldPic);
                    }
                }
            } else {
                //添加操作
                //执行上传获取上传信息,上传到Uploads中的logo文件夹下面
                $arr = $this->upload('logo');
                //获取上传文件的路径
                $filepath = 'Uploads' . $arr['savepath'] . $arr['savename'];
                //添加logo图片路径到$data
                $data['logo'] = $filepath;
            }
            //执行添加或编辑
            $this->store(new BrandModel(), $data, function () {
                //执行回调
                $this->success('操作成功！', u('lists'));
                die;
            });
        }
        if ($bid) {
            //如果是编辑操作则获取旧数据
            $oldData = (new BrandModel())->where('bid=' . $bid)->find();
            $this->assign('oldData', $oldData);
        }
        //获取所有品牌数据
        $this->display();
    }

    public function lists() {
        $m = M('brand');
        $count = $m->count();
        $p = getpage($count, 4);
        $list = $m->field(true)->order('sort asc')->limit($p->firstRow, $p->listRows)->select();
        $this->assign('brand', $list); // 赋值数据集
        $this->assign('page', $p->show()); // 赋值分页输出
        $this->display();
    }

    public function del() {
        //获取bid
        $bid = i('get.bid');
        $brandModel = new BrandModel();
        //获取图片路径
        $picPath = $brandModel->where('bid=' . $bid)->getField('logo');
        //删除文件中的数据
        unlink($picPath);
        //再删除数据库中的数据
        if ($brandModel->where('bid =' . $bid)->delete()) {
            $this->success('操作成功！', u('lists'));
            die;
        } else {
            $this->error('操作失败！', u('lists'));
            die;
        }
    }
}