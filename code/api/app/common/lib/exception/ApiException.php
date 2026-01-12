<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2018/8/3
 * Time: 21:30
 */

namespace app\common\lib\exception;
use think\Exception;
class ApiException extends Exception
{
public $message = '';
    public $HttpCode = 500;
    public $code = 0;
    public function  __construct($message='', $HttpCode=0,$code=0 )
    {
        $this->message= $message;
        $this->HttpCode=$HttpCode;
        $this->code=  $code;
    }
}