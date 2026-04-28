<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/28
     * Time: 11:42
     */
namespace app\common\services\order;
use app\common\model\Config;
use app\common\model\Order;
use app\common\model\OrderEvaluation;

use app\common\model\OrderProgram;
use app\common\model\OrderResearchFamily;
use app\common\model\OrderResearchScenes;
use app\common\model\OrderProgramLog;
use app\common\model\UserEvaluate;
use app\common\services\basicconfig\BasicConfigServices;
use app\common\services\BaseServices;
use app\common\services\beadhouse\BeadHouseServices;
use app\common\services\evaluationclass\EvaluationClassServices;
use app\common\services\familyrelation\FamilyRelationServices;
use app\common\services\user\UserServices;
use app\common\services\evaluationcapability\EvaluationCapabilityServices;
use app\common\model\EvaluationCapabilityOptions;
use app\common\services\wxmessage\WxMessageServices;

use Prophecy\Exception\Exception;
use think\Db;
class OrderServices extends BaseServices
{
    public $evalue_status = [
        1 => '已评价',
        2 => '待评价'
    ];
    public $join_research_status = [
        1 => '已参加',
        2 => '未参加'
    ];
    public $send_status = [
        1 => '发送成功',
        2 => '发送失败'
    ];

    public $status = [
        1 => '待测评',
        2 => '待发送方案',
        3 => '已发送方案',
        4 => '待评价',
        5 => '已评价'
    ];

    public function setModel()
    {
        $this->model = new Order();
    }

    public function order_capability($orderId,$user_id)
    {
        $orderInfo = $this->model
            ->where('id',$orderId)
            ->where('user_id',$user_id)
            ->field('id,age,gender,education,disease_type,disease_type,illness,hobby,hobby_content,walk,status,confirm_send_time')
            ->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        $evaluationClassServices = new EvaluationClassServices();
        $class = $evaluationClassServices->model->where('is_del',1)->field('id,name,content')->select();
        if(!$class)return res_data(0,'未获取到分类');
        $orderEvalustionServices = new OrderEvalustionServices();
        $evaluationCapabilityServices = new EvaluationCapabilityServices();
        $evaluationCapabilityOptions =  new EvaluationCapabilityOptions();

        $basicConfigServices = new BasicConfigServices();

        $orderInfo['status_title'] = '';
        if($orderInfo['status']==1 || $orderInfo['status']==2){

            // $orderInfo['status_title'] = '预计'.date('d日',$orderInfo['confirm_send_time']).is_am_pm(date('a',$orderInfo['confirm_send_time'])).date('H:i',$orderInfo['confirm_send_time']).'完成';
            $orderInfo['status_title'] = '预计次日'.is_am_pm(date('a',$orderInfo['confirm_send_time'])).date('H:i',$orderInfo['confirm_send_time']).'完成';
        }
        if($orderInfo['status']==3){
            $orderInfo['status_title'] = '专家方案已评估完成';
        }
        //性别
        if(array_key_exists($orderInfo['gender'],$basicConfigServices->patient_gender)){
            $orderInfo['gender_name'] = $basicConfigServices->patient_gender[$orderInfo['gender']];
        }else{
            $orderInfo['gender_name'] = '';
        }
        //教育
        if(array_key_exists($orderInfo['education'],$basicConfigServices->patient_education)){
            $orderInfo['education_name'] = $basicConfigServices->patient_education[$orderInfo['education']];
        }else{
            $orderInfo['education_name'] = '';
        }
        //患者疾病类型
        if(array_key_exists($orderInfo['disease_type'],$basicConfigServices->patient_disease_type)){
            $orderInfo['disease_type_name'] = $basicConfigServices->patient_disease_type[$orderInfo['disease_type']];
        }else{
            $orderInfo['disease_type_name'] = '';
        }
        //P患者病情严重程度
        if(array_key_exists($orderInfo['illness'],$basicConfigServices->patient_illness)){
            $orderInfo['illness_name'] = $basicConfigServices->patient_illness[$orderInfo['illness']];
        }else{
            $orderInfo['illness_name'] = '';
        }
        //患者确诊前的兴趣爱好
        if($orderInfo['hobby']){
            $orderInfo['hobby'] = json_decode($orderInfo['hobby'],true);
            $hobby_str = '';
            if($orderInfo['hobby']){
                foreach ($orderInfo['hobby'] as $item){
                    if(array_key_exists($item,$basicConfigServices->patient_hobby)){

                        if($item == 'Q8'){
                            $hobby_str .= $basicConfigServices->patient_hobby[$item].'('.$orderInfo['hobby_content'].')';
                        }else{
                            $hobby_str .= $basicConfigServices->patient_hobby[$item];
                        }

                    }else{
                        $hobby_str .= '';
                    }
                }
            }
            $orderInfo['hobby_name'] = $hobby_str;
        }else{
            $orderInfo['hobby_name'] = '';
        }
        //R患者行走能力
        if(array_key_exists($orderInfo['walk'],$basicConfigServices->patient_walk)){
            $orderInfo['walk_name'] = $basicConfigServices->patient_walk[$orderInfo['walk']];
        }else{
            $orderInfo['walk_name'] = '';
        }


        $orderProgramstr = '';

        // //患者信息

        $ress['patient'] = $orderInfo;

        //分类
        foreach ($class as &$item){
            $item['capability'] = [];
            $orderEvalustion = $orderEvalustionServices->model
                ->where('order_id',$orderId)
                ->where('classify_id',$item['id'])
                ->select();
            //工单问答测评问题和选项
            if($orderEvalustion){

                foreach ($orderEvalustion as &$itemitem){
                    //测评问题 选择的问题
                    $evaluationCapability = $evaluationCapabilityServices->model
                        ->where('id',$itemitem['capability_id'])
                        ->field('id,sn,name')
                        ->find();
                    $itemitem['capability'] =$evaluationCapability;

                    $itemitem['options'] = [];
                    $option = json_decode($itemitem['option_id'],true);
                    if($option){
                        //测评选项 选择的选项
                        $itemitem['options'] = $evaluationCapabilityOptions->whereIn('id',$option)->field('id,sn,name')->select();

                    }
                }
                $item['capability'] = $orderEvalustion;
            }

        }
        $ress['capability'] = $class;
        return res_data(1,'请求成功！',$ress);
    }
    /**
     * 患者信息/照护方案分类列表
     * @param $orderId
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/9
     * Time: 20:15
     * USER:GCQ
     */
    public function patient_program($orderId,$userId)
    {
        $orderInfo = $this->model
            ->where('id',$orderId)
            ->where('user_id',$userId)
            ->where('status',3)
            ->field('id,age,gender,education,disease_type,disease_type,illness,hobby,hobby_content,walk')
            ->find();
        if(!$orderInfo)return res_data(0,'未找到id='.$orderId.'照护方案');
        $basicConfigServices = new BasicConfigServices();


        //性别
        if(array_key_exists($orderInfo['gender'],$basicConfigServices->patient_gender)){
            $orderInfo['gender_name'] = $basicConfigServices->patient_gender[$orderInfo['gender']];
        }else{
            $orderInfo['gender_name'] = '';
        }
        //教育
        if(array_key_exists($orderInfo['education'],$basicConfigServices->patient_education)){
            $orderInfo['education_name'] = $basicConfigServices->patient_education[$orderInfo['education']];
        }else{
            $orderInfo['education_name'] = '';
        }
        //患者疾病类型
        if(array_key_exists($orderInfo['disease_type'],$basicConfigServices->patient_disease_type)){
            $orderInfo['disease_type_name'] = $basicConfigServices->patient_disease_type[$orderInfo['disease_type']];
        }else{
            $orderInfo['disease_type_name'] = '';
        }
        //P患者病情严重程度
        if(array_key_exists($orderInfo['illness'],$basicConfigServices->patient_illness)){
            $orderInfo['illness_name'] = $basicConfigServices->patient_illness[$orderInfo['illness']];
        }else{
            $orderInfo['illness_name'] = '';
        }
        //患者确诊前的兴趣爱好
        if($orderInfo['hobby']){
            $orderInfo['hobby'] = json_decode($orderInfo['hobby'],true);
            $hobby_str = '';
            if($orderInfo['hobby']){
                foreach ($orderInfo['hobby'] as $item){
                    if(array_key_exists($item,$basicConfigServices->patient_hobby)){

                        if($item == 'Q8'){
                            $hobby_str .= $basicConfigServices->patient_hobby[$item].'('.$orderInfo['hobby_content'].')';
                        }else{
                            $hobby_str .= $basicConfigServices->patient_hobby[$item];
                        }

                    }else{
                        $hobby_str .= '';
                    }
                }
            }
            $orderInfo['hobby_name'] = $hobby_str;
        }else{
            $orderInfo['hobby_name'] = '';
        }
        //R患者行走能力
        if(array_key_exists($orderInfo['walk'],$basicConfigServices->patient_walk)){
            $orderInfo['walk_name'] = $basicConfigServices->patient_walk[$orderInfo['walk']];
        }else{
            $orderInfo['walk_name'] = '';
        }

        $orderProgramModel = new OrderProgram();
        $orderProgramList = $orderProgramModel
            ->where('order_id',$orderId)
            ->where('advice','problem_advice')
            ->where('pid',0)
            ->field('id,content')
            ->page(0,5)
            ->select();
       // dump($orderProgramModel->getLastSql());
        // die;
        $orderProgramstr = '';
        if($orderProgramList){
            foreach($orderProgramList as $programItem){
                $content = json_decode($programItem['content'],true);
                if ($content == '无') continue;
                $orderProgramstr .= '“'.$content.'”，';
            }

            $orderProgramstr = rtrim($orderProgramstr,'，');
            if ($orderProgramstr == ''){
               $orderProgramstr = '“无”';
            }
        }else{
            $orderProgram =[];
        }

        $patient = $orderInfo['disease_type_name']. $orderInfo['gender_name'].'性，'.$orderInfo['age'].'岁，'.
            $orderInfo['education_name'].'，病情严重程度是'.$orderInfo['illness_name'].'，确诊前的兴趣爱好是'.
            $orderInfo['hobby_name'].'，行走能力是'.$orderInfo['walk_name'].'；现存照护的问题是：'.$orderProgramstr.'。';
        // //患者信息

        $ress['patient'] = $patient;


        $program_class = [
            ['id' => 1, 'program_class' => 'environment_advice', 'program_class_name' => '居住环境安排'],
            ['id' => 2, 'program_class' => 'activity_advice', 'program_class_name' => '日常生活安排'],
            ['id' => 3, 'program_class' => 'problem_advice', 'program_class_name' => '具体照护问题的照护建议'],
        ];
        $problem_advice_child = $orderProgramModel
            ->where('order_id',$orderId)
            ->where('advice',$program_class[2]['program_class'])
            ->where('pid',0)
            ->page(0,5)
            ->select();

        // dump($problem_advice_child);
        $program_class[2]['program_child'] = [];
        if($problem_advice_child){
            $argc =[];

            foreach ($problem_advice_child as $kk=>$value){
                if(json_decode($value['content'],true) != '无'){
                    $argc[$kk]['content'] = json_decode($value['content'],true);

                }

            }

            $program_class[2]['program_child'] = $argc;
        }
        //方案分类

        $ress['program'] = $program_class;

        // return res_data(1,'请求成功',$ress);
        return res_data(1,'请求成功',['patient'=>$patient,'program'=>$program_class]);
    }

