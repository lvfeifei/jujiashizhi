<?php
    /**
     * Created by PhpStorm.
     * User: Eric
     * Email: 13522679763@163.com
     * Date: 2022/7/26
     * Time: 8:35
     */
namespace app\common\services\beadhouse;

use app\admin\controller\Login;
use app\common\model\BeadHouse;
use app\common\model\SystemManager;
use app\common\services\BaseServices;

use app\common\services\user\UserServices;
use think\Db;
class BeadHouseServices extends BaseServices
{
    public function setModel()
    {
        $this->model = new BeadHouse();
    }

    /**
     * 养老员列表
     */
    public function admin_index($where)
    {
        [$page,$limit] = $this->getPageValue();
        $list = $this->admin_search($where)
            ->alias('b')
            ->join('system_manager s', 'b.sysetm_manager_id=s.id','left')
            ->field('b.*,s.username')
            ->page($page,$limit)
            ->order('b.id','desc')
            ->select();
        $bh_list = [
            'list' => [],
            'count' => 0,
        ];
        if($list){
            $userServices = new UserServices();
            foreach ($list as $k => $v){
                $list[$k]['add_time'] = $v['add_time'] ? date('Y-m-d', $v['add_time']) : '';
                if($v['invitation_code']){
                    $list[$k]['invitation_code'] = config('site_url') . '/' . $v['invitation_code'];
                }else{
                    $list[$k]['invitation_code'] = '';
                }

                $user_count = $userServices->model->where('bead_house_id',$v['id'])->count('id');
                $list[$k]['user_count'] = $user_count;

            }
            $bh_list['list'] = $list;
            $count = $this->admin_search($where)
                ->alias('b')
                ->join('system_manager s', 'b.sysetm_manager_id=s.id','left')
                ->count('b.id');
            $bh_list['count'] = $count;
        }
        return res_data(1,'请求成功',$bh_list);

    }
    /**
     * 搜索
     * @param $where
     * @return \think\Model
     * Date: 2022/7/26
     * Time: 8:24
     * USER:GCQ
     */
    public function admin_search($where) {
        $model = $this->model->where('b.status','neq',0);

        //按关键字搜索
        if (isset($where['key']) && !empty($where['key'])){
            $model = $model->where('b.title|b.address|b.name','like','%'.$where['key'].'%');
        }
        if (isset($where['status']) && !empty($where['status'])){
            $model = $model->where('b.status',$where['status']);
        }
        //tab
        return $model;
    }
    /**
     * 添加养老院
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function admin_add($data)
    {
        $admin_data = [];
        $admin_data['username'] = trim($data['username']);
        $admin_data['truename'] = $data['username'];
        $admin_data['password'] = $data['password'];
        $admin_data['mobile'] = $data['mobile'];
        $admin_data['identity'] = 11;
        $admin_data['role_id'] = 11;
        $admin_data['register_time'] = time();
        unset($data['username'],$data['password']);
        $systemManager = new SystemManager();
        $systemManager_info = $systemManager->where('username',$admin_data['username'])->find();
        if($systemManager_info){
            return res_data(0,'该账号已存在');
        }


        Db::startTrans();
        try {

            $systemManager_id = $systemManager->insertGetId($admin_data);
            $data['sysetm_manager_id'] = $systemManager_id;


            $data['add_time'] = time();
            $bead_house_id = $this->model->insertGetId($data);
            if($bead_house_id){
                //生成小程序二维码
                $page = 'pages/bindhome/bindhome';
                $qrcode = $this->get_qrcode($bead_house_id, $page);
                $updatedata =[
                    'invitation_code' => $qrcode,
                    ];
                $this->model->where('id',$bead_house_id)->update($updatedata);

            }
            if($systemManager_id && $bead_house_id){
                Db::commit();
                return res_data(1, '添加成功');
            }else{
                Db::rollback();
                return  res_data(0,'添加失败');
            }

        }catch (\Throwable $e){
            Db::rollback();
            return  res_data(0,'添加失败'.$e->getMessage());
            // return  res_data(0,'添加失败'.$e->getMessage());
        }

    }
    public function admin_show($id)
    {
       $bead_house_info =  $this->model
            ->alias('b')
            ->join('system_manager s', 'b.sysetm_manager_id=s.id','left')
            ->where('b.id',$id)
            ->field('b.*,s.username')
            ->find();
       if($bead_house_info){
           $bead_house_info['invitation_code'] = config('site_url') . '/' . $bead_house_info['invitation_code'];
       }else{
           $bead_house_info = [];
       }
       return res_data(1,'请求成功',$bead_house_info);
    }
    public function admin_edit($id,$data)
    {
        $admin_data = [];
        // $admin_data['username'] = trim($data['username']);
        if($data['password']) {
            $admin_data['password'] = encryption($data['password']);
        }


        unset($data['password']);
        $bead_house = $this->model->where('id', $id)->find();
        if(!$bead_house)return res_data(0,'未找到该数据');

        $systemManager = new SystemManager();
        // $systemManager_info = $systemManager->where('username',$admin_data['username'])->find();
        // if($systemManager_info){
        //     if($bead_house['sysetm_manager_id'] != $systemManager_info['id'])
        //     {
        //         return res_data(0,'该账号已存在');
        //     }
        // }


        //生成小程序二维码
        $page = 'pages/bindhome/bindhome';
        $qrcode = $this->get_qrcode($id, $page);
        $data['invitation_code'] = $qrcode;
        try {
            $res = $this->model->where('id',$id)->update($data);

            $sys_res = $systemManager
                ->where('id',$bead_house['sysetm_manager_id'])
                ->update($admin_data);

            if($res !== false && $sys_res !== false){
                Db::commit();
                return res_data(1, '编辑成功');
            }else{
                Db::rollback();
                return res_data(0, '编辑失败');
            }

        }catch (\Throwable $e){
            Db::rollback();
            return  res_data(0,'编辑失败'.$e->getMessage());
            // return  res_data(0,'添加失败'.$e->getMessage());
        }

    }
    
    public function admin_del($id)
    {
        $bead_house_info =  $this->model
            ->where('id', $id)
            ->where('status', 'neq',0)
            ->find();
        if(!$bead_house_info){
            return res_data(0, '未找到该养老院');
        }
        $userServices = new UserServices();
        // $user_count = $userServices->model->where('bead_house_id', $bead_house_info['id'])->count('id');

        // if($user_count > 0){
        //     return res_data(0,'该养老院下绑定了用户，删除失败');
        // }

        Db::startTrans();
        try {
            $res = $this->model->where('id', $id)->update(['status' => 0]);

            $SystemManager = new \app\admin\model\SystemManager();
            // $ManagerInfo = $SystemManager->where('id',$bead_house_info['sysetm_manager_id'])->find();
            $Manager_res = $SystemManager->where('id',$bead_house_info['sysetm_manager_id'])->update(['status'=>0]);
            $user_res = $userServices->model->where('bead_house_id', $bead_house_info['id'])->update(['bead_house_id' =>0]);
            if($res !== false && $Manager_res !== false && $user_res !== false){
                Db::commit();
                return res_data(1, '删除成功');

            }else{
                Db::rollback();
                return  res_data(0,'删除失败');
            }

        }catch (\Throwable $e){
            Db::rollback();
            return  res_data(0,'添加失败'.$e->getMessage());
            // return  res_data(0,'添加失败'.$e->getMessage());
        }

    }
    public function admin_rebuild_qrcode($id)
    {
        $bead_hours_info = $this->model->where('id',$id)->find();
        if(!$bead_hours_info){
            json_success(res_data(0, '未找到该养老院'));
        }
        //生成小程序二维码
        $page = 'pages/bindhome/bindhome';
        $qrcode = $this->get_qrcode($id, $page);
        $data = [
            'invitation_code' => $qrcode,
            ];
        $res =$this->model->where('id',$id)->update($data);
        if($res !== false){
            return res_data(1,'生成成功',['invitation_code' => config('site_url') . '/' . $qrcode]);
        }else{
            json_success(res_data(0,'生成失败'));
        }

    }
    /**
     * @param $url
     * @param $id
     * @param $name
     * @return string
     * 生成小程序二维码
     */
    public function get_qrcode($id,$page)
    {
        $url = config('xiaocx_token_url').'?key='.config('xiaocx_token_key');
        $info = http_curl($url, 'get');


        if(isset($info['access_token']) && !empty($info['access_token'])){

            $urlcode = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $info['access_token'];
            $headers = array();
            // array_push($headers, "Content-Type: text/plain");
            array_push($headers, "Content-Type: application/json");
            array_push($headers, "Accept-Charset: utf-8");
            $data = array(
                'page' => $page,
                'scene' => "id=" . $id, //这里需要注意，这里可以用 ‘id=12’ 的格式传递多个参数，最多传递32个字符
                'width' => 100,
                'check_path' => false  //true  页面不存在或小程序未发布时报错  false 页面不存在或小程序未发布时 可以正常展示小程序码
            );
            Db::name('test')->insert(['con'=>json_encode($data)]);
            $json_data = json_encode($data);
            // echo $json_data;
            // $imgInfo = http_curl($urlcode,'post',$json_data,$headers);
            $imgInfo = curl($urlcode,$json_data,1,$headers,1);
            if($imgInfo != false){
                if($imgInfo['code'] == 200){

                }else{
                    return '';
                }
            }else{
                return '';
            }
            $dir = 'static/code/';
            $local = $dir .$id . '.jpg';
            // if (!is_readable($dir)) {
            //     is_file($dir) or mkdir($dir, 0777, true);
            // }
            $filename =  ROOT_PATH .'/public/static/code/' . $id .'jpg';

            if (file_exists($filename)) {

                //文件存在
                unlink($filename);
            }
            file_put_contents($local, $imgInfo['data']);
            return $local;
        }else{
            return '';
        }
    }

