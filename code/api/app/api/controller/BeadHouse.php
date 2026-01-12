<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/8/12
 * Time: 17:39
 */

namespace app\api\controller;

use app\common\services\beadhouse\BeadHouseServices;
use think\Request;
use think\Validate;

class BeadHouse extends Basic
{
    public $services;

    public function __construct(Request $request = null, BeadHouseServices $beadHouseServices)
    {
        parent::__construct($request);
        $this->services = $beadHouseServices;
    }
    public function bead_house_info()
    {
        $user_id  = $this->userId;
        $id = input('id');
        if(empty($id)){
            json_success(res_data(0,'养老院id不能为空'));
        }
        json_success($this->services->api_bead_house_info($id,$user_id));
    }
    
    
    
}