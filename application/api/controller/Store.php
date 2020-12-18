<?php
namespace app\api\controller;
use think\Controller;

class Store extends Controller{

    /**
     * Notes: 获取门店信息
     * Class: getStoreInfo
     * user: bingwoo
     * date: 2020/12/17 12:01
     */
    public function getStoreInfo(){

        $postData = input('post.');
        $storeModel = new \app\admin\model\Store();
        $where = [];
        $where['member_secret_key'] = ['=',$postData['member_secret_key']];
        $where['airscan_secret_key'] = ['=',$postData['airscan_secret_key']];
        $data = $storeModel->where($where)->find();
        if($data){
            $data = $data->toArray();
        }
        return $data;
    }




}