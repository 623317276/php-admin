<?php
namespace app\portal\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\ThemeModel;

class AdminNoticecategoryController extends AdminBaseController
{
    public function index(){

        $noticecategory=Db::name('noticecategory')->select()->toArray();

        $this->assign('noticecategory', $noticecategory);

        return $this->fetch();
    }
    /**
     * 添加公告分类
     * @adminMenu(
     *     'name'   => '添加公告分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加公告分类',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加公告分类提交保存
     * @adminMenu(
     *     'name'   => '添加公告分类提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加公告分类提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if(request()->isAjax()){
            $data=request()->param();
            if(empty($data['categoryname'])){
                $this->error('分类名称不能为空!');
            }
            $res=Db::name('noticecategory')->insert(['categoryname'=>$data['categoryname']]);
            if($res){
                $this->success('添加成功!',url('AdminNoticecategory/index'));
            }else{
                $this->error('网络错误请稍后重试!');
            }
        }
    }
    /**
     * 编辑公告分类
     * @adminMenu(
     *     'name'   => '编辑公告分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑公告分类',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $data=request()->param();
        $noticecategory=Db::name('noticecategory')->where('id',$data['id'])->find();
        $this->assign('noticecategory', $noticecategory);
        return $this->fetch();
    }

    /**
     * 编辑公告分类提交保存
     * @adminMenu(
     *     'name'   => '编辑公告分类提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑公告分类提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
{
    $data=request()->param();
    if(request()->isAjax()){
        if(empty($data['categoryname'])){
            $this->error('分类名称不能为空!');
        }
        $res=Db::name('noticecategory')->where('Id',$data['Id'])->update(['categoryname'=>$data['categoryname']]);
        if($res){
            $this->success('编辑成功!',url('AdminNoticecategory/index'));
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
        $res=Db::name('noticecategory')->where($where)->delete();
        if($res){
            $this->success('删除成功!',url('AdminNoticecategory/index'));
        }else{
            $this->error('网络错误请稍后重试!');
        }
    }

}