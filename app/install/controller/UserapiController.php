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
                $arrs = array('code' => 0, 'resule' => lang('PasswordCannotBeEmpty'));
                echo json_encode($arrs);
                exit;
            }
            if($result['password'] != $result['pwd']){
                $arrs = array('code' => 0, 'resule' => lang('PasswordInconsistentInput'));
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
                $arrs = array('code' => 1, 'resule' => lang('PasswordResetSucceeded'),'erjipwd'=>$result['erjipwd']);
                echo json_encode($arrs);
                exit;
            }else{
                $arrs = array('code' => 0, 'resule' => lang('NetworkErrorPleaseTryAgainLater'));
                echo json_encode($arrs);
                exit;
            }
        }

        
    }
    
    
    public function login()
    {
    // 	$arrs = array('code' => 0, 'resule' => '维护中！');    
	   //     echo json_encode($arrs);    
	   //     exit;
	    
        $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'mobile'){
                $num = $num+1;
            }elseif($key == 'pwd'){
                $num = $num+1;
            }
        }
        if($num != 2){  
            $arrs = array('code' => 0, 'resule' => lang('IllegalOpera'));
            echo json_encode($arrs);    
            exit;
        }
            
            if (empty($result['mobile'])) {
                $arrs = array('code' => 0, 'resule' => lang('PhoneNumberOrEmailCannotBeEmpty'));
                echo json_encode($arrs);
                exit;
            };
            
            // 校验频繁登陆异常状态
            // $cl = check_login($result['mobile'], $_SERVER['REMOTE_ADDR']);
            // if($cl['code'] != '1'){
            //       $arrs = array('code' => 0, 'resule' => $cl['msg']);
            //         echo json_encode($arrs);
            //         exit;
            //   }
           
            //     // 判断验证码状态
            //     $cv = check_verify($result['mobile'], $result['code'], 1);//校验验证码
            //     // $cv['code'] = 1;
            //   if($cv['code'] != '1'){
            //       // 登陆失败写入表
            //         $msg_data['code'] = $result['code'];
            //         $msg_data['mobile'] = $result['mobile'];
            //         $msg_data['time'] = date("Y-m-d H:i:s", time());
            //         $msg_data['ip'] = $_SERVER['REMOTE_ADDR'];
            //         db("err_login")->insert($msg_data);
                    
            //       $arrs = array('code' => 0, 'resule' => $cv['msg']);
            //         echo json_encode($arrs);
            //         exit;
            //   }

       
            $where = "user_status = '1' and user_login='" . $result['mobile'] . "'";
            $fuser = Db::name('adminuser')->where($where)->find();
	    
            if (!$fuser) {  
                $arrs = array('code' => 0, 'resule' => lang('VerifyCodeOrMobilePhoneNumberDoesNotExist')); 
                echo json_encode($arrs);
                exit;
            }else {
                // print_r(cmf_password($result['pwd']));die;
                if(cmf_password($result['pwd']) != $fuser['user_pass']){
                    $arrs = array('code' => 0, 'resule' => lang('ThePasswordEnteredIsIncorrect')); 
                    echo json_encode($arrs);
                    exit;
                }
                
                $token = md5($this->make_password(12));
                $arrs = array('code' => 1, 'resule' => lang('LoginSuccessful'),'token'=>$token);   
                Db::name('adminuser')->where('user_login',$result['mobile'])->update(['token'=>$token]);
                session('userinfo', $fuser);    
                // session('phone', array('phonecode' => null));
                echo json_encode($arrs); 
                exit;
            }


    }

    public function register()
    {
            // $arrs = array('code' => 0, 'resule' => '注册功能暂时关闭');
            // echo json_encode($arrs);
            // exit;
                
            $result = $this->request->param();
            $num = 0;
            foreach ($result as $key => $value) {
                if($key == 'mobile'){
                    $num = $num+1;
                }elseif($key == 'code'){
                    $num = $num+1;
                }elseif($key == 'fpeople'){ 
                    $num = $num+1;
                }
            }
            if($num != 3){
                $arrs = array('code' => 0, 'resule' => lang('MissPara'));
                echo json_encode($arrs);
                exit;
            }
            // $sessioninfo = session('phone'); 
            $sets = Db::name('set')->find();
            // $pay = $result['pay'];//支付密码
            $fusers = Db::name('user')->where('mobile', $result['mobile'])->find();
            if (empty($result['mobile'])) {
                $arrs = array('code' => 0, 'resule' => lang('PhoneNumberOrEmailCannotBeEmpty'));
                echo json_encode($arrs);
                exit;

            }
            $regex = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
            $res = preg_match($regex, $result['mobile']);
            if ($res == 1) {
                // 邮件进入
                
            }else{
                // 手机进入
                if (!preg_match("/^(1(([3456789][0-9])|(47)))\d{8}$/", $result['mobile'])) {
                    $arrs = array('code' => 0, 'resule' => lang('IncorrectFormatOfMobilePhoneNumber'));
                    echo json_encode($arrs);
                    exit;
                }
            }
            
            
            if ($fusers) {
                $arrs = array('code' => 0, 'resule' => lang('MobileNumberOrEmailRegistered'));
                echo json_encode($arrs);
                exit;
            }
            if (empty($result['code'])) {
                $arrs = array('code' => 2, 'resule' => lang('VerifyCodeCannotBeEmpty'));
                echo json_encode($arrs);
                exit;

            }
            // if (empty($pay)) {
            //     $arrs = array('code' => 3, 'resule' => '支付密码不能为空！');
            //     echo json_encode($arrs);
            //     exit;   

            // }    
            // 判断验证码状态
                $cv = check_verify($result['mobile'], $result['code'], 2);//校验验证码
               if($cv['code'] != '1'){
                   $arrs = array('code' => 0, 'resule' => $cv['msg']);
                    echo json_encode($arrs);
                    exit;
               }
            // if ($sessioninfo['phonecode'] != $result ['code'] || $sessioninfo['phone'] != $result ['mobile']) {
            //     $arrs = array('code' => 0, 'resule' => '验证码不正确！');
            //     echo json_encode($arrs);
            //     exit;
            // }       
            if ($result['fpeople'] != '') {   
                $fuser = Db::name('user')->where('code',$result['fpeople'])->find();
                if (!$fuser) {
                    $arrs = array('code' => 0, 'resule' => lang('InviteeNotExist'));
                    echo json_encode($arrs);
                    exit;
                }
                
            }else{
                $arrs = array('code' => 0, 'resule' => lang('TheInviteeIsNotEmpty'));
                    echo json_encode($arrs);
                    exit;
            }
            if(isset($result['jpeople'])&&!empty($result['jpeople'])){
                $juser = Db::name('user')->where('pcode',$result['jpeople'])->find();
                if (!$juser) {
                    $arrs = array('code' => 0, 'resule' => lang('NodePersonDoesNotExist'));
                    echo json_encode($arrs);
                    exit;
                }
            }else{
                $juser = '';        
            }

            $arrd = array( 
                'mobile' => $result['mobile'],
                'parent_id' => $result['fpeople'] ? $fuser['id'] : 0,
                'point_id' => $juser ? $juser['id'] : $fuser['id'],  
                'code'=>$this->make_password(8),
                'pcode'=>$this->make_password(8),
                'created'=>date('Y-m-d H:i:s',time()),
                'avatar'=>'https://std.stdchain.app/avatar/img.png',    
            );
            
            $arrb = array(
                'uid'=>$fuser['id'],
                'num'=>$sets['zs'],
                'type'=>9,
                'time'=>time()
            );
            $fwallet = Db::name('wallet')->where('user_id',$fuser['id'])->find();
            
            if($sets['zs'] > 0){
                $wallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$fwallet['id'],'name'=>'STD'))->setInc('count',$sets['zs']);
                $addinfos = Db::name('sylistbase')->insert($arrb);  
            }
            $addinfo = Db::name('user')->insertGetId($arrd);
            
            //注册写入钱包表
            $wallets = array(
                'name'=>'BC-identity',
                // 'pay'=>md5(md5($pay)),
                'user_id'=>$addinfo,
                'created'=>time()
            );
            $walletsid = Db::name('wallet')->insertGetId($wallets);
            //写入用户钱包信息表
            //生成钱包地址和私钥
            $arrnew = array(    
                    'method'=>'get_address',
                    'symbol'=>'ETH'
                );
                $apiurl = new apiurl();
                
                //STD签名
                $arrnews = array(
                    'method'=>'get_address',
                    'symbol'=>'STD'
                );
                $ethwallet = $apiurl->http_sign($arrnew);
                $stdwallet = $apiurl->http_sign($arrnews);    

            $wallet_infos = array(
                'wallet_id'=>$walletsid,
                'name'=>'ETH',
                // 'private'=>$ethwallet['wallet_private_key'],
                'address'=>$ethwallet['data']['address'],

            );
            $wallet_infoss = array( 
                'wallet_id'=>$walletsid,
                'name'=>'STD',
                // 'private'=>$stdwallet['wallet_private_key'],    
                'address'=>$stdwallet['data']['address'],

            );

            Db::name('wallet_info')->insert($wallet_infos); 
            Db::name('wallet_info')->insert($wallet_infoss);
            if ($addinfo) { 

                $arrs = array('code' => 1, 'resule' => lang('Success'));    
                echo json_encode($arrs);
                exit;
            }

    }
    //忘记支付密码
    public function forgetpss(){
            $user = session('userinfo');

            // if (empty($user)) {
            //     echo json_encode(array('code'=>10,'resule'=>'重新登录！'));
            //     exit;  

            // }        
            $result = $this->request->param();
            $num = 0;
            foreach ($result as $key => $value) {
                if($key == 'token'){
                    $num = $num+1;
                }elseif($key == 'mobile'){
                    $num = $num+1;
                }elseif($key == 'code'){
                    $num = $num+1;
                }elseif($key == 'password'){
                    $num = $num+1;
                }elseif($key == 'passwords'){
                    $num = $num+1;
                }
            }
            if($num != 5){
                echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
                exit;
            }
            $token = $result['token'];
            // $userinfo = substr($token,12);
            $userinfos1 = Db::name('user')->where('token',$token)->find();
            if(!$userinfos1){        
                echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
                exit;
            }
            $userinfo = $userinfos1['id'];
            // $sessioninfo = session('phone');
            // $regex = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
            // $res = preg_match($regex, $result['mobile']);
            // if ($res == 1) {
            //     // 邮件进入
                
            // }else{
            //     // 手机进入
            //     if (!preg_match("/^(1(([3456789][0-9])|(47)))\d{8}$/", $result['mobile'])) {
            //         $arrs = array('code' => 0, 'resule' => '手机号格式不正确！');
            //         echo json_encode($arrs);
            //         exit;
            //     }
            // }
            
            if (empty($result['mobile'])) {
                $arrs = array('code' => 0, 'resule' => lang('PhoneNumberOrEmailCannotBeEmpty'));
                echo json_encode($arrs);
                exit;

            } 
            if (empty($result['code'])) {
                $arrs = array('code' => 0, 'resule' => lang('VerifyCodeCannotBeEmpty'));
                echo json_encode($arrs);
                exit;

            }
            if (empty($result['password'])) {
                $arrs = array('code' => 0, 'resule' => lang('PasswordCannotBeEmpty'));
                echo json_encode($arrs);
                exit;

            }
            
            
            // 判断验证码状态
                $cv = check_verify($result['mobile'], $result['code'], 3);//校验验证码
               if($cv['code'] != '1'){
                   $arrs = array('code' => 0, 'resule' => $cv['msg']);
                    echo json_encode($arrs);
                    exit;
               }
            // if ($sessioninfo['phonecode'] != $result ['code'] || $sessioninfo['phone'] != $result ['mobile']) {
            //     $arrs = array('code' => 0, 'resule' => '验证码不正确！');
            //     echo json_encode($arrs);
            //     exit;
            // }   
            
            
            
            if ($result['password'] != $result['passwords']) {
                $arrs = array('code' => 0, 'resule' => lang('PasswordInconsistentInput')); 
                echo json_encode($arrs);
                exit;

            }

            $zpwd = md5(md5($result['password']));

            Db::name('wallet')->where('user_id', $userinfo)->update(['pay' => $zpwd]);
            $arrs = array('code'=>1,'resule'=>lang('Success'));
            echo json_encode($arrs);exit;   
            // if ($result) {  
            //     $arrs = array('code'=>1,'resule'=>'修改成功！');
            //     echo json_encode($arrs);exit; 
            // } else {
            //     $arrs = array('code'=>0,'resule'=>'网络错误,请稍后重试！');
            //     echo json_encode($arrs);exit;
            // }
    }



    /**
     * 修改支付密码
     */
    public function rwpwd()
    {
        
            $user = session('userinfo');

            // if (empty($user)) {
            //     echo json_encode(array('code'=>10,'resule'=>'重新登录！'));
            //     exit;  

            // }    

            $data = request()->param();
            $num = 0;
            foreach ($data as $key => $value) {
                if($key == 'token'){
                    $num = $num+1;
                }elseif($key == 'pwd'){
                    $num = $num+1;
                }elseif($key == 'pwds'){
                    $num = $num+1;
                }elseif($key == 'ypwd'){
                    $num = $num+1;
                }
            }
            if($num != 4){
                echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
                exit;
            }
            $token = $data['token'];
            // $userinfo = substr($token,12);
            $userinfos1 = Db::name('user')->where('token',$token)->find();
            if(!$userinfos1){      
                echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
                exit;
            }
            $userinfo = $userinfos1['id'];
            $wallet = Db::name('wallet')->where('user_id',$userinfo)->find(); 
            if (empty($data['pwd'])) {
                $arrs = array('code'=>0,'resule'=>lang('PasswordCannotBeEmpty'));
                echo json_encode($arrs);exit;
            }
            if ($data['pwd'] != $data['pwds']) {
                $arrs = array('code'=>0,'resule'=>lang('PasswordInconsistentInput'));
                echo json_encode($arrs);exit;

            }
            
            $pwd = md5(md5($data['ypwd']));
            
            if ($wallet['pay'] != $pwd) {
                $arrs = array('code'=>0,'resule'=>lang('ThePasswordEnteredIsIncorrect'));
                echo json_encode($arrs);exit;
            }

            Db::name('wallet')->where('user_id', $userinfo)->update(['pay' => md5(md5($data['pwd']))]);
            $arrs = array('code'=>1,'resule'=>lang('Success'));
            echo json_encode($arrs);exit;         
            // if ($result) {
            //     $arrs = array('code'=>1,'resule'=>'修改成功！');
            //     echo json_encode($arrs);exit; 
            // } else {
            //     $arrs = array('code'=>0,'resule'=>'网络错误,请稍后重试！');
            //     echo json_encode($arrs);exit;
            // }
        
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
    public function recharge(){ 
        $postData = $_POST;
        $infoss = json_encode($postData); 
        file_put_contents(ROOT_PATH.'apiresult.txt', $infoss."\r\n", FILE_APPEND);   
        //验证签名  
        $arrnew = array(
                'appid'=>'1821563B1E81CDFF6365BDA4DFDA2827',
                'method'=>'get_address',
                'symbol'=>'ETH'
            );
            ksort($arrnew);
            reset($arrnew);

        $arg="";
            foreach($arrnew as $key=>$val){
                $arg.=$key."=".$val."&";
            }
        $sign=trim($arg,'&')."&key=C619F7BB24A103AC624E9BDA2A20552A";

        // if($postData['sign'] != $sign){     
        //     echo json_encode(array('code'=>0,'msg'=>'签名验证失败'));
        //     exit;
        // }   
        $wallet_info = Db::name('wallet_info')->where('address',$postData['address'])->find();
        if(!$wallet_info){
            echo json_encode(array('code'=>0,'msg'=>'不是本平台用户'));    
            exit;
        }
        //判断记录是否写过
        $czbase = Db::name('czbase')->where('txid',$postData['txid'])->find();
        if($czbase){	
        	echo json_encode(array('code'=>0,'msg'=>'记录已存在'));    
            exit;
        }
        $wallet = Db::name('wallet')->where('id',$wallet_info['wallet_id'])->find();
            //新增回调记录
        $RechargeCallBack=[
            'user_id'=>$wallet['user_id'],
            'symbol'=>$postData['symbol'],
            'addr'=>$postData['address'],
            'amount'=>$postData['balance'],
            'fee'=>$postData['fee'],
            'txid'=>$postData['txid'],
            // 'status'=>1,  //已确认
            'time'=>time(),
        ];
        if($postData['symbol'] == 'STD'){
            $type = 7;
        }elseif($postData['symbol'] == 'ETH'){
            $type = 10;
        }
        $sylist = array(
            'uid'=>$wallet['user_id'],
            'num'=>$postData['balance'],
            'type'=>$type,      
            'time'=>time()

        );
            Db::startTrans();
             try{ 
            Db::name('wallet_info')->where('address',$postData['address'])->setInc('count',$postData['balance']);           

            Db::name('czbase')->insert($RechargeCallBack);
            Db::name('sylistbase')->insert($sylist);
            Db::commit();
            echo json_encode(array('code'=>1,'msg'=>'成功！'));
            exit;
            } catch (\Exception $e) {
                // 回滚事务
                    Db::rollback();
            echo json_encode(array('code'=>0,'msg'=>'失败！'));    
            exit;
               }

        }
    //提币回调
    public function withdraw(){
        $postData = $_POST; 
        $infoss = json_encode($postData);
        file_put_contents(ROOT_PATH.'apiresult.txt', $infoss."\r\n", FILE_APPEND | LOCK_EX);   
        //验证签名	
        $arrnew = array(
                'appid'=>'1821563B1E81CDFF6365BDA4DFDA2827',
                'method'=>'get_address',
                'symbol'=>'ETH'
            );
            ksort($arrnew);
            reset($arrnew);

        $arg="";
            foreach($arrnew as $key=>$val){
                $arg.=$key."=".$val."&";
            }
        $sign=trim($arg,'&')."&key=C619F7BB24A103AC624E9BDA2A20552A";

        $sign = strtoupper(md5($sign)); 
        // if($postData['sign'] != $sign){ 
        //     echo json_encode(array('code'=>0,'msg'=>'签名验证失败'));
        //     exit;
        // }   
        if($postData['status'] == 1){//提币成功
            $info_status = Db::name('tbbase')->where('id',$postData['ordid'])->value('status');
            // 1通过 3不用审核
            // 提币成功的回调，如果当前数据是通过，或者自动提币的话，就不修改状态了
            if(!in_array($info_status, [1,3])){
                $info=Db::name('tbbase')->where('id',$postData['ordid'])->update(array('status'=>1));
            }
             
        }else{//提币失败
            $info=Db::name('tbbase')->where('id',$postData['ordid'])->update(array('status'=>0));
        }
        
     }
    

        //  发短信
    public function sendcode()
     {       
            $sets = Db::name('set')->where('Id',1)->find(); 
            $result = $this->request->param();
            // if($result['mobile'] == '15091869853'){
            //     $actionName = $this->request->action(); // 方法名
            //     echo $actionName;die;
            // }
            // if(empty($result['mobile']) || !in_array($result['mobile'], [15091869853,13106012429,13270407777])){
            //     $arrs = array('code' => 0, 'resule' => '系统维护中....');
            //     echo json_encode($arrs); exit;
            // }
        
            $num = 0;
            foreach ($result as $key => $value) {
                if($key == 'mobile'){
                    $num = $num+1;
                }elseif($key == 'num'){
                    $num = $num+1;
                }
            }
            if($num != 2){
                $arrs = array('code' => 0, 'resule' => lang('MissPara'));
                echo json_encode($arrs);
                exit;
            }
            $fuser = Db::name('user')->where('mobile', $result['mobile'])->find();

            $phonecode = rand(100000, 999999);
            $password = md5($sets['dxpass']); 
            $phone = $result['mobile'];
            $sign = $this->checksign();
            if (!$result['mobile']) {
                $arrs = array('code' => 0, 'resule' => lang('PhoneNumberOrEmailCannotBeEmpty'));
                echo json_encode($arrs);
                exit;
            }

            if ($result['num'] == 1) {//忘记密码/登录
                if (!$fuser) {
                    $arrs = array('code' => 0, 'resule' => lang('UserDoesNotExist'));
                    echo json_encode($arrs);
                    exit;
                }
                $content = '【STDbank】'.lang('YourLoginVerificationCodeIs').$phonecode.','.lang('PleaseKeepItSafe');

            } elseif($result['num'] == 2) {//注册
                if ($fuser) {
                    $arrs = array('code' => 0, 'resule' => lang('MobileNumberRegistered'));
                    echo json_encode($arrs);
                    exit;
                }
                $content = '【STDbank】'.lang('YourRegistrationVerificationCodeIs').$phonecode.','.lang('PleaseKeepItSafe');

            } elseif($result['num'] == 3) {//修改交易密码
                // if ($sessionuserinfo['mobile'] != $result['mobile']) {
                //     $arrs = array('code' => 0, 'resule' => '手机号有误！');
                //     echo json_encode($arrs);
                //     exit;
                // }
                $content = '【STDbank】'.lang('ModifyingTheTransactionPasswordTheVerificationCodeIs').$phonecode.lang('PleaseKeepItSafe');
            }elseif($result['num'] == 4) {//提币
                if (!$fuser) {  
                    $arrs = array('code' => 0, 'resule' => lang('UserDoesNotExist'));
                    echo json_encode($arrs);
                    exit;
                }
                $content = '【STDbank】'.lang('ApplyingForTransferVerificationCodeIs').$phonecode.lang('PleaseKeepItSafe');
            }

            // $urls = 'https://api.smsbao.com/sms?u='.$sets['dxusername'].'&p='.$password.'&m='.$phone.'&c='.$content; 
            
            // $results = $this->hqingPost($urls); // 原始发短信的代码
            
            // 5月23日 该服务器ip被短信宝屏蔽了？发不了短信，所以通过其他服务器做跳板发送短信验证码---临时使用
            // $results = $this->ceshi($sets['dxusername'], $password, $phone, $content);
            
            //验证是手机号还是邮箱
            $regex = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
            $res = preg_match($regex, $result['mobile']);
            if ($res == 1) {
                // 邮件进入
                $results = sendMail('STD', $content, $result['mobile']);
                $results = json_decode($results,true);
            }else{
                
                if (!preg_match("/^(1(([3456789][0-9])|(47)))\d{8}$/", $result['mobile'])) {
                    $arrs = array('code' => 0, 'resule' => lang('IncorrectFormatOfMobilePhoneNumber'));
                    echo json_encode($arrs);
                    exit;
                }
                // 手机进入
                // 刘修改为云片短信平台
                $url = "https://sms.yunpian.com/v2/sms/single_send.json";
                $params = array(
                    'apikey'   => '64cba4d511051601994af168fa75c723', //您申请的APPKEY
                    'mobile'    => $result['mobile'], //接受短信的用户手机号码
                    'text' =>'【STD】'.lang('YourVerificationCodeIs').$phonecode.'。'.lang('IfNotOperatedByYourselfPleaseIgnoreThisMessage') //您设置的模板变量，根据实际情况修改    
                );
                $results = $this->curl_request($url,$params);
                $results = json_decode($results,true);
            }
            
            
            
            // if($results == 0){
                if($results['code'] == 0){
                // 写入短信表
                $msg_data['verify'] = $phonecode;
                $msg_data['tel'] = $result['mobile'];
                $msg_data['created'] = time();
                $msg_data['type'] = !empty($result['num']) ? $result['num'] : 1; // 1登陆 2转账
                db("send_msg")->insert($msg_data);
                
                // session('phone', array('phonecode' => $phonecode, 'phone' => $result['mobile']));
                $arrs = array('code' => 1, 'resule' => lang('SmsOrEmailSentSuccessfully'));
                echo json_encode($arrs);    
                exit;
            }else{
                // $statusStr = array(
                //     "0" => "短信发送成功",
                //     "-1" => "参数不全",
                //     "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
                //     "30" => "密码错误",
                //     "40" => "账号不存在",
                //     "41" => "余额不足",
                //     "42" => "帐户已过期",
                //     "43" => "IP地址限制",
                //     "50" => "内容含有敏感词"
                //     );
                // $statusStr[$result] // 错误信息
                
                $arrs = array('code' => 0, 'resule' => lang('NetworkErrorPleaseTryAgainLater'), 'data' => $results);
                echo json_encode($arrs);    die;
            }
            
           
          
            
        // }	
    }

    // public function ceshi(){
    public function ceshi($dxusername, $password, $phone, $content){
        // 通过其他服务器做跳板发送短信验证码
        $url = 'https://www.larkt.net/Api/Login/sendMsg?u='.$dxusername.'&p='.$password.'&m='.$phone.'&c='.$content;
        $results =file_get_contents($url);
        return $results;
        
        
        
        // $phonecode = rand(100000, 999999);
        // $password = md5('sheng4747.9');
        // $phone = '15091869853';
        // $urls = 'https://api.smsbao.com/sms?u=mybank&p='.$password.'&m='.$phone.'&c=【STD】您的验证码是'.$phonecode.',请妥善保管，切勿泄露'; 
        //     $results = $this->hqingPost($urls);
        //     var_dump($results);
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
     public function curl_request($url, $postFields)
    {
        $postFields = http_build_query($postFields);
        //echo $url.'?'.$postFields;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

     public function checksign(){   
        //验证签名
        $arrnew = array(
                'appid'=>'1821563B1E81CDFF6365BDA4DFDA2827',
                'method'=>'check_sign',
                'key'=>'C619F7BB24A103AC624E9BDA2A20552A'
            );

        $arg="";
            foreach($arrnew as $key=>$val){
                $arg.=$key."=".$val."&";
            }
         
        $sign=trim($arg,'&');
        $sign = strtoupper(md5($sign)); 
        return $sign;
     }
       //版本更新
  public function edition_upd(){
        $result = $this->request->param();
        $edition = Db::name('bblist')->order('id desc')->find();
        if(isset($result['os'])&&!empty($result['os'])){ 
            
            if($result['os'] == 1){//安卓
                if($result['version'] == $edition['anbb']){
                    $editions['type'] = 0;
                }else{
                    switch ($edition['antype']) {
                case 0:
                    $editions['type'] = 0;
                break;
                case 1:
                    $editions['type'] = 1;
                break;  
                case 2:
                    $editions['type'] = 2;
                break;
            
                default:
                # code...
                break;
                }
                }
                

                $editions['Android_version'] = $edition['anbb'];
                $editions['url'] = $edition['anurl'];
                $editions['desc'] = $edition['ancontent'];
            }elseif($result['os'] == 2){//IOS
                if($result['version'] == $edition['iosbb']){    
                    $editions['type'] = 0;
                }else{
                                    switch ($edition['iostype']) {  
                case 0:
                    $editions['type'] = 0;
                break;
                case 1:
                    $editions['type'] = 1;
                break;
                case 2:
                    $editions['type'] = 2;
                break;
            
                default:
                # code...
                break;
                }
                }

                $editions['ios_version'] = $edition['iosbb'];
                $editions['url'] = $edition['iosurl'];
                $editions['desc'] = $edition['ioscontent'];
            }
        }else{
            $editions['iosurl'] = $edition['iosurl'];
            $editions['anurl'] = $edition['anurl'];
        }
        echo json_encode(array('code'=>1,'data'=>$editions));
        exit;
  }
}
