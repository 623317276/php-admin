<?php
namespace app\admin\controller;
use app\admin\model\NoticecategoryModel;
use cmf\controller\AdminBaseController;

class NoticecategoryController extends AdminBaseController
{
    public function index(){
        $content = hook_one('admin_noticecategory_index_view');

        if (!empty($content)) {
            return $content;
        }
        $noticecategory = new NoticecategoryModel();
        $noticecategory     = $noticecategory->select();
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
        $data      = $this->request->param();
        $noticecategory= new NoticecategoryModel();
        $result    = $noticecategory->save($data);
        if ($result === false) {
            $this->error($noticecategory->getError());
        }

        $this->success("添加成功！", url("noticecategory/index"));
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
        $id        = $this->request->param('id', 0, 'intval');
        $noticecategory = NoticecategoryModel::get($id);
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
        $data      = $this->request->param();
        $id          = intval($data['Id']);
        $noticecategory = new NoticecategoryModel();
        $result    = $noticecategory->where(["Id" =>$id])->update($data);
        if ($result === false) {
            $this->error($noticecategory->getError());
        }

        $this->success("保存成功！", url("noticecategory/index"));
    }

    /**
     * 删除友情链接
     * @adminMenu(
     *     'name'   => '删除友情链接',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除友情链接',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        NoticecategoryModel::destroy($id);

        $this->success("删除成功！", url("noticecategory/index"));
    }

}