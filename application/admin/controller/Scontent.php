<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 订单管理
 */
class Scontent extends Common
{
    public function sadd()
    {
        $admin = \session('admin');
        $where = [
            'storeid'   => $admin['storeid']
        ];
        if (request()->isPost() && request()->isAjax()) {
            $data = input();
            $res = Db::table('xm_store_content')->where($where)->find();
            if($res == null){
                if($data['banner_url'] != ''){
                    $data['banner_url'] = $str= ltrim ($data['banner_url'],'#');
                }
                $update = [
                    'storeid'  => $admin['storeid'],
                    'img'       => $data['banner_url'],
                    'packing_fee'   => $data['packing_fee'],
                    'content'   => $data['content'],
                    'ctime'     => time()
                ];
                $arr['is_charge'] = 0;
                if(isset($data['is_charge'])){
                    $update['is_charge'] = 1;
                }
                $info=Db::table('xm_store_content')->insertGetId($update);
                if($info){
                    $this -> addLog('添加了店铺简介');
                    win('添加成功');
                }else{
                    fail('添加失败');
                }
            } else {
                if($data['banner_url'] != ''){
                    $data['banner_url'] = $str= ltrim ($data['banner_url'],'#');
                }
                $arr = [
                    'img'       => $data['banner_url'],
                    'packing_fee'   => $data['packing_fee'],
                    'content'   => $data['content'],
                    'utime'     => time()
                ];
                $arr['is_charge'] = 0;
                if(isset($data['is_charge'])){
                    $arr['is_charge'] = 1;
                }
                $info = Db::table('xm_store_content')->where($where)->setField($arr);
                if($info){
                    $this -> addLog('修改了店铺简介');
                    win('修改成功');
                }else{
                    fail('修改失败');
                }
            }
        } else {
            $storeInfo = Db::table('xm_store_content')->where($where)->find();
            if(empty($storeInfo)){
                $storeInfo = [
                    'img'       => '',
                    'is_charge'   => 0,
                    'packing_fee'   => 0,
                    'content'   => '',
                ];
            }
            $this->assign('storeInfo',$storeInfo);
            return view();
        }
    }
}