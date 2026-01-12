<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/9/3
 * Time: 11:33
 */
namespace app\common\services\wxmessage;
use app\common\services\BaseServices;

class WxMessageServices extends BaseServices
{

    public function setModel()
    {

    }
    /**
     * 微信公众号消息通知
     * Date: 2022/9/3
     * Time: 11:25
     * USER:GCQ
     */
    public function wx_message($touser,$template_id,$page,$data)
    {

        //获取access_token
        $url = config('xiaocx_token_url').'?key='.config('xiaocx_token_key');
        $info = http_curl($url, 'get');

        if(isset($info['access_token']) && !empty($info['access_token'])){
            //send发送订阅通知
            $send_url = config('xiaocx_send_url').'?access_token='.$info['access_token'];

            $dataInfo = array(
                'touser' => $touser,
                'template_id' => $template_id,
                'page' => $page,
                'data' => $data
            );
            $submitData = json_encode($dataInfo);

            $res = http_curl($send_url, 'post', $submitData);

            return $res;
        }else{
            return $info;
        }

    }
}