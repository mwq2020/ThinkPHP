<?php
namespace app\index\controller;
use \think\Db;

class Video extends \think\Controller
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 视频分类入口
     * @return mixed
     */
    public function cate()
    {
        $video_list = Db::table('video_list')->where(['cat_id'=>$_REQUEST['cat_id']])->page('1,10')->select();

        $view_data = [];
        $view_data['video_list']      = $video_list;

        return $this->fetch('cate',$view_data);
    }

    /**
     * 视频详情页面
     * @return mixed
     */
    public function info()
    {
        $video_info = Db::table('video_list')->where(['id'=>$_REQUEST['id']])->find();
        $cate_info = Db::table('category')->where(['id'=>$video_info['cat_id']])->find();

        $view_data = [];
        $view_data['video_info']      = $video_info;
        $view_data['cate_info']      = $cate_info;
        
        return $this->fetch('info',$view_data);
    }
}
