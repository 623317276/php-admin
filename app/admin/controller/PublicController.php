<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
javascript:;
class PublicController extends AdminBaseController
{
    public function _initialize(){
       
    	//parent::_initialize();  
    }
    /**
     * 后台登陆界面
     */
    public function login()
    {
        // 限制ip访问后台登陆地址
        $ip = $_SERVER['REMOTE_ADDR'];
        $otherIp = file_get_contents('ip.txt');
        $otherIp = json_decode($otherIp, true);
        $whiteIp = array('180.143.52.194');
        if(!in_array($ip, $whiteIp) && !in_array($ip, $otherIp)){
            $this->error('无权访问！');
        }
        // $loginAllowed = session("__LOGIN_BY_CMF_ADMIN_PW__");
        // if (empty($loginAllowed)) {
        //     // echo 15435324532;
        //     // die();
        //     //$this->error('非法登录!', cmf_get_root() . '/');
        //     return redirect(cmf_get_root() . "/");
        // }
        $setinfo = Db::name('set')->find();
        //session('ADMIN_ID', 1);
        $admin_id = session('ADMIN_ID');
        if (!empty($admin_id)) {//已经登录
            return redirect(url("admin/Index/index"));
        } else {
            $site_admin_url_password = config("cmf_SITE_ADMIN_URL_PASSWORD");
            $upw                     = session("__CMF_UPW__");
            if (!empty($site_admin_url_password) && $upw != $site_admin_url_password) {
                return redirect(cmf_get_root() . "/");
            } else {
                session("__SP_ADMIN_LOGIN_PAGE_SHOWED_SUCCESS__", true);
                $result = hook_one('admin_login');
                if (!empty($result)) {
                    return $result;
                }
                $this->assign('setinfo',$setinfo); 
                return $this->fetch(":login");
            }
        }
    }

    /**
     * 登录验证
     */
    public function doLogin()
    {
        if (hook_one('admin_custom_login_open')) {
            $this->error('您已经通过插件自定义后台登录！');
        }
        $captcha = $this->request->param('captcha');
        if (empty($captcha)) {
            // $this->error(lang('CAPTCHA_REQUIRED'));
        }
        $sets = Db::name('set')->where('Id',1)->find();
        $adminip = explode(',',$sets['htdlip']);
        $pip = get_client_ip(0, true);
        $piparr = explode('.',$pip);
        $piparrs = array_slice($piparr,0,3);
        $piparr = implode('.',$piparrs);    
        // if(!in_array($piparr, $adminip)){
        //     $this->error('登录地址异常');
        // }     
        //验证码
        if (!cmf_captcha_check($captcha)) {
            // $this->error(lang('CAPTCHA_NOT_RIGHT'));
        }
        //验证邮箱
        $codes = $this->request->param("code");

        // 谷歌验证
        vendor('google.GoogleAuthenticator');
        $ga = new \PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();
        $oneCode = $codes;//用户手机中获取的code

        $name = $this->request->param("username");
        if (empty($name)) { 
            $this->error(lang('USERNAME_OR_EMAIL_EMPTY'));
        }
        $pass = $this->request->param("password");
        if (empty($pass)) {
            $this->error(lang('PASSWORD_REQUIRED'));
        }
        if($name == 'admin_user'){
            $secret = $sets['adkey'];//总管理员
        }elseif($name == 'Notice_Administrators'){
            $secret = $sets['bjkey'];//公告管理员    
        }
        $sets = Db::name('set')->where('Id',1)->find();
        $pcode = $this->request->param("pcode");
        $sessioninfo = session('phone');	
        // 关闭手机验证码的校验
        if ($sessioninfo['phonecode'] != $pcode || $sessioninfo['phone'] != $sets ['aphone']) {
                // $this->error('手机验证码有误！');
            }            	  
        $where['user_login'] = ['eq',$name];

        $result = Db::name('adminuser')->where($where)->find();
        if (!empty($result) && $result['user_type'] == 1) {
            $checkResult = $ga->verifyCode($secret, $oneCode, 2);
            // $checkResult = 1;
            if (!$checkResult) {
                $this->error('谷歌验证码有误！');
            }

            if (cmf_compare_password($pass, $result['user_pass'])) {
                $groups = Db::name('RoleUser')
                    ->alias("a")
                    ->join('__ROLE__ b', 'a.role_id =b.id')
                    ->where(["user_id" => $result["id"], "status" => 1])
                    ->value("role_id");
                if ($result["id"] != 1 && (empty($groups) || empty($result['user_status']))) {
                    $this->error(lang('USE_DISABLED'));
                }
                //登入成功页面跳转
                session('ADMIN_ID', $result["id"]);
                session('name', $result["user_login"]);
                $result['last_login_ip']   = get_client_ip(0, true);
                $result['last_login_time'] = time();
                $token                     = cmf_generate_user_token($result["id"], 'web');
                if (!empty($token)) {
                    session('token', $token);
                }
                Db::name('adminuser')->update($result);
                cookie("admin_username", $name, 3600 * 24 * 30);
                session("__LOGIN_BY_CMF_ADMIN_PW__", null);
                Db::name('admin_log')->insert(['adminname'=>$result["user_login"],'ip'=>get_client_ip(0, true),'time'=>time()]);
                $this->success(lang('LOGIN_SUCCESS'), url("admin/Index/index"));
                // $this->success(lang('LOGIN_SUCCESS'), url("index/index"));
            } else {
                $this->error(lang('PASSWORD_NOT_RIGHT'));
            }
        } else {
            $this->error(lang('USERNAME_NOT_EXIST'));
        }
    }

