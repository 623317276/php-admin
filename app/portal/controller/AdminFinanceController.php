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
namespace app\portal\controller;

use cmf\controller\AdminBaseController;
use FontLib\Table\Type\name;
use think\Db;
use newz\apiurl;

class AdminFinanceController extends AdminBaseController
{
    /**
     * 投资列表
     * @adminMenu(
     *     'name'   => '投资列表',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '投资列表',
     *     'param'  => ''
     * )
     */
    
    public function jwadd()
    {
        $data=request()->param();
        if($data){//修改
            $this->assign('id',$data['id']);
            $res=Db::name('jwinfo')->where('id',$data['id'])->find();
            $arrg = explode(',',$res['address']);
            $this->assign('arrg',$arrg);
            $status = 1;
            $this->assign('jwinfos',$res);
        }else{
            $arrt = array(
                'jwfree'=>"",
                'jitype'=>"",
                'xaddress'=>""
            );
            $status = 0;
            $this->assign('id','');
            $this->assign('jwinfos',$arrt);    
        }
        $this->assign('status',$status);
        return $this->fetch();
    }

    public function declaration()
    {
        $data=request()->param();
        $where=[];
        if(isset($data['investment'])&&!empty($data['investment'])){
            $where['investment']=['eq',$data['investment']];
            $this->assign('id',$data['investment']);
        }
        $keywordComplex = [];
        if(isset($data['username'])&&!empty($data['username'])){
            $keyword = $data['username'];
            $keywordComplex['user_nickname|mobile']    = ['like', "%$keyword%"];

            $this->assign('username',$data['username']);
        }

        $list=Db::name('investment')
        ->alias('a')
        ->join('user b','a.investmentid=b.id')
        ->whereOr($keywordComplex)
        ->where($where)
        ->order('cmf_investment.Id desc')
        ->paginate(10);
        // dump($list->toArray(),1,'<pre>',0);
        $investment=Db::name('investmenttype')->select()->toArray();

        $list->appends($data);

        $page=$list->render();
        $this->assign('investment',$investment);
        $lists=$list->toArray()['data'];
        foreach ($lists as $k=>$v){
            $userin = Db::name('user')->where('id',$v['investmentid'])->find();
            Db::name('investment')->where('investmentid',$v['investmentid'])->update(['username'=>$userin['user_email']]);
            $lists[$k]['username'] = $userin['user_nickname'];
            $lists[$k]['phone'] = $userin['mobile'];
        }
        $this->assign('list',$lists);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //兑换明细
    public function dhuan()
    {
        $data=request()->param();
        $where=[];
        if(isset($data['investment'])&&!empty($data['investment'])){
            $where['investment']=['eq',$data['investment']];
            $this->assign('id',$data['investment']);
        }
        $keywordComplex = [];
        if(isset($data['username'])&&!empty($data['username'])){
            $keyword = $data['username'];
            $keywordComplex['user_nickname|mobile']    = ['like', "%$keyword%"];

            $this->assign('username',$data['username']);
        }

        $list=Db::name('dhuan')
        ->alias('a')
        ->join('user b','a.uid=b.id')
        ->whereOr($keywordComplex)
        ->where($where)
        ->order('cmf_dhuan.id desc')
        ->paginate(10);
        // dump($list->toArray(),1,'<pre>',0);
        $investment=Db::name('investmenttype')->select()->toArray();

        $list->appends($data);

        $page=$list->render();
        $lists=$list->toArray()['data'];
        $this->assign('list',$lists);
        $this->assign('page',$page);
        return $this->fetch();
    }
    /**
     * 删除投资记录
     * @adminMenu(
     *     'name'   => '删除投资记录',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除投资记录',
     *     'param'  => ''
     * )
     */
    public function deletedec()
    { 
        $data=request()->param();
        if(isset($data['id'])&&$data['id']){
            $where['id']=array('eq',$data['id']);
        }elseif(isset($data['ids'])&&$data['ids']){
            $where['id']=array('in',$data['ids']);
        }
        $res=Db::name('investment')->where($where)->delete();
        if($res){
            $this->success('删除成功!',url('AdminFinance/declaration'));
        }else{
            $this->error('网络错误请稍后重试!');
        }
    }
    /**
     * 团队奖励明细
     * @adminMenu(
     *     'name'   => '团队奖励明细',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '团队奖励明细',
     *     'param'  => ''
     * )
     */
    public function teammoney()
    {
        $data=request()->param();
        $where=[];
        if(isset($data['typename'])&&!empty($data['typename'])){
            $where['type']=['eq',$data['typename']]; 
            $this->assign('type',$data['typename']);
        }
        if(isset($data['username'])&&!empty($data['username'])){
            // $where['mobile']=['like','%'.$data['username'].'%'];
            $where['mobile']=$data['username'];
            $this->assign('username',$data['username']);
        }
        $list=Db::name('sylistbase')
        ->alias('a')
        ->join('user b','b.id=a.uid')
        ->where($where)
        ->order('tb_sylistbase.id desc')
        ->field('tb_sylistbase.id,mobile,num,tb_sylistbase.type,tb_sylistbase.time,a.dfname')
        ->paginate(10);
        
        $list->appends($data);
        $page=$list->render();
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    /**
     * 删除收益记录
     * @adminMenu(
     *     'name'   => '删除收益记录',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除收益记录',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $data=request()->param();
        if(isset($data['id'])&&$data['id']){
            $where['id']=array('eq',$data['id']);
        }elseif(isset($data['ids'])&&$data['ids']){
            $where['id']=array('in',$data['ids']);
        }
        $res=Db::name('staticmoney')->where($where)->delete();
        if($res){
            $this->success('删除成功!',url('AdminFinance/teammoney'));
        }else{
            $this->error('网络错误请稍后重试!');
        }
    }
    /**
     * 存币记录
   
     */
    public function recharge()
    {
        $data=request()->param();
        $where=[];
        if(isset($data['username'])&&!empty($data['username'])){
            $where['mobile']=['like','%'.$data['username'].'%'];    
            $this->assign('username',$data['username']);
        }
        if(isset($data['id'])&&!empty($data['id'])){
            $where['a.id']=$data['id'];    
            $this->assign('id',$data['id']);
        }
        $list=Db::name('cblist')
        ->alias('a')
        ->join('user b','b.id=a.uid')
        ->where($where)
        ->order('tb_cblist.id desc')
        ->field('b.mobile,a.id,a.nums,a.status,a.days,a.time')
        ->paginate(10);
        $list->appends($data);
        $page=$list->render();
        $lists=$list->toArray()['data'];    
         
        $this->assign('list',$lists);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //发布的广告位上下架
    public function sxj(){
        $data=request()->param();
        $res=Db::name('touf')->where('id',$data['id'])->update(array('type'=>$data['type']));
        if($res){
            $this->success('删除成功!',url('AdminFinance/recharge'));
        }else{
            $this->error('网络错误请稍后重试!');
        }
    }
    /**
     * 删除EOS提币记录
     * @adminMenu(
     *     'name'   => '删除EOS提币记录',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除EOS提币记录',
     *     'param'  => ''
     * )
     */
    public function deletere()
    {
        $data=request()->param();
        
        if(isset($data['id'])&&$data['id']){
            $where['id']=array('eq',$data['id']);
        }elseif(isset($data['ids'])&&$data['ids']){
            $where['id']=array('in',$data['ids']);
        }
        $res=Db::name('invest')->where($where)->delete();
        if($res){
            $this->success('删除成功!',url('AdminFinance/slow'));
        }else{
            $this->error('网络错误请稍后重试!');
        }
    }
    /**
     * USDT提币明细
     * @adminMenu(
     *     'name'   => 'USDT提币明细',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => 'USDT提币明细',
     *     'param'  => ''
     * )
     */
    public function withdrawal()
    {
        $data=request()->param();
        $where=[];

        if(isset($data['username'])&&!empty($data['username'])){
            $where['mobile']=['like','%'.$data['username'].'%'];
            $this->assign('username',$data['username']);
        }
        $list=Db::name('czbase')
        ->alias('a')
        ->join('user b','a.user_id=b.id')
        ->where($where)
        ->order('a.id desc')      
        ->field('a.id,b.mobile,a.symbol,a.addr,a.amount,a.time')
        ->paginate(10);
        $list->appends($data);  
        $page=$list->render();
        $lists=$list->toArray()['data'];
        // foreach ($lists as $key => $value) {
        //     $userinfo = Db::name('user')->where('id',$value['user_id'])->find();
        //     $lists[$key]['mobile'] = $userinfo['mobile'];
        // }
        $this->assign('list',$lists);
        $this->assign('page',$page);
        return $this->fetch();
    }
    public function jwcaozuo(){
        $data=request()->param();
        if($data['id'] != ""){
            $arrs = array(
            'address'=>$data['provinc'].','.$data['city'].','.$data['district'].','.$data['xaddress'],
            'time'=>time(),
            'jwfree'=>$data['frees'],
            'jitype'=>$data['lx'],
            'xaddress'=>$data['xaddress']
            );   
            $res=Db::name('jwinfo')->where('id',$data['id'])->update($arrs);
        }else{
            $arrs = array(
            'address'=>$data['provinc'].','.$data['city'].','.$data['district'].','.$data['xaddress'],
            'time'=>time(),
            'jwfree'=>$data['frees'],
            'jitype'=>$data['lx'],
            'xaddress'=>$data['xaddress']
            );   
            $res=Db::name('jwinfo')->insert($arrs);  
        }
        
        if($res){
            echo json_encode(array('code'=>1));
            exit;
        }else{
            echo json_encode(array('code'=>0)); 
            exit;
        }
    }
    /**
     * 删除USDT提币记录
     * @adminMenu(
     *     'name'   => '删除USDT提币记录',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除USDT提币记录',
     *     'param'  => ''
     * )
     */
    public function deletesw()
    {
        $data=request()->param();
        if(isset($data['id'])&&$data['id']){
            $where['id']=array('eq',$data['id']);
        }elseif(isset($data['ids'])&&$data['ids']){
            $where['id']=array('in',$data['ids']);
        }
        $res=Db::name('withdrawal')->where($where)->delete();
        if($res){
            $this->success('删除成功!',url('AdminFinance/slow'));
        }else{
            $this->error('网络错误请稍后重试!');
        }
    }
    /**
     * eos提币状态审核
     */
    public function verifye(){
        $data=request()->param();

            $find = Db::name('xwithdrawal')->where('id',$data['id'])->find();      
            if($data['status'] == 1){//通过 
                Db::name('xwithdrawal')->where('id',$data['id'])->update(['status'=>1]);
                $userinfo = Db::name('user')->where('id',$find['memo'])->find(); 
                    //总表记录
                $arrb = array( 
                    'expenses'=> '-'.$find['tis'],
                    'money'=>$userinfo['comc'],  
                    'type'=>2,
                    'time'=>time(),
                    'explain'=>'COMC提现',
                    'slowid'=>$find['memo'],
                    // 'eosmoney'=>$userinfo['EOSbalance'] + $data['investnum'] , 
                    'category'=>2

                 );
                $ress = Db::name('slow')->insert($arrb); 

                    echo json_encode(array('code'=>1,'msg'=>'操作成功'));die;
                    // $this->success($result['message']); 
                

            }else{//驳回
                 $list = Db::name('user')->where('id',$find['memo'])->find();
                 $ads = array(
                    'comc'=>$list['comc']+$find['tis'],
                 );

                 $results = Db::name('user')->where('id',$find['memo'])->update($ads);
                 if($results){
                    Db::name('xwithdrawal')->where('id',$data['id'])->update(['status'=>2]);
                 }
                // $this->redirect('AdminFinance/recharge');
                 echo json_encode(array('code'=>1,'msg'=>'操作成功'));die;

            }

       
    }

    public function get_api_data($url,$data){
        $data = http_build_query($data);
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $data); 
        $output = curl_exec($ch); 
        curl_close($ch);
        $result=json_decode($output,true);

        return $result;
    }
    /**
     * usdt提币状态审核
     */
    public function verify(){
        $data=request()->param();
        if($data['status'] == 1){
            $usdtti = Db::name('withdrawal')->where('Id',$data['id'])->find();
            $url = 'http://usdt.cn/usdt/trade2?from='.$find['wallets'].'&to='.$find['address'].'&quantity='.$find['quantity'].'&memo='.$find['momes'];       
                $result = $this->hqingPost($url); 

            $res = Db::name('withdrawal')->where('Id',$data['id'])->update(['status'=>$data['status'],'verifytime'=>time()]);
            if($res){
                $find = Db::name('withdrawal')->order('verifytime desc')->limit(0,1)->find();
                Db::name('user')->where('id',$find['withdrawalid'])->update(['USDTbalance'=>$find['nextnum']]);
                $list = Db::name('user')->where('id',$find['withdrawalid'])->find();
                $a = "-";
                $c = $a.$find['withdrawalnum'];
                $res2 = Db::name('slow')->insert([
                    'expenses' => $c,
                    'money'=>$list['USDTbalance'],
                    'type'=>'1',
                    'explain'=>'USDT提币',
                    'time'=>time(),
                    'slowid'=>$list['id'],
                    'username'=>$list['user_login'],
                    'category'=>'1',
                    'num'=>$find['withdrawalnum']
                ]);
                if($res2)
                {
                    $this->redirect('AdminFinance/withdrawal');
                }else{
                    $this->error('网络错误请稍后重试!');
                }

            }
        }
       else{
            $li = Db::name('withdrawal')->where('Id',$data['id'])->find();
            $te = $li['withdrawalnum'] + $li['nextnum'];
           $results = Db::name('user')->where('id',$li['withdrawalid'])->update(['USDTbalance'=>$te]);

           if($results){
               Db::name('withdrawal')->where('Id',$data['id'])->update(['status'=>2]);
           }
           $this->redirect('AdminFinance/withdrawal');
        }
    }
    /**
     * 交易记录明细
     * @adminMenu(
     *     'name'   => '交易记录明细',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '交易记录明细',
     *     'param'  => ''
     * )
     */
    public function slow()
    {
        $session_admin_id = session('ADMIN_ID');
        $data=request()->param();
        $where=[];
        if(isset($data['type'])&&!empty($data['type'])){
            $where['type']=['eq',$data['type']];
            $this->assign('id',$data['type']);
        }
        $keywordComplex = [];
        if(isset($data['username'])&&!empty($data['username'])){
            $keyword = $data['username'];
            $keywordComplex['mobile']    = ['like', "%$keyword%"];

            $this->assign('username',$data['username']);
        }
        if(isset($data['status']) && in_array($data['status'], [1,2,3,4])){
            if($data['status'] == 4){
                $w = 0;
            }else{
                $w = $data['status'];
            }
            $where['tb_tbbase.status']=['eq',$w];
            $this->assign('status',$data['status']);
        }
        if(isset($data['addr']) && !empty($data['addr'])){
            $where['tb_tbbase.addr']=['eq',$data['addr']];
            $this->assign('addr',$data['addr']);
        }
        if(!empty($data['statrTime']) && !empty($data['endTime'])){
            $stime = strtotime($data['statrTime']);
            $etime = strtotime($data['endTime']);
            if($etime){
                $etime= strtotime(date('Y-m-d 23:59:59', $etime));  
            }

            if($etime < $stime){
                $this->error('请选取正确时间!');
            }else{
                $where['time']=['between',[$stime,$etime]];
                $this->assign('stime',$data['statrTime']);
                $this->assign('etime',$data['endTime']);
            }
        }
        $list=Db::name('tbbase')
            ->alias('a')
            ->join('user b','a.uid=b.id')
            ->whereOr($keywordComplex)
            ->where($where)
            ->order('tb_tbbase.id desc')
            ->field('tb_tbbase.id,mobile,num,tb_tbbase.num,tb_tbbase.time,addr,tb_tbbase.status,fee,snum,tb_tbbase.type')    
            ->paginate(10);
        $list->appends($data);
        $page=$list->render();
        $lists=$list->toArray()['data'];
        /*   echo "<pre>";
          print_r($lists);
           echo "<pre>";*/

        //  今日总提币量
        $time1 = strtotime(date('Y-m-d 0:0:0'));
        $time2 = strtotime(date('Y-m-d 23:59:59'));
        $tbwhere['time'] = ['between',[$time1,$time2]];
        $total_num = Db::name('tbbase')->field(['sum(snum) as snum'])->where($tbwhere)->where('status','in', '1,3')->find();
        
        $this->assign('total_num',$total_num['snum']);
        
        $this->assign('list',$lists);
        $this->assign('page',$page);
        return $this->fetch();
    }
    
    // 空投活动页面
    public function airdrop()
    {
        $data=request()->param();
        $where=[];
        
        $lists = array();
        $page = '';
        if(!empty($data['statrTime']) && !empty($data['endTime'])){
            $stime = $data['statrTime'].' 00:00:00';
            $etime = $data['endTime'].' 23:59:59';
            $where['created']=['between',[$stime,$etime]];
            $this->assign('stime',$data['statrTime']);
            $this->assign('etime',$data['endTime']);
            
            $list=Db::name('user')
                ->field('id,mobile,created')
                ->where($where)   
                ->paginate(9999);
            $list->appends($data);
            $page=$list->render();
            $lists=$list->toArray()['data'];

        }
        
        // $this->assign('stime','2020-12-12');
        // $this->assign('etime','2020-12-12');
        
        // 执行空投操作
        if(!empty($data['airdrop']) && $data['airdrop'] == 'run'){
            if(!isset($data['num']) || empty($data['num'])){
                echo "<script>alert('数量错误');</script>";
                echo '<script>window.location.href = "'.url('airdrop').'"</script>';
                die;
                // $this->error('空投数量错误!');
            }
            $userids = array_column($lists, 'id');
            $wallet = Db::name('wallet')->field('id,user_id')->where('user_id','in', $userids)->select()->toArray();
            $walletIndexBy = indexBy($wallet, 'user_id');
            Db::startTrans();
            try{
                foreach ($walletIndexBy as $v){
                    Db::name('wallet_info')->where('wallet_id', $v['id'])->where('name','STD')->setInc('count' , $data['num']);
                    $arrn = array(
        				'uid'=>$v['user_id'],
        				'num'=>$data['num'],
        				'time'=>time(),
        				'type'=>16 // 空投赠送
        			);
        			Db::name('sylistbase')->insert($arrn);
                }
                Db::commit();
                echo "<script>alert('空投成功');</script>";
                echo '<script>window.location.href = "'.url('airdrop').'"</script>';
                die;
            } catch (\Exception $e) {	
                // 回滚事务
                 Db::rollback();	
                 echo "<script>alert('空投失败');</script>";
                 echo '<script>window.location.href = "'.url('airdrop').'"</script>';
                 die;
                //  $this->error('空投失败!');
           }
           
        }
        
        $this->assign('list',$lists);
        $this->assign('page',$page);
        return $this->fetch();
    }
    
    
    // 扣除存币页面
    public function dec_cb()
    {
        $data=request()->param();
        $where=[];
        
        $lists = array();
        $page = '';
        $where['status'] = 1;
        if(!empty($data['min_num']) && !empty($data['max_num'])){
            $list=Db::name('cblist')
                ->field('uid,sum(nums) as nums,time')
                ->where($where)
                ->group('uid')
                ->having("sum(nums) between ".$data['min_num']." and ".$data['max_num'])
                ->paginate(9999);
            $list->appends($data);
            $page=$list->render();
            $lists=$list->toArray()['data'];
        // echo Db::name('cblist')->getlastsql();
        
            if(!empty($lists)){
                $uids = array_column($lists, 'uid');
                $user = Db::name('user')->field('id,mobile')->where('id', 'in', $uids)->select();
                $user = indexBy($user, 'id');
                // 查询出每个用户最大存币的一笔
                $max_cb = Db::name('cblist')->field('*,max(nums) as max_nums')->where('uid', 'in', $uids)->where('status',1)->group('uid')->select()->toArray();
                $max_cb = indexBy($max_cb, 'uid');
                // die;
                foreach ($lists as $key => $val){
                    // 写入会员账号显示
                    if(isset($user[$val['uid']])){
                        $lists[$key]['mobile'] = $user[$val['uid']]['mobile'];
                    }else{
                        $lists[$key]['mobile'] = '';
                    }
                    if(isset($max_cb[$val['uid']])){
                        // 如果当前用户最大存币的一笔小于扣除量，就不操作该用户
                        if($max_cb[$val['uid']]['max_nums'] < $data['dec_num']){
                            unset($lists[$key]);
                            continue;
                        }
                        $lists[$key]['max_nums'] = $max_cb[$val['uid']]['max_nums'];
                    }else{
                        $lists[$key]['max_nums'] = '0';
                    }
                }
            }
            
        }
        // print_r($lists);die;
        
        // 执行空投操作
        if(!empty($data['dec_cb']) && $data['dec_cb'] == 'run'){
            if(!isset($data['dec_num']) || empty($data['dec_num'])){
                echo "<script>alert('数量错误');</script>";
                echo '<script>window.location.href = "'.url('dec_cb').'"</script>';
                die;
                // $this->error('数量错误!');
            }
            $userids = array_column($lists, 'uid');
            $cblist = Db::name('cblist')->field('uid,max(nums) as max_nums')->where('uid','in', $userids)->group('uid')->select()->toArray();
            // print_r($cblist);die;
            Db::startTrans();
            try{
                foreach ($cblist as $v){
                    // 查询出来用户最大的存币记录，在这一笔存币上面扣除对应的数量
                    $one_cb = Db::name('cblist')->where('uid', $v['uid'])->where('status',1)->where('nums',$v['max_nums'])->find();
                    if(empty($one_cb)){
                        Db::rollback();
                        echo "<script>alert('扣除失败：请把信息反馈给技术人员->'".json_encode($v).");</script>";
                        echo '<script>window.location.href = "'.url('dec_cb').'"</script>';
                        die;
                    }
                    Db::name('cblist')->where('id', $one_cb['id'])->setDec('nums' , $data['dec_num']);
                    $arrn = array(
        				'uid'=>$v['uid'],
        				'num'=>-$data['dec_num'],
        				'time'=>time(),
        				'type'=>17, // 后台扣除存币量
        				'dfname'=>$one_cb['id'] // 记录cblist表的id，便于排查问题
        			);
        			Db::name('sylistbase')->insert($arrn);
                }
                Db::commit();
                echo "<script>alert('存币量扣除成功');</script>";
                echo '<script>window.location.href = "'.url('dec_cb').'"</script>';
                die;
            } catch (\Exception $e) {	
                // 回滚事务
                 Db::rollback();	
                echo "<script>alert('存币量扣除失败');</script>";
                echo '<script>window.location.href = "'.url('dec_cb').'"</script>';
                die;
                //  $this->error('空投失败!');
           }
           
        }
        
        $this->assign('min_num',isset($data['min_num']) ? $data['min_num'] : 0);
        $this->assign('max_num',isset($data['max_num']) ? $data['max_num'] : 0);
        $this->assign('dec_num',isset($data['dec_num']) ? $data['dec_num'] : 0);
        $this->assign('list',array_values($lists));
        $this->assign('page',$page);
        return $this->fetch();
    }
    
    
    /**
     * 删除交易记录
     * @adminMenu(
     *     'name'   => '删除交易收益记录',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除交易收益记录',
     *     'param'  => ''
     * )
     */
    public function deleteslow()
    {
        $data=request()->param();
        if(isset($data['id'])&&$data['id']){
            $where['id']=array('eq',$data['id']);
        }elseif(isset($data['ids'])&&$data['ids']){
            $where['id']=array('in',$data['ids']);
        }
        $res=Db::name('slow')->where($where)->delete();
        if($res){
            $this->success('删除成功!',url('AdminFinance/slow'));
        }else{
            $this->error('网络错误请稍后重试!');
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

    public function chongzhi(){

         $data=request()->param();
      
        $where=[];
        if(isset($data['username'])&&!empty($data['username'])){
            $where1['user_email']=['like','%'.$data['username'].'%'];
            $userinfos = Db::name('user')->where($where1)->find();
            
            $where['memo']=['eq',$userinfos['wallet']];
            $this->assign('username',$data['username']);
        }
        
        $list=Db::name('xecharge')->where($where)->order('id desc')->paginate(10);

        $list->appends($data);
        $page=$list->render();
        $lists=$list->toArray()['data'];
        foreach ($lists as $key => $value) {
            $userin = Db::name('user')->where('wallet',$value['memo'])->find();

            Db::name('xwithdrawal')->where('memo',$value['memo'])->update(['username'=>$userin['user_email']]);
            $lists[$key]['username'] = $userin['user_email'];
            $lists[$key]['block_time_stamp'] = $value['block_time_stamp']+8*3600;
        } 

        $this->assign('list',$lists);
        $this->assign('page',$page);
        return $this->fetch();
    }
    public function deleterecz(){
        $data=request()->param();
        $id = $data['id'];
        $deletes = Db::name('xecharge')->where('id',$id)->delete();
        if($deletes){
            $this->success('删除成功!',url('AdminFinance/chongzhi'));
        }else{
            $this->error('网络错误请稍后重试!'); 
        }
    }
    public function export(){
        Vendor('phpexcel.PHPExcel');
        // import('phpexcel.PHPExcel',VENDOR_PATH,'.php');
               //Excel表格式,这里简略写了8列
        $excel = new \PHPExcel();       
        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'F', 'G','H','I','J','k','L','M','N','O','P','Q','R','S');
        //表头数组
        $tableheader = array('序号', '资产编号', '设备名称', '主机名', '设备型号', '密级', '用途','密级编号', '所属部门', '放置地点', '责任人', '硬盘序列号', 'ip地址', 'MAC地址','操作系统', '安装时间', '启用时间', '使用情况', '是否需要制作保密标签','创建时间');
        //填充表头信息
        for ($i = 0; $i < count($tableheader); $i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1", "$tableheader[$i]");
        }
        //表格数组
        $data = array(
            array('1', 'B', 'C', 'D', 'E', 'F', 'G'),
            array('2', 'B', 'C', 'D', 'E', 'F', 'G'),
            array('3', 'B', 'C', 'D', 'E', 'F', 'G'),
            array('4', 'B', 'C', 'D', 'E', 'F', 'G'),
            array('5', 'B', 'C', 'D', 'E', 'F', 'G'),
            array('6', 'B', 'C', 'D', 'E', 'F', 'G'),
            array('7', 'B', 'C', 'D', 'E', 'F', 'G'),
        );
       
        $data2 = Db::name('investment')->select()->toArray();
        // print_r($data2);exit();
        foreach ($data2 as $key => $value) {
            
            if(empty($value['starttime']) && empty($value['investment']) && empty($value['eosnum']) && empty($value['money']) && $value['income'] == 0 && empty($value['investmentid']) && empty($value['username']) && empty($value['day']) && empty($value['status'])){
                Db::name('investment')->where('Id',$value['Id'])->delete();
            }
            
        }
        $data2 = Db::name('investment')->select()->toArray();
        foreach ($data2 as $key => $value) {
            $data2[$key]['starttime'] = date('Y-m-d H:i:s',$value['starttime']);
        }
        //填充表格信息
        for ($i = 2; $i <= count($data2) + 1; $i++) {
            $j = 0;
            foreach ($data2[$i - 2] as $key => $value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i", "$value");
                $j++;
            }
        }
        //创建Excel输入对象
        $write = new \PHPExcel_Writer_Excel5($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="信息系统表.xls"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }

    public function cxci(){
        $data = $this->request->param();
        if($data){
            $cbinfo = Db::name("cblist")->where('id',$data['id'])->find();
            $nuser = Db::name("wallet")->where('user_id',$cbinfo['uid'])->find();

            Db::name("cblist")->where(["id" => $data['id']])->setField('status', $data['status']);

            $arrs = array(
                  'num'=>$cbinfo['nums'],      
                  'time'=>time(),
                  'uid'=>$cbinfo['uid'],     
                  'type'=>6
              );

            Db::name('sylistbase')->insert($arrs);
            Db::name('wallet_info')->where(array('wallet_id'=>$nuser['id'],'name'=>'STD'))->setInc('count',$cbinfo['nums']); 

            $this->success("操作成功！", '');    
        } else {
            $this->error('数据传入失败！');
        }
    }
    //提币审核操作
    //提现操作
     public function tibcz($status=1){
          $data=request()->param();
          $infolist=Db::name('tbbase')->where('id',$data['id'])->find();
          $urls = 'https://api.bves.online/api/v1'; 

          $session_admin_id = session('ADMIN_ID');
          $adminuserinfo = Db::name('adminuser')->where('id',$session_admin_id)->find();
          Db::name('admincz')->insert(['adminname'=>$adminuserinfo['user_login'],'cz_info'=>'提币审核','time'=>time(),'ip'=>get_client_ip(0, true)]); 
            
         
          if($data['type'] == 1){//通过     
          
                $method = 'token_transfer';
                $symbol = 'STD';
                $arrh = array(
                    'symbol'=>$symbol,
                    'method'=>$method,
                    'value'=>$infolist['snum'],
                    'ordid'=>$infolist['id'],
                    'to'=>$infolist['addr']
                );	
                $apiurl = new apiurl();
                $res = $apiurl->http_sign($arrh);
                
                $infoss = json_encode($arrh); 
                file_put_contents(ROOT_PATH.'tbhd.txt', $infoss."\r\n", FILE_APPEND); 
                
                $infoss = json_encode($res); 
                file_put_contents(ROOT_PATH.'tbhd.txt', $infoss."\r\n", FILE_APPEND);
                
                // 此处注释是因为：请求钱包提币接口，经常返回null
                // if($res['code'] == 1){  
                    $info=Db::name('tbbase')->where('id',$data['id'])->update(array('status'=>1));     
                    $this->success('广播成功!',url('AdminFinance/slow')); 

                // }else{  
                //     $this->error($res['msg'],url('AdminFinance/slow')); 

                // }
              

          }elseif($data['type'] == 2){
              if($infolist['type'] == 1){
                    $types = 'STD';
              }else{
                    $types = 'ETH';
              }
              $nuser = Db::name('wallet')->where('user_id',$infolist['uid'])->find();
              //钱包信息
              $wallet_info = Db::name('wallet_info')->where(array('wallet_id'=>$nuser['id'],'name'=>$types))->find();
              // $zunum = $wallet_info['count']+$infolist['num'];
              // print_r($zunum);die;  

               
              $info=Db::name('tbbase')->where('id',$data['id'])->update(array('status'=>$data['type']));
              if($info){
                Db::name('wallet_info')->where(array('wallet_id'=>$nuser['id'],'name'=>$types))->setInc('count',$infolist['num']);             
              }
          }
          
           if($info){      
            $this->success('操作成功!',url('AdminFinance/slow')); 
            //     
            }else{  
            $this->error('网络错误请稍后重试!');
          }
     
     }
     

     public function checksign($method,$symbol,$value,$ordid,$to){
        //验证签名
        $arrnew = array(
                'appid'=>'1821563B1E81CDFF6365BDA4DFDA2827',
                'method'=>$method,
                'symbol'=>$symbol,
                'value'=>$value,
                'ordid'=>$ordid,
                'to'=>$to
            );
            ksort($arrnew);	
            reset($arrnew);

        $arg="";
            foreach($arrnew as $key=>$val){
                $arg.=$key."=".$val."&";
            }
        $sign=trim($arg,'&')."&key=C619F7BB24A103AC624E9BDA2A20552A";
        
        $sign = strtoupper(md5($sign));		
        return $sign;
     }
     public function curl_request($url, $postFields)
    {	
        $postFields = http_build_query($postFields);	
        $ch = curl_init();	
        	
        curl_setopt($ch, CURLOPT_POST, 1);		
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $result = curl_exec($ch);			
        curl_close($ch);
        return $result;
    }

function http_sign($param=[]){
    $param['appid']="1821563B1E81CDFF6365BDA4DFDA2827";
    ksort($param);
    reset($param);
    $arg="";
    foreach($param as $key=>$val){
        $arg.=$key."=".$val."&";
    }
    $sign=trim($arg,'&')."&key=C619F7BB24A103AC624E9BDA2A20552A";
    $param['sign']=strtoupper(md5($sign));	

    return json_decode($this->go_curl("https://api.bves.online/api/v1","POST",$param),true);
}



    function go_curl($url, $type, $data = false,$timeout = 20, $cert_info = [],$header=[],&$err_msg = null)
{
    $type = strtoupper($type);
    if ($type == 'GET' && is_array($data)) {
        $data = http_build_query($data);
    }
    $option = array();
    if ( $type == 'POST' ) {
        $option[CURLOPT_POST] = 1;
    }
    if ($data) {
        if ($type == 'POST') {
            $option[CURLOPT_POSTFIELDS] = $data;
        } elseif ($type == 'GET') {
            $url = strpos($url, '?') !== false ? $url.'&'.$data :  $url.'?'.$data;
        }
    }
    $option[CURLOPT_URL]            = $url;
    $option[CURLOPT_FOLLOWLOCATION] = TRUE;
    $option[CURLOPT_MAXREDIRS]      = 4;
    $option[CURLOPT_RETURNTRANSFER] = TRUE;
    $option[CURLOPT_TIMEOUT]        = $timeout;
    if($header){
        $option[CURLOPT_HTTPHEADER]=$header;
    }
    //设置证书信息
    if(!empty($cert_info) && !empty($cert_info['cert_file'])) {
        $option[CURLOPT_SSLCERT]       = $cert_info['cert_file'];
        $option[CURLOPT_SSLCERTPASSWD] = $cert_info['cert_pass'];
        $option[CURLOPT_SSLCERTTYPE]   = $cert_info['cert_type'];
    }
    //设置CA
    if(!empty($cert_info['ca_file'])) {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 1;
        $option[CURLOPT_CAINFO] = $cert_info['ca_file'];
    } else {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 0;
    }
    $ch = curl_init();
    curl_setopt_array($ch, $option);
    $response = curl_exec($ch);
    $curl_no  = curl_errno($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);
    // error_log
    if($curl_no > 0) {
        if($err_msg !== null) {
            $err_msg = '('.$curl_no.')'.$curl_err;
        }
    }
    return $response;
}

}
