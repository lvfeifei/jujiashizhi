<?php

namespace app\common\services\user;

use app\common\lib\exception\AdminException;
use app\common\lib\exception\ApiException;
use app\common\model\WechatUser;
use app\common\services\BaseServices;
use jwt\JWTServices;
use think\Cache;

class WechatUserServices extends BaseServices
{

     public function setModel()
     {
        $this->model = new WechatUser();
     }


    /**
     * 获取一个openid用户
     * @param $openid
     * @return mixed
     */
     public function getWechatUserInfo($openid) {
         $info = $this->model->where('openid',$openid)->find();
         return $info;
     }

    /**
     * 创建wechat_user用户
     * @param $wechatUser
     * @return int|string
     * @throws ApiException
     */
     public function createWechatUser($wechatUser) {
         $wechatUser['create_time'] = time();
         $wechatUser['update_time'] = time();
         $wid = $this->model->insertGetId($wechatUser);
         if (!$wid) throw new ApiException('生成微信用户失败');
         return $wid;
     }

}