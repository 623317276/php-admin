<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/12
 * Time: 13:57
 */
namespace app\admin\controller;

use app\admin\model\UserModel;
use app\admin\model\VideoLevelModel;
use app\admin\model\VideoModel;
use app\admin\model\VideoUserLevelModel;
use cmf\controller\AdminBaseController;
use app\admin\model\VideoClassModel;
use think\Request;
use think\Db;
use newz\perl;
class OrderController extends AdminBaseController{

    /**
     * 视频分类设置
     */
    public function orderlist(){
        return $this->fetch(); 
    }

    public function adds(){
        $lei = new perl();

        if($_FILES['picture']['name']){

        if(isset($_FILES['picture']['name']) && !empty($_FILES['picture']['name']) ){
                $photo = $lei->imgUpload('picture');
        }else{
                return $this->assign('errorTips', '分类不能为空');
        }
        }else{
            $infolist = $tMO->where("id = {$datas['id']}")->fRow();
            $photo = $infolist['pic'];
        }
    }

    


}