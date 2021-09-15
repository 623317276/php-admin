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
namespace app\install\controller;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST,GET');   
header('Content-Type:text/html;Charset=utf-8');       


use app\portal\model\UserModel;
use cmf\controller\HomeBaseController;
use think\Db;
use newz\apiurl;
class UserapiController extends HomeBaseController
{



    //重置密码
    public function reset()
    {
        if (request()->isGet()) {
            $data = request()->param();
            
            $info = Db::name('user')->field('id,token_time,user_nickname')->where('token', $data['token'])->find();
            
            date_default_timezone_set('PRC');
            $timediff = time() - $info['token_time'];
            $remain = $timediff / 60;
            $mins = intval($remain);
            $this->assign('erjipwd', $data['erjipwd']);
            $this->assign('time', $mins);
            $this->assign('id', $info['id']);
            return $this->fetch();
        }elseif(request()->isAjax()){
            $result = $this->request->param();

            if(empty($result['password'])){
                $arrs = array('code' => 0, 'resule' => '密码不能为空！');
                echo json_encode($arrs);
                exit;
            }
            if($result['password'] != $result['pwd']){
                $arrs = array('code' => 0, 'resule' => '两次输入不一致！');
                echo json_encode($arrs);
                exit;
            }
            if($result['erjipwd'] == 1){//修改密码
                $arrs = array(
                'user_pass'=>md5($result['password'])
                );
            }else{//修改二级密码
                $arrs = array(
                'paynum'=>md5($result['password'])
                );
            }
            
            $aa = Db::name('user')->where('id',$result['ids'])->update($arrs); 
            if($aa){
                $arrs = array('code' => 1, 'resule' => '重置密码成功！','erjipwd'=>$result['erjipwd']);
                echo json_encode($arrs);
                exit;
            }else{
                $arrs = array('code' => 0, 'resule' => '网络错误,请稍后重试！');
                echo json_encode($arrs);
                exit;
            }
        }

        
    }
    public function login()
    {


        $result = $this->request->param();
        if (request()->isAjax()) {

            if (empty($result['user_email'])) {
                $arrs = array('code' => 0, 'resule' => '手机号不能为空！');
                echo json_encode($arrs);
                exit;
            }

            $where = "user_email='" . $result['user_email'] . "' or " . "mobile='" . $result['user_email'] . "'";
            $fuser = Db::name('user')->where($where)->find();
	    

            if (!$fuser) {  
                $arrs = array('code' => 0, 'resule' => '用户名不存在！'); 
                echo json_encode($arrs);
                exit;
            }elseif ($fuser['status'] == 1) {
                $arrs = array('code' => 0, 'resule' => '账号已冻结！');
                echo json_encode($arrs);
                exit;
            } else {

                // $token = rand(10000, 99999);
                $arrs = array('code' => 1, 'resule' => '登录验证成功','userinfo'=>$fuser);
                // session('userinfo', $fuser);
                // session('token', $token);
                // $arry = array(
                //     'utoken' => $token
                // );
                
                // $fuser = Db::name('user')->where('id', $fuser['id'])->update($arry);
                echo json_encode($arrs); 
                exit;
            }


        }


    }

