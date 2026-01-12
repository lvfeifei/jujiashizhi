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

class SystemManager extends Model
{


    //获取列表
    function index($type,$page,$limit)
    {
        //$result = $this->where('status','neq', '0')->order('id desc')->page($page,$limit)->select();
        $result = $this->where('status','neq', '0')->where('role_id', 'neq', 11)->order('id desc')->page($page,$limit)->select();
//        $data['count'] = $this->where('status','neq', '0')->order('id desc')->count();

        if ($result){
            return $result;
        } else {
            return [];
        }
    }


    //获取列表
    function index_two($data)
    {
        $result = $this->where('status','neq', '0')->field('register_ip,password', true)->order('id desc')->select();

         if ($result){
            $result = json_decode(json_encode($result),true);
            $current = [];
            $infoList = [];
            foreach ($result as $k => $v) {
                $v['register_time'] = date('Y-m-d H:i:s', $v['register_time']);
                if ($v['identity'] == 1) {
                    //获取角色名称 
                    $v['role_name'] = '超级管理员';
                } elseif ($v['identity'] == 2) {
                    $v['role_name'] = '审核员';
                } elseif ($v['identity'] == 3) {
                    $v['role_name'] = '专家评审';
                }elseif ($v['identity'] == 4) {
                    $v['role_name'] = '机构';
                }
                $result[$k] = $v;
                if ($v['id'] == $data['id']) {
                    $current = $v;
                    unset($result[$k]);
                }
                $infoList = array_values($result);
            }
            array_unshift($infoList, $current);
            $data['list'] = $infoList;
            $data['count'] = $this->where('status','neq', '0')->count();
            return $data;
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
        if (isset($data['username'])) {
            $result = $this->where(array('username' => $data['username'],'status'=>1))->find();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }

    }


    //判断原密码是否相等
    function isTruePass($data)
    {

        if (isset($data)) {
             // $id=decode($data['id']);
            $result = $this->where(array('id' =>$data['id']))->value('password');


            if ($data['old_password'] == $result) {
                return 1;
            } else {
                return 2;
            }
        }
    }

    //编辑  删除
    function edit($data)
    {
        $result = $this->isUpdate(true)->allowField(true)->save($data);
        return $result;
    }

}