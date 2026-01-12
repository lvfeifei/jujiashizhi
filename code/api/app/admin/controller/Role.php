<?php

namespace app\admin\controller;

use app\admin\model\SystemRole;
use think\Controller;
use think\Db;

/**
 * 角色
 */
class Role extends Basic
{
    // 初始化
    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 返回所有菜单
     */
    public function getAllMenu()
    {
        //实例化数据库
        $SystemMenu = new \app\admin\model\SystemMenu();
        //查询条件 不显示status=0删除的数据
        $map['status'] = array('neq', 0);
        //查询数据
        $infoList = $SystemMenu->where($map)->order('sort')->field('id,pid,name')->select();
        //获得树形结构数据
        $info = menuTree($infoList, 0);
        if (!$info) {
            $info=[];
        }
        json_success(['status'=>1,'msg'=>'请求成功！','data'=>$info]);
    }


    /**
     * 角色列表
     */
    public function index()
    {
        $page = Input('page', 1);
        $limit = Input('limit', 10);
        //实例化数据库
        $systemRole = new SystemRole();
        $res=$systemRole->where(array('status'=>array('neq','0')))->where('id','neq',11)->field('menu_id',true)->page($page,$limit)->order('id desc')->select();
        $count = 0;
        $list = [];
        if ($res) {
            foreach ($res as $k=>$v){
            //     if($v['id'] == 11){
            //
            //     }else{
                    $list[] = $v;
            //     }
            }
            $count=$systemRole->where('status', 'neq',0)->where('id','neq',11)->count();

        }
        $infoList = [
            'list' => $list,
            'count' => $count,
            ];
        json_success(['status'=>1,'msg'=>'请求成功！','data'=>$infoList]);
    }

    /**
     * 添加角色
     */
    public function add()
    {
        //实例化数据库
        $name = input('name');
        $sort= input('sort',1);
        $status= input('status',1);
        $menu_ids = input('menu_ids/a');
        if (!$name) {
            json_success(['status'=>0,'msg'=>'角色名不能为空！']);
        }
        if (!$menu_ids) {
            json_success(['status'=>0,'msg'=>'菜单id不能为空！']);
        }
        //实例化数据库
        $systemRole = new SystemRole();
        $is_true = $systemRole->isTrue($name);
        if ($is_true) {
            json_success(['status'=>0,'msg'=>'角色名已存在！']);
        }
        $system_menu = new \app\admin\model\SystemMenu();
        foreach ($menu_ids as $k=>$v){
            $system_menu_info = $system_menu->where('id',$v)->find();
            if($system_menu_info){
                if($system_menu_info['pid'] == 0){
                    unset($menu_ids[$k]);
                }
            }
        }
        $menu_ids_arr = [];
        foreach ($menu_ids as $kk => $vv){
            $system_menu_info1 = $system_menu->where('id',$v)->find();
            if($system_menu_info1){
                if($system_menu_info1['pid'] != 0){
                    if(!in_array($system_menu_info1['id'], $menu_ids_arr)){
                        $menu_ids_arr[] = $system_menu_info1['id'];
                    }
                    $system_menu_info_p = $system_menu->where('id',$system_menu_info1['pid'])->find();
                    if($system_menu_info_p){
                        if($system_menu_info_p['pid'] == 0){
                            if(!in_array($system_menu_info_p['id'], $menu_ids_arr)){
                                $menu_ids_arr[] = $system_menu_info_p['id'];
                            }
                        }
                    }
                }

            }

        }
        //要添加的数据
        $addData = array(
            'name'=>$name,
            'sort'=>$sort,
            'menu_id'=>json_encode($menu_ids),
            'status'=>$status
        );
        $id = $systemRole->insertGetId($addData);
        if (!$id) {
            json_success(['status'=>0,'msg'=>'添加失败！']);
        } else {
            json_success(['status'=>1,'msg'=>'添加成功！']);
        }
    }

