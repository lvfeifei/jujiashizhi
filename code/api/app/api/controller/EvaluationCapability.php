<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/28
     * Time: 9:57
     */

namespace app\api\controller;

use app\common\services\evaluationcapability\EvaluationCapabilityServices;
use think\Request;
use think\Db;
class EvaluationCapability extends Basic
{
    public $services;
    public function __construct(Request $request = null,EvaluationCapabilityServices $evaluationCapabilityServices)
    {
        parent::__construct($request);
        $this->services = $evaluationCapabilityServices;
    }
    //根据分类id获取测评问题
    public function question()
    {
        $evaluation_class_id = $this->request->post('evaluation_class_id/d',0);
        json_success($this->services->get_question($evaluation_class_id));

    }

}