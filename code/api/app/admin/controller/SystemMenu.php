<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/12
 * Time: 10:34
 */

namespace app\admin\controller;
/**
 * 系统菜单-控制器
 * Class SystemMenu
 * @package app\admin\controller
 */
class SystemMenu extends Basic
{

    /**
     *
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 菜单 - 列表
     */
    public function index()
    {
        $seller_info = new seller_info();
       // $seller_info = new
        $key = Input('post.key');
        $region_id = Input('post.region_id');
        $page = Input('page',1);
        $limit = Input('limit',10);

        $where = '';
        if ($key) {
            $where['name'] = ['like', '%' . $key . '%'];
        }
        if($region_id){
            $where['region_id'] = $region_id;
        }
        $list = $seller_info->index($where,$page,$limit);
        if (!$list) {
            $list = [];
        }
        json_success(['status'=>1,'msg'=>'请求成功！','data'=>$list]);

    }


    /**
     * 验证表单-数据
     * @return array
     * @author jihaichuan
     */
    public function validate_form()
    {
        // 获取上级ID
        $pid = input('post.pid', 0);

        // 获取菜单名称
        $name = input('post.name', '');
        if (!$name) {
            json_success(['status'=>0,'msg'=>'请填写菜单名称！']);
        }

        // 获取菜单图标
        $icon = input('post.icon', '');
        if (($pid == 0) && !$icon) {
            json_success(['status'=>0,'msg'=>'请填写菜单图标！']);
        }

        // 获取菜单地址
        $url = input('post.url', '');

        // 获取排序
        $sort = input('post.sort', 1);

        // 判断状态
        $status = input('post.status', 1);

        // 添加数据
        return array(
            'pid' => $pid,
            'name' => $name,
            'icon' => $icon,
            'url' => $url,
            'sort' => $sort,
            'status' => $status,
        );
    }


    /**
     * 添加 - 系统菜单
     * @author jihaichuan
     */
    public function add()
    {
        // 获取验证表单数据
        $add_data = $this->validate_form();

        //实例化数据库
        $SystemMenuModel = new \app\admin\model\SystemMenu();
        $id = $SystemMenuModel->add($add_data);
        if ($id > 0) {
            json_success([
                'status' => 1,
                'msg' => '系统菜单-添加成功！'
            ]);
        } else {
            json_success(['status'=>0,'msg'=>'系统菜单-添加失败！']);
        }
    }


    /**
     * 修改 - 系统菜单
     * jihaichuan
     */
    public function edit()
    {
        // 获取验证表单数据
        $mod_data = $this->validate_form();

        $id = input('post.id');
        if (!$id) {
            json_success(['status'=>0,'msg'=>'缺少ID参数！']);
        }

        //实例化数据库
//        $SystemMenuModel = new \app\api\model\SystemMenu();
        $SystemMenuModel =new \app\admin\model\SystemMenu();
        $id = $SystemMenuModel->where('id', $id)->update($mod_data);
        if ($id > 0) {
            json_success([
                'status' => 1,
                'msg' => '系统菜单-修改成功！'
            ]);
        } else {
            json_success(['status'=>0,'msg'=>'系统菜单-修改失败！']);
        }
    }

    /**
     * 获取-单个菜单数据
     * @author jihaichuan
     */
    public function info()
    {
        $id = input('id');
        if (!$id) {
            json_success(['status'=>0,'msg'=>'缺少ID参数！']);
        }

        //实例化数据库
//        $SystemMenuModel = new \app\api\model\SystemMenu();
        $SystemMenuModel =new \app\admin\model\SystemMenu();
        $info = $SystemMenuModel->where('id', $id)->find();
        if (!$info) {
            json_success(['status'=>0,'msg'=>'无法查询到菜单！']);

        }
        json_success($info);
    }


    /**
     * 删除-系统菜单
     */
    public function delete()
    {
        $id = input('id');
        if (!$id) {
            json_success(['status'=>0,'msg'=>'缺少ID参数！']);
        }

        //实例化数据库
//        $SystemMenuModel = new \app\api\model\SystemMenu();
        $SystemMenuModel =new \app\admin\model\SystemMenu();

        $info = $SystemMenuModel->where('id', $id)->find();
        if (!$info) {
            json_success(['status'=>0,'msg'=>'无法查询到菜单！']);
        }

        // 判断是否之前已经删除
        if ($info['status'] == 0) {
            json_success(['status'=>0,'msg'=>'系统菜单-已删除！']);
        }

        // 设置
        $mod_data = array('status'=>0);
        $rows = $SystemMenuModel->where('id', $id)->update($mod_data);
        if ($rows != false) {
            json_success([
                'status' => 1,
                'msg' => '系统菜单-删除成功'
            ]);
        } else {
            json_success(['status'=>0,'msg'=>'系统菜单-删除失败！']);
        }
    }


    /**
     * 获取一级菜单
     * @author jihaichuan
     */
    public function one_menu()
    {
        //实例化数据库
//        $SystemMenuModel = new \app\api\model\SystemMenu();
        $SystemMenuModel =new \app\admin\model\SystemMenu();

        // 条件
        $map = array(
            'pid' => 0,
            'status' => array('neq', 0)
        );
        $one_menu = $SystemMenuModel->where($map)->field('id,name')->select();
        if(!$one_menu){
            $one_menu = [];
        }
        // 返回数据
        json_success(['status'=>1,'msg'=>'请求成功！','data'=>$one_menu]);
    }

}