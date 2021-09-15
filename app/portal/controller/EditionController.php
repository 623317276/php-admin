<?php
namespace app\portal\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\portal\model\PortalNoticeModel;
use app\admin\model\ThemeModel;


class EditionController extends AdminBaseController
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
        $list=Db::name('bblist')->where($where)->order('id desc')->paginate(10);

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
        $data=request()->param();
        if(request()->isPost()){
            if(empty($data['iosbb'])){
                $this->error('苹果版本号不为空!');
            }
            if(empty($data['iosurl'])){
                $this->error('苹果升级url不为空!');
            }
            if(empty($data['ioscontent'])){
                $this->error('苹果更新功能不为空!');
            }
            // if(empty($data['iostype'])){
            //     $this->error('ios版本类型不为空!');
            // }

            if(empty($data['anbb'])){
                $this->error('安卓版本号不为空!');
            }
            if(empty($data['anurl'])){
                $this->error('安卓升级url不为空!');
            }
            if(empty($data['ancontent'])){
                $this->error('安卓更新功能不为空!');
            }
            // if(empty($data['antype'])){
            //     $this->error('安卓版本类型不为空!');
            // }
            $datas['iosbb']=$data['iosbb'];
            $datas['iosurl']=$data['iosurl'];
            $datas['ioscontent']=$data['ioscontent'];
            $datas['iostype']=$data['iostype'];

            $datas['anbb']=$data['anbb'];
            $datas['anurl']=$data['anurl'];
            $datas['ancontent']=$data['ancontent'];
            $datas['antype']=$data['antype'];

            $datas['time']=time();  
            $res=Db::name('bblist')->insert($datas);
            if($res){
                $this->success('添加成功!',url('edition/index'));
            }else{
                $this->error('网络错误请稍后重试!');
            }
        }
        return $this->fetch();
    }
    /**
     * 分类编辑
     */
    public  function  edit(){
        $data=request()->param();
        $info=Db::name('bblist')->where('id',$data['id'])->find();
        $this->assign('info',$info);    
        return $this->fetch();
    }
    public function editPost()
    {
        $data=request()->param();
        
        if(request()->ispost()){
            if(empty($data['iosbb'])){
                $this->error('苹果版本号不为空!');
            }
            if(empty($data['iosurl'])){
                $this->error('苹果升级url不为空!');
            }
            if(empty($data['ioscontent'])){
                $this->error('苹果更新功能不为空!');
            }
            // if(empty($data['iostype'])){
            //     $this->error('ios版本类型不为空!');
            // }

            if(empty($data['anbb'])){
                $this->error('安卓版本号不为空!');
            }
            if(empty($data['anurl'])){
                $this->error('安卓升级url不为空!');
            }
            if(empty($data['ancontent'])){
                $this->error('安卓更新功能不为空!');
            }
            // if(empty($data['antype'])){
            //     $this->error('安卓版本类型不为空!');
            // }
            $res=Db::name('bblist')
                ->where('id',$data['id'])
                ->update([
                    'iosbb'=>$data['iosbb'],
                    'iosurl'=>$data['iosurl'],
                    'ioscontent'=>$data['ioscontent'],
                    'iostype'=>$data['iostype'],
                    'anbb'=>$data['anbb'],
                    'anurl'=>$data['anurl'],
                    'ancontent'=>$data['ancontent'],
                    'antype'=>$data['antype'],
                    'time'=>time()  
                ]);

            if($res){
                $this->success('编辑成功!',url('edition/index'));
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
        $res=Db::name('bblist')->where($where)->delete();
        if($res){
            $this->success('删除成功!',url('edition/index'));
        }else{
            $this->error('网络错误请稍后重试!');
        }
    }
}