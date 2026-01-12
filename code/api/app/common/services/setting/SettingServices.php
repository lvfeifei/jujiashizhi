<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/8/1
     * Time: 18:11
     */
namespace app\common\services\setting;
use app\common\model\Config;
use app\common\services\BaseServices;
use app\common\services\config\ConfigServices;
class SettingServices extends BaseServices
{
    public function setModel()
    {
    }

    public function adminExpertIntervene()
    {
        $ConfigServices = new ConfigServices();
        $settingInfo = $ConfigServices->whereIn('id',[6,7,8])->whereIn('status',[1,2])->select();
        $setting = [];
        foreach ($settingInfo as $item){
            $setting[$item['key']] = $item['value'];
        }

        return res_data(1,'请求成功',$setting);
    }
    public function adminExpertInterveneSave($data)
    {

        $Config = new Config();
        foreach ($data as $k =>$v){
            //所传ID是否在已有数组中
            if (!in_array($k,$Config->keys)){
                return res_data(0,$k.'无效');
            }
            $saveData = [
                'value' => $v,
            ];
            $res = $Config->where('key',$k)->update($saveData);
        }

        return res_data(1,'保存成功');
    }

}