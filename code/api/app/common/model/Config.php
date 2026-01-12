<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2018/3/12
 * Time: 14:05
 */
namespace app\common\model;
use think\Model;
class Config extends Model
{
    protected $table='cx_config';
    protected $pk='id';

    public $keys = [
        'helpType',
        'about_us',
        'job_offer',
        'service_agreement',
        'user_agreement',
        'privacy_policy',
        'community',
        'expertAvatar',
        'carePlan',
        'sendTime',
        'disclaimer',
        'privacy',
    ];

    public $values = [
        'about_us'          => '关于我们',
        'job_offer'         => '工作机会',
        'service_agreement' => '服务协议',
        'user_agreement'    => '用户协议',
        'privacy_policy'    => '隐私政策',
        'community'         => '社区公约',
    ];

}
