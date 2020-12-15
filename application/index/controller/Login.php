<?php
namespace  app\index\controller;
use think\Controller;
use think\Request;

class Login extends Controller{

    protected $redisdb;

    /**
     * Notes: 登录
     * Class: Login
     * user: bingwoo
     * date: 2020/10/20 11:41
     */
    public function Login(){

        new \Redis();
        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData=input('post.');
        //验证传递字段
        $verifData = ['admin_name','admin_pwd'];
        verifColumn($verifData,$postData);
        if(isset($postData['admin_name']) && empty($postData['admin_name'])){
            return failMsg('账号不能为空');
        }
        if(isset($postData['admin_pwd']) && empty($postData['admin_pwd'])){
            return failMsg('密码不能为空');
        }
        //验证用户名
        $where=[
            'admin_name'=>$postData['admin_name']
        ];
        if( $admin_obj = model('Admin')->where($where)->find()){
            $admin_info = $admin_obj -> toArray();
        }
        if(empty($admin_info)){
            return failMsg('账号或密码错误');
        }
        //验证密码
        $admin_pwd = createPwd($postData['admin_pwd'],$admin_info['salt']);
        if($admin_pwd != $admin_info['admin_pwd']){
            return failMsg('账号或密码错误');
        }else{
            //存储门店id信息
            $data['access_token'] = md5($postData['admin_name']);
            $this->redisdb  = new \redis();
            $this->redisdb->connect('127.0.0.1','6379');
            $this->redisdb->set($data['access_token'],json_encode($admin_info),7200);
            $str = $postData['admin_name'].'登录成功';
            addLog($str,$data['access_token']);
            return successMsg('登陆成功',$data);
        }
    }

    /**
     * 退出
     */
    public function logout(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证传递字段
        $verifData = ['access-token'];
        verifColumn($verifData,$postData);
        $this->redisdb  = new \redis();
        $this->redisdb->connect('127.0.0.1','6379');
        $this->redisdb->set($postData['access-token'],'');
        return successMsg('退出成功');
    }

    /**
     * Notes: 忘记密码
     * Class: forgetPwd
     * user: bingwoo
     * date: 2020/10/20 11:55
     */
    public function forgetPwd(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证传递字段
        $verifData = ['admin_name'];
        verifColumn($verifData,$postData);
        //验证用户名
        $where=[
            'admin_name'=>$postData['admin_name']
        ];
        if( $admin_obj = model('Admin')->where($where)->find()){
            return successMsg('请联系超级管理员修改账号密码，然后从新再试！');
        }else{
            return failMsg('账号有误，请确认后再次尝试！');
        }
    }
}
