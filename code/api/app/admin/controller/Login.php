<?php

namespace app\admin\controller;

use app\admin\model\SystemManager;
use app\common\model\LoginRecord;

class Login extends Basic
{
    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 用户进入登陆-管理员
     */
    public function login()
    {
        $user_name = input('username'); //用户名

        $pass_word = input('password');

        if(!$user_name)json_success(['status'=>0,'msg'=>'用户名不能为空']);

        if(!$pass_word)json_success(['status'=>0,'msg'=>'密码不能为空']);

        $pass_word = encryption(trim($pass_word));  //方法加密密码
        //初始化数据库
        $SystemManager = new SystemManager();
        //查询条件
        $map = array(
            'username' => $user_name,
            'password' => $pass_word,
            'status'=>array('neq',0)
        );
        //查询数据
        $info = $SystemManager->where($map)->find();

        if ($info) {
            //判断该用户是否被禁用 1=有效 2=禁用
            if ($info['status'] == 1) {
                //初始化数据库
                $LoginRecord = new LoginRecord();
                //架构准备写入登陆日志表的数据
                $data['type'] = 1;
                $data['pid'] = $info['id'];    //系统管理员id
                $data['time'] = time();         //登陆时间
                $data['ip'] = request()->ip();  //登陆ip

                //写入数据
                $LoginRecord->insert($data);
                $role_array = explode(',', $info['role_id']);
                asort($role_array);
                //记录token
                $updata['token_exp_time'] =time()+ config('expiration_time')*60;
                $str_token =$updata['token_exp_time'].'JJSZ'.$info['id'].'JJSZ';
                $token = encryption($str_token);
                $updata['token']=$token;
                $updata['login_time'] = time();
                $data = array(
                    'status' => 1,
                    'msg' => '登录成功',
                    'user_id' => encode($info['id']),
                    'user_name' => $info['username'],
                    'pic' => $info['pic'],
                    'token' => $token,
                    'role_id' => implode(',', $role_array),
                );

                $SystemManager->where($map)->update($updata);
                json_success($data);
            } else {
               json_success(['status'=>0,'msg'=>'该用户被禁用,请联系管理员']);

            }
        } else {
            json_success(['status'=>0,'msg'=>'账号或密码错误']);
        }
    }

    public function get_access_token()
    {
        $wxtoken = Db('wx_token');
        $access_token = $wxtoken->where(array('wxatid' => 1))->value('access_token');
        return $access_token;
    }

}
