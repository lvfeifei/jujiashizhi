<?php

namespace app\admin\controller;
use think\Validate;

/**
 * 管理员
 */
class Manager extends Basic
{

    // 初始化
    public function initialize()
    {
        parent::_initialize();

    }

    /*
     * 显示
     */
    public function index()
    {

        $SystemManager=new \app\admin\model\SystemManager();
        $system_role=new \app\admin\model\SystemRole();

        $type = Input('type', 1);
        $page = Input('page', 1);
        $limit = Input('limit', 10);
        $list = $SystemManager->index($type, $page, $limit);
        if ($list) {
            foreach ($list as $k=>$v){
                $v['role_name'] =$system_role->where(array('id'=>$v['role_id']))->value('name');
                $v['register_time'] = $v['register_time']? date('Y-m-d H:i:s',$v['register_time']):'--';
                $list[$k]=$v;
            }
            $infoList = array(
                'list'=>$list,
                'count'=>$SystemManager
                    ->where('status','neq', '0')
                    ->where('role_id', 'neq', 11)->order('id desc')->count('id')
            );
            json_success($infoList);
        } else {
            $infoList = array(
                'list'=>[],
                'count'=>0
            );
            json_success(['status'=>1,'msg'=>'请求成功！','data'=>$infoList]);
        }

    }


    /**
     * 添加
     */
    public function add()
    {
        //  MDAwMDAwMDAwMIGHhWuycm9x
        //   print_t(encode(1560));
        //实例化数据库
//        $SystemManager = new SystemManager();
       $SystemManager= new \app\admin\model\SystemManager();

        $data['username'] = input('post.username');
        $data['password'] = input('post.password');
        $data['identity'] = input('post.identity');

        if (!$data['username']) {
            json_success(['status'=>0,'msg'=>'用户名不能为空！']);

        }
        if (!$data['password']) {
            json_success(['status' => 0, 'msg' => '密码不能为空！']);
        }
        if (!$data['identity']) {
            json_success(['status'=>0,'msg'=>'身份不能为空！']);
        }
        $data['truename'] = input('post.username');
        $data['mobile'] = input('post.mobile');
        $rule=[
            'mobile|手机'=>[
                'require',
                'max'=>11,
                'regex'=>'/^1[3-8]{1}[0-9]{9}$/',

            ],
            'password|密码' =>[
                'require',
                'min'=>6,
                'max'=>30,
                'alphaNum'

            ]

        ];
        $message=[
            'mobile.require'=>'请输入手机号',
            'mobile.max'=>'手机号最多不能超过11个字符',
            'mobile.regex'=>'手机号格式不正确',
            'password.require'=>'请输入密码',
            'password.min'=>'密码最小长度6个字符',
            'password.max'=>'密码最大长度30个字符',
            'password.alphaNum'=>'密码只能由数字和字母组成',
        ];
        $validate = new Validate($rule,$message);
        if(!$validate->check($data)){
            json_success(['status'=>0,'msg'=> $validate->getError()]);
        }

        $status = $SystemManager->isTrue($data);

        if ($status) {
            json_success(['status'=>0,'msg'=>'用户名已存在！']);

        }
        $data['password'] = encryption($data['password']);
        $data['register_time'] = time();
        $data['role_id'] = $data['identity'];
        $row = $SystemManager->allowField(true)->save($data);
        if (!$row) {
            json_success(['status'=>0,'msg'=>'添加失败！']);

        } else {
            json_success(['status'=>1, 'msg'=>'添加成功！']);
        }

    }

