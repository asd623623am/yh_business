<?php
namespace app\api\controller;
use app\admin\model\Member;
use think\Controller;

class User extends Controller{


    /**
     * Notes: 获取用户列表
     * Class: getUserList
     * user: bingwoo
     * date: 2020/12/17 14:32
     */
    public function getUserList(){

        $postData = input('post.');
        $vericolumn = ['storeid'];
        verifColumn($vericolumn,$postData);
        $memberModel = new Member();
        $where = [];
        $where['storeid'] = ['=',$postData['storeid']];
        $mData = $memberModel ->where($where)->select();
        if($mData){
            $mData = $mData->toArray();
        }
        return json($mData);
    }

    /**
     * Notes: 获取用户信息
     * Class: getUserInfo
     * user: bingwoo
     * date: 2020/12/17 13:29
     */
    public function getUserInfo(){

        $user = new Member();
        $data = $user->select();
        if($data){
            $data = $data->toArray();
        }
        return json($data);
    }




}