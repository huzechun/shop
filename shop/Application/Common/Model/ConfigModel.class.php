<?php 
	namespace Common\Model;
	use Think\Model;
	class ConfigModel extends BaseModel{
		protected $pk = 'id';
		protected $tableName = 'config';
		protected $_validate = [
			['web_system','require','请设置网站配置'],
		];
		public function store($data){
			$data[$this->pk] = 1;
			return parent::store($data);
		}
	}
 ?>