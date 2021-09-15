<?php
namespace app\install\controller;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST,GET');   
header('Content-Type:text/html;Charset=utf-8');       


use cmf\controller\HomeBaseController;
use think\Db;
use newz\apiurl;
class AdminuserController extends HomeBaseController
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
		// $userinfo = substr($token,12);

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
	   	$table = 'adminuser';
	   	$where = array();
	   	if(isset($result['searchName'])){
	   	    $where['user_login'] = ['like', "%".$result['searchName']."%"];
	   	}
	   	$model = Db::name($table)->where($where);
		$listinfo = $model->limit(''.$startrow.','.$result['pageSize'])->select()->toArray();
	   	$total = $model->where($where)->count();
		echo json_encode(array("code"=>1,'data'=>array('list'=>$listinfo,'limit'=>$result['pageSize'],'page'=>$reqPage,'total'=>$total)));	
        exit;
	}
    
    public function addUser(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'id'){
	   			$num = $num+1;
	   		}elseif($key == 'user_login'){
	   			$num = $num+1;
	   		}elseif($key == 'user_nickname'){
	   			$num = $num+1;
	   		}elseif($key == 'user_pass'){
	   			$num = $num+1;
	   		}elseif($key == 'user_status'){
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 6){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}
	   
	   	$insertData['user_login'] = $result['user_login'];
	   	$insertData['user_nickname'] = $result['user_nickname'];
	   	$insertData['user_status'] = $result['user_status'];
	   	if(isset($result['id']) && $result['id']){
	   	    if(isset($result['user_pass']) && $result['user_pass']){
	   	        $insertData['user_pass'] = cmf_password($result['user_pass']);
    	   	}
	   	    // 编辑进入
	   	    Db::name('adminuser')->where('id', $result['id'])->update($insertData); 
	   	}else{
	   	    // 新增加进入
	   	    $userinfos = Db::name('adminuser')->where('user_login',$result['user_login'])->find();
    	   	if($userinfos){
    	   	    echo json_encode(array('code'=>0,'resule'=>lang('MobileNumberOrEmailRegistered')));
            	exit;
    	   	}
    	   	if(isset($result['user_pass']) && $result['user_pass']){
	   	        $insertData['user_pass'] = cmf_password($result['user_nickname']);
    	   	}else{
    	   	    echo json_encode(array('code'=>0,'resule'=>lang('PasswordCannotBeEmpty')));
        	    exit;
    	   	}
	   	    $insertData['created'] = date('Y-m-d H:m:s', time());
	   	    Db::name('adminuser')->insert($insertData); 
	   	}
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    public function getUser(){
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
        $userinfos = Db::name('adminuser')->field('id,user_login,user_status,user_nickname')->where('id',$result['id'])->find();
	   	if(empty($userinfos)){
	   	    echo json_encode(array('code'=>0,'resule'=>lang('UserDoesNotExist')));
        	exit;
	   	}
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success'), 'data' => $userinfos));
        exit;
    }
    
    public function getAdminUser(){
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
        $adminuser = Db::name('adminuser')->field('id,user_login')->select();
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success'), 'data' => $adminuser));
        exit;
    }
    
        public function recharge(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'cashier'){
	   			$num = $num+1;
	   		}elseif($key == 'cashierType'){
	   			$num = $num+1;
	   		}elseif($key == 'num'){
	   			$num = $num+1;
	   		}elseif($key == 'remark'){
	   			$num = $num+1;
	   		}elseif($key == 'userId'){
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 6){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}
	   	if($result['num'] <= 0){
	   	    echo json_encode(array('code'=>0,'resule'=>lang('AmountIsLessThanMinLimit')));
        	exit;
	   	}
	   	Db::startTrans();
        try{ 
            // 写入明细变动表
            $insertData['uid'] = $result['userId'];
    	   	$insertData['num'] = $result['num'];
    	   	$insertData['type'] = 1;
    	   	$insertData['typeName'] = '充值';
    	   	$insertData['created'] = date('Y-m-d H:m:s', time());
    	   	$insertData['remark'] = $result['remark'];
    	   	$insertData['cashier'] = $result['cashier'];
    	   	$insertData['cashierType'] = $result['cashierType'];
    	   	Db::name('sylistbase')->insert($insertData); 
    	   	
    	   	// 更新user表的余额字段
    	   	Db::name('user')->where('id', $result['userId'])->setInc('balance' , $result['num']); 
    	   	Db::commit();
        } catch (\Exception $e) {	
            // print_r($e);
            // 回滚事务
            Db::rollback();	
            echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
            exit;
         
       }
        
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    // 报账操作
    public function reimbursement(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'adminId'){
	   			$num = $num+1;
	   		}elseif($key == 'num'){
	   			$num = $num+1;
	   		}elseif($key == 'remark'){
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 4){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}
	   	if($result['num'] <= 0){
	   	    echo json_encode(array('code'=>0,'resule'=>lang('AmountIsLessThanMinLimit')));
        	exit;
	   	}
	   	$userinfos = Db::name('adminuser')->where('token',$result['token'])->find();
    	if(!$userinfos){	
    		echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
            exit;	
    	}
    	
	   	Db::startTrans();
        try{ 
            // 写入报账表
            $insertData['adminId'] = $result['adminId'];
    	   	$insertData['num'] = $result['num'];
    	   	$insertData['created'] = date('Y-m-d H:m:s', time());
    	   	$insertData['remark'] = $result['remark'];
    	   	$insertData['operationId'] = $userinfos['id'];
    	   	Db::name('reimbursement')->insert($insertData); 
    	   	
    	   	Db::commit();
        } catch (\Exception $e) {	
            // print_r($e);
            // 回滚事务
            Db::rollback();	
            echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
            exit;
         
       }
        
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    
}
