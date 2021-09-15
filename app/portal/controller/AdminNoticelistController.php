<?php
namespace app\portal\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\portal\model\PortalNoticeModel;
use app\admin\model\ThemeModel;


class AdminNoticelistController extends AdminBaseController
{
    /**
     * 公告列表
     * @adminMenu(
     *     'name'   => '公告列表管理',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '公告列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $data=request()->param();
        $where=[];
        if(isset($data['category'])&&!empty($data['category'])){
            $where['noticecategory']=['eq',$data['category']];
            $this->assign('id',$data['category']);
        }
        if(isset($data['title'])&&!empty($data['title'])){
            $where['title']=['like','%'.$data['title'].'%'];
            $this->assign('title',$data['title']);
        }
        $list=Db::name('notice')->where($where)->order('id desc')->paginate(10);

        //dump($list->toArray(),1,'<pre>',0);
        $category=Db::name('noticecategory')->select()->toArray();
        $list->appends($data);
        $page=$list->render();
        $this->assign('category',$category);
        $lists=$list->toArray();
        $this->assign('noticelist',$lists['data']);
        $this->assign('page',$page);
        return $this->fetch();
    }
    /**
     *添加公告
     */
    public  function  add(){
        $noticecategory=Db::name('noticecategory')->select()->toArray();
        $state = Db::name('status')->select()->toArray();;
        $data=request()->param();
        if(request()->isPost()){
            if(empty($data['noticecategory'])){
                $this->error('请选择分类!');
            }
            if(empty($data['title'])){
                $this->error('标题不能为空!');
            }
            if(empty($data['content'])){
                $this->error('内容不能为空!');
            }
            if(empty($data['state'])){
                $this->error('状态不能为空!');
            }
            $datas['title']=$data['title'];
            $datas['noticecategory']=$data['noticecategory'];

            $datas['content']=$data['content'];
            $datas['state']=$data['state'];
            $datas['time']=time();
            $res=Db::name('notice')->insert($datas);
            if($res){
                $this->success('添加成功!',url('AdminNoticelist/index'));
            }else{
                $this->error('网络错误请稍后重试!');
            }
        }
        $this->assign('noticecategory',$noticecategory);
        $this->assign('state',$state);
        return $this->fetch();
    }
    /**
     * 分类编辑
     */
    public  function  edit(){
        $data=request()->param();
        $noticecategory=Db::name('noticecategory')->select()->toArray();
        $state = Db::name('status')->select()->toArray();;
        $info=Db::name('notice')->where('Id',$data['id'])->find();
        $this->assign('state',$state);
        $this->assign('noticecategory',$noticecategory);
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function editPost()
    {
        $data=request()->param();
        if(request()->isAjax()){
            if(empty($data['noticecategory'])){
                $this->error('请选择分类!');
            }
            if(empty($data['title'])){
                $this->error('标题不能为空!');
            }
            if(empty($data['content'])){
                $this->error('内容不能为空!');
            }
            if(empty($data['state'])){
                $this->error('状态不能为空!');
            }
            $res=Db::name('notice')
                ->where('Id',$data['Id'])
                ->update([
                    'title'=>$data['title'],
                    'noticecategory'=>$data['noticecategory'],
                    'state'=>$data['state'],
                    'content'=>$data['content'],
                    'time'=>time()
                ]);

            if($res){
                $this->success('编辑成功!',url('AdminNoticelist/index'));
            }else{
                $this->error('网络错误请稍后重试!');
            }

        }
    }
    /**
     * 删除公告分类
     * @adminMenu(
     *     'name'   => '删除公告分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除公告分类',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $data=request()->param();
        if(isset($data['id'])&&$data['id']){
            $where['id']=array('eq',$data['id']);
        }elseif(isset($data['ids'])&&$data['ids']){
            $where['id']=array('in',$data['ids']);
        }
        $res=Db::name('notice')->where($where)->delete();
        if($res){
            $this->success('删除成功!',url('AdminNoticelist/index'));
        }else{
            $this->error('网络错误请稍后重试!');
        }
    }
    //服务条款
    public function fw(){
        $data=request()->param();
        $info = Db::name('tkuan')->where('id',1)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function fwedit()
    {
        $data=request()->param();
        if(request()->isAjax()){  
            if(empty($data['title'])){
                $this->error('标题不能为空!');
            }
            if(empty($data['content'])){
                $this->error('内容不能为空!');
            }
            $res=Db::name('tkuan')
                ->where('id',1)
                ->update([
                    'title'=>$data['title'],
                    'content'=>$data['content'],
                    'time'=>time()
                ]);

            if($res){
                $this->success('编辑成功!',url('AdminNoticelist/fw'));
            }else{
                $this->error('网络错误请稍后重试!');
            }

        }
    }

        //服务条款
    public function ys(){
        $data=request()->param();
        $info = Db::name('tkuan')->where('id',2)->find();
        $this->assign('info',$info);    
        return $this->fetch();
    }
    public function ysedit()
    {
        $data=request()->param();
        if(request()->isAjax()){  
            if(empty($data['title'])){
                $this->error('标题不能为空!');
            }
            if(empty($data['content'])){
                $this->error('内容不能为空!');
            }
            $res=Db::name('tkuan')
                ->where('id',2)
                ->update([
                    'title'=>$data['title'],
                    'content'=>$data['content'],
                    'time'=>time()
                ]);

            if($res){
                $this->success('编辑成功!',url('AdminNoticelist/ys'));
            }else{
                $this->error('网络错误请稍后重试!');
            }

        }
    }
    public function lpic(){
        $data=request()->param();
        $where=[];
        if(isset($data['category'])&&!empty($data['category'])){
            $where['noticecategory']=['eq',$data['category']];
            $this->assign('id',$data['category']);
        }
        
        $list=Db::name('lpic')->where($where)->order('id desc')->paginate(10);
        $list->appends($data);
        $page=$list->render();
        $lists=$list->toArray();
        $this->assign('list',$lists['data']);
        $this->assign('page',$page);
        return $this->fetch();
    }
    public  function  lpicadd(){
        if(request()->isPost()){
            $data=request()->param();


        $file = $this->request->file('file');  
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/lpic');  
            if ($info) {  
                $img = $info->getSaveName();//获取名称

                $imgpath = DS.'upload/lpic'.DS.$img;
                
                $path = str_replace(DS,"/",$imgpath);//数据库存储路径

                $status = 1;
                $message = '上传成功'.$path;

            } else {
                $status = 0;
                $message = '图片上传失败';
            }
        }else{
            $this->error('图片不能为空!');
        }
            $datas['title']=$data['title'];
            $datas['pic']=$path;
            $datas['time']=time();
            $res=Db::name('lpic')->insert($datas);
            if($res){
                $this->success('添加成功!',url('AdminNoticelist/lpic'));
            }else{
                $this->error('网络错误请稍后重试!');
            }
        }

        return $this->fetch();
    }

        public  function  lpicupd(){
            $data=request()->param();
        if(request()->isPost()){
            
        $file = $this->request->file('file');  
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/lpic');  
            if ($info) {  
                $img = $info->getSaveName();//获取名称

                $imgpath = DS.'upload/lpic'.DS.$img;
                
                $path = str_replace(DS,"/",$imgpath);//数据库存储路径

                $status = 1;
                $message = '上传成功'.$path;

            } else {
                $status = 0;
                $message = '图片上传失败';
            }
            $datas['pic']=$path;
        }
            $datas['title']=$data['title'];
            
            $datas['time']=time();
            $res=Db::name('lpic')->where('id',$data['id'])->update($datas);
            if($res){
                $this->success('编辑成功!',url('AdminNoticelist/lpic'));
            }else{
                $this->error('网络错误请稍后重试!');
            }
        }
        $res=Db::name('lpic')->where('id',$data['id'])->find();
        $this->assign('info',$res);
        return $this->fetch();
    }
    public function lpicdel(){
        $data=request()->param();
        $res=Db::name('lpic')->where('id',$data['id'])->delete();
        if($res){
                $this->success('删除成功!',url('AdminNoticelist/lpic'));
            }else{
                $this->error('网络错误请稍后重试!');
            }
    }

}