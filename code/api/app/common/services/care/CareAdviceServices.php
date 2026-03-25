<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2022/10/22
 * Time: 2:38 AM
 */
namespace app\common\services\care;
use app\common\model\EvaluationCapabilityOptions;
use app\common\services\BaseServices;
use app\common\services\basicconfig\BasicConfigServices;
use app\common\services\user\UserServices;
use think\Db;

class CareAdviceServices extends BaseServices
{
    public function setModel()
    {

    }
    public function get_advice($user_id,$ids = array())
    {
        //获取患者信息
        $userServices = new UserServices();
        $userInfo = $userServices->model
            ->where('id', $user_id)
            ->field('patient_gender,patient_age,patient_education,patient_disease_type,patient_illness,patient_hobby,patient_hobby_content,patient_walk')
            ->find();

        $patient['user_feature_list'] = [];
        if($userInfo){
           $basicConfigServices = new BasicConfigServices();

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
            $patient['user_feature_list'] = $patientList;
        }

        //
        $patient['user_problem_list'] = [];
        $user_problem_list = [];
        $evaluationCapabilityOptions = new EvaluationCapabilityOptions();
        if(is_array($ids) && !empty($ids)){

        }
        $capability_options = $evaluationCapabilityOptions
            ->where('id', 'in', $ids)
            ->select();

        if(!$capability_options) {
            json_fail('未获取到问题');
        }


        foreach ($capability_options as $value){
            if(!empty($value['sn']) && isset($value['sn']) && !empty($value['name']) && isset($value['name'])){
                array_push($user_problem_list, ['id' => $value['sn'], 'content' => $value['name']]);
            }
        }

        $patient['user_problem_list'] = $user_problem_list;
        $postjson = json_encode($patient);
        $headers = array();

        array_push($headers, "Content-Type: text/plain");


        $res = curl('http://www.nurseadvicetest.xyz:9898/expert_interaction',$postjson,1,$headers);
        $list = [];
        if($res !== false) {
            if ($res['code'] == 200) {

                $careInfo = json_decode($res['data'], true);
                //日常活动安排
                if ($careInfo['activity_advice']) {


                    foreach ($careInfo['activity_advice'] as $activity_advice) {
                        unset($activity);
                        $activity['id'] = $activity_advice['id'];
                        $activity['content'] = '日常活动安排';
                        $activity['advice'][] = $activity_advice;
                        array_push($list, $activity);

                    }
                    // $list[] = $activity_data;
                }
                //环境安排
                if ($careInfo['environment_advice']) {



                    foreach ($careInfo['environment_advice'] as $environment_advice) {
                        unset($environment);
                        $environment['id'] = $environment_advice['id'];
                        $environment['content'] = '居住环境安排';
                        $environment['advice'][] = $environment_advice;
                        array_push($list, $environment);

                    }
                    //$list[] = $environment_data;
                }
                //照护问题的照护建议
                if ($careInfo['problem_advice']) {


                    foreach ($careInfo['problem_advice'] as $problem_advice) {
                        unset($environment);
                        $environment['id'] = $problem_advice['pid'];
                        $environment['content'] = $problem_advice['pcontent'];
                        $environment['advice'] = $problem_advice['advices'];
                        array_push($list, $environment);


                    }
                    //$list[] = $problem_data;
                }
            }

        }
        $data = [];
        $data['user_id'] = $user_id;
        $data['option_id'] = implode(',',$ids);
        $data['request_time'] = time();
        Db::name('user_search_log')->insert($data);
        return res_data(1,'请求成功', $list);


    }

}
