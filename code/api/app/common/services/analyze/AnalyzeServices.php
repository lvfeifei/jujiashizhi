<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/27
     * Time: 16:58
     */
namespace app\common\services\analyze;
use app\common\model\EvaluationCapabilityOptions;
use app\common\model\OrderProgram;
use app\common\model\Province;
use app\common\model\UserEvaluate;
use app\common\services\BaseServices;
use app\common\services\evaluationcapability\EvaluationCapabilityServices;
use app\common\services\evaluationclass\EvaluationClassServices;
use app\common\services\order\OrderEvalustionServices;
use think\Db;

class AnalyzeServices extends BaseServices
{

    public function setModel()
    {

    }

    /**
     * 数据分析
     * Date: 2022/8/12
     * Time: 15:16
     * USER:GCQ
     */
    public function analyze()
    {
        ini_set('max_execution_time','0');
        set_time_limit(0);

        $evaluationClassServices = new EvaluationClassServices();
        // $evaluationCapabilityServices = new EvaluationCapabilityServices();
        // $evaluationCapabilityOptionsModel = new EvaluationCapabilityOptions();
        // $orderEvalustionServices = new OrderEvalustionServices();
        $userEvaluateModel = new UserEvaluate();
        $orderProgramModel = new OrderProgram();

        $evaluationClassList_new = [];
        $evaluationClassList = []; //分类
        $evaluationCapabilityList =[];//问题
        $evaluationClass= $evaluationClassServices->model->alias('c')
            ->join('capability cb', 'c.id = cb.classify_id','INNER')
            ->join('capability_options co', 'cb.id = co.capability_id','INNER')
            ->where('c.status',1)
            ->where('c.is_del',1)
            ->where('cb.status',1)
            ->where('cb.is_del',1)
            ->where('co.status',1)
            ->where('co.is_del',1)
            ->field('c.id,c.name,c.content,cb.id as cb_id,cb.classify_id as cb_classify_id,cb.sn as cb_sn,cb.name as cb_name,co.id as co_id,co.sn as co_sn,co.name as co_name,co.capability_id as co_capability_id')
            ->select();

            if($evaluationClass){
                foreach ($evaluationClass as $k=>$v){
                    //分类
                    $evaluationClassList[] = array('id'=>$v['id'], 'name'=>$v['name'], 'content'=>$v['content'],'capability'=>[]);
                    //问题
                    $evaluationCapabilityList[] = array('id'=>$v['cb_id'], 'name'=>$v['cb_name'],'sn'=>$v['cb_sn'],'classify_id'=>$v['cb_classify_id'], 'options'=>[]);

                    //选项
                    $evaluationCapabilityOptionList[] = array('id'=>$v['co_id'], 'name'=>$v['co_name'],'sn'=>$v['co_sn'],'capability_id'=>$v['co_capability_id'], 'count'=>0,'proportion'=>0,'ev_count'=>0);
                }

                //分类
                $evaluationClassList_new = array_unset($evaluationClassList,'id');
                //问题

                $evaluationCapabilityList_new = array_unset($evaluationCapabilityList,'id');



                //比例
                //                         // 1，2有用   3，4无用
                //                         //有效建议
                //将照护方案编号sn分组
                $userEvaluateCountList = $orderProgramModel
                    ->field('id,sn,pid')
                    ->group('sn')
                    ->select();
                // return ['a'=>$userEvaluateCountList];

                //将评价的编号sn分组统计
                $userEvaluateLIst = $userEvaluateModel
                    ->field('sn,COUNT(sn) as sn_num')
                    ->whereIn('program_option_id',[1,2])
                    ->group('sn')
                    ->select();

                if($userEvaluateCountList && $userEvaluateLIst){
                    foreach ($userEvaluateCountList as $kuec=>$vuec){
                        $userEvaluateCountList[$kuec]['num']=0;
                        foreach ($userEvaluateLIst as $kue=>$vue){
                            if($vuec['sn'] == $vue['sn']){
                                $userEvaluateCountList[$kuec]['num']=$vue['sn_num'];
                            }
                        }
                    }
                }

                $userEvaluateCountList_new =$this->get_menuTree($userEvaluateCountList,'pid');

                //评价有效的照护方案统计
                foreach ($userEvaluateCountList_new as $key_new => $value_new){
                    if (isset($value_new['child']) && !empty($value_new['child'])) {
                        $num = 0;
                        foreach ($value_new['child'] as $key_child => $value_child) {

                            $num += $value_child['num'];
                        }
                        $userEvaluateCountList_new[$key_new]['num'] = $num;
                    }
                }





                // return $userEvaluateCountList_new;
                //统计填写次数
                $orderProgramcount_list = $orderProgramModel
                    ->field('sn,count(sn) as num')
                    ->where('advice','problem_advice')
                    ->where('pid',0)
                    ->group('sn')
                    ->select();

                if($orderProgramcount_list){
                    foreach ($evaluationCapabilityOptionList as $ka=>$it){
                        foreach($orderProgramcount_list as $kopc=>$itopc){
                            if(trim($it['sn'])==trim($itopc['sn'])){
                                $evaluationCapabilityOptionList[$ka]['count']=$itopc['num'];
                            }
                        }


                    }
                    foreach ($evaluationCapabilityOptionList as $ka=>$it) {

                        foreach ($userEvaluateCountList_new as $ueclkey => $ueclitem) {
                            if($ueclitem['sn']==$it['sn']){
                                $evaluationCapabilityOptionList[$ka]['ev_num'] = $ueclitem['num'];

                                $evaluationCapabilityOptionList[$ka]['proportion'] = number_format($ueclitem['num']/$it['count'],2);

                            }


                    }


                    }





                    foreach ($evaluationCapabilityOptionList as $ka=>$it){
                        foreach($orderProgramcount_list as $kopc=>$itopc){
                            if(trim($it['sn'])==trim($itopc['sn'])){
                                $evaluationCapabilityOptionList[$ka]['count']=$itopc['num'];
                            }
                        }


                    }
                }





                foreach ($evaluationCapabilityList_new as $kk=>$vv){
                   foreach ($evaluationCapabilityOptionList as $kkk=>$vvv){
                       if($vv['id']==$vvv['capability_id']){

                           //问题人数
                            $question_number = $orderProgramModel->where('sn',$vvv['sn'])->count('order_id');
                           $vvv['question_number'] = $question_number;
                           //反馈人数
                           $feedback_number = $userEvaluateModel->where('sn','like',$vvv['sn'].'%')
                               ->group('order_id')
                               ->count('order_id');
                           $vvv['feedback_number'] = $feedback_number;
                           $vvv['feedback'] = 0;
                            if($question_number>0){
                                $vvv['feedback'] = number_format($feedback_number/$question_number*100,2);
                            }

                           //查询某一个选项问题，推出的所有推荐建议
                           $sql = "select sn,content from `cx_order_program` where pid in (select id from `cx_order_program` where sn = '{$vvv['sn']}') group by sn";
                           $group_program = Db::query($sql);
                            $vvv['group_program']=[];
                           if($group_program)
                           {


                               foreach ($group_program as $prokey =>$pro){
                                   //采纳人数
                                   $adopt = $userEvaluateModel->where('sn',$pro['sn'])
                                       ->where('program_option_id','neq',4)
                                       ->count('id');
                                   $group_program[$prokey]['adopt_number'] = $adopt;
                                   //统计 有效
                                   //select count(id) from `cx_user_evaluate` where sn ="A1-1" and `program_option_id` in (1,2);
                                    $valid = $userEvaluateModel->where('sn',$pro['sn'])
                                        ->where('program_option_id','in',[1,2])
                                        ->count('id');
                                    //有效率
                                   $group_program[$prokey]['efficiency'] = 0;
                                    if($adopt>0){
                                        $group_program[$prokey]['efficiency'] = number_format($valid/$adopt*100,2);
                                    }

                                   $group_program[$prokey]['content'] = json_decode($pro['content'],true);

                               }
                               $vvv['group_program']=$group_program;
                           }



                           //选项
                           $evaluationCapabilityList_new[$kk]['options'][] = $vvv;

                       }


                   }
                }
                foreach ($evaluationClassList_new as $ke=>$va){
                    foreach ($evaluationCapabilityList_new as $key=>$value){
                        if($va['id']==$value['classify_id']){
                            $evaluationClassList_new[$ke]['capability'][] = $value;
                        }
                    }
                }

            }

        return res_data(1,'请求成功',$evaluationClassList_new);

    }

    public function get_menuTree($array, $pid)
    {
        $arr = array();
        foreach ($array as $v) {
            if ($v['pid'] == $pid) {
                $tem = menuTree($array, $v['id']);
//            if($v['parameter'] && $v['url']){
//                $params = json_decode($v['parameter'], true);
//                $v['urlStr'] = URL($v['url'], $params);
//            } elseif($v['url']) {
//                $v['urlStr'] = URL($v['url']);
//            }
                //判断是否存在子数组
                $tem && $v['child'] = $tem;
                $arr[] = $v;
            }
        }
        return $arr;
    }

}