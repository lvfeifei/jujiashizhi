<?php

namespace app\admin\controller;
ini_set('max_execution_time', 600);

use app\common\model\WxToken;
use app\common\services\help\HelpServices;
use app\common\services\order\OrderServices;
use app\common\services\user\UserServices;
use app\common\services\wxmessage\WxMessageServices;
use think\Db;
use think\Request;
/**
 * 定时器
 */
class Command
{
    /**
     * 将定时的资讯状态2改为1立即发送
     */
    public function help_updata_status()
    {
        $helpServices = new HelpServices();
        $helpServices->model
            ->where('send_status',2)
            ->chunk(100,function ($help) use ($helpServices){
                foreach ($help as $item){
                    try {
                        $helpServices->model
                            ->where('send_status',2)
                            ->where('create_time','<',time())->update(['send_status' => 1]);
                        echo '资讯id:'.$item['id'].',同步成功.'.PHP_EOL;
                    }catch (\Throwable $e){
                        echo '资讯id:'.$item['id'].',同步失败.'.PHP_EOL;
                    }
                }
            });
        $insertData['create_time'] = time();
        $insertData['log'] = '';
        $insertData['function_name'] = 'help_updata_status';
        Db::name('timer_log')->insert($insertData);
    }
    /**
     * 定时发送照护方案
     * 将待发送方案状态转为已发送方案
     */
    public function order_updata_status()
    {
        $date = input('post.date');
        $date_time =  strtotime($date);
        $orderServices = new OrderServices();
        $WxMessageServices = new WxMessageServices();
        $userServices = new UserServices();
        $orderServices->model
            ->where('status',2)
            ->chunk(100,function ($order) use ($orderServices,$date_time,$WxMessageServices,$userServices){
                foreach ($order as $item){
                    $update['status'] =3;
                    if($date_time){
                        // $update['confirm_send_time'] = $date_time;
                    }else{
                        $date_time = time();
                    }
                    try {

                        $res = $orderServices->model
                            ->where('id',$item['id'])
                            ->where('status',2)
                            ->where('confirm_send_time','<',$date_time)
                            ->update(['status' =>3]);
                        if($res !=false){
                            //照护方案返送时间到 给用户推送消息

                            $user = $userServices->model->where('id',$item['user_id'])->field('id,openid')->find();
                            $touser = $user['openid'];
                            $template_id = 'cHvEcSyo84oy7o1iW60Ij09EfELCgBoLSIb6UvNDsLc';

                            //$page = 'my/jlxx';
                            $page = '/pages/my/zhfa/zhfa?id=' . $item['id'];
                            $data = [
                                'thing1' => ['value' => '失智老人照护方案'],
                                'thing4' => ['value' => '您的照护方案已生成，请前往小程序查看'],
                            ];

                            $res_message = $WxMessageServices->wx_message($touser,$template_id,$page,$data);
                            $message_log_data = [
                                'order_id' => $item['id'],
                                'user_id' => $item['user_id'],
                                'title' => '健康管理方案通知',
                                'template_id' => $template_id,
                                'page' => $page,
                                'data' =>json_encode($data),
                                'log' =>json_encode($res_message)
                            ];
                            Db::name('wx_message_log')->insert($message_log_data);
                        }



                        echo '工单id:'.$item['id'].',同步成功.'.PHP_EOL;
                    }catch (\Throwable $e){
                        echo '工单id:'.$item['id'].',同步失败.'.PHP_EOL;
                    }
                }
            });
        $insertData['create_time'] = time();
        $insertData['log'] = '';
        $insertData['function_name'] = 'order_updata_status';
        Db::name('timer_log')->insert($insertData);
    }

