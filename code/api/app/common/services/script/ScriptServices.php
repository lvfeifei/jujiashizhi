<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/8/1
     * Time: 18:11
     */
namespace app\common\services\script;


use app\common\services\BaseServices;
use app\common\services\user\UserServices;

class ScriptServices extends BaseServices
{
    public function setModel()
    {
    }

    public function set_refresh_greement_tatus()
    {
        $userServices = new UserServices();

        $userServices->model
            ->where('is_agree',2)
            ->chunk(100,function ($user) use ($userServices){
                foreach ($user as $item) {
                    try {
                        $userServices->model
                            ->where('is_agree',2)
                            ->update(['is_agree' => 1]);
                        echo '用户id:'.$item['id'].',修改成功.'.PHP_EOL;
                    }catch (\Throwable $e){
                        echo '用户id:'.$item['id'].',修改失败.'.PHP_EOL;
                    }
                }

            });
    }

}