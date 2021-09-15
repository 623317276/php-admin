<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25
 * Time: 15:43
 */

function htmlspecial($str){
    return htmlspecialchars_decode($str);
}

/**
 * Created by ym.
 * User: Administrator
 * Date: 2020/4/7
 * Time: 19:03
 */
function indexBy($list = array(), $index = ''){
	if(!empty($index)){
		$new = array();
		foreach ($list as $key => $val){
			$new[$val[$index]] = $val;
		}
		return $new;
	}else{
		return $list;
	}
}

/**
 * Created by ym.
 * User: Administrator
 * Date: 2020/4/7
 * Time: 19:03
 */
// function array_column($list = array(), $index = ''){
//     if(!empty($list)){
//         $new = array();
//     	foreach ($list as $key => $val){
//     		$new[] = $val[$index];
//     	}
//     	return $new;
//     }else{
//         return array();
//     }
	
// }


/**
 * 验证码校验
 * tel  手机号码
 * c_verify 验证码
 * action 操作
 */
function check_verify($tel, $c_verify, $type = 1) {
    $map['tel'] = $tel;
    $map['status'] = '1';
    $map['type'] = $type;
    $map['created'] = array('EGT',(time() - config("MsgVerifyTime")));
    $verify = db("send_msg")->where($map)->order("id desc")->value('verify');

    $return_data['code'] = 1;
    $return_data['msg'] = 'ok';
    $return_data['data'] = array();

    if($verify == null || !$verify){
        $return_data['code'] = 0;
        $return_data['msg'] = lang('SmsExpiredResend');
        return $return_data;
    }
    if($verify != $c_verify){
        $return_data['code'] = 0;
        $return_data['msg'] = lang('VerifyCodeError');
        return $return_data;
    }
    Db('send_msg')->where($map)->update(['status'=>0]);//修改短信状态

    return $return_data;

}

/**
 * 校验频繁登陆异常状态
 * tel  手机号码
 * ip 
 * 10分钟内失败50次，就限制
 */
function check_login($tel, $ip){
    $map['mobile'] = $tel;
    $map['ip'] = $ip;
    $now_time = time() - 600; // 当前时间-10分钟,判断10分钟内错误次数来限制登陆
    $map['time'] = array('EGT', date('Y-m-d H:i:s' ,$now_time));
    $err_login = db("err_login")->where($map)->order("id desc")->select();
    if(count($err_login) >= 50){
        $return_data['code'] = 0;
        $return_data['msg'] = '操作过于频繁...10分钟后重试';
        return $return_data;
    }
        $return_data['code'] = 1;
        $return_data['msg'] = '';
        return $return_data;
}

