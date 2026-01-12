<?php

namespace app\admin\controller;

use \app\common\model\Config as ConfigModel;

/**
 * 配置管理
 * Class app\admin\controller\Config
 */
class Config extends Basic
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 配置查看
     * @author yangxiuchuan
     * @date 2022-04-18
     * @return void
     */
    public function index() {
        $key = input('key');
        $config = new ConfigModel();
        //所传ID是否在已有数组中
        if (!in_array($key,$config->keys)){
            json_fail('key无效');
        }

        $info = $config->where('key',$key)->find();
        if (!$info) json_fail('暂无数据');
        json_success($info);
    }

    /**
     * 配置修改
     * @author yangxiuchuan
     * @date 2022-04-18
     * @return void
     */
    public function save() {
        $key = input('key');//协议ID
        $content = input('value','');//内容
        if (!$key){
            json_fail('缺少key参数');
        }

        $config = new ConfigModel();
        //所传ID是否在已有数组中
        if (!in_array($key,$config->keys)){
            json_fail('key无效');
        }

        $saveData = [
            'value' => $content,
        ];
        $res = $config->where('key',$key)->update($saveData);
        if (!$res){
            json_fail('保存'.$config->values[$key].'失败');
        }
        json_success('保存成功');
    }

    /*
     *  返回资讯返回大图小图方式设置
     */
    public function getHelpWay()
    {
        $Config = Db('config');
        //返回图片方式
        $info['helpType'] = $Config->getFieldBykey('helpType', 'value');
        if ($info) {

        } else {
            $info=[];
        }
        json_success(['status'=>1,'msg'=>'请求成功','data'=>$info]);
    }
    //设置资讯返回大图小图
    public function setHelpWay()
    {
        $Config = Db('config');
        //返回图片方式
        $data['helpType'] = input('post.help_type/d');
        if(!$data['helpType'])json_success(['status'=>'0','msg'=>'参数help_type不能为空']);

        $result = $this->modConfig($data);
        if ($result !== false) {
            json_success(['status'=>'1','msg'=>'设置成功']);
        } else {
            json_success(['status'=>'0','msg'=>'设置失败']);

        }
    }
    /**
     * 公共方法: 修改config数据
     */
    private function modConfig($data)
    {
        $Config = Db('Config');
        if ($data) {
            foreach ($data as $k => $v) {
                $result = $Config->where(array('key' => $k))->update(array('value' => $v));
            }
        }
        return $result;
    }
}