<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/26
     * Time: 8:31
     */

namespace app\admin\controller;
use app\common\services\evaluationcapability\EvaluationCapabilityServices;
use tests\thinkphp\library\think\config\driver\jsonTest;
use think\Request;
class EvaluationCapability extends Basic
{
    /**
     * 测评分类
     * EvaluationClass constructor.
     * @param Request|null $request
     * @param HelpServices $helpServices
     */
    protected $services;

    public function __construct(Request $request = null, EvaluationCapabilityServices $evaluationCapabilityServices)
    {
        parent::__construct($request);
        $this->services = $evaluationCapabilityServices;
    }

    /**
     * 列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 9:19
     * USER:GCQ
     */
    public function index()
    {
        $where['classify_id'] = $this->request->post('evaluationclass_id/d',0);
        $where['key'] = $this->request->post('key');
        json_success($this->services->get_list($where));
    }

    /**
     * 添加/编辑
     * Date: 2022/7/26
     * Time: 9:10
     * USER:GCQ
     */
    public function save()
    {
        $evaluation_capability_id = $this->request->post('evaluation_capability_id/d',0);
        $evaluationclass_id = $this->request->post('classify_id/d',0);
        if(!$evaluationclass_id)json_success(res_data(0,'请选择测评分类id'));
        $name = $this->request->post('name');
        if(!$name)json_success(res_data(0,'测评问题名称不能为空'));
        $type = $this->request->post('type/d',1);
        $sn = $this->request->post('sn');
        if(!$sn)json_success(res_data(0,'测评问题编号不能为空'));
        $picture = $this->request->post('picture');
        $sort = $this->request->post('sort/d',0);
        $status = $this->request->post('status/d',1);
        $optionData = [];
        if(!$evaluation_capability_id){
            //选项
            $option = $this->request->post('option/a',array());


            if($option){
                foreach ($option as $k=>$item){

                    //测评选项编号

                    if(!$item['sn'])json_success(res_data(0,'测评选项编号不能为空'));
                    $optionData[$k]['sn'] = $item['sn'];
                    //测评选项名称
                    if(!$item['name'])json_success(res_data(0,'测评选项名称不能为空'));
                    $optionData[$k]['name'] = $item['name'];
                    //图片
                    $optionData[$k]['picture'] = $item['picture'];
                    //是否是自定义字段[1:是][2:否]
                    $optionData[$k]['type'] = $item['type']?$item['type']:2;
                    $optionData[$k]['sort'] = $item['sort']?$item['sort']:0;
                    $optionData[$k]['status'] = $item['status']?$item['status']:1;
                }
            }
        }

        $data=[
            'type'=>$type,
            'classify_id'=>$evaluationclass_id,
            'name'=>$name,
            'sn'=>$sn,
            'picture'=>$picture,
            'sort'=>$sort,
            'status'=>$status,
            'option'=>$optionData

        ];
        json_success($this->services->save($evaluation_capability_id,$data));


    }

    /**
     * 详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 9:41
     * USER:GCQ
     */
    public function show()
    {
        $evaluation_capability_id = $this->request->post('evaluation_capability_id/d',0);
        if(!$evaluation_capability_id)json_success(res_data(0,'测评问题id不能为空'));
        json_success($this->services->show($evaluation_capability_id));
    }
    /**
     * 删除
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/26
     * Time: 9:33
     * USER:GCQ
     */
    public function del()
    {
        $evaluation_capability_id = $this->request->post('evaluation_capability_id/d',0);
        if(!$evaluation_capability_id)json_success(res_data(0,'测评问题id不能为空'));
        json_success($this->services->evaluation_capability_del($evaluation_capability_id));
    }

    public function  optionList()
    {
        $evaluation_capability_id = $this->request->post('evaluation_capability_id/d',0);
        if(!$evaluation_capability_id)json_success(res_data(0,'测评问题id不能为空'));
        json_success($this->services->get_option_list($evaluation_capability_id));
    }
    /**
     * 测评问题选项添加、编辑
     * Date: 2022/7/26
     * Time: 14:19
     * USER:GCQ
     */
    public function optionSave()
    {
        $evaluation_capability_option_id = $this->request->post('option_id/d',0);
        $evaluation_capability_id = $this->request->post('evaluation_capability_id/d',0);
        if(!$evaluation_capability_id) json_success(res_data(0,'测评问题id不能为空'));
        $sn = $this->request->post('sn');
        if(!$sn)json_success(res_data(0,'测评选项编号不能为空'));
        //测评选项名称
        $name = $this->request->post('name');
        if(!$name)json_success(res_data(0,'测评选项名称不能为空'));
        //图片
        $picture =  $this->request->post('picture');

        //是否是自定义字段[1:是][2:否]
        $type =  $this->request->post('type/d',2);
        $sort = $this->request->post('sort/d',0);
        $status = $this->request->post('status/d',1);
        $data=[
            'capability_id'=>$evaluation_capability_id,
            'sn'=>$sn,
            'name'=>$name,
            'picture'=>$picture,
            'type'=>$type,
            'sort'=>$sort,
            'status' =>$status
            ];
        json_success($this->services->option_save($evaluation_capability_option_id,$data));

    }

    public function optionDel()
    {
        $evaluation_capability_id = $this->request->post('evaluation_capability_id/d',0);
        if(!$evaluation_capability_id)json_success(res_data(0,'测评问题id不能为空'));
        $option_id = $this->request->post('option_id/d',0);
        if(!$option_id)json_success(res_data(0,'测评问题选项id不能为空'));
        json_success($this->services->option_del($evaluation_capability_id,$option_id));
    }
    public function getList(){
        json_success($this->services->evaluationclass_list());
    }

    /**
     * 互动问题选项
     */
    public function question()
    {
        json_success($this->services->get_question_option());
    }

}