    /**
     * 照护方案分类列表
     * @param $orderId
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/11
     * Time: 9:18
     * USER:GCQ
     */
    public function program($orderId,$userId)
    {
        $orderInfo = $this->model
            ->where('id',$orderId)
            ->where('user_id',$userId)
            ->where('status',3)
            ->find();
        if(!$orderInfo)return res_data(0,'未找到该工单');
        $program_class = [
            ['id' => 1, 'program_class' => 'environment_advice', 'program_class_name' => '居住环境安排'],
            ['id' => 2, 'program_class' => 'activity_advice', 'program_class_name' => '日常生活安排'],
            ['id' => 3, 'program_class' => 'problem_advice', 'program_class_name' => '具体照护问题的照护建议'],
        ];
        $orderProgramModel = new OrderProgram();

            $problem_advice_child = $orderProgramModel
                ->where('order_id',$orderId)
                ->where('advice',$program_class[2]['program_class'])
                ->where('pid',0)
                ->page(0,5)
                ->select();
        $program_class[2]['program_child'] = [];
        if($problem_advice_child){
            foreach ($problem_advice_child as &$value){
                $value['content'] = json_decode($value['content'],true);
            }
            $program_class[2]['program_child'] = $problem_advice_child;
        }
        return res_data(1,'请求成功',$program_class);


    }

    public function program_details($orderId,$className,$userId)
    {
        $orderInfo = $this->model
            ->where('id',$orderId)
            ->where('user_id',$userId)
            ->where('status',3)
            ->find();
        if(!$orderInfo)return res_data(0,'未找到该工单');
        $orderProgramModel = new OrderProgram();
        $program_class = [
            ['id' => 1, 'program_class' => 'environment_advice', 'program_class_name' => '居住环境安排'],
            ['id' => 2, 'program_class' => 'activity_advice', 'program_class_name' => '日常生活安排'],
            ['id' => 3, 'program_class' => 'problem_advice', 'program_class_name' => '具体照护问题的照护建议'],
        ];
        $res['program_class'] = '';
        $res['program'] = [];
        foreach ($program_class as $v){
            if($v['program_class']==$className){
                $res['program_class'] =$v['program_class_name'];
            }
        }
        $orderProgramInfo = $orderProgramModel
            ->where('order_id',$orderId)
            ->where('advice',$className)
            ->where('pid',0)->field('id,content,advice')
            ->select();
        if($orderProgramInfo){
            $i =1;
            foreach ($orderProgramInfo as $kk=>$value){

                if($value['advice']=='problem_advice'){
                    $con_value = json_decode($value['content'],true);
                    if(trim($con_value)== '无（能正常行走或借助辅助工具行走）'){
                        unset($orderProgramInfo[$kk]);
                        continue;
                    }
                    if(trim($con_value)=='无'){
                        unset($orderProgramInfo[$kk]);
                        continue;
                    }
                    $orderProgramInfo[$kk]['content'] = numToWord($i).'、'.$con_value;
                    $i++;
                }else{
                    $orderProgramInfo[$kk]['content'] = json_decode($value['content'],true);
                }

                $value['child'] = [];
                if($value['advice']=='problem_advice'){


                    $orderProgramChild = $orderProgramModel
                        ->where('order_id',$orderId)
                        ->where('pid',$value['id'])
                        ->field('id,sn,content')
                        ->select();
                    if($orderProgramChild){
                        foreach ($orderProgramChild as &$item){
                            $item['content'] = json_decode($item['content'],true);
                        }
                        $value['child'] = $orderProgramChild;
                    }

                }

            }
        }else{

            $orderProgramInfo = [];
        }
        $res['program'] = $orderProgramInfo;


        return res_data(1,'请求成功',$res);
    }


