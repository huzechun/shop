<?php 
	namespace Common\Controller;
	use Think\Controller;
	class BaseController extends Controller{
		public function __construct(){
			parent::__construct();
			if(method_exists($this, '__init')){
				$this->__init();
			}
		}
		public function store(Model $model,$data,\Closure $callback=null){
			$res = $model->store($data);
			if($res['status'] == 'success' && $callback instanceof \Closure){
				$callback($res);
			}
			$this->message($res);
		}
		public function message($res){
			if($res['status'] == 'success'){
				$this->success($res['message']);
				die;
			}else{
				$this->error($res['message']);
				die;
			}
		}
	}
 ?>