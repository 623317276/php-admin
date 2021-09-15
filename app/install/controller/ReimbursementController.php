<?php
namespace app\install\controller;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST,GET');   
header('Content-Type:text/html;Charset=utf-8');       


use cmf\controller\HomeBaseController;
use think\Db;

class ReimbursementController extends HomeBaseController
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
        
	   	$reqPage = $result['pageIndex'];
	   	if($reqPage){
                $fpage=$reqPage;
                $startrow = ($fpage-1)*$result['pageSize']; 
            }else{
                $fpage=1;
                $startrow = 0; 
            }
	   	$where = array();
	   	if(isset($result['userName'])){
	   	    $where['c.user_nickname'] = ['like', "%".$result['userName']."%"];
	   	}
	   	// tp5.0每次查询会清空查询条件 5.1已经改进这个问题
	   	$model = Db::name('reimbursement')->alias('as a')->join('adminuser b','a.operationId=b.id','left')->join('adminuser c','a.adminId=c.id','left')->field('a.*,b.user_nickname as operation_name,c.user_login,c.user_nickname')->where($where);
	   	
		$listinfo = $model->limit(''.$startrow.','.$result['pageSize'])->order("id Desc")->select()->toArray();
	   // echo Db::name('reimbursement')->getlastsql();die;
	   	$total = Db::name('reimbursement')->alias('as a')->join('adminuser b','a.operationId=b.id','left')->join('adminuser c','a.adminId=c.id','left')->field('a.*,b.user_nickname as operation_name,c.user_login,c.user_nickname')->where($where)->count();
	   	
		echo json_encode(array("code"=>1,'data'=>array('list'=>$listinfo,'limit'=>$result['pageSize'],'page'=>$reqPage,'total'=>$total)));	
        exit;
	}
    
    
}
