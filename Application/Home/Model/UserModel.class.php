<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/5/18
 * Time: 15:43
 */
namespace Home\Model;

use Think\Model;

class UserModel extends Model{
    public $error = '';
    /* 用户模型自动验证 */
    protected $_validate = array(
        //验证用户名
        array('nickname', '1,30', '用户名长度不合法', self::EXISTS_VALIDATE, 'length'),

        /* 验证密码 */
        array('password', '6,30', '密码长度必须在6-30个字符之间！', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
    );

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('password', 'md5_sha1', self::MODEL_INSERT, 'function'),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('update_time', NOW_TIME),
        array('status', 1),
    );


    public function login($username, $password){
        if(empty($username) || empty($password)) {
            $this->error = '未知错误';return false;
        }
        $where['phone'] = $username;
        $info = $this->where($where)->find();
        if(empty($info)){
            $this->error = '账号不存在';return false;
        }
        if($info['status'] != 1){
            $this->error = '用户未激活或已禁用！';return false;
        }

        if($info['password'] != md5_sha1($password)){
            $this->error = '密码错误';return false;
        }

        return self::autoLogin($info);
    }

    public function register($data){
        if(empty($data)) return false;
        if(!isMobile($data['phone'])) {
            return -9;
        }

        $where['phone'] = $data['phone'];
        $info = $this->where($where)->find();
        if($info){
            return -11;
        }

        if($this->create($data)){
            $data['uid'] = $this->add();
            if(empty($data['uid'])) return false;
            self::autoLogin($data);
            return $data['uid'];
        }else{
            return $this->getError();
        }
    }

    public function checkNick($nickname){
        if(empty($nickname)) return false;

        $res = $this->where(array('nickname'=>$nickname))->find();

        return $res ? true : false;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth_zm', null);
        session('user_auth_sign_zm', null);
    }


    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     * @return bool
     */
    private function autoLogin($user){
        if(empty($user)) return false;
        /* 更新登录信息 */
        $data = array(
            'uid'             => $user['uid'],
            'login_num'           => array('exp', '`login_num`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'nickname'        => $user['nickname'],
        );

        session('user_auth_zm', $auth);
        session('user_auth_sign_zm', data_auth_sign($auth));
        return true;
    }

}