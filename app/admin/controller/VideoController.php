<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/12
 * Time: 13:57
 */
namespace app\admin\controller;

use app\admin\model\UserModel;
use app\admin\model\VideoLevelModel;
use app\admin\model\VideoModel;
use app\admin\model\VideoUserLevelModel;
use cmf\controller\AdminBaseController;
use app\admin\model\VideoClassModel;
use think\Request;
use think\Db;

class VideoController extends AdminBaseController{

    /**
     * 视频分类设置
     */
    public function classify(Request $request){
        $param = $request->param();
        $page = $param['page'] ?? 10;
        $where = [];
        if(!empty($param['title'])){
            $where['class_name'] = ['like','%'.$param['title'].'%'];
        }
        $list = VideoClassModel::where($where)->order('id desc')->paginate($page);
        return view('classify',['list'=>$list,'param'=>$param]);
    }

    public function add(Request $request){
        $param = $request->param();
        $param['create_time'] = time();
        if($request->isPost()){
            $add_info = VideoClassModel::create($param);
            if($add_info){
                $this->success('添加成功!',url('Video/classify'));
            }else{
                $this->error('添加失败!');
            }
        }
        return view('add');
    }

    public function edit(Request $request){
        $param = $request->param();
        $where['id'] =$param['id'];
        if($request->isPost()){
            $add_info = VideoClassModel::where('id','=',$param['id'])->update($param);
            if($add_info){
                $this->success('修改成功!',url('Video/classify'));
            }else{
                $this->error('修改失败!');
            }
        }
        $info = VideoClassModel::where($where)->find();
        return view('edit',['info'=>$info,'param'=>$param]);
    }

    public function delete(Request $request){
        $param = $request->param();
        $where['id'] =$param['id'];
        $add_info = VideoClassModel::where('id','=',$param['id'])->delete();
        if($add_info){
            $this->success('删除成功!',url('Video/classify'));
        }else{
            $this->error('删除失败!');
        }
    }

    /**
     * @return string
     * 视频上传等级设置
     */
    public function grade(Request $request){
        $param = $request->param();
        $page = $param['page'] ?? 10;
        $where = [];
        if(!empty($param['title'])){
            $where['level'] = ['=',$param['title']];
        }
        $list = VideoLevelModel::where($where)->order('id desc')->paginate($page);
        return view('grade',['list'=>$list,'param'=>$param]);
    }

    public function grade_add(Request $request){
        $param = $request->param();
        $param['create_time'] = time();
        if($request->isPost()){
            $add_info = VideoLevelModel::create($param);
            if($add_info){
                $this->success('添加成功!',url('Video/grade'));
            }else{
                $this->error('添加失败!');
            }
        }
        return view('grade_add');
    }

    public function grade_edit(Request $request){
        $param = $request->param();
        $where['id'] =$param['id'];
        if($request->isPost()){
            $add_info = VideoLevelModel::where('id','=',$param['id'])->update($param);
            if($add_info){
                $this->success('修改成功!',url('Video/grade'));
            }else{
                $this->error('修改失败!');
            }
        }
        $info = VideoLevelModel::where($where)->find();
        return view('grade_edit',['info'=>$info,'param'=>$param]);
    }

    public function grade_delete(Request $request){
        $param = $request->param();
        $where['id'] =$param['id'];
        $add_info = VideoLevelModel::where('id','=',$param['id'])->delete();
        if($add_info){
            $this->success('删除成功!',url('Video/grade'));
        }else{
            $this->error('删除失败!');
        }
    }

    /**
     * @param Request $request
     * 视频审核
     */
    public function audit(Request $request){
        $param = $request->param();
        $classList = VideoClassModel::where('status','=',1)->select();
        $where = [];
        if(!empty($param['title'])){
            $where['user_nickname'] = ['=',$param['title']];
        }
        if(empty($param['status'])){
            $arr['status'] = ['in',[0,2]];
        }else{
            $arr['status'] = $param['status'];
        }
        if(!empty($param['class_id'])){
            $arr['class_id'] = ['=',$param['class_id']];
        }
        $list = VideoModel::pendingAudit($where,$arr,10);

        return view('audit',['list'=>$list,'param'=>$param,'classList'=>$classList]);
    }

