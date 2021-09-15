<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11
 * Time: 15:45
 */
namespace app\portal\model;

use think\Model;
use app\portal\model\VideoCollectModel;

class VideoModel extends Model
{
    /**
     * @param $userId
     * @param bool $paginate
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 用户视频分页处理
     */
    public static function getVideosByUserID($userId, $paginate = true, $page = 1, $size = 30)
    {
        $query = self::with('user')->where('user_id', '=', $userId);
        if (!$paginate)
        {
            return $query->order('id desc')->select();
        }
        else
        {
            // paginate 第二参数true表示采用简洁模式，简洁模式不需要查询记录总数
            return $query->order('id desc')->paginate($size, true, ['page' => $page]);
        }
    }

    public function user(){
        return $this->belongsTo('UserModel','user_id')->field('id,user_nickname');
    }

    /**
     * @param $_opt
     * @param bool $paginate
     * @param int $page 页码
     * @param int $size 每页展示数
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 视频list
     */
    public static function getLoveByVideos($_opt, $paginate = true, $page = 1, $size = 30,$userId = ''){

        $query = self::with('user')->where($_opt);
        if (!$paginate)
        {
            return $query->order('id desc')->select()->each(function($item, $key) use($userId){

            $where['video_id'] = $item["id"];
            $where['user_id'] = $userId;
            $num = VideoCollectModel::where($where)->find(); //根据ID查询相关其他信息
            if($num){
                $item['focus'] = 1;
            }else{
                $item['focus'] = 0;
            }

            return $item;
        });
        }
        else
        {
            // paginate 第二参数true表示采用简洁模式，简洁模式不需要查询记录总数
            return $query->order('id desc')->paginate($size, true, ['page' => $page])->each(function($item, $key) use($userId){

            $where['video_id'] = $item["id"];
            $where['user_id'] = $userId;
            $num = VideoCollectModel::where($where)->find(); //根据ID查询相关其他信息
                if($num){
                    $item['focus'] = 1;
                }else{
                    $item['focus'] = 0;
                }

            return $item;
        });
        }
    }

    public function collect(){
        return $this->belongsTo('VideoCollectModel','id','video_id')->field('user_id,video_id');
    }
    public static function getCollectVideos($userId, $paginate = true, $page = 1, $size = 30){
        $query = self::hasWhere('collect',['user_id'=>$userId])->with('user');
        if (!$paginate)
        {
            return $query->order('id desc')->select();
        }
        else
        {
            // paginate 第二参数true表示采用简洁模式，简洁模式不需要查询记录总数
            return $query->order('id desc')->paginate($size, true, ['page' => $page]);
        }
    }

}