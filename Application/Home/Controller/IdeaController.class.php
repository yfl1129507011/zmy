<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/8/16
 * Time: 11:06
 */
namespace Home\Controller;

class IdeaController extends HomeController{

    public function hot_analyze(){
        $this->assign('_menu_list', array('爆品提案','爆品分析'));
        $this->display('product_list');
    }

    public function material_knowledge(){
        $this->assign('_menu_list', array('新原料产品提案','原料知识'));
        $this->display('knowledge');
    }
    public function material_analyze(){
        $this->assign('_menu_list', array('新原料产品提案','产品分析'));
        $this->display('product_info');
    }

    public function replace_from(){
        $this->assign('_menu_list', array('替换产品提案','待替换产品选择'));
        $this->display('product_list');
    }
    public function replace_to(){
        $this->assign('_menu_list', array('替换产品提案','替换产品选择'));
        $this->display('product_list');
    }

    public function user_insight(){
        $by = intval(I('get.by'));
        $menuList = array('用户需求特征提案','新原料产品提案','爆品提案','替换产品提案');
        $this->assign('_menu_name', $menuList[$by]);
        $this->display();
    }
}