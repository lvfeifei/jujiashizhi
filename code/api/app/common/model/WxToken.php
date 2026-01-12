<?php

namespace app\common\model;

use think\Model;

/**
 * 微信用户登录表
 * Class WechatUser
 * @package app\common\model
 */
class WxToken extends Model
{
    protected $table='cx_wx_token';
    protected $pk='id';
}
