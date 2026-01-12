<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/8/1
     * Time: 18:21
     */
namespace app\admin\controller;

use app\common\services\setting\SettingServices;
use think\Request;
class Setting extends Basic
{

    /**
     * 配置管理
     * Class app\admin\controller\Config
     */
    protected $services;
    public function __construct(Request $request = null,SettingServices $settingServices)
    {
        parent::__construct($request);
        $this->services = $settingServices;
    }

    /**
     *
     */
    public function expert_intervene()
    {
        json_success($this->services->adminExpertIntervene());
    }
    public function save()
    {
        $expertavatar = $this->request->post('expertAvatar');
        $careplan = $this->request->post('carePlan/d');
        $sendtime = $this->request->post('sendTime');
        $data['expertAvatar'] = $expertavatar;
        $data['carePlan'] = $careplan;
        $data['sendTime'] = $sendtime;


        json_success($this->services->adminExpertInterveneSave($data));
    }

}
