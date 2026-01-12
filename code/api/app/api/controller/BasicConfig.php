<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/27
     * Time: 16:53
     */

namespace app\api\controller;
use app\common\services\basicconfig\BasicConfigServices;
use think\Request;
class BasicConfig extends Basic
{
    protected $services;
    public function __construct(Request $request = null,BasicConfigServices $BasicConfigServices)
    {
        parent::__construct($request);
        $this->services = $BasicConfigServices;
    }

    public function index()
    {
        json_success($this->services->get_basic_config());
    }
}