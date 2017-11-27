<?php
namespace app\index\controller;
use \think\Db;
use \think\Loader;

class Video extends \think\Controller
{
    public $file_list = [];
    public $file_execl_list=[];
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

    public function video_list()
    {
        $cate_id = isset($_REQUEST['cat_id']) && $_REQUEST['cat_id'] > 0 ? intval($_REQUEST['cat_id']) : 1;
        $sort = isset($_REQUEST['sort']) && in_array($_REQUEST['sort'],array('new','view_num')) ? trim($_REQUEST['sort']) : 'new';
        $db = Db::table('video_list')->where(['cat_id' => $cate_id,'is_show'=>1]);
        if($sort == 'new'){
            $db->order('add_time desc');
        } else {
            $db->order('view_num desc');
        }

        $video_list = $db->paginate(12,false,['query'=> ['cat_id' => $cate_id]]);
        $video_info = Db::table('category')->where(['id'=>$cate_id])->find();
        $nav_list = Db::table('category')->select();

        $page = $video_list->render();

        $view_data = [];
        $view_data['video_list'] = $video_list;
        $view_data['video_info'] = $video_info;
        $view_data['nav_list']   = $nav_list;
        $view_data['page']       = $page;
        $view_data['total']      = $video_list->total;

        return $this->fetch('video_list',$view_data);
    }

    /**
     * 视频详情页面
     * @return mixed
     */
    public function video_info()
    {
        $video_info = Db::table('video_list')->where(['id'=>$_REQUEST['id']])->find();
        if($video_info){
            $view_num = $video_info['view_num'] + 1;
            Db::table('video_list')->where(['id'=>$video_info['id']])->update(['view_num'=>$view_num]);
        }

        $cate_info = Db::table('category')->where(['id'=>$video_info['cat_id']])->find();
        $nav_list = Db::table('category')->select();

        $view_data = [];
        $view_data['video_info']      = $video_info;
        $view_data['cate_info']      = $cate_info;
        $view_data['nav_list']   = $nav_list;
//        echo "<pre>";
//        print_r($video_info);
//        exit;

        return $this->fetch('video_info',$view_data);
    }

    public function updateVideo()
    {
        $this->video_file();
        $file_execl_list =$this->file_execl_list;
        echo "<pre>";
        print_r($file_execl_list);
        //exit;


        foreach($file_execl_list as $key => $row){
            //更新视频文件名和视频封面到图表中
            $temp = array();
            $temp['video_sn']   = '';
            $temp['theme']      = '';
            $temp['cat_id']     = 2;
            $temp['cat_name']   = '儿童绘画';
            $temp['second_cat_name']   = basename(basename($row));
            $temp['title']      = $key;
            $temp['face_img']   = str_replace(array('mp4','/var/www/thinkphp/public/static'),array('jpg',''),$row);
            $temp['video_url']  = str_replace('/var/www/thinkphp/public/static','',$row);
            $temp['description']= '';
            $temp['duration_seconds'] = 0;
            Db::table('video_list_new')->insert($temp);
        }
    }

    public function importData()
    {
        $file_name = "/www/test/thinkphp/购买资源整理目录.xlsx";

        Loader::import('PHPExcel.Classes.PHPExcel');
        Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');
        Loader::import('PHPExcel.Classes.PHPExcel.Reader.Excel5');
        $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
        $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
        $excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
        array_shift($excel_array);  //删除第一个数组(标题)

        $this->video_file();
        $file_execl_list =$this->file_execl_list;
        echo "<pre>";
//        print_r($file_execl_list);

        $exist_nums = 0;
        $video_list = array();
        foreach($excel_array as $row){
            $temp = array();
            $temp['video_sn']   = $row[0];
            $temp['theme']      = $row[1];
            $temp['cat_id']     = 0;
            $temp['cat_name']   = $row[6];
            $temp['title']      = $row[2];
            $temp['face_img']   = '';
            $temp['video_url']  = '';
            $temp['description']= empty($row[12]) ? '' : $row[12];
            $temp['duration_seconds'] = intval($row[10])*60 + intval($row[11]);
            if(isset($file_execl_list[$temp['title']])){
                echo "<br>{$temp['title']}：已存在";
                $temp['video_url']  = str_replace('/www/test/thinkphp/public','',$this->file_execl_list[$temp['title']]);
                $temp['face_img']   = str_replace(array('vedio','mp4'),array('vedio_face','jpg'),$temp['video_url']);
                $exist_nums ++;
                unset($this->file_execl_list[$temp['title']]);
            }


            Db::table('video_list')->insert($temp);
            $video_list[] = $temp;
        }
        echo "<br>已存在文件".$exist_nums."个<br>";
        print_r($this->file_execl_list);
        //print_r($video_list);
        exit;
    }

    public function video_file()
    {
        //$video_path="/www/test/thinkphp/public/static/vedio/儿童绘画";
        $video_path="/var/www/thinkphp/public/static/vedio/儿童绘画";
        $this->getfiles($video_path);
        //echo "<pre>";
        //print_r($this->file_list);
        $mp4_list = array();
        foreach($this->file_list as $row){
            $file_info = pathinfo($row);
            if($file_info['extension'] != 'mp4'){
                continue;
            }
            $file_name = str_replace(array('_batch',".","1","2","3","4","5","6","7","8","9","0",'（','）','《','》','第集'),array('','','','','','','','','','','','','','','','',''),$file_info['filename']);
            $mp4_list[$file_name] = $row;
        }

        $this->file_execl_list = $mp4_list;
//       return $mp4_list;
    }

    public function getfiles($path){
        foreach(glob($path) as $afile){
            if(is_dir($afile)){
                $this->getfiles($afile.'/*');
            } else {
                $this->file_list[] = $afile;
                //echo $afile.'<br />';
            }
        }
    }




}
