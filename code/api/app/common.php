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

function postXmlSSLCurl($xml, $url, $second, $cert, $key)
{
    $ch = curl_init();
    //超时时间
//    curl_setopt($ch,CURLOPT_TIMEOUT,$second ? $second : $this->timeout);

    //这里设置代理，如果有的话
    //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
    //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //设置证书
    //使用证书：cert 与 key 分别属于两个.pem文件
    //默认格式为PEM，可以注释
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, $cert);
    //默认格式为PEM，可以注释
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, $key);
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    $data = curl_exec($ch);

    //返回结果
    if ($data) {
        curl_close($ch);
        return xmlToArray($data);
    } else {
        $error = curl_errno($ch);
        echo "curl出错，错误码:$error" . "<br>";
        curl_close($ch);
        return false;
    }
}


//将XML转为array
function xmlToArray($xml)
{
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}

//数组转XML
function arrayToXml($arr)
{
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        } else {
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
    }
    $xml .= "</xml>";
    return $xml;
}


function complem_number($num = 1)
{
    //自动补全编号
    $temp_num = 100000001;
    $new_num = $num + $temp_num;
    $real_num = substr($new_num, 1, 8); //即截取掉最前面的“1”
    return $real_num;
}

function add_material($url, $imgurl)
{
    $file_path = $imgurl;

    $file_data = array('media' => '@' . $file_path);
    // $file_data = array("media"  =>  new CURLFile($file_path));
    $file_data = array("media" => new CURLFile($file_path));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //需要获取的URL地址，也可以在curl_init()函数中设置。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //使用PHP curl获取页面内容或提交数据，有时候希望返回的内容作为变量储存，
    //而不是直接输出。这个时候就必需设置curl的CURLOPT_RETURNTRANSFER选项为1或true
    curl_setopt($ch, CURLOPT_POST, 1);
    //发送一个POST请求
    curl_setopt($ch, CURLOPT_POSTFIELDS, $file_data);
    //传递一个关联数组，生成multipart/form-data的POST请求
    $output = curl_exec($ch);//发送请求获取结果
    curl_close($ch);//关闭会话
    return $output;//返回结果
}

function user_no($user_no)
{
    return str_pad($user_no, 6, "0", STR_PAD_LEFT);
}

/**
 * 生成订单号
 * @author hejianyu
 */
function orderNum()
{
    // return  chr(rand(65, 90)) . date('YmdHis', time()) . numberCaptcha(4);
    return chr(rand(65, 90)) . date('YmdHis', time()) . chr(rand(65, 90));
}

function check_card($idcard, $name)
{
    //d1299ffb9ceb1f5efa07517dab177908
    //6f74145cbd7989a4f216b51ee25dfd98
    $url = 'http://op.juhe.cn/idcard/query?key=6f74145cbd7989a4f216b51ee25dfd98&idcard=' . $idcard . '&realname=' . urlencode($name);
    $http_info = http_curl($url, 'get');

    $msg = '';
    if ($http_info['error_code'] == 0) {
        return 1;
    } else {
        switch ($http_info['error_code']) {
            case '210301':
                $msg = "库中无此身份证记录";
                break;
            case '210302':
                $msg = "第三方服务器异常";
                break;
            case '210303':
                $msg = "服务器维护";
                break;
            case '210304':
                $msg = "姓名或身份证格式错误或请求次数过于频繁";
                break;
            case '210305':
                $msg = "网络错误，请重试";
                break;
            case '210306':
                $msg = "数据源错误，具体参照reason";
                break;
            case '210307':
                $msg = "sign错误";
                break;
            case '112':
                $msg = "身份证请求查询次数不足";
                break;
        }
        return $msg;
    }

}

/**
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
 * @param string $user_name 姓名
 * @return string 格式化后的姓名
 */
function substr_cut($user_name){
    $strlen = mb_strlen($user_name, 'utf-8');
    $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
    $lastStr = mb_substr($user_name, -1, 1, 'utf-8');
    if($strlen<2) {
        return $user_name;
    } else {
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }
}

/**
 * email隐藏
 * @param $email
 * @return string
 */
function obfuscate_email($email)
{
    $em   = explode("@",$email);
    $name = implode(array_slice($em, 0, count($em)-1), '@');
    $len  = floor(strlen($name)/2);

    return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
}

/**
 * 对银行卡号进行掩码处理
 * 掩码规则头4位,末尾余数位不变，中间4的整数倍字符用星号替换，并且用每隔4位空格隔开
 * @author 晓风<215628355@qq.com>
 * @param  string $bankCardNo 银行卡号
 * @return string             掩码后的银行卡号
 */
function formatBankCardNo($bankCardNo){
//每隔4位分割为数组
    $split = str_split($bankCardNo,4);
//头和尾保留，其他部分替换为星号
    $split = array_fill(1,count($split) - 2,"****") + $split;
    ksort($split);
//合并
    return implode(" ",$split);
}


//下载文件
function download_file($file)
{
    if (is_file($file)) {
        $length = filesize($file);
        $type = mime_content_type($file);
        $showname = ltrim(strrchr($file, '/'), '/');

        header("Content-Description: File Transfer");
        header('Content-type: ' . $type);
        header('Content-Length:' . $length);
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
            header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $showname . '"');
        }
        readfile($file);
        exit;
    } else {
        exit('文件已被删除！');
    }
}

/**
 * 生成二维码 方法
 */