    public function register()
    {

        // if (request()->isAjax()) {
            $result = $this->request->param();
            $sessioninfo = session('phone'); 
            $fusers = Db::name('user')->where('mobile', $result['phone'])->find();
            if (empty($result['phone'])) {
                $arrs = array('code' => 0, 'resule' => '手机号不能为空！');
                echo json_encode($arrs);
                exit;

            }
            if (!preg_match("/^(1(([35789][0-9])|(47)))\d{8}$/", $result['phone'])) {
                $arrs = array('code' => 1, 'resule' => '手机号格式不正确！');
                echo json_encode($arrs);
                exit;
            }
            if ($fusers) {
                $arrs = array('code' => 2, 'resule' => '手机号已注册！');
                echo json_encode($arrs);
                exit;
            }
            if (empty($result['code'])) {
                $arrs = array('code' => 3, 'resule' => '验证码不能为空！');
                echo json_encode($arrs);
                exit;

            }
            // if ($sessioninfo['phonecode'] != $result ['code'] || $sessioninfo['phone'] != $result ['phone']) {
            //     $arrs = array('code' => 4, 'resule' => '验证码不正确！');
            //     echo json_encode($arrs);
            //     exit;
            // }
            if ($result['fpeople'] != '') {   
                $fuser = Db::name('user')->where('code',$result['fpeople'])->find();
                if (!$fuser) {
                    $arrs = array('code' => 5, 'resule' => '邀请人不存在！');
                    echo json_encode($arrs);
                    exit;
                }
                
            }
            if ($result['jpeople'] != '') {
                $juser = Db::name('user')->where('pcode',$result['jpeople'])->find();
                if (!$juser) {
                    $arrs = array('code' => 6, 'resule' => '节点人不存在！');
                    echo json_encode($arrs);
                    exit;
                }
                
            }

            $arrd = array( 
                'mobile' => $result['phone'],
                'parent_id' => $result['fpeople'] ? $fuser['id'] : 0,
                'point_id' => $result['jpeople'] ? $juser['id'] : 0,
                'code'=>$this->make_password(8),
                'pcode'=>$this->make_password(8),
                'created'=>date('Y-m-d H:i:s',time())
            );
            $addinfo = Db::name('user')->insert($arrd);
            if ($addinfo) {

                $arrs = array('code' => 1, 'resule' => '注册成功！');
                echo json_encode($arrs);
                exit;
            }

        // } else {
        //     $code = $_GET;

        //     if (!empty($code)) {

        //         $this->assign('code', $code['zcr']);
        //     } else {
        //         $this->assign('code', "");
        //     }

        //     return $this->fetch();
        // }

    }
    public function forgetpss(){
        if (request()->isAjax()) {
            $result = $this->request->param();
            $sessioninfo = session('phone'); 
            if ($sessioninfo['phonecode'] != $result ['code'] || $sessioninfo['phone'] != $result ['phone']) {
                $arrs = array('code' => 0, 'resule' => '验证码不正确！');
                echo json_encode($arrs);
                exit;
            }
            if (empty($result['code'])) {
                $arrs = array('code' => 0, 'resule' => '验证码不能为空！');
                echo json_encode($arrs);
                exit;

            }
            if (empty($result['phone'])) {
                $arrs = array('code' => 0, 'resule' => '手机号不能为空！');
                echo json_encode($arrs);
                exit;

            }
            if (empty($result['password'])) {
                $arrs = array('code' => 0, 'resule' => '密码不能为空！');
                echo json_encode($arrs);
                exit;

            }
            if ($result['password'] != $result['passwords']) {
                $arrs = array('code' => 0, 'resule' => '两次密码输入不一致！'); 
                echo json_encode($arrs);
                exit;

            }

            $zpwd = md5($result['password']);

            $result = Db::name('user')->where('mobile', $result['phone'])->update(['user_pass' => $zpwd]);
            if ($result) {
                $arrs = array('code'=>1,'resule'=>'修改成功！');
                echo json_encode($arrs);exit; 
            } else {
                $arrs = array('code'=>0,'resule'=>'网络错误,请稍后重试！');
                echo json_encode($arrs);exit;
            }

        } else {

            return $this->fetch();
        }
    }

