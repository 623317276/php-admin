<?php

namespace app\admin\controller;
use app\admin\model\AdminMenuModel;
use app\admin\model\SetModel;
use cmf\controller\AdminBaseController;
use FontLib\Table\Type\name;
use think\Db;
class SetController extends AdminBaseController
{
    /**
     * 后台系统设置
     * @adminMenu(
     *     'name'   => '后台设置',
     *     'parent' => 'admin/Set/default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '后台设置,
     *     'param'  => ''
     * )
     */
    public function index()
    {
        //添加钩子,只执行一个
        $content = hook_one('portal_admin_set_index_view');
        if (!empty($content)) {
            return $content;
        }
        $id = 1;
        $set = SetModel::get($id);
        $this->assign('set', $set);
        return $this->fetch();
    }
    /**
     * 设置界面修改提交
     * @adminMenu(
     *     'name'   => '设置界面修改提交',
     *     'parent' => 'set',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '设置界面修改提交',
     *     'param'  => ''
     * )
     */
     public function setPost(){
         $data      = $this->request->param();
         $setModel = new SetModel();
         $result    = $setModel->where(["id" =>1])->update($data);
         if ($result === false) {
         	echo 1;exit;
             $this->error($setModel->getError());
         }
         $this->success("保存成功！", url("set/index"));
      }
      public function grant(){
         $data = $this->request->param();
         $userinfo = Db::name('user')->where('user_type',2)->select()->toArray();
         $set = Db::name('set')->find();
         $ids = [];
         
         foreach ($userinfo as $key => $value) {
            $nums = [];
             $childinfo = Db::name('user')->where('fuid',$value['id'])->select()->toArray();
             foreach ($childinfo as $k => $val) {
                $childtouzi = Db::name('investment')->where('investmentid',$val['id'])->sum('money');
                // $numm = $childtouzi/30000;
                array_push($nums,$childtouzi);
                

             }
             $nums = array_sum($nums)/30000;                                                                                           
              $ids[$value['id']] = $nums;

         } 
   
         if(!$ids){
            echo json_encode(array('code'=>0,'msg'=>'没有用户！'));
            exit;
         }
         $child_id = [];
         $child_id1 = [];
         $child_id2 = [];
         $child_id3 = [];
         $child_id4 = [];
         $child_id5 = [];
         $child_id6 = [];
         $child_id7 = [];
         $child_id8 = [];
         $child_id9 = [];
         $child_id10 = []; 
         if($data['type'] == 1){//大于10人
            $money = $set['zpeople1'];
            foreach ($ids as $key => $value) {
             if($value >= 10 && $value < 20){ 
                $child_id[] = $key;
             }
             if($value >= 20 && $value < 30){
                $child_id1[] = $key;
             }
           
             if($value >= 30 && $value < 40){
                $child_id2[] = $key;
             }
             if($value >= 40 && $value < 50){
                $child_id3[] = $key;
             }
             if($value >= 50 && $value < 60){
                $child_id4[] = $key;
             }
             if($value >= 60 && $value < 70){
                $child_id5[] = $key;
             }
             if($value >= 70 && $value < 80){
                $child_id6[] = $key;
             }
             if($value >= 80 && $value < 90){
                $child_id7[] = $key;
             }
             if($value >= 90 && $value < 100){
                $child_id8[] = $key;
             }
             if($value >= 100 && $value < 110){
                $child_id9[] = $key;
             }
            }
            //直推10
            if($child_id){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成

                foreach ($child_id as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num
                );

                $arrs = array(
                    'expenses'=>'+'.$num,
                    'money'=>$nuser['Pffbalance']+$num,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            //直推20
            if($child_id1){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id1 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*2
                );

                $arrs = array(
                    'expenses'=>'+'.$num*2,
                    'money'=>$nuser['Pffbalance']+$num*2,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            
            //直推30
            if($child_id2){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id2 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*3
                );
                $arrs = array(
                    'expenses'=>'+'.$num*3,
                    'money'=>$nuser['Pffbalance']+$num*3,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            //直推40
            if($child_id3){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id3 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*4
                );
                $arrs = array(
                    'expenses'=>'+'.$num*4,
                    'money'=>$nuser['Pffbalance']+$num*4,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            //直推50
            if($child_id4){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id4 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*5
                );
                $arrs = array(
                    'expenses'=>'+'.$num*5,
                    'money'=>$nuser['Pffbalance']+$num*5,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            //直推60
            if($child_id5){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id5 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*6
                );
                $arrs = array(
                    'expenses'=>'+'.$num*6,
                    'money'=>$nuser['Pffbalance']+$num*6,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            //直推70
            if($child_id6){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id6 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*7
                );
                $arrs = array(
                    'expenses'=>'+'.$num*7,
                    'money'=>$nuser['Pffbalance']+$num*7,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            //直推80
            if($child_id7){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id7 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*8
                );
                $arrs = array(
                    'expenses'=>'+'.$num*8,
                    'money'=>$nuser['Pffbalance']+$num*8,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            //直推90
            if($child_id8){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id8 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*9
                );
                $arrs = array(
                    'expenses'=>'+'.$num*9,
                    'money'=>$nuser['Pffbalance']+$num*9,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            //直推100
            if($child_id9){
                $count = count($child_id);//直推大于10
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count*1+$count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id9 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*10
                );
                $arrs = array(
                    'expenses'=>'+'.$num*10,
                    'money'=>$nuser['Pffbalance']+$num*10,
                    'time'=>time(),
                    'explain'=>'基金节点分红',
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
            

         }else if($data['type'] == 2){//大于20人
            $money = $set['zpeople2'];
            foreach ($ids as $key => $value) {
             if($value >= 2 && $value < 3){
                $child_id1[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 3 && $value < 4){
                $child_id2[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 4 && $value < 5){
                $child_id3[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 5 && $value < 6){
                $child_id4[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 6 && $value < 7){
                $child_id5[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 7 && $value < 8){
                $child_id6[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 8 && $value < 9){
                $child_id7[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 9 && $value < 10){
                $child_id8[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 10 && $value < 11){
                $child_id9[] = $key;
             }
            }
             //直推20
            if($child_id1){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id1 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*2
                );

                $arrs = array(
                    'expenses'=>'+'.$num*2,
                    'money'=>$nuser['Pffbalance']+$num*2,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id2){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id2 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*3
                );

                $arrs = array(
                    'expenses'=>'+'.$num*3,
                    'money'=>$nuser['Pffbalance']+$num*3,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id3){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id3 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*4
                );

                $arrs = array(
                    'expenses'=>'+'.$num*4,
                    'money'=>$nuser['Pffbalance']+$num*4,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id4){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id4 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*5
                );

                $arrs = array(
                    'expenses'=>'+'.$num*5,
                    'money'=>$nuser['Pffbalance']+$num*5,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id5){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id5 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*6
                );

                $arrs = array(
                    'expenses'=>'+'.$num*6,
                    'money'=>$nuser['Pffbalance']+$num*6,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id6){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id6 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*7
                );

                $arrs = array(
                    'expenses'=>'+'.$num*7,
                    'money'=>$nuser['Pffbalance']+$num*7,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id7){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id7 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*8
                );

                $arrs = array(
                    'expenses'=>'+'.$num*8,
                    'money'=>$nuser['Pffbalance']+$num*8,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id8){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id8 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*9
                );

                $arrs = array(
                    'expenses'=>'+'.$num*9,
                    'money'=>$nuser['Pffbalance']+$num*9,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id9){
                $count1 = count($child_id1);//直推大于20
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count1*2+$count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id9 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*10
                );

                $arrs = array(
                    'expenses'=>'+'.$num*10,
                    'money'=>$nuser['Pffbalance']+$num*10,
                    'time'=>time(),
                    'explain'=>'后台发放',
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

         }else if($data['type'] == 3){//大于30人
            $money = $set['zpeople3'];
            foreach ($ids as $key => $value) {
             if($value >= 3 && $value < 4){
                $child_id2[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 4 && $value < 5){
                $child_id3[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 5 && $value < 6){
                $child_id4[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 6 && $value < 7){
                $child_id5[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 7 && $value < 8){
                $child_id6[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 8 && $value < 9){
                $child_id7[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 9 && $value < 10){
                $child_id8[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 10 && $value < 11){
                $child_id9[] = $key;
             }
            }
            
            if($child_id2){
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id2 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*3
                );

                $arrs = array(
                    'expenses'=>'+'.$num*3,
                    'money'=>$nuser['Pffbalance']+$num*3,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id3){
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id3 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*4
                );

                $arrs = array(
                    'expenses'=>'+'.$num*4,
                    'money'=>$nuser['Pffbalance']+$num*4,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id4){
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id4 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*5
                );

                $arrs = array(
                    'expenses'=>'+'.$num*5,
                    'money'=>$nuser['Pffbalance']+$num*5,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id5){
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id5 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*6
                );

                $arrs = array(
                    'expenses'=>'+'.$num*6,
                    'money'=>$nuser['Pffbalance']+$num*6,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id6){
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id6 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*7
                );

                $arrs = array(
                    'expenses'=>'+'.$num*7,
                    'money'=>$nuser['Pffbalance']+$num*7,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id7){
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id7 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*8
                );

                $arrs = array(
                    'expenses'=>'+'.$num*8,
                    'money'=>$nuser['Pffbalance']+$num*8,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id8){
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id8 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*9
                );

                $arrs = array(
                    'expenses'=>'+'.$num*9,
                    'money'=>$nuser['Pffbalance']+$num*9,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id9){
                $count2 = count($child_id2);//直推大于30
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count2*3+$count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成
                foreach ($child_id9 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*10
                );

                $arrs = array(
                    'expenses'=>'+'.$num*10,
                    'money'=>$nuser['Pffbalance']+$num*10,
                    'time'=>time(),
                    'explain'=>'后台发放',
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

         }else if($data['type'] == 4){//大于40人
            $money = $set['zpeople4'];
            foreach ($ids as $key => $value) {
             if($value >= 4 && $value < 5){
                $child_id3[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 5 && $value < 6){
                $child_id4[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 6 && $value < 7){
                $child_id5[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 7 && $value < 8){
                $child_id6[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 8 && $value < 9){
                $child_id7[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 9 && $value < 10){
                $child_id8[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 10 && $value < 11){
                $child_id9[] = $key;
             }
            }
            
            if($child_id3){
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成

                foreach ($child_id3 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*4
                );

                $arrs = array(
                    'expenses'=>'+'.$num*4,
                    'money'=>$nuser['Pffbalance']+$num*4,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id4){
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成

                foreach ($child_id4 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*5
                );

                $arrs = array(
                    'expenses'=>'+'.$num*5,
                    'money'=>$nuser['Pffbalance']+$num*5,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
             if($child_id5){
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成

                foreach ($child_id5 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*6
                );

                $arrs = array(
                    'expenses'=>'+'.$num*6,
                    'money'=>$nuser['Pffbalance']+$num*6,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id6){
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成

                foreach ($child_id6 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*7
                );

                $arrs = array(
                    'expenses'=>'+'.$num*7,
                    'money'=>$nuser['Pffbalance']+$num*7,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id7){
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成

                foreach ($child_id7 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*8
                );

                $arrs = array(
                    'expenses'=>'+'.$num*8,
                    'money'=>$nuser['Pffbalance']+$num*8,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id8){
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成

                foreach ($child_id8 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*9
                );

                $arrs = array(
                    'expenses'=>'+'.$num*9,
                    'money'=>$nuser['Pffbalance']+$num*9,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id9){
                $count3 = count($child_id3);//直推大于40
                $count4 = count($child_id4);//直推大于50
                $count5 = count($child_id5);//直推大于60
                $count6 = count($child_id6);//直推大于70
                $count7 = count($child_id7);//直推大于80
                $count8 = count($child_id8);//直推大于90
                $count9 = count($child_id9);//直推大于100 
                $num = $count3*4+$count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
                $num = round($money/$num,3);//平分下来每人分成

                foreach ($child_id9 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*10
                );

                $arrs = array(
                    'expenses'=>'+'.$num*10,
                    'money'=>$nuser['Pffbalance']+$num*10,
                    'time'=>time(),
                    'explain'=>'后台发放',
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


         }else if($data['type'] == 5){//大于50人
            $money = $set['zpeople5'];
            foreach ($ids as $key => $value) {
             if($value >= 5 && $value < 6){
                $child_id4[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 6 && $value < 7){
                $child_id5[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 7 && $value < 8){
                $child_id6[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 8 && $value < 9){
                $child_id7[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 9 && $value < 10){
                $child_id8[] = $key;
             }
            }
            foreach ($ids as $key => $value) {
             if($value >= 10 && $value < 11){
                $child_id9[] = $key;
             }
            }

            if($child_id4){
            $count4 = count($child_id4);//直推大于50
            $count5 = count($child_id5);//直推大于60
            $count6 = count($child_id6);//直推大于70
            $count7 = count($child_id7);//直推大于80
            $count8 = count($child_id8);//直推大于90
            $count9 = count($child_id9);//直推大于100 
            $num = $count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
            $num = round($money/$num,3);//平分下来每人分成
            
                foreach ($child_id4 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*5
                );

                $arrs = array(
                    'expenses'=>'+'.$num*5,
                    'money'=>$nuser['Pffbalance']+$num*5,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id5){
            $count4 = count($child_id4);//直推大于50
            $count5 = count($child_id5);//直推大于60
            $count6 = count($child_id6);//直推大于70
            $count7 = count($child_id7);//直推大于80
            $count8 = count($child_id8);//直推大于90
            $count9 = count($child_id9);//直推大于100 
            $num = $count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
            $num = round($money/$num,3);//平分下来每人分成
            
                foreach ($child_id5 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*6
                );

                $arrs = array(
                    'expenses'=>'+'.$num*6,
                    'money'=>$nuser['Pffbalance']+$num*6,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id6){
            $count4 = count($child_id4);//直推大于50
            $count5 = count($child_id5);//直推大于60
            $count6 = count($child_id6);//直推大于70
            $count7 = count($child_id7);//直推大于80
            $count8 = count($child_id8);//直推大于90
            $count9 = count($child_id9);//直推大于100 
            $num = $count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
            $num = round($money/$num,3);//平分下来每人分成
            
                foreach ($child_id6 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*7
                );

                $arrs = array(
                    'expenses'=>'+'.$num*7,
                    'money'=>$nuser['Pffbalance']+$num*7,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id7){
            $count4 = count($child_id4);//直推大于50
            $count5 = count($child_id5);//直推大于60
            $count6 = count($child_id6);//直推大于70
            $count7 = count($child_id7);//直推大于80
            $count8 = count($child_id8);//直推大于90
            $count9 = count($child_id9);//直推大于100 
            $num = $count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
            $num = round($money/$num,3);//平分下来每人分成
            
                foreach ($child_id7 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*8
                );

                $arrs = array(
                    'expenses'=>'+'.$num*8,
                    'money'=>$nuser['Pffbalance']+$num*8,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id8){
            $count4 = count($child_id4);//直推大于50
            $count5 = count($child_id5);//直推大于60
            $count6 = count($child_id6);//直推大于70
            $count7 = count($child_id7);//直推大于80
            $count8 = count($child_id8);//直推大于90
            $count9 = count($child_id9);//直推大于100 
            $num = $count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
            $num = round($money/$num,3);//平分下来每人分成
            
                foreach ($child_id8 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*9
                );

                $arrs = array(
                    'expenses'=>'+'.$num*9,
                    'money'=>$nuser['Pffbalance']+$num*9,
                    'time'=>time(),
                    'explain'=>'后台发放',
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
            if($child_id9){
            $count4 = count($child_id4);//直推大于50
            $count5 = count($child_id5);//直推大于60
            $count6 = count($child_id6);//直推大于70
            $count7 = count($child_id7);//直推大于80
            $count8 = count($child_id8);//直推大于90
            $count9 = count($child_id9);//直推大于100 
            $num = $count4*5+$count5*6+$count6*7+$count7*8+$count8*9+$count9*10;
            $num = round($money/$num,3);//平分下来每人分成
            
                foreach ($child_id9 as $key => $value) {
                $nuser = Db::name('user')->where('id',$value)->find();
                $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*10
                );

                $arrs = array(
                    'expenses'=>'+'.$num*10,
                    'money'=>$nuser['Pffbalance']+$num*10,
                    'time'=>time(),
                    'explain'=>'后台发放',
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

         }else if($data['type'] == 100){
            $money = $set['zpeoplen'];
            $touzilist = Db::name('investment')->select();//投资15万
            $touzicount_15 = Db::name('investment')->where(array('investment'=>2))->count();//投资15万
            $touzicount_3 = Db::name('investment')->where(array('investment'=>1))->count();//投资3万
            $sum = $touzicount_3*1+$touzicount_15*5;
            $num = round($money/$sum,3);
            foreach ($touzilist as $key => $value) {
                $nuser = Db::name('user')->where('id',$value['investmentid'])->find();
               if($value['investment'] == 1){//3万
                    
                    $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num
                    );

                    $arrs = array(
                        'expenses'=>'+'.$num,
                        'money'=>$nuser['Pffbalance']+$num,
                        'time'=>time(),
                        'explain'=>'社区分红',
                        'slowid'=>$value['investmentid']
                    );

               }elseif($value['investment'] == 2){//15万
                    $arrt = array(
                    'Pffbalance'=>$nuser['Pffbalance']+$num*5
                    );

                    $arrs = array(
                        'expenses'=>'+'.$num*5,
                        'money'=>$nuser['Pffbalance']+$num*5,
                        'time'=>time(),
                        'explain'=>'社区分红',
                        'slowid'=>$value['investmentid']
                    );
               }
                    Db::name('slow')->insert($arrs);
                    Db::name('user')->where("id",$value['investmentid'])->update($arrt);
            }

            // $uid = [];
            // foreach ($userinfo as $key => $value) {
            //     $touzicount = Db::name('investment')->where('investmentid',$value['id'])->count();
            //     if($touzicount>0){
            //         $uid[] = $value['id'];
            //     }
            // }

            
            // foreach ($uid as $key => $value) {
            //     $nuser = Db::name('user')->where('id',$value)->find();
            //     $arrt = array(
            //         'Pffbalance'=>$nuser['Pffbalance']+$num
            //     );

            //     $arrs = array(
            //         'expenses'=>'+'.$num,
            //         'money'=>$nuser['Pffbalance']+$num,
            //         'time'=>time(),
            //         'explain'=>'后台发放',
            //         'slowid'=>$value
            //     );
            //   Db::startTrans();
            //  try{ 
            //   Db::name('slow')->insert($arrs);
            //   Db::name('user')->where("id",$value)->update($arrt);
            //   Db::commit();
            //    } catch (\Exception $e) {
            //     // 回滚事务
            //         Db::rollback();
            //    }
            // }

         }
         $this->success("操作成功！", url("set/index"));
      }
      
      public function jtgrant(){
         $data = $this->request->param();
         $startTime = $data['startTime'];
         $endTime = $data['endTime'];
         $xlk = $data['xlk'];
         $ffm = $data['ffm'];
         $xz_uid = $data['xz_uid'];
         $stime = strtotime($data['startTime']);
         $etime = strtotime($data['endTime']);
         $where=[];
         if($etime){  
            $etime= strtotime(date('Y-m-d 23:59:59', $etime));  
         }
         if(!$startTime || !$startTime || !$xlk || !$ffm){
         	echo json_encode(array('code'=>0,'msg'=>'信息不为空！'));
         	exit;
         }
         if($stime>$etime){
         	echo json_encode(array('code'=>0,'msg'=>'开始时间不能大于结束时间！'));
         	exit;
         }

         if($xz_uid){

            $xz_uid = explode(" ", $xz_uid);

            foreach ($xz_uid as $key => $value) {
                $xzinfo = Db::name('user')->where('user_nickname',$value)->find();
                if($xzinfo){
                    $arrm[] = $xzinfo['id'];
                }else{
                    echo json_encode(array('code'=>0,'msg'=>'用户名输入有误或者不存在！'));
                    exit;
                }
                
            }
            $listinfos = [];
            $where['starttime']=['between',[$stime,$etime]];
            $where['investment']=['eq',$xlk];
            $listinfo = Db::name('investment')->where($where)->select()->toArray();
            foreach ($listinfo as $key => $value) {
                if(!in_array($value['investmentid'],$arrm)){
                    $listinfos[] = $value;
                }
            }
            $listinfo = $listinfos;
         }else{
            $where['starttime']=['between',[$stime,$etime]];
            $where['investment']=['eq',$xlk];
            $listinfo = Db::name('investment')->where($where)->select()->toArray();
         }
         if($listinfo){
         foreach ($listinfo as $key => $value) {
            if($value['investment'] == 1){//投资3万 
                $money = $ffm;
                $msg = '3万矿机静态收益';
            }else{//投资15万
                $money = $ffm; 
                $msg = '15万矿机静态收益';
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
    }else{
        echo json_encode(array('code'=>0,'msg'=>'该范围没有投资记录！'));
        exit;
    }
        echo json_encode(array('code'=>1,'msg'=>'操作成功！'));
        exit;
         
     }

}