    public function audit_on(Request $request){
        $param = $request->param();
        $where['id'] =$param['id'];
        $data = [
            'status'=>1
        ];
        $info = VideoModel::get($param['id']);


        // 启动事务
        Db::startTrans();
        try{
            $add_info = Db::table('cmf_video')->where('id','=',$param['id'])->update($data);
            $upInfo = Db::table('cmf_video_user_level')->where('user_id','=',$info['user_id'])->find();
            $levelInfo = Db::table('cmf_video_level')->where('level','>',$upInfo['user_level'])->order('id asc')->find();

            if($levelInfo && ($upInfo['valid_num']+1)>=$levelInfo['up_num']){
                $userLevel['user_level'] =$levelInfo['level'];
            }
            $userLevel['valid_num'] =$upInfo['valid_num']+1;
            $addNum =Db::table('cmf_video_user_level')->where('id','=',$upInfo['id'])->update($userLevel);
            if($add_info && $upInfo && $addNum){
                // 提交事务
                Db::commit();
            }else{
                // 回滚事务
                Db::rollback();
            }


        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('审核失败!');
        }
        $this->success('审核成功!',url('Video/audit'));
    }

    public function audit_off(Request $request){
        $param = $request->param();
        $where['id'] =$param['id'];
        $data = [
            'status'=>2
        ];
        $add_info = VideoModel::where('id','=',$param['id'])->update($data);
        if($add_info){
            $this->success('驳回成功!',url('Video/audit'));
        }else{
            $this->error('驳回失败!');
        }
    }

    /**
     * @param Request $request
     * 视频列表
     */
    public function videoList(Request $request){
        $classList = VideoClassModel::where('status','=',1)->select();
        $param = $request->param();
        $where = [];
        if(!empty($param['title'])){
            $where['user_nickname'] = ['=',$param['title']];
        }
        if(!empty($param['class_id'])){
            $arr['class_id'] = ['=',$param['class_id']];
        }
        $arr['status'] = 1;
        $list = VideoModel::AuditAll($where,$arr,10);
        return view('videoList',['list'=>$list,'param'=>$param,'classList'=>$classList]);
    }

    public function upvideos(Request $request){
        $classInfo = VideoClassModel::where('status','=','1')->select();
        $userInfo = UserModel::select();
        $param = $request->param();
        return view('upvideos',['classInfo'=>$classInfo,'userInfo'=>$userInfo]);
    }

    public function upload(Request $request){

        $param = $request->param();
        
        $files = $request->file('videos');
        $pics = $request->file('pic');
        
        $classId = $param['class_id'] ?? 1;
        $info = VideoClassModel::get($classId);
        $user_id = $param['user_id'] ?? 27;
        $size = $info['size'];
        $addInfo = [];
        foreach($files as $key => $file){

            $info = $file->validate(['size'=>$size[$classId],'ext'=>config('video.type')])->move(ROOT_PATH . 'public' . DS . 'upload'.'/videos');

            $picInfo = $pics[$key]->move(ROOT_PATH . 'public' . DS . 'upload'.'/imgs');
            if($info && $picInfo){
                $getSaveName=str_replace("\\","/",$info->getSaveName());
                $getPicName=str_replace("\\","/",$picInfo->getSaveName());
                $val['url'] = '/upload/videos/'.$getSaveName;
                $val['create_time'] = time();
                $val['class_id'] = $classId ;
                $val['user_id'] = $user_id ;
                $val['pic'] =  '/upload/imgs/'.$getPicName ;
                $val['status'] = 1 ;
                array_push($addInfo,$val);
            }

        }
        $upNumber = count($files);
        $picNumber = count($addInfo);
        if(!empty($addInfo)){
            if((new VideoModel())->saveAll($addInfo)){
                $this->success("上传 $upNumber 个成功$picNumber", 'Video/upvideos');
            }else{
                $this->error('上传失败');
            }
        }else{
            $this->error('上传格式不正确！');
        }



    }


}