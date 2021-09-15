<?php
namespace app\install\controller;

use app\admin\model\ThemeModel;
use think\Controller;
use think\Db;

class TestController extends Controller
{
    // 发送邮件
    public function testMail()
    {
        $data = request()->param();
        //定义收件人的邮箱 
        if(!empty($data['mobile'])){
            $toemail = $data['mobile'];
        }else{
            $toemail = '';
        }
        sendMail();
    }

    // https://std.stdchain.app/install/Test/sessionUser
    public function sessionUser(){
        // $userinfos = Db::name('user')->where('mobile',18978429109)->find();
        // print_r($userinfos);die;
        // echo 11;die;
        print_r(file_get_contents('https://www.pickcoin.top/api/spot/v3/instruments/STD-USDT/ticker'));die;
        // $result = geturldata('https://mail.stdchain.app/Rylkzmerwsejrp3n/Reg/card?user_name=623317276@qq.com&body='.urlencode('13123asdasd'));
        // print_r(json_decode($result, true));die;
    }        
}

