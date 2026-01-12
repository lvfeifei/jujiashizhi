<?php

namespace app\admin\controller;

use app\common\services\order\OrderServices;
use think\Request;

class Order extends Basic
{
    protected $services;
    public function __construct(Request $request = null,OrderServices $orderServices)
    {
        parent::__construct($request);
        $this->services = $orderServices;
    }
    //测评记录
    public function index(){
        $role_id = $this->role_id;
        $user_id = $this->user_id;
        $where['gender'] = $this->request->post('gender');
        $where['education'] = $this->request->post('education');
        $where['disease_type'] = $this->request->post('disease_type');
        $where['illness'] = $this->request->post('illness');
        $where['walk'] = $this->request->post('walk');
        $where['hobby'] = $this->request->post('hobby');
        $where['status'] = $this->request->post('status/d',0);
        $where['bead_house_id'] = $this->request->post('bead_house_id');
        json_success($this->services->admin_evaluation_list($where,$user_id,$role_id));
    }
    //测评记录

    /**
     * 测评患者详情
     * Date: 2022/8/6
     * Time: 10:27
     * USER:GCQ
     */
    public function details(){
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));

        json_success($this->services->admin_order_details($order_id));
    }

    /**
     * 测评工单患者问题详情
     * Date: 2022/8/6
     * Time: 10:28
     * USER:GCQ
     */
    public function evaluationdetails()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        json_success($this->services->admin_order_evaluationdetails($order_id));
    }

    /**
     * 照护方案详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/6
     * Time: 14:05
     * USER:GCQ
     */
    public function program_details()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        json_success($this->services->admin_order_program_details($order_id));
    }
    //重新发送照护方案
    public function send_program()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        $admin_id = $this->user_id;
        json_success($this->services->admin_send_program_details($order_id,$admin_id));
    }

    /**
     * 专家确认方案
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/7
     * Time: 11:48
     * USER:GCQ
     */
    public function program_save()
    {
        $role_id = $this->role_id;
        if($role_id == 11){
            json_success(res_data('0', '您无权操作'));
        }
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        $data = $this->request->post('program/a',array());
        if(!$data)json_success(res_data(0,'提交照护方案不能为空'));

        json_success($this->services->admin_order_program_save($order_id,$data));
    }

    /**
     * 关爱研究调查
     * Date: 2022/8/15
     * Time: 15:37
     * USER:GCQ
     */
    public function research_family()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        json_success($this->services->admin_order_research_family($order_id));
    }

    /**
     *   请求照护方案  发送失败原因
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/8/15
     * Time: 16:13
     * USER:GCQ
     */
    public function program_send_error()
    {
        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        json_success($this->services->admin_order_program_send_error($order_id));
    }


}