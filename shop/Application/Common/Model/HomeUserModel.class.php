<?php


namespace Common\Model;


class HomeUserModel extends BaseModel {
    protected $pk = 'uid';
    protected $tableName = 'user';
    protected $_validate = [
        ['username','require','用户名不能为空！'],
        ['password','require','密码不能为空！'],
        ['repassword','require','确认密码不能为空！'],
        ['password','repassword','确认密码不正确',0,'confirm'],
    ];
    public function reg(){
        //验证验证码是否正确
        if(!check_verify(i("post.code"))){
            $this->error="验证码不正确！";
            return false;
        }
        //验证表单
        if(!$this->create()){
            return false;
        }
        //验证用户是否已经存在
        $username = $_POST['username'];
        $res = (new HomeUserModel())->where("username = '{$username}'")->find();
        if($res){
            $this->error = '用户名已存在！';
            return false;
        }
        //加密密码
        $this->data['password'] = md5(md5(i('post.password')));
        $this->add();
        return true;
    }
    public function login(){
        $this->_validate = [
            ['username','require','用户名不能为空！'],
            ['password','require','密码不能为空！'],
        ];
        //验证验证码是否正确
        if(!check_verify(i("post.code"))){
            $this->error="验证码不正确！";
            return false;
        }
        //验证表单
        if(!$this->create()){
            return false;
        }
        //验证用户名是否存在
        $where = [
            'username' => I('post.username'),
            'password' => md5(md5(I('post.password')))
        ];
        $userData = $this->where($where)->find();
        if(!$userData){
            $this->error = '用户名或密码错误！';
            return false;
        }
        //更新数据
        $userData['login_ip'] = get_client_ip();
        $userData['login_time'] = time();
        $this->save($userData);
        //添加session
        session('home.uid',$userData['uid']);
        session('home.username',$userData['username']);
        return true;
    }
}