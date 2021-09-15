<?php
namespace app\install\controller;

use app\admin\model\ThemeModel;
use think\Controller;
use think\Db;

class NumberpayController extends Controller
{
    // 创建订单
    // https://std.stdchain.app/install/Numberpay/createOrder
    // qrcode 扫描支付宝二维码的链接
    // token
    // pay_amount 支付金额
    // paynum 支付密码
    public function createOrder()
    {  
        $param = request()->param();
        // 参数默认值，测试时使用
        // $param['qrcode'] = isset($param['qrcode']) ? $param['qrcode'] : 'https://qr.alipay.com/fkx05949kgp4tjxkz7wzbef?t=160050192400000';
        // $param['token'] = isset($param['token']) ? $param['token'] : 'f77967fb7264d85d5edf67d90bcc449a';
        // $param['pay_amount'] = isset($param['pay_amount']) ? $param['pay_amount'] : '0.01';
        // $param['paynum'] = isset($param['paynum']) ? $param['paynum'] : '123123';
        
        if(!isset($param['qrcode']) || !isset($param['token']) || !isset($param['pay_amount']) || !isset($param['paynum'])){
            echo json_encode(array('code'=>0,'resule'=>lang('MissPara')));
            exit;
        }
		$userinfos = Db::name('user')->where('token',$param['token'])->find();
		if(!$userinfos){
        	echo json_encode(array('code'=>0,'resule'=>lang('IllegalOpera')));
        	exit;
    	}
    	$userinfo = $userinfos['id'];
    	
        // if($userinfo != 2712){
        //     echo json_encode(array('code'=>0,'resule'=>'暂未开放'));
        //     exit;    
        // }
        
        // 判断是否开启数字支付功能
        if($userinfos['number_pay'] == 2){
            echo json_encode(array('code'=>0,'resule'=>'该账号暂未开放该功能'));
            exit;    
        }else{
            // 判断是否超过每日数字支付限额
            $set = Db::name('set')->where('Id',1)->find();
    		$time1 = strtotime(date('Y-m-d 0:0:0'));
            // $time1 = strtotime(date('2020-09-20 0:0:0'));
            $time2 = strtotime(date('Y-m-d 23:59:59'));
            $sylistbase = DB::name('sylistbase')->where(['uid'=>$userinfos['id'],'type'=>15])->where("time between $time1 and $time2")->select()->toArray();
            $sylistbase_ids = array_values(array_column($sylistbase, 'id'));
            $num = 0;
            foreach ($sylistbase_ids as $v){
                $info = DB::name('number_pay')->where(['tb_sylistbase_id'=>$v])->find();
                if(!empty($info)){
                    $num += $info['pay_amount'];
                }
            }
            // 判断是否超过每日数字支付限额
            if(($num + $param['pay_amount']) > $set['number_pay']){
                echo json_encode(array('code'=>0,'resule'=>lang('MaximumTransferPerDay').$set['number_pay']));
    			exit;
            }
        }
        
        
        
    	$wallet = Db::name('wallet')->where('user_id',$userinfo)->find();
    	// 判断支付密码是否正确
    	if($wallet['pay'] != md5(md5($param['paynum']))){
			echo json_encode(array('code'=>0,'resule'=>lang('ThePasswordEnteredIsIncorrect')));
			exit;
		}
		
		//用户钱包信息		
		$stdwallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->field('count')->find();
        //std价格
		$stddcny = 	$stdwallet_info['count']*get_ticket();
    	// std转换成rmb，判断余额是否充足
    	if($stddcny < $param['pay_amount']){
			echo json_encode(array('code'=>0,'resule'=>'STD'.lang('BalanceInsufficient')));
			exit;
		}
        $apiKey = '895B296C678416B6A4518EFD1939979B'; // apikey
        $apiUrl = 'http://std_bank.ztok.net/api/';
        $data['orderid'] = substr(md5(microtime()*rand(0,9999)),0,20); // 订单id = 回调里的out_trade_id
        $data['notify_url'] = 'https://std.stdchain.app/install/Numberpay/alipayNoticey'; // 回调地址
        $data['pay_amount'] = $param['pay_amount']; // 订单金额,单位 元, -- 最低
        $data['qrcode'] = $param['qrcode']; // 支付地址，扫码获取支付宝二维码的地址，前端传入
        asort($data); // 按照参数名ASCII码从小到大排序
        $data['sign'] = $this->getSign($data, $apiKey);
        $data['action'] = 'SubmitOrder'; // 方法名，不参与签名
        // echo '以下是参数：';
        // print_r($data);
        // echo '<hr>';
        // echo '以下是响应返回值：';
        $result = curl_post($apiUrl, $data);
        $result = json_decode($result, true);
        // print_r($result);die;
        if(isset($result['result']) && $result['result'] == 'true'){
            // 支付的人民币换算回std，来扣除
            $num = bcdiv($param['pay_amount'], get_ticket(), 4);
            // 写入sylistbase表
            $arrmm = [  
                'uid'=>$userinfo,
                'num'=>$num,
                'type'=>15, // 15 数字支付扣除
                'time'=>time(),
                'orderid'=>$data['orderid'],
            ];
            Db::startTrans();
            try{ 
            	Db::name('wallet_info')->where(array('wallet_id'=>$wallet['id'],'name'=>'STD'))->setDec('count',$num);
                DB::name('sylistbase')->insert($arrmm);
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
            
        }else{
            // 创建订单失败进入
            echo json_encode(array('code'=>0,'resule'=>lang('Failed')));
            exit;
        }
    }
    
    
    // 数字支付，支付宝回调
    // https://std.stdchain.app/install/Numberpay/alipayNoticey?pay_amount=0.01&out_trade_id=c14e4f39d36a0097f3cd&pay_orderid=test&successdate=2020-09-21%2013:27:37&sys_orderid=test&returncode=10000
    public function alipayNoticey()
    {
        $result = $this->request->param();
        if(!isset($result['returncode']) || $result['returncode'] != '10000'){
            file_put_contents('alipayNoticey.txt', 'fail'.json_encode($result).PHP_EOL, FILE_APPEND | LOCK_EX);
            die;
        }
        $sylistbase = Db::name('sylistbase')->where(array('orderid'=>$result['out_trade_id'],'type'=>15))->find();
        if(empty($sylistbase)){
            file_put_contents('alipayNoticey.txt', 'empty'.json_encode($result).PHP_EOL, FILE_APPEND | LOCK_EX);
            die;
        }
        // 写入 tb_number_pay 表
        $arrmm1 = [
            'uid'=>$sylistbase['uid'],
            'out_trade_id'=> $result['out_trade_id'], // MFT钱包订单号
            'pay_amount'=>$result['pay_amount'], // 支付金额
            'pay_orderid'=>$result['pay_orderid'], // 微信、支付宝平台订单号
            'successdate'=>$result['successdate'], // 交易时间
            'sys_orderid'=>$result['sys_orderid'], // 支付系统生成的订单号
            'tb_sylistbase_id'=>$sylistbase['id'], // 关联tb_sylistbase表的id
        ];
        Db::startTrans();
        try{
            DB::name('number_pay')->insert($arrmm1);    
            Db::commit();
            // 如果接收到服务器点对点通讯时，在页面输出“SUCCESS”（没有双引号，SUCCESS字母大写）,否则会重复3次发送点对点通知.
            echo 'SUCCESS';die;
        } catch (\Exception $e) {
             // 回滚事务
             Db::rollback(); 
             echo json_encode(array('code'=>0,'resule'=>lang('OperationFailed')));   
             exit;
       }
        
    }
    
    
    // 数组转换键值对
    public function getSign($payConfig, $key) {
        ksort($payConfig);
        $str = "";
        foreach ($payConfig as $k => $v) {
            $str =$str. $k . "=" . $v."&";
        }
        $str=$str."key=".$key;

        return strtoupper(md5($str));
    }
}