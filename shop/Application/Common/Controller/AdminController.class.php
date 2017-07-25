<?php 
	namespace Common\Controller;
	use Think\Auth;

    class AdminController extends BaseController{
		public function __construct(){
			parent::__construct();
			//判断是否登录
            if(!session('admin.uid')){
                $this->error('请先登录！',u('admin/user/login'));
            }
            //检测权限
            //$auth = new Auth();
            //$rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;;
            //$result = $auth->check($rule,$_SESSION['admin']['uid']);
            //if(!$result){
            //    $this->error('没有操作权限');exit;
            //}
			//$this->assignModule();
		}
		//上传
        public function upload($module) {
            //实例化上传类
            $upload = new \Think\Upload();
            // 设置附件上传大小
            $upload->maxSize = 3145728;
            // 设置附件上传类型
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
            // 设置附件上传目录
            $upload->savePath = '/'.$module.'/';
            // 上传文件
            $info = $upload->upload();
            if (!$info) {
                // 上传错误提示错误信息
                $this->error($upload->getError());die;
            } else {
                // 上传成功
                return current($info);
            }
        }
	}
 ?>