<?php

namespace app\admin\controller;

use app\common\services\system\SkinServices;
use think\Request;

class Skin extends Basic
{
    protected $services;
    public function __construct(Request $request = null,SkinServices $skinServices)
    {
        parent::__construct($request);
        $this->services = $skinServices;
    }

    /**
     * 后台皮肤列表
     * @return void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index() {
        $where['key'] = $this->request->get('key','');
        json_success($this->services->adminList($where));
    }

    public function save() {
        $data['id'] = $this->request->post('id/d',0);
        $data['name'] = $this->request->post('name','');
        $data['picture'] = $this->request->post('picture','');
        $data['sort'] = $this->request->post('sort/d',0);
        $data['status'] = $this->request->post('status/d',1);
        $res = $this->services->save($data);
        if (!$res)json_fail('存储失败');
        json_success('存储成功');
    }

    /**
     * 详情
     * @return void
     */
    public function details() {
        $id = $this->request->get('id/d',0);
        if (!$id)json_fail('缺少皮肤id参数');
        json_success($this->services->details($id));
    }

    /**
     * 皮肤删除
     * @return void
     */
    public function del() {
        $id = $this->request->post('id/d',0);
        if (!$id)json_fail('缺少皮肤id参数');
        $res = $this->services->del($id);
        if (!$res)json_fail('删除失败');
        json_success('删除成功');
    }
}