<?php

namespace app\api\controller;

use app\common\services\user\UserServices;
use think\Cache;
use think\Request;
use think\Xiaocx;

class Login extends Basic
{

    public $services;
    public function __construct(Request $request = null )
    {

        parent::__construct($request);

    }

    /**
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 用户进入登陆
     */
    public function login()
    {
        $code = input('get.code');

        // $scene = input('get.scene');
        if (!$code) {
            json_success(res_data(0,'获取code失败'));
        }
        //请求微信接口
        $url = config('xiaocx_url').'?appid=' . config('xiaocx_app_id') . '&secret=' . config('xiaocx_app_secret') . '&js_code=' . $code . '&grant_type=authorization_code';
        $info = http_curl($url, 'get');

        if (isset($info['errcode'])) {
            json_success(res_data(0, $info['errmsg']));
        }
        if (!empty($info['openid'])) {
            //实例化数据库
            $UserServices = new UserServices();

            //查询有无数据
            $UserDetail = $UserServices->field('id,unionid,nickname,avatar_url')->where('openid',$info['openid'])->find();
            if (!$UserDetail) {
                //存储
                $data = array(
                    'openid' => $info['openid'],
                    'create_time' => time(),
                );

                if (isset($info['unionid'])) {
                    $data['unionid'] = $info['unionid'];
                }
                $userId = $UserServices->insertGetId($data);


            } else {
                $userId= $UserDetail['id'];
                if (isset($info['unionid'])) {
                    if (empty($UserDetail['unionid'])) {
                        $UserServices->model->where('id', $userId)->update(array('unionid' => $info['unionid']));
                    }
                }


            }

            //是否关注公众号
//            $this->subscribe($userId);
            // 返回用户ID
            json_success(res_data(1,'登录成功',['codeid'=>encode($userId)]));
        }else{
            json_success(res_data(0,'openid获取失败'));
        }
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 小程序端获取用户信息并存储
     */
    public function getwechatuserdetail()
    {
        $userId = input('post.user_id');
        $nickname = input('nickName');
        $gender = input('post.wx_gender');
        $language = input('post.language');

        $avatar_url = input('post.avatarUrl');
        if (!$userId) {
            json_success(['status'=>0, 'msg'=>'获取用户id错误']);
        }
        if (!$nickname) {
            json_success(['status'=>0, 'msg'=>'获取用户昵称错误']);

        }
        if (!$avatar_url) {
            json_success(['status'=>0, 'msg'=>'获取用户头像错误']);
        }
        $user_id = decode($userId);


        //实例化数据库
        $UserServices = new UserServices();
        //要存储的数据
        $saveData = array(
            'nickname' => base64_encode($nickname),
            'gender' => $gender,
            'language' => $language,

            'avatar_url' => $avatar_url,
//            'timestamp' => time(),
            'authorization' => 1,
            'nickname_old' => $nickname
        );
        //存储
        $UserServices->where(array('id' => $user_id))->update($saveData);
        $info = $UserServices->getUserInfo($user_id);
        if($info){
        }else{
            $info = [];
        }
        json_success(['status'=>1, 'msg'=>'请求成功',$info]);
    }



    /**
     * 维护获取用户信息
     * @author Chengkaikai
     */
    public function getWechatUserInfo()
    {
        // 获取code
        $code = input('code');
        if (!$code) {
            json_success(['status'=>0, 'msg'=>'获取code失败']);

        }
        $userId = input('user_id');
        if (!$userId) {
            json_success(['status'=>0, 'msg'=>'用户ID不存在']);

        }
        $userId = decode($userId);

        // 获取数据
        $encryptedData = input('encrypt_data');
        if (!$encryptedData) {
            json_success(['status'=>0, 'msg'=>'获取encryptedData失败']);
        }

        // 获取iv
        $iv = input('iv');
        if (!$iv) {
            json_success(['status'=>0, 'msg'=>'获取iv失败']);

        }
        $iv = urldecode($iv);
        $encryptedData = urldecode($encryptedData);

        // 获取小程序ID
        $appid = config('xiaocx_app_id');

        //请求微信接口
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . Config('xiaocx_app_secret') . '&js_code=' . $code . '&grant_type=authorization_code#wechat_redirect';
        $wechat = http_curl($url, 'get');
        if ($wechat) {
            // 初始化解密
            $xiaocx = new Xiaocx();

            $xiaocx->setConfig($appid, $wechat['session_key']);
            $data = [];

            // 判断是否解析失败
            $errCode = $xiaocx->decryptData($encryptedData, $iv, $data);

            if ($errCode == 0) {
                //对象转数组
                $res = object_array($data);
                $saveData = array(
                    'nickname' => base64_encode($res['nickName']),
                    'wx_gender' => $res['wx_gender'],
                    'language' => $res['language'],
                    'avatar_url' => $res['avatarUrl'],
                    'authorization' => 1,
                    'nickname_old' => $res['nickName']
                );
                //实例化数据库
                $UserServices = new UserServices();
                $UserServices->where(array('id' => $userId))->update($saveData);

                json_success(['status'=>1, 'msg'=>'更新成功']);
            } else {
                json_success(['status'=>0, 'msg'=>$errCode]);

            }
        } else {
            json_success(['status'=>0, 'msg'=>'获取微信服务器API接口错误']);
        }
    }

    /**
     * 获取登录地址
     * @return void
     */
    public function getAuthUrl() {
        $type = $this->request->post('type','');
        $url = $this->request->post('url','');
        $checkKey = $this->request->post('key','');
        if (!$type)json_fail('缺少type参数');
        if ($type == 1){
            $key = config('auth_user_qq_key');
            $typeName = 'qq';
        }else{
            $key = config('auth_user_wechat_key');
            $typeName = 'wechat';
        }
        $result = [
            'type' => $typeName,
            'url' => $url
        ];
        if ($checkKey) $result['key'] = $checkKey;
        $md5key = md5(time());
        $res = curl(config('auth_user_url'),['key' => $key,'state' => $md5key],'1',[]);
        if ($res['code'] != 200)json_fail('获取地址错误');
        Cache::set($md5key,json_encode($result),600);
        $data = json_decode($res['data'],true);
        json_success($data);
    }

    /**
     * 获取微信手机号码
     * @author jihaichuan
     */
    public function get_wechat_phone()
    {
        // 获取code
        $code = input('code');
        if (!$code) {
            json_fail('获取code失败');
        }

        // 获取数据
        $encryptedData = input('encrypt_data');
        if (!$encryptedData) {
            json_fail('获取encryptedData失败');
        }

        // 获取iv
        $iv = input('iv');
        if (!$iv) {
            json_fail('获取iv失败');
        }
        $iv = urldecode($iv);
        $encryptedData = urldecode($encryptedData);

        // 解析内容
        $res = $this->decode_wechat_phone($code, $iv, $encryptedData);
        if ($res == false) {
            json_fail('请求失败，请重试');
        }
        if (is_array($res)) {
            json_success($res);
        } else {
            json_fail($res);
        }
    }
    /**
     * 用户注册
     * @author jihaichuan
     */
    public function wechat_register()
    {

        $userId = input('user_id');
        if (!$userId) {
            json_fail('用户ID不存在！');
        }
        $userId = decode($userId);


        //实例化数据库
        $User = new UserServices();

        // 判断用户是否做关联
        $m_data = [
            // 'mobile' => $mobile,
            'is_register' => 1,
        ];
        $info = $User->model->where(array('id' => $userId))->update($m_data);

        if ($info === false) {
            json_fail('注册失败');
        }
        json_success('注册成功');
    }

    public function decode_wechat_phone($code, $iv, $encryptedData)
    {
        // 获取小程序ID
        $appid = config('xiaocx_app_id');

        //请求微信接口
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . Config('xiaocx_app_secret') . '&js_code=' . $code . '&grant_type=authorization_code#wechat_redirect';
        $wechat = http_curl($url, 'get');

        if ($wechat) {
            // 初始化解密
            $xiaocx = new Xiaocx();

            $xiaocx->setConfig($appid, $wechat['session_key']);
            $data = [];

            // 判断是否解析失败
            $errCode = $xiaocx->decryptData($encryptedData, $iv, $data);
            if ($errCode == 0) {
                return object_array($data);
            } else {
                return $errCode;
            }
        } else {
            return false;
        }
    }


}
