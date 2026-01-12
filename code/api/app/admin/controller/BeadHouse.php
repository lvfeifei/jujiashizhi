<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/8/12
 * Time: 17:39
 */

namespace app\admin\controller;

use app\common\services\beadhouse\BeadHouseServices;
use think\console\Input;
use think\Request;
use think\Validate;

class BeadHouse extends Basic
{
    public $services;

    public function __construct(Request $request = null, BeadHouseServices $beadHouseServices)
    {
        parent::__construct($request);
        $this->services = $beadHouseServices;
    }

    public function index()
    {
        $where['status'] = $this->request->post('status/d',0);  //默认0查询全部    //发布状态  1合作中  2合作结束  0全部
        $where['key'] = $this->request->post('key');
        json_success($this->services->admin_index($where));
    }

    public function add()
    {
        $title = input('title');
        if(empty($title))json_success(res_data(0,'养老院名称不能为空'));

        $logo = input('logo');
        if(empty($logo))json_success(res_data(0,'logo地址不能为空'));

        $address = input('address');
        if(empty($address))json_success(res_data(0,'养老院地址不能为空'));

        $name = input('name');
        if(empty($name))json_success(res_data(0,'联系人不能为空'));

        $mobile = input('mobile');
        if(empty($mobile))json_success(res_data(0,'联系电话不能为空'));
        $username = input('username');
        if(empty($username))json_success(res_data(0,'账号不能为空'));
        $password = input('password');
        if(empty($password))json_success(res_data(0,'密码不能为空'));

        $status = input('status/d', 1);
        if(!$status){
           $status =1;
        }
        $data = [];
        $data['mobile'] = $mobile;
        $data['password'] = $password;
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
        $data['title'] = $title;
        $data['logo'] = $logo;
        $data['address'] = $address;
        $data['name'] = $name;
        $data['username'] = $username;
        $data['status'] = $status;
        json_success($this->services->admin_add($data));

    }
    
    /**
     * 展示
     * @return void
     */
    public function show()
    {
        $id = input('id');
        if(empty($id))json_success(res_data(0, '养老院id不能为空'));
        json_success($this->services->admin_show($id));
    }
    
    /**
     * 编辑
     * @return void
     */
    public function edit()
    {
        $id = input('id');
        if(empty($id))json_success(res_data(0, '养老院id不能为空'));
        $title = input('title');
        if(empty($title))json_success(res_data(0,'养老院名称不能为空'));

        $logo = input('logo');
        if(empty($logo))json_success(res_data(0,'logo地址不能为空'));

        $address = input('address');
        if(empty($address))json_success(res_data(0,'养老院地址不能为空'));

        $name = input('name');
        if(empty($name))json_success(res_data(0,'联系人不能为空'));

        $mobile = input('mobile');
        if(empty($mobile))json_success(res_data(0,'联系电话不能为空'));
        $username = input('username');
        if(empty($username))json_success(res_data(0,'账号不能为空'));
        $password = input('password');

        $status = input('status/d', 1);
        if(!$status){
            $status =1;
        }
        $data = [];

        if($password){
            $data['mobile'] = $mobile;
            $data['password'] = $password;
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
        }else{
            $data['mobile'] = $mobile;
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
        }
        $validate = new Validate($rule,$message);
        if(!$validate->check($data)){
            json_success(['status'=>0,'msg'=> $validate->getError()]);
        }
        if($password){
            $data['password'] = $data['password'];
        }else{
            $data['password'] = $password;
        }

        $data['title'] = $title;
        $data['logo'] = $logo;
        $data['address'] = $address;
        $data['name'] = $name;
        // $data['username'] = $username;
        $data['status'] = $status;
        json_success($this->services->admin_edit($id,$data));

    }
    
    /**
     * 删除
     * @return void
     */
    public function del()
    {
        $id = input('id');
        if(empty($id))json_success(res_data(0, '养老院id不能为空'));
        json_success($this->services->admin_del($id));
    }
    
    /**
     *  重新生成二维码
     * @return void
     */

    public function rebuild_qrcode()
    {
        $id = input('id');
        if(empty($id))json_success(res_data(0, '养老院id不能为空'));
        json_success($this->services->admin_rebuild_qrcode($id));
    }
    
    public function beadhouselist()
    {
        $user_id = $this->user_id;
        $role_id = $this->role_id;
        json_success($this->services->admin_beadhouselist($user_id,$role_id));
    }

    /**
     * 首页展示养老院信息
     */
    public function bead_house_info()
    {
        $user_id = $this->user_id;
        json_success($this->services->admin_get_bead_house_info($user_id));
    }

    /**
     * 下载二维码
     */
    public function download_code()
    {

        $code_url = input('get.code_url');
        $name = 'beadhouse_code';
        $str = date('Y-m-d',time()) . rand(100000,999999);
        $local = 'static/code/' . $str .'/' . $name . '.png';
        $name = http_down($code_url, $local, $timeout = 60);
        download_file($name);
    }
    
    
}