    /**
     *评价状态到时间可以评价  状态修改2
     */
    public function order_updata_evaluate()
    {
        $date = input('post.date');
        $date_time =  strtotime($date);

        $orderServices = new OrderServices();
        $WxMessageServices = new WxMessageServices();
        $userServices = new UserServices();
        $orderServices->model
            ->where('status',3)
            ->where('is_evaluate',3)
            ->chunk(100,function ($order) use ($orderServices,$date_time,$WxMessageServices,$userServices){
                foreach ($order as $item){
                    $update['is_evaluate'] = 2;
                    if($date_time){
                        // $update['evaluate_start_time'] = $date_time;
                    }else{
                        $date_time = time();
                    }
                    try {
                        $res =$orderServices->model
                            ->where('id',$item['id'])
                            ->where('status',3)
                            ->where('is_evaluate',3)
                            ->where('evaluate_start_time','<',$date_time)
                            ->update($update);

                        if($res != false){
                            //照护方案返送时间到 给用户推送消息

                            $user = $userServices->model->where('id',$item['user_id'])->field('id,openid')->find();
                            $touser = $user['openid'];
                            $template_id = 'adOB9ynl2QxVOrdHKi5Esqdi5mchfH312Jt3VEoI5vs';

                            //$page = 'index';
                            $page ='/pages/index/pingjia/pingjia?id=' .$item['id'];
                            $data = [
                                'phrase1' => ['value' => '待评价'],
                                'time2' => ['value' => date('Y年m月d日 H:i',$item['create_time'])],
                                'thing3' => ['value' => '请您对使用一周的照护方案进行评价'],
                            ];

                            $res_message = $WxMessageServices->wx_message($touser,$template_id,$page,$data);
                            $message_log_data = [
                                'order_id' => $item['id'],
                                'user_id' => $item['user_id'],
                                'title' => '服务评价通知',
                                'template_id' => $template_id,
                                'page' => $page,
                                'data' =>json_encode($data),
                                'log' =>json_encode($res_message)
                            ];
                            Db::name('wx_message_log')->insert($message_log_data);
                        }
                        echo '工单id:'.$item['id'].',同步成功.'.PHP_EOL;
                    }catch (\Throwable $e){
                        echo '工单id:'.$item['id'].',同步失败.'.PHP_EOL;
                    }
                }
            });
        $insertData['create_time'] = time();
        $insertData['log'] = '';
        $insertData['function_name'] = 'order_updata_evaluate';
        Db::name('timer_log')->insert($insertData);
    }

    /**
     * 判断工单是否请求到照护方案 没有就重新发送
     * Date: 2022/9/3
     * Time: 13:56
     * USER:GCQ
     */
    public function order_send_program(){


        $orderServices = new OrderServices();
        $orderServices->model
            ->where('is_send',2)

            ->chunk(100,function ($order) use ($orderServices){
                foreach ($order as $item){

                    try {
                        // admin_send_program_details
                        $data = $orderServices->admin_send_program_details($item['id'],1);
                        if(isset($data['status'])){
                        if($data['status']==1){
                            echo '工单id:'.$item['id'].',同步成功.'.PHP_EOL;
                        }else{
                            echo '工单id:'.$item['id'].',同步失败['.$data['msg'].'].'.PHP_EOL;
                        }
                        }
                    }catch (\Throwable $e){
                        echo '工单id:'.$item['id'].',同步失败.'.PHP_EOL;
                    }
                }
            });
        $insertData['create_time'] = time();
        $insertData['log'] = '';
        $insertData['function_name'] = 'order_send_program';
        Db::name('timer_log')->insert($insertData);
    }

