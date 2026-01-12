<?php

namespace app\admin\controller;

use app\admin\model\SystemManager;
use think\Controller;
use think\Request;


class Basic extends Controller
{
    protected $user_id;
    public $role_id;
    // protected $project_ids;

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *  初始化方法，所有子类继承
     */
    protected function _initialize()
    {

        $module = strtolower($this->request->module());
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());
        $array_action = array(
            'admin/login/login',
            'admin/index/upload_img',
            'admin/export/export_excel',
            'admin/export/export_scenes',
            'admin/export/export_analyze',
            'admin/upload/upload_img',
            'admin/upload/upload_video',
            'admin/script/refresh_greement_tatus',
            'admin/beadhouse/download_code',
        );

        if (!in_array($module . '/' . $controller . '/' . $action, $array_action)) {
            
            $token = $this->request->header('token');
            if(!$token)json_success(['status'=>0,'msg'=>'token不能为空！']);

            // $user_id = $this->request->header('usertoken');
            // if(!$user_id)json_success(['status'=>0,'msg'=>'usertoken不能为空！']);
            // $user_id = decode($user_id);
            $system_manager = new SystemManager();

            // $system_manager_info = $system_manager->where('token',$token)->where('id',$user_id)->find();
            $system_manager_info = $system_manager->where('token',$token)->find();
            if($system_manager_info){
                $this->user_id=$system_manager_info['id'];
                $this->role_id = $system_manager_info['role_id'];
                // if($system_manager_info['role_id'] == 1){
                //     $this->project_ids = '';
                //
                // }else{
                //     $this->project_ids = $system_manager_info['project_ids'];
                // }
            }else{
                json_fail([
                    'status' =>403,
                    'msg' => '该账号已在其它电脑登录'
                ],403);
            }
        }
    }
    /**
     * 默认请求
     */
    public function index()
    {

    }


    /**
     * @param $url
     * @param $data
     * @return mixed
     * 请求外链
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
     * 获取Excel的内容
     * @param     $file
     * @param int $sheet
     * @return array|string
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @author jihaichuan
     */
    protected function get_excel($file, $sheet = 0, $col = null, $row = 3)
    {

        // $file = mb_substr(dirname(__FILE__), 0, -18).'/public/'.$file;
        // 判断文件是否存在
        if (empty($file) or !file_exists($file)) {
            return '文件不存在！';
        }

        vendor("phpexcel.PHPExcel"); //方法一
        import("phpexcel.PHPExcel.Reader.Excel5");
        import("phpexcel.PHPExcel.Reader.Excel2007");
        // 示例话
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($file)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($file)) {
                return '读取文档失败';
            }
        }

        $PHPExcel = $PHPReader->load($file);                      //建立excel对象
        $currentSheet = $PHPExcel->getSheet($sheet);              //**读取excel文件中的指定工作表*/
        $allColumn = $currentSheet->getHighestColumn();       //**取得最大的列号*/
        $allRow = $currentSheet->getHighestRow();                 //**取得一共有多少行*/

        $data = array();
        $addr = '';

        for ($rowIndex = $row; $rowIndex <= $allRow; $rowIndex++) {
            for ($colIndex = 'A'; $colIndex <= $allColumn; $colIndex++) {
                $addr = $colIndex . $rowIndex;
                $cell = $currentSheet->getCell($addr)->getValue();
                // if ($colIndex == 'I') {
                //     $b = $colIndex . $rowIndex;
                //     $cell = gmdate('Y-m-d H:i:s', \PHPExcel_Shared_Date::ExcelToPHP($currentSheet->getCell($b)->getValue()));
                // }
                $data[$rowIndex][$colIndex] = $cell;
            }
        }
        return $data;
    }



    protected function get_excel_two($file, $sheet = 0, $col = null, $row = 3)
    {

        // $file = mb_substr(dirname(__FILE__), 0, -18).'/public/'.$file;
        // 判断文件是否存在
        if (empty($file) or !file_exists($file)) {
            return '文件不存在！';
        }

        vendor("phpexcel.PHPExcel"); //方法一
        import("phpexcel.PHPExcel.Reader.Excel5");
        import("phpexcel.PHPExcel.Reader.Excel2007");
        // 示例话
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($file)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($file)) {
                return '读取文档失败';
            }
        }

        $PHPExcel = $PHPReader->load($file);                      //建立excel对象
        $currentSheet = $PHPExcel->getSheet($sheet);              //**读取excel文件中的指定工作表*/
        $allColumn = $currentSheet->getHighestColumn();       //**取得最大的列号*/
        $allRow = $currentSheet->getHighestRow();                 //**取得一共有多少行*/

        $data = array();
        $addr = '';
        ++$allColumn;
        for ($rowIndex = $row; $rowIndex <= $allRow; $rowIndex++) {
            for ($colIndex = 'A'; $colIndex <= $allColumn; $colIndex++) {
                $addr = $colIndex . $rowIndex;
                $cell = $currentSheet->getCell($addr)->getValue();
                 if ($colIndex == 'E' || $colIndex == 'F') {
                     $b = $colIndex . $rowIndex;
                     $cell = gmdate('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($currentSheet->getCell($b)->getValue()));
                 }
                $data[$rowIndex][$colIndex] = $cell;
            }
        }
        return $data;
    }

    /**
     * 获取Excel的内容
     * @param     $file
     * @param int $sheet
     * @return array|string
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @author jihaichuan
     */
    protected function get_excel_1($file, $sheet = 0, $col = null, $row = 3)
    {

       // $file = mb_substr(dirname(__FILE__), 0, -18).'/public/'.$file;
        // 判断文件是否存在
        if (empty($file) or !file_exists($file)) {
            return '文件不存在！';
        }


        vendor("phpexcel.PHPExcel"); //方法一
        import("phpexcel.PHPExcel.Reader.Excel5");
        import("phpexcel.PHPExcel.Reader.Excel2007");

        // 示例话
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($file)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($file)) {
                return '读取文档失败';
            }
        }
        $PHPExcel = $PHPReader->load($file);                      //建立excel对象
        $currentSheet = $PHPExcel->getSheet($sheet);              //**读取excel文件中的指定工作表*/
        $allColumn = $currentSheet->getHighestColumn($col);       //**取得最大的列号*/
        $allRow = $currentSheet->getHighestRow();                 //**取得一共有多少行*/

        $data = array();
        $addr = '';
        ++$allColumn;
        for ($rowIndex = $row; $rowIndex <= $allRow; $rowIndex++) {
            for ($colIndex = 'A'; $colIndex != $allColumn; $colIndex++) {
                $addr = $colIndex . $rowIndex;
                if ($colIndex == 'AJ') {
                    \PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME5;
                    $cell = $currentSheet->getCell($addr)->getFormattedValue();
//                    $cell = $currentSheet->getCell($addr)->setFormatCode();
                } else {
                    $cell = $currentSheet->getCell($addr)->getValue();
                }

//                if ($colIndex == 'L' || $colIndex == 'U') {
//                    $a = $colIndex . $rowIndex;
//                    $cell = $currentSheet->getCell($a)->getValue();
//                    if (is_object($cell)) {
//                        $cell = $cell->__tostring();
//                    }
//                }
//                if ($colIndex == 'I'){
//                    $b = $colIndex . $rowIndex;
//                    $cell = gmdate('Y-m-d H:i:s',\PHPExcel_Shared_Date::ExcelToPHP($currentSheet->getCell($b)->getValue()));
//                }
                $data[$rowIndex][$colIndex] = $cell;
            }
        }
        return $data;
    }

    /**
     *
     * @param int $time 时间戳
     * 2020/7/29
     * 19:51
     */
    public function GetWeek($timestamp)
    {
        $number = date('w', $timestamp);
        $arr = array('周一', '周二', '周三', '周四', '周五', '周六', '周日');
        $week = $arr[$number - 1];
        return $week;
    }


    /**
     * @param $address
     * @return bool
     *获取经纬度
     */
    public function getLocation($address = '')
    {
        $key = config('location_key');
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?address=' . $address . "&key=" . $key;
        $info = http_curl($url, 'get');
        if ($info['message'] == '查询无结果') {
            json_fail('请输入正确地址');
        } else {
            if (!empty($info['result'])) {
                $arr['lng'] = $info['result']['location']['lng'];
                $arr['lat'] = $info['result']['location']['lat'];
                return $arr;
            } else {
                json_fail($info['status']);
            }
        }
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
            $num = 80;
            // 利用三元运算判断文字是否超出设置的字数进行截取
            return mb_strlen($contents,'utf-8') > $num ? mb_substr($contents, 0, $num, "utf-8").'...' : mb_substr($contents, 0, $num, "utf-8");
        }else{
            return false;
        }
    }

}
