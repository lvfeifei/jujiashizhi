<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/7/26
 * Time: 16:28
 */

namespace app\api\controller;

use app\common\services\help\HelpServices;
use think\Request;
use think\Db;
class Help extends Basic
{
    public $services;
    public function __construct(Request $request = null,HelpServices $helpServices)
    {
        parent::__construct($request);
        $this->services = $helpServices;
    }

    /**
     * 资讯列表
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/27
     * Time: 15:42
     * USER:GCQ
     */
    public function index()
    {

        $help_class_id = $this->request->post('help_class_id/d',1);

        $where['help_class_id'] = $help_class_id;
        json_success($this->services->api_help_list($where));
    }

    /**
     * 详情
     * Date: 2022/7/27
     * Time: 15:52
     * USER:GCQ
     */
    public function details()
    {
        $help_id = $this->request->post('help_id/d',0);
        json_success($this->services->get_details($help_id));
    }


}