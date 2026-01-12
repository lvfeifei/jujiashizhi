<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/7/28
 * Time: 11:41
 */

namespace app\common\model;

use think\Model;
class Order extends Model
{
    protected $table = 'cx_order';
    protected $pk = 'id';
}