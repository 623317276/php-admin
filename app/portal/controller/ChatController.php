<?php

namespace app\portal\controller;

use app\portal\model\ChatrecordModel;
use app\portal\model\CreateroomModel;
use app\portal\model\FrendsModel;
use app\portal\model\GroupChatModel;
use app\portal\model\GroupMembersModel;
use app\portal\model\FrendsModel as FriendsModel;
use cmf\controller\HomeBaseController;
use app\portal\model\PortalCategoryModel;
use think\Db;
use think\Request;

class ChatController extends HomeBaseController
{
    public function _initialize()
    {
        $this->checkLogin();
    }

    public function index()
    {
        $user = session('userinfo');
        $this->assign('user', $user);
        $groupList =CreateroomModel::column('id,roomname,avatar');

        $groups = GroupMembersModel::where('user_id','=',$user['id'])->field('room_id')->select();
        $rooms = [];
        foreach ($groups as $v){
            array_push($rooms,$v['room_id']);
        }

        $where['userid']=$user['id'];
        $where1 = [];
        if($rooms){
            $where1['roomid'] = ['in',$rooms];
        }

        $list = Db::name('chatindex')->where($where)->whereOr($where1)->order('time desc')->select();
        $counts = count($list);
        $this->assign('counts', $counts);
        $this->assign('list', $list);

        $this->assign('groupList', $groupList);
        $where = [];
        $a = $user['id'];
        $where['status'] = ['eq', 1];
        $where['sid'] = ['eq', $a];
        $lists = Db::name('frends')->where($where)->select()->toArray();


        ksort($lists);
        $this->assign('data', $lists);
        $where1 = [];
        $where1['oid'] = ['eq', $user['id']];
        $mtime = strtotime(date("Y-m-d H:i:s", strtotime("-3 day")));
        $where1['time'] = ['egt', $mtime];
        $where1['status'] = ['eq', 2];
        $count = Db::name('frends')->where($where1)->count();
        $this->assign('count', $count);

        $w = [];
        $w['contect'] = ['eq', 1];
        $w['slowid'] = $user['id'];
        $record = Db::name('slow')->where($w)->order('time desc')->select();
        $this->assign('record', $record);
        return $this->fetch();
    }

    public function aa()
    {
        if (request()->isAjax()) {
            $user = session('userinfo');
            $data = request()->param();
            $where = [];
            $regex = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
            if (isset($data['value']) && !empty($data['value'])) {
                if (preg_match($regex, $data['value']) == 1) {
                    $where['user_email'] = ['eq', $data['value']];
                } else {
                    $where['user_nickname'] = ['like', '%' . $data['value'] . '%'];
                }
            }
            $_opt['sid'] = ['eq', $user['id']];

            $list = FriendsModel::getFriends($where,$_opt);
            if($list){
                $result =[
                    'code'=>200,
                    'data'=> $list
                ];
            }else{
                $result =[
                    'code'=>200,
                    'data'=> []
                ];
            }

            return json($result);

        } else {
            $result =[
                'code'=>400,
                'data'=> '请求不正确'
            ];
            return json($result);
        }
    }

    public function getFirstChart($str)
    {
        if (empty($str)) {
            return '';
        }
        $char = ord($str[0]);
        if ($char >= ord('A') && $char <= ord('z')) {
            return strtoupper($str[0]);
        }
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        return null;
    }

