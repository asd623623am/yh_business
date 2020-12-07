<?php
namespace  app\index\controller;
use think\Controller;
use think\Request;

class User extends Controller{

    /**
     * Notes: 获取登录信息
     * Class: getUserInfo
     * user: bingwoo
     * date: 2020/10/29 10:38
     */
    public function getMyUserInfo(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        //验证字段
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
        $userInfo = getStoreidByKey($getData['access-token']);
        $data['admin_name'] = $userInfo['admin_name'];
        $data['store_name'] = '';
        $data['store_logo'] = '';
        $storeData = model('store')->where(['storeid'=>$userInfo['storeid']])->field('logo,name')->find();
        if($storeData){
            $storeData = $storeData->toArray();
            $data['store_name'] = $storeData['name'];
            if(!empty($storeData['logo'])){
                $data['store_logo'] = $storeData['logo'];
            }
        }
        return successMsg('',$data);
    }
}
