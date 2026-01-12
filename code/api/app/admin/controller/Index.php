<?php

namespace app\admin\controller;

use app\admin\model\SystemManager;
use app\common\model\Company;
use green\ContentSecurityServices;
use OSS\OssClient;
use PhpOffice\PhpWord\Shared\ZipArchive;
use think\Controller;
use think\Db;

class Index extends Basic
{

    /**
     * 初始化-继承
     */
    public function _initialize()
    {
        // 继承
        parent::_initialize();
    }


    public function index()
    {

    }


    /**
     * 上传图片
     */
    public function upload_img()
    {
        $file = request()->file('file');

        try {
            $ossPath = 'upload'.'/'.date('Y').'/'.date('m').'/';
            $newFileName = md5($ossPath).date('YmdHis') . rand(0, 9999).strchr($file->getInfo()['name'],'.');
            $oss = new OssClient(config('alioss.accessKeyId'),config('alioss.accessKeySecret'),config('alioss.endpoint'));
            $ossInfo = $oss->uploadFile(config('alioss.oss_bucket'),$ossPath.$newFileName,$file->getRealPath());
        }catch (\Throwable $e){
            json_fail('上传失败');
        }
        $url = $ossInfo['oss-request-url'];
        $ContentSecurityServices = new ContentSecurityServices();
        $url = $ContentSecurityServices->imageFilter($url);

        json_success(['url' => $url]);
    }

    public function upload_stream_img() {
        $base64 = request()->post('base64','');

       $march = preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result);
       if (!$march){
           json_fail('上传失败');
       }
        $base64_image = str_replace($result[1],'',$base64);
        $file_content = base64_decode($base64_image);
        $file_ext = $result[2];
        try {
            $ossPath = 'upload'.'/'.date('Y').'/'.date('m').'/';
            $newFileName = md5($ossPath).date('YmdHis') . rand(0, 9999).'.'.$file_ext;
            $oss = new OssClient(config('alioss.accessKeyId'),config('alioss.accessKeySecret'),config('alioss.endpoint'));
            $ossInfo = $oss->putObject(config('alioss.oss_bucket'),$ossPath.$newFileName,$file_content);
        }catch (\Throwable $e){
            json_fail('上传失败');
        }
        $url = $ossInfo['oss-request-url'];
        $ContentSecurityServices = new ContentSecurityServices();
        $url = $ContentSecurityServices->imageFilter($url);

        json_success(['url' => $url]);
    }


}
