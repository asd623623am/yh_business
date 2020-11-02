<?php

namespace app\index\controller;
use think\Controller;
use think\Request;

/**
 * Created: by PhpStorm.
 * package: app\index\controller
 * Class: Store
 * user: bingwoo
 * date: 2020/10/26 11:47
 */
class Store extends Controller{

    /**
     * Notes: 获取店铺设置信息
     * Class: getStoreContentInfo
     * user: bingwoo
     * date: 2020/10/26 13:21
     */
    public function getStoreContentInfo(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        //验证字段
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
        $storeid = getStoreidByKey($getData['access-token']);
        $scData = model('storecontent')->where(['storeid'=>$storeid])->find();
        $data = [];
        if(!empty($scData)){
            $data = $scData->toArray();
        }
        if(empty($data)){
            $scData = model('storecontent')->where(['storeid'=>0])->find();
            if(!empty($scData)){
                $data = $scData->toArray();
            }
        }
        return successMsg('',$data);

    }

    /**
     * Notes: 修改门店内容信息
     * Class: editStoreInfo
     * user: bingwoo
     * date: 2020/10/26 13:21
     */
    public function editStoreContentInfo(){
        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','is_charge'];
        verifColumn($verifData,$postData);
        $storeid = getStoreidByKey($postData['access-token']);
        $scData = model('storecontent')->where(['storeid'=>$storeid])->find();
        $editData = [
            'is_charge'=>$postData['is_charge'],
            'packing_fee'=>0,
            'storeid'=>$storeid,
            'utime'=>time(),
        ];
        if(isset($postData['img']) && !empty($postData['img'])){
            $editData['img'] = $postData['img'];
        }
        if(isset($postData['packing_fee']) && !empty($postData['packing_fee'])){
            $editData['packing_fee'] = $postData['packing_fee'];
        }
        if(isset($postData['content']) && !empty($postData['content'])){
            $editData['content'] = $postData['content'];
        }
        if(!empty($scData)){
            $where['id'] = $scData['id'];
            $ret = model('storecontent')->save($editData,$where);
        }else{
            $editData['ctime'] = time();
            $ret = model('storecontent')->save($editData);
        }
        if($ret){
            return successMsg('保存成功');
        }else{
            return failMsg('保存失败');
        }
    }

    /**
     * Notes: 上传图片
     * Class: uploadImg
     * user: bingwoo
     * date: 2020/10/26 14:11
     */
    public function uploadImg(){

        $postData = input('post.');
        //验证字段
        $verifData = ['access-token'];
        verifColumn($verifData,$postData);
        getStoreidByKey($postData['access-token']);
        $file = $_FILES['file'];//得到传输的数据
        if(empty($file)){
            exit('非法操作此页面');
        }
        //动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            return successMsg('上传成功',$info);
        }else{
            // 上传失败获取错误信息
            return failMsg('上传失败');
        }
    }


}
