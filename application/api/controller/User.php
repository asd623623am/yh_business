<?php
namespace app\api\controller;
use app\admin\model\Member;
use think\Controller;
use think\Request;

class User extends Controller{


    /**
     * Notes: 获取用户列表
     * Class: getUserList
     * user: bingwoo
     * date: 2020/12/17 14:32
     */
    public function getUserList(){

        $postData = input('');
        $vericolumn = ['storeid'];
        verifColumn($vericolumn,$postData);
        $userModel = new \app\common\model\User();
        $where = [];
        $where['storeid'] = ['=',$postData['storeid']];
        $userData = $userModel->getUserList($where);
        return json($userData['data']);
    }

    /**
     * Notes: 获取用户信息
     * Class: getUserInfo
     * user: bingwoo
     * date: 2020/12/17 13:29
     */
    public function getUserInfo(){

        $postData = input('');
        $vericolumn = ['wx_openid'];
        verifColumn($vericolumn,$postData);
        $userModel = new \app\common\model\User();
        $where = [];
        $where['wx_openid'] = ['=',$postData['wx_openid']];
        $userInfo = $userModel->getUserInfo($where);
        return json($userInfo);
    }

    /**
     * Notes: 同步会员信息(仅第一次同步数据)
     * Class: syncUserInfo
     * user: bingwoo
     * date: 2021/1/13 19:58
     * param wx_opneid:微信openid、card:卡号、phone：电话
     */
    public function syncUserInfo(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('');
        $veriColumn = ['wx_openid'];
        verifColumn($veriColumn,$postData);
        $wx_openid = $postData['wx_openid'];
        //获取会员用户数据
        $url = 'http://airscanmember.yinheyun.com.cn/api.php/user/getUserInfo?third_wx_openid='.$wx_openid;
        $ret = request_get($url);
        $ret = json_decode($ret,true);
        if(isset($ret['code']) && $ret['code'] == 1){
            if(!empty($ret['data'])){
                //@ todo 过滤用户数据
                $userModel = new \app\index\model\User();
                $saveData = $userModel->isVerifSyncColumn($ret['data']);
                $where = [];
                $where['wx_openid'] = ['=',$wx_openid];
                $syncRet = $userModel->editUserData(1,$saveData,$where);
                if($syncRet){
                    successMsg('同步数据成功');
                }
                failMsg('同步数据失败');
            }
        }
        failMsg('同步会员信息失败');
    }




}