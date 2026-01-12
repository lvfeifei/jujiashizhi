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

class SystemMenu extends Model
{

    //获取列表
    function index(){
        $result=$this->where(array('status'=>array('neq','0')))->field('register_time,register_ip,password',true)->order('sort asc')->select();
        if ($result) {
            $result = collection($result)->toArray();//对象转成数组
            return $result;
        } else {
            return [];
        }
    }

    //获取列表
    function show($menu_id){
//        $menu_id = json_decode($menu_id);
        $result=$this->where(array('status'=>1,'id'=>array('in',$menu_id)))->field('register_time,register_ip,password',true)->order('sort asc')->select();
        if ($result) {
            $result = collection($result)->toArray();//对象转成数组
            return $result;
        } else {
            return [];
        }
    }

    //添加
    function add($data)
    {
        $result = $this->isUpdate(false)->allowField(true)->save($data);
        if ($result) {
            return $this->id;
        } else {
            return false;
        }
    }

    //判断用户名是否存在
    function isTrue($data)
    {
        if (isset($data['name'])) {
            $result = $this->where(array('name' => $data['name']))->find();
            if ($result) {
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
