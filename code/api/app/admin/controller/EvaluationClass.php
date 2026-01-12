<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/26
     * Time: 8:31
     */

namespace app\admin\controller;
use app\common\services\evaluationclass\EvaluationClassServices;
use think\Request;
class EvaluationClass extends Basic
{
    /**
     * 测评分类
     * EvaluationClass constructor.
     * @param Request|null $request
     * @param HelpServices $helpServices
     */
    protected $services;

    public function __construct(Request $request = null, EvaluationClassServices $evaluationClassServices)
    {
        parent::__construct($request);
        $this->services = $evaluationClassServices;
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
        json_success($this->services->get_list());
    }

    /**
     * 添加/编辑
     * Date: 2022/7/26
     * Time: 9:10
     * USER:GCQ
     */
    public function save()
    {

        $evaluationclass_id = $this->request->post('classify_id/d',0);
        $name = $this->request->post('name');
        if(!$name)json_success(res_data(0,'测评分类名称不能为空'));
        $content = $this->request->post('content');
        $sort = $this->request->post('sort/d',0);
        $status = $this->request->post('status/d',1);
        $data=[
            'name'=>$name,
            'content'=>$content,
            'sort'=>$sort,
            'status'=>$status,

        ];
        json_success($this->services->save($evaluationclass_id,$data));


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
        $evaluationclass_id = $this->request->post('classify_id/d',0);
        json_success($this->services->show($evaluationclass_id));
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
        $evaluationclass_id = $this->request->post('evaluationclass_id/d',0);
        if(!$evaluationclass_id)json_success(res_data(0,'资讯id不能为空'));
        json_success($this->services->evaluationclass_del($evaluationclass_id));
    }

    public function getList(){
        json_success($this->services->evaluationclass_list());
    }
}