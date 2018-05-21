<?php
/**
 * Created by PhpStorm.
 * User: yangfeilong
 * Date: 2018/4/24
 * Time: 15:16
 */

namespace Admin\Controller;

class IndexController extends AdminController {
    public function index(){
        if(UID){
            $this->meta_title = '管理首页';
            $this->display();
        } else {
            $this->redirect('Public/login');
        }
    }

    public function main(){
        $this->display();
    }

}