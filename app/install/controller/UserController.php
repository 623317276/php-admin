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
	   	    echo json_encode(array('code'=>0,'resule'=>'非会员不可充值'));
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
    
    // 消费方法
    public function consumption(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	$type = [1=>'微信',2=>'支付宝',3=>'现金',4=>'欠款' ];
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'receivable'){
	   		    // 应收数量
	   			$num = $num+1;
	   		}elseif($key == 'num'){
	   		    // 实收数量
	   			$num = $num+1;
	   		}elseif($key == 'cashierType'){
	   		    // 付款类型 1微信 2支付宝 3现金 4欠款 5帐号余额
	   			$num = $num+1;
	   		}elseif($key == 'goodsList'){
	   		    // 消费的商品
	   			$num = $num+1;
	   		}elseif($key == 'id'){
	   		    // 消费的用户id
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
            // 更新user表的余额字段
    	   	if($result['id'] == 1 && $result['cashierType'] == 5){
    	   	    // 非会员不能使用此方式结算
    	   	        Db::rollback();	
    	   	        echo json_encode(array('code'=>0,'resule'=>'非会员不能使用此方式结算'));
        	        exit;
    	   	    
    	   	}else{
    	   	    // 会员消费进入
    	   	    $user = Db::name('user')->where('id', $result['id'])->find();
        	   	if($result['cashierType'] == 4){
        	   	    // 欠款进入
        	   	    Db::name('user')->where('id', $result['id'])->update(['arrears' => bcadd($user['arrears'], bcsub($result['receivable'],$result['num'],2), 2)]);    
        	   	}elseif($result['cashierType'] == 5){
        	   	    // 帐号余额进入
        	   	    if($user['balance'] < $result['num']){
        	   	        Db::rollback();	
        	   	        echo json_encode(array('code'=>0,'resule'=>lang('InsufficientAccountBalance')));
            	        exit;
        	   	    }
        	   	    // 付款进入
        	   	    Db::name('user')->where('id', $result['id'])->update(['balance' => bcsub($user['balance'], $result['num'], 2)]);    
        	   	}else{
        	   	    
        	   	}
    	   	}
    	   	
            // 写入消费表
            $iData['uid'] = $result['id'];
    	   	$iData['adminId'] = $userinfos['id']; // 操作人id
    	   	$iData['goodsInfo'] = json_encode($result['goodsList']);
    	   	$iData['cashierType'] = $result['cashierType'];
    	   	$iData['cashierTypeName'] = $type[$result['cashierType']]; 
    	   	$iData['created'] = date('Y-m-d H:m:s', time());
    	   	$iData['num'] = $result['num']; // 实收数量
    	   	$iData['receivable'] = $result['receivable']; // 应收数量
    	   	$iData['remark'] = $result['remark'];
    	   	Db::name('consumption')->insert($iData); 
    	   	
            
            // 写入明细变动表
            $insertData['uid'] = $result['id'];
    	   	$insertData['num'] = $result['num'];
    	   	$insertData['receivable'] = $result['receivable'];
    	   	$insertData['type'] = 2;
    	   	$insertData['typeName'] = '消费';
    	   	$insertData['created'] = date('Y-m-d H:m:s', time());
    	   	$insertData['remark'] = $result['remark'];
    	   	$insertData['cashier'] = $userinfos['id']; // 操作人id
    	   	$insertData['cashierType'] = $type[$result['cashierType']];
    	   	Db::name('sylistbase')->insert($insertData); 
    	   	
    	   	
    	   	Db::commit();
        } catch (\Exception $e) {	
            print_r($e);
            // 回滚事务
            Db::rollback();	
            echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
            exit;
         
       }
        
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    // 还款方法
    public function repayment(){
	   	$result = $this->request->param();
	   	$num = 0;	
	   	$type = [1=>'微信',2=>'支付宝',3=>'现金' ];
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'num'){
	   		    // 总的消费数量
	   			$num = $num+1;
	   		}elseif($key == 'cashierType'){
	   		    // 付款类型 1微信 2支付宝 3现金 4欠款 5帐号余额
	   			$num = $num+1;
	   		}elseif($key == 'cashier'){
	   			$num = $num+1;
	   		}elseif($key == 'userId'){
	   		    // 还款用户的id
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
            // 更新user表的余额字段
    	   	$user = Db::name('user')->where('id', $result['userId'])->find();
    	   	Db::name('user')->where('id', $result['userId'])->update(['arrears' => bcsub($user['arrears'], $result['num'], 2)]);
    	   	
            // 写入还款表
            $iData['uid'] = $result['userId'];
    	   	$iData['adminId'] = $result['cashier']; // 操作人id
    	   	$iData['num'] = $result['num']; // 消费数量
    	   	$iData['created'] = date('Y-m-d H:m:s', time());
    	   	$iData['remark'] = $result['remark'];
    	   	Db::name('repayment')->insert($iData); 
    	   	
            
            // 写入明细变动表
            $insertData['uid'] = $result['userId'];
    	   	$insertData['num'] = $result['num'];
    	   	$insertData['type'] = 3;
    	   	$insertData['typeName'] = '还款';
    	   	$insertData['created'] = date('Y-m-d H:m:s', time());
    	   	$insertData['remark'] = $result['remark'];
    	   	$insertData['cashier'] = $result['cashier']; // 操作人id
    	   	$insertData['cashierType'] = $type[$result['cashierType']];
    	   	Db::name('sylistbase')->insert($insertData); 
    	   	
    	   	
    	   	Db::commit();
        } catch (\Exception $e) {	
            print_r($e);
            // 回滚事务
            Db::rollback();	
            echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
            exit;
         
      }
        
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
        
    }
    
    
}
