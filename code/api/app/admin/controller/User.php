<?php

namespace app\admin\controller;

use app\common\services\credit\CreditServices;
use app\common\services\order\OrderServices;
use app\common\services\recharge\RechargeServices;
use app\common\services\user\CreditBillServices;
use app\common\services\user\UserBillServices;
use app\common\services\user\UserContactServices;
use app\common\services\user\UserMoneyFrozenServices;
use app\common\services\user\UserServices;
use think\Request;

class User extends Basic
{
    protected $services;
    public function __construct(Request $request = null,UserServices $userServices)
    {
        parent::__construct($request);
        $this->services = $userServices;
    }

    /**
     * 用户列表
     * @return void
     */
    public function index() {
        $where['key'] = $this->request->post('key','');
        $where['gender'] = $this->request->post('gender');
        $where['education'] = $this->request->post('education');
        $where['care_years'] = $this->request->post('care_years');
        $where['relation'] = $this->request->post('relation');
        $where['live'] = $this->request->post('live');
        $where['bead_house_id'] = $this->request->post('bead_house_id');
        $user_id = $this->user_id;
        $role_id = $this->role_id;
        json_success($this->services->adminList($where, $user_id, $role_id));
    }


    /**
     * 用户详情
     * @return void
     */
    public function details() {
        $id = $this->request->post('id/d',0);
        if (!$id)json_fail('缺少用户id参数');
        json_success($this->services->adminDetails($id));
    }
    /**
     * 用户详情  患者列表
     * @return void
     */
    public function patient_list() {
        $id = $this->request->post('id/d',0);
        if (!$id)json_fail('缺少用户id参数');
        json_success($this->services->admin_evaluation_list($id));
    }



}