    /**
     *  下拉选项列表
     * @param $user_id
     * @param $role_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function admin_beadhouselist($user_id,$role_id)
    {
        if($role_id == 11){
            $list = $this->model->where('status', 'neq', 0)->where('sysetm_manager_id', $user_id)->field('id,title')->select();
        }else{
            $list = $this->model->where('status', 'neq', 0)->field('id,title')->select();
        }
       
        if($list){
        
        }else{
            $list = [];
        }
        return res_data(1, '请求成功', $list);
    }

    public function admin_get_bead_house_info($user_id)
    {
        $bead_house_info =  $this->model
            ->alias('b')
            ->join('system_manager s', 'b.sysetm_manager_id=s.id','left')
            ->where('b.sysetm_manager_id',$user_id)
            ->field('b.*,s.username')
            ->find();
        if($bead_house_info){
            $bead_house_info['invitation_code'] = config('site_url') . '/' . $bead_house_info['invitation_code'];
        }else{
            $bead_house_info = "";
        }
        return res_data(1,'请求成功',$bead_house_info);
    }


    //api

    public function api_bead_house_info($id,$user_id)
    {
        $info = $this->model->where('id', $id)->field('id, title, logo')->find();
        if($info){
            $userServices = new UserServices();
            $userInfo = $userServices->model->where('id',$user_id)->find();
            $type = 0;
            if($userInfo) {
                if ($userInfo['bead_house_id'] == $id) {
                    $type = 1;
                }

            }
            $info['type'] = $type;
        }else{
            $info = '';
        }


        return res_data(1,'请求成功', $info);
    }

}