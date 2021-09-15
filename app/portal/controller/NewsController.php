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
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalCategoryModel;
use think\Db;
class NewsController extends HomeBaseController
{
    public function news()
    {
    	$type = $this->request->param();
    	$type = $type['type'];

    	if($type==1){//系统公告
    		$where = array(
    			'noticecategory'=>2
    		);
    	}else if($type==2){//行业资讯
    		$where = array(
    			'noticecategory'=>5
    		);
    	}

        $notice = Db::name('notice')->where($where)->select();
        $this->assign('notice',$notice);
        $this->assign('type',$type); 
        return $this->fetch();
    }

}
