<?php

namespace app\api\controller;
use app\common\services\user\UserServices;
use OSS\OssClient;
use think\Cache;
use think\cache\driver\Memcached;
use think\Controller;
use think\Db;
use think\Image;

/**
 *
 */
class Basic extends Controller
{

    protected $userId;
    protected $userInfo;
    protected $noLogin = [
        'api/index/index',//首页
        'api/login/login',//首页
        'api/index/test',//测试接口
        'api/config/index'

    ];

    public function _initialize()
    {
        parent::_initialize();
        if (!in_array(strtolower($this->request->module()).'/'. strtolower($this->request->controller()) .'/'. strtolower($this->request->action()),$this->noLogin)){
            $codeid= trim(ltrim($this->request->header("codeid")));
            if(!$codeid)json_success(res_data(0,'codeid不能为空'));
            $user_id = decode($codeid);
            $UserServices = new UserServices();
            $UserInfo =$UserServices->model
                ->where('id',$user_id)
                ->field('openid,unionid',true)
                ->find();
            if(!$UserInfo)json_success(res_data(0,'user_id不合法'));
            $this->userId =$user_id;
            $this->userInfo=$UserInfo;
        }else{

        }
    }



    /**
     * @param $user_no
     * @return string
     *  处理 编号id
     */
    public function user_no($user_no)
    {
        return str_pad($user_no, 6, "0", STR_PAD_LEFT);
    }


    /**
     * 生成二维码
     */
    public function api_notice_increment($url, $data)
    {
        $ch = curl_init();
        $header = array("Accept-Charset: utf-8");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        return $tmpInfo;
    }

    /**
     * 检测-是否请求是否存在memcache
     * @param $user_id
     * @return bool
     */
    protected function check_user_memcache($user_id)
    {
        if (!is_numeric($user_id)) {
            return -2;
        }
        $memcache = new Memcached();
        if (!$memcache->has('user_id' . $user_id)) {
            return -3;
        }

        // 判断当前用户是否存在
        $is_check = $this->is_check_user($user_id);
        if ($is_check == false) {
            return -4;
        }
        // 返回
        return 1;
    }



    /**
     * 微信违规验证
     * 2020/5/19
     * 21:49
     *   //$post_content=array("content"=>$content);
     * //$post_content='{"content":"特3456书yuuo莞6543李zxcz蒜7782法fgnv级"}';
     */
    public function msg_sec_check($content = '')
    {
        $access_token = $this->get_token();
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token=' . $access_token;
        $result = http_curl($url, 'post', json_encode(array('content' => $content), JSON_UNESCAPED_UNICODE));

        if ($result['errcode'] != 0) {
            return false;
//            json_fail('内容含有违法违规内容');
        }
        return true;
    }

    /**
     * @param $path
     * @param string $id
     * @return string
     * @throws \Exception
     * 生成小程序码
     */
    public function course_qrcode($course_id, $path, $user_id)
    {
//           $path = 'pages/i/broadcast_detail';
        $token = $this->get_token();
        //获取 二维码
        $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=" . $token;
        //跳转页面
        if (strpos($path, '?') !== false) {
            //'包含该字符串';
            $path = $path . '&course_id=' . $course_id . '&share_user_id=' . $user_id;
        } else {
            //'不包含该字符串';
            $path = $path . '?course_id=' . $course_id . '&share_user_id=' . $user_id;;
        }
        $data = array('path' => $path);
        $code = json_encode($data);
        $imgInfo = $this->api_notice_increment($url, $code);
        $local = 'static/Code/' . date('YmdHis') . rand(1221212, 1212012102102) . '.png';
        file_put_contents($local, $imgInfo);
//        sleep(1);
//        $code_url = $this->upImg($local);
        return $local;
    }

    /**
     * @param $address
     * @return bool
     *获取经纬度
     */
    public function getLocation($address = '北京市海淀区农大南路88号')
    {
//        $key = "5B6BZ-36GWQ-MHB5G-GJROJ-QYSS3-AVBI7";
        $key = config('location_key');
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?address=' . $address . "&key=" . $key;
        $info = http_curl($url, 'get');
        if ($info['message'] == '查询无结果') {
            return false;
        } else {
            $arr['lng'] = $info['result']['location']['lng'];
            $arr['lat'] = $info['result']['location']['lat'];
            return $arr;
        }
    }


