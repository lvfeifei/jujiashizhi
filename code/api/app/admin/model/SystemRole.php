<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2018/3/12
 * Time: 14:05
 */
namespace app\admin\model;
use think\Model;
class SystemRole extends Model
{

    //获取列表
    function index(){
        $result=$this->where(array('status'=>array('neq','0')))->field('menu_id',true)->order('id desc')->select();
        if ($result) {
            $result = collection($result)->toArray();//对象转成数组
            return $result;
        } else {
            return [];
        }
    }


    //判断用户名是否存在
    function isTrue($name)
    {
        if (isset($name)) {
            $result = $this->where(array('name' => $name))->where('status','neq',0)->find();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }

    }
    //判断用户名是否存在（用于修改角色）
    function isMineTrue($name,$role_id)
    {
        if (isset($name)) {
            $result = $this->where(array('name' => $name,'id'=>array('neq',$role_id)))->where('status','neq',0)->count();
            if ($result > 0) {
                return true;
            } else {
                return false;
            }
        }

    }

    //编辑  删除
    function edit($data){

        $result = $this->isUpdate(true)->allowField(true)->save($data);
        return $result;
    }

}