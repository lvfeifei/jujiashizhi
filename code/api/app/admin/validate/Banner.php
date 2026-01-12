<?php

namespace app\admin\validate;

use think\Validate;

class Banner extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'picture' => 'require',
        'url_type' => 'require',
//        'position' => 'require'
    ];

    protected $message = [
        'id' => '缺少ID',
        'title' => '标题名必须',
        'picture' => '广告图片必填',
        'url_type' => '缺少url类型',
//        'position' => '缺少位置'

    ];


    protected $scene = [
        'add' => [
            'title'
            , 'picture'
            , 'url_type'
//            , 'position'
        ],

        'update' => [
            'id'
            , 'title'
            , 'picture'
            , 'url_type'
//            , 'position'
        ],


        'edit' => ['id'],
        'del' => ['id'],
    ];
}