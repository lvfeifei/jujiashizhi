<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/28
     * Time: 11:42
     */
namespace app\common\services\order;

use app\common\model\OrderEvaluation;
use app\common\services\BaseServices;
use app\common\services\user\UserServices;
use app\common\services\evaluationcapability\EvaluationCapabilityServices;
use think\Db;
class OrderEvalustionServices extends BaseServices
{
    public function setModel()
    {
        $this->model = new OrderEvaluation();
    }

}