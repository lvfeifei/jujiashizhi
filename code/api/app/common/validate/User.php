<?php


namespace app\common\validate;


use think\Validate;

class User extends Validate
{
    protected $rule = [
        'id' => 'require',
        'mobile' => 'require',
        'order_count' => 'require',
        'Identity' => 'require',
        'total_price' => 'require',
        'withdraw' => 'require',
        'balance' => 'require',
        'wx_user_id' => 'require',
        'bind_wx_time' => 'require',
        'bind_time' => 'require',
        'create_time' => 'require',
        'status' => 'require',
        'old_nickname' => 'require',
        'avatar_url' => 'require',
        'sex' => 'require',
        'username' => 'require',

    ];

    protected $message = [
        'id' => '缺少ID',
        'mobile' => '缺少电话',
//        'order_count' => 'require',
//        'Identity' => '缺少身份',
//        'total_price' => 'require',
//        'withdraw' => 'require',
//        'balance' => 'require',
//        'wx_user_id' => 'require',
//        'bind_wx_time' => 'require',
//        'bind_time' => 'require',
//        'create_time' => 'require',
        'status' => '缺少状态',
        'old_nickname' => '缺少昵称',
        'avatar_url' => '缺少头像',
        'sex' => '缺少性别',
        'username' => '缺少姓名',
    ];

    protected $scene = [
        'add' => [
            'mobile',
            'status',
            'old_nickname',
            'avatar_url',
            'sex',
            'username',
        ],

        'update' => [
            'id',
            'mobile',
            'status',
            'old_nickname',
            'avatar_url',
            'sex',
            'username',
        ],


        'edit' => ['id'],
        'del' => ['id'],
    ];
}