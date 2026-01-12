<?php


namespace app\admin\controller;


class City extends Basic
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $City = new \app\common\model\City();
        $list=  $City->field('id,city_name')->where('status',1)->select();
        json_success($list);
    }



}