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
        $data['admin_img'] = '/image/20201204162137.jpg';
        $data['store_name'] = '';
        $data['store_logo'] = '';
        $storeData = model('store')->where(['storeid'=>$userInfo['storeid']])->field('logo,name')->find();
        if($storeData){
            $storeData = $storeData->toArray();
            $data['store_name'] = $storeData['name'];
            if(!empty($storeData['logo'])){
                $data['store_logo'] = '/uploads/'.$storeData['logo'];
            }
        }
        return successMsg('',$data);
    }


    /**
     * Notes: 解密用户手机号并保存
     * Class: ecryptUserPhoned
     * user: bingwoo
     * date: 2021/1/15 20:37
     */
    public function ecryptUserPhoned(){

        if(Request::instance()->isGet() == false){
            failMsg('请求方式有误');
        }
        $postData = input('get.');
        $verifColumn = ['encryptedData','iv','sessionKey','openid','wx_name','wx_img','storeid','sex'];
        verifColumn($verifColumn,$postData);
        $aesKey = base64_decode($postData['sessionKey']);
        $aesIV = base64_decode($postData['iv']);
        $aesCipher = base64_decode($postData['encryptedData']);
        $result = openssl_decrypt($aesCipher,"AES-128-CBC",$aesKey,1,$aesIV);
        $dataObj = json_decode($result);
        if($dataObj == NULL){
            failMsg('参数有误');
        }
        $data = [
            'phone'=>''
        ];
        if(isset($dataObj->purePhoneNumber)){
            $data['phone'] = $dataObj->purePhoneNumber;
            $userModel = new \app\index\model\User();
            $where = [];
            $where['wx_openid'] = ['=',$postData['openid']];
            $userInfo = $userModel->getUserInfo($where);
            if($userInfo){
                $saveData = [
                    'phone'=>$dataObj->purePhoneNumber
                ];
                $ret = $userModel->editUserData('',$saveData,$where);
                if($ret){
                    $this->syncUserInfo($postData['openid']);
                    successMsg('更新用户信息成功',$data);
                }
            }else{
                $addData = [
                    'wx_name'=>$postData['wx_name'],
                    'wx_img'=>$postData['wx_img'],
                    'wx_openid'=>$postData['openid'],
                    'storeid'=>$postData['storeid'],
                    'phone'=>$dataObj->purePhoneNumber,
                    'sex'=>(int)$postData['sex']-1,
                    'create_time'=>time(),
                    'update_time'=>time(),
                ];
                $addRete = $userModel->addUserData($addData);
                if(!$addRete){
                    $this->syncUserInfo($postData['openid']);
                    successMsg('添加用户信息成功',$data);
                }
            }
        }
        successMsg('更新用户信息失败',$data);

    }

    /**
     * Notes: 同步会会员信息到
     * Class: syncUserInfo
     * user: bingwoo
     * date: 2021/1/28 15:45
     */
    public function syncUserInfo($wx_openid){
        $url = 'http://airscanmemeber.yinheyun.com.cn/api.php/syncuser/syncUserInfo';
        $param = [
            'wx_openid'=>$wx_openid
        ];
        return request_post($url,$param);
    }
}
