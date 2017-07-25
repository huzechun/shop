<?php 
	namespace Common\Model;
	use Think\Model;
	class BaseModel extends Model{
		public function store($data){
			if($this->create($data)){
				$action = isset($data[$this->pk])?'save':'add';
				$res = $this->$action($data);
				return ['status'=>'success','message'=>'操作成功！','data'=>$res];
			}else{
				return ['status'=>'error','message'=>$this->getError()];
			}
		}
	}
 ?>