<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//header('Access-Control-Allow-Origin:*');
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
    exit;
}else{
    header("Access-Control-Allow-Origin: *");
}
// [ 应用入口文件 ]

// 获取请求的时间
$timestamp = 0;

// 获取签名
$sign = '';

// 是否sh脚步请求
$sh = true;

$request_uri = $_SERVER['REQUEST_URI'];
if ($request_uri) {
	$urls = explode('/', $_SERVER['REQUEST_URI']);
	if (($urls[1] == 'api') && ($sh==false)) {
		if ($_GET) {
			$sign = isset($_GET['sign'])?$_GET['sign']:'';
			$timestamp = isset($_GET['timestamp'])?$_GET['timestamp']:'';
			$sh = isset($_GET['sh'])? true:false;
		}
		if ($_POST) {
			$sign = isset($_POST['sign'])?$_POST['sign']:'';
			$timestamp = isset($_POST['timestamp'])?$_POST['timestamp']:'';
			$sh = isset($_POST['sh'])? true : false;
		}
		if ($sh==false) {
			if ($sign && $timestamp) {
				// 验证服务器时间与客户端请求时间是否一致
				if (($timestamp != time()) && ((time() - $timestamp) > 60)) {
//					exit('请求时间超时');
					echo json_encode('请求时间超时');
					exit();
				}

				// 加密验证
				$sign_md5 = md5(($timestamp + 86400) . 'hqrw');
				if ($sign_md5 != $sign) {
//					exit('md5解密不正确');
					echo json_encode('md5解密不正确');
					exit();
				}
			} else {
				echo json_encode('非法请求');
				exit();
			}
		}
	}
}


// 定义应用目录
define('APP_PATH', __DIR__ . '/../app/');

define('CONF_PATH', __DIR__ .'/../conf/');

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';


