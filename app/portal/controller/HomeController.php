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
class HomeController extends HomeBaseController
{
    public function _initialize()
    {
        $user = session('userinfo');
        $utoken = session('token');
        $yrnum = session("yenum");
        

        if (empty($user)) {
             $this->redirect('Login/start');

        } else {
            $fusers = Db::name('user')->where('id', $user['id'])->find(); 
            if ($utoken == $fusers['utoken']) {

            } else {
                session_unset();
                session_destroy();
                
            }
            
             
        }


    }

    public function huoqushu()//ai投资获取EOS实时价格
    {
        $url = "https://api.coinmarketcap.com/v1/ticker/eos/"; 
        $result = $this->hqingPost($url);
        $arrg = array();
        foreach ($result as $key => $value) {
                if ($value['symbol'] == "EOS") {
                    $arrg = $value;

                }
            }

        echo json_encode($arrg);exit;
       
    }
    //转账
    public function transfer()
    {
        $user = session('userinfo');
        $userinfo = Db::name('user')->where('id', $user['id'])->find();//转出账号
        $arrm = array(
                'explain'=>'提前提现产生手续费',
                'slowid'=>$userinfo['id']
            );
        $zsxunum = Db::name('slow')->where($arrm)->sum('expenses');
        if(request()->isAjax()){
            $data = request()->param();  
            $userinfos = Db::name('user')->where('mobile', $data['ruuser'])->find();//转入账号
            if($data['ruuser'] == ""){
                $arrs = array('code'=>0,'resule'=>'转入用户不为空');
                echo json_encode($arrs);exit;
            }
            $useremail = $data['ruuser'];

            $where = "user_email='{$useremail}'"; 
            
            // $where = "user_email='{$useremail}'"; 
            $ruuser = Db::name('user')->where($where)->find();
            if($ruuser['id'] == $userinfo['id']){
                $arrs = array('code'=>0,'resule'=>'转入用户不能为自己'); 
                echo json_encode($arrs);exit;
            }
            $arrv = array(
                'investmentid'=>$user['id'],
                'status'=>0
            );
            $eosnums = Db::name('investment')->where($arrv)->sum("eosnum");

            if(!$ruuser){
                $arrs = array('code'=>0,'resule'=>'转入账号不存在');
                echo json_encode($arrs);exit;
            }

            if($data['money'] == ""){
                $arrs = array('code'=>0,'resule'=>'转账的金额不能为空');
                echo json_encode($arrs);exit;
            }
            if($data['leixing'] == "" || $data['leixing'] == "请选择转账币种"){
                $arrs = array('code'=>0,'resule'=>'请选择转账类型');
                echo json_encode($arrs);exit;
            }
            if($userinfo['paynum'] != md5($data['paynum'])){
                $arrs = array('code'=>0,'resule'=>'交易密码不正确！');
                echo json_encode($arrs);exit;
            }
            if($data['leixing'] == 'EOS'){
                if($userinfo['EOSbalance'] < $data['money']+$eosnums){ 
                $arrs = array('code'=>0,'resule'=>'账户eos余额不足！');
                echo json_encode($arrs);exit;
                }
            }
            if($data['leixing'] == 'SUD'){
                if($userinfo['SUBbalance'] < $data['money']){
                $arrs = array('code'=>0,'resule'=>'账户sud余额不足！');
                echo json_encode($arrs);exit;
                }
            }
            if($data['leixing'] == 'USDT'){
                if($userinfo['USDTbalance'] < $data['money']){
                $arrs = array('code'=>0,'resule'=>'账户usdt余额不足！');
                echo json_encode($arrs);exit;
                }
            }

            if($data['leixing'] == 'EOS'){
                $arrc = array(
               'EOSbalance'=>$userinfo['EOSbalance']-$data['money']
                );
                $arrcc = array(
               'EOSbalance'=>$ruuser['EOSbalance']+$data['money']
                );
                $type = 2;
                $explain = "EOS";
                $numss = $userinfo['EOSbalance']-$eosnums; //转出
                $ruuser1 = $ruuser['EOSbalance']-$eosnums; //转入

                $a = Db::connect('db_config_1')->name('user_wallet')->where('user_id',$ruuser['wallet'])->find();
                $b = Db::connect('db_config_1')->name('user_wallet')->where('user_id',$userinfo['wallet'])->find();
                if($a){//数据存在更改
                    $arrf = array(//转入账户
                        'val'=>$a['val']+$data['money']
                    );
                    $arrv = array(//转出账户
                        'val'=>$b['val']-$data['money']
                    );
                     Db::connect('db_config_1')->name('user_wallet')->where('user_id',$ruuser['wallet'])->update($arrf); 
                     Db::connect('db_config_1')->name('user_wallet')->where('user_id',$userinfo['wallet'])->update($arrv);

                }else{//不存在 写入 
                    $arrf = array(
                        'user_id'=>$ruuser['wallet'],
                        'coin_id'=>1,
                        'val'=>$data['money'],
                        'update_time'=>time()
                    );
                    $arrv = array(//转出账户
                        'val'=>$b['val']-$data['money']
                    );
                    Db::connect('db_config_1')->name('user_wallet')->insert($arrf);
                    Db::connect('db_config_1')->name('user_wallet')->where('user_id',$userinfo['wallet'])->update($arrv);  
                }




            }
            if($data['leixing'] == 'SUD'){
                $arrc = array(
               'SUBbalance'=>$userinfo['SUBbalance']-$data['money']
                );
                $arrcc = array(
               'SUBbalance'=>$ruuser['SUBbalance']+$data['money']
                );
                $type = 4;
                $explain = "SUD";
                $numss = $userinfo['SUBbalance'];
                $ruuser1 = $ruuser['SUBbalance'];
            }
            if($data['leixing'] == 'USDT'){
                $arrc = array(
               'USDTbalance'=>$userinfo['USDTbalance']-$data['money']
                );
                $arrcc = array(
               'USDTbalance'=>$ruuser['USDTbalance']+$data['money']
                ); 

                $type = 1;
                $explain = "USDT";
                $numss = $userinfo['USDTbalance'];
                $ruuser1 = $ruuser['USDTbalance'];
            }
            //总表记录转春账号
            $arrb = array( 
                'expenses'=> '-'.round($data['money'],4),
                'money'=>$numss-$data['money'],
                'type'=>$type,
                'time'=>time(),
                'explain'=>$explain."转出",
                'slowid'=>$userinfo['id'],
                'category'=>2

            );
            $arrbb = array(  
                'expenses'=> '+'.round($data['money'],4),
                'money'=>$ruuser1+$data['money'],
                'type'=>$type,
                'time'=>time(),
                'explain'=>$explain."转入",
                'slowid'=>$ruuser['id'],
                'category'=>2

            );
            Db::startTrans();
             try{
              Db::name('slow')->insert($arrb);
              Db::name('slow')->insert($arrbb);
              Db::name('user')->where("id",$userinfo['id'])->update($arrc);//转出账号
              Db::name('user')->where("id",$ruuser['id'])->update($arrcc);//转入账号
              Db::commit();
              $arrs = array('code'=>1,'resule'=>'操作成功！');
              echo json_encode($arrs);exit;
               } catch (\Exception $e) {
                // 回滚事务
                    Db::rollback();
                    $arrs = array('code'=>0,'resule'=>'网络错误,请刷新后重新操作！');
                    echo json_encode($arrs);exit;
               }
            
            

        }else{
            return $this->fetch();
        }
        
       
    }

