<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25
 * Time: 13:57
 */

namespace app\portal\model;

use think\Model;

class ChatrecordModel extends Model
{
    public function getTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public function user(){
        return $this->belongsTo('UserModel','sid')->field('id,user_nickname,avatar');
    }
    public static function getRecords($_opt,$opt){
        $result = self::with('user')->where($_opt)->whereOr($opt)->order('time desc')->limit(100)->select();

        return $result;
    }
}