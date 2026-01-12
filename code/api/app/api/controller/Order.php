<?php

namespace app\api\controller;




use app\common\services\order\OrderServices;

use think\Request;

class Order extends Basic
{
    public $services;
    public function __construct(Request $request = null,OrderServices $orderServices)
    {
        parent::__construct($request);
        $this->services = $orderServices;
    }

    /**
     * 患者信息/照护方案分类列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/10
     * Time: 8:46
     * USER:GCQ
     */
    public  function patient_program()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数order_id不能为空'));
        $user_id = $this->userId;

        json_success($this->services->patient_program($order_id,$user_id));
    }
    /**
     * 照护方案分类列表
     * Date: 2022/8/4
     * Time: 21:02
     * USER:GCQ
     */
    public function program()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        $user_id = $this->userId;
        json_success($this->services->program($order_id,$user_id));
    }

    /**
     * 照护方案分类列表
     * Date: 2022/8/4
     * Time: 21:02
     * USER:GCQ
     */
    public function order_capability()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        $user_id = $this->userId;
        json_success($this->services->order_capability($order_id,$user_id));
    }

    /**
     * 照护方案详情
     * Date: 2022/8/10
     * Time: 8:47
     * USER:GCQ
     */
    public function program_details()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        $user_id = $this->userId;
        $class_name = $this->request->post('program_class','');
        if(!$class_name)json_success(res_data(0,'参数advice不能为空'));
        json_success($this->services->program_details($order_id,$class_name,$user_id));
    }

    /**
     * 首页获取最新一条已发送的照护方案
     * Date: 2022/8/10
     * Time: 18:59
     * USER:GCQ
     */
    public function program_info()
    {
        $user_id = $this->userId;
        json_success($this->services->get_program_new_one_info($user_id));
    }


    /**
     *  测评问题详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/4
     * Time: 21:00
     * USER:GCQ
     */
    public function details(){
        $order_id = $this->request->post('order_id/d',0);
        if(!$order_id)json_success(res_data(0,'参数order_id不能为空'));
        $user_id = $this->userId;
        json_success($this->services->details($order_id,$user_id));
    }
    /**
     * 测评问题提交
     * Date: 2022/7/28
     * Time: 11:14
     * USER:GCQ
     */
    public function orderCreate()
    {
        $question = $this->request->post('question/a',[]);

        if(!$question)json_success(res_data('0','请完善测评问题'));
        // dump($question);
        // $question = [
        //     //
        //     ['id'=>1,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[3,6]],
        //     ['id'=>2,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[12,13]],
        //     ['id'=>3,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[18,19]],
        //     ['id'=>4,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[24,25]],
        //     ['id'=>5,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[29]],
        //     ['id'=>6,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[32,33]],
        //     ['id'=>7,'evaluation_class_id'=>1,'type'=>2,'option_content'=>'','options'=>[35,37]],
        //     //
        //     ['id'=>8,'evaluation_class_id'=>2,'type'=>2,'option_content'=>'','options'=>[40,42,43]],
        //     //
        //     ['id'=>9,'evaluation_class_id'=>3,'type'=>2,'option_content'=>'','options'=>[59,60,61,62]],
        //     //
        //     ['id'=>10,'evaluation_class_id'=>4,'type'=>1,'option_content'=>'dsfasf','options'=>[112]],
        //     ['id'=>11,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[69]],
        //     ['id'=>12,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[71]],
        //     ['id'=>13,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[73]],
        //     ['id'=>14,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[76]],
        //     ['id'=>15,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[78]],
        //     ['id'=>16,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[79]],
        //     ['id'=>17,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[81]],
        //     ['id'=>18,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[83]],
        //     ['id'=>19,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[85]],
        //     ['id'=>20,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[87]],
        //     ['id'=>21,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[90]],
        //     ['id'=>22,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[92]],
        //     ['id'=>23,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[94]],
        //     ['id'=>24,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[96]],
        //     ['id'=>25,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[98]],
        //     ['id'=>26,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[100]],
        //     ['id'=>27,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[102]],
        //     ['id'=>28,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[104]],
        //     ['id'=>29,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[106]],
        //     ['id'=>30,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[107]],
        //     ['id'=>31,'evaluation_class_id'=>4,'type'=>2,'option_content'=>'','options'=>[109]]
        // ];
        $user_id = $this->userId;
        // $user_id = 2;
        // echo '<br/>';
        // echo $user_id;
        json_success($this->services->create($question,$user_id));


    }
    /**
     * 关爱调研提交
     * Date: 2022/7/30
     * Time: 11:49
     * USER:GCQ
     */
    public function orderResearchCreate()
    {
        $order_id = $this->request->post('order_id/d',0);
        $user_id = $this->userId;
        $family_relation = $this->request->post('family_relation/a',array());

        if(!$family_relation)json_success(res_data(0,'请完善家庭关系态度量表问答'));
        $scenes_one = $this->request->post('scenes_one');
        $scenes_two = $this->request->post('scenes_two');
        $scenes_three = $this->request->post('scenes_three');
        $scenes_four = $this->request->post('scenes_four');
        $scenes_five = $this->request->post('scenes_five');
        //语音时长
        $scenes_one_time = $this->request->post('scenes_one_time/d',0);  //'场景1 (语音)时长',
        $scenes_two_time = $this->request->post('scenes_two_time/d',0);  //'场景2 (语音)时长',
        $scenes_three_time = $this->request->post('scenes_three_time/d',0);  //'场景3 (语音)时长',
        $scenes_four_time = $this->request->post('scenes_four_time'); //'场景4 (语音)时长',
        $scenes_five_time = $this->request->post('scenes_five_time'); //'场景5 (语音)时长',


        $data['family_relation'] = $family_relation;
        $data['scenes_one'] = $scenes_one;
        $data['scenes_two'] = $scenes_two;
        $data['scenes_three'] = $scenes_three;
        $data['scenes_four'] = $scenes_four;
        $data['scenes_five'] = $scenes_five;

        $data['scenes_one_time'] = $scenes_one_time;
        $data['scenes_two_time'] = $scenes_two_time;
        $data['scenes_three_time'] = $scenes_three_time;
        $data['scenes_four_time'] = $scenes_four_time;
        $data['scenes_five_time'] = $scenes_five_time;

        json_success($this->services->research_create($order_id,$data,$user_id));
    }
    public function is_pending()
    {
        $user_id = $this->userId;
        json_success($this->services->is_pending($user_id));
    }
}