<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25
 * Time: 17:44
 */
namespace app\portal\model;

use think\Model;

class CreateroomModel extends Model
{
    public function room(){
        return $this->hasOne('GroupMembersModel','room_id');
    }

    public function user(){
        return $this->hasOne('GroupMembersModel','room_id');
    }
    public static function getRooms($_opt){
        $result = self::hasWhere('room',$_opt)->where($_opt)->select();
        return $result;
    }
}