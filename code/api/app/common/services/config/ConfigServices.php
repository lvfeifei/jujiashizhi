<?php

namespace app\common\services\config;

use app\common\model\Config;
use app\common\services\BaseServices;

class ConfigServices extends BaseServices
{
    public function setModel()
    {
        $this->model = new Config();
    }



}