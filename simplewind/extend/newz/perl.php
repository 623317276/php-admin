<?php
namespace newz;
class perl {

	  function imgUpload($field){
        $name = self::filter($_FILES[$field]['name']); 
        $tmpname = $_FILES[$field]['tmp_name'];
        
        if( filesize($tmpname) >= 1024 * 1024 ){
            $this->exitjson(1, $this->show('picture_oversize')); 
        }

        $suffix = strrpos($name, '.') + 1;
        $fileSuffixArr = array('jpg', 'jpeg', 'png');
        if( !in_array(strtolower(substr($name, $suffix)), $fileSuffixArr) ){
            $this->exitjson(1, $this->show('picture_format'));
        }
        $newname = $name ? self::fileRename($name) : '';
        if(!move_uploaded_file($tmpname, ROOT_PATH.'/public/pic/'.$newname) ){	
            return '';
        }

        return $newname;
    } 

     function videoUpload($field){
        $name = self::filter($_FILES[$field]['name']); 
        $tmpname = $_FILES[$field]['tmp_name'];
        
        // if( filesize($tmpname) >= 1024 * 1024 ){
        //     $this->exitjson(1, $this->show('picture_oversize')); 
        // }

        $suffix = strrpos($name, '.') + 1;
        $fileSuffixArr = array('mp3', 'mp4');
        if( !in_array(strtolower(substr($name, $suffix)), $fileSuffixArr) ){
            $this->exitjson(1, $this->show('picture_format'));
        }
        $newname = $name ? self::fileRename($name) : '';
        if(!move_uploaded_file($tmpname, ROOT_PATH.'/public/video/'.$newname) ){
            return '';
        }

        return $newname;
    } 

	static function filter($pStr, $pTrans=array()){
		$tTrans = array("'"=>'', '"'=>'', '`'=>'', '\\'=>'', '<'=>'＜', '>'=>'＞');
		return strtr(trim($pStr), array_merge($tTrans, $pTrans));
	}

	static  function a(){
		echo 1111111111;exit;
	}


