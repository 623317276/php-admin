<?php
$smtpserver = "smtp.163.com";//SMTP服务器
$smtpserverport =25;//SMTP服务器端口
$smtpusermail = "slfenga@163.com";//SMTP服务器的用户邮箱
$smtpemailto = check_email($_COOKIE['email']);//发送给谁
$email=$smtpemailto;
$smtpuser = "slfenga@163.com";//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
$smtppass = "13693591081fsl";//SMTP服务器的用户密码
$mailtitle = '找回密码-马利来健康助手';//邮件主题
//******************** 配置信息 ********************************
$getpasstime=time();
$token=md5($row['jk_id'].$row['jk_username'].$row['jk_password'].$getpasstime);
$uid=$clean['jk_id'];
$HostUrl='http://'.$_SERVER['HTTP_HOST'];
$retsurl=$HostUrl.'/jk/rest.php?token='.$token;
$hresturl='<a href="'.$retsurl.'">'.$retsurl.'</a>';
$nr="<h1>健康更检测助手找回密码</h1><h2>亲爱的".$row['jk_username']."请点击以下链接重置密码，30分钟内有效!</h2>";
$mailcontent=$nr.$retsurl;//邮件内容
$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
//************************ 配置信息 ****************************
$smtp = new Smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
$smtp->debug = false;//是否显示发送的调试信息
