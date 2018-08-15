<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/5/28
 * Time: 13:53
 */
namespace Home\Controller;

use Think\Controller;

class TestController extends Controller{

    public function di(){
        $model = D('test');
        var_dump($model);
    }

    public function index(){
        $model = D('CompKol');
        $model->compKol('5779037727');
    }

    public function excel(){
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        header('Content-Type:text/html;charset=utf8');
        $data = S('MEI_ZHANG_DATA');
        if(empty($data)){
            $data = importExcel('meiz.xlsx');
            echo '<pre>';
            print_r($data);
        }
    }

}