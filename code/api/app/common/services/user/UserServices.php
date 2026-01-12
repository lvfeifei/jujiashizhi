<?php

namespace app\common\services\user;

use app\admin\controller\Order;
use app\common\model\EvaluationCapabilityOptions;
use app\common\model\User;
use app\common\model\UserEvaluate;
use app\common\services\BaseServices;
use app\common\services\basicconfig\BasicConfigServices;
use app\common\services\beadhouse\BeadHouseServices;
use app\common\services\order\OrderServices;
use app\common\services\order\OrderEvalustionServices;
use app\common\model\OrderProgram;
use app\common\model\Config;
use think\Cache;
use think\Db;
use think\Exception;
use think\helper\Arr;
use think\Request;

class UserServices extends BaseServices
{
    public function setModel()
    {
        $this->model = new User();
    }

    /**
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *  获取用户信息
     */
    public function getUserInfo($user_id)
    {
        //查询用户信息
        $userInfo = $this->model->where(array('id' => $user_id))->field('openid,unionid',true)->find();
        if ($userInfo) {
            $userInfo['nickname'] = $userInfo['nickname'] ? base64_decode($userInfo['nickname']) : '';
            $basicConfigServices = new BasicConfigServices();

            //性别
            if(array_key_exists($userInfo['gender'],$basicConfigServices->gender)){
                $userInfo['gender_name'] = $basicConfigServices->gender[$userInfo['gender']];
            }else{
                $userInfo['gender_name'] = '';
            }
            //教育
            if(array_key_exists($userInfo['education'],$basicConfigServices->education)){
                $userInfo['education_name'] = $basicConfigServices->education[$userInfo['education']];
            }else{
                $userInfo['education_name'] = '';
            }
            //照护年限
            if(array_key_exists($userInfo['care_years'],$basicConfigServices->care_years)){
                $userInfo['care_years_name'] = $basicConfigServices->care_years[$userInfo['care_years']];
            }else{
                $userInfo['care_years_name'] = '';
            }
            //与患者关系
            if(array_key_exists($userInfo['relation'],$basicConfigServices->relation)){
                $userInfo['relation_name'] = $basicConfigServices->relation[$userInfo['relation']];
            }else{
                $userInfo['relation_name'] = '';
            }
            //是否和患者同住
            if(array_key_exists($userInfo['live'],$basicConfigServices->live)){
                $userInfo['live_name'] = $basicConfigServices->live[$userInfo['live']];
            }else{
                $userInfo['live_name'] = '';
            }
            //患者
            //性别
            if(array_key_exists($userInfo['patient_gender'],$basicConfigServices->patient_gender)){
                $userInfo['patient_gender_name'] = $basicConfigServices->patient_gender[$userInfo['patient_gender']];
            }else{
                $userInfo['patient_gender_name'] = '';
            }
            //教育
            if(array_key_exists($userInfo['patient_education'],$basicConfigServices->patient_education)){
                $userInfo['patient_education_name'] = $basicConfigServices->patient_education[$userInfo['patient_education']];
            }else{
                $userInfo['patient_education_name'] = '';
            }
            //患者疾病类型
            if(array_key_exists($userInfo['patient_disease_type'],$basicConfigServices->patient_disease_type)){
                $userInfo['patient_disease_type_name'] = $basicConfigServices->patient_disease_type[$userInfo['patient_disease_type']];
            }else{
                $userInfo['patient_disease_type_name'] = '';
            }
            //P患者病情严重程度
            if(array_key_exists($userInfo['patient_illness'],$basicConfigServices->patient_illness)){
                $userInfo['patient_illness_name'] = $basicConfigServices->patient_illness[$userInfo['patient_illness']];
            }else{
                $userInfo['patient_illness_name'] = '';
            }
            //患者确诊前的兴趣爱好
            if($userInfo['patient_hobby']){
                $userInfo['patient_hobby'] = json_decode($userInfo['patient_hobby'],true);
                $patient_hobby_str = '';
                if($userInfo['patient_hobby']){
                    foreach ($userInfo['patient_hobby'] as $item){
                        if(array_key_exists($item,$basicConfigServices->patient_hobby)){
                            $patient_hobby_str .= $basicConfigServices->patient_hobby[$item];
                        }else{
                            $patient_hobby_str .= '';
                        }
                    }
                }
                $userInfo['patient_hobby_name'] = $patient_hobby_str;
            }else{
                $userInfo['patient_hobby_name'] = '';

            }
            //R患者行走能力
            if(array_key_exists($userInfo['patient_walk'],$basicConfigServices->patient_walk)){
                $userInfo['patient_walk_name'] = $basicConfigServices->patient_walk[$userInfo['patient_walk']];
            }else{
                $userInfo['patient_walk_name'] = '';
            }

            $userInfo['create_time'] = date('Y-m-d H:i:s',$userInfo['create_time']);
            $userInfo['bead_house_title'] = '';
            if($userInfo['bead_house_id']){
                $beadHouseServices = new BeadHouseServices();
                $beadHouseInfo = $beadHouseServices->model->where('id', $userInfo['bead_house_id'])->field('title')->find();
                if($beadHouseInfo){
                    $userInfo['bead_house_title'] = $beadHouseInfo['title'];
                }
            }



        }else{
            $userInfo = [];
        }
        return res_data(1,'请求成功',$userInfo);
    }
    //同意协议
    public function set_agreement($user_id)
    {
        $time = time();
        $user_info = $this->model->where('id',$user_id)->find();
        if($user_info){
            if($user_info['is_agree'] == 2){
                return res_data(1,'已同意' );
            }
        }
        $res = $this->model->where('id', $user_id)->update(['is_agree' => 2,'agree_time' => $time]);
        if($res !== false){
            return res_data(1,'已同意');
        }else{
            return res_data(0,'未同意');
        }
    }
    public function order_history($userId){
        [$page,$limit] = $this->getPageValue();
        $orderServices = new OrderServices();

        $orderList = $orderServices
            ->where('user_id',$userId)
            // ->whereIn('status',[2,3])
            ->order('status','asc')
            ->order('create_time','desc')

            ->field('id,user_id,status,is_join_research,is_evaluate,create_time,confirm_send_time')
            // ->page($page,$limit)
            ->select();
        $configModel = new Config();
        $key ='expertAvatar';
        if (!in_array($key,$configModel->keys)){
            $expert_avatar = '未设置';
        }else{
            $result = $configModel->where('key',$key)->value('value');
            $expert_avatar = $result;
        }
        $key = '';
        $key = 'sendTime';
        if (!in_array($key,$configModel->keys)){
            $send_ime = '';
        }else{
            $result = $configModel->where('key',$key)->value('value');
            $send_ime = $result;
        }

        //$expert_name = '郑怡康专家';
        $expert_name = config('expert_name');
        $orderProgramModel = new OrderProgram();
        $orderEvalustionServices = new OrderEvalustionServices();
        $evaluationCapabilityOptionsModel = new EvaluationCapabilityOptions();
        // $count = 0;
        if($orderList){
            // $count = $orderServices
            //     ->where('user_id',$userId)
            //     // ->whereIn('status',[2,3])
            //     ->order('status','asc')
            //     ->order('create_time','desc')
            //     ->count('id');
            foreach ($orderList as &$item){
                $item['expert_name'] = $expert_name;
                $item['expert_avatar'] = $expert_avatar;
                $item['status_title'] = '';
                if($item['status']==1 || $item['status']==2){

                    // $item['status_title'] = '预计'.date('d日',$item['confirm_send_time']).is_am_pm(date('a',$item['confirm_send_time'])).date('H:i',$item['confirm_send_time']).'完成';
                    $item['status_title'] = '预计次日'.is_am_pm(date('a',$item['confirm_send_time'])).date('H:i',$item['confirm_send_time']).'完成';

                }
                if($item['status']==3){
                    $item['status_title'] = '专家方案已评估完成';
                }
                $item['create_time'] = date('Y.m.d',$item['create_time']);
                $item['status_name'] = '';
                if(array_key_exists($item['status'],$orderServices->status)){
                    $item['status_name'] = $orderServices->status[$item['status']];
                }
                $item['is_evaluate_name'] = '';
                if(array_key_exists($item['is_evaluate'],$orderServices->evalue_status)){
                    $item['is_evaluate_name'] = $orderServices->evalue_status[$item['is_evaluate']];
                }
                $item['evalustion'] = [];
                $orderEvalustionList = $orderEvalustionServices->model
                    ->where('order_id',$item['id'])
                    ->whereIn('classify_id',[1,2,3])
                    ->select();
                if($orderEvalustionList){
                    $options = [];
                    foreach ($orderEvalustionList as $k=>$evalustionItem){
                        $optionarr = json_decode($evalustionItem['option_id']);
                        if($optionarr){
                            $evaluationCapabilityOptionsList = $evaluationCapabilityOptionsModel
                                ->whereIn('id',$optionarr)
                                ->field('id,name')

                                ->select();
                            if($evaluationCapabilityOptionsList){
                                array_push($options,$evaluationCapabilityOptionsList);
                            }
                        }
                    }
                    $optionsnew = [];
                    foreach ($options as $key=>$v){
                        if($v && is_array($v)){
                            foreach ($v as $vvv){
                                if($vvv['name']=='无'){
                                    continue;
                                }
                                if($vvv['name']=='无（能正常行走或借助辅助工具行走）'){
                                    continue;
                                }
                                $optionsnew[] =array('id'=>$vvv['id'],'name'=>$vvv['name']);

                            }
                        }


                    }
                    $item['evalustion']=array_slice($optionsnew,0,8);;

                    // $item['evalustion'] = $option;
                }

            }
        }
        // $list = [
        //     'list' => $orderList,
        //     'count' => $count,
        //     ];
        return res_data(1,'请求成功',$orderList);
    }
    /**
     *
     * @param $wechatUser
     * @return int|string
     * @throws ApiException
     */
    public function save($userId,$data) {
        $userInfo = $this->model->where('id',$userId)->where('status',1)->find();
        if(!$userInfo)return res_data(0,'用户操作异常');
        $res= $this->model->where('id',$userId)->update($data);
        if($res !== false){
            return res_data(1,'设置成功');
        }else{
            return res_data(0,'设置失败');
        }

    }

