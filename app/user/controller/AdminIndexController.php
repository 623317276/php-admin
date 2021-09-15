<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;

/**
 * Class AdminIndexController
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class AdminIndexController extends AdminBaseController
{

    /**
     * 会员
     * @adminMenu(
     *     'name'   => '会员
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '会员',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('user_admin_index_view');

        if (!empty($content)) {
            return $content;
        }

        $where   = [];
        $request = input('request.');

        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
        // print_r($data['typename']);
        if(isset($request['typename'])&&!empty($request['typename'])){ 
            if($request['typename'] == 0 || $request['typename'] == 1){
                $where['status']=['eq',$request['typename']]; 
            }elseif($request['typename'] == 2){
                $where['withed']=['eq',$request['typename']]; 
            }   
            $this->assign('type',$request['typename']);
        }

        $a =2;
        // $where['user_type'] = intval($a);
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $keywordComplex['mobile']    = ['like', "%$keyword%"];
        }
        $usersQuery = Db::name('user');

        $list = $usersQuery->whereOr($keywordComplex)->where($where)->order("created DESC")->paginate(10);
        // 获取分页显示
        $list->appends($request);
        $page = $list->render();
        $lists=$list->toArray()['data'];
        foreach ($lists as $key => $value) {
            $user_wallet = Db::name('wallet')->where('user_id',$value['id'])->find();
            $user_b = Db::name('wallet_info')->where(['wallet_id'=>$user_wallet['id'],'name'=>"STD"])->find();
            // $lists[$key]['eth'] = array_shift($user_b)['count'];
            $lists[$key]['std'] = $user_b['count'];
        }
        
        $this->assign('lists', $lists);  
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }
    //用户推荐关系图
    public function relation(){
       $data=request()->param();
       if(empty($data)){
        $where = [];
        $admins = Db::name('user')->field('id,point_id,mobile')->where($where)->select();
        $vv = array();
        foreach ($admins as $k => $v) {
            if ($k == 0) {
                $vv[$k]['id'] = $v['id'];
                $vv[$k]['name'] =  "(" . $v['mobile'] . ")";
                $vv[$k]['pid'] = 0;
            } else {
                $vv[$k]['id'] = $v['id'];
                $vv[$k]['name'] =  "(" . $v['mobile'] . ")";
                $vv[$k]['pid'] = strtoupper($v['point_id']);
            }
        }

       }else{

            if($data['subordinate'] != "" && $data['superior'] == ""){//查下級
               
                $userinfo = Db::name('user')->field('id,point_id,mobile')->where('mobile',$data['subordinate'])->find();
                $admins = $this->unbraller($userinfo['id']);   
                $vv = array();
                foreach ($admins as $k => $v) {
            if ($k == 0) {  
                $vv[$k]['id'] = $v['id'];
                $vv[$k]['name'] =  "(" . $v['mobile'] . ")";
                $vv[$k]['pid'] = 0;
            } else {
                $vv[$k]['id'] = $v['id'];
                $vv[$k]['name'] =  "(" . $v['mobile'] . ")";
                $vv[$k]['pid'] = strtoupper($v['point_id']); 
                }
                }

            }
            if($data['superior'] != "" && $data['subordinate'] == ""){//查上级

                $userinfo = Db::name('user')->field('id,point_id,mobile')->where('mobile',$data['superior'])->find();
                $admins = $this->eachs($userinfo['id']);
                $vv = array();
                foreach ($admins as $k => $v) {
            if ($k == 0) {
                $vv[$k]['id'] = $v['id'];
                $vv[$k]['name'] = "(" . $v['mobile'] . ")";
                $vv[$k]['pid'] = 0;
            } 
                }
                
            }
            if($data['superior'] == "" && $data['subordinate'] == ""){//所有
               $where = ['user_type'=>2];
        $admins = Db::name('user')->field('id,point_id,mobile')->where($where)->select();
        $vv = array();
        foreach ($admins as $k => $v) {
                    if ($k == 0) {
                        $vv[$k]['id'] = $v['id'];
                        $vv[$k]['name'] =  "(" . $v['mobile'       ] . ")";
                      $vv[$k]['pid'] = 0;
                    } else {
                        $vv[$k]['id'] = $v['id'];
                        $vv[$k]['name'] =  "(" . $v['mobile'       ] . ")";
                     $vv[$k]['pid'] = strtoupper($v['point_id']);
                    }
                }
            }
            if($data['superior'] != "" && $data['subordinate'] != ""){//所有
               $where = [];
               $admins = Db::name('user')->field('id,point_id,mobile')->where($where)->select();
               $vv = array();
                foreach ($admins as $k => $v) { 
                    if ($k == 0) {
                        $vv[$k]['id'] = $v['id'];
                        $vv[$k]['name'] =  "(" . $v['mobile'] . ")";
                      $vv[$k]['pid'] = 0;
                    } else {
                        $vv[$k]['id'] = $v['id'];
                        $vv[$k]['name'] =  "(" . $v['mobile'] . ")";
                     $vv[$k]['pid'] = strtoupper($v['point_id']);
                    }
                }
            }

       }
        
     //    echo '<pre>'; print_r($vv); die;
        $this->assign('vv',json_encode($vv));
        return $this->fetch();
    }


    public function unbraller($id){
    // $id = $_GET['id'];
      
    $children = $temp = $money = array();
    $ones = Db::name('user')->where('id',$id)->find();
    $children[] = $ones;
    $userinfo = Db::name('user')->select()->toArray();
    $this->findBottom($userinfo, $id, $children);
    // $investment = Db::name('investment')->select()->toArray();
    return $children; 
    }
    //伞下所有人的ID
    public function unbrallers($id){
    // $id = $_GET['id'];
      
    $children = $temp = $money = array();
    $userinfo = Db::name('user')->select()->toArray();
    $this->findBottoms($userinfo, $id, $children);
    // $investment = Db::name('investment')->select()->toArray();
    return $children; 
    }
    public function unbrallerss($id){
    // $id = $_GET['id'];
      
    $children = $temp = $money = array();
    $ones = Db::name('user')->where('id',$id)->find();
    $children[] = $ones['id'];

    $userinfo = Db::name('user')->select()->toArray();
    $this->findBottoms($userinfo, $id, $children);
    // $investment = Db::name('investment')->select()->toArray();
    return $children; 
    }
    public function findBottoms($userinfo, $id, &$children){
    if(!$id){
            return 'parent_id is null';
        }
        
        foreach ($userinfo as $key => $value) {
           if($value['point_id'] == $id){
                $children[] = $value['id'];
                $this->findBottoms($userinfo, $value['id'], $children);
           }
        }

        return $children;
  }



public function findBottom($userinfo, $id, &$children){
    if(!$id){
            return 'parent_id is null';
        }
        
        foreach ($userinfo as $key => $value) {
           if($value['point_id'] == $id){
                $children[] = $value;
                $this->findBottom($userinfo, $value['id'], $children);
           }
        }

        return $children;
}

 // 找上级
    function eachs($id){
    // $id = 60;
    $parent = $newArr = array();
    $userinfo = Db::name('user')->select()->toArray();
    foreach ($userinfo as $key => $value) {
        $newArr[$value['id']] = $value;
    }
    $this->eachs111111($userinfo, $newArr[$id]['parent_id'], $parent);
     return $parent;
    // print_r($parent);die;
  }

  public function eachs111111($userinfo, $id, &$parent)
    {
        if(!$id){
            return 'parent_id is null';
        }
        foreach ($userinfo as $key => $value) {
           if($value['id'] == $id){
                $parent[] = $value;
                $this->eachs111111($userinfo, $value['parent_id'], $parent);
           }
        }
        return $parent;
    }

    /**
     * 本站用户拉黑
     * @adminMenu(
     *     'name'   => '本站用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户拉黑',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $result = Db::name("user")->where(["id" => $id])->setField('status', 0);
            if ($result) {
                $this->success("会员拉黑成功！", "adminIndex/index");
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }
    public function add()
    {
        $id = $this->request->param();

        if($id){
        $result = Db::name("user")->where(["id" => $id['id']])->find();
        $this->assign('user',$result);
        }

        return $this->fetch();
    }
    public function czhi()
    {
        $data = $this->request->param();
        $this->assign('uid',$data['id']);
        return $this->fetch();
    }
    public function chongzhi()
    {
        $data = $this->request->param();
        $nuser = Db::name('wallet')->where('user_id',$data['uid'])->find();
        //钱包信息
        $wallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$nuser['id'],'name'=>'STD'))->find();
        $money = $data['money'];//金额
        if(!$data['money']){
            $this->error('金额不为空!');
        }
        if($data['money']<0){
            $this->error('金额不能小于0!');
        }
        if($data['leixing'] == 1){//std余额 
              if($data['zeng'] == 1){//增加
                $moneya = $wallet_info['count']+$money;
                $moneys = $money;
                $msg = '后台增加账户余额';
              }elseif($data['zeng'] == 2){//减少
                if($wallet_info['count']<$money){
                $this->error('账户余额不足!');
              }
                $moneya = $wallet_info['count']-$money;
                $moneys = '-'.$money;
                $msg = '后台减少账户余额';
              }
              $arrt = array(
                'count'=>$moneya
              );
              $arrs = array(
                  'num'=>$moneys,
                  'time'=>time(),
                  'uid'=>$data['uid'],
                  'type'=>4
              );

        }
        // elseif($data['leixing'] == 2){//收益余额
        //       if($data['zeng'] == 1){//增加
        //         $moneya = $nuser['Pffbalance']+$money;
        //         $moneys = '+'.$money;
        //         $msg = '后台增加收益余额';
        //       }elseif($data['zeng'] == 2){//减少
        //         if($nuser['Pffbalance']<$money){
        //         $this->error('账户余额不足!');
        //       }
        //         $moneya = $nuser['Pffbalance']-$money;
        //         $moneys = '-'.$money;
        //         $msg = '后台减少收益余额';
        //       }
        //       $arrt = array(
        //         'Pffbalance'=>$moneya
        //       );
        //       $arrs = array(
        //           'expenses'=>$moneys,
        //           'money'=>$moneya,
        //           'time'=>time(),
        //           'explain'=>$msg,
        //           'slowid'=>$data['uid']
        //       );

        // }

        
        
        
     
              Db::name('sylistbase')->insert($arrs);
              Db::name('wallet_info')->where(array('wallet_id'=>$nuser['id'],'name'=>'STD'))->update($arrt);  
            $this->success("操作成功！", "adminIndex/index");
              
        
    }
    
    
    public function editPost()
    {
        $data = $this->request->param();
        $res=Db::name('user')
                ->where('id',$data['id'])
                ->update([
                    'user_nickname'=>$data['username'],
                    'user_email'=>$data['emial'],
                    'mobile'=>$data['phone']
                ]);

        if($res){
                $this->success('编辑成功!',url('AdminIndex/index'));
            }else{
                $this->error('网络错误请稍后重试!');
            }
        
        
      
    }


    /**
     * 本站用户启用
     * @adminMenu(
     *     'name'   => '本站用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = input('param.id', 0, 'intval');

        if ($id) {
            Db::name("user")->where(["id" => $id])->setField('status', 1);
            $this->success("会员启用成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
    public function dell()
    {
        $data = $this->request->param();
        $result = Db::name("user")->where(["id" => $data['id']])->delete();
        if ($result) {
            $this->success("操作成功！", "adminIndex/index");
        } else {
           $this->error("操作失败！", "adminIndex/index");
        }
        
    }
    public function txkg(){ 
        $data = $this->request->param();
        if ($data) {
            Db::name("user")->where(["id" => $data['id']])->setField('withed', $data['withed']);
            $this->success("操作成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
    public function open(){ 
        $data = $this->request->param();
        if ($data) {
            Db::name("user")->where(["id" => $data['id']])->setField('open', $data['open']);
            $this->success("操作成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
    public function zz_vip(){ 
        $data = $this->request->param();
        if ($data) {
            Db::name("user")->where(["id" => $data['id']])->setField('zz_vip', $data['zz_vip']);
            $this->success("操作成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
    
    public function number_pay(){
        $data = $this->request->param();
        if ($data) {
            Db::name("user")->where(["id" => $data['id']])->setField('number_pay', $data['number_pay']);
            $this->success("操作成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
    
    //修改用户的推荐码和节点吗
    public function xg(){
        $data = $this->request->param();
        if($data['type'] == 1){//邀请码
            $res = Db::name('user')->where('id',$data['id'])->update(array('code'=>$data['code']));
        }else{//节点码
            $res = Db::name('user')->where('id',$data['id'])->update(array('pcode'=>$data['code']));
        } 
        if($res){
            echo json_encode(array('code'=>1,'msg'=>'操作成功'));
            exit;
        }else{
            echo json_encode(array('code'=>0,'msg'=>'操作失败'));
            exit;
        }
    }
    public function yjcx(){
        $data = $this->request->param();
        //伞下所有人ID
        $admins = $this->unbrallerss($data['id']); 
        $numss = [];

        foreach ($admins as $key => $value) {
            $res = Db::name('cblist')->where(array('uid'=>$value,'status'=>1))->select()->toArray();
            if($res){
              $numss[] = $res; 
            }
            
        }
        
        $newnum = [];
        foreach ($numss as $key => $value) {
            foreach ($value as $k => $v) {
                $wtime = $v['time']+24*3600;
                $ntime = time();
                if($ntime >= $wtime){
                    $newnum[] = $v['nums'];  
                }
            }
            
        }

        //总业绩
        $zyj = array_sum($newnum);  
     

        //计算推荐业绩
        //查询直推用户
        $ztnum = [];
        $zuserlist = Db::name('user')->where('point_id',$data['id'])->select()->toArray();
        $zuserlists = Db::name('user')->where('parent_id',$data['id'])->select()->toArray();
        foreach ($zuserlists as $key => $value) {
            $res1 = Db::name('cblist')->where(array('uid'=>$value['id'],'status'=>1))->select()->toArray();
            if($res1){    
              $ztnum[] = $res1; 
            }
        }
        $ztnums = [];
        foreach ($ztnum as $key => $value) {
            foreach ($value as $k => $v) {
                $wtime = $v['time']+24*3600;
                $ntime = time();
                if($ntime >= $wtime){
                    $ztnums[] = $v['nums'];  
                }
            }
            
        }
        //直推业绩
        $ztzyj = array_sum($ztnums); 
        
        //计算大小区业绩 
        $dxqusnum = [];
        foreach ($zuserlist as $key => $value) {
        $dxqu = []; 
        $dxqus = []; 
            $dqyj = $this->unbrallerss($value['id']);
            foreach ($dqyj as $k => $v) {
                $res11 = Db::name('cblist')->where(array('uid'=>$v,'status'=>1))->select()->toArray();
                if($res11){         
                    $dxqu[] = $res11; 
                }
            }
            foreach ($dxqu as $k => $v) {
                foreach ($v as $keys => $values) {
                    $wtime = $values['time']+24*3600;	
                    $ntime = time();
                    if($ntime >= $wtime){
                        $dxqus[] = $values['nums'];  
                    }
                }
                
            }
           
            $dxqusnum[] = array_sum($dxqus);

        }

        
        if(!empty($dxqusnum)){
            //最大业绩区
            $maxs = max($dxqusnum);
            $xiao = array_search($maxs, $dxqusnum);
            array_splice($dxqusnum, $xiao, 1);
            //最小区业绩
            $xqyj =  array_sum($dxqusnum);
        }else{  
            $maxs = 0;
            $xqyj = 0;
        }
        $arrg = array(
            'zyj'=>$zyj,
            'ztyj'=>$ztzyj,
            'maxs'=>$maxs,
            'xqyj'=>$xqyj
        );

        echo json_encode($arrg);     
        exit;

    }
}
