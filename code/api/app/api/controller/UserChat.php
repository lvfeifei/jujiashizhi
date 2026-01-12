<?php

namespace app\api\controller;



use app\common\model\Config;

class UserChat extends Basic
{
    /**
     * 初始化方法，所有子类继承
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 马上办列表
     */
    public function chart_list(){
        // $user_id = input('user_id');
        // $page = input('page');
        // $limit = input('limit');
        // if(!$user_id){
        //     json_fail('获取用户id错误');
        // }
        // $user_id = decode($user_id);
        $user_id = $this->userId;
        //数据库
        $user_chat = new \app\common\model\UserChat();
        $user = new \app\common\model\User();
        $system_manager = new \app\common\model\SystemManager();

        $list = $user_chat->where(array('user_id' => $user_id, 'status' => array('neq', 0)))->order('id asc')->select();
        //更改消息为已读
        $user_chat->where(array('user_id' => $user_id, 'user_read' => 2))->update(array('user_read' => 1));
        if($list){
            foreach ($list as $k => $v) {
                $v['content'] = $v['content'] ? json_decode($v['content']) : '';

                if($v['msg_type'] == 0){
                    if(date('Ymd', $v['create_time']) == date('Ymd')) {

                        $v['content'] = date('H:i',$v['create_time']);

                    }else if(date('Ymd', $v['create_time']) == date("Ymd",strtotime("-1 day"))){

                        $v['content'] = '昨天'.date('H:i',$v['create_time']);
                    }
                }



                if($v['type'] == 1){
                    $user_info = $user->where(array('id'=>$v['user_id']))->field('avatar_url,nickname')->find();
                    if($user_info){
                        $v['avatar_url'] =$user_info['avatar_url'];
                        // $v['real_name'] =$user_info['real_name'];
                        $v['nickname'] = $user_info['nickname'] ? base64_decode($user_info['nickname']) : '';
                    }else{
                        $v['avatar_url'] ='';
                        $v['nickname'] ='';
                    }
                }else{
                    $configModel = new Config();
                    $key ='expertAvatar';
                    $result = $configModel->where('key',$key)->value('value');
                    $v['avatar_url'] =$result;
                    //$v['nickname'] = '郑怡康专家';
                    $v['nickname'] = config('expert_name');
                    // $user_info = $system_manager->where(array('id'=>$v['pid']))->field('truename,header')->find();
                    // if($user_info){
                    //     $v['avatar_url'] =$user_info['header'];
                    //     $v['nickname'] = $user_info['truename'];
                    // }else{
                    //     $v['avatar_url'] ='';
                    //     $v['nickname'] ='';
                    // }
                }
                $list[$k] = $v;
            }
        }
        json_success($list);
    }
    //
    public function count_unread()
    {
        $user_id = $this->userId;
        $user_chat = new \app\common\model\UserChat();
        $count =  $user_chat->where('user_id', $user_id)->where('type', 2)->where('user_read', 2)->count('id');
        return $count;
    }
    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 定时器返回消息
     */
    public function monitor_news()
    {
        // $user_id = input('user_id');
        // if (!$user_id) {
        //     json_fail('获取用户id失败');
        // }
        // $user_id = decode($user_id);
        $user_id = $this->userId;
        $user_chat = new \app\common\model\UserChat();
        $user = new \app\common\model\User();
        $system_manager = new \app\common\model\SystemManager();

        $list = $user_chat->where(array('user_id' => $user_id, 'user_read' => 2))->order('id')->select();

        //更改消息为已读
        $user_chat->where(array('user_id' => $user_id, 'user_read' => 2))->update(array('user_read' => 1));
        if($list){
            foreach ($list as $k => $v) {
                $v['content'] = $v['content'] ? json_decode($v['content']) : '';

                if($v['type'] == 1){
                    $user_info = $user->where(array('id'=>$v['user_id']))->field('avatar_url,nickname,real_name')->find();
                    if($user_info){
                        $v['avatar_url'] =$user_info['avatar_url'];
                        $v['real_name'] =$user_info['real_name'];
                        $v['nickname'] = $user_info['nickname'] ? base64_decode($user_info['nickname']) : '';
                    }else{
                        $v['avatar_url'] ='';
                        $v['nickname'] ='';
                    }
                }else{
                    $configModel = new Config();
                    $key ='expertAvatar';
                    $result = $configModel->where('key',$key)->value('value');
                    $v['avatar_url'] =$result;
                    //$v['nickname'] = '郑怡康专家';
                    $v['nickname'] = config('expert_name');
                    // $user_info = $system_manager->where(array('id'=>$v['pid']))->field('truename,header')->find();
                    // if($user_info){
                    //     $v['avatar_url'] =$user_info['header'];
                    //     $v['nickname'] = $user_info['truename'];
                    // }else{
                    //     $v['avatar_url'] ='';
                    //     $v['nickname'] ='';
                    // }
                }
                $list[$k] = $v;
            }
        }
        json_success($list);
    }