    /**
     * @param $url
     * @param $data
     * @return bool|string
     * 生成二维码
     */
    public function notice_increment($url, $data)
    {
        $ch = curl_init();
        $header = array("Accept-Charset: utf-8");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        return $tmpInfo;
    }

    /**
     * @param $url
     * @param $id
     * @param $name
     * @return string
     * 生成普通带参数的二维码
     */
    public function get_qr_code($url, $id, $name = 'ticket')
    {
       // //带LOGO
       //  $errorCorrectionLevel = 'L';//容错级别
       //  $matrixPointSize = 9;//生成图片大小
       //  //生成二维码图片
       //  Vendor('phpqrcode.phpqrcode');
       //  $object = new \QRcode();
       //  $ad = 'static/Qrcode/'.$id.$name.'.jpg';
       //  $object->png($url, $ad, $errorCorrectionLevel, $matrixPointSize, 2);
       //  $logo = 'static/Image/logo1.jpg';//准备好的logo图片
       //  $QR = 'static/Qrcode/'.$id.$name.'.jpg';//已经生成的原始二维码图
       //
       //  if ($logo !== FALSE) {
       //    $QR = imagecreatefromstring(file_get_contents($QR));
       //    $logo = imagecreatefromstring(file_get_contents($logo));
       //    $QR_width = imagesx($QR);//二维码图片宽度
       //    $QR_height = imagesy($QR);//二维码图片高度
       //    $logo_width = imagesx($logo);//logo图片宽度
       //    $logo_height = imagesy($logo);//logo图片高度
       //    $logo_qr_width = $QR_width / 5;
       //    $scale = $logo_width/$logo_qr_width;
       //    $logo_qr_height = $logo_height/$scale;
       //    $from_width = ($QR_width - $logo_qr_width) / 2;
       //    //重新组合图片并调整大小
       //    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
       //    $logo_qr_height, $logo_width, $logo_height);
       //  }
       //  //输出图片  带logo图片
       //  imagepng($QR, 'static/Qrcode/'.$id.$name.'.jpg');
        //不带LOGO
        Vendor('phpqrcode.phpqrcode');
        //生成二维码图片
        $object = new \QRcode();
        $level = 3;
        $size = 4;
        if (!is_dir('static/Qrcode')){
            mkdir('static/Qrcode',0777,true);
        }
        $ad = 'static/Qrcode/' . $id . $name . '.jpg';
        $errorCorrectionLevel = intval($level);//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, $ad, $errorCorrectionLevel, $matrixPointSize, 2);
        return '/static/Qrcode/' . $id . $name . '.jpg';
    }


    /**
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return int
     * 计算距离
     */
    public function getDistance($lat1, $lng1, $lat2, $lng2){
        $earthRadius = 6367000; //approximate radius of earth in meters
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance);
    }

    /**
     * 提取富文本中的纯文字
     * addtime 2020年8月10日 09:45:20
     * @param [type] $string 字符串
     * @return void
     */
    public function StringExtractionText($string)
    {
        if($string){
            // 把一些预定义的 HTML 实体转换为字符
            // 预定义字符是指:<,>,&等有特殊含义(<,>,用于链接签,&用于转义),不能直接使用
            $html_string = htmlspecialchars_decode($string);
            // 将空格去除
            $content = str_replace(" ", "", $html_string);
            // 去除字符串中的 HTML 标签
            $contents = strip_tags($content);
            // 设置截取的字数
            $num = 50;
            // 利用三元运算判断文字是否超出设置的字数进行截取
            return mb_strlen($contents,'utf-8') > $num ? mb_substr($contents, 0, $num, "utf-8").'...' : mb_substr($contents, 0, $num, "utf-8");
        }else{
            return false;
        }
    }

    /**
     * 上传-单张图片
     * @param $file
     * @param $folder
     * @return string
     * @author jihaichuan
     */
    protected function upload_single_img($file, $folder)
    {
        // 移动到框架应用根目录/uploads/ 目录下
        $root = '.';
        $path = '/uploads/' . $folder . '/';
        $info = $file->move($root . $path);
        if (!$info) {
            json_fail([
                'status' => -12,
                'msg' => '上传文件失败！'
            ]);
        }

        // 获取文件上传的路径
        return config('site_url') . $path . $info->getSaveName();
    }

}


