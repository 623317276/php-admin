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
use newz\perl;
class UcenterController extends HomeBaseController
{
    function __construct(){
        parent::__construct();
        $result = $this->request->param();
        $token = $result['token'];
        $userinfos = Db::name('user')->where('token',$token)->find();
        if(empty($userinfos)){        
            echo json_encode(array('code'=>10,'resule'=>lang('LoginAgainWhenTheInfoIsExpired')));
            exit;   
        }
    }
    
    public function _initialize()
    {
        $user = session('userinfo1');
        // if (empty($user)) {
        //      echo json_encode(array('code'=>10,'resule'=>'重新登录！'));
        //      exit;      

        // } 
        $result = $this->request->param();
        $token = $result['token'];
        // $userinfo = substr($token,12);  
        $userinfos = Db::name('user')->where('token',$token)->find();
        if($userinfos['status'] == 1){        
            echo json_encode(array('code'=>10,'resule'=>lang('ReLogin')));
            exit;   
        }
    }

    //邀请好友
    public function friends(){
        $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'token'){
                $num = $num+1;
            }
        }
        if($num != 1){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;   
        }
        $token = $result['token'];
        // $userinfo = substr($token,12);
        $userinfos1 = Db::name('user')->field('code,pcode')->where('token',$token)->find();
        if(!$userinfos1){     
            echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            exit;
        }       
        $url = "http://std.stdchain.app/h5/#/pages/login/register?fpeople=".$userinfos1['code']."&jpeople=".$userinfos1['pcode'];            
        $res = array(   
            'userinfo'=>$userinfos1,
            'src'=>$url
        );
        echo json_encode(array('code'=>1,'resule'=>lang('Success'),'data'=>$res));    
        exit;
    }
    
    //我的团队
    public function teams(){   
        // echo json_encode(array('code'=>0,'resule'=>'暂未开放'));    
        // exit;
        
        $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'token'){
                $num = $num+1;
            }
        }
        if($num != 1){
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
        $zuserlist = Db::name('user')->where('point_id',$userinfo)->select()->toArray();
        
        // print_r($zuserlist);die;
        
        
        //计算大小区业绩 
        $dqarrs = [];
        $dxqusnum = [];
        
        $all_userinfo = Db::name('user')->field('id,point_id')->select()->toArray();
        foreach ($zuserlist as $key => $value) {
        $dxqu = []; 
        $dxqus = []; 
            $dqyj = $this->unbrallerss($value['id'], $all_userinfo);
            // print_r(implode(',',$dqyj));die;
            // ----------------------------------------------------------------------
            $res11 = Db::name('cblist')->where(array('status'=>1))->where('uid','in',$dqyj)->select()->toArray();
            // foreach ($dqyj as $k => $v) {
            //     $res11 = Db::name('cblist')->where(array('uid'=>$v,'status'=>1))->select()->toArray();
            //     if($res11){         
            //         $dxqu[] = $res11; 
            //     }
            // }
            // ----------------------------------------------------------------------
            // print_r($res11);
            // die;
                foreach ($res11 as $keys => $values) {
                    $wtime = $values['time']+24*3600;   
                    $ntime = time();
                    if($ntime >= $wtime){
                        $dxqus[] = $values['nums'];  
                    }
                }
                
            
            
            // foreach ($dxqu as $k => $v) {
            //     foreach ($v as $keys => $values) {
            //         $wtime = $values['time']+24*3600;    
            //         $ntime = time();
            //         if($ntime >= $wtime){
            //             $dxqus[] = $values['nums'];  
            //         }
            //     }
                
            // }
          
            $dxqusnum[] = array_sum($dxqus);
            $dqarrs[] = array('umobile'=>$value['mobile'],'yj'=>array_sum($dxqus),'pnum'=>count($dqyj));
        }
            
        
        if(!empty($dxqusnum)){
            //最大业绩区
            $maxs = max($dxqusnum);
            $xiao = array_search($maxs, $dxqusnum);
            array_splice($dxqusnum, $xiao, 1);
            //最小区业绩
            $xqyj =  array_sum($dxqusnum);
        }else{  
            $maxs = 0;
            $xqyj = 0;
        }
            // if($userinfo = '590'){
            //     echo json_encode(array('code'=>1,'max'=>$dqarrs,'small'=>0,'teamlist'=>[]));
            //     exit;
            // }
        echo json_encode(array('code'=>1,'max'=>$maxs,'small'=>$xqyj,'teamlist'=>$dqarrs));
        exit;
    }
    //签到页面显示
    public function sign_show(){
        $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'token'){
                $num = $num+1;
            }
        }
        if($num != 1){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;
        }
        $token = $result['token'];
        // $uid = substr($token,12);
        $userinfo = Db::name('user')->where('token',$token)->find();
        if(!$userinfo){
            echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            exit;
        }; 
        $sets = Db::name('set')->find();
        $nowtime = time();
        $utime = strtotime($userinfo['created'])+$sets['qdday']*24*3600;
        if($nowtime>$utime){//已过期
             $play_status = 0;
        }else{
             $play_status = 1;
        }
        //本周的签到
        $beginWeek = mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y"));
        $endWeek = mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"));
        $where['uid'] = ['eq',$userinfo['id']];
        $where['type'] = ['eq',12];
        $where['time'] = ['between',[$beginWeek,$endWeek]];
        $qdinfolist = Db::name('sylistbase')->where($where)->select()->toArray();
        //累计签到
        $where1['uid'] = ['eq',$userinfo['id']];
        $where1['type'] = ['eq',12];
        $qdcount = Db::name('sylistbase')->where($where1)->count();
        //判断当日是否签到
        $time1 = strtotime(date('Y-m-d 0:0:0'));
        $time2 = strtotime(date('Y-m-d 23:59:59'));
        $where2['type'] = ['eq',12];
        $where2['uid'] = ['eq',$userinfo['id']];
        $where2['time'] = ['between',[$time1,$time2]];
        $qdinfo = Db::name('sylistbase')->where($where2)->find();
        if($qdinfo){
            $status = 1;
        }else{
            $status = 0;
        }
        $weekarray=array("7","1","2","3","4","5","6");
        $newarr = [];
        foreach ($qdinfolist as $key => $value) {
            // $arrv = array($value['id']=>$weekarray[date("w",$value['time'])]);
            $arrv = $weekarray[date("w",$value['time'])];
            $newarr[] = $arrv;
        }
        echo json_encode(array('code'=>1,'resule'=>lang('CheckInRecord'),'data'=>$newarr,'count'=>$qdcount,'status'=>$status,'play_status'=>$play_status));
        exit;

    }
    //签到送币
    public function sign_in(){
        $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'token'){
                $num = $num+1;
            }
        }
        if($num != 1){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;
        }
        $sets = Db::name('set')->find();
        $token = $result['token'];
        // $uid = substr($token,12);
        $userinfo = Db::name('user')->where('token',$token)->find();
        if(!$userinfo){
            echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            exit;
        }   
        $u_wallet = Db::name('wallet')->where('user_id',$userinfo['id'])->find();
        $nowtime = time();
        $utime = strtotime($userinfo['created'])+$sets['qdday']*24*3600;
        $time1 = strtotime(date('Y-m-d 0:0:0'));
        $time2 = strtotime(date('Y-m-d 23:59:59'));
        $where1['type'] = ['eq',12];
        $where1['uid'] = ['eq',$userinfo['id']];
        $where1['time'] = ['between',[$time1,$time2]];
        $qdinfo = Db::name('sylistbase')->where($where1)->find();
        if($qdinfo){        
             echo json_encode(array('code'=>0,'resule'=>lang('signInOnceDay')));
             exit;
        }   
        if($nowtime>$utime){//已过期
             echo json_encode(array('code'=>0,'resule'=>lang('MineExpired')));
             exit;
        }else{
            $arrh = array(
                'uid'=>$userinfo['id'],
                'num'=>$sets['qds'],
                'type'=>12,
                'time'=>time()
            );
            Db::startTrans();
             try{ 
            Db::name('wallet_info')->where(array('wallet_id'=>$u_wallet['id'],'name'=>'STD'))->setInc('count',$sets['qds']);
            Db::name('sylistbase')->insert($arrh);
            Db::commit();
                echo json_encode(array('code'=>1,'resule'=>lang('Success')));
                exit;   
            } catch (\Exception $e) {   
                // 回滚事务
                    Db::rollback(); 
                 echo json_encode(array('code'=>0,'resule'=>lang('Failed')));   
               }
        }

    }
    //服务协议
    public function greement(){
        $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'token'){
                $num = $num+1;
            }elseif($key == 'type'){
                $num = $num+1;
            }
        }
        if($num != 2){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;
        }
        $sets = Db::name('set')->find();
        $token = $result['token'];
        // $uid = substr($token,12);
        $userinfo = Db::name('user')->where('token',$token)->find();
        if(!$userinfo){
            echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            exit;
        } 
        if($result['type'] == 1){
            $ids = 1;
        }elseif($result['type'] == 2){
            $ids = 2;
        }
        $list = Db::name('tkuan')->where('id',$ids)->find();
        $list['content'] = htmlspecialchars_decode($list['content']);
        $list['time'] = date('Y-m-d H:i:s',$list['time']);  

        echo json_encode(array('code'=>1,'resule'=>lang('Success'),'data'=>$list));
        exit;
    }
    public function user_info(){
        $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'token'){
                $num = $num+1;
            }
        }
        if($num != 1){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;
        }
        $sets = Db::name('set')->find();
        $token = $result['token'];
        // $uid = substr($token,12);
        $userinfo = Db::name('user')->where('token',$token)->find();
        if(!$userinfo){
            echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            exit;
        } 
        echo json_encode(array('code'=>1,'resule'=>lang('UserInfo'),'data'=>$userinfo));
        exit;
    }
    

