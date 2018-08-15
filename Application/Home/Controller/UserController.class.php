<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/5/18
 * Time: 15:25
 */
namespace Home\Controller;

use Think\Controller;

class UserController extends Controller{

    public function _initialize()
    {
        # 定义网站title
        $this->title = '众上数字极品';
    }

    public function login(){
        $back = I('back');
        if(IS_POST){
            //登录
            $username 	= trim(I('post.phone', ''));		//账号
            $password 	= trim(I('post.password', ''));		//密码
            //不能为空
            if (empty($username)) $this->error('账号不能为空！');
            //密码不能为空
            if (empty($password)) $this->error('密码不能为空！');
            //密码长度
            $len = strlen($password);
            if ($len < 6 || $len > 30) $this->error(self::showRegError(-4));
            $userModel = D('User');
            $res = $userModel->login($username, $password);
            if($res){
                $jumpUrl = checkJumpUrl($back);
                if(!$jumpUrl) $jumpUrl = U('Index/index');
                $this->success('登录成功！', $jumpUrl);
            }else{
                $this->error($userModel->error);
            }
        }else{
            if (is_login()) redirect(U('Index/index'));
            $this->assign('back',remove_xss($back));
            $this->display('login');
        }
    }

    public function register(){
        if(IS_POST){
            $data = I('post.');
            /* 检测验证码 */
            /*if(!check_verify($data['code'])){
                $this->error('验证码输入错误！');
            }*/
            /*if($data['password'] != $data['repassword']){
                $this->error('密码和重复密码不一致！');
            }*/
            $uid = D('User')->register($data);
            if($uid>0){
                $this->success('注册成功！', U('Index/index'));
            }else{
                if(is_numeric($uid)) {
                    $this->error(self::showRegError($uid));
                }else{
                    $this->error($uid);
                }
            }
        }else {
            if (is_login()) redirect(U('Index/index'));
            $this->display('register');
        }
    }

    public function logout() {
        if(is_login()){
            D('User')->logout();
        }
        $this->redirect('Index/index');
    }

    public function verify(){
        $config = array(
            'fontSize'  =>  20,
            'imageH'    =>  40,              // 验证码图片高度
            'imageW'    =>  140,              // 验证码图片宽度
            'length'    =>  4,               // 验证码位数
            'fontttf'   =>  '2.ttf',
        );
        $verify = new \Think\Verify($config);
        $verify->entry(1);
    }

    public function getCode(){
        $this->ajaxReturn(array('status'=>200));
    }

    public function checkNick(){
        $nickname = remove_xss(I('post.nickname'));
        $data = array();
        $data['status'] = 200;
        if(D('User')->checkNick($nickname)){
            $data['status'] = 404;
            $data['info'] = '昵称已被占用';
        }

        $this->ajaxReturn($data);
    }

    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:  $error = '用户名长度必须在16个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            case -12: $error = '账号必须是手机号或邮箱格式！'; break;
            default:  $error = '未知错误';
        }
        return $error;
    }
}