    //邮箱验证码
    public function sendemail()
    {
        if (request()->isAjax()) {
             $result = $this->request->param();
             $fuser = Db::name('user')->where('user_email', $result['email'])->find();
            
            if (!$result['email']) {
                $arrs = array('code' => 0, 'resule' => '邮箱不为空！');
                echo json_encode($arrs);
                exit;
            }
            if (!preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $result['email'])) {
                $arrs = array('code' => 0, 'resule' => '邮箱格式不正确！');
                echo json_encode($arrs);
                exit;
            }

            if ($result['num'] == 1) {//注册发邮箱
                if ($fuser) {
                    $arrs = array('code' => 0, 'resule' => '邮箱已注册！');
                    echo json_encode($arrs);
                    exit;
                }
            } elseif($result['num'] == 2) {//忘记登录密码
                if (!$fuser) {
                    $arrs = array('code' => 0, 'resule' => '邮箱不存在！');
                    echo json_encode($arrs);
                    exit;
                }
            }
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
                if($result['num'] == 1){
                     $mailcontent = '您的验证码为'.$code;//邮件内容 
                }elseif($result['num'] == 2){
                     $mailcontent = '亲爱的'.$fuser['user_nickname'].'您的验证码为'.$code;//邮件内容 
                } 
               

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



    //退出登录
    public function layout() 
    {


        session('userinfo', null);
        $this->redirect('Login/login');


    }

    /**
     * 修改密码
     */
    public function rwpwd()
    {
        if (request()->isPost()) {
            $data = request()->param();

            if ($data['pwd'] != $data['pwds']) {
                $arrs = array('code'=>0,'resule'=>'密码不一致！');
                echo json_encode($arrs);exit;

            }
            if (empty($data['pwd'])) {
                $arrs = array('code'=>0,'resule'=>'密码不能为空！');
                echo json_encode($arrs);exit;
            }
            if (strlen($data['pwd']) < 6 || strlen($data['pwd']) > 18) {
                $arrs = array('code'=>0,'resule'=>'密码必须为6-18位！');
                echo json_encode($arrs);exit;

            }
            $pwd = md5($data['ypwd']);
            $userpass = session('userinfo')['user_pass'];
            if ($userpass != $pwd) {
                $arrs = array('code'=>0,'resule'=>'您输入的原始密码错误！');
                echo json_encode($arrs);exit;
            }
            $zpwd = md5($data['pwd']);

            $result = Db::name('user')->where('id', session('userinfo')['id'])->update(['user_pass' => $zpwd]);
            if ($result) {
                $arrs = array('code'=>1,'resule'=>'修改成功！');
                session('userinfo', null);
                echo json_encode($arrs);exit; 
            } else {
                $arrs = array('code'=>0,'resule'=>'网络错误,请稍后重试！');
                echo json_encode($arrs);exit;
            }
        }
        return $this->fetch();
    }
    /**
     * 修改二级密码
     */
    public function rwpwder()
    {
        if (request()->isPost()) {
            $data = request()->param();
            $userinfo = session('userinfo');
            $userinfo = Db::name('user')->where('id', $userinfo['id'])->find();
            
            if ($data['pwd'] != $data['pwds']) {
                $arrs = array('code'=>0,'resule'=>'密码不一致！');
                echo json_encode($arrs);exit;

            }
            if (empty($data['pwd'])) {
                $arrs = array('code'=>0,'resule'=>'密码不能为空！');
                echo json_encode($arrs);exit;
            }
           
            $pwd = md5($data['ypwd']);
           
            if ($userinfo['paynum'] != $pwd) {
                $arrs = array('code'=>0,'resule'=>'您输入的原始密码错误！');
                echo json_encode($arrs);exit;
            }
            $zpwd = md5($data['pwd']);

            $result = Db::name('user')->where('id', $userinfo['id'])->update(['paynum' => $zpwd]);
            if ($result) {
                $arrs = array('code'=>1,'resule'=>'修改成功！');
                echo json_encode($arrs);exit; 
            } else {
                $arrs = array('code'=>0,'resule'=>'网络错误,请稍后重试！');
                echo json_encode($arrs);exit;
            }
        }
        return $this->fetch();
    }

    public function forget_password()
    {
        if (request()->isAjax()) { 

            $result = request()->param();
            $erjipwd = $result['erjipwd'];
            $sessioninfo = session('phone'); 
            $fusers = Db::name('user')->where('mobile', $result['phone'])->find();

            if ($sessioninfo['phonecode'] != $result ['code'] || $sessioninfo['phone'] != $result ['phone']) {
                $arrs = array('code' => 0, 'resule' => '验证码不正确！');
                echo json_encode($arrs);
                exit;
            }
            if (!preg_match("/^(1(([35789][0-9])|(47)))\d{8}$/", $result['phone'])) {
                $arrs = array('code' => 0, 'resule' => '手机号格式不正确！');
                echo json_encode($arrs);
                exit;
            }
            if (!$fusers) {
                $arrs = array('code' => 0, 'resule' => '用户不存在！');
                echo json_encode($arrs);
                exit;
            }

            if (empty($result['password'])) {
                $arrs = array('code' => 0, 'resule' => '密码不能为空！');
                echo json_encode($arrs);
                exit;

            }
            if (empty($result['phone'])) {
                $arrs = array('code' => 0, 'resule' => '手机号不能为空！');
                echo json_encode($arrs);
                exit;
            }
            if (empty($result['code'])) {
                $arrs = array('code' => 0, 'resule' => '验证码不能为空！');
                echo json_encode($arrs);
                exit;
            }
            if ($result['password'] != $result['passwords']) {
                $arrs = array('code' => 0, 'resule' => '两次输入不一致！');
                echo json_encode($arrs);
                exit;
            }
            if($erjipwd == 1){
                $arrt = array(
                'user_pass'=>md5($result['password'])
                ); 
            }elseif($erjipwd == 2){
                $arrt = array(
                'paynum'=>md5($result['password'])
                ); 
            }
           
             
            $userup = Db::name('user')->where('mobile', $result['phone'])->update($arrt);
            if($userup){
                if($erjipwd == 1){
                    session('userinfo', null);
                }
                $arrs = array('code' => 1, 'resule' => '密码重置成功！','erjipwd'=>$erjipwd); 
                echo json_encode($arrs);
                exit;
            }else{
                $arrs = array('code' => 0, 'resule' => '网络异常！');
                echo json_encode($arrs);
                exit;
            }

        } else {
            $type = $this->request->param();

            $this->assign('type',$type['type']);
            return $this->fetch();
        }


    }


    

    function make_password($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    //获取充值记录入库 
    public function withdrawal(){ 
        $infolist = $_POST;
        $type = $_POST['type']; 
        $czjl = Db::name('xecharge')->where('block_num',$infolist['block_num'])->find();
        if($czjl){
            echo 3;exit; 
        }
        $userinfo = Db::name('user')->where('wallet',$infolist['memo'])->find();   
        // $userinfo = Db::name('user')->where('id',$infolist['memo'])->find();   
        //投资中的
        $arrv = array(   
            'investmentid'=>$userinfo['id'],
            'status'=>0
        );
        $eosnums = Db::name('investment')->where($arrv)->sum("eosnum"); 

        if($type == 1){//充值
            $arrf = array(
                'quantity'=>$infolist['quantity'],//金额
                'memo'=>$infolist['memo'],
                'block_num'=>$infolist['block_num'],//区块高度
                'block_time'=>$infolist['block_time'],
                'block_time_stamp'=>$infolist['block_time_stamp'],
                'trx_id'=>$infolist['trx_id']

            );
            //更新用户eos
            $arrty = array(
            'EOSbalance'=>$userinfo['EOSbalance']+$infolist['quantity'],
           );
            //总表记录
            $arrb = array( 
                 'expenses'=> '+'.$infolist['quantity'],
                 'money'=>$userinfo['EOSbalance']+$infolist['quantity']-$eosnums,
                 'type'=>2, 
                 'time'=>$infolist['block_time_stamp'],
                 'explain'=>'EOS充值', 
                 'slowid'=>$userinfo['id'],
                 'category'=>2

            );
            Db::startTrans();
             try{ 
            Db::name('user')->where('id',$userinfo['id'])->update($arrty);
            Db::name('xecharge')->insert($arrf);
            Db::name('slow')->insert($arrb);
            Db::commit();
            echo 1;
            } catch (\Exception $e) {
                // 回滚事务
                    Db::rollback();
                 echo 2; 
               }

        }else{

        }
    }

        //  发短信
    public function sendcode()
    {   
        if (request()->isAjax()) {

            $result = $this->request->param();
            $fuser = Db::name('user')->where('mobile', $result['phone'])->find();

            $phonecode = rand(100000, 999999);
            $password = md5('mybank520');
            $phone = $result['phone'];

            if (!$result['phone']) {
                $arrs = array('code' => 0, 'resule' => '手机号不为空！');
                echo json_encode($arrs);
                exit;
            }

            if (!preg_match("/^(1(([35789][0-9])|(47)))\d{8}$/", $result['phone'])) {
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

                if ($sessionuserinfo['mobile'] != $result['phone']) {
                    $arrs = array('code' => 0, 'resule' => '手机号有误！');
                    echo json_encode($arrs);
                    exit;
                }
                $content = '【STDbank】您正在修改交易密码，验证码是'.$phonecode.'切勿泄露 ';
            }
            $urls = 'https://api.smsbao.com/sms?u=mybank&p='.$password.'&m='.$phone.'&c='.$content; 
            $results = $this->hqingPost($urls);
      
        
            if($results == 0){
                session('phone', array('phonecode' => $phonecode, 'phone' => $result['phone']));
                $arrs = array('code' => 1, 'resule' => '短信已发送至手机');
                echo json_encode($arrs);
                exit;
            }else{
                $arrs = array('code' => 0, 'resule' => '网络异常，请稍后再试！');
                echo json_encode($arrs);    
            }
            
           
          
            
        }
    }

    public function ceshi(){
        $phonecode = rand(100000, 999999);
        $password = md5('mybank520');
        $phone = '15091869853';
        $urls = 'https://api.smsbao.com/sms?u=mybank&p='.$password.'&m='.$phone.'&c=【STD】您的验证码是'.$phonecode.',请妥善保管，切勿泄露 '; 
            $results = $this->hqingPost($urls);
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