    /**
     * 测评问题详情
     * @param $orderId
     * @param int $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/4
     * Time: 20:59
     * USER:GCQ
     */
    public function details($orderId,$userId=0)
    {
        $orderInfo = $this->model
            ->where('id',$orderId)
            ->where('user_id',$userId)
            ->field('id,gender,age,education,disease_type,illness,hobby,hobby_content,walk,status')
            ->find();
        $order['user'] = [];
        $order['evaluation_capability'] = [];
        if($orderInfo) {
            $basicConfigServices = new BasicConfigServices();
            $orderInfo['gender_name'] = '';
            if (array_key_exists($orderInfo['gender'], $basicConfigServices->patient_gender)) {
                $orderInfo['gender_name'] = $basicConfigServices->patient_gender[$orderInfo['gender']];
            }
            $orderInfo['education_name'] = '';
            if (array_key_exists($orderInfo['education'], $basicConfigServices->patient_education)) {
                $orderInfo['education_name'] = $basicConfigServices->patient_education[$orderInfo['education']];
            }

            $orderInfo['disease_type_name'] = '';
            if (array_key_exists($orderInfo['disease_type'], $basicConfigServices->patient_disease_type)) {
                $orderInfo['disease_type_name'] = $basicConfigServices->patient_disease_type[$orderInfo['disease_type']];
            }
            $orderInfo['illness_name'] = '';
            if (array_key_exists($orderInfo['illness'], $basicConfigServices->patient_illness)) {
                $orderInfo['illness_name'] = $basicConfigServices->patient_illness[$orderInfo['illness']];
            }

            $orderInfo['walk_name'] = '';
            if (array_key_exists($orderInfo['walk'], $basicConfigServices->patient_walk)) {
                $orderInfo['walk_name'] = $basicConfigServices->patient_walk[$orderInfo['walk']];
            }
            $orderInfo['hobby_name'] = '';
            $hobby = json_decode($orderInfo['hobby'], true);
            if ($hobby) {
                foreach ($hobby as $v) {
                    if (array_key_exists($v, $basicConfigServices->patient_hobby)) {
                        if ($v == 'Q8') {
                            $orderInfo['hobby_name'] .= $basicConfigServices->patient_hobby[$v] . '(' . $orderInfo['hobby_content'] . ')';
                        } else {
                            $orderInfo['hobby_name'] .= $basicConfigServices->patient_hobby[$v] . '、';
                        }

                    }

                }
            }
            //测评问题
            $evaluationClassServices =new EvaluationClassServices();
            $class = $evaluationClassServices->model
                ->where('status',1)
                ->where('is_del',1)
                ->field('id,name,content')
                ->select();
            if($class){

                $orderEvalustionServices = new OrderEvalustionServices();
                $evaluationCapabilityServices = new EvaluationCapabilityServices();
                $evaluationCapabilityOptions = new EvaluationCapabilityOptions();

                foreach ($class as &$item){
                    $item['capability'] = [];
                    $orderEvalustion = $orderEvalustionServices->model
                        ->where('order_id',$orderId)
                        ->where('classify_id',$item['id'])
                        ->select();

                    if($orderEvalustion){
                        $evaluationCapabilityOptionsList = [];
                        foreach ($orderEvalustion as &$itemitem){

                            $evaluationCapability = $evaluationCapabilityServices->model
                                ->where('id',$itemitem['capability_id'])
                                ->field('id,sn,name')
                                ->select();
                            $itemitem['capability'] =$evaluationCapability;


                            $option = json_decode($itemitem['option_id'],true);
                            if($option){
                                $itemitem['optins'] = $evaluationCapabilityOptions->whereIn('id',$option)->field('id,sn,name')->select();

                            }
                        }
                        $item['capability'] = $orderEvalustion;
                    }

                }
            }

            $order['user'] = $orderInfo;
            $order['evaluation_capability'] = $class;
        }

        return res_data(1,'请求成功',$order);

    }
    /**
     * 测评问题提交
     * @param array $data
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/4
     * Time: 15:44
     * USER:GCQ
     */
    public function create($data=[],$userId)
    {

//        $order_info = $this->model
//            ->where('user_id',$userId)
//            ->whereIn('status',[1,2])
//            ->find();
//        if($order_info)return res_data(0,'您有待测评的问题，请等待专家发送方案后再提交');

        //获取患者基本信息
        $userServices = new UserServices();
        $userInfo = $userServices
            ->where('id',$userId)
            ->find();

        if(!$userInfo)return res_data(0,'用户获取失败');
        $patientData=[
            'gender' =>$userInfo['patient_gender'], //L患者性别：[L1:男] [L2:女]
            'age' =>$userInfo['patient_age'],  //M患者年龄
            'education' =>$userInfo['patient_education'],  //N教育程度 [N1:未上过学/不识字][N2:小学][N3:初中][N:高中/中专][N5:本科及以上]
            'disease_type' =>$userInfo['patient_disease_type'], //O患者疾病类型：[O1:阿尔茨海默病][O2:血管性痴呆][O3:混合性痴呆][O4:其他]
            'illness' =>$userInfo['patient_illness'],  //P患者病情严重程度：[P1:轻度] [P2:中度] [P3:重度]
            'hobby' =>$userInfo['patient_hobby'], //Q患者确诊前的兴趣爱好（可多选）：[Q1:无][Q2:唱歌/唱戏/听音乐/听戏/演奏乐器][Q3:跳舞/健美操/八段锦/打太极/练气功][Q4:散步/慢跑/爬山/打球/游泳/旅游][Q5:绘画/书法/写作/阅读][Q6:养花草植物][Q7:养宠物][Q8:其他（请列出________）]
            'hobby_content' =>$userInfo['patient_hobby_content'], //患者兴趣爱好8 自定义爱好
            'walk' =>$userInfo['patient_walk'], //R患者行走能力：[R1:可以正常行走][R2:自行使用拐杖、助步器、轮椅][R3:使用轮椅且需帮助][R4:完全卧床]
            'status' => 1,
            'is_del' => 1,
            'is_join_research'=>2,


        ];

        //问题选项
        //判断提交的问题是否完整
        // $is_aus_question = $this->is_aus_question($data);
        // if($is_aus_question !=1)return res_data(0,'请先完善问题');
        // foreach ($data as $item){
        //     if(!$item)return res_data(0,'请先完善问题');
        // }

        $orderEvalustionServices = new OrderEvalustionServices();
        Db::startTrans();
        try {

            $patientData['user_id'] = $userId;
            $patientData['create_time'] = time();

            $configModel = new Config();
            $key ='carePlan';
            if (!in_array($key,$configModel->keys)){
                $carePlan_type = 1;
            }else{
                $result = $configModel->where('key',$key)->value('value');
                $carePlan_type = $result;
            }

            $key ='sendTime';
            if (!in_array($key,$configModel->keys)){
                $sendTime = '9:00';
            }else{
                $result = $configModel->where('key',$key)->value('value');
                $sendTime = $result;
            }

            /*
            $stime = strtotime($sendTime)+86400;
            if($patientData['create_time']){
                $stime = strtotime($sendTime,$patientData['create_time'])+86400;
            }
            */ 

            $stime = $patientData['create_time'];
            if ($sendTime != null && $sendTime != '') {
                $stime = strtotime($sendTime,$patientData['create_time'])+86400;
            }


            //添加问题
            if($carePlan_type==1){
                //专家审核后发送
                $patientData['status'] = 1;
                $patientData['confirm_send_time'] = $stime;
                $patientData['evaluate_start_time'] = $patientData['confirm_send_time']+7*86400;
            }else{
                //立即发送
                $patientData['status'] = 3;
                $patientData['confirm_send_time'] =  $patientData['create_time'];
                $patientData['evaluate_start_time'] = $patientData['confirm_send_time']+7*86400;
            }
            $id = $this->model->insertGetId($patientData);
            $questionData =[];
            foreach ($data as $k=>$it){
                $questionData[$k]['order_id'] = $id;
                $questionData[$k]['classify_id']= $it['evaluation_class_id'];
                $questionData[$k]['capability_id']= $it['id'];
                $questionData[$k]['option_id']= json_encode($it['options']);
                $questionData[$k]['option_content']= trim($it['option_content']);
            }
            $insert_res = $orderEvalustionServices->model->insertAll($questionData);
            Db::commit();
        }catch (\Throwable $e){
            Db::rollback();
            return  res_data(0,'添加失败'.$e->getMessage());
            // return  res_data(0,'添加失败'.$e->getMessage());
        }

        //请求照护方案
        //患者
        $basicConfigServices = new BasicConfigServices;

        $patientList = [];
        //年龄
        array_push($patientList,['id'=> 'M', "content"=>$userInfo['patient_age']]);
        if(array_key_exists($userInfo['patient_gender'],$basicConfigServices->patient_gender)){
            array_push($patientList,['id'=> $userInfo['patient_gender'], "content"=>$basicConfigServices->patient_gender[$userInfo['patient_gender']]]);
        }

        if(array_key_exists($userInfo['patient_education'],$basicConfigServices->patient_education)){
            array_push($patientList,['id'=> $userInfo['patient_education'], "content"=>$basicConfigServices->patient_education[$userInfo['patient_education']]]);
        }
        //
        if(array_key_exists($userInfo['patient_disease_type'],$basicConfigServices->patient_disease_type)){
            array_push($patientList,['id'=> $userInfo['patient_disease_type'], "content"=>$basicConfigServices->patient_disease_type[$userInfo['patient_disease_type']]]);
        }
        if(array_key_exists( $userInfo['patient_illness'],$basicConfigServices->patient_illness)){
            array_push($patientList,['id'=> $userInfo['patient_illness'], "content"=>$basicConfigServices->patient_illness[$userInfo['patient_illness']]]);
        }


        if(array_key_exists( $userInfo['patient_walk'],$basicConfigServices->patient_walk)){
            array_push($patientList,['id'=> $userInfo['patient_walk'], "content"=>$basicConfigServices->patient_walk[$userInfo['patient_walk']]]);
        }

        $patient_hobby = json_decode($userInfo['patient_hobby'],true);
        if($patient_hobby){
            foreach ($patient_hobby as $v){
                if(array_key_exists($v, $basicConfigServices->patient_hobby)){
                    array_push($patientList,['id'=>$v,'content'=>$basicConfigServices->patient_hobby[$v]]);
                }

            }
        }
        //问题
        $user_problem_list = [];
        $user_environment = [];
        $EvaluationCapabilityOptions = new EvaluationCapabilityOptions();
        $EvaluationCapabilityOptionsList = $EvaluationCapabilityOptions->select();
        // $optionsarr =  array_column($data,'options');

        $EvaluationCapabilityServices = new EvaluationCapabilityServices();
        $EvaluationCapabilityList= $EvaluationCapabilityServices->model->select();

        foreach ($data as $problem){

            if($problem['evaluation_class_id']==1 || $problem['evaluation_class_id']==2 || $problem['evaluation_class_id']==3){

                // $EvaluationCapabilityList = $EvaluationCapabilityOptions->whereIn('id',$problem['options'])->select();
                if($EvaluationCapabilityOptionsList){
                    foreach ($EvaluationCapabilityOptionsList as $e) {
                        if(is_array($problem['options'])){
                            if(in_array($e['id'],$problem['options'])){
                                array_push($user_problem_list, ['id' => $e['sn'], 'content' => $e['name']]);
                            }
                        }


                    }
                }


            }
            if($problem['evaluation_class_id']==4){
                // $EvaluationCapabilityInfo = $EvaluationCapabilityServices->model->where('id',$problem['id'])->find();
                if($EvaluationCapabilityList){
                    foreach ($EvaluationCapabilityList as $ecL){
                        if($ecL['id'] == $problem['id']){
                            // $EvaluationCapabilityOptionsInfo = $EvaluationCapabilityOptions->where('id',$problem['options'][0])->find();
                            if($EvaluationCapabilityOptionsList){
                                foreach ($EvaluationCapabilityOptionsList as $ecol){
                                    if(is_array($problem['options'])){
                                        if($ecol['id'] == $problem['options'][0]){
                                            array_push($user_environment, ['id' => $ecL['sn'], 'answer' => $ecol['sn'], 'content' => $ecL['name']]);
                                        }
                                    }
                                }
                            }
                        }

                    }

                }
            }
        }

        $patient['user_feature_list'] = $patientList;
        $patient['user_environment'] = $user_environment;
        $patient['user_problem_list'] = $user_problem_list;


        $postjson = json_encode($patient);
        $headers = array();

        array_push($headers, "Content-Type: text/plain");
        // dump($postjson);


        $res = curl('http://www.nurseadvicetest.xyz:9898/gen',$postjson,1,$headers);





        $configModel = new Config();
        $key ='expertAvatar';
        if (!in_array($key,$configModel->keys)){
            $expert_avatar = '';
        }else{
            $result = $configModel->where('key',$key)->value('value');
            $expert_avatar = $result;
        }
        $key ='carePlan';
        if (!in_array($key,$configModel->keys)){
            $careplan = '1';
        }else{
            $result = $configModel->where('key',$key)->value('value');
            $careplan = $result;
        }
        $key ='sendTime';
        if (!in_array($key,$configModel->keys)){
            $sendtime = '9:00';
        }else{
            $result = $configModel->where('key',$key)->value('value');
            $sendtime = $result;
        }
        $res_data = [
            'careplan' => $careplan,
            'expert_avatar' => $expert_avatar,
            'sendtime' => $sendtime,
            'id' => $id,
        ];



        if($res !== false) {


            if ($res['code'] == 200) {

                //工单测评问题发送记录日志
                $OrderProgramLog = new OrderProgramLog();
                $orderEvalustionLogData = [
                    'order_id' => $id,  //工单id
                    'user_id' => $userId,
                    'post_parameter' => $postjson,
                    'is_send' => 1,
                    'log' => $res['data'],
                    'send_time' => time(),
                ];
                $OrderProgramLog->insertGetId($orderEvalustionLogData);
                $OrderProgram = new OrderProgram();
                $careInfo = json_decode($res['data'], true);

                // dump($careInfo);
                Db::startTrans();
                try {

                    //日常活动安排
                    if ($careInfo['activity_advice']) {
                        $activity_data = [];
                        foreach ($careInfo['activity_advice'] as $activity_advice) {

                            $activity_data[] = [
                                'order_id' => $id,
                                'advice' => 'activity_advice',
                                'sn' => $activity_advice['id'],
                                'content' => json_encode($activity_advice['content']),
                                // 'pic_pos' =>json_encode($activity_advice['pic_pos']),
                                // 'pic_urls' => json_encode($activity_advice['pic_urls']),
                                'create_time' => time(),
                                'user_id' => $userId,
                            ];


                        }
                        $OrderProgram->insertAll($activity_data);
                    }
                    //环境安排
                    if ($careInfo['environment_advice']) {
                        $environment_data = [];
                        foreach ($careInfo['environment_advice'] as $environment_advice) {

                            $environment_data[] = [
                                'order_id' => $id,
                                'advice' => 'environment_advice',
                                'sn' => $environment_advice['id'],
                                'content' => json_encode($environment_advice['content']),
                                // 'pic_pos' =>json_encode($environment_advice['pic_pos']),
                                // 'pic_urls' => json_encode($environment_advice['pic_urls']),
                                'create_time' => time(),
                                'user_id' => $userId,
                            ];

                        }
                        $OrderProgram->insertAll($environment_data);
                    }
                    //照护问题的照护建议
                    if ($careInfo['problem_advice']) {
                        foreach ($careInfo['problem_advice'] as $problem_advice) {
                            $problem_data = [];
                            $problem_data = [
                                'order_id' => $id,
                                'advice' => 'problem_advice',
                                'sn' => $problem_advice['pid'],
                                'content' => json_encode($problem_advice['pcontent']),
                                // 'pic_pos' =>'',
                                // 'pic_urls' =>'',
                                'create_time' => time(),
                                'user_id' => $userId,

                            ];
                            $orderProgram_id = $OrderProgram->insertGetId($problem_data);
                            if ($orderProgram_id) {
                                $advices_data = [];
                                foreach ($problem_advice['advices'] as $advices) {

                                    $advices_data[] = [
                                        'order_id' => $id,
                                        'advice' => 'problem_advice',
                                        'sn' => $advices['id'],
                                        'content' => json_encode($advices['content']),
                                        // 'pic_pos' =>json_encode($advices['pic_pos']),
                                        // 'pic_urls' => json_encode($advices['pic_urls']),
                                        'pid' => $orderProgram_id,
                                        'create_time' => time(),
                                        'user_id' => $userId,

                                    ];

                                }
                                $OrderProgram->insertAll($advices_data);
                            }
                        }
                    }
                    $this->model->where('id', $id)->update(['is_send' => 1]);
                    Db::commit();


                    return res_data(1, '提交成功', $res_data);
                } catch (\Throwable $e) {
                    Db::rollback();
                    $this->model->where('id', $id)->update(['is_send' => 2]);
                    return res_data(0, '添加失败' . $e->getMessage());
                }

            } else {

                //工单测评问题发送记录日志
                $OrderProgramLog = new OrderProgramLog();
                $orderEvalustionLogData = [
                    'order_id' => $id,  //工单id
                    'user_id' => $userId,
                    'post_parameter' => $postjson,
                    'is_send' => 2,
                    'log' => $res['data'],
                    'send_time' => time(),
                ];
                if (!$orderEvalustionLogData['log']) {
                    $orderEvalustionLogData['log'] = '未获取到数据';
                }
                $OrderProgramLog->insertGetId($orderEvalustionLogData);
                return res_data(1, '提交成功', $res_data);
            }
        }else{
            //工单测评问题发送记录日志
            $OrderProgramLog = new OrderProgramLog();
            $orderEvalustionLogData = [
                'order_id' => $id,  //工单id
                'user_id' => $userId,
                'post_parameter' => $postjson,
                'is_send' => 2,
                'log' => $res,
                'send_time' => time(),
            ];
            if (!$orderEvalustionLogData['log']) {
                $orderEvalustionLogData['log'] = '未获取到数据';
            }
            $OrderProgramLog->insertGetId($orderEvalustionLogData);
            return res_data(1, '提交成功', $res_data);
        }

    }

