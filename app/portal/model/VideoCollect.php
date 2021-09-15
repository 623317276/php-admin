<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13
 * Time: 9:15
 */
namespace app\portal\model;

use think\Model;

class VideoCollect extends Model
{
    public static function getVideosByUserID($userId, $paginate = true, $page = 1, $size = 30)
    {
        $query = self::where('user_id', '=', $userId);
        if (!$paginate)
        {
            return $query->select();
        }
        else
        {
            // paginate �ڶ�����true��ʾ���ü��ģʽ�����ģʽ����Ҫ��ѯ��¼����
            return $query->paginate($size, true, ['page' => $page]);
        }
    }

}