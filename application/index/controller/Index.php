<?php
namespace app\index\controller;

use \think\Db;

class Index extends \think\Controller
{

    /**
     * 视频网站首页入口
     * @return mixed
     */
    public function index()
    {
        $cat_list = Db::table('category')->select();

        foreach($cat_list as &$row){
            $row['video_list'] = Db::table('video_list')->where(['cat_id'=>$row['id'],'is_show'=>1])->limit(0,2)->select();
        }

        $ranking_list = Db::table('video_list')->order('view_num desc')->where(['is_show'=>1])->limit(0,8)->select();
        $special_list = Db::table('video_list')->order('view_num desc')->where(['is_show'=>1])->limit(0,4)->select();
        $recommend = Db::table('video_list')->where(['is_show'=>1])->order('view_num desc')->find();
        $nav_list = Db::table('category')->select();

        $view_data = [];
        $view_data['cat_list']      = $cat_list;
        $view_data['nav_list']   = $nav_list;
        $view_data['ranking_list']  = $ranking_list;
        $view_data['special_list']  = $special_list;
        $view_data['recommend']     = $recommend;

        return $this->fetch('index',$view_data);
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