    //单聊
    public function danliao()
    {
        $data = request()->param();

        $user = session('userinfo');
        //判断是否是群聊
        if (empty($data['roomid'])) {

            $user1 = Db::name('user')->where('id', $data['id'])->find();  //别人
            $user2 = Db::name('user')->where('id', $user['id'])->find();    //自己

            $this->assign('user1', $user1);
            $this->assign('user2', $user2);
            $this->assign('user', $user);
            $userot = Db::name('user')->where('id', $data['id'])->find();  //别人
            $this->assign('userot', $userot);
            $a = array(
                'roomid' => ''
            );
            $this->assign('roomid', $a);

        } else {

            $room = Db::name('createroom')->where('Id', $data['roomid'])->find();
            $user1 = [
                'user_nickname' => $room['roomname'],
                'id' => ''
            ];
            $user2 = [
                'avatar' => $room['avatar']
            ];
            $userot = [
                'id' => ''
            ];
            $this->assign('user2', $user2);
            $this->assign('user1', $user1);
            $this->assign('roomid', $data);
            $this->assign('userot', $userot);


        }
        $this->assign('userId', $user['id']);
//        if (request()->isAjax()) {
//            $dataa = request()->param();
//            $file = request()->file('pic');
//            if($file){
//            $picInfo = $file->move(ROOT_PATH . 'public' . DS . 'upload'.'/chat');
//            if($picInfo){
//                $getPicName=str_replace("\\","/",$picInfo->getSaveName());
//                $type = 1;
//                $dataa['value'] = '/upload/chat/'.$getPicName;
//            }
//            }else{
//                $type = 0;
//            }
//
//            if ($dataa['roomid'] != '') {
//                $room = Db::name('createroom')->where('Id', $dataa['roomid'])->find();
//                Db::name('chatrecord')->insert([
//                    'sid' => $user['id'],
//                    'yid' => 1,
//                    'type' => $type,
//                    'content' => $dataa['value'],
//                    'emotionl' => $dataa['emotion'],
//                    'roomid' => $dataa['roomid'],
//                    'time' => time()
//                ]);
//                $chats = Db::name('chatindex')->where('sid', $user['id'])->find();
//                if ($chats) {
//                    Db::name('chatindex')->where('sid', $user['id'])->update([
//                        'content' => $dataa['value'],
//                        'emotionl' => $dataa['emotion'],
//                        'roomid' => $dataa['roomid'],
//                        'time' => time()]);
//                } else {
//                    Db::name('chatindex')->insert([
//                        'sid' => $user['id'],
//                        'userid' => $user['id'],
//                        'avator' => $room['avatar'],
//                        'username' => $room['roomname'],
//                        'content' => $dataa['value'],
//                        'emotionl' => $dataa['emotion'],
//                        'roomid' => $dataa['roomid'],
//                        'time' => time()
//                    ]);
//                }
//
//            } else {
//
//                Db::name('chatrecord')->insert([
//                    'sid' => $user['id'],
//                    'oid' => $dataa['id'],
//                    'yid' => 1,
//                    'type' => $type,
//                    'content' => $dataa['value'],
//                    'emotionl' => $dataa['emotion'],
//                    'time' => time()
//                ]);
//                $chat = Db::name('chatindex')->where('sid', $dataa['id'])->find();
//                $userinfo = Db::name('user')->where('id', $dataa['id'])->find();
//                if ($chat) {
//                    Db::name('chatindex')->where('sid', $dataa['id'])->update([
//                        'content' => $dataa['value'],
//                        'roomid' => $dataa['roomid'],
//                        'emotionl' => 1,
//                        'time' => time()]);
//                } else {
//                    Db::name('chatindex')->insert([
//                        'sid' => $dataa['id'],
//                        'avator' => $userinfo['avatar'],
//                        'userid' => $user['id'],
//                        'username' => $userinfo['user_nickname'],
//                        'content' => $dataa['value'],
//                        'emotionl' => 1,
//                        'time' => time()
//                    ]);
//                }
//                $chats = Db::name('chatindex')->where('sid', $user['id'])->find();
//                if ($chats) {
//                    Db::name('chatindex')->where('sid', $user['id'])->update([
//                        'content' => $dataa['value'],
//                        'emotionl' => $dataa['emotion'],
//                        'emotionl' => 1,
//                        'time' => time()]);
//                } else {
//                    Db::name('chatindex')->insert([
//                        'sid' => $user['id'],
//                        'avator' => $user['avatar'],
//                        'userid' => $dataa['id'],
//                        'username' => $user['user_nickname'],
//                        'content' => $dataa['value'],
//                        'emotionl' => 1,
//                        'time' => time()
//                    ]);
//                }
//            }
//        }

        return $this->fetch();
    }

