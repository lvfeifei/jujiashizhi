<?php
namespace app\api\controller;

use app\common\services\user\UserServices;


use think\console\Input;
use think\Request;

/**
 * Class User
 * @package app\api\controller
 */
class User extends Basic
{
    public $services;
    public function __construct(Request $request = null,UserServices $userServices)
    {
        parent::__construct($request);
        $this->services = $userServices;
    }

    /**
     * 个人中心
     * @return void
     */
    public function index() {
        $user_id = input('user_id');
        if (!$user_id) {
            json_success(res_data(0,'获取用户id失败'));
        }
        $user_id = decode($user_id);
        json_success($this->services->getUserInfo($user_id));
    }

    //我的记录用户信息
    public function user()
    {
        $user_id = $this->userId;
        json_success($this->services->getUserInfo($user_id));
    }
    public function agreement()
    {
        $user_id = $this->userId;
        json_success($this->services->set_agreement($user_id));

    }
    /**
     * 我的记录 历史照护方案
     * Date: 2022/8/10
     * Time: 19:46
     * USER:GCQ
     */
    public function my_history()
    {

        $user_id = $this->userId;
        json_success($this->services->order_history($user_id));

    }

    /**
     * 修改头像
     * @return void
     */
    public function avatar() {
        $avatar = $this->request->post('avatar','');
        if (!$avatar)json_fail('缺少头像地址参数');
        $res = $this->services->model->where('id',$this->userId)->update(['avatar' => $avatar]);
        if (!$res)json_fail('修改失败');
        //积分增加
        $CreditBillServices = new CreditBillServices();
        if (!$CreditBillServices->isAvatar($this->userId)) $CreditBillServices->integralChange($this->userId,'avatar',5,'上传头像');
        json_success('修改成功');
    }



    //照护者信息
    public function userSave() {
        //照护者
        $user_id = $this->userId;
        $gender = $this->request->post('gender','S1');//性别
        $age =  $this->request->post('age/d','0');//年龄
        if($age > 100 || $age <= 0){
            json_success(res_data(0,'设置照护者年龄段在0-100之间'));
        }
        $education =  $this->request->post('education','U1');//教育
        $care_years =  $this->request->post('care_years','V1');//照顾年限
        $relation =  $this->request->post('relation','W1');//与患者关系
        $live =  $this->request->post('live','X1');//是否与患者同居

        //患者
        $patient_gender =  $this->request->post('patient_gender','L');//性别
        $patient_age =  $this->request->post('patient_age/d','0');//年龄
        if($patient_age > 150 || $patient_age <= 0){

            json_success(res_data(0,'设置患者年龄段在0-150之间'));
        }
        $patient_education =  $this->request->post('patient_education','N1');//教育
        $patient_disease_type =  $this->request->post('patient_disease_type','O1');//患者疾病类型
        $patient_illness =  $this->request->post('patient_illness','P1');
        $patient_hobby =  $this->request->post('patient_hobby/a',array());
        $patient_walk =  $this->request->post('patient_walk','R1');



        $data = [
        'gender' => $gender,
        'age' => $age,
        'education' => $education,
        'care_years' => $care_years,
        'relation' => $relation,
        'live' => $live,
        'patient_gender' => $patient_gender,
        'patient_age' => $patient_age,
        'patient_education' => $patient_education,
        'patient_disease_type' => $patient_disease_type,
        'patient_illness' => $patient_illness,
        'patient_hobby' => json_encode($patient_hobby),
        'patient_walk' => $patient_walk,
        ];
        if(in_array('Q8',$patient_hobby)){
            $patient_hobby_content = $this->request->post('patient_hobby_content');
            $data['patient_hobby_content'] = $patient_hobby_content;
        }
        json_success($this->services->save($user_id,$data));
    }

    /**
     * 评价提交
     * Date: 2022/8/7
     * Time: 17:15
     * USER:GCQ
     */
    public function evaluate_save(){
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        $user_id = $this->userId;
        $data = $this->request->post('program/a', array());

        if(!$data)json_success(res_data(0,'评价问题选项不能为空'));
        json_success($this->services->evaluate_save($order_id,$data,$user_id));
    }

    /**
     * 绑定养老院
     */
    public function bind_bead_house()
    {
        $user_id = $this->userId;
        $id = input('id');
        if(empty($id))json_success(res_data(0,'养老院id不能为空'));
        json_success($this->services->set_bind_bead_house($user_id,$id));
    }

    //解绑养老院
    public function unbind_bead_house()
    {
        $user_id = $this->userId;
        // $id = input('id');
        // if(empty($id))json_success(res_data(0,'养老院id不能为空'));
        json_success($this->services->set_unbind_bead_house($user_id));
    }





    /**
     * 访问用户信息
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userHome() {
        $toUserId = $this->request->get('user_id/d',0);
        $userId = 0;
        if (isset($this->userId) && !empty($this->userId)) $userId = $this->userId;
        json_success($this->services->userHome($toUserId,$userId));
    }



    /**
     * 设置皮肤
     * @return void
     */
    public function setSkin() {
        $skinLink = $this->request->post('picture','');
        if (!$skinLink)json_fail('缺少皮肤链接地址参数');
        $UserServices = new UserServices();
        $res = $UserServices->model->where('id',$this->userId)->update(['skin_img' => $skinLink]);
        if ($res === false)json_fail('修改失败');
        json_success('修改成功');
    }




}
