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
class HomeController extends HomeBaseController
{

	    public function _initialize()
    {
        $user = session('userinfo1');
        // print_r($user);die;
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

	//钱包接口
	public function index(){
	       // $a = $this->get_allticker();
	       // print_r($a);die;
			$result = $this->request->param();
			$num = 0;
			foreach ($result as $key => $value) {
				if($key == 'token'){
					$num = $num+1;
				}
			}
			if($num == 0){
				echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            	exit;
			}
			$sign = $this->checksign();
            $user = session('userinfo');
            $token = $result['token'];
			// $userinfo = substr($token,12);		

			$userinfos = Db::name('user')->where('token',$token)->find();
        	if(!$userinfos){	
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}
        	$userinfo = $userinfos['id'];
        	//给老用户生成新的钱包地址
        	//判断用户有没有钱包
        	
			$wallet = Db::name('wallet')->where('user_id',$userinfo)->find();
			$wallet_info = Db::name('wallet_info')->where('wallet_id',$wallet['id'])->select()->toArray();		
			if(empty($wallet_info)){		
// 			echo 1;exit;	
				//注册写入钱包表
            	// $wallets = array(
             //    	'name'=>'BC-identity',
             //    	// 'pay'=>md5(md5($pay)),
             //    	'user_id'=>$userinfo,
             //    	'created'=>time()
            	// );	
            	// $walletsid = Db::name('wallet')->insertGetId($wallets);
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
                'wallet_id'=>$wallet['id'],
                'name'=>'ETH',
                // 'private'=>$ethwallet['wallet_private_key'],
                'address'=>$ethwallet['data']['address'],

            );
            $wallet_infoss = array( 
                'wallet_id'=>$wallet['id'],
                'name'=>'STD',
                // 'private'=>$stdwallet['wallet_private_key'],    
                'address'=>$stdwallet['data']['address'],

            );
            // print_r($wallet_infoss);die;	
            Db::name('wallet_info')->insert($wallet_infos); 
            Db::name('wallet_info')->insert($wallet_infoss);



			}
			//用户钱包信息
			$ethwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'ETH'))->field('count')->find();		
			$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->field('count')->find();
			//eth价格	
			$ethprice = 1;	

			//STD行情
// 			$stdinfo = file_get_contents('http://mdncapi.bqiapp.com/api/coin/web-charts?webp=1&code=stdcoin&type=d');
// 			$stdinfo = json_decode($stdinfo,true);
// 			if($stdinfo['code'] == 200){
// 				$arrz = substr($stdinfo['value'],1);
// 				$arrz = substr($arrz,0,-1);
// 				$arrz = explode('],[',$arrz);
				
// 				foreach ($arrz as $key => $value) {
// 					$sm = explode(',',$value);
// 					$sm[0] = date('Y-m-d H:i:s',$sm[0]/1000);
// 					$arrb[1] = $sm;
// 				}
// 			}

            //STD行情
// 			@$stdinfo = file_get_contents('https://www.pickcoin.pro/api/spot/v3/instruments/STD-USDT/ticker'); // 又不行了
// 			@$stdinfo = json_decode($stdinfo,true);
//             $arrb[1][1] = isset($stdinfo['last']) ? $stdinfo['last'] : 2;
            
            
			$ethcny = $ethwallet_info['count']*$ethprice['price_usd']*7.0255;
			//std价格