    /**
     * 判断提交的问题是否完整
     * @param $data
     * @return int
     * @throws \think\Exception
     * Date: 2022/7/28
     * Time: 12:10
     * USER:GCQ
     */
    protected function is_aus_question($data)
    {
        //判断问题完整性
        $evaluationCapabilityServices =  new EvaluationCapabilityServices();
        $evaluationCapabilityCount = $evaluationCapabilityServices->model
            ->where('status',1)
            ->where('is_del',1)
            ->field('id')
            ->count('id');
        if($evaluationCapabilityCount==count($data)){
            return 1;
        }else{
            return 0;
        }

    }

    /**
     * 关爱研究调查提交
     * @param $orderId
     * @param $data
     * @param $userId
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/9
     * Time: 15:03
     * USER:GCQ
     */
    public function research_create($orderId,$data,$userId)
    {
        $orderInfo = $this->model->where('id',$orderId)->where('user_id',$userId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        if($orderInfo['is_join_research']==1)return res_data(0,'您已经调研,请勿重复提交');
        $orderResearchFamily = new OrderResearchFamily();
        $orderResearchCount =  $orderResearchFamily->where('order_id',$orderId)->count('id');
        if($orderResearchCount>0)return res_data(0,'您已经调研,请勿重复提交');
        $orderResearchScenes = new OrderResearchScenes();
        $orderResearchScenesCount =  $orderResearchScenes->where('order_id',$orderId)->count('id');
        if($orderResearchScenesCount>0) return res_data(0,'您已经调研,请勿重复提交');

        //调研问题
        $familyRelationData = [];
        foreach ($data['family_relation'] as $k=>$v){
            $familyRelationData[$k]['order_id'] = $orderId;
            $familyRelationData[$k]['family_relation_id'] = $v['question_id'];
            $familyRelationData[$k]['family_relation_option_id'] = $v['option_id'];
            $familyRelationData[$k]['create_time'] = time();
        }
        if(!$familyRelationData)return res_data(0,'提交的问题格式错误');
        //场景
        $scenesData['order_id'] = $orderId;
        $scenesData['scenes_one'] = $data['scenes_one'];
        $scenesData['scenes_two'] = $data['scenes_two'];
        $scenesData['scenes_three'] = $data['scenes_three'];
        $scenesData['scenes_four'] = $data['scenes_four'];
        $scenesData['scenes_five'] = $data['scenes_five'];
        $scenesData['scenes_one_time'] = $data['scenes_one_time'];
        $scenesData['scenes_two_time'] = $data['scenes_two_time'];
        $scenesData['scenes_three_time'] = $data['scenes_three_time'];
        $scenesData['scenes_four_time'] = $data['scenes_four_time'];
        $scenesData['scenes_five_time'] = $data['scenes_five_time'];
        $orderResearchFamily = new OrderResearchFamily();
        $orderResearchScenes = new OrderResearchScenes();

        Db::startTrans();
        try {

            $orderResearchFamily->insertAll($familyRelationData);

            $orderResearchScenes->insert($scenesData);
            //修改是否关爱调研状态
            $this->model->where('id',$orderId)->where('user_id',$userId)->update(['is_join_research'=>1]);
            Db::commit();
            return  res_data(1,'添加成功');
        }catch (\Throwable $e){
            Db::rollback();
            return  res_data(0,'添加失败');
        }



    }

    /**
     * 判断是否可以提交测评问题
     * @param $userId
     * @return array
     * @throws \think\Exception
     * Date: 2022/8/9
     * Time: 15:08
     * USER:GCQ
     */
    public function is_pending($userId)
    {
        $orderCount = $this->model->
            where('user_id',$userId)
            ->where('status',1)
            ->count('id');
        if($orderCount>0){
            return res_data(1,'请求成功',['tips'=>'您有待审核的工单，请等待审核完成再提交新工单','status'=>true]);
        }else{
            return res_data(1,'请求成功',['status'=>false]);
        }
    }

    /**
     * 首页获取最新条照护方案
     * Date: 2022/8/10
     * Time: 19:00
     * USER:GCQ
     */
    public function get_program_new_one_info($userId)
    {
        $orderInfo = $this->model
            ->where('user_id',$userId)
            ->where('status',3)
            ->order('create_time','desc')
            ->field('id')
            ->find();
        if($orderInfo){
            return res_data(1,'请求成功',$orderInfo);
        }else{
            return res_data(0,'未查询到已发送的照护方案');
        }
    }





    //admin后台
    /**
     * 测评问题详情患者工单列表
     * @param $where
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/6
     * Time: 9:44
     * USER:GCQ
     */
    public function admin_evaluation_list($where,$user_id,$role_id)
    {

        [$page,$limit] = $this->getPageValue();
        $list = $this->admin_evaluation_search($where,$user_id,$role_id)
            ->order('create_time','desc')
            ->page($page,$limit)
            ->select();
        $count = 0;
        if($list){
            $count = $this->admin_evaluation_search($where,$user_id,$role_id)->count('id');
            $basicConfigServices = new BasicConfigServices();
            foreach ($list as &$item){
                //是否参加调研
                $item['is_join_research_name'] = '';
                if (array_key_exists($item['is_join_research'], $this->join_research_status)) {
                    $item['is_join_research_name'] = $this->join_research_status[$item['is_join_research']];
                }
                //测评状态
                $item['status_name'] = '';

                if (array_key_exists($item['status'], $this->status)) {
                    $item['status_name'] = $this->status[$item['status']];
                }
                if($item['status']==3){
                    $status = 4;
                    if($item['is_evaluate']==2){
                        if (array_key_exists($status = 4, $this->status)) {
                            $item['status_name'] = $this->status[$status = 4];
                        }
                    }

                }

                if($item['is_evaluate']==1 ){
                    $status = 5;
                    if (array_key_exists($status = 5, $this->status)) {
                            $item['status_name'] = $this->status[$status = 5];
                    }
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
        $beadHouseServices = new BeadHouseServices();
        $beadHouseinfo = $beadHouseServices->model->where('sysetm_manager_id',$user_id)->find();
        if($beadHouseinfo){
            $bead_house_id = $beadHouseinfo['id'];
        }else{
            $bead_house_id = '';
        }
        return res_data(1,'请求成功',['list'=>$list,'count'=>$count,'bead_house_id'=>$bead_house_id]);
    }

    /**
     * 测评问题详情患者工单列表搜索
     * @param $where
     * @return \think\Model
     * Date: 2022/8/6
     * Time: 9:45
     * USER:GCQ
     */
    public function admin_evaluation_search($where,$user_id,$role_id)
    {
        $beadHouseServices = new BeadHouseServices();

        $model = $this->model->where('is_del',1);

        if($role_id == 11){

            $beadHouseinfo = $beadHouseServices->model->where('sysetm_manager_id',$user_id)->find();
            if($beadHouseinfo) {

                $userServices = new UserServices();
                $user_ids = $userServices->model->where('bead_house_id', $beadHouseinfo['id'])->field('id')->select();
                if ($user_ids) {
                    $ids = array_column($user_ids, 'id');
                    $model = $model->where('user_id', 'in', $ids);
                } else {
                    $model = $model->where('user_id', 'in', '');
                }

            }else{
                $model = $model->where('user_id', 'in', '');
            }
        }else{
            if (isset($where['bead_house_id']) && !empty($where['bead_house_id'])) {

                $userServices = new UserServices();
                $user_ids = $userServices->model->where('bead_house_id', $where['bead_house_id'])->field('id')->select();
                if ($user_ids) {

                    $ids = array_column($user_ids, 'id');
                    $model = $model->where('user_id', 'in', $ids);
                }else{
                    $model = $model->where('user_id', 'in', '');
                }
            }
        }

        //按性别
        if (isset($where['gender']) && !empty($where['gender'])){
            $model = $model->where('gender',$where['gender']);
        }
        //教育程度
        if (isset($where['education']) && !empty($where['education'])){
            $model = $model->where('education',$where['education']);
        }
        //患者疾病类型
        if (isset($where['disease_type']) && !empty($where['disease_type'])){
            $model = $model->where('disease_type',$where['disease_type']);
        }
        //患者病情严重程度
        if (isset($where['illness']) && !empty($where['illness'])){
            $model = $model->where('illness',$where['illness']);
        }
        //患者行走能力
        if (isset($where['walk']) && !empty($where['walk'])){
            $model = $model->where('walk',$where['walk']);
        }
        //兴趣爱好
        if (isset($where['hobby']) && !empty($where['hobby'])){
            $basicConfigServices = new BasicConfigServices();

            $keyword = $where['hobby'];
            $model = $model->where(function ($query) use($keyword,$basicConfigServices) {
                $retrun = array();
                foreach($basicConfigServices->patient_hobby as $k=>$val){
                    // 如果$val中含有$keyword字符串，那么添加到数组$return中
                    if(stripos($val,$keyword) != false || stripos($val,$keyword) === 0)
                    {
                        array_push($retrun ,$k);
                    }
                }

                if($retrun){
                    foreach ($retrun as $item){
                        $query->whereOr('hobby','like','%'.$item.'%');
                    }
                }
                $query->whereOr('hobby_content|id','like','%'.$keyword.'%');

            });
                // 'hobby|hobby_content','like','%'.$where['hobby'].'%');
        }

        //0查询全部，1查询待出方案，2待发送方案，3已发送方案，4待评价, 5 已评价

        if (isset($where['status']) && !empty($where['status'])){
            if($where['status'] == 1 || $where['status'] == 2){
                $model = $model->where('status',$where['status']);
            }else{
                if($where['status'] == 3){
                    $model = $model->where('status',$where['status']);
                }
                if($where['status'] == 4){
                    $model = $model->where('is_evaluate',2)->where('status',3);
                }
                //
                if($where['status'] == 5){
                    $model = $model->where('is_evaluate',1)->where('status',3);
                }
            }




        }
        return $model;

    }
    public function search($array,$str){
        foreach($array as $key=>$val){
            $retrun = array(); // 如果$key中含有$str字符串，那么添加到数组$return中
            if(stripos($key,$str) != false || stripos($key,$str) === 0)
            {
                array_push($retrun ,$key);
            }
        }
        return $retrun;
    }
    /**
     * 测评问题详情工单患者详情
     * @param $orderId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/6
     * Time: 10:29
     * USER:GCQ
     */
    public function admin_order_details($orderId)
    {
        $orderInfo = $this->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        if($orderInfo){
            $basicConfigServices = new BasicConfigServices();

            //患者
            //性别
            if(array_key_exists($orderInfo['gender'],$basicConfigServices->patient_gender)){
                $orderInfo['gender_name'] = $basicConfigServices->patient_gender[$orderInfo['gender']];
            }else{
                $orderInfo['gender_name'] = '';
            }
            //教育
            if(array_key_exists($orderInfo['education'],$basicConfigServices->patient_education)){
                $orderInfo['education_name'] = $basicConfigServices->patient_education[$orderInfo['education']];
            }else{
                $orderInfo['education_name'] = '';
            }
            //患者疾病类型
            if(array_key_exists($orderInfo['disease_type'],$basicConfigServices->patient_disease_type)){
                $orderInfo['disease_type_name'] = $basicConfigServices->patient_disease_type[$orderInfo['disease_type']];
            }else{
                $orderInfo['disease_type_name'] = '';
            }
            //P患者病情严重程度
            if(array_key_exists($orderInfo['illness'],$basicConfigServices->patient_illness)){
                $orderInfo['illness_name'] = $basicConfigServices->patient_illness[$orderInfo['illness']];
            }else{
                $orderInfo['illness_name'] = '';
            }
            //患者确诊前的兴趣爱好
            if($orderInfo['hobby']){
                $orderInfo['hobby'] = json_decode($orderInfo['hobby'],true);
                $hobby_str = '';
                if($orderInfo['hobby']){
                    foreach ($orderInfo['hobby'] as $item){
                        if(array_key_exists($item,$basicConfigServices->patient_hobby)){

                            if($item == 'Q8'){
                                $hobby_str .= $basicConfigServices->patient_hobby[$item].'('.$orderInfo['hobby_content'].')';
                            }else{
                                $hobby_str .= $basicConfigServices->patient_hobby[$item];
                            }

                        }else{
                            $hobby_str .= '';
                        }
                    }
                }
                $orderInfo['hobby_name'] = $hobby_str;
            }else{
                $orderInfo['hobby_name'] = '';
            }
            //R患者行走能力
            if(array_key_exists($orderInfo['walk'],$basicConfigServices->patient_walk)){
                $orderInfo['walk_name'] = $basicConfigServices->patient_walk[$orderInfo['walk']];
            }else{
                $orderInfo['walk_name'] = '';
            }
            $orderInfo['create_time'] = date('Y-m-d H:i:s',$orderInfo['create_time']);
            $orderInfo['confirm_send_time'] = date('Y-m-d H:i:s',$orderInfo['confirm_send_time']);
        }else{
            $orderInfo = [];
        }
        return res_data(1,'请求成功',$orderInfo);


    }

    /**
     * 测评问题详情患者工单问题详情
     * @param $orderId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/6
     * Time: 11:27
     * USER:GCQ
     */
    public function admin_order_evaluationdetails($orderId)
    {
        $orderInfo = $this->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        $evaluationClassServices = new EvaluationClassServices();
        $class = $evaluationClassServices->model->where('is_del',1)->field('id,name,content')->select();
        if(!$class)return res_data(0,'未获取到分类');
        $orderEvalustionServices = new OrderEvalustionServices();
        $evaluationCapabilityServices = new EvaluationCapabilityServices();
        $evaluationCapabilityOptions =  new EvaluationCapabilityOptions();

        $orderProgramModel =  new OrderProgram();

        $userEvaluateModel = new UserEvaluate();
        $basicConfigServices = new BasicConfigServices();
        $userEvaluateList = [];
        // if($orderInfo['is_evaluate']==2){
        //     $userEvaluateList = $userEvaluateModel->where('order_id',$orderId)->select();
        // }

        //分类
        foreach ($class as &$item){
            $item['capability'] = [];
            $orderEvalustion = $orderEvalustionServices->model
                ->where('order_id',$orderId)
                ->where('classify_id',$item['id'])
                ->select();
            //工单问答测评问题和选项
            if($orderEvalustion){

                foreach ($orderEvalustion as &$itemitem){
                    //测评问题 选择的问题
                    $evaluationCapability = $evaluationCapabilityServices->model
                        ->where('id',$itemitem['capability_id'])
                        ->field('id,sn,name')
                        ->select();
                    $itemitem['capability'] =$evaluationCapability;

                    $itemitem['options'] = [];
                    $option = json_decode($itemitem['option_id'],true);
                    if($option){
                        //测评选项 选择的选项
                        $itemitem['options'] = $evaluationCapabilityOptions->whereIn('id',$option)->field('id,sn,name')->select();
                        //照护方案


                        //选择的选项查找照护方案
                        foreach($itemitem['options'] as $optionitem){
                            $optionitem['program_child'] =[];
                            // $optionitem['program'] =[];
                            $orderProgramInfo  = $orderProgramModel
                                ->where('order_id',$orderId)
                                ->where('sn',$optionitem['sn'])
                                ->where('pid',0)
                                ->field('id,order_id,sn,advice,content,pid')
                                ->find();
                            if($orderProgramInfo){
                                // $userEvaluate= $userEvaluateModel->where('order_id', $orderId)->where('program_question_id', $orderProgramInfo['id'])->find();
                                // $orderProgramInfo['evaluate'] = '';
                                // if ($userEvaluate) {
                                //     if (array_key_exists($userEvaluate['program_option_id'], $basicConfigServices->care_options)) {
                                //         $orderProgramInfo['evaluate'] = $basicConfigServices->care_options[$userEvaluate['program_option_id']];
                                //     }
                                // }
                                    $orderProgramInfo['content'] = json_decode($orderProgramInfo['content'],true);
                                    // $optionitem['program'] = $orderProgramInfo;
                                    // $optionitem['program_child'] =[];
                                    $orderProgramChild = $orderProgramModel
                                        ->where('order_id',$orderId)
                                        ->where('pid',$orderProgramInfo['id'])
                                        ->field('id,order_id,sn,advice,content')
                                        ->select();
                                    if($orderProgramChild){

                                        foreach ($orderProgramChild as $chiletiem){

                                            $userEvaluateInfo = $userEvaluateModel->where('order_id', $orderId)->where('program_question_id', $chiletiem['id'])->find();
                                            $chiletiem['evaluate'] = '';
                                            if ($userEvaluateInfo) {
                                                if (array_key_exists($userEvaluateInfo['program_option_id'], $basicConfigServices->care_options)) {
                                                    $chiletiem['evaluate'] = $basicConfigServices->care_options[$userEvaluateInfo['program_option_id']];
                                                }
                                            }

                                            $chiletiem['content'] = json_decode($chiletiem['content'],true);
                                        }
                                        $optionitem['program_child'] = $orderProgramChild;
                                    }
                            }


                        }


                    }


                }
                $item['capability'] = $orderEvalustion;
            }

        }

        //是否评价

        return res_data(1,'请求成功',$class);

    }

    /**
     * 照护方案详情
     * @param $orderId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/6
     * Time: 14:04
     * USER:GCQ
     */
    public function admin_order_program_details($orderId)
    {
        $orderInfo = $this->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        $orderProgramLogModel = new OrderProgramLog();
        $log = [];
        if($orderInfo['is_send']==2){
            $orderProgramLogInfo = $orderProgramLogModel->where('order_id',$orderId)->order('send_time','desc')->find();
            if($orderProgramLogInfo){
                $orderProgramLogInfo['post_parameter'] = json_decode($orderProgramLogInfo['post_parameter']);
                if($orderProgramLogInfo['is_send']==2){
                }else{
                    $orderProgramLogInfo['log'] = json_decode($orderProgramLogInfo['log'],true);
                }

                $log = $orderProgramLogInfo;
            }
        }
        if($orderInfo['is_send']==2)return res_data(1,'请求成功',$log);
        $orderProgramModel = new OrderProgram();
        $program_class = [
            ['id' => 1, 'program_class' => 'environment_advice', 'program_class_name' => '一、居住环境安排'],
            ['id' => 2, 'program_class' => 'activity_advice', 'program_class_name' => '二、日常生活安排'],
            ['id' => 3, 'program_class' => 'problem_advice', 'program_class_name' => '三、具体照顾方案照护问题建议'],
        ];

        foreach ($program_class as &$classitem){
            $classitem['program_care'] = [];
            $orderProgram = $orderProgramModel
                ->where('order_id',$orderId)
                ->where('advice',$classitem['program_class'])
                ->where('pid',0)
                ->where('is_del',1)
                ->field('is_del',true)
                ->select();
            if($orderProgram){
                foreach ($orderProgram as &$orderProgramitem){

                    if($orderProgramitem['advice'] == 'problem_advice'){
                        $orderProgramitem['advice_child'] = [];
                        $orderProgramchild = $orderProgramModel
                            ->where('order_id',$orderId)
                            ->where('pid',$orderProgramitem['id'])
                            ->where('is_del',1)
                            ->field('is_del',true)
                            ->select();

                        if($orderProgramchild){
                            foreach ($orderProgramchild as &$programchild){
                                $programchild['content'] = json_decode($programchild['content'],true);
                                $programchild['create_time'] = date('Y-m-d H:i:s',$programchild['create_time']);
                            }
                        }
                        $orderProgramitem['advice_child'] = $orderProgramchild;
                        $orderProgramitem['content'] = json_decode($orderProgramitem['content'],true);
                    }else{
                        $orderProgramitem['content'] = json_decode($orderProgramitem['content'],true);
                        $orderProgramitem['create_time'] = date('Y-m-d H:i:s',$orderProgramitem['create_time']);
                    }




                }
                $classitem['program_care'] = $orderProgram;
            }
        }
        return res_data(1,'请求成功',$program_class);
    }

    /**
     * 照护方案发送失败重新发送
     * @param $orderId
     * @param $adminId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/6
     * Time: 17:06
     * USER:GCQ
     */
    public function admin_send_program_details($orderId,$adminId=1)
    {
        $orderInfo = $this->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        //测评问题
        $orderEvalustionServices = new  OrderEvalustionServices();
        $orderEvalustionList = $orderEvalustionServices->model->where('order_id',$orderId)->select();
        if(!$orderEvalustionList)return res_data(0,'该工单id还未提交测评问题');
        //照护方案
        $orderProgramModel = new OrderProgram();
        $orderProgram = $orderProgramModel->where('order_id',$orderId)->select();
        if($orderProgram)return res_data(0,'该工单id的方案已存在，请勿重新请求');

        //请求照护方案
        //患者
        $basicConfigServices = new BasicConfigServices;

        $patientList = [];
        //年龄
        array_push($patientList,['id'=> 'M', "content"=>$orderInfo['age']]);
        if(array_key_exists($orderInfo['gender'],$basicConfigServices->patient_gender)){
            array_push($patientList,['id'=> $orderInfo['gender'], "content"=>$basicConfigServices->patient_gender[$orderInfo['gender']]]);
        }

        if(array_key_exists($orderInfo['education'],$basicConfigServices->patient_education)){
            array_push($patientList,['id'=> $orderInfo['education'], "content"=>$basicConfigServices->patient_education[$orderInfo['education']]]);
        }
        //
        if(array_key_exists($orderInfo['disease_type'],$basicConfigServices->patient_disease_type)){
            array_push($patientList,['id'=> $orderInfo['disease_type'], "content"=>$basicConfigServices->patient_disease_type[$orderInfo['disease_type']]]);
        }
        if(array_key_exists($orderInfo['illness'],$basicConfigServices->patient_illness)){
            array_push($patientList,['id'=> $orderInfo['illness'], "content"=>$basicConfigServices->patient_illness[$orderInfo['illness']]]);
        }


        if(array_key_exists($orderInfo['walk'],$basicConfigServices->patient_walk)){
            array_push($patientList,['id'=> $orderInfo['walk'], "content"=>$basicConfigServices->patient_walk[$orderInfo['walk']]]);
        }

        $patient_hobby = json_decode($orderInfo['hobby'],true);
        if($patient_hobby){
            foreach ($patient_hobby as $v){
                if(array_key_exists($v, $basicConfigServices->patient_hobby)){
                    array_push($patientList,['id'=>$v,'content'=>$basicConfigServices->patient_hobby[$v]]);
                }

            }
        }
        //问题
        $user_problem_list = [];
        //环境
        $user_environment = [];
        $EvaluationCapabilityOptions = new EvaluationCapabilityOptions();
        $EvaluationCapabilityServices = new EvaluationCapabilityServices();
        foreach ($orderEvalustionList as $problem){
            $options = json_decode($problem['option_id'],true);
            if($options){
                if($problem['classify_id']==1 || $problem['classify_id']==2 || $problem['classify_id']==3){

                    $EvaluationCapabilityList = $EvaluationCapabilityOptions->whereIn('id',$options)->select();
                    if($EvaluationCapabilityList){
                        foreach ($EvaluationCapabilityList as $e) {
                            array_push($user_problem_list, ['id' => $e['sn'], 'content' => $e['name']]);
                        }
                    }


                }
                if($problem['classify_id']==4){

                    $EvaluationCapabilityInfo = $EvaluationCapabilityServices->where('id',$problem['capability_id'])->find();

                    if($EvaluationCapabilityInfo){
                        $EvaluationCapabilityOptionsInfo = $EvaluationCapabilityOptions->where('id',$options[0])->find();

                        if($EvaluationCapabilityOptionsInfo){
                            array_push($user_environment, ['id' => $EvaluationCapabilityInfo['sn'], 'answer' => $EvaluationCapabilityOptionsInfo['sn'], 'content' => $EvaluationCapabilityInfo['name']]);
                        }
                    }
                }
            }

        }

        $patient['user_feature_list'] = $patientList;
        $patient['user_environment'] = $user_environment;
        $patient['user_problem_list'] = $user_problem_list;
        $postjson = json_encode($patient);
        $headers = array();
        array_push($headers, "Content-Type: text/plain");
        // dump($postjson);
        $res = curl('http://www.nurseadvicetest.xyz:9898/gen',$postjson,1,$headers);

        if($res['code']==200){

            //工单测评问题发送记录日志
            $OrderProgramLog = new OrderProgramLog();
            $orderEvalustionLogData = [
                'order_id' => $orderId,  //工单id
                'admin_id' => $adminId,
                'post_parameter' => $postjson,
                'is_send' =>1,
                'log' =>$res['data'],
                'send_time' => time(),
            ];
            $OrderProgramLog->insertGetId($orderEvalustionLogData);
            $OrderProgram = new OrderProgram();
            $careInfo = json_decode($res['data'],true);

            // dump($careInfo);
            Db::startTrans();
            try {
                //日常活动安排
                if($careInfo['activity_advice']){
                    foreach ($careInfo['activity_advice'] as $activity_advice){
                        $activity_data=[];
                        $activity_data = [
                            'order_id' => $orderId,
                            'advice' =>'activity_advice',
                            'sn' =>$activity_advice['id'],
                            'content' =>json_encode($activity_advice['content']),
                            // 'pic_pos' =>json_encode($activity_advice['pic_pos']),
                            // 'pic_urls' => json_encode($activity_advice['pic_urls']),
                            'create_time' => time(),
                            'user_id' => $orderInfo['user_id'],
                        ];

                        $OrderProgram->insertGetId($activity_data);
                    }
                }
                //环境安排
                if($careInfo['environment_advice']){
                    foreach ($careInfo['environment_advice'] as $environment_advice){
                        $environment_data=[];
                        $environment_data = [
                            'order_id' => $orderId,
                            'advice' =>'environment_advice',
                            'sn' =>$environment_advice['id'],
                            'content' =>json_encode($environment_advice['content']),
                            // 'pic_pos' =>json_encode($environment_advice['pic_pos']),
                            // 'pic_urls' => json_encode($environment_advice['pic_urls']),
                            'create_time' => time(),
                            'user_id' => $orderInfo['user_id'],
                        ];
                        $OrderProgram->insertGetId($environment_data);
                    }
                }
                //照护问题的照护建议
                if($careInfo['problem_advice']){
                    foreach ($careInfo['problem_advice'] as $problem_advice){
                        $problem_data=[];
                        $problem_data = [
                            'order_id' => $orderId,
                            'advice' =>'problem_advice',
                            'sn' =>$problem_advice['pid'],
                            'content' =>json_encode($problem_advice['pcontent']),
                            // 'pic_pos' =>'',
                            // 'pic_urls' =>'',
                            'create_time' => time(),
                            'user_id' => $orderInfo['user_id'],

                        ];
                        $orderProgram_id = $OrderProgram->insertGetId($problem_data);
                        if($orderProgram_id){
                            foreach ($problem_advice['advices'] as $advices){
                                $advices_data=[];
                                $advices_data = [
                                    'order_id' => $orderId,
                                    'advice' =>'problem_advice',
                                    'sn' =>$advices['id'],
                                    'content' =>json_encode($advices['content']),
                                    // 'pic_pos' =>json_encode($advices['pic_pos']),
                                    // 'pic_urls' => json_encode($advices['pic_urls']),
                                    'pid' => $orderProgram_id,
                                    'create_time' => time(),
                                    'user_id' => $orderInfo['user_id'],

                                ];
                                $OrderProgram->insertGetId($advices_data);
                            }
                        }


                    }
                }
                $this->model->where('id',$orderId)->update(['is_send'=>1]);
                Db::commit();
                return res_data(1,'提交成功');
            }catch (\Throwable $e){
                Db::rollback();
                $this->model->where('id',$orderId)->update(['is_send'=>2]);
                return  res_data(0,'添加失败'.$e->getMessage());
            }
        }else{

            //工单测评问题发送记录日志
            $OrderProgramLog = new OrderProgramLog();
            $orderEvalustionLogData = [
                'order_id' => $orderId,  //工单id
                'admin_id' => $adminId,
                'post_parameter' => $postjson,
                'is_send' =>2,
                'log' =>$res['data'],
                'send_time' => time(),
            ];
            if(!$orderEvalustionLogData['log']){
                $orderEvalustionLogData['log'] = '未获取到数据';
            }
            $OrderProgramLog->insertGetId($orderEvalustionLogData);
            return res_data(0,'发送失败');
        }
    }

    /**
     * 专家确认方案
     * @param $orderId
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/7
     * Time: 11:47
     * USER:GCQ
     */
    public function admin_order_program_save($orderId,$data)
    {

        $WxMessageServices = new WxMessageServices();
        $orderInfo = $this->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');

        if($orderInfo['status'] == 2)return res_data(0,'该工单为待发送方案,请勿重新提交');
        if($orderInfo['status'] == 3)return res_data(0,'该工单为已发送方案,请勿重新提交');
        $orderEvalustionServices = new  OrderEvalustionServices();
        $orderEvalustionList = $orderEvalustionServices->model->where('order_id',$orderId)->select();
        if(!$orderEvalustionList)return res_data(0,'该工单id还未提交测评问题');
        //照护方案
        $orderProgramModel = new OrderProgram();
        $orderProgram = $orderProgramModel->where('order_id',$orderId)->select();
        if(!$orderProgram)return res_data(0,'该工单id未获取到照护方案，请先获取照护方案');

        $configModel = new Config();
        $key ='carePlan';
        if (!in_array($key,$configModel->keys)){
            $carePlan_type = 1;
        }else{
            $result = $configModel->where('key',$key)->value('value');
            $carePlan_type = $result;
        }

        $key ='sendTime';
        if (!in_array($key,$configModel->keys)){
            $sendTime = '9:00';
        }else{
            $result = $configModel->where('key',$key)->value('value');
            $sendTime = $result;
        }
        // $stime = strtotime($sendTime)+86400;
        // $stime = strtotime($sendTime,time())+86400;
        // if($orderInfo['create_time']){
        //     $stime = strtotime($sendTime,$orderInfo['create_time'])+86400;
        // }
        
        $stime = time();
        if ($sendTime != null && $sendTime != '') {
            $stime = strtotime($sendTime,time())+86400;
        }

        $updataDatePush = [];
        $orderProgramInfo = [];
        if($data){

            foreach ($data as $item){
                foreach ($item['program_care'] as $k=>$program_care){
                    //日常生活安排
                    $activity_adviceDate = [];
                    if($program_care['advice']=='activity_advice'){
                        $activity_adviceDate['id'] = $program_care['id'];
                        $activity_adviceDate['order_id'] = $program_care['order_id'];
                        $activity_adviceDate['sn'] = $program_care['sn'];
                        $activity_adviceDate['content'] = $program_care['content'];
                        $activity_adviceDate['pid'] = $program_care['pid'];
                        $orderProgramInfo ='';
                        $orderProgramInfo = $orderProgramModel
                            ->where('id',$program_care['id'])
                            ->where('order_id',$program_care['order_id'])
                            ->find();
                        if($orderProgramInfo){
                            $content = '';
                            $content = json_decode($orderProgramInfo['content'],true);
                            if($program_care['content']===$content){

                            }else{
                                array_push($updataDatePush,$activity_adviceDate);
                            }
                        }

                    }
                    //环境
                    $environment_adviceDate = [];
                    if($program_care['advice']=='environment_advice'){
                        $environment_adviceDate['id'] = $program_care['id'];
                        $environment_adviceDate['order_id'] = $program_care['order_id'];
                        $environment_adviceDate['sn'] = $program_care['sn'];
                        $environment_adviceDate['content'] = $program_care['content'];
                        $environment_adviceDate['pid'] = $program_care['pid'];
                        $orderProgramInfo ='';
                        $orderProgramInfo = $orderProgramModel
                            ->where('id',$program_care['id'])
                            ->where('order_id',$program_care['order_id'])
                            ->find();
                        if($orderProgramInfo){
                            $content = '';
                            $content = json_decode($orderProgramInfo['content'],true);
                            if($program_care['content']===$content){

                            }else{
                                array_push($updataDatePush,$environment_adviceDate);
                            }
                        }

                    }
                    //问题
                    $problem_adviceData = [];
                    if($program_care['advice']=='problem_advice'){
                        $problem_adviceData['id'] = $program_care['id'];
                        $problem_adviceData['order_id'] = $program_care['order_id'];
                        $problem_adviceData['sn'] = $program_care['sn'];
                        $problem_adviceData['content'] = $program_care['content'];
                        $problem_adviceData['pid'] = $program_care['pid'];
                        // array_push($updataDatePush,$problem_adviceData);
                        foreach ($program_care['advice_child'] as $advice_child_item){
                            $adviceChild = [];
                            $adviceChild['id'] = $advice_child_item['id'];
                            $adviceChild['order_id'] = $advice_child_item['order_id'];
                            $adviceChild['sn'] = $advice_child_item['sn'];
                            $adviceChild['content'] = $advice_child_item['content'];
                            $adviceChild['pid'] = $advice_child_item['pid'];
                            $orderProgramInfo ='';
                            $orderProgramInfo = $orderProgramModel
                                ->where('id',$advice_child_item['id'])
                                ->where('order_id',$advice_child_item['order_id'])
                                ->find();
                            if($orderProgramInfo){

                                $content = '';
                                $content = json_decode($orderProgramInfo['content'],true);
                                if($content ===$advice_child_item['content']){
                                    // 即相互都不存在差集，那么这两个数组就是相同的了，多数组也一样的道理

                                }else{
                                    array_push($updataDatePush,$adviceChild);
                                }

                            }

                        }
                    }

                }
            }
        }

        Db::startTrans();
        try {



            //修改保存照护方案
            foreach ($updataDatePush as $dataitem){
                $updateData =[];
                $updateData['content'] = json_encode($dataitem['content']);
                $updateData['is_update'] = 1;

                $orderProgramModel->where('id',$dataitem['id'])->where('order_id',$orderId)->update($updateData);
            }

            //需专家确认后发送  指定发送时间
            $order_update_date['status'] = 2;
            if ($sendTime == null || $sendTime == '') {
               $userServices = new UserServices();
               $user = $userServices->model->where('id',$orderInfo['user_id'])->field('id,openid')->find();
               $touser = $user['openid'];
               $template_id = 'cHvEcSyo84oy7o1iW60Ij09EfELCgBoLSIb6UvNDsLc';
               $page = '/pages/my/zhfa/zhfa?id=' . $orderInfo['id'];
               $data = [
                   'thing1' => ['value' => '失智老人照护方案'],
                   'thing4' => ['value' => '您的照护方案已生成，请前往小程序查看'],
               ];

               $res_message = $WxMessageServices->wx_message_new($touser,$template_id,$page,$data);
               $message_log_data = [
                   'order_id' => $orderInfo['id'],
                   'user_id' => $orderInfo['user_id'],
                   'title' => '健康管理方案通知',
                   'template_id' => $template_id,
                   'page' => $page,
                   'data' =>json_encode($data),
                   'log' =>json_encode($res_message)
               ];

               Db::name('wx_message_log')->insert($message_log_data);
               $order_update_date['status'] = 3;
            }
            $order_update_date['confirm_send_time'] = $stime;  //专家确认时间
            $order_update_date['evaluate_start_time'] =$stime+7*86400; //评价发送时间   专家确认发送时间加7天
            $this->model->where('id',$orderId)->update($order_update_date);


            Db::commit();
            return res_data(1,'提交成功');
        }catch (\Throwable $e){
            Db::rollback();
            return  res_data(0,'确认失败'.$e->getMessage());
        }
        //等于1 需专家确认后发送
    }

    public function admin_order_research_family($orderId)
    {
        $orderInfo = $this->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        if($orderInfo['is_join_research'] == 2)return res_data(0,'未参加关爱调研');
        $orderResearchFamilyModel = new OrderResearchFamily();
        $res = [];
        //家庭态度量总分
        $score = $orderResearchFamilyModel
            ->where('order_id',$orderId)
            ->sum('family_relation_option_id');
        $res['score'] = $score;
        //家庭态度量表
        $orderResearchFamilylist = $orderResearchFamilyModel
            ->where('order_id',$orderId)
            ->select();
        $familyRelationServices = new FamilyRelationServices();
        if($orderResearchFamilylist){
            foreach ($orderResearchFamilylist as &$item){
                $familyRelationInfo= $familyRelationServices->model->where('id',$item['family_relation_id'])->find();
                $item['family_relation_name'] = '';
                $item['family_relation_option_name'] = '';
                if($familyRelationInfo){
                    $item['family_relation_name'] = $familyRelationInfo['name'];
                    if(array_key_exists($item['family_relation_option_id'],$familyRelationServices->options)){
                        $item['family_relation_option_name'] = $familyRelationServices->options[$item['family_relation_option_id']];
                    }
                }

            }
            $res['family'] = $orderResearchFamilylist;
        }else{
            $res['family'] = [];
        }

        //语音
        $orderResearchScenesModel = new OrderResearchScenes();
        $orderResearchScenesinfo = $orderResearchScenesModel->where('order_id',$orderId)->find();
        if($orderResearchScenesinfo){
            $res['scenes'] = $orderResearchScenesinfo;
        }else{
            $res['scenes'] = [];

        }
        return res_data(0,'请求成功',$res);
    }

    /**
     * 请求照护方案  发送失败原因
     * @param $orderId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/15
     * Time: 16:13
     * USER:GCQ
     */
    public function admin_order_program_send_error($orderId)
    {
        $orderInfo = $this->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'未找到该工单');
        $orderProgramLogModel = new OrderProgramLog();
        $log = [];
        if($orderInfo['is_send']==2){
            $orderProgramLogInfo = $orderProgramLogModel->where('order_id',$orderId)->order('send_time','desc')->find();
            if($orderProgramLogInfo){
                $orderProgramLogInfo['post_parameter'] = json_decode($orderProgramLogInfo['post_parameter']);
                $orderProgramLogInfo['log'] = json_decode($orderProgramLogInfo['log']);
                $log = $orderProgramLogInfo;
            }
        }

        return res_data(1,'请求成功',$log);
    }
}
