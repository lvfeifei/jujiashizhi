<?php

namespace app\admin\controller;
ini_set('max_execution_time', 600);

use think\Controller;

use think\Request;
use think\Db;
/**
 * 刷新图片地址
 */
class RefreshUrl extends Controller
{
    protected $image;//替换值
    protected $replace;  //被替换的值

    public function _initialize()
    {
    }
    //刷新数据库图片地址
    public function refres_url(){
        $old_domain = input('old_domain');
        if(!$old_domain){
            json_success(res_data(0, '旧的图片腾讯云桶域名不能为空'));
        }
        $new_domain = input('new_domain');
        if(!$new_domain){
            json_success(res_data(0, '新的图片腾讯云桶域名不能为空'));
        }
        
        $table_name = input('table_name');
        if(!$table_name){
            json_success(res_data(0, '修改的表名不能为空'));
        }
        $field = input('field');
        if(!$field){
            json_success(res_data(0, '修改的字段不能为空'));
        }
        //$list = Db::name($table_name)->where($field,'like',$old_domain.'%')->select();
        //dd($list);
        Db::name($table_name)->where($field,'like',$old_domain.'%')
        ->chunk(100,function ($infolist) use ($old_domain, $new_domain, $table_name, $field){
             //dd($infolist);
            foreach ($infolist as $item){
            
                try {
                    if($item[$field]){
            //          判断新地址是否在数据库picture字段存在
                        $newpicture_url = '';
                        if(strpos($item[$field],$new_domain) !== false) {
                            echo '助具id:'.$item['id'].',同步失败3.'.$newpicture_url. PHP_EOL;
                        }else{
                            $newpicture_url = str_replace($old_domain,$new_domain,$item[$field]);
                            Db::name($table_name)->where('id',$item['id'])->update([$field=>$newpicture_url]);
                            echo '助具id:'.$item['id'].',同步成功.'.$newpicture_url. PHP_EOL;
                        }
                        
                    
                    }else{
                        echo '助具id:'.$item['id'].',同步失败2'.PHP_EOL;
                    }
                }catch (\Throwable $e){
                    echo '助具id:'.$item['id'].',同步失败1'.PHP_EOL;
                }
            }
        });
       
       
    }
    
    //刷新数据库富文本图片地址
    public function content_refres_url(){
        $old_domain = input('old_domain');
        if(!$old_domain){
            json_success(res_data(0, '旧的图片腾讯云桶域名不能为空'));
        }
        $new_domain = input('new_domain');
        if(!$new_domain){
            json_success(res_data(0, '新的图片腾讯云桶域名不能为空'));
        }
        
        $table_name = input('table_name');
        if(!$table_name){
            json_success(res_data(0, '修改的表名不能为空'));
        }
        $field = input('field');
        if(!$field){
            json_success(res_data(0, '修改的字段不能为空'));
        }
       
        Db::name($table_name)->where($field,'like','%'.$old_domain.'%')
            ->chunk(100,function ($infolist) use ($old_domain, $new_domain, $table_name, $field){
                //dd($infolist);
                foreach ($infolist as $item){
                    
                    try {
                        if($item[$field]){
                            //          判断新地址是否在数据库picture字段存在
                            $newpicture_url = '';
                            if(strpos($item[$field],$new_domain) !== false) {
                                echo '助具id:'.$item['id'].',同步失败3.'.$newpicture_url. PHP_EOL;
                            }else{
                                $content_str = preg_replace('/'.$old_domain.'/', $new_domain, $item[$field]);
                                Db::name($table_name)->where('id',$item['id'])->update([$field=>$content_str]);
                                echo '助具id:'.$item['id'].',同步成功.'. PHP_EOL;
                            }
                            
                            
                        }else{
                            echo '助具id:'.$item['id'].',同步失败2'.PHP_EOL;
                        }
                    }catch (\Throwable $e){
                        echo '助具id:'.$item['id'].',同步失败1'.PHP_EOL;
                    }
                }
            });
        
        
    }


}