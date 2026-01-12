<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/29
     * Time: 20:25
     */

namespace app\api\controller;

use app\common\services\familyrelation\FamilyRelationServices;
use think\Request;
use think\Db;
class FamilyRelation extends Basic
{
    public $services;
    public function __construct(Request $request = null,FamilyRelationServices $familyRelationServices)
    {
        parent::__construct($request);
        $this->services = $familyRelationServices;
    }

    /**
     * 家庭关系态度量表
     * Date: 2022/7/30
     * Time: 11:00
     * USER:GCQ
     */
    public function index()
    {
        json_success($this->services->get_list());
    }

    /**
     * 关爱调研场景模板
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/30
     * Time: 11:39
     * USER:GCQ
     */
    public function scenes()
    {
        json_success($this->services->get_scenes());
    }
}