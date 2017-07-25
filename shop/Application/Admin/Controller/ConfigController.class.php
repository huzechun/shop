<?php
	namespace Admin\Controller;
	use Common\Model\ConfigModel;
	use Common\Controller\AdminController;
	class ConfigController extends AdminController{
		public function set(){
			if(IS_POST){
				$this->store(new ConfigModel,i('post.'));
			}
			// 获取旧数据
			$field = m('config')->find(1);
			$this->assign('field',$field);
			$this->display();
		}
	}
 ?>
