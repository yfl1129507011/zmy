<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/5/8
 * Time: 11:50
 */
namespace Home\Controller;

use Think\Controller;

class VisitorController extends Controller{

    /**
     * @param bool|false $visit_uid
     * @return bool
     * 前端页面使用方式
     * <script>
     *     var _visit_uid = '{$visit_uid}';
     *     var _self = '{:U("Visitor/index")}';
     * </script>
     * <script src="__STATIC__/js/visitLog.js"></script>
     */
    public function index($visit_uid = false){
        $visit_uid = remove_xss($visit_uid);
        if(empty($visit_uid)) return false;

        $where = array('unique_id'=>$visit_uid);
        $save = array('line_time'=>NOW_TIME);
        M('user_visit_log')->where($where)->save($save);
    }
}