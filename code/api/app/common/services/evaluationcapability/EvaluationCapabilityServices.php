<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/26
     * Time: 8:35
     */
namespace app\common\services\evaluationcapability;
use app\common\model\EvaluationCapabilityOptions;
use app\common\model\EvaluationCapability;
use app\common\services\evaluationclass\EvaluationClassServices;
use app\common\services\BaseServices;
use think\Request;
use think\Db;
class EvaluationCapabilityServices extends BaseServices
{
    //问题
    protected $questionType = [
        1=>'单选',
        2=>'多选'
    ];
    protected $questionStatus = [
        1=>'正常',
        2=>'关闭'
    ];
    //选项
    protected $optionType = [
        1=>'是',
        2=>'否'
        ];
    protected $optionStatus = [
        1=>'正常',
        2=>'关闭'
    ];
    public function setModel()
    {
        $this->model = new EvaluationCapability();
    }

    /**
     * 问题列表
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
        $list = $this
            ->evaluationSearch($where)
            ->order('e.sort','desc')
            ->order('e.create_time','desc')
            ->field('e.id,e.classify_id,e.sn,e.type,e.name,e.picture,e.sort,e.status,e.create_time,c.name as classname')
            ->page($page,$limit)
            ->select();
        $count = 0;
        if($list){
            $count = $this->evaluationSearch($where)->count('e.id');
            $evaluationClassServices  = new EvaluationClassServices();
            foreach ($list as &$item){
                $evaluationClassServices =
                $item['type_name'] = $this->questionType[$item['type']];
                $item['status_name'] = $this->questionStatus[$item['status']];
                $item['create_time'] = date('Y-m-d H:i');
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
    public function evaluationSearch($where) {

        $model = $this->model->alias('e')->join('classify c', 'e.classify_id = c.id');
        $model =$model->where('e.is_del',1);
        //按测评分类搜索
        if (isset($where['classify_id']) && !empty($where['classify_id'])){
            $model = $model->where('e.classify_id',$where['classify_id']);
        }
        //按关键字搜索
        if (isset($where['key']) && !empty($where['key'])){
            $model = $model->where('e.name','like','%'.$where['key'].'%');
        }

        return $model;


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
    public function save($evaluationcapabilityId=0,$data)
    {
        if($evaluationcapabilityId){
            //编辑
            unset($data['option']);
            $evaluationCapabilityInfo =  $this->model->where('id',$evaluationcapabilityId)->where('is_del',1)->find();
            if(!$evaluationCapabilityInfo)return res_data(0,'未找到该id的测评分类');
            $res = $this->model->where('id',$evaluationcapabilityId)->update($data);
            if ($res === false)return res_data(0,'修改失败');
            return res_data(1,'修改成功');
        }else{
            //添加
            $optionData = $data['option'];
            unset($data['option']);
            $evaluationCapabilityOptions = new EvaluationCapabilityOptions();
            Db::startTrans();
            try {
                $data['create_time'] = time();
                //添加问题

                $id = $this->model->insertGetId($data);

                if($id){
                    //问题添加成功，添加选项
                    foreach ($optionData as &$item){
                        $item['capability_id'] = $id;
                        $item['create_time'] = time();
                    }
                    $res=$evaluationCapabilityOptions->insertAll($optionData);
                }

                Db::commit();
                return res_data(1,'添加成功');
            }catch (\Throwable $e){
                Db::rollback();
                return  res_data(0,'添加失败'.$e->getMessage());
            }







            // $id = $this->model->insertGetId($data);
            // if($id){
            //     return res_data(1,'添加成功',['id'=>$id]);
            // }else{
            //     return res_data(0,'添加失败');
            // }

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
    public function show($evaluationCapabilityId=0)
    {
        $evaluationCapabilityInfo =  $this->model
            ->where('id',$evaluationCapabilityId)
            ->where('is_del',1)
            ->field('id,classify_id,sn,type,name,picture,sort,status,create_time')
            ->find();
        if(!$evaluationCapabilityInfo)return res_data(0,'未找到该id的测评问题');
        $evaluationCapabilityInfo['type_name'] = $this->questionType[$evaluationCapabilityInfo['type']];
        $evaluationCapabilityInfo['status_name'] = $this->questionStatus[$evaluationCapabilityInfo['status']];
        $evaluationCapabilityInfo['create_time'] = date('Y-m-d H:i',$evaluationCapabilityInfo['create_time']);
        $evaluationCapabilityInfo['option'] =[];
        //选项获取
        $evaluationCapabilityOptions = new EvaluationCapabilityOptions();
        $evaluationCapabilityOptionsList = $evaluationCapabilityOptions
            ->where('capability_id',$evaluationCapabilityId)
            ->where('is_del',1)
            ->field('is_del',true)
            ->select();

        if($evaluationCapabilityOptionsList){
            foreach ($evaluationCapabilityOptionsList as &$item){
                $item['type_name'] = $this->optionType[$item['type']];
                $item['status_name'] = $this->optionType[$item['status']];
                $item['create_time'] = data('Y-m-d H:i',$item['create_time']);
            }
            $evaluationCapabilityInfo['option']=$evaluationCapabilityOptionsList;
        }
        return res_data(1,'请求成功',$evaluationCapabilityInfo);
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
    public function evaluation_capability_del($evaluationCapabilityId=0)
    {
        $evaluation_capabilityInfo =  $this->model->where('id',$evaluationCapabilityId)->field('id,is_del')->find();
        if(!$evaluation_capabilityInfo)return res_data(0,'未找到该id的测评问题');
        if($evaluation_capabilityInfo['is_del']==2)return res_data(1,'已删除');
        $res = $this->model->where('id',$evaluationCapabilityId)->update(['is_del'=>2]);
        if($res){
            return res_data(1,'删除成功');
        }else{
            return res_data(0,'删除失败');
        }
    }
    public function get_option_list($evaluation_capability_id=0)
    {
        $evaluationCapabilityOptions = new EvaluationCapabilityOptions();
        $list =$evaluationCapabilityOptions
            ->where('capability_id',$evaluation_capability_id)
            ->where('is_del',1)
            ->field('id,capability_id,sn,name,picture,type,sort,status,create_time')->select();
        if($list){
            foreach ($list as &$item){
                $item['type_name'] = $this->optionType[$item['type']];
                $item['status_name'] = $this->optionType[$item['status']];
                $item['create_time'] = data('Y-m-d H:i',$item['create_time']);
            }
        }
        return res_data(1,'请求成功',$list);

    }
    /**
     * 测评问题选项添加、编辑
     * @param int $evaluationCapabilityOptionId
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 14:22
     * USER:GCQ
     */
    public function option_save($evaluationCapabilityOptionId=0,$data)
    {
        $evaluationCapabilityOptions = new EvaluationCapabilityOptions();
        if($evaluationCapabilityOptionId){
            //编辑
            $evaluationCapabilityOptionsInfo = $evaluationCapabilityOptions
                ->where('id',$evaluationCapabilityOptionId)
                ->where('is_del',1)
                ->find();
            if(!$evaluationCapabilityOptionsInfo)return res_data(0,'未找到该id的测评选项');
            $res = $evaluationCapabilityOptions->where('id',$evaluationCapabilityOptionId)->update($data);
            if ($res === false)return res_data(0,'修改失败');
            return res_data(1,'修改成功');
        }else{
            //添加
            $data['create_time'] = time();
            $id = $evaluationCapabilityOptions->insertGetId($data);
            if($id){
                return  res_data(1,'添加成功');
            }else{
                return  res_data(0,'添加失败');
            }
        }
    }

