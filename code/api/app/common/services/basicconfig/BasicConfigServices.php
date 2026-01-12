<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/27
     * Time: 16:58
     */
namespace app\common\services\basicconfig;
use app\common\services\BaseServices;

class BasicConfigServices extends BaseServices
{
    //照护者
    public $gender = [
        'S1'=>'男',
        'S2'=>'女'
    ];
    //U教育程度 [U1:未上过学/不识字][U2:小学][U3:初中][U4:高中/中专][U5:本科及以上]
    public $education = [
        'U1'=>'未上过学/不识字',
        'U2'=>'小学',
        'U3'=>'初中',
        'U4'=>'高中/中专',
        'U5'=>'本科（大专）及以上',
    ];
    //V照护年限：[V1<1年][V2:1-2年][V3:2–4年][V4:>4年]
    public $care_years = [
        'V1'=>'<1年',
        'V2'=>'1-2年',
        'V3'=>'>2–4年',
        'V4'=>'>4年',
    ];
    //W与患者关系：[W1:配偶][W2:子女][W3:媳婿][W4:其他]
    public $relation = [
        'W1'=>'配偶',
        'W2'=>'子女',
        'W3'=>'媳婿',
        'W4'=>'其他',
    ];
    //X与患者同住：[X1:是][X2:否]
    public $live = [
        'X1'=>'是',
        'X2'=>'否',
    ];

    //患者
    //L患者性别：[L1:男] [L2:女]
    public $patient_gender = [
        'L1'=>'男',
        'L2'=>'女'
    ];
    //N教育程度 [N1:未上过学/不识字][N2:小学][N3:初中][N:高中/中专][N5:本科及以上]
    public $patient_education = [
        'N1'=>'未上过学/不识字',
        'N2'=>'小学',
        'N3'=>'初中',
        'N4'=>'高中/中专',
        'N5'=>'本科（大专）及以上',

    ];
    //O患者疾病类型：[O1:阿尔茨海默病][O2:血管性痴呆][O3:混合性痴呆][O4:其他]
    public $patient_disease_type = [
        'O1'=>'阿尔茨海默病',
        'O2'=>'血管性痴呆',
        'O3'=>'混合性痴呆',
        'O4'=>'其他',

    ];
    //P患者病情严重程度：[P1:轻度] [P2:中度] [P3:重度]
    public $patient_illness=[
        'P1'=>'轻度',
        'P2'=>'中度',
        'P3'=>'重度',

    ];
    //Q患者确诊前的兴趣爱好（可多选）：[Q1:无][Q2:唱歌/唱戏/听音乐/听戏/演奏乐器][Q3:跳舞/健美操/八段锦/打太极/练气功][Q4:散步/慢跑/爬山/打球/游泳/旅游][Q5:绘画/书法/写作/阅读][Q6:养花草植物][Q7:养宠物][Q8:其他（请列出________）]
    public $patient_hobby = [
        'Q1'=>'无',
        'Q2'=>'唱歌/唱戏/听音乐/听戏/演奏乐器',
        'Q3'=>'跳舞/健美操/八段锦/打太极/练气功',
        'Q4'=>'散步/慢跑/爬山/打球/游泳/旅游',
        'Q5'=>'绘画/书法/写作/阅读',
        'Q6'=>'养花草植物',
        'Q7'=>'养宠物',
        'Q8'=>'其他',

    ];
    //R患者行走能力：[R1:可以正常行走][R2:自行使用拐杖、助步器、轮椅][R3:使用轮椅且需帮助][R4:完全卧床]
    public $patient_walk = [
        'R1'=>'可以正常行走',
        'R2'=>'自行使用拐杖、助步器、轮椅',
        'R3'=>'使用轮椅且需帮助',
        'R4'=>'完全卧床',

    ];
    public $activity_options = [
        1 =>'非常有益',
        2 =>'有些有益',
        3 =>'没有益处',
        4 =>'未采纳/做不到'
        ];
    public $care_options = [
        1 =>'非常有效',
        2 =>'有些有效',
        3 =>'没有效果',
        4 =>'未采纳/做不到'
    ];
    public $cart_program= [
        1=>['care' =>'activity_advice','name' => '日常活动安排'],
        2=>['care' =>'environment_advice','name' => '居住环境安排'],
        3=>['care' =>'problem_advice','name' => '照护问题的照护建议'],
    ];
    public function setModel()
    {

    }
    public function get_basic_config()
    {
        $data = [
            'S'=>$this->gender,
            'U'=>$this->education,
            'V'=>$this->care_years,
            'W'=>$this->relation,
            'X'=>$this->live,
            'L'=>$this->patient_gender,
            'N'=>$this->patient_education,
            'O'=>$this->patient_disease_type,
            'P'=>$this->patient_illness,
            'Q'=>$this->patient_hobby,
            'R'=>$this->patient_walk,
          ];
        return res_data(1,'请求成功',$data);
    }


}