public function unbrallerss($id, $userinfo){
    // $id = $_GET['id'];
      
    $children = $temp = $money = array();   
    // $ones = Db::name('user')->where('id',$id)->find();
    $children[] = $id;

    // $userinfo = Db::name('user')->select()->toArray();
    $this->findBottoms($userinfo, $id, $children);
    // $investment = Db::name('investment')->select()->toArray();
    return $children; 
    }
    public function findBottoms($userinfo, $id, &$children){
    if(!$id){
            return 'parent_id is null';
        }
        
        foreach ($userinfo as $key => $value) {
           if($value['point_id'] == $id){
                $children[] = $value['id'];
                $this->findBottoms($userinfo, $value['id'], $children);
           }
        }

        return $children;
  }
  //收款地址
  public function receivables_add(){
    //   echo json_encode(array('code'=>0,'resule'=>'该功能暂时关闭！'));
    //         exit;
    // echo 111;exit;  
    $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'token'){
                $num = $num+1;
            }elseif($key == 'type'){
                $num = $num+1;
            }
        }
        if($num != 2){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;
        }
        $sets = Db::name('set')->find();
        $token = $result['token'];
        // $uid = substr($result['token'],12);
        $userinfo = Db::name('user')->where('token',$token)->find();
        
        if(!$userinfo){
            echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            exit;
        } 

        //没有钱包给用户生成钱包
        $wallet = Db::name('wallet')->where('user_id',$userinfo['id'])->find();
        $wallet_info = Db::name('wallet_info')->where(['wallet_id'=>$wallet['id'],'name'=>'STD'])->find();  
        // echo json_encode(array('code'=>0,'resule'=>'维护中！'));
        //     exit;   
        if($wallet_info['address'] == ''){
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
                'address'=>$ethwallet['data']['address'],

            );
            $wallet_infoss = array(  
                'address'=>$stdwallet['data']['address'],

            );
            // print_r($wallet_infoss);die;        
             Db::name('wallet_info')->where(['wallet_id'=>$wallet['id'],'name'=>'STD'])->update($wallet_infoss);
             Db::name('wallet_info')->where(['wallet_id'=>$wallet['id'],'name'=>'ETH'])->update($wallet_infos);
        }
        $wallet = Db::name('wallet')->where('user_id',$userinfo['id'])->find();
        //判断有没有钱包地址，如果没有加地址
        $std_wallet = Db::name('wallet_info')->where(['name'=>'STD','wallet_id'=>$wallet['id']])->find();
        if($std_wallet['address'] == ""){       
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
                    'address'=>$ethwallet['data']['address'],

                );
                $wallet_infoss = array(
                    'address'=>$stdwallet['data']['address'],

                );

                Db::name('wallet_info')->where(['name'=>'ETH','wallet_id'=>$wallet['id']])->update($wallet_infos); 
                Db::name('wallet_info')->where(['name'=>'STD','wallet_id'=>$wallet['id']])->update($wallet_infoss);     
                }



        $where['wallet_id'] = ['eq',$wallet['id']];
        if($result['type'] == 1){//std钱包
            $where['name'] = ['eq','STD'];
        }elseif($result['type'] == 2){
            $where['name'] = ['eq','ETH'];
        }   
        $wallet_info = Db::name('wallet_info')->where($where)->field('address')->find();
        echo json_encode(array('code'=>1,'resule'=>lang('Success'),'data'=>$wallet_info));
        exit;

  } 
  
  public function ceshi1(){
        set_time_limit(600);
        $set = Db::name('set')->find();
        $tjone = $set['tjone']/100;//推荐第一种
        $tjtwo = $set['tjtwo']/100;//推荐第二种
        $tjsan = $set['tjsan']/100;//推荐第三种
        $dxone = explode('|', $set['dxone']);//大小区第一种
        $dxtwo = explode('|', $set['dxtwo']);//大小区第二种
        $dxsan = explode('|', $set['dxsan']);//大小区第三种

        $userinfo = Db::name('user')->select()->toArray();  
        $time1 = strtotime(date('Y-m-d 0:0:0'));
        $time2 = strtotime(date('Y-m-d 23:59:59'));
        
        foreach ($userinfo as $key => $value) {
            $value['id'] = 1847;
            //计算自身当天的活期收益
            $where1['uid'] = $value['id'];
            $where1['type'] = 1;    
            $where1['time'] = ['between',[$time1,$time2]];       
            $zishq = Db::name('sylistbase')->where($where1)->sum('num');

            $nuser = Db::name('wallet')->where('user_id',$value['id'])->find();
            //钱包信息
            $wallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$nuser['id'],'name'=>'STD'))->find();

            //计算直推人员
            $ztuserlist = Db::name('user')->where('parent_id',$value['id'])->select()->toArray(); 
        
            $ucblist = Db::name('cblist')->where(array('status'=>1,'uid'=>$value['id']))->select()->toArray();
            if($ucblist){
                $karr = [];
                foreach ($ucblist as $k => $v) {
                    $ntime = time();
                    $wtime = $v['time']+24*3600; 
                    // $wtime = $v['time']+1;
                    if($ntime >= $wtime){//自身存币满24小时
                        $karr[] = $v['nums'];
                    }
                }
                //用户的存币量
                $uc = array_sum($karr);

                if($uc >=100 && $uc <= 999){
                        $lx = $tjone;
                        $xe = $dxone['1'];
                }elseif($uc >=1000 && $uc <= 9999){
                        $lx = $tjtwo;
                        $xe = $dxtwo['1'];
                }elseif($uc >=10000){
                        $lx = $tjsan; 
                        $xe = $dxsan['1'];  
                }
                $sy = [];
                foreach ($ztuserlist as $k => $val) {
                    $where['uid'] = $val['id'];
                    $where['type'] = 1;
                    $where['time'] = ['between',[$time1,$time2]];       
                    $sxsy = Db::name('sylistbase')->where($where)->sum('num');
                    if($sxsy>0){    
                        $sy[] = $sxsy;
                    }
                }

                //推荐总收益
                $tjsy = array_sum($sy);
                $tjsy = $tjsy*$lx;
                if($tjsy >= $xe){
                    $tjsy = $xe;
                }else{
                    $tjsy = $tjsy;
                }
                if($tjsy>0){
                    $hqjtj = $tjsy+$zishq;//活期收益+推荐收益
                    if($hqjtj<$uc){
                        $dsy = $tjsy;
                    }else{
                        $dsy = $uc-$zishq;
                    }
                    print_r($dsy);die;         
                    $arrs = array(
                        'num'=>$dsy,
                        'time'=>time(),
                        'uid'=>$value['id'],    
                        'type'=>2
                    ); 
                    $moneya = $wallet_info['count']+$dsy;
                    $arrt = array(
                        'count'=>$moneya    
                    );
                      
                } 
            
              }
          }
          
  } 

}
