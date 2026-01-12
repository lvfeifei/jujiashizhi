<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/7/25
 * Time: 14:09
 */
namespace app\common\services\helpclass;

use app\common\model\HelpClass;
use app\common\services\BaseServices;
use think\Db;
use think\Request;
class HelpClassServices extends BaseServices
{
    public function setModel()
    {
        $this->model = new HelpClass();
    }

    /**
     *  获取资讯分类
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 16:38
     * USER:GCQ
     */
    public function get_help_class()
    {
        $help_class = $this->model->select();
       return $help_class;
    }


}