    public function option_del($evaluationCapabilityId=0,$optionId=0)
    {
        $evaluationCapabilityOptions =new EvaluationCapabilityOptions();
        $evaluationCapabilityOptionsInfo  =  $evaluationCapabilityOptions
            ->where('id',$optionId)
            ->where('capability_id',$evaluationCapabilityId)
            ->field('id,is_del')->find();
        if(!$evaluationCapabilityOptionsInfo)return res_data(0,'未找到该id的测评分类问题选项');
        if($evaluationCapabilityOptionsInfo['is_del']==2)return res_data(1,'已删除');
        $res = $evaluationCapabilityOptions->where('id',$optionId)->update(['is_del'=>2]);
        if($res){
            return res_data(1,'删除成功');
        }else{
            return res_data(0,'删除失败');
        }
    }
    public function get_question_option()
    {
        $capabilility = $this->model
            ->where('classify_id', 'in', [1,2,3])
            ->where('status', 1)
            ->where('is_del', 1)
            ->field('id,classify_id,sn,name')
            ->order('sort', 'desc')
            ->select();
        $evaluationCapabilityOptions = new EvaluationCapabilityOptions();
        $capabilityOptions = $evaluationCapabilityOptions
            ->where('status', 1)
            ->where('is_del', 1)
            ->field('id,capability_id,sn,name')
            ->select();
        $question = [];
        if($capabilility){

            foreach ($capabilility as $key=>$value){

                if($capabilityOptions){
                    foreach ($capabilityOptions as $item){
                        if($value['id'] == $item['capability_id']){
                            if($item['name'] == '无' || $item['name'] == '无（能正常行走或借助辅助工具行走）'){

                            }else{
                                $question[] = $item;
                            }

                        }
                    }
                }
            }
        }
        return res_data(1,'请求成功', $question);
    }


    /**
     * 前端
     */

    /**
     * 问题选项
     * @param $evaluation_class_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/28
     * Time: 10:44
     * USER:GCQ
     */
    public function get_question($evaluation_class_id)
    {
        $evaluationCapabilityINfo = $this->model
            ->where('classify_id',$evaluation_class_id)
            ->where('status',1)
            ->where('is_del',1)
            ->field('id,classify_id,sn,name,picture,type')
            ->order('sort','desc')
            ->order('create_time','desc')
            ->select();
        if($evaluationCapabilityINfo){

            $evaluationCapabilityOptions = new EvaluationCapabilityOptions();
            foreach ($evaluationCapabilityINfo as &$item){
                $options = [];
                $options = $evaluationCapabilityOptions
                    ->where('capability_id',$item['id'])
                    ->where('status',1)
                    ->where('is_del',1)
                    ->field('id,capability_id,sn,name,picture,type')
                    ->order('sort','desc')
                    ->order('create_time','desc')
                    ->select();
                $item['options'] = [];
                if($options) {
                    $item['options'] = $options;
                }
            }

            return res_data(1,'请求成功',$evaluationCapabilityINfo);
        }else{
            return res_data(1,'请求成功');
        }

    }




}