function qr_code($url)
{
    //引入vendor 下phpqrcode 文件
    vendor('phpqrcode.phpqrcode');

    $filename = uniqid() . '.png';
    $path = 'qrcode/' . date('Ymd', time()) . '/';

    //创建文件
    if (!file_exists($path)) {
        mkdir($path, 0777, true);//创建目录
        chmod($path, 0777);//赋予权限
    }
    $filepath = $path . $filename;

    $errorCorrectionLevel = 'L';//纠错级别 :L,M,Q,H
    $matrixPointSize = "5";     //生成图片大小 :1到10
    QRcode::png($url, $filepath, $errorCorrectionLevel, $matrixPointSize, 2);

    return config('site_url') . '/' . $filepath;
    exit();
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
 * excel表格导出
 * @param string $fileName 文件名称
 * @param array $headArr 表头名称
 * @param array $data 要导出的数据
 * @author Eric
 */

function excelExport($fileName = '', $headArr = [], $data = [])
{

    $fileName .= date("Y_m_d_H_i", time()) . rand(1, 100) . ".xlsx";
    vendor("phpexcel.PHPExcel"); //方法一
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getProperties();
    $key = ord("A"); // 设置表头

    foreach ($headArr as $v) {
        $colum = chr($key);
        $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        $key += 1;
    }
    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();
    foreach ($data as $key => $rows) { // 行写入
        set_time_limit(0);
        $span = ord("A");
        foreach ($rows as $keyName => $value) { // 列写入
            set_time_limit(0);
            $objActSheet->setCellValue(chr($span) . $column, $value);
            $span++;
        }
        $column++;
    }
    $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表
    $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=" . $fileName);
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output'); // 文件通过浏览器下载
    exit();
}


/**
 * excel表格导出
 * @param string $fileName 文件名称
 * @param array $headArr 表头名称
 * @param array $data 要导出的数据
 * @author Eric
 */

function excelExportSaveLocal($fileName = '', $headArr = [], $data = [])
{

    $fileName = $fileName . ".xlsx";
    vendor("phpexcel.PHPExcel"); //方法一
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getProperties();
    $key = ord("A"); // 设置表头

    foreach ($headArr as $v) {
        $colum = chr($key);
        $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($colum)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        $key += 1;
    }
    $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=" . $fileName);
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//    $fileName = iconv('utf-8', 'gb2312', $fileName); // 重命名表
    $dir = ROOT_PATH . 'public' . DS . 'channel/';
    $save_path = $dir . $fileName;
    try {
        $objWriter->save($save_path); // 文件通过浏览器下载
    } catch (\Exception $e) {
        return false;
    }
    return 'channel/' . $fileName;
}

function data($num, $val)
{
    return array(
        'status' => $num,
        'msg' => $val
    );
}

//判断两个字符串是否相等
function trie_mall($new, $str)
{
    $oldchar = array(" ", "　", "\t", "\n", "\r");
    $newchar = array("", "", "", "", "");
    $new_str = str_replace($oldchar, $newchar, $str);
    if ($new === $new_str) {
        return 1;
    } else {
        return 2;
    }
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int $expire 过期时间 单位 秒
 * return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function encode($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? 'SRWERWE1232' : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key 加密密钥
 * return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function decode($data, $key = '')
{
    $key = md5(empty($key) ? 'SRWERWE1232' : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);
    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数字验证码
 * @author hejianyu
 */
function numberCaptcha($length = 4)
{
    (int)$min = substr(10000000000, 0, $length);
    (int)$max = substr(99999999999, 0, $length);
    return rand($min, $max);
}

/**
 * 格式化打印数组
 * @param $arr
 * @author zhengjingqiang
 * @email scenewood@163.com
 */
function print_t($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre><hr/>';
    exit;
}

//微信用户名解密
function urlsafe_b64decode($string)
{
    $data = str_replace(array('-', '_'), array('+', '/'), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

//微信用户名加密
function urlsafe_b64encode($string)
{
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
    return $data;
}

function dd($arr)
{
    print_t($arr);
}

/***** 身份证验证*******/
function validation_filter_id_card($id_card)
{
    if (strlen($id_card) == 18) {
        return idcard_checksum18($id_card);
    } elseif ((strlen($id_card) == 15)) {
        $id_card = idcard_15to18($id_card);
        return idcard_checksum18($id_card);
    } else {
        return false;
    }
}

// 计算身份证校验码，根据国家标准GB 11643-1999
function idcard_verify_number($idcard_base)
{
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}

// 将15位身份证升级到18位
function idcard_15to18($idcard)
{
    if (strlen($idcard) != 15) {
        return false;
    } else {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
            $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
        } else {
            $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
        }
    }
    $idcard = $idcard . idcard_verify_number($idcard);
    return $idcard;
}


// 18位身份证校验码有效性检查
function idcard_checksum18($idcard)
{
    if (strlen($idcard) != 18) {
        return false;
    }
    $idcard_base = substr($idcard, 0, 17);
    if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
        return false;
    } else {
        return true;
    }
}

/***** 身份证验证结束*******/


/**
 * 无限分类
 * @param $array
 * @param int $pid
 * @return array
 * @author zhengjingqiang
 * @email scenewood@163.com
 */
function menuTree($array, $pid)
{
    $arr = array();
    foreach ($array as $v) {
        if ($v['pid'] == $pid) {
            $tem = menuTree($array, $v['id']);
//            if($v['parameter'] && $v['url']){
//                $params = json_decode($v['parameter'], true);
//                $v['urlStr'] = URL($v['url'], $params);
//            } elseif($v['url']) {
//                $v['urlStr'] = URL($v['url']);
//            }
            //判断是否存在子数组
            $tem && $v['child'] = $tem;
            $arr[] = $v;
        }
    }
    return $arr;
}

function menuTree_two($array, $pid)
{
    $arr = array();
    foreach ($array as $v) {
        if ($v['reply_user_id'] == $pid) {
            $tem = menuTree($array, $v['user_id']);
//            if($v['parameter'] && $v['url']){
//                $params = json_decode($v['parameter'], true);
//                $v['urlStr'] = URL($v['url'], $params);
//            } elseif($v['url']) {
//                $v['urlStr'] = URL($v['url']);
//            }
            //判断是否存在子数组
            $tem && $v['child'] = $tem;
            $arr[] = $v;
        }
    }
    return $arr;
}

/***** 生成二维码*******/
function getQrcode($id, $type)
{
    $EarlyRise = D('EarlyRise');
    $info = $EarlyRise->field('title,id,picture,content')->find($id);
    $info['content'] = strip_tags(htmlspecialchars_decode($info['content']));
    return $info;
}


/**
 * 密码加密
 * @author hejianyu
 */
function encryption($str)
{
    $result = sha1(sha1($str, true));
    return $result;
}

function Allerror()
{
    return $arr = array('errorCode' => '10000', 'msg' => '获取参数错误');
}


/**
 * 注册IP
 * @author hejianyu
 */
function registerIp()
{
    $registerIp = get_client_ip();
    return $registerIp;
}


/**
 * 遍历获取目录下的指定类型的文件
 * @param $path      目录地址
 * @param array $files
 * @return array
 * @author jihaichuan
 * @date 2015-10-16
 */
function getfiles($path, $allowFiles, &$files = array())
{
    if (!is_dir($path)) return null;
    if (substr($path, strlen($path) - 1) != '/') $path .= '/';
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $path2 = $path . $file;
            if (is_dir($path2)) {
                getfiles($path2, $allowFiles, $files);
            } else {
                if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
                    $files[] = array(
                        'url' => substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                        'mtime' => filemtime($path2)
                    );
                }
            }
        }
    }
    return $files;
}


/**
 * 无限分类
 * @param $array
 * @param int $pid
 * @return array
 */
function tree($array, $pid = 0)
{
    $arr = array();
    $tem = array();
    foreach ($array as $v) {
        if ($v['pid'] == $pid) {
            if (isset($v['url'])) {
                $parameter = array();
                if ($v['parameter']) {
                    $parameter = json_decode($v['parameter'], true);
                }
                $v['url'] = URL($v['url'], $parameter);
            }
            $tem = tree($array, $v['id']);
            //判断是否存在子数组
            $tem && $v['children'] = $tem;
            $arr[] = $v;
        }
    }
    return $arr;
}


/**
 * 到期时间
 * @author jihaichuan
 */
function expirationTime()
{
    return time() + 60 * 10;
}


/**
 * 将邮箱中间部分转换成*
 * @param $email 邮箱
 * @return mixed
 * @author hejianyu
 */
function hideEmail($email)
{
    $n = strpos($email, '@');
    if ($n < 3) {
        $info = substr_replace($email, "****", $n, 0);
    } elseif ($n < 6) {
        $info = substr_replace($email, "****", 2, $n - 2);
    } else {
        $info = substr_replace($email, "****", 2, $n - 4);
    }
    return $info;
}


/**
 * 格式化金额
 * @param $price
 * @return float|string
 * @author jihaichuan
 */
function formatAmount($price)
{
    if (!$price) return '0.00';
    return number_format($price / 100, 2);
}


/** 将字符串转换成数组
 * @param string $str
 * @return array
 * @author jihaichuan
 */
function nl2brStrArr($str = '')
{
    $listArr = explode('<br />', nl2br($str));
    $listArray = array();
    if ($listArr) {
        foreach ($listArr as $k => $v) {
            if ($v) {
                $listArray[] = $v;
            }
        }
    }
    return $listArray;
}


/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * return 签名结果
 */
function md5Sign($prestr, $key)
{
    $prestr = $prestr . $key;
    return md5($prestr);
}

/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * @param $key 私钥
 * return 签名结果
 */
function md5Verify($prestr, $sign, $key)
{
    $prestr = $prestr . $key;
    $mysgin = md5($prestr);

    if ($mysgin == $sign) {
        return true;
    } else {
        return false;
    }
}


/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstring($para)
{
    $arg = "";
    while (list ($key, $val) = each($para)) {
        $arg .= $key . "=" . $val . "&";
    }
    //去掉最后一个&字符
    $arg = substr($arg, 0, count($arg) - 2);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstringUrlencode($para)
{
    $arg = "";
    while (list ($key, $val) = each($para)) {
        $arg .= $key . "=" . urlencode($val) . "&";
    }
    //去掉最后一个&字符
    $arg = substr($arg, 0, count($arg) - 2);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}

/**
 * 除去数组中的空值和签名参数
 * @param $para 签名参数组
 * return 去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para)
{
    $para_filter = array();
    while (list ($key, $val) = each($para)) {
        if ($key == "sign" || $key == "sign_type" || $val == "") continue;
        else    $para_filter[$key] = $para[$key];
    }
    return $para_filter;
}

/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para)
{
    ksort($para);
    reset($para);
    return $para;
}

/**
 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
 * 注意：服务器需要开通fopen配置
 * @param $word 要写入日志里的文本内容 默认值：空值
 */
function logResult($word = '')
{
    $fp = fopen("log.txt", "a");
    flock($fp, LOCK_EX);
    fwrite($fp, "执行日期：" . strftime("%Y%m%d%H%M%S", time()) . "\n" . $word . "\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * 远程获取数据，POST模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param string $url 指定URL完整路径地址
 * @param string|bool $cacert_url 指定当前工作目录绝对路径
 * @param array = $para 请求的数据
 * return 远程输出的数据
 */
function getHttpResponsePOST( $url, $para, $cacert_url = false)
{

    $curl = curl_init($url);
    if ($cacert_url !== false){
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
    }else{
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl, CURLOPT_POST, true); // post传输数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $para);// post传输数据
    $responseText = curl_exec($curl);
//    var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseText;
}

/**
 * @param $url
 * @param $params
 * @param $ispost
 * @param $headers
 * @param $https
 * @return array|false
 */
function curl($url, $params = false, $ispost = 0, $headers = [], $https = 0)
{
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($https) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
//        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
    }
    if ($headers){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }

    $response = curl_exec($ch);
    if ($response === false){
//        return curl_error($ch);
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return ['code' => $httpCode,'data' => $response];
}

/**
 * 远程获取数据，GET模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * return 远程输出的数据
 */
function getHttpResponseGET($url, $cacert_url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseText;
}

/**
 * 实现多种字符编码方式
 * @param $input 需要编码的字符串
 * @param $_output_charset 输出的编码格式
 * @param $_input_charset 输入的编码格式
 * return 编码后的字符串
 */
function charsetEncode($input, $_output_charset, $_input_charset)
{
    $output = "";
    if (!isset($_output_charset)) $_output_charset = $_input_charset;
    if ($_input_charset == $_output_charset || $input == null) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
    } elseif (function_exists("iconv")) {
        $output = iconv($_input_charset, $_output_charset, $input);
    } else die("sorry, you have no libs support for charset change.");
    return $output;
}

/**
 * 实现多种字符解码方式
 * @param $input 需要解码的字符串
 * @param $_output_charset 输出的解码格式
 * @param $_input_charset 输入的解码格式
 * return 解码后的字符串
 */
function charsetDecode($input, $_input_charset, $_output_charset)
{
    $output = "";
    if (!isset($_input_charset)) $_input_charset = $_input_charset;
    if ($_input_charset == $_output_charset || $input == null) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
    } elseif (function_exists("iconv")) {
        $output = iconv($_input_charset, $_output_charset, $input);
    } else die("sorry, you have no libs support for charset changes.");
    return $output;
}


/**
 * 截取字符串
 * @param string $str // 字符串
 * @param int $start // 开始截取位置
 * @param int $end // 结束截取的位置
 * @return string       // 返回截取后的字符串
 * @author jihaichuan
 */
function cutStr($str = '', $start = 0, $end = 10)
{
    if ($str) {
        $count = strlen($str);
        if ($count > $end) {
            $str = mb_strcut($str, $start, $end, 'utf-8') . '...';
        }
        return $str;
    }
}


/**
 * 获取ip地址接口
 * @param string $ip
 * @param string $coor
 * @return mixed
 * @author jihaichuan
 */
function getIpApi($ip = '', $coor = '')
{
    if (!$ip) {
        $ip = get_client_ip();
    }
    // 初始化CURL
    $ch = curl_init();
    $url = 'http://apis.baidu.com/apistore/iplookupservice/iplookup?ip=' . $ip;
    $header = array(
        'apikey: ac25999eb56d018d9b8ae215d8b17af9',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch, CURLOPT_URL, $url);
    $res = curl_exec($ch);

    return json_decode($res, true);
}


/**
 * 快递接口
 * @param $com // 物流公司编号，对于数据库表中数据
 * @param $sn // 物流单号
 * @return mixed
 * @author jihaichuan
 */
function getExpressApi($com, $sn)
{
    $ch = curl_init();
    $url = 'http://v.juhe.cn/exp/index?key=52683939544112da0c8d2a8010ddc573&com=' . $com . '&no=' . $sn;

    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch, CURLOPT_URL, $url);
    $res = curl_exec($ch);

    return json_decode($res, true);
}


/**
 * 快递100-生成查询快递iFrame标签内容的URL地址
 * @param $com // 快递公司编号
 * @param $sn // 快递单号
 * @author jihaichuan
 */
function buildExpressUrl($com, $sn)
{
    // 快递100公司编号
    $name = '';
    switch ($com) {
        case 'sf':
            $name = 'shunfeng';
            break;
        case 'sto':
            $name = 'shentong';
            break;
        case 'zto':
            $name = 'zhongtong';
            break;
        default:
            $name = 'shunfeng';
            break;
    }

    // 组合URL地址
    return 'http://www.kuaidi100.com/chaxun?com=' . $name . '&nu=' . $sn;
}

/**
 * @param $map 传入时间戳
 * @return int
 * Date: 2019/11/22
 * Time: 21:04
 * USER:GCQ
 */
function remainingDay($map)
{
    $time = $map - time();
    if ($time >= 86400) {
        $day = intval($time / 86400);
    } else {
        $day = 0;
    }
    return $day;
}

/**
 * 剩余时间
 * @param timer $
 * @return mixed
 * @author jihaichuan
 */
function remainingTime($timer)
{
    $timeArr = array();
    // 判断天数
    if ($timer >= 86400) {
        $timeArr['day'] = intval($timer / 86400);
        if ($timeArr['day'] < 10) {
            $timeArr['day'] = '0' . $timeArr['day'];
        }
        $timer = ($timer % 86400);
    } else {
        $timeArr['day'] = '00';
    }
    // 判断小时
    if ($timer < 86400 && $timer >= 3600) {
        $timeArr['hour'] = intval($timer / 3600);
        if ($timeArr['hour'] < 10) {
            $timeArr['hour'] = '0' . $timeArr['hour'];
        }
        $timer = ($timer % 3600);
    } else {
        $timeArr['hour'] = '00';
    }
    // 判断分钟
    if ($timer < 3600 && $timer > 60) {
        $timeArr['minute'] = intval($timer / 60);
        if ($timeArr['minute'] < 10) {
            $timeArr['minute'] = '0' . $timeArr['minute'];
        }
        $timer = ($timer % 60);
    } else {
        $timeArr['minute'] = '00';
    }
    // 判断秒
    if ($timer < 60 && $timer >= 1) {
        $timeArr['second'] = $timer;
        if ($timeArr['second'] < 10) {
            $timeArr['second'] = '0' . $timeArr['second'];
        }
    } else {
        $timeArr['second'] = '00';
    }
    return $timeArr;
}


/**
 * 字符串替换星号方法
 * @param String $str 要替换的字符串
 * @param Integer $start 前面保留几位
 * @param Integer $middle 中间替换几位
 * @param Integer $end 最后保留几位
 */
function starStrConvert($str, $start, $middle, $end)
{
    $pattern = "/(\w{" . $start . "})(\w{" . $middle . "})(\w{" . $end . "})/";
    $replacement = '$1****$3';
    $str = preg_replace($pattern, $replacement, $str);
    return $str;
}


/**
 * 生成用户昵称
 * @param $mobile 手机号码后六位
 * @param int $n 重复时数字后缀
 * @return string
 * @author hejianyu
 */
function createNickname($mobile, $n = 0)
{
    $name = '台球圈';
    $nickname = $name . $mobile;

    if ($n != 0) {
        $nickname = $nickname . $n;
    }

    //昵称是否被使用
    $info = R('Api/Member/isNickname', array('name' => $nickname));
    if ($info) {
        return createNickname($mobile, $n + 1);
    } else {
        return $nickname;
    }
}


/**
 * @param $msec
 * @return string
 *  转换成小时分钟
 */
function Msec2Time($msec)
{
    if (is_numeric($msec)) {
        $sec = abs(ceil(($msec - time())));
        $days = intval($sec / (3600 * 24));
        $hours = intval(($sec % (3600 * 24)) / 3600);
        $minute = ceil(($sec % 3600) / 60);

        if ($days) {
            if ($days <= 1) {
                return $days . '天前';
            } elseif (date('Y', time()) == date('Y', $msec)) {
                return date('m-d', $msec);
            } else {
                return date('Y-m-d', $msec);
            }

        } elseif ($hours) {
            return $hours . '小时前';
        } elseif ($minute) {
            return $minute . '分钟前';
        } else {
            return '刚刚';
        }

    }
}

/**
 * @param $msec
 * @return string
 * 转换成小时分钟
 */
function ConTime($msec)
{
    if (is_numeric($msec)) {

        $hours = intval($msec / 3600);
        $minute = ceil(($msec % 3600) / 60);
        $time = array(
            $hours ? $hours . '小时' : '', //小时数
            $minute ? $minute . '分钟' : ''//分钟数
        );
        return implode('', $time);
    }
}

/**
 * @param $sec
 * @return string
 * 把秒转换成 分 秒
 */
function changeTimeType($seconds)
{
    if ($seconds > 3600) {
        $hours = intval($seconds / 3600);
        $minutes = $seconds % 3600;
        $time = $hours . "时" . gmstrftime('%M分%S秒', $minutes);
    } else {
        if ($seconds < 60) {
            $time = gmstrftime('%S秒', $seconds);
        } else {
            $time = gmstrftime('%M分%S秒', $seconds);
        }


    }
    return $time;
}


/**
 * 将日期格式根据以下规律修改为不同显示样式
 * 小于1分钟 则显示多少秒前
 * 小于1小时，显示多少分钟前
 * 一天内，显示多少小时前
 * 3天内，显示前天22:23或昨天:12:23。
 * 超过3天，则显示完整日期。
 * @static
 * @param  int $sorce_date 数据源日期 unix时间戳
 * @return void
 */
function getDateStyle($sorce_date)
{

    $nowTime = time();  //获取今天时间戳

    $timeHtml = ''; //返回文字格式
    $temp_time = 0;
    switch ($sorce_date) {

        //一分钟
        case ($sorce_date + 60) >= $nowTime:
            $temp_time = $nowTime - $sorce_date;
            $timeHtml = $temp_time . "秒前";
            break;

        //小时
        case ($sorce_date + 3600) >= $nowTime:
            $temp_time = date('i', $nowTime - $sorce_date);
            $timeHtml = $temp_time . "分钟前";
            break;

        //天
        case ($sorce_date + 3600 * 24) >= $nowTime:
            $temp_time = date('H', $nowTime) - date('H', $sorce_date);
            $timeHtml = $temp_time . '小时前';
            break;

        //昨天
        case ($sorce_date + 3600 * 24 * 2) >= $nowTime:
            $temp_time = date('H:i', $sorce_date);
            $timeHtml = '昨天' . $temp_time;
            break;

        //前天
        case ($sorce_date + 3600 * 24 * 3) >= $nowTime:
            $temp_time = date('H:i', $sorce_date);
            $timeHtml = '前天' . $temp_time;
            break;

        //3天前
        case ($sorce_date + 3600 * 24 * 4) >= $nowTime:
            $timeHtml = '3天前';
            break;

        default:
            $timeHtml = date('Y-m-d', $sorce_date);
            break;

    }
    return $timeHtml;

}

/**
 *  发送短信通知
 */

function http_send_message($mobile, $str)
{
    $row = [];
    $url = 'http://smss.1tai.com/sms/server/send';
    $data = [
        'sign_name' => "TheONE钢琴盛典",
        'phone_number' => $mobile,
        'content' => $str
    ];
    $header = array(
        'Postman-Token:a714ad5f-dce5-a759-b883-e92e6220fe98',
        'Cache-Control:no-cache',
        'Content-Type:application/json'
    );
    $info = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $info);
    $res = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    $row = json_decode($res, true);
    return $row['meta']['code'];
}

