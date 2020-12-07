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
        $userInfo = getStoreidByKey($getData['access-token']);
        $storeid = $userInfo['storeid'];
        $scData = model('storecontent')->where(['storeid'=>$storeid])->find();
        $data = [];
        if(!empty($scData)){
            $data = $scData->toArray();
            $data['img'] = '/uploads/images/'.$data['img'];
        }
        if(empty($data)){
            $scData = model('storecontent')->where(['storeid'=>0])->find();
            if(!empty($scData)){
                $data = $scData->toArray();
                $data['img'] = '/uploads/images/'.$data['img'];
            }
        }
        return successMsg('操作成功',$data);

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
        $userInfo = getStoreidByKey($postData['access-token']);
        $storeid = $userInfo['storeid'];
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

        date_default_timezone_set("Asia/Shanghai"); //设置时区
        if(is_uploaded_file($_FILES['file']['tmp_name'])) {
            //把文件转存到你希望的目录（不要使用copy函数）
            $uploaded_file=$_FILES['file']['tmp_name'];
            //我们给每个用户动态的创建一个文件夹
            $date = date('Ymd',time());
            $user_path=ROOT_PATH . 'public' . DS . 'uploads/images/'.$date;
            //判断该用户文件夹是否已经有这个文件夹
            if(!file_exists($user_path)) {
                //mkdir($user_path);
                mkdir($user_path,0777,true);
            }

            //$move_to_file=$user_path."/".$_FILES['file']['name'];
            $file_true_name=$_FILES['file']['name'];
            $fielName = time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,"."));
            $move_to_file=$user_path."/".$fielName;//strrops($file_true,".")查找“.”在字符串中最后一次出现的位置
            if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {
                echo $date.'/'.$fielName."**上传成功".date("Y-m-d H:i:sa");

            } else {
                echo "上传失败".date("Y-m-d H:i:sa");

            }
        } else {
            echo "上传失败".date("Y-m-d H:i:sa");
        }

    }




}
