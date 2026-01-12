<?php

namespace app\api\controller;

use \app\common\model\Config as ConfigModel;
use think\Request;
/**
 * 配置管理
 * Class app\admin\controller\Config
 */
class Config extends Basic
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);

    }

    /**
     * 配置查看
     * @author yangxiuchuan
     * @date 2022-04-18
     * @return void
     */
    public function index() {
        $key = input('key');
        $config = new ConfigModel();
        //所传ID是否在已有数组中
        if (!in_array($key,$config->keys)){
            json_fail('key无效');
        }
        $info = $config->where('key',$key)->find();
        if (!$info) $info = [];
        json_success($info);
    }

}