/**
 * @param $url
 * @param string $type
 * @param string $res
 * @param string $header
 * @return mixed|string
 * 获取CURL请求
 */
function http_curl($url, $type = 'get', $res = '', $header = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    switch ($type) {
        case 'post':
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $res);
            break;
        case 'put':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $res);
            break;
    }
    $outopt = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return json_decode($outopt, true);
}

/**
 * 判定是否为儿童(低于12岁)
 */
function getIDCardInfo($IDCard)
{

    if (strlen($IDCard) == 18) {
        $tyear = intval(substr($IDCard, 6, 4));
        $tmonth = intval(substr($IDCard, 10, 2));
        $tday = intval(substr($IDCard, 12, 2));
        if ($tyear > date("Y") || $tyear < (date("Y") - 100)) {
            $flag = 0;
        } elseif ($tmonth < 0 || $tmonth > 12) {
            $flag = 0;
        } elseif ($tday < 0 || $tday > 31) {
            $flag = 0;
        } else {
            $tdate = $tyear . "-" . $tmonth . "-" . $tday . " 00:00:00";
            if ((time() - mktime(0, 0, 0, $tmonth, $tday, $tyear)) > 12 * 365 * 24 * 60 * 60) {
                $flag = 0;
            } else {
                $flag = 1;
            }
        }
    } elseif (strlen($IDCard) == 15) {
        $tyear = intval("19" . substr($IDCard, 6, 2));
        $tmonth = intval(substr($IDCard, 8, 2));
        $tday = intval(substr($IDCard, 10, 2));
        if ($tyear > date("Y") || $tyear < (date("Y") - 100)) {
            $flag = 0;
        } elseif ($tmonth < 0 || $tmonth > 12) {
            $flag = 0;
        } elseif ($tday < 0 || $tday > 31) {
            $flag = 0;
        } else {
            $tdate = $tyear . "-" . $tmonth . "-" . $tday . " 00:00:00";
            if ((time() - mktime(0, 0, 0, $tmonth, $tday, $tyear)) > 12 * 365 * 24 * 60 * 60) {
                $flag = 0;
            } else {
                $flag = 1;
            }
        }
    }
    $result['error'] = 2;//0：未知错误，1：身份证格式错误，2：无错误
    $result['isAdult'] = $flag;//0标示成年，1标示未成年
    $result['birthday'] = $tdate;//生日日期
    return $result;
}

