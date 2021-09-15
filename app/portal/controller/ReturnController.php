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
namespace app\portal\controller;
use app\portal\model\UserModel;
use cmf\controller\HomeBaseController;
use think\Db;



class ReturnController extends HomeBaseController
{



    /**
     * @return mixed回调
     */

    public function recharge_return(){
       
        $request = file_get_contents('php://input');
     file_put_contents("test.log", json_encode($_POST) . "\n", FILE_APPEND);

      
// Fill these in with the information from your CoinPayments.net account.
        $cp_merchant_id = 'b0046d2b47a6bf94be6f835b80a83f0d';
        $cp_ipn_secret = '8YXH#qFD58e&7Y5';
        // $cp_merchant_id = '5586ec1ea409d6be2045dc7ab1a7b0b6';
      //  $cp_ipn_secret = '8YXH#qFD58e&7Y5';
        $cp_debug_email = '';

//These would normally be loaded from your database, the most common way is to pass the Order ID through the 'custom' POST field.

//$order_total = $zz['all_price'];
        $order_total = 0;

        if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
            print_r('IPN Mode is not HMAC');
        }
        if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
            print_r('No HMAC signature sent.');
        }
        if ($request === FALSE || empty($request)) {
            print_r('Error reading POST data');
        }
        if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($cp_merchant_id)) {
            print_r('No or incorrect Merchant ID passed');
        }
        $hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));
        if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
            //if ($hmac != $_SERVER['HTTP_HMAC']) { <-- Use this if you are running a version of PHP below 5.6.0 without the hash_equals function
            print_r('HMAC signature does not match');
        }
// 此时验证了HMAC签名，加载一些变量。
        $txn_id = $_POST['txn_id'];
       // $item_name = $_POST['item_name'];
       // $item_number = $_POST['item_number'];
        $amount1 = floatval($_POST['amount1']);
       // $amount2 = floatval($_POST['amount2']);
        $currency1 = $_POST['currency1'];
       // $currency2 = $_POST['currency2'];
        $status = intval($_POST['status']);
      //  $status_text = $_POST['status_text'];

        $order_currency ="USDT";

        //根据系统的API，您可能需要检查并查看事务ID$txn_id是否已经在此时处理过。

        // 核对原币，确保买方没有兑换。
        if ($currency1 != $order_currency) {

            print_r('Original currency mismatch!');
        }

        // 核对订单总额
        if ($amount1 < $order_total) {

            print_r('Amount is less than order total!');
        }
        if ($status >= 100 || $status == 2) {
            //成功走这
            $log =Db::name('usdt_recharge')->where('txd_id',$txn_id)->find();
            if($log['type']==0){
               file_put_contents("test10.log", 10 . "\n", FILE_APPEND);
                //开始走这，现更改状态
                $data_b['type'] = 1;
                $data_b['success_time'] = time();
                $data_b['money'] = $_POST['received_amount'];
                $save_chongzhi = Db::name('usdt_recharge')->where('txd_id',$txn_id)->update($data_b);
                if($save_chongzhi){
                    //给用户加钱
                    $user = Db::name('user')->where('id',$log['user_id'])->find();
                    $data_c['USDTbalance'] = $user['USDTbalance']+$_POST['received_amount'];
                    $save_user = Db::name('user')->where('id',$log['user_id'])->update($data_c);
                    if($save_user){
                        //添加记录
                        $date = 'USDT充值';
                        $b = "+";
                        $d = $b.$_POST['received_amount'];
                        $user = Db::name('user')->where('id',$log['user_id'])->find();
                        $total = $user['USDTbalance'] +$_POST['received_amount'];

                        $res = Db::name('slow')->insert([
                            'expenses' => $d,
                            'money'=>$total,
                            'type'=>'1',
                            'time'=>time(),
                            'explain'=>$date,
                            'slowid'=>$user['id'],
                            'username'=>$user['user_login'],
                            'category'=>'2'
                        ]);
                    }

                }
            }

            // 付款已完成或排队等待夜间付款，成功
        } else if ($status < 0) {

            //付款错误，这通常是最终的，但如果没有汇率转换或经卖方同意，付款有时会重新打开。
        } else {
            //付款待定，您可以选择在订单页面添加注释。
        }
    }

}