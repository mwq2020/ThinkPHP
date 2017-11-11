<?php
namespace app\index\controller;
use \think\Db;

class Video extends \think\Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function cate()
    {
        $video_list = Db::table('video_list')->where(['cat_id'=>$_REQUEST['cat_id']])->page('1,10')->select();

        return $this->fetch('cate',['video_list' => $video_list]);
    }

    public function info()
    {
        $video_info = Db::table('video_list')->where(['id'=>$_REQUEST['id']])->find();
        return $this->fetch('info',['video_info' => $video_info]);
    }
}