/**
 * 返回成功数据
 * @param $data
 * @author jihaichuan
 */
function json_success($data)
{
    header('HTTP/1.1 200 OK');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * 返回成功数据
 * @param $data
 * @author jihaichuan
 */
function json_gate_success($data)
{
    header('HTTP/1.1 200 OK');
    // header('Content-Type: text/html; charset=GBK');
    header("Content-type: application/json; charset=GB2312");
    echo mb_convert_encoding(json_encode($data, JSON_UNESCAPED_UNICODE), 'GB2312', 'auto');
//     echo iconv("utf-8","GB2312//TRANSLIT",json_encode($data));
//     echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}


/**
 * 返回成功数据
 * @param $data
 * @author jihaichuan
 */
function json_unicode_success($data)
{
    header('HTTP/1.1 200 OK');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}


/**
 * JSON 错误数据返回
 * @param $string
 * @author jihaichuan
 */
// function json_fail($string)
// {
//     header('HTTP/1.1 500 Internal Server Error');
//     header("Content-type: Application/json; charset=utf-8");
//     if (is_array($string)) {
//         echo json_encode($string);
//         exit;
//     } else {
//         echo $string;
//     }
//     exit;
// }
function res_data($status,$msg,$data=[]){

    $datainfo=[
        'status'=>$status,
        'msg'=>$msg,
        'data'=>$data
    ];
    return $datainfo;
}


/**
 * JSON 错误数据返回
 * @param $string
 * @author jihaichuan
 */
function json_fail($string,$httpCode=500)
{
    header('HTTP/1.1 '.$httpCode.' Internal Server Error');
    if(is_array($string)){
        echo json_encode($string);exit;
    }else{
        echo $string;
    }
    exit;
}
    /**
     * JSON 错误数据返回
     * @param $string
     * @author jihaichuan
     */

/**
 *
 * 获取星期几
 */

function get_week($date)
{
    //强制转换日期格式
    $date_str = date('Y-m-d', strtotime($date));

    //封装成数组
    $arr = explode("-", $date_str);

    //参数赋值
    //年
    $year = $arr[0];

    //月，输出2位整型，不够2位右对齐
    $month = sprintf('%02d', $arr[1]);

    //日，输出2位整型，不够2位右对齐
    $day = sprintf('%02d', $arr[2]);

    //时分秒默认赋值为0；
    $hour = $minute = $second = 0;

    //转换成时间戳
    $strap = mktime($hour, $minute, $second, $month, $day, $year);

    //获取数字型星期几
    $number_wk = date("w", $strap);

    //自定义星期数组
    $weekArr = array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");

    //获取数字对应的星期
    return $weekArr[$number_wk];
}

/**
 *
 * 数组排序
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = array();
        foreach ($list as $i => $data) {
            $refer[$i] = $data[$field];
        }
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = $list[$key];
        }
        return $resultSet;
    }
    return false;
}

/**
 * 二位数组去除重复值
 * @param $arr 传入数组
 * @param $key 判断的key值
 * @return array
 */
function array_unset($arr, $key)
{   //$arr->传入数组   $key->判断的key值
    //建立一个目标数组
    $res = array();
    foreach ($arr as $value) {
        //查看有没有重复项
        if (isset($res[$value[$key]])) {
            //有：销毁
            unset($value[$key]);
        } else {
            $res[$value[$key]] = $value;
        }
    }
    return $res;
}

/**
 *
 *模板消息
 */
function sendTemplate($touser, $template_id, $form_id, $page, $data)
{

    $dataInfo = array(
        'touser' => $touser,
        'template_id' => $template_id,
        'form_id' => $form_id,
        'page' => $page,
        'data' => $data
    );
    $submitData = json_encode($dataInfo);
    //实例化表
    $token = Db('wx_token')->limit(1)->value('access_token');
    //获取模板消息
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $token;
    $info = http_curl($url, 'post', $submitData);

    return $info;
}

function download($file_url, $new_name = '')
{

    if (!isset($file_url) || trim($file_url) == '') {
        echo '500';
    }
    if (!file_exists($file_url)) { //检查文件是否存在
        echo '404';
    }


    $file_name = basename($file_url);
    $file_type = explode('.', $file_url);
    $file_type = $file_type[count($file_type) - 1];
    $file_name = trim($new_name == '') ? $file_name : urlencode($new_name);
    $file_type = fopen($file_url, 'r'); //打开文件
    //输入文件标签
    header("Content-type: application/octet-stream");
    header("Accept-Ranges: bytes");
    header("Accept-Length: " . filesize($file_url));
    header("Content-Disposition: attachment; filename=" . $file_name);
    //输出文件内容
    echo fread($file_type, filesize($file_url));

    fclose($file_type);
}

/**
 * 二维数组根据某个字段排序
 * @param array $array 要排序的数组
 * @param string $keys 要排序的键字段
 * @param string $sort 排序类型  SORT_ASC     SORT_DESC
 * @return array 排序后的数组
 */
function arraySort($array, $keys, $sort = SORT_DESC)
{
    $keysValue = [];
    foreach ($array as $k => $v) {
        $keysValue[$k] = $v[$keys];
    }
    array_multisort($keysValue, $sort, $array);
    return $array;
}

/**
 * @param $start_time
 * @param $end_time
 * @return float
 * 计算相差几天
 */
function get_between_day($start_time, $end_time)
{
    $d1 = strtotime($start_time);
    $d2 = strtotime($end_time);
    $day = round(($d2 - $d1) / 3600 / 24);

    return $day;
}


/**
 * @param $array
 * @return array
 * 调用这个函数，将其幻化为数组，然后取出对应值
 * 对象转数组
 */
function object_array($array)
{
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}


/**
 * 获取access_token
 * @return access_token
 */
function access_token()
{
    $wechat_token = new \app\common\model\WechatToken();
    $access_token = $wechat_token->whereTime('updatetime', '-2 hours')->value('access_token');
    if ($access_token) {
        return $access_token;
    } else {
        $appid = config('wechat_app_id');
        $appsecret = config('wechat_app_secret');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
        $access_token = http_curl($url, 'get');
        if (isset($access_token['errcode'])) {
            json_fail($access_token['errcode']);
        } else {
            $save['updatetime'] = date('Y-m-d H:i:s', time());
            $save['access_token'] = $access_token['access_token'];
            $wechat_token->where('wxatid', 1)->update($save);
            return $access_token['access_token'];
        }
    }
}

// 步骤1.设置appid和appsecret
//$appid = 'wxd75a2b20d3a54752';
//$appsecret = '9b32270f32874ea7a7427f88ff770777';
// 步骤2.生成签名的随机串
function nonceStr($length)
{
    $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //62个字符
    $strlen = 62;
    while ($length > $strlen) {
        $str .= $str;
        $strlen += 62;
    }
    $str = str_shuffle($str);
    return substr($str, 0, $length);
}

function getRandomString($len, $chars=null)
{
    if (is_null($chars)){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;

}

// 步骤3.获取access_token
//    access_token();
function http_get($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

// 步骤4.获取ticket
//
//$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$access_token";
//$res = json_decode ( http_get ( $url ) );
//$ticket = $res->ticket;
//获取微信签名所需的 ticket
function getTicket()
{
    $wxTicket = new \app\common\model\WxTicket();
    $wx_ticket = $wxTicket->whereTime('update_time', '-2 hours')->value('ticket');
    if (!empty($wx_ticket)) {
        return $wx_ticket;
    } else {
        $token = access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$token&type=jsapi";
//        $tmp = $this->http_get($url); //json格式
        $res = json_decode(http_get($url));//json格式
//        $obj = json_decode($tmp);
        $save['update_time'] = date('Y-m-d H:i:s', time());
        $save['ticket'] = $res->ticket;
        $wxTicket->where(array('id' => 1))->update($save);
        return $res->ticket;
    }

}

// 步骤5.生成wx.config需要的参数
function getWxConfig($url)
{
    $timestamp = time();
    $nonceStr = nonceStr(16);   //获取签名随机串
//    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; //获取当前访问的URL
//    $url = 'http://huirui-dev.xiaocx.org/index.html'; //获取当前访问的URL
    $jsapiTicket = getTicket();
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    $signature = sha1($string);
    $WxConfig["appId"] = config('wechat_app_id');
    $WxConfig["nonceStr"] = $nonceStr;
    $WxConfig["timestamp"] = $timestamp;
    $WxConfig["url"] = $url;
    $WxConfig["signature"] = $signature;
    $WxConfig["rawString"] = $string;

    return $WxConfig;
}


//$day_list = array(strtotime('2016-06-29'),strtotime('2016-06-28'),strtotime('2016-06-27'),strtotime('2016-06-22'));
//$day_list = array('1467164018','1467100301','1466985253','1466901657','1466839901','1466839901','1466670876');

//$days = getContinueDay(array_unique($day_list));
//echo $days;
/**
 * PHP计算当前连续天数
 * @param $day_list
 * @return int
 * Date: 2019/12/20
 * Time: 15:29
 * USER:GCQ
 */
function getContinueDay($day_list)
{
    //昨天开始时间戳
    $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));

    if ($beginYesterday > $day_list[0]) {
        $days = 0;
    } else {
        $days = 1;
    }

    $count = count($day_list);
    dump($day_list);
    for ($i = 0; $i < $count; $i++) {
        if ($i < $count - 1) {
            $res = compareDay($day_list[$i], $day_list[$i + 1]);
            if ($res) {
                $days++;
            } else {
                break;
            }
        }
    }

    return $days;
}

function compareDay($curDay, $nextDay)
{
    $lastBegin = mktime(0, 0, 0, date('m', $curDay), date('d', $curDay) - 1, date('Y', $curDay));
    $lastEnd = mktime(0, 0, 0, date('m', $curDay), date('d', $curDay), date('Y', $curDay)) - 1;

    if ($nextDay >= $lastBegin && $nextDay <= $lastEnd) {
        return true;
    } else {
        return false;
    }

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
function changeDateTypes($duration)
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
 * 把HH:MM:SS格式的时间字符串转换成秒数，可以使用date_parse函数解析具体的时间信息。
 * @param $time
 * @return float|int|mixed
 * 2020/5/29
 * 22:10
 */
function changeTimeFormat($time = '')
{
//    if (empty($time)){
//        $time=date('H:i:s',time());
//    }
    $count = count(explode(':', $time));
    if ($count == 2) {
        $time = '00:' . $time;
    } elseif ($count == 1) {
        $time = '00:00:' . $time;
    }
    $parsed = date_parse($time);
    $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
    return $seconds;
}


/**
 * $seconds = 3600*34 + 122;
 * @param $seconds
 * @return string
 * 2020/5/29
 * 21:51
 */

function changeTimeTypes($seconds)
{
    if ($seconds >= 3600) {
        $hours = intval($seconds / 3600);
        $minutes = $seconds % 3600;
        $time = $hours . ":" . gmstrftime('%M:%S', $minutes);
    } else {
        if ($seconds >= 60) {
//                    $time = gmstrftime('%H:%M:%S', $seconds);
            $time = gmstrftime('%M:%S', $seconds);
        } else {
            $time = gmstrftime('%M:%S', $seconds);
        }
    }
    return $time;
}

/**
 * 时间格式 化 时分秒
 * @param $seconds
 * @return string
 * 2020/6/15
 * 19:24
 */
function changeTimeTypesHis($seconds)
{
//    if ($seconds >= 3600){
    $hours = intval($seconds / 3600);
    $minutes = $seconds % 3600;
    $time = $hours . "小时" . gmstrftime('%M分%S秒', $minutes);
//    }else{
//        if ($seconds>=60){
////                    $time = gmstrftime('%H:%M:%S', $seconds);
//            $time = gmstrftime('%M:%S', $seconds);
//        }else{
//            $time = gmstrftime('%M:%S', $seconds);
//        }
//    }
    return $time;
}

function time_change($seconds)
{
    $hours = intval($seconds / 3600) > 0 ? intval($seconds / 3600) : '00';
    $minute = intval(($seconds % 3600)/60) > 0 ? intval(($seconds % 3600)/60) : '00';
    return $hours.':'.$minute;
}

/**
 * 砍价算法-生成砍价金额
 *
 * @param int $people 砍价人数或次数
 * @param int $amount 砍价总额 单位元
 * @param int $min 最低砍价金额 不得低于0
 * @param int $max 最高砍价金额 砍价次数 * 最高砍价金额不得小于砍价总额
 * @param int $level 层级 防止递归超出限制
 *
 * @return array
 */
function genRandomAmount($people = 0, $totalAmount = 0, $min = 0, $max = 0, $level = 1)
{
    // 防止递归超出限制报异常，提前退出
    if ($level == 200) {
        return [];
    }

    $arr = [];

    // 数据错误直接返回
    if (empty($people) || empty($totalAmount)) {
        return [];
    }

    // 转换成分便于计算
    $tmpTotal = $totalAmount * 100;
    $tmpMin = $min * 100;
    $tmpMax = $max * 100;

    // 计算n-1次的随机金额，如果不减1，则会出现多减一次随机金额的问题，应该是最后的金额直接赋值
    for ($i = 0; $i < $people - 1; $i++) {
        $arr[$i] = mt_rand($tmpMin, $tmpMax);
        $tmpTotal = $tmpTotal - $arr[$i];
    }

    // 最后的价格直接使用最后剩余的价格
    $arr[$people - 1] = $tmpTotal;

    // 最后一次价格小于最小金额或者大于最大金额都不对，继续递归重新计算
    if ($tmpTotal < $tmpMin || $tmpTotal > $tmpMax) {
        return genRandomAmount($people, $totalAmount, $min, $max, $level + 1);
    }

    // 返回单位元的数据
    return array_map(function ($value) {
        return $value / 100;
    }, $arr);
}

/**
 * 砍价算法-获取砍价金额
 *
 * @param int $people 砍价人数或次数
 * @param int $amount 砍价总额
 * @param int $min 最低砍价金额 不得低于0
 * @param int $max 最高砍价金额 砍价次数 * 最高砍价金额不得小于砍价总额
 *
 * @return array
 */
function getRandomAmount($people = 0, $totalAmount = 0, $min = 0, $max = 0)
{
    // 数据错误直接返回
    if (empty($people) || empty($totalAmount)) {
        return [];
    }

    if ($people * $max <= $totalAmount) {
        return false;
    }

    $arr = genRandomAmount($people, $totalAmount, $min, $max);

    // 有几率会因为递归调用超出限制而返回空数组，这里继续重新生成，直到金额正确
    while (empty($arr)) {
        $arr = genRandomAmount($people, $totalAmount, $min, $max);
    }

    return $arr;
}


/**
 * PHP将网页上的图片攫取到本地存储
 * @param $imgUrl  图片url地址
 * @param string $saveDir 本地存储路径 默认存储在当前路径
 * @param null $fileName 图片存储到本地的文件名
 * @return mix
 */
function crabImage($imgUrl, $saveDir = './images/', $fileName = null)
{
    if (empty($imgUrl)) {
        return false;
    }
    //获取图片信息大小
    $imgSize = getImageSize($imgUrl);
    if (!in_array($imgSize['mime'], array('image/jpg', 'image/gif', 'image/png', 'image/jpeg'), true)) {
        return false;
    }

    //获取后缀名
    $_mime = explode('/', $imgSize['mime']);
    $_ext = '.' . end($_mime);

    if (empty($fileName)) {  //生成唯一的文件名
        $fileName = uniqid(time(), true) . $_ext;
    }

    //开始攫取
    ob_start();
    readfile($imgUrl);
    $imgInfo = ob_get_contents();
    ob_end_clean();

    if (!file_exists($saveDir)) {
        mkdir($saveDir, 0777, true);
    }
    $fp = fopen($saveDir . $fileName, 'a');
    $imgLen = strlen($imgInfo);    //计算图片源码大小
    $_inx = 2048;   //每次写入2k
    $_time = ceil($imgLen / $_inx);
    for ($i = 0; $i < $_time; $i++) {
        fwrite($fp, substr($imgInfo, $i * $_inx, $_inx));
    }
    fclose($fp);
    return array('file_name' => $fileName, 'save_path' => $saveDir . $fileName);
}

/**
 * @param $url
 * @param $id
 * @param $name
 * @return string
 * 生成普通带参数的二维码
 */
function get_qr_code($url, $id)
{
//        //带LOGO
//         $errorCorrectionLevel = 'L';//容错级别
//         $matrixPointSize = 9;//生成图片大小
//         //生成二维码图片
//         Vendor('phpqrcode.phpqrcode');
//         $object = new \QRcode();
//         $ad = 'static/Qrcode/'.$id.$name.'.jpg';
//         $object->png($url, $ad, $errorCorrectionLevel, $matrixPointSize, 2);
//         $logo = 'static/Image/logo1.jpg';//准备好的logo图片
//         $QR = 'static/Qrcode/'.$id.$name.'.jpg';//已经生成的原始二维码图
//
//         if ($logo !== FALSE) {
//           $QR = imagecreatefromstring(file_get_contents($QR));
//           $logo = imagecreatefromstring(file_get_contents($logo));
//           $QR_width = imagesx($QR);//二维码图片宽度
//           $QR_height = imagesy($QR);//二维码图片高度
//           $logo_width = imagesx($logo);//logo图片宽度
//           $logo_height = imagesy($logo);//logo图片高度
//           $logo_qr_width = $QR_width / 5;
//           $scale = $logo_width/$logo_qr_width;
//           $logo_qr_height = $logo_height/$scale;
//           $from_width = ($QR_width - $logo_qr_width) / 2;
//           //重新组合图片并调整大小
//           imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
//           $logo_qr_height, $logo_width, $logo_height);
//         }
//         //输出图片  带logo图片
//         imagepng($QR, 'static/Qrcode/'.$id.$name.'.jpg');
    //不带LOGO
    Vendor('phpqrcode.phpqrcode');
    //生成二维码图片
    $object = new \QRcode();
    $level = 3;
    $size = 10;
    $name = uniqid();
    $ad = 'static/Qrcode/' . $id . $name . '.jpg';
    $errorCorrectionLevel = intval($level);//容错级别
    $matrixPointSize = intval($size);//生成图片大小
    $object->png($url, $ad, $errorCorrectionLevel, $matrixPointSize, 2);
    return 'static/Qrcode/' . $id . $name . '.jpg';
}


/**
 * @param null $time
 * @return false|string
 * 2020/8/3
 * 11:50
 * 时间转换格式 几秒  几分 几小时  几天前
 */
function get_last_time($time = NULL)
{
    $text = '';
    $time = $time === NULL || $time > time() ? time() : intval($time);
    $t = time() - $time; //时间差 （秒）
    $y = date('Y', $time) - date('Y', time());//是否跨年
    switch ($t) {
        case $t == 0:
            $text = '刚刚';
            break;
        case $t < 60:
            $text = $t . '秒前'; // 一分钟内
            break;
        case $t < 60 * 60:
            $text = floor($t / 60) . '分钟前'; //一小时内
            break;
        case $t < 60 * 60 * 24:
            $text = floor($t / (60 * 60)) . '小时前'; // 一天内
            break;
        case $t < 60 * 60 * 24 * 3:
            $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time); //昨天和前天
            break;
        case $t < 60 * 60 * 24 * 30:
            $text = date('m月d日 H:i', $time); //一个月内
            break;
        case $t < 60 * 60 * 24 * 365 && $y == 0:
            $text = date('m月d日', $time); //一年内
            break;
        default:
            $text = date('Y年m月d日', $time); //一年以前
            break;
    }
    return $text;
}

/**
 *
 * @param int $time 时间戳
 * 2020/7/29
 * 19:51
 */
function GetWeek($timestamp)
{
    $number = date('w', $timestamp);
    $arr = array('周一', '周二', '周三', '周四', '周五', '周六', '周日');
    $week = $arr[$number - 1];
    return $week;
}

/**
 * @param $birthday
 * @return bool|false|int|mixed
 * 2020/9/3
 * 21:02
 * 生日计算年龄
 */
function birthday($birthday)
{
    $age = strtotime($birthday);
    if ($age === false) {
        return false;
    }
    list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
    $now = strtotime("now");
    list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));
    $age = $y2 - $y1;
    if ((int)($m2 . $d2) < (int)($m1 . $d1))
        $age -= 1;
    return $age;
}

