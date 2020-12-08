<?php
namespace  app\index\controller;
use think\Controller;
use think\Request;

class Qrcode extends Controller{

    /**
     * Notes: 获取门店桌台列表
     * Class: getStoreDeskList
     * user: bingwoo
     * date: 2020/12/8 10:14
     */
    public function getStoreDeskList(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        //验证字段
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
        $userInfo = getStoreidByKey($getData['access-token']);
        $storeid = $userInfo['storeid'];
        $deskData = model('qrcode')->where(['storeid'=>$storeid])->field('storeid,tnumber')->select();
        if($deskData){
            $deskData = $deskData->toArray();
        }
        return successMsg('操作成功',$deskData);
    }
}
