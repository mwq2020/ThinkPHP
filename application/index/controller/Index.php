<?php
namespace app\index\controller;

use \think\Db;

class Index extends \think\Controller
{

    public function index()
    {
        $cat_list = Db::table('category')->select();

        foreach($cat_list as &$row){
            $row['video_list'] = Db::table('video_list')->where(['cat_id'=>$row['id']])->limit(0,2)->select();
        }

//        echo "<pre>";
//        print_r($cat_list);
//        exit;

        return $this->fetch('index',['cat_list'=>$cat_list]);
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