    /**
     * 评价保存
     * @param $orderId
     * @param $data
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/8
     * Time: 9:24
     * USER:GCQ
     */
    public function evaluate_save($orderId,$data,$userId){
        $orderServices = new OrderServices();
        $orderInfo = $orderServices->model->where('id',$orderId)->where('user_id',$userId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        if($orderInfo['status'] != 3)return res_data(0,'该工单还未发送');
        $orderEvalustionServices = new  OrderEvalustionServices();
        $orderEvalustionList = $orderEvalustionServices->model->where('order_id',$orderId)->select();
        if(!$orderEvalustionList)return res_data(0,'该工单id还未提交测评问题');
        //照护方案
        $orderProgramModel = new OrderProgram();
        $orderProgram = $orderProgramModel->where('order_id',$orderId)->select();
        if(!$orderProgram)return res_data(0,'该工单id未获取到照护方案，请先获取照护方案');


        //判断评价是否填写完整
        if($this->verify_problem($orderId,$data,$userId)==false)return res_data(0,'请完善评价问题');

        $userEvaluate = new UserEvaluate();

        $userEvaluateInfo =$userEvaluate->where('order_id',$orderId)->where('user_id',$userId)->find();
        if($userEvaluateInfo)return res_data(0,'评价已提交');


        $insertData = [];
        foreach ($data as $k=>$item){
            $insertData[$k]['order_id'] = $orderId;  //工单id
            $insertData[$k]['user_id'] = $userId;    //用户id
            $insertData[$k]['sn'] = $item['sn'];   //照护方案编号
            $insertData[$k]['program_question_id'] = $item['id'];  //照护方案id
            $insertData[$k]['program_option_id'] = $item['option'];   //照护选项id
            $insertData[$k]['create_time'] = time();
        }

        $orderServices = new OrderServices();
        Db::startTrans();
        try {
            //添加评价数据
            $res = $userEvaluate->insertAll($insertData);
            //修改评价状态
            $orderServices->model->where('id',$orderId)->update(['is_evaluate' => 1]);
            Db::commit();
            return res_data(1,'评价成功');
        }catch (\Throwable $e){
            Db::rollback();
            return res_data(0,'评价失败');
        }


    }
    public function verify_problem($orderId,$data,$userId){
        $orderServices = new OrderServices();

        $orderEvalustionServices = new  OrderEvalustionServices();

        //照护方案
        $orderProgramModel = new OrderProgram();

        $program_class = [
            ['id' => 1, 'program_class' => 'activity_advice', 'program_class_name' => '日常生活安排'],
            ['id' => 2, 'program_class' => 'problem_advice', 'program_class_name' => '照护问题的照护建议'],
            // ['id' => 3, 'program_class' => 'environment_advice', 'program_class_name' => '居住环境安排'],
        ];
        $basicConfigServices  = new BasicConfigServices();
        $activity_options = $basicConfigServices->activity_options;
        $care_options = $basicConfigServices->care_options;
        $count = 0;
        if($program_class){

            $orderProgramCount = $orderProgramModel
                ->where('order_id',$orderId)
                ->where('advice','activity_advice')
                ->where('pid',0)
                ->field('is_del',true)
                ->count('id');
            $count = $orderProgramCount;
            foreach ($program_class as &$item){
                $item['Program'] = [];
                $orderProgramList = $orderProgramModel
                    ->where('order_id',$orderId)
                    ->where('advice',$item['program_class'])
                    ->where('pid',0)
                    ->field('is_del',true)
                    ->select();
                if($orderProgramList){
                    foreach ($orderProgramList as $key => $value){
                        $orderProgramList[$key]['content'] = json_decode($value['content'],true);
                        if($value['advice'] == 'problem_advice'){
                            if(trim($value['content'])== '无（能正常行走或借助辅助工具行走）'){
                                unset($orderProgramList[$key]);
                                continue;
                            }
                            if(trim($value['content'])== '无'){
                                unset($orderProgramList[$key]);
                                continue;
                            }

                        }

                        $programCount = $orderProgramModel
                            ->where('order_id',$orderId)
                            ->where('advice',$item['program_class'])
                            ->where('pid',$value['id'])
                            ->count('id');
                        $count+=$programCount;

                    }


                }

            }
            // echo count($data);
            // echo '---';
            // echo $count;
            // die;

        }
        if(count($data)==$count){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 绑定养老院
     * @param $user_id
     * @param $id
     * @return array
     */
    public function set_bind_bead_house($user_id,$id)
    {
        $beadHouseServices = new BeadHouseServices();
        $beadhouseinfo = $beadHouseServices->model->where('id',$id)->where('status','neq', 0)->find();
        if($beadhouseinfo){
            $res = $this->model->where('id',$user_id)->update(['bead_house_id'=>$id]);
            if($res !== false){
                return res_data(1,'绑定成功');
            }else{
                return res_data(0,'绑定失败');
            }
        }else{
            return res_data(0, '养老院不存在');
        }

    }

    public function set_unbind_bead_house($user_id)
    {
        $res = $this->model->where('id',$user_id)->update(['bead_house_id'=>0]);
        if($res !== false){
            return res_data(1,'解绑成功');
        }else{
            return res_data(0,'解绑失败');
        }
    }

    //后台
    /**
     * 后台用户列表
     * @param $where
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function adminList($where,$user_id, $role_id) {
        [$page,$limit] = $this->getPageValue();
        $list = $this->adminSearch($where, $user_id, $role_id)
            ->field('id,nickname,avatar_url,gender,age,education,care_years,relation,live,status,create_time,is_agree,agree_time,bead_house_id')
            ->where('authorization',1)
            ->whereIn('status',[1,2])
            ->order('create_time','desc')->page($page,$limit)->select();
        $count = 0;
        if ($list){
            $beadHouseServices = new BeadHouseServices();
            $basicConfigServices = new BasicConfigServices();
            foreach ($list as &$item){
                $item['nickname'] = $item['nickname'] ? base64_decode($item['nickname']) : '';
                if ($item['status'] == 1){
                    $item['status_s'] = '正常';
                }elseif ($item['status'] == 2){
                    $item['status_s'] = '黑名单';
                }
                //性别
                if(array_key_exists($item['gender'],$basicConfigServices->gender)){
                    $item['gender_name'] = $basicConfigServices->gender[$item['gender']];
                }else{
                    $item['gender_name'] = '';
                }
                //教育
                if(array_key_exists($item['education'],$basicConfigServices->education)){
                    $item['education_name'] = $basicConfigServices->education[$item['education']];
                }else{
                    $item['education_name'] = '';
                }
                //照护年限
                if(array_key_exists($item['care_years'],$basicConfigServices->care_years)){
                    $item['care_years_name'] = $basicConfigServices->care_years[$item['care_years']];
                }else{
                    $item['care_years_name'] = '';
                }
                //与患者关系
                if(array_key_exists($item['relation'],$basicConfigServices->relation)){
                    $item['relation_name'] = $basicConfigServices->relation[$item['relation']];
                }else{
                    $item['relation_name'] = '';
                }
                //是否和患者同住
                if(array_key_exists($item['live'],$basicConfigServices->live)){
                    $item['live_name'] = $basicConfigServices->live[$item['live']];
                }else{
                    $item['live_name'] = '';
                }

                if ($item['is_agree'] == 2){
                    $item['is_agree_t'] = '同意';
                }else{
                    $item['is_agree_t'] = '未同意';
                }
                $item['create_time'] = date('Y-m-d H:i:s',$item['create_time']);
                $item['agree_time'] = date('Y-m-d H:i:s',$item['agree_time']);
                $item['bead_house_title'] = '';
                if($item['bead_house_id']){
                    $beadHouseInfo = $beadHouseServices->model->where('id', $item['bead_house_id'])->field('title')->find();
                    if($beadHouseInfo){
                        $item['bead_house_title'] = $beadHouseInfo['title'];
                    }
                }

            }
            $count = $this->adminSearch($where, $user_id, $role_id)
                ->where('authorization',1)
                ->whereIn('status',[1,2])
                ->count('id');
        }
        $beadHouseServices = new BeadHouseServices();
        $beadHouseinfo = $beadHouseServices->model->where('sysetm_manager_id',$user_id)->find();
        if($beadHouseinfo){
            $bead_house_id = $beadHouseinfo['id'];
        }else{
            $bead_house_id = '';
        }
        return res_data(1,'请求成功',['list' => $list,'count' => $count,'bead_house_id'=>$bead_house_id]);
    }


    public function adminSearch($where,$user_id, $role_id) {
        $model = $this->model;
        if($role_id == 11){
            $beadHouseServices = new BeadHouseServices();
            $bead_house = $beadHouseServices->model->where('sysetm_manager_id',$user_id)->field('id')->find();
            $model->where('bead_house_id', $bead_house['id']);
        }else{
            if (isset($where['bead_house_id']) && !empty($where['bead_house_id'])){
                $model = $model->where('bead_house_id',$where['bead_house_id']);
            }
        }
        
      
        //关键词搜索
        if (isset($where['key']) && !empty($where['key'])){
            $model = $model->where('nickname',$where['key']);
        }
        //性别
        if (isset($where['gender']) && !empty($where['gender'])){
            $model = $model->where('gender',$where['gender']);
        }
        //教育程度
        if (isset($where['education']) && !empty($where['education'])){
            $model = $model->where('education',$where['education']);
        }
        //照顾年限
        if (isset($where['care_years']) && !empty($where['care_years'])){
            $model = $model->where('care_years',$where['care_years']);
        }
        //与患者关系
        if (isset($where['relation']) && !empty($where['relation'])){
            $model = $model->where('relation',$where['relation']);
        }
        //是否与患者同住
        if (isset($where['live']) && !empty($where['live'])){
            $model = $model->where('live',$where['live']);
        }

        return $model;
    }

    /**
     * 后台用户详情
     * @param $id
     * @return array|bool|\PDOStatement|string|\think\Model|null
     * @throws AdminException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function adminDetails($id) {
        $userInfo = $this->model
            ->where('id',$id)
            ->field('openid,unionid',true)
            ->find();
        if($userInfo){
            $basicConfigServices = new BasicConfigServices();
            if ($userInfo['status'] == 1){
                $userInfo['status_s'] = '正常';
            }elseif ($userInfo['status'] == 2){
                $userInfo['status_s'] = '黑名单';
            }
            if($userInfo['is_agree'] == 2){
                $userInfo['is_agree_t'] = '同意';
            }else{
                $userInfo['is_agree_t'] =  '未同意';
            }
            $userInfo['nickname'] = $userInfo['nickname'] ? base64_decode($userInfo['nickname']) : '';
            //性别
            if(array_key_exists($userInfo['gender'],$basicConfigServices->gender)){
                $userInfo['gender_name'] = $basicConfigServices->gender[$userInfo['gender']];
            }else{
                $userInfo['gender_name'] = '';
            }
            //教育
            if(array_key_exists($userInfo['education'],$basicConfigServices->education)){
                $userInfo['education_name'] = $basicConfigServices->education[$userInfo['education']];
            }else{
                $userInfo['education_name'] = '';
            }
            //照护年限
            if(array_key_exists($userInfo['care_years'],$basicConfigServices->care_years)){
                $userInfo['care_years_name'] = $basicConfigServices->care_years[$userInfo['care_years']];
            }else{
                $userInfo['care_years_name'] = '';
            }
            //与患者关系
            if(array_key_exists($userInfo['relation'],$basicConfigServices->relation)){
                $userInfo['relation_name'] = $basicConfigServices->relation[$userInfo['relation']];
            }else{
                $userInfo['relation_name'] = '';
            }
            //是否和患者同住
            if(array_key_exists($userInfo['live'],$basicConfigServices->live)){
                $userInfo['live_name'] = $basicConfigServices->live[$userInfo['live']];
            }else{
                $userInfo['live_name'] = '';
            }
            //患者
            //性别
            if(array_key_exists($userInfo['patient_gender'],$basicConfigServices->patient_gender)){
                $userInfo['patient_gender'] = $basicConfigServices->patient_gender[$userInfo['patient_gender']];
            }else{
                $userInfo['patient_gender_name'] = '';
            }
            //教育
            if(array_key_exists($userInfo['patient_education'],$basicConfigServices->patient_education)){
                $userInfo['patient_education_name'] = $basicConfigServices->patient_education[$userInfo['patient_education']];
            }else{
                $userInfo['patient_education_name'] = '';
            }
            //患者疾病类型
            if(array_key_exists($userInfo['patient_disease_type'],$basicConfigServices->patient_disease_type)){
                $userInfo['patient_disease_type_name'] = $basicConfigServices->patient_disease_type[$userInfo['patient_disease_type']];
            }else{
                $userInfo['patient_disease_type_name'] = '';
            }
            //P患者病情严重程度
            if(array_key_exists($userInfo['patient_illness'],$basicConfigServices->patient_illness)){
                $userInfo['patient_illness_name'] = $basicConfigServices->patient_illness[$userInfo['patient_illness']];
            }else{
                $userInfo['patient_illness_name'] = '';
            }
            //患者确诊前的兴趣爱好
            if($userInfo['patient_hobby']){
                $userInfo['patient_hobby'] = json_decode($userInfo['patient_hobby'],true);
                $patient_hobby_str = '';
                if($userInfo['patient_hobby']){
                    foreach ($userInfo['patient_hobby'] as $item){
                        if(array_key_exists($item,$basicConfigServices->patient_hobby)){
                            if($item == 'Q8'){
                                $patient_hobby_str .= $basicConfigServices->patient_hobby[$item].'('.$userInfo['patient_hobby_content'].')';
                            }else{
                                $patient_hobby_str .= $basicConfigServices->patient_hobby[$item];
                            }
                            // $patient_hobby_str .= $basicConfigServices->patient_hobby[$item];
                        }else{
                            $patient_hobby_str .= '';
                        }
                    }
                }
                $userInfo['patient_hobby_name'] = $patient_hobby_str;
            }else{
                $userInfo['patient_hobby_name'] = '';

            }
            //R患者行走能力
            if(array_key_exists($userInfo['patient_walk'],$basicConfigServices->patient_walk)){
                $userInfo['patient_walk_name'] = $basicConfigServices->patient_walk[$userInfo['patient_walk']];
            }else{
                $userInfo['patient_walk_name'] = '';
            }

            $userInfo['create_time'] = date('Y-m-d H:i:s',$userInfo['create_time']);
            $userInfo['agree_time'] = date('Y-m-d H:i:s',$userInfo['agree_time']);
            $beadHouseServices = new BeadHouseServices();
            $userInfo['bead_house_title'] = '';
            if($userInfo['bead_house_id']){
                $beadHouseInfo = $beadHouseServices->model->where('id', $userInfo['bead_house_id'])->field('title')->find();
                if($beadHouseInfo){
                    $userInfo['bead_house_title'] = $beadHouseInfo['title'];
                }
            }
        }else{
            $userInfo = [];
        }

        return $userInfo;
    }
    public function admin_evaluation_list($id){
        $OrderServices = new OrderServices();
        [$page,$limit] = $this->getPageValue();
        $list = $OrderServices->model
            ->where('user_id',$id)
            ->order('create_time','desc')
            ->page($page,$limit)
            ->select();
        $count = 0;
        if($list){
            $count =$OrderServices->model->where('user_id',$id)->count('id');
            $basicConfigServices = new BasicConfigServices();
            foreach ($list as &$item){
                //是否参加调研
                $item['is_join_research_name'] = '';
                if (array_key_exists($item['is_join_research'], $OrderServices->join_research_status)) {
                    $item['is_join_research_name'] = $OrderServices->join_research_status[$item['is_join_research']];
                }
                //测评状态
                $item['status_name'] = '';
                if (array_key_exists($item['status'], $OrderServices->status)) {
                    $item['status_name'] = $OrderServices->status[$item['status']];
                }
                $item['gender_name'] = '';
                if (array_key_exists($item['gender'], $basicConfigServices->patient_gender)) {
                    $item['gender_name'] = $basicConfigServices->patient_gender[$item['gender']];
                }
                $item['education_name'] = '';
                if (array_key_exists($item['education'], $basicConfigServices->patient_education)) {
                    $item['education_name'] = $basicConfigServices->patient_education[$item['education']];
                }

                $item['disease_type_name'] = '';
                if (array_key_exists($item['disease_type'], $basicConfigServices->patient_disease_type)) {
                    $item['disease_type_name'] = $basicConfigServices->patient_disease_type[$item['disease_type']];
                }
                $item['illness_name'] = '';
                if (array_key_exists($item['illness'], $basicConfigServices->patient_illness)) {
                    $item['illness_name'] = $basicConfigServices->patient_illness[$item['illness']];
                }

                $item['walk_name'] = '';
                if (array_key_exists($item['walk'], $basicConfigServices->patient_walk)) {
                    $item['walk_name'] = $basicConfigServices->patient_walk[$item['walk']];
                }
                $item['hobby_name'] = '';
                $hobby = json_decode($item['hobby'], true);
                if ($hobby) {
                    foreach ($hobby as $v) {
                        if (array_key_exists($v, $basicConfigServices->patient_hobby)) {
                            if ($v == 'Q8') {
                                $item['hobby_name'] .= $basicConfigServices->patient_hobby[$v] . '(' . $item['hobby_content'] . ')';
                            } else {
                                $item['hobby_name'] .= $basicConfigServices->patient_hobby[$v] . '、';
                            }

                        }

                    }
                }



                $item['create_time'] = date('Y-m-d H:i',$item['create_time']);
            }
        }
        return res_data(1,'请求成功',['list'=>$list,'count'=>$count]);
    }

    /**
     * 获取用户状态
     * @param $status
     * @return string|null
     */
    public function getStatusName($status){
        if ($status == 1){
            return '正常';
        }elseif ($status == 2){
            return '黑名单';
        }else{
            return null;
        }
    }


}