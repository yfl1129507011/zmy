<?php
namespace Home\Controller;

class IndexController extends HomeController
{
    public function index()
    {
        $this->display('index');
    }

    public function home(){
        $this->display();
    }
}