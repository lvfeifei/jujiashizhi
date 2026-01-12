<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/8/12
 * Time: 17:39
 */

namespace app\admin\controller;

use app\common\services\export\ExportServices;
use think\Request;
class Export extends Basic
{
    public $services;
    public function __construct(Request $request = null, ExportServices $exportServices)
    {
        parent::__construct($request);
        $this->services = $exportServices;
    }

    /**
     * 导出用户 和患者信息， 照护方案
     * Date: 2022/8/12
     * Time: 17:47
     * USER:GCQ
     */
    public function export_excel()
    {
        // $order_id = $this->request->post('id/d',0);
        $order_id = $this->request->get('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));
        json_success($this->services->export_patient_excel($order_id));
    }

    /**
     * 下载语音
     * Date: 2022/8/12
     * Time: 17:48
     * USER:GCQ
     */
    public function export_scenes()
    {
//        $order_id = $this->request->post('id/d',0);
         $order_id = $this->request->get('id/d',0);
        if(!$order_id)json_success(res_data(0,'参数id不能为空'));

        json_success($this->services->export_scenes($order_id));
    }

    /**
     * 导出数据分析
     * Date: 2022/8/12
     * Time: 17:48
     * USER:GCQ
     */
    public function export_analyze()
    {
        json_success($this->services->export_analyze_excel());
    }
}