    //发消息
    public function ajaxSend(Request $request){
        $user = session('userinfo');
            $dataa = $request->param();
            $file = $request->file('pic');
            if($file){
                $picInfo = $file->move(ROOT_PATH . 'public' . DS . 'upload'.'/chat');
                if($picInfo){
                    $getPicName=str_replace("\\","/",$picInfo->getSaveName());
                    $type = 1;
                    $dataa['value'] = '/upload/chat/'.$getPicName;
                }
            }else{
                $type = 0;
            }
            if ($dataa['roomid'] != '') {
                $room = Db::name('createroom')->where('Id', $dataa['roomid'])->find();
                $msg = [
                    'sid' =>$user['id'],
                    'type' => $type,
                    'content' => $dataa['value'],
                    'roomid' => $dataa['roomid'],
                    'create_time' => time(),
                ];
                $groupInfo = GroupChatModel::insert($msg);
                $where =[
//                    'sid'=>$user['id'],
                    'roomid'=>$dataa['roomid']
                ];
                $chats = Db::name('chatindex')->where($where)->find();
                if ($chats) {
                    Db::name('chatindex')->where($where)->update([
                        'content' => $dataa['value'],
                        'type' => $type,
                        'time' => time()]);
                } else {
                    Db::name('chatindex')->insert([
                        'sid' => $user['id'],
                        'userid' => $user['id'],
                        'avator' => $room['avatar'],
                        'username' => $room['roomname'],
                        'content' => $dataa['value'],
                        'roomid' => $dataa['roomid'],
                        'type' => $type,
                        'time' => time()
                    ]);
                }

            } else {
                Db::name('chatrecord')->insert([
                    'sid' => $user['id'],
                    'oid' => $dataa['id'],
                    'yid' => 1,
                    'type' => $type,
                    'content' => $dataa['value'],
                    'time' => time()
                ]);
                $where =[
                    'sid'=>$dataa['id'],
                    'userid'=>$user['id']
                ];
                $chat = Db::name('chatindex')->where($where)->find();
                $userinfo = Db::name('user')->where('id', $dataa['id'])->find();
                if ($chat) {
                    Db::name('chatindex')->where($where)->update([
                        'content' => $dataa['value'],
                        'type' => $type,
                        'time' => time()]);
                } else {
                    Db::name('chatindex')->insert([
                        'sid' => $dataa['id'],
                        'avator' => $userinfo['avatar'],
                        'userid' => $user['id'],
                        'username' => $userinfo['user_nickname'],
                        'content' => $dataa['value'],
                        'type' => $type,
                        'time' => time()
                    ]);
                }
                $where1 =[
                    'sid'=>$user['id'],
                    'userid'=>$dataa['id']
                ];
                $chats = Db::name('chatindex')->where($where1)->find();
                if ($chats) {
                    Db::name('chatindex')->where($where1)->update([
                        'content' => $dataa['value'],
                        'emotionl' => $dataa['emotion'],
                        'type' => $type,
                        'time' => time()]);
                } else {
                    Db::name('chatindex')->insert([
                        'sid' => $user['id'],
                        'avator' => $user['avatar'],
                        'userid' => $dataa['id'],
                        'username' => $user['user_nickname'],
                        'content' => $dataa['value'],
                        'type' => $type,
                        'time' => time()
                    ]);
                }
            }
            $result =[
                'code'=>200,
                'msg'=>'发送成功'
            ];
            return json($result);

    }

