<?php

namespace app\common\lib\exception;

use think\Exception;

class AdminException extends Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}