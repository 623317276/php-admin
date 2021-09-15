<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/12
 * Time: 16:14
 */
namespace app\admin\model;

use think\Model;
class VideoModel extends Model
{
    public function user()
    {
        return $this->belongsTo('UserModel','user_id')->field('id,user_nickname');
    }

    public function videoClass()
    {
        return $this->belongsTo('VideoClassModel','class_id')->field('id,class_name');
    }

    public static function pendingAudit($_opt,$arr,$page=10,$classOpt=''){
        $result = self::hasWhere('user',$_opt)->with('videoClass')->where($arr)->paginate($page);
        return $result;
    }

    public static function AuditAll($_opt,$arr,$page=10){
        $result = self::hasWhere('user',$_opt)->with('videoClass')->where($arr)->paginate($page);
        return $result;
    }

}