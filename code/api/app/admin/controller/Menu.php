<?php
namespace app\admin\controller;

/**
 * 菜单
 */
class Menu extends Basic
{

    // 初始化
     private $SystemMenu;
    public function _initialize()
    {
        parent::_initialize();
//         $this->SystemMenu  = new SystemMenu();
        $this->SystemMenu=new \app\admin\model\SystemMenu();
    }

    /*
 * 列表
 */
    public function index()
    {
        $data = input('post.');
        $list =  $this->SystemMenu ->index();
        if ($list) {
            //获取树形结构数据
            $info = menuTree($list,0);
        } else {
            $info = [];
        }
        json_success(['status'=>1,'msg'=>'请求成功','data'=>$info]);
    }

    /*
   * 添加
   */
    public function add()
    {
        //实例化数据库
        $data = input('post.');
        if (!isset($data['name'])) {
            json_success(['status'=>0,'msg'=>'菜单名不能为空']);
        }
        $status =  $this->SystemMenu ->isTrue($data);
        if ($status) {
            json_success(['status'=>0,'msg'=>'菜单名已存在']);
        }

        if (!empty($data)) {
            $id =  $this->SystemMenu ->add($data);
            if (!$id) {
                json_success(['status'=>0,'msg'=>'添加失败']);

            } else {
                json_success(['status'=>1,'msg'=>'添加失败','data'=>$id]);
            }
        } else {
            json_success(['status'=>0,'msg'=>'添加数据不能为空']);

        }

    }

    /**
     * 获取-单个菜单数据
     * @author jihaichuan
     */
    public function show_edit()
    {
        $id = input('id');
        if (!$id) {
            json_success(['status'=>0,'msg'=>'缺少ID参数']);
        }

        //实例化数据库
//        $SystemMenuModel = new \app\api\model\SystemMenu();
        $SystemMenuModel= new \app\admin\model\SystemMenu();
        $info = $SystemMenuModel::where('id', $id)->find();
        if (!$info) {
            $info = [];
        }
        json_success(['status'=>1,'msg'=>'请求成功','data'=>$info]);
    }
    /*
     * 修改
     */
    public function edit()
    {
        //实例化数据库
        $data = input('post.');

        $status =  $this->SystemMenu ->isTrue($data);
        if ($status) {
            json_fail(data('-1', '菜单名已存在!'));
        }

        if (!empty($data)){
            $id =  $this->SystemMenu ->edit($data);
    //                dd($id);
            if (!$id) {
                json_success(['status'=>0,'msg'=>'未做任何修改']);

            } else {
                json_success(['status'=>1,'msg'=>'修改成功']);
            }
        } else {
            json_success(['status'=>0,'msg'=>'数据不能为空']);
        }

    }
    /*
 * 删除
 */
    public function del()
    {
        //实例化数据库
        $data = input('post.');
        if (!empty($data)) {
            $data['status']=0;
            $id =  $this->SystemMenu ->edit($data);
            if (!$id) {
                json_success(['status'=>0,'msg'=>'删除失败']);
            } else {
                json_success(['status'=>1,'msg'=>'删除成功']);
            }
        } else {
            json_success(['status'=>0,'msg'=>'数据不能为空']);
        }
    }


}