	private static $err_msg = array(
			'en' => array(
				'login_first' => 'Please login before proceeding.',				// 请先登录再进行操作
				'code_error' => 'Code error.',									// 验证码错误
				'email_code_error' => 'Email code error.',									// 邮件验证码错误
				'phone_code_error' => 'Phone code error.',									// 手机验证码错误
				'img_code_error' => 'Image code error.',									// 图片验证码错误
				'password_error' => 'Old password error.', // 老密码错误
				'email_password_error' => 'Email or password error, you have 1 tries left.', // 邮箱或密码错误
				'google_auth_error' => 'Google auth code error.', // 谷歌验证码错误
				'login_ip_frequently' => 'Too many invalid login attemps, please retry 2 hours later.', //此ip登录频繁，请2小时后再试
				'password_error_over_times' => 'Password error, over %s times.',	// 密码错误，您还有%s次机会
				'success' => 'Success.', // 成功
				'email_exists' => 'This email already exists.', // 邮箱已存在
				'email_no_exists' => 'This email not exist.', // 邮箱不存在
				'check_email' => 'Invalid email address.', // 检测邮件地址
				'reg_email_title' => 'Global ample - Email Verification Code', // 邮件验证码title
				'reg_email_content' => 'Your verification code is : %s, Please enter in 10 minutes.', // 邮件验证码内容
				'password_length' => 'Password length must be between 6-20',	// 密码长度在6-20之间
				'confirm_password_not_match' => 'Confirm password not match.',	// 两次密码不同
				'system_error' => 'System error.',
				'same_old_password' => 'The password can not be the same as the original password.',	//密码不能和原密码相同
				'completed' => 'Completed', // 完成
				'waiting' => 'Waiting', // 等待
				'canceled' => 'Canceled', // 已取消
				'confirmation' => 'Confirmation', // 确认中
				'parameter_error' => 'Parameter error.',        // 参数错误
				'price_error' => 'Price error.',        // 价格错误
				'number_error' => 'Number error.',      // 数量错误
				'price_range' => 'Price Range: ',       // 价格范围
				'min_number' => 'Minimum number: ',     // 最小数量
				'lack_balance' => 'Your %s available balance is insufficient.', // 可用余额不足
				'operation_frequent' => 'Operation is too frequent.', // 操作太频繁
				'address_title' => 'Address', //地址
				'no_more' => 'No more', // 没有更多
				'get_address_fail' => 'Get address fail.', // 获取地址失败
				'minimum_withdrawal' => 'Minimum withdrawal: %s.', //最小提现金额
				'maximum_withdrawal' => 'Maximum withdrawal: %s',
				'current_available_amount' => 'Current remaining available amount: %s.', // 当前剩余可用额度
				'open_google_auth' => 'Please open Google Auth first.',	 // 请先开启谷歌验证
				'trust_not_exists' => 'The record does not exist.', 	// 委托记录不存在
				'lack_auth' => 'Insufficient permissions.', 		// 权限不足
				'operation_failed' => 'Operation failed.', 		// 操作失败
				'picture_oversize' => 'Please make sure the picture is less than 1MB.', // 请确保图片不超过 1MB
				'picture_format' => 'Image format is wrong, only support jpg, jpeg and png.', // 图片格式错误，仅支持jpg、jpeg和png
				'incomplete' => 'The information is incomplete.',	// 信息不完整
				'passport_exists' => 'The certificate number already exists.',	// 该证件号码已存在
				'google_auth_used' => 'Google auth used, Please wait next code.', // 谷歌验证码已经被用过了, 请等待下一个
				'send_email_frequent' => 'Send email too frequent, Please retry after.', // 发送邮件太频繁
				'invite_code_error' => 'Invite code error.', // 邀请码错误
			),

			'cn' => array(
				'login_first' => '请先登录再进行操作',
				'code_error' => '验证码错误',
				'email_code_error' => '邮件验证码错误',
				'phone_code_error' => '手机验证码错误',
				'img_code_error' => '图片验证码错误',
				'password_error' => '原密码错误',
				'email_password_error' => '手机号码错误或还没有注册',
				'google_auth_error' => '谷歌验证码错误',
				'login_ip_frequently' => '密码错误次数太多，请5分钟后再试',
				'password_error_over_times' => '密码错误，您还有%s次机会',
				'success' => '成功',
				'regsuccess' => '恭喜您注册成功',
				'email_exists' => '该邮箱已存在',
				'email_no_exists' => '邮箱不存在',
				'check_email' => '手机号码错误',
				'reg_email_title' => 'Global ample - 邮件验证码',
				'reg_email_content' => '您的验证码是 : %s, 请在10分钟内输入。',
				'password_length' => '密码长度在6-20之间',
				'confirm_password_not_match' => '两次密码不同',
				'system_error' => '系统错误',
				'same_old_password' => '密码不能和原密码相同', 
				'waiting' => '等待',
				'canceled' => '已取消',
				'confirmation' => '确认中',
				'completed' => '已完成',
				'parameter_error' => '参数错误',
				'price_error' => '价格错误',
				'number_error' => '数量错误',
				'price_range' => '价格范围',
				'min_number' => '最小数量',
				'lack_balance' => '您的 %s 可用余额不足',
				'operation_frequent' => '操作太频繁',
				'address_title' => '地址',
				'no_more' => '没有更多',
				'get_address_fail' => '获取地址失败',
				'minimum_withdrawal' => '最小提现金额: %s',
				'maximum_withdrawal' => '最大提现金额: %s',
				'current_available_amount' => '当前剩余可用额度: %s',
				'open_google_auth' => '请先开启谷歌验证',
				'trust_not_exists' => '委托记录不存在',
				'lack_auth' => '权限不足',
				'operation_failed' => '操作失败',
				'picture_oversize' => '请确保图片不超过 1MB',
				'picture_format' => '图片格式错误，仅支持jpg、jpeg和png',
				'incomplete' => '信息填写不完整',
				'passport_exists' => '该证件号码已存在',
				'google_auth_used' => '谷歌验证码已经被用, 请等待下一个验证码',
				'send_email_frequent' => '发送邮件太频繁, 请稍后重试',
				'invite_code_error' => '邀请码错误',
				'min_code_error' => '您发起的交易额不能小于对方设置的最小限额',
				'max_code_error' => '您发起的交易额不能大于对方设置的最大限额',
				'xianzhi_error' => '对方的  %s  可用余额不足', 
				'stop_error' => '对不起，该交易信息不存在', 
				'stop_trade' => '对不起，不能交易自己的订单',
				'stop_modimerad' => '对不起，请稍后发布交易信息',
				'stop_tradecu' => '对不起，请稍后进行交易',
				'da_tradecu' => '对不起，您的售出数量大于您的实际币量',
			),

			'tw' => array(
				'login_first' => '請先登錄再進行操作',
				'code_error' => '驗證碼錯誤',
				'email_code_error' => '郵件驗證碼錯誤',
				'phone_code_error' => '手機驗證碼錯誤',
				'img_code_error' => '圖片驗證碼錯誤',
				'password_error' => '原密碼錯誤',
				'email_password_error' => '郵箱或密碼錯誤',
				'google_auth_error' => '谷歌驗證碼錯誤',
				'login_ip_frequently' => '密碼錯誤次數太多，請2小時後再試',
				'password_error_over_times' => '密碼錯誤，您還有%s次機會',
				'success' => '成功',
				'email_exists' => '該郵箱已存在',
				'email_no_exists' => '郵箱不存在',
				'check_email' => '郵箱地址錯誤',
				'reg_email_title' => 'Global ample - 郵件驗證碼',
				'reg_email_content' => '您的驗證碼是 : %s, 請在10分鍾內輸入。',
				'password_length' => '密碼長度在6-20之間',
				'confirm_password_not_match' => '兩次密碼不同',
				'system_error' => '系統錯誤',
				'same_old_password' => '密碼不能和原密碼相同',
				'waiting' => '等待',
				'canceled' => '已取消',
				'confirmation' => '確認中',
				'completed' => '已完成',
				'parameter_error' => '參數錯誤',
				'price_error' => '價格錯誤',
				'number_error' => '數量錯誤',
				'price_range' => '價格範圍',
				'min_number' => '最小數量',
				'lack_balance' => '您的 %s 可用余額不足',
				'operation_frequent' => '操作太頻繁',
				'address_title' => '地址',
				'no_more' => '沒有更多',
				'get_address_fail' => '獲取地址失敗',
				'minimum_withdrawal' => '最小提現金額: %s',
				'maximum_withdrawal' => '最大提現金額: %s',
				'current_available_amount' => '當前剩余可用額度: %s',
				'open_google_auth' => '請先開啓谷歌驗證',
				'trust_not_exists' => '委托記錄不存在',
				'lack_auth' => '權限不足',
				'operation_failed' => '操作失敗',
				'picture_oversize' => '請確保圖片不超過 1MB',
				'picture_format' => '圖片格式錯誤，僅支持jpg、jpeg和png',
				'incomplete' => '信息填寫不完整',
				'passport_exists' => '該證件號碼已存在',
				'google_auth_used' => '谷歌驗證碼已經被用, 請等待下壹個驗證碼',
				'send_email_frequent' => '發送郵件太頻繁, 請稍後重試',
				'invite_code_error' => '邀請碼錯誤',
			),
		);

	// 错误信息
	public static function show($key = 'login_first')
	{
		return self::$err_msg[LANG][$key];
	}

    static function fileRename($name){
    	$suffix_index = strrpos($name, '.');
    	$code = rand(100000,999999);
    	return date('Ymd').md5(substr($name, 0, $suffix_index).$code) . '.' . strtolower(substr($name, $suffix_index+1));
    }

    protected function exitjson($code = '1', $msg = '非法请求', $data = array())  {
		header('Content-Type: application/json; charset=utf-8');
	    if (!empty($data)) {
	        exit(json_encode(array('code' => $code, 'msg' => $msg, 'data' => $data)));
	    } else {
	        exit(json_encode(array('code' => $code, 'msg' => $msg)));
	    }      
	}

}