<?php
namespace Admin\Controller;
use Think\Controller;

class HeadsController extends Controller {
    protected function _initialize(){
        header("Content-type:text/html;charset=utf-8");
        header('Access-Control-Allow-Origin:*');
    }
    public function index(){
//        $this->display();
    }
    
}
