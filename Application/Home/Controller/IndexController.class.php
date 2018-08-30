<?php
namespace Home\Controller;

class IndexController extends HomeController
{
    public function index()
    {
        $arr = array(9,4,6,1,5,7);
        var_dump(binary_search($arr, 0, count($arr)-1, 6));
        var_dump(b_array_search($arr, 6));
        die;

        $this->display('index');
    }

    public function home(){
        $this->display();
    }
}