    public function clock()
    {

        $data = request()->param();
        $userot = Db::name('user')->where('id', $data['id'])->find();  //别人
        $this->assign('userot', $userot);
        if (request()->isAjax()) {
            $user = session('userinfo');
            if ($data['roomid'] == '') {
                $where = [];
                $where['sid'] = ['eq', $user['id']];
                $where['oid'] = ['eq', $data['id']];
                $where1['oid'] = ['eq', $user['id']];
                $where1['sid'] = ['eq', $data['id']];

//                $list = ChatrecordModel::getRecords($where,$where1);
                $sql = "SELECT *,FROM_UNIXTIME(a.time,'%Y/%m/%d %H:%i:%s') as sendTime FROM `cmf_chatrecord` as a JOIN cmf_user as b ON a.sid= b.Id WHERE  a.sid = {$user['id']}  AND a.oid = {$data['id']} OR a.sid = {$data['id']}  AND a.oid = {$user['id']} ORDER BY a.time desc LIMIT 100";
                $list =Db::query($sql);
//                dump($sql);
//die;
//                $list = DB::getRecords($where,$where1);



//                $lists = $list->toArray();

                $lists = array_reverse($list);
                if ($lists) {
                    return json($lists);
                } else {
                    echo 1;
                }
            } else {
                $where = [];
                $user5 = Db::name('createroom')->where('Id', $data['roomid'])->find();
                $str = str_replace(',', '', $user5['peopleid']);
                $str = (123456);
                $s = str_split($str);
                /*  $list = array();
                  $user['id'] = 5;*/
                $data['roomid'] = 15;
                $where['roomid'] = ['eq', $data['roomid']];
                $lists = Db::name('chatrecord')->where($where)->order('time ASC')->select()->toArray();
                /* foreach($s as $key => $value){
                   $where['sid'] = ['eq', $value];
                     $where['roomid'] = ['eq', $data['roomid']];
                     $where['oid'] = ['eq', $user['id']];
                     $list[] = Db::name('chatrecord')->where($where)->order('time ASC')->select()->toArray();
                 }
                 //去除空数组
                 foreach ($list as $key=>$value){
                     if(!$value){
                         unset($list[$key]);
                     }
                 }
                 //三维数组转二位数组
                 $lists = array();
                 foreach ($list as $key=>$value){
                     foreach($value as $v){
                         $lists[]=$v;
                     }
                 }*/
                $ava = array();
                foreach ($lists as $k => $v) {
                    $user6 = Db::name('user')->where('id', $v['sid'])->find();
                    $ava[] = $user6['avatar'];
                    $user4 = Db::name('user')->where('id', $user['id'])->find();    //自己
                    $time = date('Y-m-d H:i:s', $v['time']);
                    $lists[$k]['time'] = $time;
                    $lists[$k]['avatar2'] = $user4['avatar'];
                    $lists[$k]['avatar1'] = $ava[$k];
                    $lists[$k]['sessionid'] = $user['id'];
                }
                if ($lists) {
                    echo json_encode($lists);
                    exit;
                } else {
                    echo 1;
                }
            }


        }

    }

    public function weiyouquan()
    {
        return $this->fetch();
    }

    //好友页面
    public function addfrend()
    {
        $user = session('userinfo');
        $user = Db::name('user')->where('id', $user['id'])->find();
        $this->assign('user', $user);
        return $this->fetch();
    }

    public function gerenxinxi()
    {
        $data = request()->param();
        $user = session('userinfo');
        $this->assign('user', $user);
        return $this->fetch();
    }

    //钱包
    public function wallet(){
        $user = session('userinfo');
        return view('wallet',['user',$user]);
    }

    public function haoyouzhuye()
    {
        $data = request()->param();
        $user = Db::name('user')->where('id', $data['id'])->find();
        $this->assign('user', $user);
        return $this->fetch();
    }

    //获取添加的好友信息
    public function afrend()
    {
        if (request()->isAjax()) {
            $data = request()->param();
            $where = [];
            if (isset($data['value']) && !empty($data['value'])) {
                if (preg_match("/^1[34578]\d{9}$/", $data['value'])) {
                    $where['mobile'] = ['eq', $data['value']];
                } else {
                    $where['user_email'] = ['eq', $data['value']];
                }
            }
            if($where){
                $li = Db::name('user')->where($where)->select()->toArray();
                if ($li) {
                    echo json_encode($li);
                    exit;
                } else {
                    echo 1;
                }
            }else{
                echo 1;
            }

        }
    }

    //发送添加信息
    public function trueadd()
    {
        $userinfo = session('userinfo');
        if (request()->isAjax()) {
            $data = request()->param();
            $where1 = array();
            $where2 = array();
            $where1['sid'] = ['eq',$userinfo['id']];
            $where1['oid'] = ['eq',$data['id']];
            $usetrue = Db::name('frends')->where($where1)->find();

            if($usetrue){
                if($usetrue['status'] ==2){
                    $arrs = array('code' => 0, 'resule' => '验证消息只能发送一次');
                    return json($arrs);
                }elseif ($usetrue['status'] ==1){
                    $arrs = array('code' => 0, 'resule' => '该用户已经是你的好友！');
                    return json($arrs);
                }else{
                    $id = $usetrue['Id'];
                }

            }

            $users = session('userinfo');
            if($data['id'] == $users['id']){
                $arrs = array('code' => 0, 'resule' => '自己不能添加自己为好友');
                return json($arrs);
            }
            $user = Db::name('user')->where('id', $data['id'])->find();
            $data = [
                'sid' => $userinfo['id'],
                'oid' => $user['id'],
                'status' => 2,
                'avator' => $user['avatar'],
                'user_nickname' => $user['user_nickname'],
                'mobile' => $user['mobile'],
                'email' => $user['user_email'],
                'time' => time()
            ];
            if(isset($id) && $id){
                $data['Id'] = $id;
                $res = (new FrendsModel())->update($data);
            }else{
                $res = (new FrendsModel())->save($data);
            }


            if ($res) {
                $arrs = array('code' => 1, 'resule' => '确认消息发送成功');
                return json($arrs);
            } else {
                $arrs = array('code' => 0, 'resule' => '确认消息发送失败');
                return json($arrs);
            }
        }
    }

