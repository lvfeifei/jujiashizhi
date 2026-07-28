<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2022/10/22
 * Time: 2:37 AM
 */

namespace app\admin\controller;


use app\common\services\care\CareAdviceServices;
use think\Request;
class CareAdvice extends Basic
{
    /**
     *
     * @var EvaluationCapabilityServices
     */
    protected $services;

    public function __construct(Request $request = null, CareAdviceServices $careAdviceServices)
    {
        parent::__construct($request);
        $this->services = $careAdviceServices;
    }

    public function advice()
    {
        $user_id = $this->request->post('user_id/d',0);
        if(empty($user_id)){
           json_fail('用户id不能为空');
        }
        $ids = $this->request->post('ids/a',array());
        if(!$ids)json_success(res_data(0,'请选择问题选项'));
        json_success($this->services->get_advice($user_id,$ids));
    }

    public function graphrag_advice()
    {
        $user_id = $this->request->post('user_id/d',0);
        if(empty($user_id)){
           json_fail('用户id不能为空');
        }
        $question = $this->request->post('question/s', '');
        if(!$question)json_success(res_data(0,'请输入问题'));
        json_success($this->services->get_graphrag_advice($user_id,$question));
    }
}
