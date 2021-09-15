<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;

class FinanceController extends AdminBaseController
{
 
 

   //报单金额
    public function declaration()
    {
 
        
        return $this->fetch(); 
    }

    //静态钱包明细
    public function staticmoney()
    {
      
        
        return $this->fetch(); 
    }
    //动态钱包明细
    public function dynamicmoney()
    {
      
        
        return $this->fetch(); 
    }
    //团队奖励明细
    public function teammoney()
    {
      
        
        return $this->fetch(); 
    }
    //充值明细
    public function recharge()
    {
      
        echo 1;exit;
        return $this->fetch(); 
    }
    //提现明细
    public function withdrawal()
    {
      
        
        return $this->fetch(); 
    }
}
