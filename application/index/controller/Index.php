<?php
namespace app\index\controller;

use \think\Db;

class Index extends \think\Controller
{

    public function index()
    {
        return $this->fetch();
    }
    

    public function test()
    {
        $res = Db::table('act_activity')->where('id',1)->find();
        echo "<pre>";
        print_r($res);
        exit;
	    return "test index";
    }


}
