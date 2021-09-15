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

use app\portal\model\UserModel;
use cmf\controller\HomeBaseController;
use think\Db;

class ApiController extends HomeBaseController
{



    //个人每天收益释放
    function dynamic() 
    {
        $touzilist = Db::name('investment')->where('status=0')->select()->toArray();
        $type = Db::name('set')->find();
        $stype = $type['primaryincome'];
        $type = $type['secondary'];
        $stype = explode('|',$stype);
        $type = explode('|',$type);
        $stype = $stype[0];//3万日收益
        $type = $type[0];//15万日收益
        foreach ($touzilist as $key => $value) {
            if($value['investment'] == 1){//投资3万 
                $money = $stype;
                $msg = '3万矿机日收益';
            }else{//投资15万
                $money = $type; 
                $msg = '15万矿机日收益';
            }
            $nuser = Db::name('user')->where('id',$value['investmentid'])->find();
            $touzi = Db::name('investment')->where('Id',$value['Id'])->find();
            $arrts = array(
                    'income'=>$touzi['income']+$money
                );

            $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$money
                );

                $arrs = array(
                    'expenses'=>'+'.$money,
                    'money'=>$nuser['Pffbalance']+$money,
                    'time'=>time(),
                    'explain'=>$msg,
                    'slowid'=>$value['investmentid']
                );
              Db::startTrans();
             try{ 
              Db::name('slow')->insert($arrs);
              Db::name('user')->where("id",$value['investmentid'])->update($arrt);
              Db::name('investment')->where("Id",$value['Id'])->update($arrts);
              Db::commit();
               } catch (\Exception $e) {
                // 回滚事务
                    Db::rollback();
               }

        }

    }
    public function hqingp(){
        $url = "https://api.coinmarketcap.com/v1/ticker/";
        $result = $this->hqingPost($url);
        echo json_encode($result);
        exit;

    }
    function hqingPost($furl)
    {
        $url_get = $furl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_get);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output, true);
        return $result;

    }

}