    public function my_invest()//ai投资明细
    {


        $user = session('userinfo');
        $arrq = array(
            "investmentid"=>$user['id'],
            'status'=>0
        );
        $arrq1 = array(
            "investmentid"=>$user['id'],
            'status'=>1
        );
        $infolist = Db::name('investment')->where($arrq)->order('Id desc')->select()->toArray();
        $infolist1 = Db::name('investment')->where($arrq1)->order('Id desc')->select()->toArray();
        foreach ($infolist as $key => $value) {
            if($value['investment'] == 1){
                $investment = "初级投资";
            }
            if($value['investment'] == 2){
                $investment = "中级投资";
            }
            if($value['investment'] == 3){
                $investment = "高级投资";
            }
            $infolist[$key]['investment'] = $investment;
            $infolist[$key]['starttime'] = date('m-d H:i',$value['starttime']);
        } 
        foreach ($infolist1 as $key => $value) {
            if($value['investment'] == 1){
                $investment = "初级投资";
            }
            if($value['investment'] == 2){
                $investment = "中级投资";
            }
            if($value['investment'] == 3){
                $investment = "高级投资";
            }
            $infolist1[$key]['investment'] = $investment;
            $infolist1[$key]['starttime'] = date('m-d H:i',$value['starttime']);
        } 
        $this->assign('infolist1',$infolist1);
         $this->assign('infolist',$infolist);
        return $this->fetch();
       
    }