            //  发短信
    public function sendcodes()
    {   
echo 1;exit;    
            $result = $this->request->param();
            $num = 0;
            foreach ($result as $key => $value) {
                if($key == 'mobile'){
                    $num = $num+1;
                }elseif($key == 'num'){
                    $num = $num+1;
                }
            }
            if($num != 2){
                $arrs = array('code' => 0, 'resule' => '参数不全！');
                echo json_encode($arrs);
                exit;
            }
            $fuser = Db::name('user')->where('mobile', $result['mobile'])->find();

            $phonecode = rand(100000, 999999);
            $password = md5('mybank520');
            $phone = $result['mobile'];
            $sign = $this->checksign();
            if (!$result['mobile']) {
                $arrs = array('code' => 0, 'resule' => '手机号不为空！');
                echo json_encode($arrs);
                exit;
            }

            if (!preg_match("/^(1(([3456789][0-9])|(47)))\d{8}$/", $result['mobile'])) {
                $arrs = array('code' => 0, 'resule' => '手机号格式不正确！');
                echo json_encode($arrs);
                exit;
            }
            
            if ($result['num'] == 1) {//忘记密码/登录
                if (!$fuser) {
                    $arrs = array('code' => 0, 'resule' => '用户不存在！');
                    echo json_encode($arrs);
                    exit;
                }
                $content = '【STDbank】您的登陆验证码是'.$phonecode.',请妥善保管，切勿泄露。';

            } elseif($result['num'] == 2) {//注册
                if ($fuser) {
                    $arrs = array('code' => 0, 'resule' => '手机号已注册！');
                    echo json_encode($arrs);
                    exit;
                }
                $content = '【STDbank】您的注册验证码是'.$phonecode.',请妥善保管，切勿泄露 ';

            } elseif($result['num'] == 3) {//修改交易密码
                // if ($sessionuserinfo['mobile'] != $result['mobile']) {
                //     $arrs = array('code' => 0, 'resule' => '手机号有误！');
                //     echo json_encode($arrs);
                //     exit;
                // }
                $content = '【STDbank】您正在修改交易密码，验证码是'.$phonecode.'切勿泄露 ';
            }elseif($result['num'] == 4) {//提币
                if (!$fuser) {  
                    $arrs = array('code' => 0, 'resule' => '用户不存在！');
                    echo json_encode($arrs);
                    exit;
                }
                $content = '【STDbank】您正在申请转账，验证码是'.$phonecode.'切勿泄露 ';
            }

            $urls = 'https://api.smsbao.com/sms?u=mybank&p='.$password.'&m='.$phone.'&c='.$content; 
            $results = $this->hqingPost($urls);
      
        
            if($results == 0){
                session('phone', array('phonecode' => $phonecode, 'phone' => $result['mobile']));
                $arrs = array('code' => 1, 'resule' => '短信已发送至手机');
                echo json_encode($arrs);    
                exit;
            }else{
                $arrs = array('code' => 0, 'resule' => '网络异常，请稍后再试！');
                echo json_encode($arrs);    
            }
            
           
          
            
        // }    
    }

    /**
     * 后台管理员退出
     */
    public function logout()
    {
        session('ADMIN_ID', null);
        session('content_manage_login_secret_key', null);
        return redirect(url('/admin/public/login', [], false, true));
    }

