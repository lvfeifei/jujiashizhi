<?php

namespace app\admin\controller;



use app\common\model\SystemManager;
use app\common\model\Config;
use app\common\services\basicconfig\BasicConfigServices;
use app\common\services\user\UserServices;
use app\common\services\wxmessage\WxMessageServices;
use think\Db;
/**
 * @package app\admin\controller
 * 马上办
 */
class UserChat extends Basic
{

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 投诉列表
     */
    public function index()
    {
        $page = input('page', 1);
        $limit = input('limit', 10);
        $key = input('key');
        $map = array(
            'a.status' => array('neq', 0),
            'a.type'=>1,
            'a.msg_type' => array('in',[1,2])
        );
        if ($key) {
            $map['b.nickname_old'] = array('like', '%' . $key . '%');
        }

        $user_chat = new \app\common\model\UserChat();

        $list = $user_chat->alias('a')
            ->field('a.*,b.nickname_old,b.nickname,b.avatar_url')
            ->join('user b', 'b.id=a.user_id')
            ->where($map)
            ->order('a.create_time desc')
            ->group('a.user_id')
            ->page($page, $limit)
            ->select();
        if(!$list) {
            $info_list = array(
                'list' => $list,
                'count' => 0
            );
        } else {
            foreach ($list as $k => $v) {
                $v['create_time'] = $user_chat->where(array('user_id'=>$v['user_id']))->order('id desc')->value('create_time');
                $v['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s', $v['create_time']) : '--';
                $v['nickname'] = $v['nickname'] ? base64_decode($v['nickname']) : '--';
                $v['content'] = $v['content'] ? json_decode($v['content']) : '--';
                //未读条数
                $v['no_read_count'] = $user_chat->where(array('pid'=>$v['user_id'],'type'=>1,'status'=>2))->count(1);
                $list[$k] = $v;
            }
            $time_arr = array_column($list, 'create_time');
            array_multisort($time_arr, SORT_DESC, $list);
            $count = $user_chat->alias('a')
                ->join('user b', 'b.id=a.user_id')
                ->where($map)
                ->group('a.user_id')
                ->count(1);
            $info_list = array(
                'list' => $list,
                'count' => $count
            );
        }
        json_success($info_list);
    }

    /**
     * 投诉详情
     */
    public function chart_list(){
        $user_id = input('user_id');
        if(!$user_id){
            json_fail('获取用户id错误');
        }

        //数据库
        $user_chat = new \app\common\model\UserChat();
        $user = new \app\common\model\User();
        $system_manager = new SystemManager();
        $configModel = new Config();
        $list = $user_chat->where(array('user_id' => $user_id, 'status' => array('neq', 0)))->order('id asc')->select();
        $user_detail = $user->where(array('id'=>$user_id))->find();
        if($user_detail){
            $user_detail['nickname'] = $user_detail['nickname'] ?base64_decode($user_detail['nickname']):'--';
            $basicConfigServices = new BasicConfigServices();
            //性别
            $patient = [];
            if(array_key_exists($user_detail['patient_gender'],$basicConfigServices->patient_gender)){
                $patient['patient_gender_name'] = $basicConfigServices->patient_gender[$user_detail['patient_gender']];
            }else{
                $patient['patient_gender_name'] = '';
            }
            //教育
            if(array_key_exists($user_detail['patient_education'],$basicConfigServices->patient_education)){
                $patient['patient_education_name'] = $basicConfigServices->patient_education[$user_detail['patient_education']];
            }else{
                $patient['patient_education_name'] = '';
            }
            //患者疾病类型
            if(array_key_exists($user_detail['patient_disease_type'],$basicConfigServices->patient_disease_type)){
                $patient['patient_disease_type_name'] = $basicConfigServices->patient_disease_type[$user_detail['patient_disease_type']];
            }else{
                $patient['patient_disease_type_name'] = '';
            }
            //P患者病情严重程度
            if(array_key_exists($user_detail['patient_illness'],$basicConfigServices->patient_illness)){
                $patient['patient_illness_name'] = $basicConfigServices->patient_illness[$user_detail['patient_illness']];
            }else{
                $patient['patient_illness_name'] = '';
            }
            //患者确诊前的兴趣爱好
            if($user_detail['patient_hobby']){
                $user_detail['patient_hobby'] = json_decode($user_detail['patient_hobby'],true);
                $hobby_str = '';
                if($user_detail['patient_hobby']){
                    foreach ($user_detail['patient_hobby'] as $item){
                        if(array_key_exists($item,$basicConfigServices->patient_hobby)){

                            if($item == 'Q8'){
                                $hobby_str .= $basicConfigServices->patient_hobby[$item].'('.$user_detail['patient_hobby_content'].')';
                            }else{
                                $hobby_str .= $basicConfigServices->patient_hobby[$item];
                            }

                        }else{
                            $hobby_str .= '';
                        }
                    }
                }
                $patient['patient_hobby_name'] = $hobby_str;
            }else{
                $patient['patient_hobby_name'] = '';
            }
            //R患者行走能力
            if(array_key_exists($user_detail['patient_walk'],$basicConfigServices->patient_walk)){
                $orderInfo['patient_walk_name'] = $basicConfigServices->patient_walk[$user_detail['patient_walk']];
            }else{
                $orderInfo['patient_walk_name'] = '';
            }
            $user_detail['patient_text'] = $patient['patient_gender_name'].'、'.$user_detail['patient_age'].'岁、'. $patient['patient_education_name'].'教育程度、'
            .'患有'.$patient['patient_disease_type_name'].'病、病情程度属于'.$patient['patient_illness_name'].'、'.$orderInfo['patient_walk_name']
            .'、确诊前的爱好：'.$patient['patient_hobby_name'];
        }
        //更改消息为已读
        $user_chat->where(array('user_id' => $user_id, 'type'=>1,'status' => 2))->update(array('status' => 1));
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
        $info_list = array(
            'list'=>$list,
            'user_detail' => $user_detail ? $user_detail  :''
        );

        json_success($info_list);
    }



