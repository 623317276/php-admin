<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace cmf\controller;

use think\Controller;
use think\Request;
use think\Response;
use think\View;
use think\Config;

class BaseController extends Controller
{
    /**
     * 构造函数
     * @param Request $request Request对象
     * @access public
     */
    public function __construct(Request $request = null)
    {
        if (!cmf_is_installed() && $request->module() != 'install') {
            header('Location: ' . cmf_get_root() . '/?s=install');
            exit;
        }

        if (is_null($request)) {
            $request = Request::instance();
        }

        $this->request = $request;

        $this->_initializeView();
        $this->view = View::instance(Config::get('template'), Config::get('view_replace_str'));


        // 控制器初始化
        $this->_initialize();

        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                    $this->beforeAction($options) :
                    $this->beforeAction($method, $options);
            }
        }
        
        // 服务端对同一IP，同一账号多次失败登陆做鉴别
        // $checkUrl = array('install/Userapi/login'); // 次数受限路由
        // $moduleName = $this->request->module(); // model名
        // $controllerName = $this->request->controller(); // 控制器名
        // $actionName = $this->request->action(); // 方法名
        // $sessioninfo = session('phone');
        // $c = session($sessioninfo['phone']);
        // if($c['num'] > 50 && in_array($this->request->url(), $checkUrl)){
            // echo json_encode(array('code'=>10,'resule'=>'操作异常，请重新登陆'));
            // exit;
        // }
        
        // $sessioninfo = session('phone');
        // if(!empty(session($sessioninfo['phone']))){
        //     $temp = session($sessioninfo['phone']);
        //     $num = $temp['num'] + 1;
        // }else{
        //     $num = 0;
        // }
        // session($sessioninfo['phone'], array('ip' => $_SERVER['REMOTE_ADDR'], 'num' => $num));
        
        
        // $result = $this->request->param();
        // if(empty($result['mobile']) || !in_array($result['mobile'], [15091869853,13106012429,13270407777])){
        //     $arrs = array('code' => 0, 'resule' => '系统维护中....');
        //     echo json_encode($arrs); exit;
        // }
    }


    // 初始化视图配置
    protected function _initializeView()
    {
    }

    /**
     *  排序 排序字段为list_orders数组 POST 排序字段为：list_order
     */
    protected function listOrders($model)
    {
        if (!is_object($model)) {
            return false;
        }

        $pk  = $model->getPk(); //获取主键名称
        $ids = $this->request->post("list_orders/a");

        if (!empty($ids)) {
            foreach ($ids as $key => $r) {
                $data['list_order'] = $r;
                $model->where([$pk => $key])->update($data);
            }

        }

        return true;
    }

}