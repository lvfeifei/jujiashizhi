<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2022/11/23
 * Time: 12:13 PM
 */

namespace app\admin\controller;

use app\common\services\script\ScriptServices;
use think\Request;
use think\db;
class Script extends Basic
{
    public $services;
    public function __construct(Request $request = null, ScriptServices $scriptServices)
    {
        parent::__construct($request);
        $this->services = $scriptServices;
    }

    public function refresh_greement_tatus()
    {
        $this->services->set_refresh_greement_tatus();
    }

    //照护方案表刷新新加的字段
    // public function refresh_test(){
    //     $a = 1;
    //     Db::name('order_program')
    //         ->where('user_id',0)
    //         ->chunk(1000,function ($o) use($a){
    //         foreach ($o as $v){
    //
    //            $order_info =  Db::name('order')->where('id',$v['order_id'])->field('user_id')->find();
    //             if($order_info){
    //                 Db::name('order_program')->where('order_id',$v['order_id'])->update(['user_id' => $order_info['user_id']]);
    //                 echo 'id:'.$v['id'].',同步成功.'.PHP_EOL;
    //             }else{
    //                 echo 'id:'.$v['id'].',同步失败.'.PHP_EOL;
    //             }
    //         }
    //         });
    //
    // }

}