<?php


namespace app\common\validate;


use think\Validate;

class Login extends Validate
{

    protected $rule = [
        'mobile'=>'require|number|alphaNum|length:11',
    ];

    protected $message = [
        'mobile.require'=>'请输入手机号',
        'mobile.number'=>'格式错误',
        'mobile.alphaNum'=>'格式错误',
        'mobile.length'=>'请输入正确手机号'
    ];

    protected $scene = [
        'add' => [

        ],

        'update' => [
            'mobile'
        ],
        'edit' => ['id'],
        'del' => ['id'],
    ];

}