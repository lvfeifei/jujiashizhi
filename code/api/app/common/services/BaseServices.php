<?php

namespace app\common\services;

use app\common\model\UserToken;
use app\common\services\user\CreditBillServices;
use app\common\services\user\UserServices;
use app\common\services\user\UserTokenServices;
use jwt\JWTServices;
use think\Model;

abstract class BaseServices
{

    /** @var Model $model */
    public $model;

    /**
     * @param Model $product
     */
    public function __construct() {
        $this->setModel();
    }

    abstract function setModel();



    public function getPageValue() {
        $page = input('page',1);
        $limit = input('limit',10);
        return [$page,$limit];
    }

    public function getFind($search,$searchField = 'id',$field = '*',$where = []) {
        $model = $this->model->where($searchField,$search);
        if (!empty($where)){
            $model = $model->where($where);
        }
        return $model->field($field)->find();
    }

    public function getSelect($where,$field = '*') {
        return $this->model->where($where)->field($field)->select();
    }


    /**
     * 获取指定id指定字段值
     * @param $id
     * @param $field
     * @return array|float|mixed|string
     */
    public function getOneValueById($id,$field) {
        if (!$field)return '';
        return $this->model->where('id',$id)->value($field);
    }

    /**
     * 获取列表
     * @param $ids
     * @param $field
     * @return array|bool|\PDOStatement|string|\think\Collection|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListById($ids,$field = '*') {
        if (is_array($ids)){
            return $this->model->whereIn('id',$ids)->field($field)->select();
        }
        return $this->getOneById($ids,$field);
    }

    /**
     * 获取一条数据
     * @param $id
     * @param $field
     * @return array|bool|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOneById($id,$field = '*') {
        return $this->model->where('id',$id)->field($field)->find();
    }

    /**
     * post请求
     * @param string $url 访问地址
     * @param array $data 发送数据
     * @return bool|string
     */
    public function curl_post($url, $data)
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


    public function qrcode($path)
    {
        Vendor('phpqrcode.phpqrcode');
        //生成二维码图片
        $object = new \QRcode();
        $name = time() . uniqid();
        $ad = 'static/Qrcode/' . $name . '.jpg';
        $object->png($path, $ad, 3, 4, 2);
        return '/static/Qrcode/' . $name . '.jpg';
    }



    /**
     *
     * @param $name
     * @param $arguments
     * @return Model
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->model, $name], $arguments);
    }
    /**
     * 上传-单张图片
     * @param $file
     * @param $folder
     * @return string
     * @author jihaichuan
     */
    public function upload_single_img($file, $folder)
    {
        $secretId = config('tencent.accessKeyId'); //替换为用户的 secretId，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
        $secretKey =  config('tencent.accessKeySecret'); //替换为用户的 secretKey，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
        $region = config('tencent.region'); //替换为用户的 region，已创建桶归属的region可以在控制台查看，https://console.cloud.tencent.com/cos5/bucket
        $bucket = config('tencent.oss_bucket');;
        $clien = array(
            'region' => $region,
            //'schema' => 'https', //协议头部，默认为http
            'credentials'=> array(
                'secretId'  => $secretId ,
                'secretKey' => $secretKey));
        $cosClient = new \Qcloud\Cos\Client($clien);
        $file_info = $file->getInfo();
        $ex = "jpg";
        if($file_info['type'] == 'image/png'){
            $ex = "png";
        }else if($file_info['type'] == 'image/gif'){
            $ex = "gif";
        }
        //cos存储的路径
        $dstPath =  $folder;
        $picname = $dstPath.'/'.rand(11,999).time(). '.' .$ex;

        $srcPath = $file_info['tmp_name'];
        //上传
        try {
            $result = $cosClient->putObject(array(
                'Bucket' => $bucket,
                'Key' => $picname,
                'Body' => fopen($srcPath, 'rb'),
                'ContentType' => $file_info['type'],
            ));
            //文件已经上传成功
            return ['code'=>1,'msg'=>'上传成功！', 'data'=>$result];
            //echo $result['Location'];
        } catch (\Exception $e) {
            return ['code'=>'-1','上传失败！'];
        }
    }
}