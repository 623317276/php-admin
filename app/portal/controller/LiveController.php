<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11
 * Time: 14:48
 */

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
use app\portal\model\VideoClassModel;
use app\portal\model\VideoCollectModel;
use app\portal\model\VideoLevelModel;
use app\portal\model\VideoUserLevelModel;
use cmf\controller\HomeBaseController;
use app\portal\model\VideoModel;
use think\Db;
use think\Request;

class LiveController extends HomeBaseController
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     * 視頻显示列表
     */
  public function videos(Request $request){

//      $classList = VideoClassModel::where('status','=',1)->select();
//      $param = $request->param();
//      $classId = $param['calss_id'] ?? 1;
//      $page = $param['page'] ?? 1;
//      $size = $param['size'] ?? 20;
//
//      $where = [];
//      $where['class_id'] = $classId;
//      $videos = VideoModel::getLoveByVideos($where, $paginate = true, $page, $size);
////      dump($videos);die;
//      $this->assign('videos',$videos);
//      $this->assign('classList',$classList);
      return $this->fetch();
  }

  public function ajaxVideos(Request $request){
      $param = $request->param();
      $classId = $param['class_id'] ?? 3;
      $where['class_id'] = $classId;
      $page = $param['page'] ?? 1;
      $size = $param['size'] ?? 20;
      $userId = session('userinfo.id');
      $videos = VideoModel::getLoveByVideos($where, $paginate = true, $page, $size,$userId);
      if($videos->isEmpty()){
          // 对于分页最好不要抛出MissException，客户端并不好处理
         $result = [
              'current_page' => $videos->currentPage(),
              'data' => []
          ];
      }else{
          $data= $videos->toArray();
          $result= [
              'current_page' => $videos->currentPage(),
              'data' => $data
          ];
      }
      return json_encode($result);
  }

  public function videoPlay(Request $request){
      $param = $request->param();
      $info = VideoModel::get($param['id']);
      VideoModel::where('id','=',$param['id'])->setInc('click_num');
      return view('videoPlay',['info'=>$info]);

  }
    /**
     * @param Request $request
     * @return string
     * 视频上传
     */
  public function upload(Request $request){
      $this->checkLogin();
      $file = $request->file('video');
      $pic = $request->file('pic');
      $param = $request->param();
      $userId = session('userinfo.id');
      if(empty($param['title'])){
          $result = [
              'code'=>400,
              'msg'=>'视频标题不能为空'
          ];
          return json($result);
      }
      if(empty($param['class_id'])){
          $result = [
              'code'=>400,
              'msg'=>'分类不能为空'
          ];
          return json($result);
      }
      $classId = $param['class_id'] ?? 1;
      $info = VideoClassModel::get($classId);
      $size = $info['size'];
      if($pic){
          $img = $pic->move(ROOT_PATH . 'public' . DS . 'upload/imgs');
          if($img){
              $getImgName=str_replace("\\","/",$img->getSaveName());
              $imgUrl = '/upload/imgs/'.$getImgName;
              $param['pic'] = $imgUrl;
          }
      }else{
          $result = [
              'code'=>400,
              'msg'=>'视频主图不能为空'
          ];
          return json($result);
      }
      if($file){
          $video = $file->validate(['size'=>$size[$classId],'ext'=>config('video.type')])->move(ROOT_PATH . 'public' . DS . 'upload/videos');
          if($video){
              // 成功上传后 获取上传信息
              $getSaveName=str_replace("\\","/",$video->getSaveName());
              $url = '/upload/videos/'.$getSaveName;
              $param['url'] = $url;
              $param['create_time'] = time();
              $param['class_id'] = $classId;
              $param['user_id'] = $userId;
              Db::startTrans();
              try{
                  (new VideoModel())->save($param);
                  $levelInfo = (new VideoUserLevelModel())->where('user_id','=',$userId)->find();
                  if($levelInfo){
                      (new VideoUserLevelModel())->where('user_id','=',$userId)->setInc('up_num');
                  }else{
                      $data['user_id'] = $userId;
                      $data['create_time'] = time();
                      (new VideoUserLevelModel())->save($data);
                  }
                  Db::commit();
                  $result = [
                      'code'=>200,
                      'msg'=>'上传成功'
                  ];
              }catch (\Exception $e){
                  Db::rollback();
                  $result = [
                      'code'=>400,
                      'msg'=>$e->getMessage()
                  ];
              }

//              if(VideoModel::create($param)){
//                  $levelInfo = VideoUserLevelModel::where('user_id','=',$userId)->find();
//                  if($levelInfo){
//                      VideoUserLevelModel::where('user_id','=',$userId)->setInc('up_num');
//                  }else{
//                      $data['user_id'] = $userId;
//                      $data['create_time'] = time();
//                      VideoUserLevelModel::create($data);
//                  }
//                  $result = [
//                      'code'=>200,
//                      'msg'=>'上传成功'
//                  ];
//              }else{
//                  $result = [
//                      'code'=>400,
//                      'msg'=>'网络异常请重试'
//                  ];
//              }

              return json($result);

          }else{
              // 上传失败获取错误信息
              $result = [
                  'code'=>400,
                  'msg'=>$file->getError()
              ];
              return json($result);
          }
      }else{
          $result = [
              'code'=>400,
              'msg'=>'视频不能为空'
          ];
          return json($result);
      }

  }

    /**
     * @param Request $request
     * @return string
     * 收藏与取消收藏
     */
  public function collect(Request $request){
      $this->checkLogin();
      $param = $request->param();
      $userId = session('userinfo.id');
      $where['user_id'] = $userId;
      $where['video_id'] = $param['id'];
      $info = VideoCollectModel::where($where)->find();
      if($info){
          if(VideoCollectModel::where($where)->delete()){
              $result = [
                  'code'=>200,
                  'msg'=>'取消关注成功'
              ];
          }else{
              $result = [
                  'code'=>400,
                  'msg'=>'取消关注失败'
              ];
          }
          return json($result);
      }else{
          $data = [
              'user_id'=> $userId ,
              'video_id'    => $param['id'],
              'create_time' => time()
          ];
          if(VideoCollectModel::create($data)){
              $result = [
                  'code'=>200,
                  'msg'=>'关注成功'
              ];
          }else{
              $result = [
                  'code'=>400,
                  'msg'=>'关注失败'
              ];
          }
          return json($result);
      }

  }

    /**
     * @param Request $request
     * @return \think\response\View
     * @throws \think\exception\DbException
     * 收藏视频列表
     */
  public function collectList(Request $request){
      $this->checkLogin();
      $where['user_id'] = session('userinfo.id');
      $list = VideoCollectModel::where($where)->order('id desc')->paginate(10);
      return view('collectList',['list'=>$list]);
  }

    /**
     * @param Request $request
     * @return \think\response\View
     * @throws \think\exception\DbException
     * 我的视频
     */
  public function ajaxMyVideos(Request $request){
      $this->checkLogin();
      $param = $request->param();
      $user_id = session('userinfo.id');
      $page = $param['page'] ?? 1;
      $size = $param['size'] ?? 10;
      $list = VideoModel::getVideosByUserID($user_id,true,$page,$size);
      if ($list->isEmpty())
      {
          // 对于分页最好不要抛出MissException，客户端并不好处理
          $result = [
              'current_page' => $list->currentPage(),
              'data' => []
          ];
          return json($result);
      }

      $data = $list
          ->toArray();
      $result =  [
          'current_page' => $list->currentPage(),
          'data' => $data
      ];
      return json($result);
  }

  public function myVideo(){
      return view('myvideo');
  }

  public function mine(Request $request){
      $this->checkLogin();
      $_opt['id'] = session('userinfo.id');
      $userInfo = UserModel::userInfo($_opt);
      return view('mine',['userInfo'=>$userInfo]);
  }

    public function live(Request $request){
        return view('live');
    }
    public function focus(Request $request){
        $this->checkLogin();
        return view('focus');
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 我关注的视频
     */
    public function ajaxMyFocus(Request $request){
        $this->checkLogin();
        $param = $request->param();
        $user_id = session('userinfo.id');
        $page = $param['page'] ?? 1;
        $size = $param['size'] ?? 10;

        $list = VideoModel::getCollectVideos($user_id,true,$page,$size);

        if ($list->isEmpty())
        {
            // 对于分页最好不要抛出MissException，客户端并不好处理
            $result = [
                'current_page' => $list->currentPage(),
                'data' => []
            ];
            return json($result);
        }

        $data = $list->toArray();
        $result =  [
            'current_page' => $list->currentPage(),
            'data' => $data
        ];
        return json($result);
    }

    public function photo(){
        $this->checkLogin();
        return view('photo');
    }
}
