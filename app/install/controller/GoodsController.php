<?php
namespace app\install\controller;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST,GET');   
header('Content-Type:text/html;Charset=utf-8');       


use cmf\controller\HomeBaseController;
use think\Db;

class GoodsController extends HomeBaseController
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
	   	    $where['name'] = ['like', "%".trim($result['searchName'])."%"];
	   	}
        
        // 添加一个title字段，用户穿梭看搜索使用
		$listinfo = Db::name('goods')->field('*,name as title')->where($where)->limit(''.$startrow.','.$result['pageSize'])->order("id Desc")->select()->toArray();
	   	$total = Db::name('goods')->where($where)->count();
	   
		echo json_encode(array("code"=>1,'data'=>array('list'=>$listinfo,'limit'=>$result['pageSize'],'page'=>$reqPage,'total'=>$total)));	
        exit;
	}
    
    public function addGoods(){
	   	$result = $this->request->param();
	   //	print_r($result);die;
	   	$num = 0;	
	   	foreach ($result as $key => $value) {
	   		if($key == 'token'){
	   			$num = $num+1;
	   		}elseif($key == 'name'){
	   			$num = $num+1;
	   		}elseif($key == 'price'){
	   			$num = $num+1;
	   		}elseif($key == 'status'){
	   			$num = $num+1;
	   		}elseif($key == 'remark'){
	   			$num = $num+1;
	   		}elseif($key == 'stock'){
	   			$num = $num+1;
	   		}
	   	}
	   	if($num != 6){
	   		echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
        	exit;
	   	}

	   
	   	$insertData['name'] = $result['name'];
	   	$insertData['stock'] = $result['stock'];
	   	$insertData['price'] = $result['price'];
	   	$insertData['status'] = $result['status'];
	   	@$insertData['remark'] = $result['remark'];
	   	if(isset($result['id']) && $result['id']){
	   	    Db::name('goods')->where('id', $result['id'])->update($insertData); 
	   	}else{
	   	    $insertData['created'] = date('Y-m-d H:m:s', time());
	   	    Db::name('goods')->insert($insertData); 
	   	}
	   	echo json_encode(array('code'=>1,'resule'=>lang('Success')));
        exit;
    }
    
    public function getGoods(){
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
        $userinfos = Db::name('goods')->where('id',$result['id'])->find();

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
    
    
}