// 			$stddcny = 	$stdwallet_info['count']*$arrb[1][1]*7.0255;
            $stddcny = 	$stdwallet_info['count']*get_ticket();
			$arrc = array('STD'=>sprintf("%.4f",$stdwallet_info['count']),'stdcny'=>sprintf("%.2f",$stddcny),'ETH'=>sprintf("%.4f",$ethwallet_info['count']),'ethcny'=>sprintf("%.2f",$ethcny),'Total'=>sprintf("%.2f",$ethcny+$stddcny));		
			$arrn = array(
				'code'=>1,
				'resule'=>lang('CapitalDeta'),
				'data'=>$arrc

			);
			echo json_encode($arrn);
			exit;
	}
    
	    //转账记录页面
    public function transfer_page(){
        $result = $this->request->param();
        $token = $result['token'];
		$userinfos = Db::name('user')->where('token',$token)->find();
    	if(!$userinfos){	
        	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
        	exit;
    	}
    	$userinfo = $userinfos['id'];
    	$wallet = Db::name('wallet')->where('user_id',$userinfo)->find();
		//用户钱包信息
// 		$ethwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'ETH'))->field('count')->find();		
// 		$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->field('count')->find();
		$reqPage = $result['page'];
		   	if($reqPage){
                    $fpage=$reqPage;
                    $startrow = ($fpage-1)*20; 
                }else{
                    $fpage=1;
                    $startrow = 0; 
                }
          $where['uid'] = ['eq',$userinfos['id']];
        //   TransferOutTo 转出到： type=13
        //     ToChangeInto  转入 ： type = 14
         $where['type'] = ['in',[13,14]];
         //底部列表
        $listinfo = Db::name('sylistbase')->limit(''.$startrow.',20')->where($where)->order("id Desc")->select()->toArray();
        foreach ($listinfo as $key => $value) {	
        	switch ($value['type']) {
     			case 13:
     				$listinfo[$key]['type'] = lang('TransferOutTo').$value['dfname'];	
     				$listinfo[$key]['num'] = '-'.$value['num'];

     			break;
     			case 14:
     				$listinfo[$key]['type'] = $value['dfname'].lang('ToChangeInto');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
   				default:	
     				echo "Your favorite fruit is neither apple, banana, or orange!";
			}
			$listinfo[$key]['time'] = date('Y-m-d H:i:s',$value['time']);
		}
		
        //STD行情
// 		$stdinfo = file_get_contents('http://mdncapi.bqiapp.com/api/coin/web-charts?webp=1&code=stdcoin&type=d');
// 		$stdinfo = json_decode($stdinfo,true);
// 		if($stdinfo['code'] == 200){
// 			$arrz = substr($stdinfo['value'],1);
// 			$arrz = substr($arrz,0,-1);
// 			$arrz = explode('],[',$arrz);
			
// 			foreach ($arrz as $key => $value) {
// 				$sm = explode(',',$value);
// 				$sm[0] = date('Y-m-d H:i:s',$sm[0]/1000);
// 				$arrb[1] = $sm;
// 			}
// 		}
// 		$bi = sprintf("%.4f",$stdwallet_info['count']);
// 		$cny = 	sprintf("%.2f",$stdwallet_info['count']*$arrb[1][1]*7.0255);
		$arrv = [
        // 	'number'=>$bi,	
        // 	'cny_number'=>$cny,
        	'list'=>$listinfo,
        	'page'=>$fpage
        ];
        echo json_encode(array('code'=>1,'resule'=>lang('TransferDetails'),'data'=>$arrv));
        exit;
        
    }
    
        //数字支付记录页面
    public function number_pay(){
        $result = $this->request->param();
        if(!isset($result['token'])){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;
        }
        $token = $result['token'];
		$userinfos = Db::name('user')->where('token',$token)->find();
    	if(!$userinfos){	
        	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
        	exit;
    	}
		$reqPage = isset($result['page']) ? $result['page'] : 1;
		   	if($reqPage){
                    $fpage=$reqPage;
                    $startrow = ($fpage-1)*20; 
                }else{
                    $fpage=1;
                    $startrow = 0; 
                }
          $where['uid'] = ['eq',$userinfos['id']];
          $where['type'] = 15;
          
		$listinfo = Db::name('sylistbase')->limit(''.$startrow.',20')->where($where)->order("id Desc")->select()->toArray();
        foreach ($listinfo as $key => $value) {	
        	$num_pay = Db::name('number_pay')->where(array('tb_sylistbase_id' => $value['id']))->find();
 		    if(empty($num_pay)){
 		        $num_pay['pay_amount'] = 'NA';
 		    }
 			$listinfo[$key]['type'] = lang('StdNumberPay'). $num_pay['pay_amount'] .' CNY';
 			$listinfo[$key]['num'] = '-'.$value['num'];
			$listinfo[$key]['time'] = date('Y-m-d H:i:s',$value['time']);
		}
		
        //STD行情
// 		$stdinfo = file_get_contents('http://mdncapi.bqiapp.com/api/coin/web-charts?webp=1&code=stdcoin&type=d');
// 		$stdinfo = json_decode($stdinfo,true);
// 		if($stdinfo['code'] == 200){
// 			$arrz = substr($stdinfo['value'],1);
// 			$arrz = substr($arrz,0,-1);
// 			$arrz = explode('],[',$arrz);
			
// 			foreach ($arrz as $key => $value) {
// 				$sm = explode(',',$value);
// 				$sm[0] = date('Y-m-d H:i:s',$sm[0]/1000);
// 				$arrb[1] = $sm;
// 			}
// 		}
		$arrv = [
        	'list'=>$listinfo,
        	'page'=>$fpage
        ];
        echo json_encode(array('code'=>1,'resule'=>lang('StdNumberPayDetail'),'data'=>$arrv));
        exit;
        
    }
    
	    //非小号行情
    public function quotations(){			
        $url = 'https://fxhapi.feixiaohao.com/public/v1/ticker';
        
        $result = file_get_contents($url);
        $result = json_decode($result,true);

        $arrg = array_slice($result,0,50);
        foreach ($arrg as $key => $value) {
        	$arrg[$key]['cny'] = $value['price_usd']*7.0669;
        }
        
        echo json_encode(array('code'=>1,'resule'=>lang('Market'),'data'=>$arrg));
        exit;
    }
	//存币获取用户std接口
	public function userinfo(){
			$result = $this->request->param();
			$num = 0;
			foreach ($result as $key => $value) {
				if($key == 'token'){
					$num = $num+1;
				}
			}
			if($num == 0){
				echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            	exit;
			}
			$sign = $this->checksign();
            $token = $result['token'];
			// $userinfo = substr($token,12);

			$userinfos = Db::name('user')->where('token',$token)->find();
        	if(!$userinfos){	
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}
        	$userinfo = $userinfos['id'];	
			$wallet = Db::name('wallet')->where('user_id',$userinfo)->find();
			//用户钱包信息		
			$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->field('count')->find();
			$ethwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'ETH'))->field('count')->find();
				
			echo json_encode(array('user_std'=>$stdwallet_info['count'],'user_eth'=>$ethwallet_info['count']));
			exit;	
	}
		//提币获取用户账户余额和手续费
	public function userinfos(){
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
			$sign = $this->checksign();
			$token = $result['token'];
			// $userinfo = substr($token,12);
			$userinfos = Db::name('user')->where('token',$token)->find();
        	if(!$userinfos){	
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}
        	$userinfo = $userinfos['id'];
			$type = $result['type'];//1 std 2eth
			$status = isset($result['status']) ? $result['status'] : 1;//1 std 2eth
			$sets = Db::name('set')->find();
			if($type == 1){
				$name = 'STD';  
			}else{
				$name = 'ETH';
			}
			if($status == 1){
				$fee = $sets['fee'];
			}else{
				$fee = $sets['zzfree'];	    
			}
			$wallet = Db::name('wallet')->where('user_id',$userinfo)->find();
			//用户钱包信息		
			$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>$name))->find();
			$arrm = array('user_num'=>sprintf("%.4f",$stdwallet_info['count']),'fee'=>$fee);
			$arrb = array('code'=>1,'resule'=>lang('WalletInfo'),'data'=>$arrm);
			echo json_encode($arrb);	
			exit;	
	}
	//存币操作
	public function deposit(){
		// if (request()->isAjax()) {
		 	$result = $this->request->param();
		 	$sign = $this->checksign();
		 	$num = 0;
		 	foreach ($result as $key => $value) {
		 		if($key == 'token'){
		 			$num = $num+1;
		 		}elseif($key == 'nums'){
		 			$num = $num+1;
		 		}
		 	}
		 	if($num != 2){
		 		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            	exit;
		 	}
			$token = $result['token'];
			// $userinfo = substr($token,12);
			$userinfos = Db::name('user')->where('token',$token)->find();
        	if(!$userinfos){		
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}	
        	$userinfo = $userinfos['id'];
			$wallet = Db::name('wallet')->where('user_id',$userinfo)->find();

			//用户钱包信息		
			$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->field('count')->find();
				

			// if($wallet['pay'] != md5(md5($result['pay']))){
			// 	echo json_encode(array('code'=>0,'resule'=>'支付密码不正确'));
			// 	exit;
			// }	
			if($result['nums'] < 10){	
				echo json_encode(array('code'=>0,'resule'=>lang('SaveNotLessThan').'10'));
				exit;
			}
			// 此处是因为前端4舍5入了， 导致跟查询出来的量有误差，所以换成了int
			$result['nums'] = (int)$result['nums'];
			$stdwallet_info['count'] = (int)$stdwallet_info['count'];
			if($stdwallet_info['count'] < $result['nums']){
				echo json_encode(array('code'=>0,'resule'=>'STD'.lang('BalanceInsufficient')));
				exit;
			}
			$arrg = array(
				'uid'=>$userinfo,
				'nums'=>$result['nums'],
				'time'=>time()

			);
			$arrn = array(
				'uid'=>$userinfo,
				'num'=>$result['nums'],
				'time'=>time(),
				'type'=>5

			);
			Db::startTrans();
             try{ 
            Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->setDec('count',$result['nums']);
            Db::name('cblist')->insert($arrg);
           	Db::name('sylistbase')->insert($arrn);
           		
           		
            Db::commit();
            echo json_encode(array('code'=>1,'resule'=>lang('Success')));
            	exit;
            } catch (\Exception $e) {	
                // 回滚事务
                    Db::rollback();	
                 echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
                 exit;
                 
               }
	}
	//取消存币
	public function cancel_deposit(){	
			$data = $this->request->param();
			$num = 0;
			foreach ($data as $key => $value) {
				if($key == 'token'){
					$num = $num+1;
				}elseif($key == 'id'){
					$num = $num+1;
				}
			}
			if($num != 2){
				echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
                exit;
			}
			$sign = $this->checksign();
            $token = $data['token'];
            // $userinfo = substr($token,12);
            $userinfos1 = Db::name('user')->where('token',$token)->find();
            if(!$userinfos1){      
                echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
                exit;
            }
            if($userinfos1['open'] == 2){      
                echo json_encode(array('code'=>0,'resule'=>lang('ContactAdminOpen').$data['id']));
                exit;
            }
            $userinfo = $userinfos1['id'];
			$cbinfo = Db::name("cblist")->where('id',$data['id'])->find();
			// 判断存币状态不是正常的话，不给取消
			if($cbinfo['status'] != 1){
			    echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));
			    exit;
			}
            $nuser = Db::name("wallet")->where('user_id',$cbinfo['uid'])->find();

            Db::name("cblist")->where(["id" => $data['id']])->setField('status', 0);

            $arrs = array(
                  'num'=>$cbinfo['nums'],      
                  'time'=>time(),
                  'uid'=>$cbinfo['uid'],    
                  'type'=>6
              );

            $res = Db::name('sylistbase')->insert($arrs);
            $ress = Db::name('wallet_info')->where(array('wallet_id'=>$nuser['id'],'name'=>'STD'))->setInc('count',$cbinfo['nums']);	
            if($res && $ress){
           		echo json_encode(array('code'=>1,'resule'=>lang('Success')));
           		exit;
            }else{
            	echo json_encode(array('code'=>0,'resule'=>lang('NetworkAnomaly')));
           		exit;
            }	
	}
	//新闻咨询列表
	public function newslist(){
		   		$result = $this->request->param();
		   		$num = 0;
		   		foreach ($result as $key => $value) {
		   			if($key == 'type'){
		   				$num = $num+1;
		   			}
		   		}
		   		if($num == 0){
		   			echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
           			exit;
		   		}
		   		$where['state']=['eq',1];
		    	if($result['type'] == 1){//热门
		    		 $type = 1;
		    		 $where['noticecategory']=['eq',1];
		    	}else{//最热
		    		 $type = 2;
		    		 $where['noticecategory']=['eq',6];
		    	}	
		    $noticelist = Db::name('notice')->where($where)->select()->toArray();
		    foreach ($noticelist as $key => $value) {
		    	$noticelist[$key]['time'] = date('Y-m-d H:i:s',$value['time']);
		    	
		    }		
		    echo json_encode(array('code'=>1,'resule'=>lang('NewsList'),'data'=>$noticelist));			
		    exit;
	}
	//新闻咨询详情
	public function newsinfo(){
		   	$result = $this->request->param();	
		   	$num = 0;
		   	foreach ($result as $key => $value) {
		   			if($key == 'id'){
		   				$num = $num+1;
		   			}
		   		}	
		   	if($num == 0){
		   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
           			exit;
		   	}
		    $noticelist = Db::name('notice')->where('Id',$result['id'])->find();
		   	if($noticelist){
		   		$noticelist['content'] = htmlspecialchars_decode($noticelist['content']);	

		   		$noticelist['time'] = date('Y-m-d H:i:s',$noticelist['time']);
		   	}else{	
		   		echo json_encode(array('code'=>0,'resule'=>lang('NewsNotExist')));
           			exit;
		   	}
		    
		    
		    echo json_encode(array('code'=>1,'resule'=>lang('NewsDeta'),'data'=>$noticelist));			
		    exit;
	}
	//总记录查询
	public function recordlist(){
		   	$result = $this->request->param();
		   	$num = 0;	
		   	foreach ($result as $key => $value) {
		   		if($key == 'token'){
		   			$num = $num+1;
		   		}elseif($key == 'page'){
		   			$num = $num+1;
		   		}elseif($key == 'type'){
		   			$num = $num+1;
		   		}
		   	}
		   	if($num != 3){
		   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            	exit;
		   	}
		   	$user = session('userinfo');
            $token = $result['token'];
			// $userinfo = substr($token,12);

			$userinfos = Db::name('user')->where('token',$token)->find();
    		if(!$userinfos){
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}
        	$userinfo = $userinfos['id'];
		   	$sign = $this->checksign();
		   	$reqPage = $result['page'];
		   	if($reqPage){
                    $fpage=$reqPage;
                    $startrow = ($fpage-1)*20; 
                }else{
                    $fpage=1;
                    $startrow = 0; 
                }
		   	switch ($result['type']) {
   				case 1:
     				$table = 'cblist';
     				$where['status'] = ['eq',1]; 
     				$where['uid'] = ['eq',$userinfo];	
     			break;
				case 2:
     				$table = 'sylistbase';
     				$where['type'] = ['eq',1]; 
     				$where['uid'] = ['eq',$userinfo];	

     			break;
   				case 3:
     				$table = 'sylistbase';
     				$where['type'] = ['eq',2]; 
     				$where['uid'] = ['eq',$userinfo];	

     			break;
     			case 4:
     				$table = 'sylistbase';
     				$where['type'] = ['eq',3]; 
     				$where['uid'] = ['eq',$userinfo];	

     			break;
     			case 5:
     				$table = 'czbase';
     				$where['user_id'] = ['eq',$userinfo];
     			break;
     			case 6:
     				$table = 'tbbase';
     				$where['uid'] = ['eq',$userinfo];
     			break;
   				default:	
     				echo "Your favorite fruit is neither apple, banana, or orange!";
			}
			if($result['type'] == 3 || $result['type'] == 4){
				$table = 'sylistbase';
 				$where1['type'] = ['eq',2]; 
 				$where1['uid'] = ['eq',$userinfo];
 				$where2['type'] = ['eq',3]; 
 				$where2['uid'] = ['eq',$userinfo];
 				//总推荐奖励
				$tui = Db::name($table)->where($where1)->sum('num');
				//总节点大小区奖励
				$lian = Db::name($table)->where($where2)->sum('num');
				$profit_info = array(
					'invitation'=>$tui,
					'link'=>$lian,
					'total'=>$tui+$lian
				);
				$listinfo = Db::name($table)->limit(''.$startrow.',20')->where($where)->order("id Desc")->select()->toArray();
				foreach ($listinfo as $key => $value) {
					$listinfo[$key]['num'] = '+'.$value['num'];
				}
                $profit = $profit_info;
			}else{
				$listinfo = Db::name($table)->limit(''.$startrow.',20')->where($where)->order("id Desc")->select()->toArray();
				foreach ($listinfo as $key => $value) {
					switch ($result['type']) {
           				case 1:
           					$listinfo[$key]['num'] = '-'.$value['nums'];
             			break;
        				case 2:
        					$listinfo[$key]['num'] = '+'.$value['num'];
             			break;
             			case 5:
             				$listinfo[$key]['num'] = '+'.$value['amount'];	
             			break;
             			case 6:
             				$listinfo[$key]['num'] = '-'.$value['num'];
             			break;
           				default:	
             				echo "Your favorite fruit is neither apple, banana, or orange!";
        			}
				}
                $profit = [];
			}
			foreach ($listinfo as $key => $value) {
				$listinfo[$key]['time'] = date('Y-m-d H:i:s',$value['time']);
			}
			echo json_encode(array("code"=>1,'data'=>array('list'=>$listinfo,'limit'=>20,'page'=>$reqPage),'profit'=>$profit));	
                exit;
	}
	//提币操作
	
	public function withdraw(){	
		   	$result = $this->request->param();
		   	$num = 0;
		   	foreach ($result as $key => $value) {
		   			if($key == 'type'){
		   				$num = $num+1;
		   			}elseif($key == 'nums'){
		   				$num = $num+1;
		   			}elseif($key == 'token'){
		   				$num = $num+1;
		   			}elseif($key == 'pay'){
		   				$num = $num+1;
		   			}elseif($key == 'address'){
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

			$nowtime = time();
			$today = strtotime(date("Y-m-d"),time())+7*3600;
			// if($nowtime>$today){
				
			// }else{
				
			// 	echo json_encode(array('code'=>0,'resule'=>'请在每天白天7点之后操作！'));
   //          	exit;
			// }		
    		if(!$userinfos1){	
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}
        	$userinfo = $userinfos1['id'];
        	if(empty($result['nums'])){	
        		echo json_encode(array('code'=>0,'resule'=>lang('CannotBeEmpty')));
        		exit;
        	}
        	// 判断验证码状态
                $cv = check_verify($result['mobile'], $result['code'], 4);//校验验证码
               if($cv['code'] != '1'){
                   $arrs = array('code' => 0, 'resule' => $cv['msg']);
                    echo json_encode($arrs);
                    exit;
               }
        // 	$sessioninfo = session('phone'); 		
        // 	if ($sessioninfo['phonecode'] != $result ['code'] || $sessioninfo['phone'] != $result ['mobile']) {
        //         $arrs = array('code' => 0, 'resule' => '验证码或支付密码不正确！');  
        //         echo json_encode($arrs);
        //         exit;
        //     }					

		   	$set = Db::name('set')->find();
		   	if($result['type'] == 1){	
		   		if($result['nums'] < $set['tbxedi']){
		   			echo json_encode(array('code'=>0,'resule'=>lang('AmountIsLessThanMinLimit')));
            		exit;
		   		}elseif($result['nums'] > $set['tbxemax']){
		   			echo json_encode(array('code'=>0,'resule'=>lang('AmountIsGreaterThanMaxLimit')));
            		exit;
		   		}
		   	}
		   	if($result['type'] == 1){
		   		$type = 1;
		   		$name = 'STD';
		   		$fee = $set['fee'];
		   		$types = 8;
		   	}else{
		   		$type = 2;
		   		$name = 'ETH';
		   		$fee = $result['nums']*$set['bffee']/100;
		   		$types = 11;		
		   	}	
		    		
		    $wallet = Db::name('wallet')->where('user_id',$userinfo)->find();
			//用户钱包信息		
			$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>$name))->find();
			//判断用户是否可以提币
			$userinfos = Db::name('user')->where('id',$userinfo)->find();
			//查询当天提币次数
	        $time1 = strtotime(date('Y-m-d 0:0:0'));
        	$time2 = strtotime(date('Y-m-d 23:59:59'));

        // 	$where['status'] = ['eq',1]; // 一天提币次数，不管是否审核，都算在内
        	$where['uid'] = ['eq',$userinfos1['id']];			
        	$where['time'] = ['between',[$time1,$time2]];
        	$tbnums = Db::name('tbbase')->where($where)->count();
        	$sign = $this->checksign();
        	if($tbnums>=$set['tbnum']){
        		echo json_encode(array('code'=>0,'resule'=>lang('PersonMax').$set['tbnum'].lang('Times')));	
				exit;
        	}
			if($userinfos['withed'] == 2){
				echo json_encode(array('code'=>0,'resule'=>lang('ContactAdmin')));
				exit;
			}
			if($result['nums']<$fee){	
				echo json_encode(array('code'=>0,'resule'=>lang('NotEnoughCharge')));
				exit;
			}
			if($wallet['pay'] == ""){			
				echo json_encode(array('code'=>0,'resule'=>lang('SetPayPassword')));
				exit;
			}
			if($wallet['pay'] != md5(md5($result['pay']))){
				echo json_encode(array('code'=>0,'resule'=>lang('VerifyOrPayPasswordError')));
				exit;
			}
			if($stdwallet_info['count']<$result['nums']){	
				echo json_encode(array('code'=>0,'resule'=>lang('InsufficientAccountBalance')));
				exit;
			}
			if((float)$result['nums'] > (float)$set['tibkg']){
				$status = 0;
			}else{
				$status = 3;
			}
			$arrg = array(		
				'uid'=>$userinfo,
				'type'=>$result['type'],
				'num'=>$result['nums'],
				'addr'=>$result['address'],
				'fee'=>$fee,
				'snum'=>$result['nums']-$fee,
				'status'=>$status,
				'time'=>time(),
				'finish'=>0, // 1定时任务已执行  0未执行, 定时任务扫描finish=0的数据进行提币操作

			);
			$arrv = array(	
				'uid'=>$userinfo,
				'num'=>$result['nums'],
				'type'=>$types,
				'time'=>time()
			);
			Db::startTrans();
            try{ 
            Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>$name))->setDec('count',$result['nums']);			
            $info = Db::name('tbbase')->insertGetId($arrg);	
            $infolist = Db::name('tbbase')->where('id',$info)->find();
    //         if($infolist['status'] == 3){
    //         	//走公链
				// $method = 'token_transfer';
    //             $symbol = 'STD';
    //             $arrh = array(
    //                 'symbol'=>$symbol,
    //                 'method'=>$method,
    //                 'value'=>$infolist['snum'],
    //                 'ordid'=>$infolist['id'],
    //                 'to'=>$infolist['addr']
    //             );
    //             $apiurl = new apiurl(); 
    //             $res = $apiurl->http_sign($arrh);
                
    //             $infoss = json_encode($arrh); 
    //             file_put_contents(ROOT_PATH.'tbhd.txt', $infoss."\r\n", FILE_APPEND); 
                
    //             $infoss = json_encode($res); 
    //             file_put_contents(ROOT_PATH.'tbhd.txt', $infoss."\r\n", FILE_APPEND);  
    //         }
            
            Db::name('sylistbase')->insert($arrv);			
            Db::commit();		
            if($info && $infolist){
            	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
            	exit;
            }else{
            	echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed'))); 
            	exit;
            }
           	
            
            	
            } catch (\Exception $e) {	
                // 回滚事务
                    Db::rollback();	
                 echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed'))); 
                 exit;  
               }

		
	}
	
	//转账
    public function transfer(){
        $result = $this->request->param();
        $num = 0;
        foreach ($result as $key => $value) {
            if($key == 'token'){
                $num = $num+1;
            }elseif($key == 'transferee') {//转入方
                $num = $num+1;
            }elseif($key == 'paynum') {
                $num = $num+1;
            }elseif($key == 'code') {
                $num = $num+1;
            }elseif($key == 'num') {
                $num = $num+1;
            }elseif($key == 'mobile') {
                $num = $num+1;
            }
        }
        if($num != 6){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;   
        }
        $token = $result['token'];
        $user = session('userinfo'); 
        $userinfos1 = Db::name('user')->where('token',$token)->find();
        if(empty($userinfos1)){		    
            echo json_encode(array('code'=>11,'resule'=>lang('IllegalOpera')));
            exit;
        }
        if($userinfos1['withed'] == 2){
			echo json_encode(array('code'=>0,'resule'=>lang('ContactAdmin')));
			exit;
		}
		$set = Db::name('set')->where('Id',1)->find();
		$time1 = strtotime(date('Y-m-d 0:0:0'));
        $time2 = strtotime(date('Y-m-d 23:59:59'));
        $counts = DB::name('sylistbase')->where(['uid'=>$userinfos1['id'],'type'=>13])->where("time between $time1 and $time2")->count();
        // 开通无限制转账功能，就不做转账次数的限制
        if($userinfos1['zz_vip'] != 1){
            if($counts >= $set['zz_num']){
                echo json_encode(array('code'=>0,'resule'=>lang('MaximumTransferPerDay').$set['zz_num'].lang('Times')));
    			exit;
            }
        }else{
            if($counts >= $set['zzvip_num']){
                // 把18015143209号单独开通一天可以10次转账功能 --- 2020/11/22 增加
                if($userinfos1['mobile'] == '18015143209'){
                    if($counts > 10){
                        echo json_encode(array('code'=>0,'resule'=>lang('MaximumTransferPerDay').$set['zzvip_num'].lang('Times')));
    			        exit;
                    }
                }else{
                    echo json_encode(array('code'=>0,'resule'=>lang('MaximumTransferPerDay').$set['zzvip_num'].lang('Times')));
    			    exit;
                }
                
    //             echo json_encode(array('code'=>0,'resule'=>lang('MaximumTransferPerDay').$set['zzvip_num'].lang('Times')));
    // 			exit;
            }
        }
        $zz_num = DB::name('sylistbase')->where(['uid'=>$userinfos1['id'],'type'=>13])->where("time between $time1 and $time2")->sum('num');
        
        // 开通无限制转账功能，就不做转账数量的限制
        if($userinfos1['zz_vip'] != 1){
            if($zz_num >= $set['zz_nums']){
                echo json_encode(array('code'=>0,'resule'=>lang('MaximumTransferPerDay').$set['zz_nums']));
    			exit;
            }
        }else{
            // 限制每天转账数量，需要把当天已转数量 + 当前数量 判断
            if(($zz_num + $result['num']) >= $set['zzvip_nums']){
                echo json_encode(array('code'=>0,'resule'=>lang('MaximumTransferPerDay').$set['zzvip_nums']));
    			exit;
            }
        }
        if(empty($result['transferee'])){
            echo json_encode(array('code'=>0,'resule'=>lang('ReceiverNotEmpty')));
            exit;
        }
        if(empty($result['mobile'])){
            echo json_encode(array('code'=>0,'resule'=>lang('PhoneNumberNotEmpty')));
            exit;
        }
        if(empty($result['code'])){
            echo json_encode(array('code'=>0,'resule'=>lang('VerifyCodeNotEmpty')));
            exit;
        }
        //判断转入方是否存在
        $zrf = Db::name('user')->where('mobile',$result['transferee'])->find();
        
        			
        if(empty($zrf)){ 	 
            echo json_encode(array('code'=>0,'resule'=>lang('RecipientInfoError')));
            exit;
        }
        if($zrf['id'] == $userinfos1['id']){    
            echo json_encode(array('code'=>0,'resule'=>lang('NotYourself')));
            exit;
        }
        
        if($result['num'] < 10){   		
            echo json_encode(array('code'=>0,'resule'=>lang('MustBeGreaterThan').'10！'));
            exit;
        }
        
        //转出方实际扣除
        $sj_num = $result['num'];
        //转入方到账
        $zr_dz = $result['num']-$set['zzfree'];
        $wallet = Db::name('wallet')->where('user_id',$userinfos1['id'])->find();
		//转出方用户钱包信息		
		$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->find();
		//转入方用户钱包信息	
		$zrf_wallet = Db::name('wallet')->where('user_id',$zrf['id'])->find();	

        if($sj_num > $stdwallet_info['count']){
            echo json_encode(array('code'=>0,'resule'=>lang('InsufficientAccountBalance')));
            exit;
        }
        if($wallet['pay'] != md5(md5($result['paynum']))){
			echo json_encode(array('code'=>0,'resule'=>lang('VerifyOrPayPasswordError')));
			exit;
		} 
        
		// 判断验证码状态
        $cv = check_verify($result['mobile'], $result['code'], 4);//校验验证码
       	if($cv['code'] != '1'){
            $arrs = array('code' => 0, 'resule' => $cv['msg']);
            echo json_encode($arrs);
            exit;
       	}

        $arrmm = [  
            'uid'=>$userinfos1['id'],
            'num'=>$sj_num,
            'type'=>13,
            'time'=>time(),
            'dfname'=>$zrf['mobile']
        ];
        $arrmm1 = [
            'uid'=>$zrf['id'],
            'num'=>$zr_dz,
            'type'=>14,
            'time'=>time(),
            'dfname'=>$userinfos1['mobile']
        ];
        Db::startTrans();
             try{ 
        	Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->setDec('count',$sj_num);
            Db::name('wallet_info')->where(array('wallet_id'=>$zrf_wallet['id'],'name'=>'STD'))->setInc('count',$zr_dz);
            DB::name('sylistbase')->insert($arrmm);	    
            DB::name('sylistbase')->insert($arrmm1);    
            Db::commit();
            $arrs = array('code' => 1, 'resule' => lang('Success'));
            echo json_encode($arrs);
            exit;
            } catch (\Exception $e) { 	          
                // 回滚事务
                    Db::rollback(); 
                 echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
                 exit;
                 
               }
        
    }
	public function ceshi(){
		$nowtime = time();
		$today = strtotime(date("Y-m-d"),time())+18*3600+52*60;
		if($nowtime>$today){
			echo '可以提币';exit;
		}else{
			echo '不能提币';exit;
		}
		// $time = strtotime($today);//获取到
		echo $today."<br />";
		echo date("Y-m-d H:i:s",$today)."<br />";
	}
	//非小号行情
    public function quotation(){
        $url = 'https://fxhapi.feixiaohao.com/public/v1/ticker';
        $result = file_get_contents($url);
        $result = json_decode($result,true);
            foreach ($result as $key => $value) {
                if ($value['symbol'] == 'ETH') {
                    $arrg[] = $value;
                }
            }
        return $arrg[0];
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
     
     // 动静态收益详情
     public function reward_info(){
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
		   	$user = session('userinfo');
            $token = $result['token'];

			$userinfos = Db::name('user')->where('token',$token)->find();
    		if(!$userinfos){
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}
        	$userinfo = $userinfos['id'];
        	$where['uid'] = ['eq',$userinfos['id']];
        	$where['type'] = ['in',[1,2,3]]; // 1活期奖 2推荐奖 3大小区
        	$listinfo = Db::name('sylistbase')->field(['type','sum(num) as sum'])->where($where)->order("id Desc")->group('type')->select()->toArray();
            $cb_total = $sy_total = $recommend_total = $current_total = $large_small_total = 0; // 存币总量、收益总量
        	foreach ($listinfo as $k => $v){
        	    if($v['type'] == 1){
        	       // $listinfo[$k]['name'] = '活期';
        	       $current_total = $v['sum'];
        	    }elseif($v['type'] == 2){
        	        $recommend_total = $v['sum'];
        	       // $listinfo[$k]['name'] = '推荐奖';
        	    }elseif($v['type'] == 3){
        	        $large_small_total = $v['sum'];
        	       // $listinfo[$k]['name'] = '大小区';
        	    }
        	    $sy_total += $v['sum'];
        	}
        	
        	$table = 'cblist';
 			$whe['status'] = ['eq',1]; 
 			$whe['uid'] = ['eq',$userinfo];
        	$cb_info = Db::name($table)->field(['sum(nums) as nums'])->where($whe)->find(); // 存币详情
			$cb_total = isset($cb_info['nums']) ? $cb_info['nums'] : 0;
			$lack_number = bcsub($sy_total, $cb_total * config('sy_multiple'), 4);// 需要补存币的数量
			
    		//计算大小区业绩 
            $dqarrs = [];
            $dxqusnum = [];
            $all_userinfo = Db::name('user')->field('id,point_id')->select()->toArray();
            $zuserlist = Db::name('user')->where('point_id',$userinfo)->select()->toArray();
            foreach ($zuserlist as $key => $value) {
            $dxqu = []; 
            $dxqus = []; 
                $dqyj = $this->unbrallerss($value['id'], $all_userinfo);
                $res11 = Db::name('cblist')->where(array('status'=>1))->where('uid','in',$dqyj)->select()->toArray();
                foreach ($res11 as $keys => $values) {
                    $wtime = $values['time']+24*3600;   
                    $ntime = time();
                    if($ntime >= $wtime){
                        $dxqus[] = $values['nums'];  
                    }
                }
                $dxqusnum[] = array_sum($dxqus);
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
            
        	$arrv = [
            // 	'info'=>$listinfo,
                'recommend_total' => (string)$recommend_total, // 推荐奖
                'current_total' => (string)$current_total, // 活期
                'large_small_total' => (string)$large_small_total, // 大小区
            	'sy_total' => (string)$sy_total, // 收益总量
            	'cb_total' => (string)$cb_total, // 总存币量
            	'yj_total' => (string)bcadd($maxs, $xqyj, 4), // 总业绩
            	'lack_number' => ($cb_total * config('sy_multiple') > $sy_total) ? '可得收益' : (string)abs($lack_number), // 需要补存币的数量
            ];
            echo json_encode(array('code'=>1,'resule'=>lang('IncomeDepositDetail'),'data'=>$arrv));
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
  
     //std跟eth钱包详情
     public function wallet_info(){
     		$result = $this->request->param();
		   	$num = 0;	
		   	foreach ($result as $key => $value) {
		   		if($key == 'token'){
		   			$num = $num+1;
		   		}elseif($key == 'page'){
		   			$num = $num+1;
		   		}elseif($key == 'type'){//1std 2eth
		   			$num = $num+1;
		   		}
		   	}
		   	if($num != 3){
		   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            	exit;
		   	}
		   	$user = session('userinfo');
            $token = $result['token'];
			// $userinfo = substr($token,12);

			$userinfos = Db::name('user')->where('token',$token)->find();
    		if(!$userinfos){
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}
        	$userinfo = $userinfos['id'];
        	$wallet = Db::name('wallet')->where('user_id',$userinfo)->find();
			//用户钱包信息
			$ethwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'ETH'))->field('count')->find();		
			$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->field('count')->find();


        	$reqPage = $result['page'];
		   	if($reqPage){
                    $fpage=$reqPage;
                    $startrow = ($fpage-1)*20; 
                }else{
                    $fpage=1;
                    $startrow = 0; 
                }
          $where['uid'] = ['eq',$userinfos['id']];
         if($result['type'] == 1){
         	$where['type'] = ['in',[1,2,3,7,8,9,12,13,14,15,16,17]];
         }elseif($result['type'] == 2){
         	$where['type'] = ['in',[10,11]];
         }
         //底部列表
        $listinfo = Db::name('sylistbase')->limit(''.$startrow.',20')->where($where)->order("id Desc")->select()->toArray();
        foreach ($listinfo as $key => $value) {	
        	switch ($value['type']) {
   				case 1:
     				$listinfo[$key]['type'] = lang('Current');
     				$listinfo[$key]['num'] = '+'.$value['num'];
     			break;
				case 2:
     				$listinfo[$key]['type'] = lang('Recommend');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
   				case 3:
     				$listinfo[$key]['type'] = lang('LargeAndSmallAreas');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 4:
     				$listinfo[$key]['type'] = lang('BackgroundOperation');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 5:
     				$listinfo[$key]['type'] = lang('Deposit');
     				$listinfo[$key]['num'] = '-'.$value['num'];

     			break;
     			case 6:
     				$listinfo[$key]['type'] = lang('CancelDeposit');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 7:
     				$listinfo[$key]['type'] = lang('Recharge');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 8:
     				$listinfo[$key]['type'] = lang('WithdrawMoney');
     				$listinfo[$key]['num'] = '-'.$value['num'];

     			break;
     			case 9:
     				$listinfo[$key]['type'] = lang('RegGift');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 6:
     				$listinfo[$key]['type'] = lang('CancelDeposit');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 10:
     				$listinfo[$key]['type'] = lang('Recharge');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 11:
     				$listinfo[$key]['type'] = lang('WithdrawMoney');
     				$listinfo[$key]['num'] = '-'.$value['num'];

     			break;
     			case 12:
     				$listinfo[$key]['type'] = lang('SignGift');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 13:
     				$listinfo[$key]['type'] = lang('TransferOutTo').$value['dfname'];	
     				$listinfo[$key]['num'] = '-'.$value['num'];

     			break;
     			case 14:
     				$listinfo[$key]['type'] = $value['dfname'].lang('ToChangeInto');
     				$listinfo[$key]['num'] = '+'.$value['num'];

     			break;
     			case 15:
     			    $num_pay = Db::name('number_pay')->where(array('tb_sylistbase_id' => $value['id']))->find();
     			    if(empty($num_pay)){
     			        $num_pay['pay_amount'] = 'NA';
     			    }
     				$listinfo[$key]['type'] = lang('StdNumberPay'). $num_pay['pay_amount'] .' CNY';
     				$listinfo[$key]['num'] = '-'.$value['num'];

     			break;
     			case 16:
     				$listinfo[$key]['type'] = lang('AirDropGift');
     				$listinfo[$key]['num'] = $value['num'];

     			break;
     			case 17:
     				$listinfo[$key]['type'] = lang('DecCbNum');
     				$listinfo[$key]['num'] = $value['num'];

     			break;
   				default:	
     				echo "Your favorite fruit is neither apple, banana, or orange!";
			}
			$listinfo[$key]['time'] = date('Y-m-d H:i:s',$value['time']);
		}

        //币种 数量
        // 此处修改，是为了前端传值过来的时候传的是其他语言（韩文）的值，导致报错
        if($result['type'] == 2){
            //eth价格	
			$ethprice = $this->quotation();	
			$bi = sprintf("%.4f",$ethwallet_info['count']);
			$cny = sprintf("%.2f",$ethwallet_info['count']*$ethprice['price_usd']*7.0255);
        	
        }else{
        	//STD行情
// 			$stdinfo = file_get_contents('http://mdncapi.bqiapp.com/api/coin/web-charts?webp=1&code=stdcoin&type=d');
// 			$stdinfo = json_decode($stdinfo,true);
// 			if($stdinfo['code'] == 200){
				// $arrz = substr($stdinfo['value'],1);
				// $arrz = substr($arrz,0,-1);
				// $arrz = explode('],[',$arrz);
				
				// foreach ($arrz as $key => $value) {
				// 	$sm = explode(',',$value);
				// 	$sm[0] = date('Y-m-d H:i:s',$sm[0]/1000);
				// 	$arrb[1] = $sm;
				// }
// 			}
			$bi = sprintf("%.4f",$stdwallet_info['count']);
			$cny = 	sprintf("%.2f",$stdwallet_info['count']*get_ticket());
        }
//         if($result['type'] == 1){
//         	//STD行情
// 			$stdinfo = file_get_contents('http://mdncapi.bqiapp.com/api/coin/web-charts?webp=1&code=stdcoin&type=d');
// 			$stdinfo = json_decode($stdinfo,true);
// 			if($stdinfo['code'] == 200){
// 				$arrz = substr($stdinfo['value'],1);
// 				$arrz = substr($arrz,0,-1);
// 				$arrz = explode('],[',$arrz);
				
// 				foreach ($arrz as $key => $value) {
// 					$sm = explode(',',$value);
// 					$sm[0] = date('Y-m-d H:i:s',$sm[0]/1000);
// 					$arrb[1] = $sm;
// 				}
// 			}
// 			$bi = sprintf("%.4f",$stdwallet_info['count']);
// 			$cny = 	sprintf("%.2f",$stdwallet_info['count']*$arrb[1][1]*7.0255);
//         }elseif($result['type'] == 2){
//         	//eth价格	
// 			$ethprice = $this->quotation();	
// 			$bi = sprintf("%.4f",$ethwallet_info['count']);
// 			$cny = sprintf("%.2f",$ethwallet_info['count']*$ethprice['price_usd']*7.0255);
//         }

        $arrv = [
        	'number'=>$bi,	
        	'cny_number'=>$cny,
        	'list'=>$listinfo,
        	'page'=>$fpage
        ];
        echo json_encode(array('code'=>1,'resule'=>lang('WalletDetails'),'data'=>$arrv));
        exit;
     }
     public function wheel_planting(){
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
		   	$user = session('userinfo');
            $token = $result['token'];
			// $userinfo = substr($token,12);

			$userinfos = Db::name('user')->where('token',$token)->find();
    		if(!$userinfos){
            	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            	exit;
        	}
        	$userinfo = $userinfos['id'];
        	//轮播图
        	$lbpic = Db::name('lpic')->where('status',1)->field('pic')->order('id desc')->select()->toArray();
        	foreach ($lbpic as $key => $value) {
        		$lbpic[$key]['pic'] = 'http://std.stdchain.app'.$value['pic'];
        	}
        	echo json_encode(array('code'=>1,'data'=>$lbpic));
        	exit;
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
}
