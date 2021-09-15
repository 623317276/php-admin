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
use FontLib\Table\Type\name;
use think\Db;
use app\admin\model\Menu;

class MainController extends AdminBaseController
{

    public function _initialize()
    {
        parent::_initialize();
        $res = session('ADMIN_ID');
        $rule = Db::name('role_user')->where('user_id',$res)->field('role_id')->find();
        if($rule['role_id'] == 5){
        	echo '<div style="color:red;">权限不足！</div>';
        	exit;

        }
       	
    }

    /**
     *  后台欢迎页
     */
    public function index()
    {
        //平台用户账户总额
        // $user=Db::name('user')->where('user_type',2)->sum("balance"); 
        //今日新增会员
        $time1 = strtotime(date('Y-m-d 0:0:0'));
        $time2 = strtotime(date('Y-m-d 23:59:59'));
        $today_add_users_count = Db::name('user')->select()->toArray();
        $teday_user = [];
        foreach ($today_add_users_count as $key => $value) {
            if(strtotime($value['created'])>=$time1 && strtotime($value['created']) <= $time2){
                $teday_user[] = $value;
            }
        }
        $today_add_users_count = count($teday_user);
        //会员总数
        $all_users_count = Db::name('user')->count();
        //eth总额
        $eth_num = Db::name('wallet_info')->where('name','ETH')->sum('count');
        //std总额
        $std_num = Db::name('wallet_info')->where('name','STD')->sum('count');
        //总有效存币量
        $zc = Db::name('cblist')->where('status',1)->select()->toArray();
        $newarr = [];
        foreach ($zc as $key => $value) {
            $ntime = time();
            $wtime = $value['time']+24*3600;
            if($ntime >= $wtime){
                $newarr[] = $value['nums'];
            };  
        }
        $numss = array_sum($newarr);
        //STD充值
        $std_cz = Db::name('czbase')->where(['symbol'=>'STD'])->sum('amount');
        //std提币
        $wheres['status'] = array('in', [1,3]);
        $std_tb = Db::name('tbbase')->where($wheres)->sum('num');    
        //冷钱包地址余额
        $arrh = array(
            'symbol'=>'STD',
            'address'=>'0x6B83Fa9c8a486cC3253993F74febDaf3A61bD63b',
            'method'=>'eth_token_balance',
            'tokenAddress'=>'0x5703840e9Ae9ff88C25Af351E465A163674F27b2', // 合约地址
            // 'tokenAddress'=>'0xD8DEaEdd223a2150612C5C6F33609be89d553d9B', // 合约地址
            'decimals'=>18,             
        );
        //热钱包地址
        $re = array(
            'symbol'=>'STD',
            'address'=>'0xc06b1f066a9ce833c685729f4d5bae20ebd6b099',
            'method'=>'eth_token_balance',
            'tokenAddress'=>'0x5703840e9Ae9ff88C25Af351E465A163674F27b2', // 合约地址
            // 'tokenAddress'=>'0xD8DEaEdd223a2150612C5C6F33609be89d553d9B', // 合约地址
            'decimals'=>18,             
        );
        //热钱包ETH
        $re_eth = array(
            'symbol'=>'ETH',
            'address'=>'0xc06b1f066a9ce833c685729f4d5bae20ebd6b099',
            'method'=>'eth_balance',           
        );
        
        
        $res = $this->http_sign($arrh);
        $res1 = $this->http_sign($re);
        $re_eth = $this->http_sign($re_eth);
        // var_dump($res);
        // var_dump($res1);
        // die;
        $arrg = array(
            // 'ubalance'=>$user,
            'today_add_users_count'=>$today_add_users_count,
            'all_users_count'=>$all_users_count,
            'eth_num'=>$eth_num,
            'std_num'=>$std_num,
            'numss'=>$numss,
            'std_cz'=>$std_cz,
            'std_tb'=>$std_tb,
            'lye'=>isset($res['data']['balance']) ? $res['data']['balance'] : 0,
            'rlb'=>isset($res1['data']['balance']) ? $res1['data']['balance'] : 0,
            're_eth'=>isset($re_eth['data']['balance']) ? $re_eth['data']['balance'] : 0,    
        );
        $this->assign('arrg',$arrg);
        return $this->fetch();
    }
    //获取冷热钱包余额
    public function lryu(){
        echo 1111;exit;
        var_dump($this->http_sign([
    "symbol"=>'ETH',                                   //币种(代币简称)
    "method"=>'eth_balance',                             
    'address'=>'0xc06b1f066a9ce833c685729f4d5bae20ebd6b099'     //公账
    ]));
        // $arrh = array(
        //     'symbol'=>'STD',
        //     'address'=>'0x6B83Fa9c8a486cC3253993F74febDaf3A61bD63b',
        //     'method'=>'eth_token_balance',
        //     'tokenAddress'=>'0xD8DEaEdd223a2150612C5C6F33609be89d553d9B',
        //     'decimals'=>18,             
        // );
        // $res = $this->http_sign($arrh);
        // print_r($res);die;      
    }
function http_sign($param=[]){  
    $param['appid']="2d2382818144614506b5a60eb1db1223"; 
    ksort($param);
    reset($param);
    $arg="";
    foreach($param as $key=>$val){
        $arg.=$key."=".$val."&";
    }
    $sign=trim($arg,'&')."&key=iHTMLaOpUFbAsHo64a1d8fc8c3a2d6db689d1b4ad4b3b75ItfwXacUMxvfcTU";
    $param['sign']=strtoupper(md5($sign));  

    return json_decode($this->go_curl("https://zpay.135qkl.com/api/v1","POST",$param),true);
}
    function go_curl($url, $type, $data = false,$timeout = 20, $cert_info = [],$header=[],&$err_msg = null)
{
    $type = strtoupper($type);
    if ($type == 'GET' && is_array($data)) {
        $data = http_build_query($data);
    }
    $option = array();
    if ( $type == 'POST' ) {
        $option[CURLOPT_POST] = 1;
    }
    if ($data) {
        if ($type == 'POST') {
            $option[CURLOPT_POSTFIELDS] = $data;
        } elseif ($type == 'GET') {
            $url = strpos($url, '?') !== false ? $url.'&'.$data :  $url.'?'.$data;
        }
    }
    $option[CURLOPT_URL]            = $url;
    $option[CURLOPT_FOLLOWLOCATION] = TRUE;
    $option[CURLOPT_MAXREDIRS]      = 4;
    $option[CURLOPT_RETURNTRANSFER] = TRUE;
    $option[CURLOPT_TIMEOUT]        = $timeout;
    if($header){
        $option[CURLOPT_HTTPHEADER]=$header;
    }
    //设置证书信息
    if(!empty($cert_info) && !empty($cert_info['cert_file'])) {
        $option[CURLOPT_SSLCERT]       = $cert_info['cert_file'];
        $option[CURLOPT_SSLCERTPASSWD] = $cert_info['cert_pass'];
        $option[CURLOPT_SSLCERTTYPE]   = $cert_info['cert_type'];
    }
    //设置CA
    if(!empty($cert_info['ca_file'])) {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 1;
        $option[CURLOPT_CAINFO] = $cert_info['ca_file'];
    } else {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 0;
    }
    $ch = curl_init();
    curl_setopt_array($ch, $option);
    $response = curl_exec($ch);
    $curl_no  = curl_errno($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);
    // error_log
    if($curl_no > 0) {
        if($err_msg !== null) {
            $err_msg = '('.$curl_no.')'.$curl_err;
        }
    }
    return $response;
}

    public function dashboardWidget()
    {
        $dashboardWidgets = [];
        $widgets = $this->request->param('widgets/a');
        if (!empty($widgets)) {
            foreach ($widgets as $widget) {
                if ($widget['is_system']) {
                    array_push($dashboardWidgets, ['name' => $widget['name'], 'is_system' => 1]);
                } else {
                    array_push($dashboardWidgets, ['name' => $widget['name'], 'is_system' => 0]);
                }
            }
        }

        cmf_set_option('admin_dashboard_widgets', $dashboardWidgets, true);

        $this->success('更新成功!');

    }

}
