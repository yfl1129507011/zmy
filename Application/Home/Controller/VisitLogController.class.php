<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/5/7
 * Time: 16:41
 */
namespace Home\Controller;

use Think\Controller;

class VisitLogController extends Controller {

    protected function _initialize(){
        $this->visit_uid = $this->addUserInfo();
    }

    /**
     * 记录用户访问信息
     */
    public function addUserInfo(){
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