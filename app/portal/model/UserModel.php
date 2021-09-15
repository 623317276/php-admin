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
namespace app\portal\model;

use think\Model;

class UserModel extends Model
{

    protected $type = [
        'more' => 'array',
    ];

    public function level(){
        return $this->hasOne('VideoUserLevelModel','user_id')->field('id,user_id,user_level');
    }

    public static function userInfo($_opt){
        $result = self::with('level')->where($_opt)->find();
        return $result;
    }

    /*
     * 获取群组
     */
    public function room(){
        return $this->hasOne('VideoUserLevelModel','user_id')->field('id,user_id,user_level');
    }
    public static function getRooms($_opt){
        $result = self::with('level')->where($_opt)->find();
        return $result;
    }



}