<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/26
     * Time: 8:35
     */
namespace app\common\services\evaluationclass;

use app\common\model\EvaluationClass;
use app\common\services\BaseServices;
use think\Request;
use think\Db;
class EvaluationClassServices extends BaseServices
{
    protected $status = [
        1=>'开启',
        2=>'关闭',
    ];
    public function setModel()
    {
        $this->model = new EvaluationClass();
    }

    /**
     * 列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 9:19
     * USER:GCQ
     */
    public function get_list($where=[])
    {
        [$page,$limit] = $this->getPageValue();
        $list = $this->model
            ->where('is_del',1)
            ->order('sort','desc')
            ->order('create_time','desc')
            ->field('id,name,content,sort,status,create_time')
            ->page($page,$limit)
            ->select();
        if($list){
            foreach ($list as &$item){
                $item['create_time'] = date('Y-m-d H:i');
                $item['status_name'] = $this->status[$item['status']];

            }
        }
        return res_data(1,'请求成功',$list);
    }

    /**
     * 添加/编辑
     * @param int $evaluationclassId
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 9:11
     * USER:GCQ
     */
    public function save($evaluationclassId=0,$data)
    {

        if($evaluationclassId){
            //编辑
            $evaluationClassInfo =  $this->model->where('id',$evaluationclassId)->find();
            if(!$evaluationClassInfo)return res_data(0,'未找到该id的测评分类');
            $res = $this->model->where('id',$evaluationclassId)->update($data);
            if ($res === false)return res_data(0,'修改失败');
            return res_data(1,'修改成功');
        }else{
            //添加
            $data['create_time'] = time();
            $id = $this->model->insertGetId($data);
            if($id){
                return res_data(1,'添加成功',['id'=>$id]);
            }else{
                return res_data(0,'添加失败');
            }

        }
    }

    /**
     * 详情
     * @param int $evaluationclassId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 9:41
     * USER:GCQ
     */
    public function show($evaluationclassId=0)
    {
        $evaluationclassInfo =  $this->model
            ->where('id',$evaluationclassId)
            ->where('is_del',1)
            ->field('id,name,content,create_time,status,sort')
            ->find();
        if(!$evaluationclassInfo)return res_data(0,'未找到该id的测评分类');
        $evaluationclassInfo['status_name'] = $this->status[$evaluationclassInfo['status']];
        $evaluationclassInfo['create_time'] = date('Y-m-d H:i',$evaluationclassInfo['create_time']);
        return res_data(1,'请求成功',$evaluationclassInfo);
    }
    /**
     * 删除
     * @param int $evaluationclassId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 9:33
     * USER:GCQ
     */
    public function evaluationclass_del($evaluationclassId=0)
    {
        $evaluationclassInfo =  $this->model->where('id',$evaluationclassId)->field('id,is_del')->find();
        if(!$evaluationclassInfo)return res_data(0,'未找到该id的测评分类');
        if($evaluationclassInfo['is_del']==2)return res_data(1,'已删除');
        $res = $this->model->where('id',$evaluationclassId)->update(['is_del'=>2]);
        if($res){
            return res_data(1,'删除成功');
        }else{
            return res_data(0,'删除失败');
        }
    }

    /**
     * 公共调用测评分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 9:44
     * USER:GCQ
     */
    public function evaluationclass_list()
    {
        $list = $this->model
            ->where('status',1)
            ->where('is_del',1)
            ->order('sort','desc')
            ->order('create_time','desc')
            ->field('id,name,content,sort,status,create_time')
            ->select();
        if($list){
            foreach ($list as &$item){
                $item['create_time'] = date('Y-m-d H:i');
                $item['status_name'] = $this->status[$item['status']];
            }
        }
        return res_data(1,'请求成功',$list);
    }
}