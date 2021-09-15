<?php
namespace app\install\controller;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST,GET');   
header('Content-Type:text/html;Charset=utf-8');       


use cmf\controller\HomeBaseController;
use think\Db;

class IncomeController extends HomeBaseController
{
      public function _initialize(){
    	$result = $this->request->param();
    	if(!isset($result['token'])){
    	    echo json_encode(array('code'=>0,'resule'=>lang('MissPara').' token'));
            exit;
    	}			
    	$userinfos = Db::name('adminuser')->where('token',$result['token'])->find();
    	if(!$userinfos){	
    		echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            exit;	
    	}
    
    }
    
    public function getIncome(){
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
        $data['userNum'] = Db::name('user')->count();
        
        $stime = date('Y-m-d', time()).' 00:00:00';
        $etime = date('Y-m-d', time()).' 23:59:59';
        $where['created']=['between',[$stime,$etime]];
		$data['income'] = Db::name('consumption')->where($where)->sum('num');
		
		echo json_encode(array("code"=>1,'data'=>$data));	
        exit;
	}
    
    
}
