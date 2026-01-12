<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------


// 应用公共文件
/**
 * 生成订单号
 * @author hejianyu
 */
function orderNum()
{
    // return  chr(rand(65, 90)) . date('YmdHis', time()) . numberCaptcha(4);
    return chr(rand(65, 90)) . date('YmdHis', time()) . chr(rand(65, 90));
}


/**
 * @param $url 网址
 * @param $filename 保存文件名
 * @param int $timeout 过期时间
 * @return bool
 * 下载远程文件
 */
function http_down($url, $filename, $timeout = 60)
{
    $path = dirname($filename);
    if (!is_dir($path) && !mkdir($path, 0755, true)) {
        return false;
    }
    $fp = fopen($filename, 'w');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $filename;
}


/**
 * API 返回JSON数据
 * @param string $code
 * @param string $message
 * @param array $data
 */
function apiResponse($code = '1', $message = '', $data = array())
{
    header('Access-Control-Allow-Origin:*');
    header('Content-Type:application/json;charset=utf-8');
    $result = array(
        'code' => $code,
        'message' => $message,
        'data' => $data,
    );
    die(json_encode($result, JSON_UNESCAPED_UNICODE));
}

/**
 * 将秒传为 时分秒
 * @param $data
 * 2020/5/29
 * 21:35
 */
function time_format($data)
{
    if ($data > 3600) {
        $s = $data % 3600;
        $h = floor($data / 3600);
        $i = floor($s / 60);
        $s = $s % 60;

        if ($h < 10) {
            $h = '0' . $h;
        }
        if ($i < 10) {
            $i = '0' . $i;
        }
        if ($s < 10) {
            $s = '0' . $s;
        }

        $str_time = $h . ':' . $i . ':' . $s;
    } elseif ($data < 3600) {
        $str_time = date('i:s', $data);
    }
    return $str_time;
}

/**
 * 将  12:00:00 转化为 秒数
 * 2020/5/29
 * 21:39
 */
function date_format($duration)
{
    $duration_arr = explode(':', $duration);
    if (count($duration_arr) == 3) {
        $h = (int)$duration_arr[0] * 3600; //小时 计算秒
        $m = (int)$duration_arr[1] * 60;   //分 计算秒
        $s = (int)$duration_arr[2];
        $duration = $h + $m + $s;
    } elseif (count($duration_arr) == 2) {
        $m = (int)$duration_arr[0] * 60;   //分 计算秒
        $s = (int)$duration_arr[1];
        $duration = $m + $s;
    }
    return $duration;
}

/**
 * //$seconds = 3600*34 + 122;
 * @param $seconds
 * @return string
 * 2020/5/29
 * 21:51
 */

function changeTimeTypes($seconds)
{
    print_t($seconds);
    if ($seconds > 3600) {
        $hours = intval($seconds / 3600);
        $minutes = $seconds % 3600;
        $time = $hours . ":" . gmstrftime('%M:%S', $minutes);
    } else {
        $time = gmstrftime('%H:%M:%S', $seconds);
    }
    return $time;
}