    /*
     * 修改
     */
    public function edit()
    {
        //实例化数据库
        $SystemManager= new \app\admin\model\SystemManager();

        $data['password'] = input('post.password');
        $data['identity'] = input('post.identity');
        $id = input('post.id');
        if (!$id) {
            json_success(['status'=>0,'msg'=>'ID不能为空！']);
        }

        $data['mobile'] = input('post.mobile');
        if ($data['password']) {
            $rule=[
                'mobile|手机'=>[
                    'require',
                    'max'=>11,
                    'regex'=>'/^1[3-8]{1}[0-9]{9}$/',

                ],
                'password|密码' =>[
                    'require',
                    'min'=>6,
                    'max'=>30,
                    'alphaNum'

                ]

            ];
            $message=[
                'mobile.require'=>'请输入手机号',
                'mobile.max'=>'手机号最多不能超过11个字符',
                'mobile.regex'=>'手机号格式不正确',
                'password.require'=>'请输入密码',
                'password.min'=>'密码最小长度6个字符',
                'password.max'=>'密码最大长度30个字符',
                'password.alphaNum'=>'密码只能由数字和字母组成',
            ];
            $validate = new Validate($rule,$message);
            if(!$validate->check($data)){
                json_success(['status'=>0,'msg'=> $validate->getError()]);
            }
            $data['password'] = encryption($data['password']);
        }else{
            $rule=[
                'mobile|手机'=>[
                    'require',
                    'max'=>11,
                    'regex'=>'/^1[3-8]{1}[0-9]{9}$/',

                ],

            ];
            $message=[
                'mobile.require'=>'请输入手机号',
                'mobile.max'=>'手机号最多不能超过11个字符',
                'mobile.regex'=>'手机号格式不正确',
            ];
            $validate = new Validate($rule,$message);
            if(!$validate->check($data)){
                json_success(['status'=>0,'msg'=> $validate->getError()]);
            }
            unset($data['password']);
        }

        if (!$data['identity']) {
            json_success(['status'=>0,'msg'=>'身份不能为空！']);
        }
        $data['truename'] = input('post.truename');



        $data['role_id'] = $data['identity'];
        $row = $SystemManager->allowField(true)->save($data, ['id' => $id]);
        if ($row !== false) {
            //添加关联表
            json_success(['status'=>1, 'msg'=>'修改成功！']);
        }else{
            json_success(['status'=>0,'msg'=>'修改失败！']);
        }
    }


    /*
 * 删除
 */
    public function del()
    {
        $SystemManager= new \app\admin\model\SystemManager();

//        $SystemManager = new SystemManager();
        //实例化数据库
        $id = input('post.id');

        if (!$id) {
            json_success(['status'=>0,'msg'=>'ID不能为空！']);
        }


        $data['status'] = 0;
        $id = $SystemManager->where('id', $id)->update($data);
        if (!$id){
            json_success(['status'=>0,'msg'=>'删除失败！']);
        } else {
            json_success(['status'=>1, 'msg'=>'删除成功！']);
        }

    }

    //修改员工显示内容
    public function show_edit(){

        $id = Input('post.id');
        if (!$id) {
            json_success(['status'=>0,'msg'=>'获取id错误！']);
        }
        $info = Db('SystemManager')->where('id',$id)->field('id,role_id,username,truename,password,mobile,identity')->find();
        if (!$info){
            $info = [];
        }
        json_success(['status'=>1,'msg'=>'请求成功','data'=>$info]);

    }
    /**
     * 修改密码
     */
    public function edit_pass_word()
    {
        $data = input('post.');

        // if (!$data['id']) {
        //     json_success(['status'=>0,'msg'=>'ID不能为空！']);
        // }

        if (!isset($data['old_password'])) {
            json_success(['status'=>0,'msg'=>'原密码不能为空！']);
        }
        if (!$data['old_password']) {
            json_success(['status'=>0,'msg'=>'原密码不能为空！']);
        }

        //实例化数据库
//        $systemManager = new SystemManager();
        $systemManager= new \app\admin\model\SystemManager();

        //判断原密码是否一致
        $data['old_password'] = encryption($data['old_password']);

        $data['id'] = $this->user_id;
        $status = $systemManager->isTruePass($data);
        if ($status == 2) {
            json_success(['status'=>0,'msg'=>'原登录密码输入不正确！']);
        }
        //判断两次密码是否一致
        if (!isset($data['newPassword'])) {
            json_success(['status'=>0,'msg'=>'新密码不能为空！']);
        }
        if (!isset($data['newConfirmPassword'])) {
            json_success(['status'=>0,'msg'=>'确认密码不能为空！']);
        }

        $newPassword = encryption($data['newPassword']);   //加密新密码
        $newConfirmPassword = encryption($data['newConfirmPassword']);    //加密确认密码
        if ($newPassword !== $newConfirmPassword) {
            json_success(['status'=>0,'msg'=>'新密码与确认密码不一致！']);
        }
        //解密
        // $data['id'] = $data['id'];

        if (!empty($data)) {
            $data['password'] = $newPassword;
            $res = $systemManager->edit($data);
            if ($res !== false) {
                json_success(['status'=>1, 'msg'=>'修改成功！']);
            } else {

                json_success(['status'=>0,'msg'=>'修改失败！']);
            }
        } else {
            json_success(['status'=>0,'msg'=>'数据不能为空！']);
        }
    }

}