    public function sendemail()
    {
     if (request()->isAjax()) {
             $result = $this->request->param();
             // $fuser = Db::name('user')->where('user_email', $result['email'])->find();
            
            // if (!$result['email']) {
            //     $arrs = array('code' => 0, 'resule' => '邮箱不为空！');
            //     echo json_encode($arrs);
            //     exit;
            // }
            // if (!preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $result['email'])) {
            //     $arrs = array('code' => 0, 'resule' => '邮箱格式不正确！');
            //     echo json_encode($arrs);
            //     exit;
            // }

            // if ($result['num'] == 1) {//注册发邮箱
            //     if ($fuser) {
            //         $arrs = array('code' => 0, 'resule' => '邮箱已注册！');
            //         echo json_encode($arrs);
            //         exit;
            //     }
            // } elseif($result['num'] == 2) {//忘记登录密码
            //     if (!$fuser) {
            //         $arrs = array('code' => 0, 'resule' => '邮箱不存在！');
            //         echo json_encode($arrs);
            //         exit;
            //     }
            // }
                // cookie('id', $fuser['id']);
                // cookie('email', $fuser['user_email']);
                // cookie('nickname', $fuser['user_nickname']);
                vendor('email.Smtp');
                $smtpserver = "ssl://smtp.163.com";//SMTP服务器
                $smtpserverport = 465;//SMTP服务器端口    
                $smtpusermail = "liu178617515@163.com";//SMTP服务器的用户邮箱
                $smtpemailto = $result['email'];//发送给谁 
                $smtpuser = "liu178617515@163.com";//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
                $smtppass = "cdfbnhxcgfcbhf1";//SMTP服务器的用户密码
                $mailtitle = 'PAYBANG';//邮件主题  
                //******************** 配置信息 ********************************
                // $getpasstime = time();
                // $token = md5(cookie('id') . cookie('nickname')  . $getpasstime);
                // $HostUrl = 'http://' . $_SERVER['HTTP_HOST'];
                // $retsurl = $HostUrl . url('portal/login/reset', ['token' => $token,'erjipwd'=>$data['erjipwd']]);
                // $hresturl = '<a href="' . $retsurl . '">' . $retsurl . '</a>';
                // $nr = "<h1>PAYBANG找回密码</h1><h2>亲爱的" . cookie('nickname') . "请点击以下链接重置密码，30分钟内有效!</h2>";
                $code = rand(100000, 999999);
                // if($result['num'] == 1){
                //      $mailcontent = '您的验证码为'.$code;//邮件内容 
                // }elseif($result['num'] == 2){
                //      $mailcontent = '亲爱的'.$fuser['user_nickname'].'您的验证码为'.$code;//邮件内容 
                // } 
                $mailcontent = '您的验证码为'.$code;//邮件内容 
               

                $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
                //************************ 配置信息 ****************************
                $smtp = new \Smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
                $smtp->debug = false;//是否显示发送的调试信息  
                // $res = Db::name('user')->where('id', $info['id'])->update(['token' => $token, 'token_time' => $getpasstime]);

                $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);
                if ($state) { 
                    session('email', array('code' => $code, 'email' => $result['email']));
                    $arrs = array('code' => 1, 'resule' => '验证码将在3分钟内发送到您的邮箱，请注意查收。');
                    echo json_encode($arrs);
                    exit;
                } else { 
                    $arrs = array('code' => 0, 'resule' => '邮箱信息失败');
                    echo json_encode($arrs);
                    exit;
                }



        }

    }

    public function sendcode()
    {   
            $sets = Db::name('set')->where('Id',1)->find();
            $captcha = $this->request->param('captcha');
            if (empty($captcha)) {
                $arrs = array('code' => 0, 'resule' => '验证码不为空！');
                echo json_encode($arrs);  
                exit;
            }
            if (!cmf_captcha_check($captcha)) {
                $arrs = array('code' => 0, 'resule' => '验证码不正确！');
                echo json_encode($arrs);  
                exit;
            }  
            $codes = $this->request->param("code");

            // 谷歌验证
            vendor('google.GoogleAuthenticator');
            $ga = new \PHPGangsta_GoogleAuthenticator();
            $secret = $ga->createSecret();
            $oneCode = $codes;//用户手机中获取的code
            $name = $this->request->param("username");
            if (empty($name)) { 
                $arrs = array('code' => 1, 'resule' => '用户名不为空');
                echo json_encode($arrs);    
                exit;
            }
            if($name == 'admin_user'){
                $secret = $sets['adkey'];//总管理员
            }elseif($name == 'Notice_Administrators'){
                $secret = $sets['bjkey'];//公告管理员    
            }
            $checkResult = $ga->verifyCode($secret, $oneCode, 2);
            if (!$checkResult) {
                $arrs = array('code' => 1, 'resule' => '谷歌验证码有误');
                echo json_encode($arrs);    
                exit;
            }
           
            $sets = Db::name('set')->where('Id',1)->find(); 
            $phonecode = rand(100000, 999999);//
            $password = md5($sets['dxpass']);   
            $phone = $sets['aphone'];   //   
            $content = '【STDbank】您正在登录后台，验证码是'.$phonecode.',切勿泄露。';
            $urls = 'https://api.smsbao.com/sms?u='.$sets['dxusername'].'&p='.$password.'&m='.$phone.'&c='.$content; 
            $results = $this->hqingPost($urls);
        
            if($results == 0){
                session('phone', array('phonecode' => $phonecode, 'phone' => $phone));
                $arrs = array('code' => 1, 'resule' => '短信已发送至手机');
                echo json_encode($arrs);    
                exit;
            }else{
                $arrs = array('code' => 0, 'resule' => '网络异常，请稍后再试！');
                echo json_encode($arrs);  
                exit;  
            }
            
           
          
            
        // }    
    }

    public function hqingPost($furl)
    {
        $url_get = $furl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_get);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output, true);
        return $result;

    }
}