<?php 
	namespace Common\Behaviors;
	use wechat\WeChat;
	use Think\Behavior;
	// 加载公共函数文件
	require 'Application/Common/Common/helper.php';
	class AppBeginBehavior extends Behavior{
		public function run(&$param){
			// 加载系统配置
			$this->loadConfig();
		}
		public function loadConfig(){
			$config = m('config')->find(1);
			$system = json_decode($config['web_system'],true);
			v('config.system',$system);
		}
	}
 ?>