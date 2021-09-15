<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 18:58
 */
namespace app\portal\model;
use think\Model;

class FrendsModel extends Model
{
    public function user(){
        return $this->belongsTo('UserModel','oid');
    }
    public static function getFriends($_opt,$where){
        $result = self::hasWhere('user',$_opt)->where($where)->select();
        return $result;
    }
    public static function addGroup($_opt){
        $result = self::with('user')->where($_opt)->select();
        return $result;
    }
}