<?php
namespace app\api\controller;
use think\Controller;

class Store extends Controller{

    /**
     * Notes: 获取门店列表
     * Class: getStoreList
     * user: bingwoo
     * date: 2020/12/17 12:01
     */
    public function getStoreList(){

        $postData = input('');
        if(!key_verify($postData)){
            return json(['code'=>2,'msg'=>'密钥信息有误']);
        }
        $storeModel = new \app\admin\model\Store();
        $where = [];
        $data = $storeModel->where($where)->select();
        if($data){
            $data = $data->toArray();
            return json($data);
        }
    }

    /**
     * Notes: 获取门店信息
     * Class: getStoreInfo
     * user: bingwoo
     * date: 2020/12/17 12:01
     */
    public function getStoreInfo(){

        $postData = input('');
        if(!key_verify($postData)){
            return json(['code'=>2,'msg'=>'密钥信息有误']);
        }
        $storeModel = new \app\admin\model\Store();
        $where = [];
        $where['store_secret_key'] = ['=',$postData['store_secret_key']];
        $data = $storeModel->where($where)->find();
        if($data){
            $data = $data->toArray();
        }
        return json($data);
    }


}