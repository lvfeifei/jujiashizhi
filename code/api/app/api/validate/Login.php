<?php

namespace app\api\validate;

use think\Validate;

class Login extends Validate
{

    protected $regex = [
        'pwd' => '//'
    ];

    protected $rule = [
        'pwd' => 'require|length:6,12',
        'cpwd' => 'require|confirm:pwd',
        'email' => 'require|email'
    ];

    protected $message = [
        'pwd.require' => '缺少密码参数',
        'pwd.length' => '密码长度应为6-12个字符之间',
        'cpwd.require' => '缺少确认密码参数',
        'cpwd.confirm' => '密码和确认密码不一致',
        'email.require' => '缺少邮箱参数',
        'email.email' => '邮箱格式错误',
    ];

    protected $scene = [
        'data_completion' => ['pwd','cpwd','email'],
        'pwd' => ['pwd','cpwd']
    ];
}