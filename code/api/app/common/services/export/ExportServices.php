<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/26
     * Time: 8:35
     */
namespace app\common\services\export;

use app\common\model\EvaluationCapabilityOptions;
use app\common\model\OrderProgram;
use app\common\model\OrderResearchFamily;
use app\common\model\OrderResearchScenes;
use app\common\model\UserEvaluate;
use app\common\services\BaseServices;
use app\common\services\basicconfig\BasicConfigServices;
use app\common\services\evaluationcapability\EvaluationCapabilityServices;
use app\common\services\evaluationclass\EvaluationClassServices;
use app\common\services\order\OrderEvalustionServices;
use app\common\services\order\OrderServices;
use app\common\services\upload\UploadServices;
use app\common\services\user\UserServices;
use PhpOffice\PhpWord\Shared\ZipArchive;
use think\Request;
use think\Db;
class ExportServices extends BaseServices
{

    public function setModel()
    {

    }

    /**
     *测评问题导出excel
     * Date: 2022/8/12
     * Time: 19:17
     * USER:GCQ
     */
    public function export_patient_excel($orderId)
    {
        $orderServices = new OrderServices();
        $orderInfo = $orderServices->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        if($orderInfo['is_send']==2)return res_data(0,'未发送照护方案');
        $userServices = new UserServices();
        $userInfo = $userServices->model->where('id',$orderInfo['user_id'])->find();
        $basicConfigServices = new BasicConfigServices();
        //照护者
        if($userInfo){
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
            //情感表达

            $userInfo['emotion'] = 0;
            $orderResearchFamilyModel = new OrderResearchFamily();
            $score = $orderResearchFamilyModel
                ->where('order_id',$orderId)
                ->sum('family_relation_option_id');
            $userInfo['emotion'] = $score;


        }else{
            $user_info = [];
        }
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

        $userEvaluateModel = new UserEvaluate();
        $orderProgramModel = new OrderProgram();
        $orderProgramList=$orderProgramModel->where('order_id',$orderId)->where('pid',0)->select();
        if($orderProgramList){
            foreach($orderProgramList as $key=>$item){
                $orderProgramList[$key]['efficient'] = false;//有效建议  false 无用建议
                $userEvaluateInfo = $userEvaluateModel
                    ->where('program_question_id',$item['id'])
                    ->where('order_id',$orderId)
                    ->whereIn('program_option_id',[1,2])
                    ->find();
                if($userEvaluateInfo){
                    $orderProgramList[$key]['efficient']=true; //有效建议
                }
                $orderProgramList[$key]['advice'] = $item['advice'];
                $orderProgramList[$key]['content'] =json_decode($item['content'],true);
                $orderProgramList[$key]['child'] = [];
                $orderProgramChild = $orderProgramModel->where('order_id',$orderId)->where('pid',$item['id'])->select();
                if($orderProgramChild){
                    foreach ($orderProgramChild as $kk=>$vv){
                        $orderProgramChild[$kk]['efficient'] = false;
                        $userEvaluateChildInfo = $userEvaluateModel
                            ->where('program_question_id',$vv['id'])
                            ->where('order_id',$orderId)
                            ->whereIn('program_option_id',[1,2])
                            ->find();

                        if($userEvaluateChildInfo){
                            $orderProgramChild[$kk]['efficient']=true; //有效建议
                        }
                        $orderProgramChild[$kk]['content'] =json_decode($vv['content'],true);
                    }
                    $orderProgramList[$key]['child'] = $orderProgramChild;
                }
            }
        }else{
            $orderProgramList = [];
        }







        //当前用户信息
        $dateinfo['userinfo'] = $userInfo;
        //当前工单患者信息
        $dateinfo['orderinfo'] = $orderInfo;
        //当前工单照护方案
        $dateinfo['program'] = $orderProgramList;
        $headArr['parent'] = [
            '用户名',
            '照护者基本信息',
            '痴呆患者信息'
        ];
        $headArr['child'] = [
            'ID',
            '性别',
            '年龄',
            '教育程度',
            '照护年限',
            '与患者关系',
            '患者同住',
            '照护者情感表达',
            '性别',
            '年龄',
            '疾病类型',
            '疾病严重程度',
            '确诊前兴趣爱好',
            '行走能力',
            // '生活自理模块'
        ];
        $headArr['program'] = [
            '问题编号',
            '照护建议系统推荐',
            '照护建议专家调整后',
            '反馈有效的建议'
        ];
        $this->excelExport('',$headArr,$dateinfo);
        //导出excel
        return res_data(1,'请求成功',$dateinfo);

    }
    private function excelExport($fileName = '', $headArr = [], $data = [])
    {

        $fileName .= date("Y_m_d_H_i", time()) . rand(1, 100) . ".xlsx";
        vendor("Phpexcel_7_4.PHPExcel"); //方法一
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties();

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . '5', $headArr['parent'][0]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . '5', $headArr['parent'][0]);
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);//加粗

        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . '5', $headArr['parent'][1]);
        $objPHPExcel->getActiveSheet()->mergeCells('C'.''.'5'.":".'I'.''.'5',$headArr['parent'][1]);
        $objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);//加粗

        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . '5', $headArr['parent'][2]);
        $objPHPExcel->getActiveSheet()->mergeCells('J'.''.'5'.":".'P'.''.'5',$headArr['parent'][2]);
        $objPHPExcel->getActiveSheet()->getStyle('J5')->getFont()->setBold(true);//加粗
        // 设置行高
        $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(30);

        $key = ord("B"); // 设置表头
        $colum = chr($key);
        foreach ($headArr['child'] as $v) {
            $colum = chr($key);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(20);
            $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '6', $v);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '6', $v);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '6')->getFont()->setBold(true);//加粗
            $fontColor = new \PHPExcel_Style_Color();
            $fontColor->setRGB('FF000000');
            $objPHPExcel->getActiveSheet()->getStyle($colum . '6')->getFont()->setColor($fontColor);//设置颜色
            $key += 1;
        }

        // 设置行高
        $objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(30);

        $key = ord("Q"); // 设置表头
        foreach ($headArr['program'] as $v){
            $colum = chr($key);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(20);
            $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '5', $v);
            $objPHPExcel->getActiveSheet()->mergeCells($colum.''.'5'.":".$colum.''.'6',$v);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '5')->getFont()->setBold(true);//加粗
            $key += 1;
        }

        //边框样式
        $color='#000000';
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    // 'style' => \PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,//细边框
                    'color' => array('argb' => $color),
                ),
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('B5:T5')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B6:T6')->applyFromArray($styleArray);
        //背景颜色
        //设置填充的样式和背景色
        $objPHPExcel->getActiveSheet()->getStyle( 'B5:T5')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle( 'B5:T5')->getFill()->getStartColor()->setARGB('DDEBF7');
        $objPHPExcel->getActiveSheet()->getStyle( 'B6:T6')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle( 'B6:T6')->getFill()->getStartColor()->setARGB('DDEBF7');

        // $key = ord("Q"); // 设置表头
        // foreach ($data['program'] as $programItem) {
        //     $colum = chr($key);
        //     dump($programItem);
        //     $key += 1;
        // }

        $column = 7;
        $data['program']=json_decode(json_encode($data['program']),true);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $colorarr[0]= 'DDEBF7';
        $colorarr[1] = 'FFF2CC';
        $colorarr[2] = 'E2EFDA';

        foreach ($data['program'] as $key => $rows) { // 行写入
            set_time_limit(0);
            if($rows['pid']==0) {
                $rand = rand(0,2);
                if($rows['child']){

                    foreach ($rows['child'] as $childKey =>$childItem){
                        //设置边框
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $column . ":" . 'T' .$column)->applyFromArray($styleArray);
                        //背景
                        $objPHPExcel->getActiveSheet()->getStyle( 'Q' . $column . ":" . 'T' .$column)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                        $objPHPExcel->getActiveSheet()->getStyle( 'Q' . $column . ":" . 'T' .$column)->getFill()->getStartColor()->setARGB($colorarr[$rand]);


                        //pid=0
                        $objActSheet->setCellValue('Q' . $column,$rows['sn']);
                        if($rows['is_update']==1){
                            $objActSheet->setCellValue('S' . $column,$rows['sn']);
                        }

                        // child
                        $objActSheet->setCellValue('R' . $column,$childItem['sn']);
                        if($childItem['is_update']==1){
                            $objActSheet->setCellValue('S' . $column,$childItem['sn']);

                        }

                        if($childItem['efficient']==true){
                            $objActSheet->setCellValue('T' . $column,$childItem['sn']);
                        }
                        //照护者信息
                        $objActSheet->setCellValue('B' . $column, $data['userinfo']['id']);
                        $objActSheet->setCellValue('C' . $column, $data['userinfo']['gender_name']);
                        $objActSheet->setCellValue('D' . $column, $data['userinfo']['age'].'岁');
                        $objActSheet->setCellValue('E' . $column, $data['userinfo']['education_name']);
                        $objActSheet->setCellValue('F' . $column, $data['userinfo']['care_years_name']);
                        $objActSheet->setCellValue('G' . $column, $data['userinfo']['relation_name']);
                        $objActSheet->setCellValue('H' . $column, $data['userinfo']['live_name']);
                        $objActSheet->setCellValue('I' . $column, $data['userinfo']['emotion']);
                        //患者信息
                        $objActSheet->setCellValue('J' . $column, $data['orderinfo']['gender_name']);
                        $objActSheet->setCellValue('K' . $column, $data['orderinfo']['age'].'岁');
                        $objActSheet->setCellValue('L' . $column, $data['orderinfo']['disease_type_name']);
                        $objActSheet->setCellValue('M' . $column, $data['orderinfo']['disease_type_name']);
                        $objActSheet->setCellValue('N' . $column, $data['orderinfo']['hobby_name']);
                        $objActSheet->setCellValue('O' . $column, $data['orderinfo']['walk_name']);
                        // $objActSheet->setCellValue('P' . $column, $data['orderinfo']['walk_name']);

                        $column++;
                    }
                }else{
                    //设置边框
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $column . ":" . 'T' .$column)->applyFromArray($styleArray);
                    //背景
                    $objPHPExcel->getActiveSheet()->getStyle( 'Q' . $column . ":" . 'T' .$column)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle( 'Q' . $column . ":" . 'T' .$column)->getFill()->getStartColor()->setARGB($colorarr[$rand]);

                    //照护者信息
                    $objActSheet->setCellValue('B' . $column, $data['userinfo']['id']);
                    $objActSheet->setCellValue('C' . $column, $data['userinfo']['gender_name']);
                    $objActSheet->setCellValue('D' . $column, $data['userinfo']['age'].'岁');
                    $objActSheet->setCellValue('E' . $column, $data['userinfo']['education_name']);
                    $objActSheet->setCellValue('F' . $column, $data['userinfo']['care_years_name']);
                    $objActSheet->setCellValue('G' . $column, $data['userinfo']['relation_name']);
                    $objActSheet->setCellValue('H' . $column, $data['userinfo']['live_name']);
                    $objActSheet->setCellValue('I' . $column, $data['userinfo']['emotion']);
                    //患者信息
                    $objActSheet->setCellValue('J' . $column, $data['orderinfo']['gender_name']);
                    $objActSheet->setCellValue('K' . $column, $data['orderinfo']['age'].'岁');
                    $objActSheet->setCellValue('L' . $column, $data['orderinfo']['disease_type_name']);
                    $objActSheet->setCellValue('M' . $column, $data['orderinfo']['disease_type_name']);
                    $objActSheet->setCellValue('N' . $column, $data['orderinfo']['hobby_name']);
                    $objActSheet->setCellValue('O' . $column, $data['orderinfo']['walk_name']);
                    // $objActSheet->setCellValue('P' . $column, $data['orderinfo']['']);
                    if($rows['advice'] =='problem_advice'){
                        $objActSheet->setCellValue('Q' . $column,$rows['sn']);
                    }else{
                        $objActSheet->setCellValue('R' . $column,$rows['sn']);
                    }

                    if($rows['is_update']==1){
                        $objActSheet->setCellValue('S' . $column,$rows['sn']);
                    }
                    if($rows['efficient']==true){
                        $objActSheet->setCellValue('T' . $column,$rows['sn']);
                    }

                    $column++;
                }

            }
            // $objPHPExcel->getActiveSheet()->getStyle( 'Q' . $column . ":" . 'T' .$column)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            // $objPHPExcel->getActiveSheet()->getStyle( 'Q' . $column . ":" . 'T' .$column)->getFill()->getStartColor()->setARGB('DDEBF7');

            // foreach ($rows as $keyName => $value) { // 列写入

            //    // echo $keyName;
            //     set_time_limit(0);
            //     $objActSheet->setCellValue(chr($span) . $column,$value);
                // $span++;
            // }
            // $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表
        $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . $fileName);
        header('Cache-Control: max-age=0');
        // $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output'); // 文件通过浏览器下载
        exit();
    }


    public function export_analyze_excel()
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
                                if($pro['content']){
                                    $content = json_decode($pro['content'],true);
                                    if($content && is_array($content)){
                                        foreach ($content as $con_k=>$con_v){
                                            if($con_v['type'] == 'image'){
                                                unset($content[$con_k]);
                                            }
                                            if($con_v['type'] == 'video'){
                                                unset($content[$con_k]);
                                            }
                                        }
                                    }
                                }
                                $group_program[$prokey]['content'] = $content;


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

        $headArr = [
            '分类',
            '问题',
            '选项',
            '推荐照护建议及数据统计',
            '采纳人数',
            '有效率',
            '问题人数',
            '参与反馈人数',
            '后反馈率'
        ];
        $fileName = '数据分析';
        $this->analyze_excelExport($fileName,$headArr,$evaluationClassList_new);
        return res_data(1,'请求成功',$evaluationClassList);
    }
    private function analyze_excelExport($fileName = '',$headArr,$data)
    {
        ini_set('max_execution_time','0');
        set_time_limit(0);

        $fileName .= date("Y_m_d_H_i", time()) . rand(1, 100) . ".xlsx";
        vendor("Phpexcel_7_4.PHPExcel"); //方法一
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties();
        $key = ord("A"); // 设置表头

        foreach ($headArr as $v) {
            $colum = chr($key);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(20);
            $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key += 1;
        }
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        $span = ord("A");
        set_time_limit(0);
        foreach ($data as $key => $class) { // 行写入
            foreach ($class['capability'] as $capabilityKdy=>$capability){
                foreach ($capability['options'] as $optionsKey=>$options){

                    if(isset($options['group_program']) && is_array($options['group_program'])){
                        foreach($options['group_program'] as $group_program_k=>$group_program_v) {

                            $objActSheet->setCellValue('A' . $column, $class['name']);
                            $objActSheet->setCellValue('B' . $column, $capability['name']);
                            $objActSheet->setCellValue('C' . $column, $options['name']);
                            $con = '';
                            foreach ($group_program_v['content'] as $v) {
                                if($v['type'] == 'text') {
                                    $con .= $v['con'];
                                }

                            }
                            $objActSheet->setCellValue('D' . $column,$group_program_v['sn'].$con);
                            $objActSheet->setCellValue('E' . $column,$group_program_v['adopt_number']);
                            $objActSheet->setCellValue('F' . $column,$group_program_v['efficiency']);
                            $objActSheet->setCellValue('G' . $column, $options['question_number']);
                            $objActSheet->setCellValue('H' . $column, $options['feedback_number']);
                            $objActSheet->setCellValue('I' . $column, $options['feedback']);
                            $column++;
                        }

                    }else {

                        $objActSheet->setCellValue('A' . $column, $class['name']);
                        $objActSheet->setCellValue('B' . $column, $capability['name']);
                        $objActSheet->setCellValue('C' . $column, $options['name']);
                        $objActSheet->setCellValue('D' . $column,'');
                        $objActSheet->setCellValue('E' . $column,'');
                        $objActSheet->setCellValue('F' . $column,'');
                        $objActSheet->setCellValue('G' . $column, $options['question_number']);
                        $objActSheet->setCellValue('H' . $column, $options['feedback_number']);
                        $objActSheet->setCellValue('I' . $column, $options['feedback']);
                        $column++;
                    }



                }

            }


        }
        $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表
        $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . $fileName);
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output'); // 文件通过浏览器下载
        exit();
    }






    /**
     * 下载语音
     * @param $orderId
     * @param $userId
     * Date: 2022/8/12
     * Time: 18:19
     * USER:GCQ
     */
    public function export_scenes($orderId)
    {
        $orderServices = new OrderServices();
        $orderInfo = $orderServices->model->where('id',$orderId)->find();
        if(!$orderInfo)return res_data(0,'该工单不存在');
        if($orderInfo['is_join_research']==2)return res_data(0,'您未参加关爱调研');
        $orderResearchScenesModel = new OrderResearchScenes();
        $orderResearchScenesInfo = $orderResearchScenesModel->where('order_id',$orderId)->find();
        $scenes = [];

        if($orderResearchScenesInfo){
            //场景一
            if($orderResearchScenesInfo['scenes_one']){
                array_push($scenes,['url'=>$orderResearchScenesInfo['scenes_one'],'name'=>'scenes_one']);
            }
            //场景二
            if($orderResearchScenesInfo['scenes_two']){
                array_push($scenes,['url'=>$orderResearchScenesInfo['scenes_two'],'name'=>'scenes_two']);
            }
            //场景三
            if($orderResearchScenesInfo['scenes_three']){
                array_push($scenes,['url'=>$orderResearchScenesInfo['scenes_three'],'name'=>'scenes_two']);
            }
            //场景四
            if($orderResearchScenesInfo['scenes_four']){
                array_push($scenes,['url'=>$orderResearchScenesInfo['scenes_four'],'name'=>'scenes_two']);
            }
            //场景五
            if($orderResearchScenesInfo['scenes_five']){
                array_push($scenes,['url'=>$orderResearchScenesInfo['scenes_five'],'name'=>'scenes_two']);
            }

            if($scenes){

                $this->Download($scenes);

            }else{
                return res_data(0,'该工单关爱调研场景语音未设置');
            }

        }else{
            return res_data(0,'没有获取到该工单关爱调研场景语音');
        }

    }
    /**
     * 下载文件
     * @param $img
     * @return string
     */
    public function Download($img)
    {

        $items = [];
        $names = [];
        if($img)
        {
            //用于前端跳转zip链接拼接
            $path_redirect = '/zip/'.date('Ymd');
            //临时文件存储地址
            $path      = '/tmp'.$path_redirect;
            if(!is_dir($path))
            {
                mkdir($path, 0777,true);
            }
            foreach ($img as $key => $value) {
                $fileContent = '';
                $fileContent = $this->CurlDownload($value['url']);
                if( $fileContent )
                {
                    $__tmp = $this->SaveFile( $value['url'] , $path , $fileContent );
                    $items[] = $__tmp[0];
                    $names[] = $value['name'].'_'.($key+1).'.'.$__tmp[1];
                }
            }
            if( $items )
            {

                $zip = new ZipArchive();
                $filename = time().'download.zip';
                $zipname = $path.'/'.$filename;
                if (!file_exists($zipname)) {
                    $res = $zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                    if ($res) {
                        foreach ($items as $k => $v) {
                            $value = explode("/", $v);
                            $end  = end($value);
                            $zip->addFile($v, $end);
                            $zip->renameName($end, $names[$k]);
                        }
                        $zip->close();
                    } else {
                        return '';
                    }
                    //通过前端js跳转zip地址下载,让不使用php代码下载zip文件
                    //if (file_exists($zipname)) {
                    //拼接附件地址
                    //$redirect = 域名.$path_redirect.'/'.$filename;
                    //return $redirect;
                    //header("Location:".$redirect);
                    //}
                    //直接写文件的方式下载到客户端

                    if (file_exists($zipname)) {
                        header("Cache-Control: public");
                        header("Content-Description: File Transfer");
                        header('Content-disposition: attachment; filename=语音'.date('YmdHis') . rand(0, 9999).'.zip'); //文件名
                        header("Content-Type: application/zip"); //zip格式的
                        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
                        header('Content-Length: ' . filesize($zipname)); //告诉浏览器，文件大小
                        @readfile($zipname);
                    }
                    //删除临时文件
                    @unlink($zipname);
                }
            }
            return '';
        }
    }
    /**
     * curl获取链接内容
     * @param $url
     * @return mixed|string
     */
    public function CurlDownload($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $errno   = curl_errno($ch);
        $error   = curl_error($ch);
        $res=curl_exec($ch);
        curl_close($ch);
        if($errno>0){
            return '';
        }

        return $res;
    }
    /**
     * 保存临时文件
     * @param $url
     * @param $dir
     * @param $content
     * @return array
     */
    public function SaveFile( $url ,$dir , $content)
    {
        $fname   = basename($url); //返回路径中的文件名部分
        $str_name  = pathinfo($fname); //以数组的形式返回文件路径的信息
        $extname  = strtolower($str_name['extension']); //把扩展名转换成小写
        $path    = $dir.'/'.md5($url).$extname;
        $fp     = fopen( $path ,'w+' );
        fwrite( $fp , $content );
        fclose($fp);
        return array( $path , $extname) ;
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