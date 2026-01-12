<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/29
     * Time: 20:42
     */
namespace app\common\services\familyrelation;


use app\common\model\FamilyRelation;
use app\common\model\Scenes;
use app\common\services\BaseServices;
use think\Db;
use think\Request;
class FamilyRelationServices extends BaseServices
{
    //选项
    public $options = [
        1 => '从不',
        2 => '偶尔',
        3 => '有时',
        4 => '每天',
    ];

    public function setModel()
    {
        $this->model = new FamilyRelation();
    }

    /**
     * 关爱调研问题
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/30
     * Time: 11:20
     * USER:GCQ
     */
    public function get_list()
    {
        $list = $this->model->select();
        if($list){
            foreach ($list as $item){
                $item['options'] = $this->options;
            }
        }else{
            $list = [];
        }
        return res_data(1,'请求成功',$list);
    }

    public function get_scenes()
    {
        $scenes = new Scenes();
        $list = $scenes->select();
        if($list){

        }else{
            $list = [];
        }
        return res_data(1,'请求成功',$list);
    }

}