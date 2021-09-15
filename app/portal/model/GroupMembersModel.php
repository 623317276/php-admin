<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:12
 */
namespace app\portal\model;

use think\Model;

class GroupMembersModel extends Model
{
    public function getTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public function user(){
        return $this->belongsTo('UserModel','user_id')->field('id,user_nickname,avatar');
    }
    public static function getRecords($_opt,$opt){
        $result = self::with('user')->where($_opt)->whereOr($opt)->select();
        return $result;
    }

    public static function GetRoomMen($_opt){
        $result = self::with('user')->where($_opt)->select();
        return $result;
    }
}