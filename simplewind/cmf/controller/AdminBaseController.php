<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace cmf\controller;

use think\Db;

class AdminBaseController extends BaseController
{

    public function _initialize()
    {
    	 
        // $admin_login_check_array=config('admin_login_check');
        // $content_manage_login_secret_key=session('content_manage_login_secret_key');
        // if(empty($content_manage_login_secret_key) || !$content_manage_login_secret_key){
        //     if(empty($_SERVER['HTTP_REFERER'])){
        //         $this->checklogout();
        //         $this->redirect("portal/login/start");
        //         //退出
        //         exit;
        //     }else{
        //         $host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        //         $redirect=empty(trim(strrchr($_SERVER['HTTP_REFERER'], '/'),'/'))?'':trim(strrchr($_SERVER['HTTP_REFERER'], '/'),'/');
        //         if(!empty($redirect)){
        //             $key=empty(trim(strrchr($redirect, '?'),'?'))?'':trim(strrchr($redirect, '='),'=');
        //             if(!empty($key)){
        //                 $redirect_filename=empty(substr($redirect,0,strpos($redirect, '?')))?'':substr($redirect,0,strpos($redirect, '?'));
        //                 if(!empty($redirect_filename)){
        //                     if(in_array($host,$admin_login_check_array['webname_list']) && $key==$admin_login_check_array['key'] && $redirect_filename==$admin_login_check_array['redirect_filename']){
        //                         session('content_manage_login_secret_key',$key);
        //                     }else{
        //                         $this->checklogout();
        //                         $this->redirect("portal/login/start");
        //                         //退出
        //                         exit;
        //                     }
        //                 }else{
        //                     $this->checklogout();
        //                     $this->redirect("portal/login/start");
        //                     //退出
        //                     exit;
        //                 }
        //             }else{
        //                 $this->checklogout();
        //                 $this->redirect("portal/login/start");
        //                 //退出
        //                 exit;
        //             }
        //         }else{
        //             $this->checklogout();
        //             $this->redirect("portal/login/start");
        //             //退出
        //             exit;
        //         }
        //     }
        // }

        // if(session('content_manage_login_secret_key')!=$admin_login_check_array['key']){
        //     $this->checklogout();
        //     $this->redirect("portal/login/start");
        //     //退出
        //     exit;
        // }
        // $ips = $_SERVER['REMOTE_ADDR'];
        // $ips = explode('.',$ips);
        // $ips = array_slice($ips,0,2);
        // $ips = implode('.',$ips);
        
        // if(!in_array($ips,$admin_login_check_array['ip_list'])){
        //     $this->checklogout();
        //     $this->redirect("portal/login/start");
        //     //退出
        //     exit;
        // }
        
        // 监听admin_init
        hook('admin_init');
        parent::_initialize();
        $session_admin_id = session('ADMIN_ID');
        if (!empty($session_admin_id)) {
            $user = Db::name('user')->where(['id' => $session_admin_id])->find();

            if (!$this->checkAccess($session_admin_id)) {
                $this->error("您没有访问权限！");
            }
            $this->assign("admin", $user);
        } else {
            // $request= \Request::instance();
            $controller_name=$this->request->controller();
            if($controller_name!='Public'){
                if ($this->request->isPost()) {
                    $this->error("您还没有登录！", url("admin/public/login"));
                } else {
                    header("Location:" . url("admin/public/login"));
                    exit();
                }
            }
            
        }
    }

    public function _initializeView()
    {
        $cmfAdminThemePath    = config('cmf_admin_theme_path');
        $cmfAdminDefaultTheme = cmf_get_current_admin_theme();

        $themePath = "{$cmfAdminThemePath}{$cmfAdminDefaultTheme}";

        $root = cmf_get_root();

        //使cdn设置生效
        $cdnSettings = cmf_get_option('cdn_settings');
        if (empty($cdnSettings['cdn_static_root'])) {
            $viewReplaceStr = [
                '__ROOT__'     => $root,
                '__TMPL__'     => "{$root}/{$themePath}",
                '__STATIC__'   => "{$root}/static",
                '__WEB_ROOT__' => $root
            ];
        } else {
            $cdnStaticRoot  = rtrim($cdnSettings['cdn_static_root'], '/');
            $viewReplaceStr = [
                '__ROOT__'     => $root,
                '__TMPL__'     => "{$cdnStaticRoot}/{$themePath}",
                '__STATIC__'   => "{$cdnStaticRoot}/static",
                '__WEB_ROOT__' => $cdnStaticRoot
            ];
        }

        $viewReplaceStr = array_merge(config('view_replace_str'), $viewReplaceStr);
        config('template.view_base', "$themePath/");
        config('view_replace_str', $viewReplaceStr);
    }

    /**
     * 初始化后台菜单
     */
    public function initMenu()
    {
    }

    /**
     *  检查后台用户访问权限
     * @param int $userId 后台用户id
     * @return boolean 检查通过返回true
     */
    private function checkAccess($userId)
    {
        // 如果用户id是1，则无需判断
        if ($userId == 1) {
            return true;
        }

        $module     = $this->request->module();
        $controller = $this->request->controller();
        $action     = $this->request->action();
        $rule       = $module . $controller . $action;

        $notRequire = ["adminIndexindex", "adminMainindex"];
        if (!in_array($rule, $notRequire)) {
            return cmf_auth_check($userId);
        } else {
            return true;
        }
    }
    public function checklogout()
    {
        session('ADMIN_ID', null);
        session('content_manage_login_secret_key', null);
        // return redirect(url('/admin', [], false, true));
    }
}