    //新的朋友
    public function newfrend()
    {
        $user = session('userinfo');
        $where = [];
        $where1 = [];
        $where['oid'] = ['eq', $user['id']];
        $where1['oid'] = ['eq', $user['id']];
        $mtime = strtotime(date("Y-m-d H:i:s", strtotime("-3 day")));
        $where['time'] = ['egt', $mtime];
        $where1['time'] = ['lt', $mtime];
        $list = Db::name('frends')->where($where)->order('time desc')->select();
        $lists = Db::name('frends')->where($where1)->order('time desc')->select();
        $this->assign('list', $list);
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    public function status()
    {
        $data = request()->param();
        /*    var_dump($data['status']);
            exit;*/
        $userinfo = session('userinfo');
        $res = Db::name('frends')->where('Id', $data['id'])->update(['status' => $data['status']]);
        if ($res) {
            $find = Db::name('frends')->where('Id', $data['id'])->find();
            $user = Db::name('user')->where('id', $find['sid'])->find();
            $_opt=[
                'sid' =>$userinfo['id'],
                'oid' =>$user['id']
            ];
            $info = (new FrendsModel())->where($_opt)->find();
            $data = [
                'sid' => $userinfo['id'],
                'oid' => $user['id'],
                'status' => $data['status'],
                'time' => time(),
                'avator' => $user['avatar'],
                'user_nickname' => $user['user_nickname'],
                'mobile' => $user['mobile'],
                'email' => $user['user_email']
            ];
            if($info){
                $res2 = (new FrendsModel())->where('Id','=',$info['Id'])->update($data);
            }else{
                $res2 = (new FrendsModel())->save($data);
            }


            if ($res2) {
                $this->redirect('portal/chat/newfrend');
            }

        }
    }

    public function transfer_record()
    {
        $data = request()->param();
        $user = Db::name('user')->where('id', $data['id'])->find();  //别人
        $this->assign('user', $user);
        return $this->fetch();
    }

    //社交转账提交
    public function extend()
    {
        $user = session('userinfo');
        if (request()->isAjax()) {
            $data = request()->param();

            $balance = Db::name('user')->where('id', $user['id'])->find();
            $balances = Db::name('user')->where('id', $data['otherid'])->find();
            $nexpb = $balance['SUBbalance'] - $data['hbAmount'];
            $password = md5(trim($data['password']));
            if($password!=$balance['paynum']){
                $arrs = array('code' => 0, 'resule' => '交易密码有误！');
                return json($arrs);
            }
            if ($nexpb < 0) {
                $arrs = array('code' => 0, 'resule' => 'SUD数量不够转账！');
                return json($arrs);
            }
            $date1 = '转账-来自' . $balance['user_nickname'];
            $b = "+";
            $d = $b . $data['hbAmount'];
            $totalpb1 = $balances['SUBbalance'] + $data['hbAmount'];

            // 启动事务
            Db::startTrans();
            try {
                Db::name('user')->where('id', $data['otherid'])->update(['SUBbalance' => $totalpb1]);

                $arraa = array(
                    'expenses' => $d,
                    'money' => $totalpb1,
                    'type' => '4',
                    'time' => time(),
                    'explain' => $date1,
                    'slowid' => $data['otherid'],
                    'category' => '2',
                    'contect' => '1',
                    'message' => $data['content'],
                    'avatar' => $balance['avatar']
                );

                Db::name('slow')->insert($arraa);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $arrs = array('code' => 0, 'resule' => '网络错误,请刷新后重新操作！');
                return json($arrs);
            }
            Db::startTrans();
            try {
                $date2 = '转账-转给' . $balances['user_nickname'];
                $a = "-";
                $c = $a . $data['hbAmount'];
                $totalpb = $balance['SUBbalance'] - $data['hbAmount'];
                Db::name('user')->where('id', $user['id'])->update(['SUBbalance' => $totalpb]);
                $arrbb = array(
                    'expenses' => $c,
                    'money' => $totalpb,
                    'type' => '4',
                    'time' => time(),
                    'explain' => $date2,
                    'slowid' => $user['id'],
                    'username' => $user['user_login'],
                    'category' => '2',
                    'contect' => '1',
                    'message' => $data['content'],
                    'avatar' => $balances['avatar']
                );
                Db::name('slow')->insert($arrbb);

                Db::commit();
                $arrs = array('code' => 1, 'resule' => '转账成功！');
                return json($arrs);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $arrs = array('code' => 0, 'resule' => '网络错误,请刷新后重新操作！');
                return json($arrs);
            }

        }


    }

    //群聊抛出页面
    public function groupchat()
    {
        $user = session('userinfo');
        $where = [];
        $a = $user['id'];
        $a = 4;
        $where['status'] = ['eq', 1];
        $where['sid'] = ['eq', $a];
        $lists = Db::name('frends')->where($where)->select()->toArray();
        ksort($lists);
        $this->assign('data', $lists);
        return $this->fetch();
    }

    public function createroom()
    {
        $user = session('userinfo');
        $user2 = Db::name('user')->where('id', $user['id'])->find();
        $find = Db::name('createroom')->order('time desc')->limit(0, 1)->find();
        $this->assign('room', $find);
        $this->assign('user2', $user2);
        if (request()->isAjax()) {
            $dataa = request()->param();
            $ids = strlen(implode("", $dataa['id'])) + 1;
            $id = implode(",", $dataa['id']);
            $name = '';
            for ($i = 0; $i < count($dataa['id']); $i++) {
                $n = Db::name('frends')->where('id', $dataa['id'][$i])->find();
                $name .= $n['user_nickname'];
            }
            $avatar = '/themes/simpleboot3/public/img/ucenter/user.jpg';
            $res = Db::name('createroom')->insertGetId([
                'userid' => $user['id'],
                'roomname' => $name,
                'avatar' => $avatar,
                'peopleid' => $id,
                'num' => $ids,
                'time' => time()
            ]);
            if ($res) {
                $arrs = array('code' => 1, 'resule' => '群聊创建成功');
                echo json_encode($arrs);
                exit;
            } else {
                $arrs = array('code' => 0, 'resule' => '群聊创建失败');
                echo json_encode($arrs);
                exit;
            }
        }
        return $this->fetch();
    }

    public function grouplist()
    {
        $user = session('userinfo');
        $_opt =[
            'user_id'=>$user['id']
        ];
        $list = CreateroomModel::getRooms($_opt)->toArray();
        $this->assign('data', $list);
        return $this->fetch();
    }

    //发红包

    //创建群界面
    public function addgroup(Request $request){
        $user = session('userinfo');
        $where =[
            'sid' =>$user['id']
        ];
        $friends = FrendsModel::addGroup($where)->toArray();
        return view('addgroup',['userId'=>$user['id'],'friends'=>$friends]);
    }

    public function ajaxAddGroup(Request $request){
        $param = $request->param();
        $file = $request->file('pic');
        $userIds = explode(',',$param['userIds']);
        $user = session('userinfo');
        if($file){
            $picInfo = $file->move(ROOT_PATH . 'public' . DS . 'upload'.'/group');
            if($picInfo){
                $getPicName=str_replace("\\","/",$picInfo->getSaveName());
                $param['avatar'] = '/upload/group/'.$getPicName;
            }
        }else{
           $result = [
               'code'=>400,
               'msg'=>'群头像不能为空'
           ];
           return json($result);
        }
        if(empty($param['roomname'])) {
            $result = [
                'code'=>400,
                'msg'=>'群名不能为空'
            ];
            return json($result);
        }
        if(CreateroomModel::where('roomname','=',$param['roomname'])->find()){
            $result = [
                'code'=>400,
                'msg'=>'该群名已经被人创建，请换个群名'
            ];
            return json($result);
        }
        $param['peopleid'] = $user['id'].','.$param['userIds'];
        $param['num'] = count($userIds)+1;
        $data =[];
        unset($param['userIds']);
        Db::startTrans();
        try{
            $roomId = (new CreateroomModel)->insertGetId($param);
            $inf = [
                'room_id'=>$roomId,
                'user_id'=>$user['id'],
                'create_time'=>time()
            ];
            array_push($data,$inf);
            foreach ($userIds as $key =>$v){
                $val['room_id'] = $roomId;
                $val['user_id'] = $v;
                $val['create_time'] = time();
                array_push($data,$val);
            }

            (new GroupMembersModel())->saveAll($data);
            Db::commit();
            $result = [
                'code'=>200,
                'roomId'=>$roomId,
                'msg'=>'群创建成功'
            ];
            return json($result);
        }catch (\Exception $e){
            Db::rollback();
            $result = [
                'code'=>400,
                'msg'=>$e->getMessage()
            ];
            return json($result);
        }

    }

    //群聊页面
    public function groupQuantity(Request $request){
        $param = $request->param();
        $user = session('userinfo');
        $info = CreateroomModel::where('id','=',$param['roomid'])->find();
        return view('group_quantity',['info'=>$info,'userId'=>$user['id']]);
    }

    //轮训群聊消息
    public function ajaxQuantity(Request $request)
    {
        $user = session('userinfo');
        $param = $request->param();
        if(empty($param['roomid'])){
            $result = [
                'code'=> 400,
                'data'=>'群未找到'
            ];
            return json_encode($result);
        }
        $wher['roomid'] = $param['roomid'];
        $list = GroupChatModel::getRecords($wher);
        $lists = $list->toArray();
        $lists = array_reverse($lists);
        if($list){
            $result = [
                'code'=> 200,
                'data'=>$lists
            ];
        }else{
            $result = [
                'code'=> 200,
                'data'=>[]
            ];
        }
        return json_encode($result);
    }

    //添加群成员
    public function ajaxAddFriends(Request $request){
        $param = $request->param();

        if(empty($param['userIds'])){
            $result=[
                'code'=>400,
                'msg'=>'添加用户不能为空'
            ];
            return json($result);
        }
        $data =[];
        foreach ($param['userIds'] as $key =>$vo){
            $va['room_id'] = $param['roomId'];
            $va['user_id'] = $vo;
            $va['create_time'] = time();
            array_push($data,$va);
        }

        $num = count($param['userIds']);
        Db::startTrans();
        try{
            (new GroupMembersModel())->saveAll($data);
            Db::name('createroom')->where('Id','=',$param['roomId'])->setInc('num',$num);
            Db::commit();
            $result =[
                'code'=>200,
                'msg'=>'添加成功'
            ];
            return json($result);
        }catch (\Exception $e){
            Db::rollback();
            $result =[
                'code'=>400,
                'msg'=>$e->getMessage()
            ];
            return json($result);
        }
    }

    //群显示好友列表
    public function friends(Request $request){
        $param = $request->param();
        return view('friends');
    }

    public function chat_in(Request $request){
        $param = $request->param();
        $where =[
            'room_id'=>$param['room_id']
        ];
        $memberList = GroupMembersModel::GetRoomMen($where)->toArray();
        $count = count($memberList);
        return view('chat_in',['memberList'=>$memberList,'count'=>$count,'param'=>$param]);
    }

    public function add_user(Request $request){
        $param = $request->param();
        $user = session('userinfo');
        $where =[
            'sid'=>$user['id']
        ];
        $friends = FrendsModel::addGroup($where)->toArray();
        $opt=[
            'room_id'=>$param['room_id']
        ];
        $memberList = GroupMembersModel::where($opt)->column('user_id,room_id');
        $list = [];
        foreach ($friends as $key=> $v){
            if(!isset($memberList[$v['user']['id']])){
                array_push($list,$v);
            }
        }

        return view('add_user',['list'=>$list,'roomId'=>$param['room_id']]);
    }


}