/**
 * 求各位数之和
 * @param $nums
 * @return int
 */
function sum_of_digits($nums)
{

    $digits_sum = 0;

    for ($i = 0; $i < strlen($nums); $i++) {

        $digits_sum += $nums[$i];

    }

    return $digits_sum;

}

/**
 * @param $posttime
 * @return mixed|string
 * 查询过去多长时间
 */
function time_ago($posttime)
{
    //当前时间的时间戳
    $nowtimes = time();
    //相差时间戳
    $counttime = $nowtimes - $posttime;
    //进行时间转换
    if ($counttime <= 10) {
        return '刚刚';
    } else if ($counttime > 10 && $counttime <= 30) {
        return '刚才';
    } else if ($counttime > 30 && $counttime <= 60) {
        return '刚一会';
    } else if ($counttime > 60 && $counttime <= 120) {
        return '1分钟前';
    } else if ($counttime > 120 && $counttime <= 180) {
        return '2分钟前';
    } else if ($counttime > 180 && $counttime < 3600) {
        return intval(($counttime / 60)) . '分钟前';
    } else if ($counttime >= 3600 && $counttime < 3600 * 24) {
        return intval(($counttime / 3600)) . '小时前';
    } else if ($counttime >= 3600 * 24 && $counttime < 3600 * 24 * 2) {
        return '昨天 ' . date('H:i', $posttime);
    } else if ($counttime >= 3600 * 24 * 2 && $counttime < 3600 * 24 * 3) {
        return '前天 ' . date('H:i', $posttime);
    } else if ($counttime >= 3600 * 24 * 3 && $counttime <= 3600 * 24 * 20) {
        return intval(($counttime / (3600 * 24))) . '天前';
    } else {
        return date('Y-m-d H:i', $posttime);
    }
}

