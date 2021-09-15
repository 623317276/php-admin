<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\portal\model\UserModel;
use cmf\controller\HomeBaseController;
use think\Db;
use cmf\controller\AdminBaseController;
use newz\perl;

class IndexController extends HomeBaseController
{
    public function _initialize()
    {
        $user = session('userinfo');
        $utoken = session('token');
        $yrnum = session("yenum");
        

        if (empty($user)) {
            $this->redirect('Login/login');

        } else {
            $fusers = Db::name('user')->where('id', $user['id'])->find();
            if ($utoken == $fusers['utoken']) {

            } else {
                session_unset();
                session_destroy();
                
            }
            
             
        }


    }
    //修改钱包页面
    public function editwallet()
    {
        $user = session('userinfo');
        $userinfo = Db::name('user')->where('id', $user['id'])->find();
        $this->assign('userinfo',$userinfo);
        return $this->fetch();
    } 
    //充值页面
    public function czhi()
    {
        $user = session('userinfo');

        $url = "http://pff.tffc.ltd/portal/login/register?zcr=" . $user['reference'];
        $this->assign('url', $url);
        $this->assign('user', $user);
        return $this->fetch();
    } 
    //充值明细
    public function czhilist()
    {
        $user = session('userinfo');
        $lists= Db::name('xwithdrawal')->where('memo',$user['id'])->order('id desc')->paginate(10);
        //dump($list->toArray(),1,'<pre>',0);
        $liste = $lists->toArray();
        if($liste['data']){
            $this->assign('type', 1);
        }else{
            $this->assign('type', 0);
        }
        $this->assign('lists', $liste['data']);
        return $this->fetch();
    } 
    public function index()
    {   

    	// $yrnum = session("yenum");
     //    $user = session('userinfo');
     //    $userinfo = Db::name('user')->where('id', $user['id'])->find();
        
        return $this->fetch();
    } 

        public function lfx_adver_fabu()
    {   

        // $yrnum = session("yenum");
     //    $user = session('userinfo');
     //    $userinfo = Db::name('user')->where('id', $user['id'])->find();
        
        return $this->fetch();
    }
        public function Pcenter()
    {   

        // $userinfo = Db::name('user')->where('id', $user['id'])->find();  
        return $this->fetch();
    }
            public function charts()
    {   

        // $userinfo = Db::name('user')->where('id', $user['id'])->find();  
        return $this->fetch();
    }
            public function tables()
    {   
        $user = session('userinfo');
        $userinfo = Db::name('user')->where('id', $user['id'])->find(); 
        $tflist =  Db::name('touf')->where('uid', $user['id'])->select()->toArray(); 
        $this->assign('tflist',$tflist);
        return $this->fetch();
    }

    public function fabu(){
        $user = session('userinfo');
        $result = $this->request->param();

        $lei = new perl();
        //视频
        if($_FILES['video']['name']){

        if(isset($_FILES['video']['name']) && !empty($_FILES['video']['name']) ){
                $videos = $lei->videoUpload('video');

        }else{
                return $this->assign('errorTips', '分类不能为空');
        }
        }else{
          
           
        }
        //图片
        if($_FILES['pic']['name']){

        if(isset($_FILES['pic']['name']) && !empty($_FILES['pic']['name']) ){
                $zphoto = $lei->imgUpload('pic');

        }else{
                return $this->assign('errorTips', '分类不能为空');
        }
        }else{
    
           
        }
        $jwid = $result['jwid'];
        $jwid = explode(',',$jwid);
        $address = $result['ssq'];
        foreach ($jwid as $key => $value) { 
           $arrt = array(
            'uid'=>$user['id'],
            'address'=>$address,
            'jwid'=>$value,
            'pic'=>'/pic/'.$zphoto,
            'video'=>'/video/'.$videos,
            'time'=>time()
            );
           $result = Db::name('touf')->insert($arrt);   
        }
        
        
        if($result){
            $this->success('操作成功!',url('/portal/index/lfx_adver_fabu'));
        }else{
            $this->error('网络错误请稍后重试!');
        }

    }
    public function jixing(){
         $result = $this->request->param();
         if($result['vals'] == 1){
            $where = [];
         }else{
            $keyword = $result['vals'];
            $keyword = explode(' ',$keyword);
            $keyword = implode(',',$keyword);
            $where['address']    = ['like', "%$keyword%"];
         }
         $jxinfo = Db::name('jwinfo')->where($where)->select()->toArray();
         echo json_encode($jxinfo);
         exit;

    }
}
