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
        $cate_id = isset($_REQUEST['cat_id']) && $_REQUEST['cat_id'] > 0 ? intval($_REQUEST['cat_id']) : 1;
        $sort = isset($_REQUEST['sort']) && in_array($_REQUEST['sort'],array('new','view_num')) ? trim($_REQUEST['sort']) : 'new';
        $db = Db::table('video_list')->where(['cat_id' => $cate_id]);
        if($sort == 'new'){
            $db->order('add_time desc');
        } else {
            $db->order('view_num desc');
        }

        $video_list = $db->paginate(12);

        $page = $video_list->render();

        $view_data = [];
        $view_data['video_list'] = $video_list;
        $view_data['page']      = $page;
        $view_data['total']      = $video_list->total;
//        echo "<pre>";
//        print_r($view_data);exit;

        return $this->fetch('cate',$view_data);
    }

    /**
     * 视频详情页面
     * @return mixed
     */
    public function info()
    {
        $video_info = Db::table('video_list')->where(['id'=>$_REQUEST['id']])->find();
        if($video_info){
            $view_num = $video_info['view_num'] + 1;
            Db::table('video_list')->where(['id'=>$video_info['id']])->update(['view_num'=>$view_num]);
        }

        $cate_info = Db::table('category')->where(['id'=>$video_info['cat_id']])->find();

        $view_data = [];
        $view_data['video_info']      = $video_info;
        $view_data['cate_info']      = $cate_info;

        return $this->fetch('info',$view_data);
    }
}
