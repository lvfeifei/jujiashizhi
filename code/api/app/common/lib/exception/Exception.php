<?php

/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2018/8/3
 * Time: 21:17
 */
namespace  app\common\lib\exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;

class Exception extends Handle
{
    public  $httpCode = 500;
    public  function render(\Exception $e)
    {
        // 参数验证错误
        if ($e instanceof ValidateException) {
            return json($e->getError(), 422);
        }

        // 请求异常
        if ($e instanceof HttpException && request()->isAjax()) {
            return response($e->getMessage(), $e->getStatusCode());
        }

        if ($e instanceof ApiException){
            return json_fail($e->message);
        }
        if ($e instanceof AdminException){
            return json_fail($e->getMessage());
        }

        //可以在此交由系统处理
        return parent::render($e);
    }
}