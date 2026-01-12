<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/8/12
     * Time: 8:43
     */

namespace app\admin\controller;
use app\common\services\analyze\AnalyzeServices;
use think\Request;
class Analyze extends Basic
{
    /**
     *数据分析控制器
     */
    private $services;
    public function __construct(Request $request = null,AnalyzeServices $analyzeServices)
    {
        parent::__construct($request);
        $this->services = $analyzeServices;
    }

    /**
     *数据分析
     */
    public function analyze()
    {

        json_success($this->services->analyze());
    }

}