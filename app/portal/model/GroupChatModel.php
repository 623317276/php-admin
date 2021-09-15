<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 9:25
 */
namespace app\portal\model;

use think\Model;

class GroupChatModel extends Model
{
    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public function user(){
        return $this->belongsTo('UserModel','sid')->field('id,user_nickname,avatar');
    }
    public static function getRecords($_opt){
        $result = self::with('user')->where($_opt)->order('create_time desc')->limit(100)->select();
        return $result;
    }
}