/**
 * 文件大小单位格式化
 * @param $bytes 文件实际大小，单位byte
 * @param $prec 转换后精确度，默认精确到小数点后两位
 * @return 转换后的大小+单位的字符串
 */
function fsizeformat($bytes, $prec = 2)
{
    $rank = 0;
    $size = $bytes;
    $unit = "B";
    while ($size > 1024) {
        $size = $size / 1024;
        $rank++;
    }
    $size = round($size, $prec);
    switch ($rank) {
        case "1":
            $unit = "KB";
            break;
        case "2":
            $unit = "MB";
            break;
        case "3":
            $unit = "GB";
            break;
        case "4":
            $unit = "TB";
            break;
        default :

    }
    return $size . " " . $unit;
}




/**
 * # 字符串转 ascii 码的方法
 * @param $c 输入要处理的字符串
 * @param $prefix 对转换后的 ascii 进行加工的字符
 * feiniaomy.com 飞鸟慕鱼博客
 *
 * @return string
 */
// function ascii_encode($c, $prefix="&#") {
//     $len = strlen($c);
//     $scill = null;
//     $a = 0;
//     while ($a < $len) {
//         $ud = 0;
//         if (ord($c{$a}) >= 0 && ord($c{$a}) <= 127) {
//             $ud = ord($c{$a});
//             $a += 1;
//         } else if (ord($c{$a}) >= 192 && ord($c{$a}) <= 223) {
//             $ud = (ord($c{$a}) - 192) * 64 + (ord($c{$a + 1}) - 128);
//             $a += 2;
//         } else if (ord($c{$a}) >= 224 && ord($c{$a}) <= 239) {
//             $ud = (ord($c{$a}) - 224) * 4096 + (ord($c{$a + 1}) - 128) * 64 + (ord($c{$a + 2}) - 128);
//             $a += 3;
//         } else if (ord($c{$a}) >= 240 && ord($c{$a}) <= 247) {
//             $ud = (ord($c{$a}) - 240) * 262144 + (ord($c{$a + 1}) - 128) * 4096 + (ord($c{$a + 2}) - 128) * 64 + (ord($c{$a + 3}) - 128);
//             $a += 4;
//         } else if (ord($c{$a}) >= 248 && ord($c{$a}) <= 251) {
//             $ud = (ord($c{$a}) - 248) * 16777216 + (ord($c{$a + 1}) - 128) * 262144 + (ord($c{$a + 2}) - 128) * 4096 + (ord($c{$a + 3}) - 128) * 64 + (ord($c{$a + 4}) - 128);
//             $a += 5;
//         } else if (ord($c{$a}) >= 252 && ord($c{$a}) <= 253) {
//             $ud = (ord($c{$a}) - 252) * 1073741824 + (ord($c{$a + 1}) - 128) * 16777216 + (ord($c{$a + 2}) - 128) * 262144 + (ord($c{$a + 3}) - 128) * 4096 + (ord($c{$a + 4}) - 128) * 64 + (ord($c{$a + 5}) - 128);
//             $a += 6;
//         } else if (ord($c{$a}) >= 254 && ord($c{$a}) <= 255) { //error
//             $ud = false;
//         }
//         $scill .= $prefix.$ud.";";
//     }
//     return $scill;
// }