    /**
     * 展示修改
     */
    public function show_edit(){
        $role_id = input('role_id');
        if(!$role_id){
            json_success(['status'=>0,'msg'=>'获取角色id错误！']);
        }
        //实例化数据库
        $systemRole = new SystemRole();
        $info = $systemRole->where(array('id'=>$role_id))->find();

        if($info){
            $menu_id = json_decode($info['menu_id']);
            $system_menu = new \app\admin\model\SystemMenu();
            if($menu_id){

                foreach ($menu_id as $k => $v){
                    $syste_menu_arr = $system_menu->where('id',$v)->find();
                    if($syste_menu_arr){
                        if($syste_menu_arr['pid'] == 0){
                            unset($menu_id[$k]);
                        }
                    }
                }
            }
            $info['menu_id'] = [];
            if($menu_id){
                $id_arr = [];
                foreach ($menu_id as $kk=>$vv){
                    $id_arr[] =  $vv;
                }
                $info['menu_id'] = $id_arr;
            }


        }else{
            $info = [];
        }
        json_success(['status'=>1,'msg'=>'请求成功！','data'=>$info]);
    }
    public function show_edit1(){
        $role_id = input('role_id');
        if(!$role_id){
            json_success(['status'=>0,'msg'=>'获取角色id错误！']);
        }
        //实例化数据库
        $systemRole = new SystemRole();
        $system_menu = new \app\admin\model\SystemMenu();
        $info = $systemRole->where(array('id'=>$role_id))->find();

        if($info){
            $menu_id = json_decode($info['menu_id']);
            //处理menu_id
            $menu_list = $system_menu->where(array('id'=>array('in',$menu_id),'pid'=>0,'status'=>1))->select();
            if($menu_list){
                foreach ($menu_list as $k=>$v){
                    $child_list = $system_menu->where(array('pid'=>$v['id'],'status'=>1))->column('id');
                    if($child_list){
                        foreach ($child_list as $key=>$value){
                            if(!in_array($value,$menu_id)){
                                $a = array_search($v['id'],$menu_id);
                                unset($menu_id[$a]);
                                break;
                            }
                        }
                    }
                }
                $menu_id = array_merge($menu_id);
            }
            $info['menu_id'] = $menu_id;

        }else{
            $info = [];
        }
            json_success(['status'=>1,'msg'=>'请求成功！','data'=>$info]);
    }

    /**
     * 修改
     */
    public function edit()
    {
        //实例化数据库
        $role_id = input('role_id');
        $name = input('name');
        $sort= input('sort',1);
        $status= input('status',1);
        $menu_ids = input('menu_ids/a');
        if(!$role_id){
            json_success(['status'=>0,'msg'=>'获取角色id错误！']);
        }
        if (!$name) {
            json_success(['status'=>0,'msg'=>'角色名不能为空！']);
        }
        if (!$menu_ids) {
            json_success(['status'=>0,'msg'=>'菜单id不能为空！']);
        }
        //实例化数据库
        $systemRole = new SystemRole();
        $system_menu = new \app\admin\model\SystemMenu();
        $is_true = $systemRole->isMineTrue($name,$role_id);
        if ($is_true) {

            json_success(['status'=>0,'msg'=>'角色名已存在！']);
        }


        foreach ($menu_ids as $k=>$v){
            $system_menu_info = $system_menu->where('id',$v)->find();
            if($system_menu_info){
                if($system_menu_info['pid'] == 0){
                    unset($menu_ids[$k]);
                }
            }
        }
        $menu_ids_arr = [];
        foreach ($menu_ids as $kk => $vv){
            $system_menu_info1 = $system_menu->where('id',$vv)->find();
            if($system_menu_info1){
                if($system_menu_info1['pid'] != 0){
                    if(!in_array($system_menu_info1['id'], $menu_ids_arr)){
                        $menu_ids_arr[] = $system_menu_info1['id'];
                    }
                    $system_menu_info_p = $system_menu->where('id',$system_menu_info1['pid'])->find();
                    if($system_menu_info_p){
                        if($system_menu_info_p['pid'] == 0){
                            if(!in_array($system_menu_info_p['id'], $menu_ids_arr)){
                                $menu_ids_arr[] = $system_menu_info_p['id'];
                            }
                        }
                    }
                }

            }

        }

        //要添加的数据
        $saveData = array(
            'name'=>$name,
            'sort'=>$sort,
            'menu_id'=>json_encode($menu_ids_arr),
            'status'=>$status
        );
        $result = $systemRole->where(array('id'=>$role_id))->update($saveData);
        if ($result === false) {
            json_success(['status'=>0,'msg'=>'修改失败！']);
        } else {

            json_success(['status'=>1,'msg'=>'修改成功！']);
        }
    }

    /**
     * 删除
     */
    public function delete_role()
    {
        $role_id = input('role_id');

        if(!$role_id){
            json_success(['status'=>0,'msg'=>'获取角色id错误！']);
        }
        if($role_id ==11){
            json_success(['status' =>0, 'msg'=>'养老院权限不能删除']);
        }
        //实例化数据库
        $systemRole = new SystemRole();
        $info = $systemRole->where(array('id'=>$role_id))->update(array('status'=>0));
        if($info === false){
            json_success(['status'=>0,'msg'=>'删除失败！']);
        }
        json_success(['status'=>1,'msg'=>'删除成功！']);
    }
    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 返回可用所有菜单
     */
    public function can_use_menu()
    {
        //实例化数据库
        $SystemMenu = new \app\admin\model\SystemMenu();
        //查询条件 不显示status=0删除的数据
        $map['status'] = 1;
        //查询数据
        $infoList = $SystemMenu->where($map)->order('sort')->field('id,pid,name')->select();
        //获得树形结构数据
        $info = menuTree($infoList, 0);
        if ($info) {
            json_success(res_data(1,'请求成功',$info));
        } else {
            json_success(res_data(1,'请求成功'));

        }
    }


}
