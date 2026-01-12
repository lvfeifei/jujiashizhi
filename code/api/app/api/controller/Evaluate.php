<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/28
     * Time: 9:57
     */

namespace app\api\controller;

use app\common\services\evaluate\EvaluateServices;

use think\Request;
use think\Db;
class Evaluate extends Basic
{
    public $services;
    public function __construct(Request $request = null,EvaluateServices $evaluateServices)
    {
        parent::__construct($request);
        $this->services = $evaluateServices;
    }
    //评价问题
    public function index()
    {

        $order_id = $this->request->post('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        $user_id = $this->userId;
        json_success($this->services->get_evaluate($order_id,$user_id));
    }

    //判断 评价开始时间并弹窗提示
    public function is_pop_up()
    {
        $user_id = $this->userId;
        json_success($this->services->is_pop_up($user_id));
    }




}