    /**
     *回复内容
     */
    public function reply_user(){
        // $user_id = input('user_id');
        $msg_type = input('msg_type',1);
        $content = input('content');

        $voice_time = input('voice_time/d',0);
        // if(!$user_id){
        //     json_fail('获取用户id错误');
        // }
        // $user_id = decode($user_id);

        if(strlen($content) == 0 ){
                json_fail('内容不能为空');
        }
        $user_id = $this->userId;
        $user_chart = new \app\common\model\UserChat();
        //查询上一条时间
        $create_time =$user_chart->where(array('user_id'=>$user_id))->order('create_time desc')->value('create_time');
        $add_time = '';

        //是否大于五分钟
        if((time()-$create_time) >= 300){
            $add_time = date('H:i');
        }
        if ((time()-$create_time) >= 24*60*60){
            $add_time = date('Y-m-d H:i');
        }

        if($add_time){
            $user_chart->insertGetId(array('user_id'=>$user_id,'type'=>1,'pid'=>$user_id,'msg_type'=>0,'content'=>json_encode($add_time),'create_time'=>time()));
        }

        $add_data = array(
            'user_id'=>$user_id,
            'type'=>1,
            'pid'=>$user_id,
            'msg_type'=>$msg_type,
            'content'=>json_encode($content),
            'voice_time' => $voice_time,
            'create_time'=>time()
        );

        $chart_id = $user_chart->insertGetId($add_data);

        if($chart_id  === false){
            json_fail('回复失败');
        }

        $user = new \app\common\model\User();
        $system_manager = new \app\common\model\SystemManager();

        // 查询未读消息内容
        $chat_list = $user_chart->where(array('user_id'=>$user_id))->where('user_read', '=', 2)->select();
        if($chat_list){
            $user_chart->where(array('user_id' => $user_id))->where('user_read', '=', 2)->update(array('user_read' => 1));
            foreach ($chat_list as $k => $v) {
                $v['content'] = $v['content'] ? json_decode($v['content']) : '';
                if($v['msg_type'] == 0){
                    if(date('Ymd', $v['create_time']) == date('Ymd')) {

                        $v['content'] = date('H:i',$v['create_time']);

                    }else if(date('Ymd', $v['create_time']) == date("Ymd",strtotime("-1 day"))){

                        $v['content'] = '昨天'.date('H:i',$v['create_time']);
                    }
                }

                if($v['type'] == 1){
                    // $user_info = $user->where(array('id'=>$v['user_id']))->field('avatar_url,nickname,real_name')->find();
                    $user_info = $user->where(array('id'=>$v['user_id']))->field('avatar_url,nickname')->find();

                    if($user_info){
                        $v['avatar_url'] =$user_info['avatar_url'];
                        // $v['real_name'] =$user_info['real_name'];
                        $v['nickname'] = $user_info['nickname'] ? base64_decode($user_info['nickname']) : '';
                    }else{
                        $v['avatar_url'] ='';
                        $v['nickname'] ='';
                    }
                }else{
                    $configModel = new Config();
                    $key ='expertAvatar';
                    $result = $configModel->where('key',$key)->value('value');
                    $v['avatar_url'] =$result;
                    //$v['nickname'] = '郑怡康专家';
                    $v['nickname'] = config('expert_name');
                    // $user_info = $system_manager->where(array('id'=>$v['pid']))->field('truename,header')->find();
                    // if($user_info){
                    //     $v['avatar_url'] =$user_info['header'];
                    //     $v['nickname'] = $user_info['truename'];
                    // }else{
                    //     $v['avatar_url'] ='';
                    //     $v['nickname'] ='';
                    // }
                }
                $chat_list[$k] = $v;
            }
        }
        json_success($chat_list);
    }

    public function expert_default()
    {
        $configModel = new Config();
        $key ='expertAvatar';
        $result = $configModel->where('key',$key)->value('value');
        $res['avatar_url'] =$result;
        //$res['nickname'] = '郑怡康专家';
        $res['nickname'] = config('expert_name');
        $res['content'] = '后台专家团队均具备5年以上失智照护临床经验，您在家中照护老人时，遇到任何问题，都可以咨询我们。';
        $key ='sendTime';
        if (!in_array($key,$configModel->keys)){
            $sendtime = '9:00';
        }else{
            $result = $configModel->where('key',$key)->value('value');
            $sendtime = $result;
        }
        $res['sendtime'] = $sendtime;

        json_success($res);
    }


}
