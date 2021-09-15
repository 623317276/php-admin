<?php
namespace app\install\controller;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST,GET');   
header('Content-Type:text/html;Charset=utf-8');       


use cmf\controller\HomeBaseController;
use think\Db;

class CarController extends HomeBaseController
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
    
    public function getList(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'pageIndex'){
	   			$num = $num+1;
	   		}elseif($key == 'pageSize'){
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 3){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}
        $token = $result['token'];

		$userinfos = Db::name('adminuser')->where('token',$token)->find();
		if(!$userinfos){
        	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
        	exit;
    	}
    	$userinfo = $userinfos['id'];
	   	$reqPage = $result['pageIndex'];
	   	if($reqPage){
                $fpage=$reqPage;
                $startrow = ($fpage-1)*$result['pageSize']; 
            }else{
                $fpage=1;
                $startrow = 0; 
            }
	   	$where = array();
	   	if(isset($result['searchName'])){
	   	    $where['u.name'] = ['like', "%".trim($result['searchName'])."%"];
	   	}
	   	if(isset($result['carNumber1']) && $result['carNumber1']){
	   	    $where['c.car_number1'] = trim($result['carNumber1']);
	   	}
	   	if(isset($result['carNumber2']) && $result['carNumber2']){
	   	    $where['c.car_number2'] = ['like', "%".trim($result['carNumber2'])."%"];
	   	}
        
		$listinfo = Db::name('car')->field('c.*,u.name')->alias('c')->join('user u','c.uid=u.id','left')->where($where)->where('deleted', 1)->limit(''.$startrow.','.$result['pageSize'])->order("id Desc")->select()->toArray();
	   	$total = Db::name('car')->field('c.*,u.name')->alias('c')->join('user u','c.uid=u.id','left')->where($where)->where('deleted', 1)->count();
	   
		echo json_encode(array("code"=>1,'data'=>array('list'=>$listinfo,'limit'=>$result['pageSize'],'page'=>$reqPage,'total'=>$total)));	
        exit;
	}
    
    public function addCar(){
	   	$result = $this->request->param();
	   //	print_r($result);die;
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'uid'){
	   			$num = $num+1;
	   		}elseif($key == 'car_number1'){
	   			$num = $num+1;
	   		}elseif($key == 'car_number2'){
	   			$num = $num+1;
	   		}elseif($key == 'brand'){
	   			$num = $num+1;
	   		}elseif($key == 'insurance_expiration_time'){
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 6){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}

	   
	   	$insertData['uid'] = $result['uid'];
	   	$insertData['car_number1'] = $result['car_number1'];
	   	$insertData['car_number2'] = $result['car_number2'];
	   	$insertData['brand'] = $result['brand'];
	   	$insertData['insurance_expiration_time'] = $result['insurance_expiration_time'];
	   	@$insertData['remark'] = $result['remark'];
	   	if(isset($result['id']) && $result['id']){
	   	    Db::name('car')->where('id', $result['id'])->update($insertData); 
	   	}else{
	   	    $insertData['created'] = date('Y-m-d H:m:s', time());
	   	    Db::name('car')->insert($insertData); 
	   	}
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    public function getCar(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
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
        $userinfos = Db::name('car')
        ->field('c.*,u.id as uid,u.name')
        ->alias('c')
        ->join('user u','c.uid=u.id','left')
        ->where('c.id',$result['id'])->where('deleted', 1)->find();
        // echo Db::name('car')->getlastsql();die;

	   	echo json_encode(array('code'=>1,'resule'=>lang('Success'), 'data' => $userinfos));
        exit;
    }
    
    public function deleteCar(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
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
        Db::name('car')->where('id',$result['id'])->update(['deleted' => 2]);

	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    public function getUserCar(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
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
        $userinfos = Db::name('car')->where('uid',$result['id'])->where('deleted', 1)->select()->toArray();
        // echo Db::name('car')->getlastsql();die;

	   	echo json_encode(array('code'=>1,'resule'=>lang('Success'), 'data' => $userinfos));
        exit;
    }
    
}
