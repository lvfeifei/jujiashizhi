<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/7/25
 * Time: 14:04
 */

namespace app\admin\controller;

use app\common\services\helpclass\HelpClassServices;
use think\Request;
class HelpClass extends Basic
{
    /**
     * 资讯分类
     * @var HelpClassServices
     */
    protected $services;
    public function __construct(Request $request = null,HelpClassServices $helpClassServices)
    {
        parent::__construct($request);
        $this->services = $helpClassServices;
    }
    public function index()
    {
            $help_class = $this->services->get_help_class();
            json_success(res_data(1,'请求成功',$help_class));

    }

}