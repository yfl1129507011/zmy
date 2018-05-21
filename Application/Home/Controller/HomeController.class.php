<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/5/18
 * Time: 15:28
 */
namespace Home\Controller;

use Think\Controller;

class HomeController extends Controller{

    // 是否添加用户访问日志
    protected $is_visit_log = true;

    /* 用于输出404页面 */
    public function _empty(){
        $this->error('页面不存在！', U('Index/index'));
    }

    protected function _initialize(){
        $uid = is_login();
        if(!$uid){
            $jumpUrl = is_ssl()?'https://':'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header('Location: ' . U('User/login').'?back='.urlencode($jumpUrl));exit;
        }
        define('UID', $uid);
        if($this->is_visit_log){
            $this->addUserInfo();
        }
    }

    /**
     * 记录用户访问信息
     */
    protected function addUserInfo(){
        $info = array();
        $info['sid'] = session_id();  // 用户session_id标识
        $info['request_uri'] = __SELF__;
        $info['client_ip'] = get_client_ip(0,true);
        $info['city'] = taobaoIP($info['client_ip']);
        $info['device'] = getDevice();
        $info['ua'] = $_SERVER['HTTP_USER_AGENT'];
        $info['visit_time'] = NOW_TIME;
        $info['unique_id'] = md5($info['sid'].$info['request_uri'].time());
        M('user_visit_log')->add($info);

        return $info['unique_id'];
    }
}