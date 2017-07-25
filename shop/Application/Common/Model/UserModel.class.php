<?php


namespace Common\Model;


class UserModel extends BaseModel {
    protected $pk = 'id';
    protected $tableName = 'admin_user';
    protected $_validate = [
        ['username', 'require', '登录账号不能为空！'],
        ['password', 'require', '密码不能为空！'],
    ];

    public function isLogin() {
        //验证表单数据是否为空
        if (!$this->create()) {
            return false;
        }
        //先验证验证码是否正确
        if (!check_verify(I('post.code'))) {
            $this->error = "验证码不正确!";
            return false;
        }
        //获取登录信息
        $username = I('post.username');
        //$password = md5(md5(I('post.password')));
        $password = md5(I('post.password'));
        //print_r($_POST);
        //定义判断条件数组
        $condition = [
            'username' => $username,
            'password' => $password
        ];

        //查询数据
        $userData = $this->where($condition)->find();
        //判断是否能找到对应用户
        if (!$userData) {
            $this->error = "用户名或密码不正确!";
            return false;
        }
        //更新用户数据
        $userData['login_ip'] = get_client_ip();
        $userData['login_time'] = time();
        $this->save($userData);
        //添加session
        session('admin.uid',$userData['uid']);
        session('admin.username',$userData['nickname']);

        return true;
    }
}