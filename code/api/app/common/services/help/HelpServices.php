<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/7/25
 * Time: 14:09
 */
namespace app\common\services\help;
use app\common\model\Config;
use app\common\model\Help;
use app\common\services\BaseServices;
use app\common\services\config\ConfigServices;
use think\Db;
use think\Request;
class HelpServices extends BaseServices
{
    protected $sendStatus=[
        1=>'立即发布',
        2=>'定时发布',
        3=>'暂不发布',
        ];
    public function setModel()
    {
        $this->model = new Help();
    }

    /**
     * 获取资讯列表
     * Date: 2022/7/25
     * Time: 18:20
     * USER:GCQ
     */
    public function get_list($where)
    {
        [$page,$limit] = $this->getPageValue();
        $list = $this
            ->helpSearch($where)
            ->order('sort','desc')
            ->order('create_time','desc')
            ->field('id,help_class_id,title,image,bigimage,create_time,send_status,sort,update_time,video')
            ->page($page,$limit)
            ->select();

        $count = 0;
        if($list){
            $count = $this->helpSearch($where)->count('id');
            foreach ($list as &$item){
                $item['send_status_name'] = '';
                if (array_key_exists($item['send_status'], $this->sendStatus)) {
                    $item['send_status_name'] = $this->sendStatus[$item['send_status']];
                }

                $item['create_time'] = date('Y-m-d H:i',$item['create_time']);
                $item['update_time'] = date('Y-m-d H:i',$item['update_time']);
            }
        }
        return res_data(1,'请求成功',['list'=>$list,'count'=>$count]);
    }

    /**
     * 搜索
     * @param $where
     * @return \think\Model
     * Date: 2022/7/26
     * Time: 8:24
     * USER:GCQ
     */
    public function helpSearch($where) {
        $model = $this->model->where('is_del',1);
        //按资讯分类搜索
        if (isset($where['help_class_id']) && !empty($where['help_class_id'])){
            $model = $model->where('help_class_id',$where['help_class_id']);
        }
        //按关键字搜索
        if (isset($where['key']) && !empty($where['key'])){
            $model = $model->where('title','like','%'.$where['key'].'%');
        }
        if (isset($where['send_status']) && !empty($where['send_status'])){
            $model = $model->where('send_status',$where['send_status']);
        }
        //tab
        return $model;
    }

    /**
     * 新增/编辑
     * @param int $helpId
     * @param $data
     * @return array
     * Date: 2022/7/25
     * Time: 18:58
     * USER:GCQ
     */
    public function save($helpId=0,$data)
    {
        if($helpId){
            //编辑
            $helpInfo =  $this->model->where('id',$helpId)->find();
            if(!$helpInfo)return res_data(0,'未找到该id的资讯');

            if($data['send_status'] == 2){
                if($data['create_time'] >= time()){
                    $data['send_status'] = 2;
                }else{
                    $data['send_status'] = 1;
                }

            }
            $data['update_time'] = time();

            $res = $this->model->where('id',$helpId)->update($data);
            if ($res === false)return res_data(0,'修改失败');
            return res_data(1,'修改成功');

        }else{
            //新增
            if($data['send_status']==1 || $data['send_status'] == 3){
                //立即发布
                $data['create_time'] = time();
                $data['update_time'] = $data['create_time'];
            }
            if($data['send_status'] == 2){
                if($data['create_time'] >= time()){
                    $data['send_status'] = 2;
                }else{
                    $data['send_status'] = 1;
                }
                $data['update_time'] = $data['create_time'];
            }

            $help_id = $this->model->insertGetId($data);
            if($help_id){
                return res_data(1,'添加成功');
            }else{
                return res_data(0,'添加失败');
            }

        }
    }

    /**
     * 查询单个资讯
     * @param $helpId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/25
     * Time: 19:42
     * USER:GCQ
     */
    public function help_show($helpId)
    {

        $helpInfo =  $this->model->where('id',$helpId)->where('is_del',1) ->field('id,help_class_id,title,content,image,bigimage,create_time,send_status,sort,video')->find();
        if(!$helpInfo)return res_data(0,'未找到该id的资讯');
        $helpInfo['send_status_name'] = $this->sendStatus[$helpInfo['send_status']];
        $helpInfo['create_time'] = date('Y-m-d H:i',$helpInfo['create_time']);
        return res_data(1,'请求成功',$helpInfo);
    }

    /**
     * 资讯删除
     * @param $helpId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/25
     * Time: 19:57
     * USER:GCQ
     */
    public function help_del($helpId)
    {
        $helpInfo =  $this->model->where('id',$helpId)->field('id,is_del')->find();
        if(!$helpInfo)return res_data(0,'未找到该id的资讯');
        if($helpInfo['is_del']==0)return res_data(1,'已删除');
        $res = $this->model->where('id',$helpId)->update(['is_del'=>0]);
        if($res){
            return res_data(1,'删除成功');
        }else{
           return res_data(0,'删除失败');
        }

    }

    //前端

    /**
     * 资讯列表
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 16:51
     * USER:GCQ
     */
    public function api_help_list($where)
    {

        [$page,$limit] = $this->getPageValue();
        $list = $this->model
            ->where('help_class_id',$where['help_class_id'])
            ->where('send_status',1)
            ->where('is_del',1)
            ->order('sort','desc')
            ->order('create_time','desc')
            ->field('id,help_class_id,title,image,bigimage,create_time,sort,video')
            ->page($page,$limit)
            ->select();

        $count = 0;
        $show_type=1;
        if($list){
            $count = $this->model
                ->where('help_class_id',$where['help_class_id'])
                ->where('send_status',1)
                ->where('is_del',1)
                ->count('id');
            $config = new ConfigServices();
            $key ='helpType';
            if (!in_array($key,$config->model->keys)){
                $show_type = 1;
            }else{
                $result = $config->where('key',$key)->value('value');
                $show_type = $result;
            }
            foreach ($list as &$item){
                // $item['show_type'] = $show_type;
                $item['create_time'] = date('Y-m-d H:i',$item['create_time']);
            }
        }
        return res_data(1,'请求成功',['list'=>$list,'show_type'=>$show_type,'count'=>$count]);
    }

    /**
     * 资讯详情
     * @param $help_id
     * Date: 2022/7/27
     * Time: 15:45
     * USER:GCQ
     */
    public function get_details($helpId)
    {
        $helpInfo = $this->model
            ->where('id',$helpId)
            ->where('send_status',1)
            ->where('is_del',1)
            ->field('id,title,content,create_time,video')
            ->find();

        if($helpInfo){
            $helpInfo['create_time'] = date('Y-m-d H:i',$helpInfo['create_time']);
        }else{
            $helpInfo = [];
        }
        return res_data(1,'请求成功',$helpInfo);
    }

}