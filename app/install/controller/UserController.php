<?php
namespace app\install\controller;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST,GET');   
header('Content-Type:text/html;Charset=utf-8');       


use app\portal\model\UserModel;
use cmf\controller\HomeBaseController;
use think\Db;
use newz\apiurl;
class UserController extends HomeBaseController
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
	   	$table = 'user';
	   	$where = array();
	   	if(isset($result['searchName'])){
	   	    $where['name'] = ['like', "%".trim($result['searchName'])."%"];
	   	}
	   	$model = Db::name($table)->where($where);
		$listinfo = $model->limit(''.$startrow.','.$result['pageSize'])->order("id asc")->select()->toArray();
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
	   		}elseif($key == 'mobile'){
	   			$num = $num+1;
	   		}elseif($key == 'name'){
	   			$num = $num+1;
	   		}elseif($key == 'id'){
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 4){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}
	   
	    if($result['id'] == 1){
	        echo json_encode(array('code'=>0,'resule'=>lang('ThisDataCannotEdited')));
        	exit;
	    }
	   	$insertData['name'] = $result['name'];
	   	$insertData['user_remark'] = $result['user_remark'];
	   	$insertData['mobile'] = $result['mobile'];
	   
	   	if(isset($result['id']) && $result['id']){
	   	    Db::name('user')->where('id', $result['id'])->update($insertData); 
	   	}else{
	   	    $insertData['created'] = date('Y-m-d H:m:s', time());
	   	    Db::name('user')->insert($insertData); 
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
        $userinfos = Db::name('user')->where('id',$result['id'])->find();
	   	if(empty($userinfos)){
	   	    echo json_encode(array('code'=>0,'resule'=>lang('UserDoesNotExist')));
        	exit;
	   	}
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success'), 'data' => $userinfos));
        exit;
    }
    
    public function getAllUser(){
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
        $userinfos = Db::name('user')->select()->toArray();
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
        $adminuser = Db::name('adminuser')->where('user_status', 1)->field('id,user_login,user_nickname')->select();
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
	   	if($result['userId'] == 1){
	   	    echo json_encode(array('code'=>0,'resule'=>'?????????????????????'));
        	exit;
	   	}
	   	Db::startTrans();
        try{ 
            // ?????????????????????
            $insertData['uid'] = $result['userId'];
    	   	$insertData['num'] = $result['num'];
    	   	$insertData['type'] = 1;
    	   	$insertData['typeName'] = '??????';
    	   	$insertData['created'] = date('Y-m-d H:m:s', time());
    	   	$insertData['remark'] = $result['remark'];
    	   	$insertData['cashier'] = $result['cashier'];
    	   	$insertData['cashierType'] = $result['cashierType'];
    	   	Db::name('sylistbase')->insert($insertData); 
    	   	
    	   	// ??????user??????????????????
    	   	Db::name('user')->where('id', $result['userId'])->setInc('balance' , $result['num']); 
    	   	Db::commit();
        } catch (\Exception $e) {	
            // print_r($e);
            // ????????????
            Db::rollback();	
            echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
            exit;
         
       }
        
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    // ????????????
    public function consumption(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	$type = [1=>'??????',2=>'?????????',3=>'??????',4=>'??????' ];
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'receivable'){
	   		    // ????????????
	   			$num = $num+1;
	   		}elseif($key == 'num'){
	   		    // ????????????
	   			$num = $num+1;
	   		}elseif($key == 'cashierType'){
	   		    // ???????????? 1?????? 2????????? 3?????? 4?????? 5????????????
	   			$num = $num+1;
	   		}elseif($key == 'goodsList'){
	   		    // ???????????????
	   			$num = $num+1;
	   		}elseif($key == 'id'){
	   		    // ???????????????id
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 6){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}
	   	if($result['num'] > $result['receivable']){
	   	    echo json_encode(array('code'=>0,'resule'=>lang('AmountIsGreaterThanMaxLimit')));
        	exit;
	   	}
	   	if($result['num'] < 0){
	   	    echo json_encode(array('code'=>0,'resule'=>lang('AmountIsLessThanMinLimit')));
        	exit;
	   	}
	   	$userinfos = Db::name('adminuser')->where('token',$result['token'])->find();
	   	Db::startTrans();
        try{
            // ??????user??????????????????
    	   	if($result['id'] == 1 && $result['cashierType'] == 5){
    	   	    // ????????????????????????????????????
    	   	        Db::rollback();	
    	   	        echo json_encode(array('code'=>0,'resule'=>'????????????????????????????????????'));
        	        exit;
    	   	    
    	   	}else{
    	   	    // ??????????????????
    	   	    $user = Db::name('user')->where('id', $result['id'])->find();
        	   	if($result['cashierType'] == 4){
        	   	    // ????????????
        	   	    Db::name('user')->where('id', $result['id'])->update(['arrears' => bcadd($user['arrears'], bcsub($result['receivable'],$result['num'],2), 2)]);    
        	   	}elseif($result['cashierType'] == 5){
        	   	    // ??????????????????
        	   	    if($user['balance'] < $result['num']){
        	   	        Db::rollback();	
        	   	        echo json_encode(array('code'=>0,'resule'=>lang('InsufficientAccountBalance')));
            	        exit;
        	   	    }
        	   	    // ????????????
        	   	    Db::name('user')->where('id', $result['id'])->update(['balance' => bcsub($user['balance'], $result['num'], 2)]);    
        	   	}else{
        	   	    
        	   	}
    	   	}
    	   	
            // ???????????????
            $iData['uid'] = $result['id'];
    	   	$iData['adminId'] = $userinfos['id']; // ?????????id
    	   	$iData['goodsInfo'] = json_encode($result['goodsList']);
    	   	$iData['cashierType'] = $result['cashierType'];
    	   	$iData['cashierTypeName'] = $type[$result['cashierType']]; 
    	   	$iData['created'] = date('Y-m-d H:m:s', time());
    	   	$iData['num'] = $result['num']; // ????????????
    	   	$iData['receivable'] = $result['receivable']; // ????????????
    	   	$iData['remark'] = $result['remark'];
    	   	Db::name('consumption')->insert($iData); 
    	   	
            
            // ?????????????????????
            $insertData['uid'] = $result['id'];
    	   	$insertData['num'] = $result['num'];
    	   	$insertData['receivable'] = $result['receivable'];
    	   	$insertData['type'] = 2;
    	   	$insertData['typeName'] = '??????';
    	   	$insertData['created'] = date('Y-m-d H:m:s', time());
    	   	$insertData['remark'] = $result['remark'];
    	   	$insertData['cashier'] = $userinfos['id']; // ?????????id
    	   	$insertData['cashierType'] = $type[$result['cashierType']];
    	   	Db::name('sylistbase')->insert($insertData); 
    	   	
    	   	
    	   	Db::commit();
        } catch (\Exception $e) {	
            print_r($e);
            // ????????????
            Db::rollback();	
            echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
            exit;
         
       }
        
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    // ????????????
    public function repayment(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	$type = [1=>'??????',2=>'?????????',3=>'??????' ];
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'num'){
	   		    // ??????????????????
	   			$num = $num+1;
	   		}elseif($key == 'cashierType'){
	   		    // ???????????? 1?????? 2????????? 3?????? 4?????? 5????????????
	   			$num = $num+1;
	   		}elseif($key == 'cashier'){
	   			$num = $num+1;
	   		}elseif($key == 'userId'){
	   		    // ???????????????id
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 5){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}
	   	if($result['num'] <= 0){
	   	    echo json_encode(array('code'=>0,'resule'=>lang('AmountIsLessThanMinLimit')));
        	exit;
	   	}
	   	$userinfos = Db::name('adminuser')->where('token',$result['token'])->find();
	   	Db::startTrans();
        try{
            // ??????user??????????????????
    	   	$user = Db::name('user')->where('id', $result['userId'])->find();
    	   	Db::name('user')->where('id', $result['userId'])->update(['arrears' => bcsub($user['arrears'], $result['num'], 2)]);
    	   	
            // ???????????????
            $iData['uid'] = $result['userId'];
    	   	$iData['adminId'] = $result['cashier']; // ?????????id
    	   	$iData['num'] = $result['num']; // ????????????
    	   	$iData['created'] = date('Y-m-d H:m:s', time());
    	   	$iData['remark'] = $result['remark'];
    	   	Db::name('repayment')->insert($iData); 
    	   	
            
            // ?????????????????????
            $insertData['uid'] = $result['userId'];
    	   	$insertData['num'] = $result['num'];
    	   	$insertData['type'] = 3;
    	   	$insertData['typeName'] = '??????';
    	   	$insertData['created'] = date('Y-m-d H:m:s', time());
    	   	$insertData['remark'] = $result['remark'];
    	   	$insertData['cashier'] = $result['cashier']; // ?????????id
    	   	$insertData['cashierType'] = $type[$result['cashierType']];
    	   	Db::name('sylistbase')->insert($insertData); 
    	   	
    	   	
    	   	Db::commit();
        } catch (\Exception $e) {	
            print_r($e);
            // ????????????
            Db::rollback();	
            echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
            exit;
         
      }
        
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
        
    }
    
    
}