    public function my_income()//我的收益
    {
        $user = session('userinfo');
        $userinfo = Db::name('user')->where('id', $user['id'])->find();
        $arra = array(//ai收益
            'incomeid'=>$userinfo['id'],
            'typename'=>1
        );
        $arrb = array(//分享收益
            'incomeid'=>$userinfo['id'],
            'typename'=>2
        );
        $arrc = array(//团队收益
            'incomeid'=>$userinfo['id'],
            'typename'=>3
        );
        $shouyiinfoz = Db::name('staticmoney')->where('incomeid',$userinfo['id'])->order("id desc")->select()->toArray();//总收益
        foreach ($shouyiinfoz as $key => $value) {
            if($value['typename'] == 1){
                $shouyiinfoz[$key]['typename'] = 'AI收益';
            }
            if($value['typename'] == 2){
                $shouyiinfoz[$key]['typename'] = '分享收益';
            }
            if($value['typename'] == 3){
                $shouyiinfoz[$key]['typename'] = '团队收益';
            }
        }
        $shouyiinfo = Db::name('staticmoney')->where($arra)->order("id desc")->select()->toArray();
        $shouyiinfo1 = Db::name('staticmoney')->where($arrb)->order("id desc")->select()->toArray();//分享
        $shouyiinfo2 = Db::name('staticmoney')->where($arrc)->order("id desc")->select()->toArray();//团队
        $this->assign('shouyiinfo',$shouyiinfo);
        $this->assign('shouyiinfo1',$shouyiinfo1);
        $this->assign('shouyiinfo2',$shouyiinfo2);
        $this->assign('shouyiinfoz',$shouyiinfoz);
        return $this->fetch();
       
    }
    //提现操作
    public function tixians()//我的收益
    {
        $user = session('userinfo');
        
        $userinfo = Db::name('user')->where('id', $user['id'])->find();
        $fuserinfo = Db::name('user')->where('id', $userinfo['fuid'])->find();

        if(request()->isAjax()){
            $data = request()->param();
            $infolist = Db::name('investment')->where("Id",$data['id'])->find();
            //扣除团队总业绩
           
            $teanyeji = Db::name('teammoney')->select()->toArray(); 
         
            foreach ($teanyeji as $key => $value) {
                $arrmm = $value['teamid'];
                $arrmm = explode(',',$arrmm);
                if(in_array($infolist['investmentid'],$arrmm)){
                if($value['fuid'] == $fuserinfo['id']){
                    $arrg = array(
                        'investmentid'=>$userinfo['id'],
                        'status'=>0

                    );
                    $tzje = Db::name('investment')->where($arrg)->sum('money');
                    $tzje = $tzje-$infolist['money'];
                    if($tzje == 0){
                        foreach ($arrmm as $k => $v) {
                            if($v == $userinfo['id']){
                                unset($arrmm[$k]);
                            }
                        }
                        $teamid = implode(',',$arrmm);
                        $arrgg = array(
                        'moneynum'=>$value['moneynum']-$infolist['money'],
                        'teamnum'=>$value['teamnum']-1,
                        'teamid' =>$teamid
                        );
                    }else{
                        $arrgg = array(
                        'moneynum'=>$value['moneynum']-$infolist['money'],
                        );
                    }
                    Db::name('teammoney')->where('id',$value['id'])->update($arrgg);

                }else{
                    foreach ($arrmm as $k => $v) {
                            if($v == $userinfo['id']){
                                unset($arrmm[$k]);
                            }
                        }
                     $teamid = implode(',',$arrmm);   
                    $arrff = array(
                        'moneynum'=>$value['moneynum']-$infolist['money'],
                        'teamid' =>$teamid 
                    );
                    Db::name('teammoney')->where('id',$value['id'])->update($arrff);
                }
            }           
              
            }


            
            $arrf = array(
                'status'=>1, 
                
            ); 
           //投资中的
            $arrv = array(
                'investmentid'=>$userinfo['id'],
                'status'=>0
            );
            $eosnums = Db::name('investment')->where($arrv)->sum("eosnum"); 
            $arrm = array(
                'explain'=>'提前提现产生手续费',
                'slowid'=>$userinfo['id']
            );
           /* $zsxunum = Db::name('slow')->where($arrm)->sum('expenses');*/
            if($infolist['day']<30){//收取手续费
                $sxf = $infolist['eosnum']*0.06;//收取的手续费
                $sxf = round($sxf,2); 
                //总表记录
                $arrb = array(
                     'expenses'=> '-'.$sxf, 
                     'money'=>$userinfo['EOSbalance']-$eosnums+$infolist['eosnum']-$sxf, 
                     'type'=>2, 
                     'time'=>time(),
                     'explain'=>'开启AI未满30天手续费',
                     'slowid'=>$userinfo['id'],
                     'category'=>2

                    );
                 Db::name('slow')->insert($arrb);
            }
            $sxfs = array(
                'EOSbalance'=>$userinfo['EOSbalance']-$sxf
            );

            Db::name('user')->where("Id",$user['id'])->update($sxfs);
           $result = Db::name('investment')->where("Id",$data['id'])->update($arrf);
            if($result){
                $arrs = array('code'=>1,'resule'=>'提现成功');
                    echo json_encode($arrs);exit;
           }else{
                $arrs = array('code'=>1,'resule'=>'网络异常');
                    echo json_encode($arrs);exit;
           }

        }
      
       
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

  
  function eachs($id){
    // $id = 60;
    $parent = $newArr = array();
    $userinfo = Db::name('user')->where('user_type',2)->select()->toArray();
    foreach ($userinfo as $key => $value) {
        $newArr[$value['id']] = $value;
    }
    $this->eachs111111($userinfo, $newArr[$id]['fuid'], $parent);
     return $parent;
    // print_r($parent);die;
  }

/**
 * [eachs111111 description]
 * @param  [type] $userinfo [user数据]
 * @param  [type] $id       [父id]
 * @param  [type] &$parent  [装父id变量]
 * @return [type]           [array]
 */
public function eachs111111($userinfo, $id, &$parent)
    {
        if(!$id){
            return 'fuid is null';
        }
        $flag = false;
        foreach ($userinfo as $key => $value) {
           if($value['id'] == $id){
                $parent[] = $value['id'];
                $this->eachs111111($userinfo, $value['fuid'], $parent);
           }
        }
        return $parent;
    }
    
    public function jiedian()
    {
        $user = session('userinfo');
        $sets = Db::name('set')->find();
        $arrg = array(
            'slowid'=>$user['id'],
            'explain'=>'3万矿机静态收益'
        );

        $arrg1 = array(
            'slowid'=>$user['id'],
            'explain'=>'15万矿机静态收益'
        );
        $investmentlist = Db::name('investment')->where('investmentid',$user['id'])->order('id desc')->select()->toArray();
        $shouyi = Db::name('slow')->where($arrg)->order('Id desc')->select()->toArray();//3万矿机收益
        $shouyi1 = Db::name('slow')->where($arrg1)->order('Id desc')->select()->toArray();//15万矿机收益
        $arrs = array(
            'investmentid'=>$user['id'],
            'investment'=>2
        );
        $arrs1 = array(
            'investmentid'=>$user['id'],
            'investment'=>1
        );
        $investmentlist_15 = Db::name('investment')->where($arrs)->order('id desc')->count();//15万
        $investmentlist_3 = Db::name('investment')->where($arrs1)->order('id desc')->count();//3万
        foreach ($investmentlist as $key => $value) {
            $investmentlist[$key]['time'] = date('Ymd',$value['starttime']);
            if($value['investment'] == 1){
                $investmentlist[$key]['type'] = '3万';
            }elseif($value['investment'] == 2){
                $investmentlist[$key]['type'] = '15万';
            }
        }
        foreach ($shouyi as $key => $value) {
            $shouyi[$key]['time'] = date('Ymd',$value['time']);
        }
        foreach ($shouyi1 as $key => $value) {
            $shouyi1[$key]['time'] = date('Ymd',$value['time']);
        }
        if($investmentlist){
        	$this->assign('type',1);
        }else{
        	$this->assign('type',0);
        }
        if($shouyi){
        	$this->assign('type1',1);
        }else{
        	$this->assign('type1',0);
        }
        if($shouyi1){
        	$this->assign('type2',1);
        }else{
        	$this->assign('type2',0);
        }
        $set = $sets['primaryincome'];//3万
        $set1 = $sets['secondary'];//15万
        $set = explode('|',$set);
        $set1 = explode('|',$set1);
        $arrs = array(
            'swsy'=>$set[0],
            'swwsy'=>$set1[0],
            'swsm'=>$set[1],
            'swwsm'=>$set1[1],
        );
        $this->assign('arrs',$arrs); 
        $this->assign('investmentlist',$investmentlist); 
        $this->assign('shouyi',$shouyi); 
        $this->assign('shouyi1',$shouyi1); 
        $this->assign('investmentlist_15',$investmentlist_15); 
        $this->assign('investmentlist_3',$investmentlist_3); 
        return $this->fetch();
    }

    public function touzi()
    {
        $data = $this->request->param();
        if($data['type'] == 1){//三万投资
            $moneys = 30000;
        }elseif($data['type'] == 2){//15万投资
            $moneys = 150000;
        }
        $user = session('userinfo');
        $userinfo = Db::name('user')->where('id',$user['id'])->find();
        if($userinfo['pff'] < $moneys){
            echo json_encode(array('code'=>0,'resule'=>'PFF余额不足'));
            exit;
        }
        
        $finfolist = $this->eachs($user['id']);
        $arrf = [];
        $num = 1;
        //写入投资记录
        $invest = array(
        	'starttime'=>time(),
        	'investment'=>$data['type'],//投资类型
        	'money'=>$moneys,
        	'investmentid'=>$user['id'],

        );
        //交易总表
        $arrss = array( 
                'expenses'=>'-'.$moneys,
                'money'=>$userinfo['pff']-$moneys,
                'time'=>time(),
                'explain'=>'购买矿机花费'.$moneys.'PFF',
                'slowid'=>$user['id']
            );

        $arrzi = array(
            'pff'=>$userinfo['pff']-$moneys
        );
        //记录总表
        Db::startTrans();
             try{ 
              Db::name('investment')->insert($invest);
              Db::name('slow')->insert($arrss);
              Db::name('user')->where("id",$user['id'])->update($arrzi);
              Db::commit();
              echo json_encode(array('code'=>1,'resule'=>'购买成功！'));
               } catch (\Exception $e) {
                // 回滚事务
                    Db::rollback();
               }

        $set = Db::name('set')->find();
        $zset = $set['directmoney']/100;
        $sset = $set['secondmoney']/100;
        foreach ($finfolist as $key => $value) {//计算投资返利 
            $key = $key+1;
            if($key == 1){
                $money = $moneys*$zset;
                $explain = '链接收益';
            }else{
                $cif = pow($sset,$num);
                $money = round($moneys*$zset*$cif,3);
                $num = $num+1;
                $explain = '节点链接收益';
            }
            if($money<1){
            	$money = number_format($money,3,'','');
            	$money = substr_replace($money, '.', 1, 0);
            }
            if($money == 0){
                break; 
            }
            $nuser = Db::name('user')->where('id',$value)->find();
            $arrt = array(
                'Pffbalance'=>$nuser['Pffbalance']+$money
            );

            $arrs = array(
                'expenses'=>'+'.$money,
                'money'=>$nuser['Pffbalance']+$money,
                'time'=>time(),
                'explain'=>$explain,
                'childname'=>$userinfo['user_nickname'],
                'slowid'=>$value
            );
            Db::startTrans();
             try{ 
              Db::name('slow')->insert($arrs);
              Db::name('user')->where("id",$value)->update($arrt);
              Db::commit();
               } catch (\Exception $e) {
                // 回滚事务
                    Db::rollback();
               }
              
        }

        
        
    }
}
