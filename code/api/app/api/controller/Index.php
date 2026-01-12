<?php

namespace app\api\controller;

use app\common\model\Area;
use app\common\model\City;
use app\common\model\Config as ConfigModel;
use app\common\services\order\OrderServices;
use app\common\services\user\UserServices;
use app\common\services\basicconfig\BasicConfigServices;
use app\common\model\EvaluationCapabilityOptions;
use app\common\services\evaluationcapability\EvaluationCapabilityServices;
use app\common\services\order\OrderEvalustionServices;
use app\common\model\OrderProgram;

use OSS\OssClient;
use think\Cache;

class Index extends Basic
{

    public function index()
    {
        echo 'API';
    }

    /**
     * 上传图片
     * @author jihaichuan
     */
    public function upload()
    {
        $file = request()->file('file');

        try {
            $ossPath = 'upload'.'/'.date('Y').'/'.date('m').'/';
            $newFileName = md5($ossPath).date('YmdHis') . rand(0, 9999).strchr($file->getInfo()['name'],'.');
            $oss = new OssClient(config('alioss.accessKeyId'),config('alioss.accessKeySecret'),config('alioss.endpoint'));
            $ossInfo = $oss->uploadFile(config('alioss.oss_bucket'),$ossPath.$newFileName,$file->getRealPath());
        }catch (\Throwable $e){
            json_fail('上传失败');
        }

        $url = $ossInfo['oss-request-url'];
        $ContentSecurityServices = new ContentSecurityServices();
        $url = $ContentSecurityServices->imageFilter($url);

        json_success(['url' => $url]);
    }

    /**
     * 文本信息（关于我们...）
     * @return void
     */
    public function text() {
        $key = $this->request->get('key');//协议ID
        if (!$key) json_fail('缺少key参数');
        $config = new ConfigModel();
        //所传ID是否在已有数组中
        if (!in_array($key,$config->keys)){
            json_fail('key无效');
        }
        $result = $config->where('key',$key)->value('value');
        json_success(['content' => $result]);
    }

    /**
     * 省
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function province() {
        $countryId = $this->request->post('country_id/d',0);
        if (!$countryId)json_fail('缺少国家id参数');
        if ($countryId != 1)json_success([]);
        $Province = new Province();//省
        $list = $Province->order('id','asc')->field('id,province_name as name')->select();
        json_success($list);
    }

    /**
     * 市
     * @return void
     */
    public function city() {
        $provinceId = $this->request->post('province_id/d',0);
        if (!$provinceId)json_fail('缺少省id参数');
        $City = new City();//市
        $list = $City->where('province_id',$provinceId)->order('id','asc')->field('id,city_name')->select();
        json_success($list);
    }

    /**
     * 区
     * @return void
     */
    public function area() {
        $cityId = $this->request->post('city_id/d',0);
        if (!$cityId)json_fail('缺少市id参数');
        $Area = new Area();//区
        $list = $Area->where('city_id',$cityId)->order('id','asc')->field('id,area_name')->select();
        json_success($list);
    }

    public function send_program()
    {
        $orderId = $this->request->post('order_id/d',0);
        if (!$orderId)json_fail('缺少order_id参数');
        $question = [
            //
            ['id'=>1,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[3,6]],
            ['id'=>2,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[12,13]],
            ['id'=>3,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[18,19]],
            ['id'=>4,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[24,25]],
            ['id'=>5,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[29]],
            ['id'=>6,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[32,33]],
            ['id'=>7,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[35,37]],
            //
            ['id'=>8,'evaluation_class_id'=>2,'type'=>2,'option_content'=>'','options'=>[40,42,43]],
            //
            ['id'=>9,'evaluation_class_id'=>3,'type'=>2,'option_content'=>'','options'=>[59,60,61,62]],
            //
            ['id'=>10,'evaluation_class_id'=>4,'type'=>1,'option_content'=>'dsfasf','options'=>[112]],
            ['id'=>11,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[69]],
            ['id'=>12,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[71]],
            ['id'=>13,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[73]],
            ['id'=>14,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[76]],
            ['id'=>15,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[78]],
            ['id'=>16,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[79]],
            ['id'=>17,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[81]],
            ['id'=>18,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[83]],
            ['id'=>19,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[85]],
            ['id'=>20,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[87]],
            ['id'=>21,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[90]],
            ['id'=>22,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[92]],
            ['id'=>23,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[94]],
            ['id'=>24,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[96]],
            ['id'=>25,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[98]],
            ['id'=>26,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[100]],
            ['id'=>27,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[102]],
            ['id'=>28,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[104]],
            ['id'=>29,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[106]],
            ['id'=>30,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[107]],
            ['id'=>31,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[109]]
        ];
        $userServices = new UserServices();
        $userInfo = $userServices
            ->where('id',2)
            ->find();

        if(!$userInfo)return res_data(0,'用户获取失败');
        $patientData=[
            'gender' =>$userInfo['patient_gender'], //L患者性别：[L1:男] [L2:女]
            'age' =>$userInfo['patient_age'],  //M患者年龄
            'education' =>$userInfo['patient_education'],  //N教育程度 [N1:未上过学/
            //不识字][N2:小学][N3:初中][N:高中/中专][N5:本科及以上]
            'disease_type' =>$userInfo['patient_disease_type'], //O患者疾病类型：[O1:阿尔茨海默病][O2:血管性痴呆][O3:混合性痴呆][O4:其他]
            'illness' =>$userInfo['patient_illness'],  //P患者病情严重程度：[P1:轻度] [P2:中度] [P3:重度]
            'hobby' =>$userInfo['patient_hobby'], //Q患者确诊前的兴趣爱好（可多选）：[Q1:无][Q2:唱歌/唱戏/听音乐/听戏/演奏乐器][Q3:跳舞/健美操/八段锦/打太极/练气功][Q4:散步/慢跑/爬山/打球/游泳/旅游][Q5:绘画/书法/写作/阅读][Q6:养花草植物][Q7:养宠物][Q8:其他（请列出________）]
            'hobby_content' =>$userInfo['patient_hobby_content'], //患者兴趣爱好8 自定义爱好
            'walk' =>$userInfo['patient_walk'], //R患者行走能力：[R1:可以正常行走][R2:自行使用拐杖、助步器、轮椅][R3:使用轮椅且需帮助][R4:完全卧床]
            'status' => 1,
            'is_del' => 1,
            'is_join_research'=>2,
            'is_evaluate' =>2,

        ];

        $orderServices = new OrderServices();
        $orderInfo = $orderServices->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        //测评问题
        $orderEvalustionServices = new  OrderEvalustionServices();
        $orderEvalustionList = $orderEvalustionServices->model->where('order_id',$orderId)->select();
        if(!$orderEvalustionList)return res_data(0,'该工单id还未提交测评问题');
        //照护方案
        // $orderProgramModel = new OrderProgram();
        // $orderProgram = $orderProgramModel->where('order_id',$orderId)->select();
        // if($orderProgram)return res_data(0,'该工单id的方案已存在，请勿重新请求');
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
        $careInfo = json_decode($res['data'],true);
        // dump($careInfo);
        // die;
         json_success(res_data(1,'请求成功',$careInfo));

    }

    /**
     * @return void
     */
    public function test() {
        json_success('test');
    }


}
