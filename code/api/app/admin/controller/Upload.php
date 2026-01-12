<?php

namespace app\admin\controller;

use app\common\services\upload\UploadServices;
use Qcloud\Cos\Client;
//use think\Request;
use think\Request;
class Upload extends Basic
{

    /**
     * 初始化-继承
     * @author jihaichuan
     */
    public $services;
    public function __construct(Request $request = null,UploadServices $uploadServices)
    {
        parent::__construct($request);
        $this->services = $uploadServices;
    }
    public function index()
    {

    }

    /**
     * 上传图片
     * @author jihaichuan
     */
    public function upload_img()
    {
        // 上传的文件夹
        $folder = input('folder');
        if (!$folder) {
            // json_success(res_data(0,'请设置上传目录！'));
            $folder = 'zixun';
        }

        // 上传图片的字段
        //$field = input('field', 'img');
        //$file = request()->file('file');
        // if (!$file) {
        //     json_success(res_data(0,'请上传文件！'));
        //
        // }
        $file = request()->file('file');
        $upload = request()->file('upload');



        if ($file || $upload) {

        }else{
            json_success(res_data(0,'请上传文件！'));
        }


        if($file){
            // 上传单张图片
            $res = $this->services->upload_single_img($file,$folder);
             if($res['code']==1){
                 json_success(['status'=>1,'msg'=>$res['msg'],'data'=>['imgurl'=>config('tencent.endpoint').'/'.$res['data']['Key']]]);


             }else{
                 json_success(res_data(0,$res['msg']));

             }
        }else if ($upload){
            // 上传单张图片
            $res = $this->services->upload_single_img($upload,$folder);
            if($res['code']==1) {

                $edit_res = [
                    'fileName' => '',
                    'uploaded' => 1,
                    'url' => config('tencent.endpoint') . '/' . $res['data']['Key'],
                ];
                json_success($edit_res);
            }else{
                json_fail($res['msg']);

            }
        }
        // 上传单张图片
        // $res = $this->services->upload_single_img($file,$folder);




    }

    /**
     * 上传视屏
     *
     */
    public function upload_video()
    {
        // 上传的文件夹
        $folder = input('folder');
        if (!$folder) {
            json_success(res_data(0,'请设置上传目录！'));
        }


        $file = request()->file('file');
        if (!$file) {
            json_success(res_data(0,'请上传文件！'));

        }

        $res = $this->services->set_upload_video($file,$folder);

        if($res['code']==1){
            json_success(['status'=>1,'msg'=>$res['msg'],'data'=>['imgurl'=>config('tencent.endpoint').'/'.$res['data']['Key']]]);

        }else{
            json_success(res_data(0,$res['msg']));

        }



    }


}
