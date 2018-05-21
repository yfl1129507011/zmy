<?php
/**
 * Created by PhpStorm.
 * User: yangfeilong
 * Date: 2018/4/24
 * Time: 15:16
 */

namespace Admin\Controller;

/**
 * 后台首页控制器
 */
class PublicController extends \Think\Controller {

    /**
     * 后台用户登录
     */
    public function login($username = null, $password = null, $verify = null){
        if(IS_POST){
            /* 检测验证码 TODO: */
            if(!check_verify($verify)){
                $this->error('验证码输入错误！');
            }

            $uid = $this->autoLogin($username, $password, $nickname);
            if($uid > 0){ //登录成功
                /* 记录登录SESSION和COOKIES */
                $auth = array(
                    'uid'             => $uid,
                    'username'        => $nickname,
                );
                session('user_auth', $auth);
                session('user_auth_sign', data_auth_sign($auth));

                $this->success('登录成功！', U('Index/index'));
            } else { //登录失败
                switch($uid) {
                    case 0: $error = '用户名或密码不能为空';break;
                    case -1: $error = '用户名错误！'; break;
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break;
                }
                $this->error($error);
            }
        } else {
            if(checkLogin()){
                $this->redirect('Index/index');
            }else{
                $this->display();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(checkLogin()){
            session('user_auth', null);
            session('user_auth_sign', null);
            $this->redirect('login');
        } else {
            $this->redirect('login');
        }
    }

    public function verify(){
        $config = array(
            'fontSize'  =>  18,
	        'imageH'    =>  33,              // 验证码图片高度
	        'imageW'    =>  118,              // 验证码图片宽度
	        'length'    =>  4,               // 验证码位数
            'fontttf'   =>  '2.ttf',
		);
        $verify = new \Think\Verify($config);
        $verify->entry(1);
    }

    private function autoLogin($username = null, $password = null, &$nickname = null){
        if(empty($username) || empty($password)) return 0;
        $adminCfg = C('ADMIN_INFO');
        $username = strtoupper($username);
        if(array_key_exists($username, $adminCfg)){
            if($password == $adminCfg[$username]['PASSWORD']){
                $nickname = $adminCfg[$username]['NICKNAME'];
                return $adminCfg[$username]['UID'];
            }
            return -2;
        }

        return -1;
    }

}
