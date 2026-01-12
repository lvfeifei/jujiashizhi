<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/29
     * Time: 15:54
     */
namespace app\common\services\upload;

use app\common\services\BaseServices;
use phpDocumentor\Reflection\Type;
use Prophecy\Exception\Exception;

class UploadServices extends BaseServices
{
    public function setModel()
    {

    }
    /**
     * 上传-单张图片
     * @param $file
     * @param $folder
     * @return string
     * @author jihaichuan
     */
    public function upload_single_img($file, $folder, $Type=0)
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

        if($Type==1){

            $ex = 'mp3';
            if($file_info['type'] == 'audio/mpeg'){
                $ex = 'mp3';
            }
            //cos存储的路径
            $dstPath =  $folder;
            $picname = $dstPath.'/'.rand(11,999).time(). '.' .$ex;
            $srcPath = $file_info['tmp_name'];
        }else{
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
        }


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
            return ['code'=>'-1','msg'=>'上传失败！'];
        }
    }

    public function set_upload_video($file, $folder)
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
        $ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));
        $imagesExt = array('wmv','mp4','avi','ram','rm','mpg','mpeg','mov','flv');

        if (!in_array($ext, $imagesExt)) {
            return "非法文件类型";
        }
        $ex = 'mp4';

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