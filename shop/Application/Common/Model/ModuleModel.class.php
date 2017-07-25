<?php 
	namespace Common\Model;
	class ModuleModel extends BaseModel{
		protected $pk = 'id';
		protected $tableName = 'module';
		protected $_validate = [
			['title','require','请设置模块标题'],
			['name','require','请设置模块标识'],
			['actions','require','请设置模块动作'],
		];
	}
 ?>