    public function user_chat()
    {
        $UserChat = new \app\common\model\UserChat();
        $WxMessageServices = new WxMessageServices();
        $userServices = new UserServices();
        $UserChatList  = $UserChat
            ->field('user_id')
            // ->where('type',2)
            ->where('msg_type',1)
            ->where('user_read',2)
            ->where('send_message_status',2)
            ->where('create_time', '<', time())
            ->group('user_id')
            ->select();
        
        if($UserChatList){
            foreach ($UserChatList as $key => $value){
                $chat_arr = $UserChat
                    ->where('user_id',$value['user_id'])
                    ->where('msg_type',1)
                    ->where('user_read',2)
                    ->where('send_message_status',2)
                    ->where('create_time', '<', time())
                    ->order('create_time','desc')
                    ->select();
                if($chat_arr){
                    if(isset($chat_arr[0]) && !empty($chat_arr[0])){
                        $arr = $chat_arr[0];
                        //创建时间+5分钟大于当前时间发送消息
                        if($arr['create_time']+300 < time()) {
    
                            $res = $UserChat
                                ->where('id', $arr['id'])
                                ->update(['send_message_status' => 1]);
                            //咨询回复通知
                            if ($res == 1) {
        
                                $user = $userServices->model->where('id', $arr['user_id'])->field('id,openid')->find();
                                $touser = '';
                                if (isset($user['openid'])) {
                                    $touser = $user['openid'];
                                }
        
                                $template_id = 'KYeiLmBXUM0SIo9Hf1IW_8PMA3uzjVNEnDQ0CDnLMTA';
        
                                $page = '/pages/result/result';
                                $data = [
                                    'name1' => ['value' => '照护专家'],
                                    'time5' => ['value' => date('Y年m月d日 H:i', $arr['create_time'])],
                                    'thing3' => ['value' => '请调整老人的生活规律...'],
                                ];
        
                                $res_message = $WxMessageServices->wx_message($touser, $template_id, $page, $data);
                                echo '用户id:' . $arr['user_id'] . ',消息推送成功.' . PHP_EOL;
                                $message_log_data = [
                                    'order_id' => 0,
                                    'user_id' => $arr['user_id'],
                                    'title' => '咨询回复通知',
                                    'template_id' => $template_id,
                                    'page' => $page,
                                    'data' => json_encode($data),
                                    'log' => json_encode($res_message)
                                ];
                                Db::name('wx_message_log')->insert($message_log_data);
                            }
                        }

                    }
                }

            }
        }else{
            echo '暂无消息发送';
        }
        $insertData['create_time'] = time();
        $insertData['log'] = '';
        $insertData['function_name'] = 'user_chat';
        Db::name('timer_log')->insert($insertData);

    }



    //测试推送公众好消息
    public function test()
    {
        $WxMessageServices = new WxMessageServices();
        $touser = 'o7W215ZzovuUeFUoyCDBMCecSp2c';
        $template_id = 'cHvEcSyo84oy7o1iW60Ij09EfELCgBoLSIb6UvNDsLc';

        $page = 'my/jlxx/jlxx';
        $data = [
            'thing1' => ['value' => '失智老人照护方案'],
            'thing4' => ['value' => '您的照护方案已生成，请前往小程序查看'],
            // 'time7' => ['value' => '2022年09月03日 13:11'],
        ];
        $res_message = $WxMessageServices->wx_message($touser,$template_id,$page,$data);
        $message_log_data = [
            'order_id' => 0,
            'user_id' => 0,
            'title' => '健康管理方案通知',
            'template_id' => $template_id,
            'page' => $page,
            'data' =>json_encode($data),
            'log' =>json_encode($res_message)
        ];
        Db::name('wx_message_log')->insert($message_log_data);

    }


    /**
     * 定时器维护小程序token
     */
    public function token()
    {

        $con = '更新token '.date('Y-m-d H:i:s');
        $insertData['create_time'] = time();
        $insertData['log'] = $con;
        $insertData['function_name'] = 'token';
        Db::name('timer_log')->insert($insertData);
        $appid = config('xiaocx_app_id');
        $secret = config('xiaocx_app_secret');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret;
        $info = http_curl($url, 'get');
        if (!$info) {
            json_fail('获取token失败');
        }

        //实例化数据库
        $wx_token = new WxToken();

        //查询
        $infoList = $wx_token->where(array('wxatid' => 1))->value('access_token');

        if ($infoList) {
            //更新的数据
            $data = array(
                'access_token' => $info['access_token'],
                'expires_in' => $info['expires_in'],
                'updatetime' => date('Y-m-d H:i:s')
            );
            $wx_token->where(array('wxatid' => 1))->update($data);
        } else {
            //添加的数据
            $data = array(
                'access_token' => $info['access_token'],
                'expires_in' => $info['expires_in'],
                'createtime' => date('Y-m-d H:i:s'),
                'updatetime' => date('Y-m-d H:i:s')
            );
            $wx_token->insertGetId($data);
        }

        echo '更新token成功';
    }

}