    /**
     * 消息列表(处理结果页)
     */
    public function can_use_chart_list(){
        $user_id = input('user_id');
        if(!$user_id){
            json_fail('获取用户id错误');
        }

        //数据库
        $user_chat = new \app\common\model\UserChat();
        $user = new \app\common\model\User();
        $system_manager = new SystemManager();

        $list = $user_chat->where(array('user_id' => $user_id, 'msg_type'=>array('in',[1,2]),'status' => array('neq', 0)))->order('id asc')->select();
        $user_detail = $user->where(array('id'=>$user_id))->find();
        if($user_detail){
            $user_detail['nickname'] = $user_detail['nickname'] ?base64_decode($user_detail['nickname']):'--';
        }
        //更改消息为已读
        $user_chat->where(array('user_id' => $user_id, 'type'=>1,'status' => 2))->update(array('status' => 1));
        if($list){
            foreach ($list as $k => $v) {
                $v['content'] = $v['content'] ? json_decode($v['content']) : '';
                if($v['type'] == 1){
                    $user_info = $user->where(array('id'=>$v['user_id']))->field('avatar_url,nickname')->find();
                    if($user_info){
                        $v['avatar_url'] =$user_info['avatar_url'];
                        // $v['real_name'] =$user_info['real_name'];
                        $v['nickname'] = $user_info['nickname'] ? base64_decode($user_info['nickname']) : '';
                    }else{
                        $v['avatar_url'] ='';
                        // $v['nickname'] ='';
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
            $info_list = array(
                'list'=>$list,
                'count'=>$user_chat->where(array('user_id' => $user_id, 'msg_type'=>array('in',[1,2]),'status' => array('neq', 0)))->count(1),
                'user_detail' => $user_detail ? $user_detail  :''
            );
        }else{
            $info_list = array(
                'list'=>[],
                'count'=>0,
                'user_detail' => $user_detail ? $user_detail  :''
            );
        }


        json_success($info_list);
    }

    /**
     *回复内容
     */
    public function reply_user(){
        $user_id = input('user_id');
        $manger_id = input('manger_id');
        $msg_type = input('msg_type');
        $content = input('content');
        $voice_time = input('voice_time/d',0);
        if(!$user_id){
            json_fail('获取用户id错误');
        }

        // if(!$manger_id){
        //     json_fail('获取管理员id错误');
        // }
        $manger_id = $this->user_id;
        // $manger_id = decode($manger_id);
        if(strlen($content) == 0 ){
            json_fail('内容不能为空');
        }

        $user_chart = new \app\common\model\UserChat();
        //查询上一条时间
        $create_time =$user_chart->where(array('user_id'=>$user_id))->order('create_time desc')->value('create_time');

        //是否大于五分钟
        $add_time = '';
        if((time()-$create_time) >= 300){
            $add_time = date('H:i');
        }
        if ((time()-$create_time) >= 24*60*60){
            $add_time = date('Y-m-d H:i');
        }

        if($add_time){
            $user_chart->insertGetId(array('user_id'=>$user_id,'type'=>2,'pid'=>$manger_id,'msg_type'=>0,'content'=>json_encode($add_time),'create_time'=>time()));
        }

        $add_data = array(
            'user_id'=>$user_id,
            'type'=>2,
            'pid'=>$manger_id,
            'msg_type'=>$msg_type,
            'content'=>json_encode($content),
            'voice_time' => $voice_time,
            'create_time'=>time()
        );

        $chart_id = $user_chart->insertGetId($add_data);

        if($chart_id  === false){
            json_fail('回复失败');
        }


        //咨询回复通知
//        if($msg_type==1){
//            $WxMessageServices = new WxMessageServices();
//            $userServices = new UserServices();
//            $user = $userServices->model->where('id',$user_id)->field('id,openid')->find();
//            $touser = $user['openid'];
//            $template_id = 'vC9Egd_RSKtURoc2RnmJo96CaaUVbuFz5-sRFWp2YYg';
//
//            $page = 'result';
//            $data = [
//                'name1' => ['value' => '照护专家'],
//                'time5' => ['value' => date('Y年m月d日 H:i',$create_time)],
//                'thing3' => ['value' => '请调整老人的生活规律...'],
//            ];
//
//            $res_message = $WxMessageServices->wx_message($touser,$template_id,$page,$data);
//            $message_log_data = [
//                'order_id' => 0,
//                'user_id' => $user_id,
//                'title' => '咨询回复通知',
//                'template_id' => $template_id,
//                'page' => $page,
//                'data' =>json_encode($data),
//                'log' =>json_encode($res_message)
//            ];
//            Db::name('wx_message_log')->insert($message_log_data);
//        }



        json_success('回复成功');
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 返回可用街道
     */
    public function can_use_sheet(){
        $street = new Street();
        $list = $street->where(array('status'=>1))->select();

        json_success($list);
    }

    /**
     * 处理结果
     */
    public function add_result(){
        $user_chat_id = input('user_chat_ids/a');
        $user_id = input('user_id');
        $title = input('title');
        $street_id = input('street_id');
        $content = input('content');
        $manger_id = input('manger_id');
        if(!$user_chat_id){
            json_fail('获取马上办id错误');
        }
        if(!$user_id){
            json_fail('获取用户id错误');
        }
        if(!$title){
            json_fail('获取标题错误');
        }
        if(!$street_id){
            json_fail('获取街道id错误');
        }
        if(!$content){
            json_fail('获取内容错误');
        }
        if(!$manger_id){
            json_fail('获取管理员id错误');
        }
        $manger_id = decode($manger_id);

        $problem = new Problem();
        $problem_user_chat = new ProblemUserChat();
        $user = new \app\common\model\User();
        $user_chart = new \app\common\model\UserChat();

        $addDta = array(
            'user_id'=>$user_id,
            'title'=>$title,
            'street_id'=>$street_id,
            'content'=>$content,
            'create_time'=>time(),
        );
        $problem_id = $problem->insertGetId($addDta);

        if($problem_id === false){
            json_fail('添加失败');
        }

        //添加关联表
        if($user_chat_id){
            $add_problem_data = [];
            foreach ($user_chat_id as $k=>$v){
                $add_problem_data[] = array(
                    'problem_id'=>$problem_id,
                    'user_chat_id'=>$v
                );
            }
            $problem_user_chat->insertAll($add_problem_data);
        }



        //存储消息类型
        $chart_content = array(
          'problem_id'=> $problem_id,
          'title'=> $title,
          'time'=> date('Y年m月d日'),
        );
        $add_chart_data = array(
            'user_id'=>$user_id,
            'type'=>2,
            'pid'=>$manger_id,
            'msg_type'=>3,
            'content'=>json_encode($chart_content),
            'create_time'=>time()
        );

        $chart_id = $user_chart->insertGetId($add_chart_data);
        if($chart_id === false){
            json_fail('处理失败');
        }

        $this->send_subscribe($user_id);

        json_success('处理成功');
    }

    /**
     * 处理结果列表
     */
    public function problem_list(){
        $user_id = input('user_id');
        $street_id = input('street_id');
        $key = input('key');
        $start_time = input('start_time');
        $end_time = input('end_time');

        $page = input('page', 1);
        $limit = input('limit', 10);

        $map = array(
            'a.status'=>array('neq',0)
        );

        if($user_id){
            $map['a.user_id'] = $user_id;
        }

        if($key){
            $map['a.title'] = array('like','%'.$key.'%');
        }
        if ($start_time && $end_time) {
            $start_time = date('Y-m-d H:i:s', strtotime($start_time));
            $end_time = date('Y-m-d 23:59:59', strtotime($end_time));
            $map['a.create_time'] = array('between time', array($start_time, $end_time));
        }
        if($street_id){
            $map['a.street_id'] = $street_id;
        }

        $problem = new Problem();
        $street = new Street();

        $list = $problem->alias('a')
            ->field('a.*,b.nickname_old,b.nickname,b.avatar_url,b.real_name')
            ->join('user b', 'b.id=a.user_id')
            ->where($map)
            ->order('a.id desc')
            ->page($page, $limit)
            ->select();
        if (!$list) {
            $info_list = array(
                'list' => [],
                'count' => 0
            );
        } else {
            foreach ($list as $k => $v) {
                $v['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s', $v['create_time']) : '--';
                $v['nickname'] = $v['nickname'] ? base64_decode($v['nickname']) : '';
                $v['street_name'] = $street->where(array('id'=>$v['street_id']))->value('name');
                $list[$k] = $v;
            }
            $count = $problem->alias('a')
                ->field('a.*,b.nickname_old,b.nickname,b.avatar_url')
                ->join('user b', 'b.id=a.user_id')
                ->where($map)
                ->count(1);
            $info_list = array(
                'list' => $list,
                'count' => $count
            );
        }
        json_success($info_list);
    }


    /**
     * 处理结果详情
     */
    public function problem_detail(){
        $problem_id = input('problem_id');

        if(!$problem_id){
            json_fail('获取结果id错误');
        }
        $problem = new Problem();
        $street = new Street();
        $problem_user_chat = new ProblemUserChat();
        $user_chat = new \app\admin\model\UserChat();
        $user = new \app\admin\model\User();
        $system_manager = new SystemManager();

        $map = array(
            'a.id'=>$problem_id
        );
        $info = $problem->alias('a')
            ->field('a.*,b.nickname_old,b.nickname,b.avatar_url,b.real_name')
            ->join('user b', 'b.id=a.user_id')
            ->where($map)
            ->find();

        if($info){
            $info['create_time'] = $info['create_time'] ? date('Y-m-d H:i:s', $info['create_time']) : '--';
            $info['nickname'] = $info['nickname'] ? base64_decode($info['nickname']) : '';
            $info['street_name'] = $street->where(array('id'=>$info['street_id']))->value('name');

            $chart_ids = $problem_user_chat->where(array('problem_id'=>$problem_id))->column('user_chat_id');

            if($chart_ids){
                $chart_list = $user_chat->where(array('id' => array('in',$chart_ids),'status' => array('neq', 0)))->order('id asc')->select();
                if($chart_list){
                    foreach ($chart_list as $k => $v) {
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
                            $user_info = $system_manager->where(array('id'=>$v['pid']))->field('truename,header')->find();
                            if($user_info){
                                $v['avatar_url'] =$user_info['header'];
                                $v['nickname'] = $user_info['truename'];
                            }else{
                                $v['avatar_url'] ='';
                                $v['nickname'] ='';
                            }
                        }
                        $list[$k] = $v;
                    }
                    $info['chart_list'] = $list;
                }else{
                    $info['chart_list'] = [];
                }
            }else{
                $info['chart_list'] = [];
            }
        }

        json_success($info ? $info :'');
    }


    /**
     * 发送订阅消息
     * @author jihaichuan
     */
    public function send_subscribe($user_id)
    {
        //发送订阅消息
        $user = new \app\common\model\User();
        $open_id = $user->where(array('id'=>$user_id))->value('openid');

        //发送的数据
        $data = array(
            "thing1" => array(
                'value' => '海淀创卫工作人员',
            ),
            "time2" => array(
                'value' => date('Y年m月d日 H:s', time()),
            ),
            "thing3" => array(
                'value' => '回复了您一条消息，请去小程序查看',
            )
        );
        $res = $this->subscribeMessage($open_id,'NcL2W_n8SRzSzAcVjdOMh6D-2SVYQ1C5okEW8XL67p0','/pages/right_off/index', $data);
        dd($res);
    }
}
