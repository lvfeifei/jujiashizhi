<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Email: 13522679763@163.com
 * Date: 2022/7/25
 * Time: 14:04
 */

namespace app\admin\controller;

use app\common\services\help\HelpServices;
use think\Request;
class Help extends Basic
{
    /**
     * 资讯
     * @var HelpServices
     */
    protected $services;
    public function __construct(Request $request = null,HelpServices $helpServices)
    {
        parent::__construct($request);
        $this->services = $helpServices;
    }

    /**
     * 资讯列表
     * Date: 2022/7/25
     * Time: 18:19
     * USER:GCQ
     */
    public function index()
    {
        $where['send_status'] = $this->request->post('send_status/d',0);  //默认0查询全部    //发布状态  1立即发布  2定时发布  3暂不发布
        //资讯分类id
        $where['help_class_id'] = $this->request->post('help_class_id/d',0);
        $where['key'] = $this->request->post('key');
        json_success($this->services->get_list($where));

    }

    /**
     * 添加/编辑资讯
     * Date: 2022/7/25
     * Time: 18:21
     * USER:GCQ
     */
    public function save()
    {
        //资讯id
        $help_id = $this->request->post('help_id/d',0);
        $help_class_id = $this->request->post('help_class_id/d',0);
        if(!$help_class_id)json_success(res_data(0,'请选择资讯分类id'));
        $title = $this->request->post('title');
        if(!$title)json_success(res_data(0,'资讯标题不能为空'));
        $small_img = $this->request->post('small_img');
        if(!$small_img)json_success(res_data(0,'小图不能为空'));
        $big_img = $this->request->post('big_img');
        if(!$big_img)json_success(res_data(0,'大图不能为空'));
        $content = $this->request->post('content');
        if(!$content)json_success(res_data(0,'资讯内容不能为空'));

        $video = $this->request->post('video');
        if(!$video){
            $video = '';
        }
        $sort = $this->request->post('sort/d',0);
        //发布状态  1立即发布  2定时发布  3暂不发布
        $send_status = $this->request->post('send_status/d',1);  //默认1立即发布


        $data=[
            'title'=>$title,
            'help_class_id'=>$help_class_id,
            'image'=>$small_img,
            'bigimage'=>$big_img,
            'content'=>$content,
            'sort'=>$sort,
            'send_status'=>$send_status,
            'video' => $video,
        ];
        if($send_status==2){
            $send_time = $this->request->post('send_time');
            if(!$send_time) json_success(res_data(0,'请指定发布时间'));
            $data['create_time'] = strtotime($send_time);
        }
        json_success($this->services->save($help_id,$data));


    }

    /**
     * 查询单个资讯
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/25
     * Time: 19:51
     * USER:GCQ
     */
    public function helpShow(){
        $help_id = $this->request->post('help_id/d',0);
        if(!$help_id)json_success(res_data(0,'资讯id不能为空'));
        json_success($this->services->help_show($help_id));
    }

    /**
     * 资讯删除
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Date: 2022/7/25
     * Time: 20:03
     * USER:GCQ
     */
    public function del()
    {
        $help_id = $this->request->post('help_id/d',0);
        if(!$help_id)json_success(res_data(0,'资讯id不能为空'));
        json_success($this->services->help_del($help_id));
    }

}