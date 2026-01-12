<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/26
     * Time: 8:35
     */
namespace app\common\services\evaluate;
use app\common\services\order\OrderServices;
use app\common\model\OrderProgram;
use app\common\services\order\OrderEvalustionServices;
use app\common\services\BaseServices;
use app\common\services\basicconfig\BasicConfigServices;
use think\Request;
use think\Db;
class EvaluateServices extends BaseServices
{

    public function setModel()
    {

    }
    public function get_evaluate($orderId,$userId)
    {
        $orderServices = new OrderServices();
        $orderInfo =$orderServices->model->where('id',$orderId)->where('user_id',$userId)->find();

        if(!$orderInfo)return res_data(0,'该工单不存在');
        if($orderInfo['status'] != 3)return res_data(0,'该工单还未发送');
        $orderEvalustionServices = new  OrderEvalustionServices();
        $orderEvalustionList = $orderEvalustionServices->model->where('order_id',$orderId)->select();
        if(!$orderEvalustionList)return res_data(0,'该工单id还未提交测评问题');
        //照护方案
        $orderProgramModel = new OrderProgram();
        $orderProgram = $orderProgramModel->where('order_id',$orderId)->select();
        if(!$orderProgram)return res_data(0,'该工单id未获取到照护方案，请先获取照护方案');

        $program_class = [
            ['id' => 1, 'program_class' => 'activity_advice', 'program_class_name' => '日常活动安排'],
            ['id' => 2, 'program_class' => 'problem_advice', 'program_class_name' => '照护问题的照护建议'],
            // ['id' => 3, 'program_class' => 'environment_advice', 'program_class_name' => '居住环境安排'],
        ];
        $basicConfigServices  = new BasicConfigServices();
        $activity_options = $basicConfigServices->activity_options;
        $care_options = $basicConfigServices->care_options;
        if($program_class){
            foreach ($program_class as &$item){
                $item['Program'] = [];
                $orderProgramList = $orderProgramModel
                    ->where('order_id',$orderId)
                    ->where('advice',$item['program_class'])
                    ->where('pid',0)
                    ->field('is_del',true)
                    ->select();
                if($orderProgramList){
                    $i=1;
                    foreach ($orderProgramList as $kk=>$value){


                        // $value['content'] = json_decode($value['content'],true);
                        $value['child'] = [];
                        $value['options'] = [];
                        if($value['advice'] == 'activity_advice'){
                            // if(trim($value['content'])== '无'){
                            //     continue;
                            // }
                            $value['options'] = $activity_options;
                        }
                        if($value['advice'] == 'problem_advice'){
                            $content = json_decode($value['content'],true);
                            $orderProgramList[$kk]['content'] = '问题'.$i.'、'. $content;

                            if(trim($content)== '无（能正常行走或借助辅助工具行走）'){
                                unset($orderProgramList[$kk]);
                                continue;
                            }
                            if(trim($content)== '无'){
                                unset($orderProgramList[$kk]);
                                continue;
                            }
                            $i++;
                            $value['options'] = $care_options;
                        }else{
                            $orderProgramList[$kk]['content'] = json_decode($value['content'],true);
                        }


                        $program = $orderProgramModel
                            ->where('order_id',$orderId)
                            ->where('advice',$item['program_class'])
                            ->where('pid',$value['id'])
                            ->select();
                        if($program){
                            foreach ($program as $programitem){
                                $programitem['content'] = json_decode($programitem['content'],true);
                                $programitem['options'] = $care_options;
                            }
                            $value['child'] = $program;
                        }

                    }

                    $item['Program'] = $orderProgramList;
                }

            }

        }


        return res_data(1,'请求成功',$program_class);
    }

    public function is_pop_up($userId)
    {
        $orderServices = new OrderServices();
        $orderInfo = $orderServices->model
            ->where('user_id',$userId)
            ->where('is_evaluate',2)
//            ->where('evaluate_start_time','<',time())
               ->order('create_time', 'asc')
            ->find();
        if($orderInfo) {
            // if ($orderInfo['is_evaluate'] == 1) {
            //     return res_data(1, '请求成功',['id'=>$orderInfo['id'],'status'=>false]);
            // } else {
                return res_data(1, '请求成功', ['id'=>$orderInfo['id'],'status'=>true]);

            // }
        }else{
            return res_data(1, '请求成功', ['id'=>0,'status'=>false]);
        }

    }



}