/**
 * 获取全球唯一标识
 * @return string
 */
function token_uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

/**
 *
 * @param $m
 * Date: 2022/8/12
 * Time: 9:47
 * USER:GCQ
 */
function is_am_pm($m){
    $d =['am'=>'上午','pm'=>'下午'];
    if(array_key_exists($m,$d)){
        return $d[$m];
    }
    return '';
}
/**
 * 把数字1-1亿换成汉字表述，如：123->一百二十三
 * @param [num] $num [数字]
 * @return [string] [string]
 */
function numToWord($num)
{
    $chiNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
    $chiUni = array('','十', '百', '千', '万', '亿', '十', '百', '千');

    $chiStr = '';

    $num_str = (string)$num;

    $count = strlen($num_str);
    $last_flag = true; //上一个 是否为0
    $zero_flag = true; //是否第一个
    $temp_num = null; //临时数字

    $chiStr = '';//拼接结果
    if ($count == 2) {//两位数
        $temp_num = $num_str[0];
        $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num].$chiUni[1];
        $temp_num = $num_str[1];
        $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
    }else if($count > 2){
        $index = 0;
        for ($i=$count-1; $i >= 0 ; $i--) {
            $temp_num = $num_str[$i];
            if ($temp_num == 0) {
                if (!$zero_flag && !$last_flag ) {
                    $chiStr = $chiNum[$temp_num]. $chiStr;
                    $last_flag = true;
                }
            }else{
                $chiStr = $chiNum[$temp_num].$chiUni[$index%9] .$chiStr;
                $zero_flag = false;
                $last_flag = false;
            }
            $index ++;
        }
    }else{
        $chiStr = $chiNum[$num_str[0]];
    }
    return $chiStr;
}
