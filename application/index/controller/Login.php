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

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData=input('post.');
        //验证传递字段
        $verifData = ['admin_name','admin_pwd','wx_openid'];
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
            //更新用户信息
            $saveData = [
                'wx_openid'=>$postData['wx_openid'],
                'pstrs'=>$postData['admin_pwd'],
            ];
            $where = ['admin_id'=>$admin_info['admin_id']];
            model('Admin')->save($saveData,$where);
            //存储门店id信息
            $data['access_token'] = md5($postData['wx_openid']);
            $this->redisdb  = new \redis();
            $this->redisdb->connect('127.0.0.1','6379');
            $this->redisdb->set($data['access_token'],json_encode($admin_info),86400);
            $this->redisdb->set($postData['wx_openid'],$data['access_token'],86400);
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
        $verifData = ['wx_openid'];
        verifColumn($verifData,$postData);
        $this->redisdb  = new \redis();
        $this->redisdb->connect('127.0.0.1','6379');
        $this->redisdb->set($postData['wx_openid'],'');
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

    /**
     * Notes: 获取tocken
     * Class: getAccessToken
     * user: bingwoo
     * date: 2020/12/23 10:15
     */
    public function getAccessToken(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证传递字段
        $verifData = ['wx_openid'];
        verifColumn($verifData,$postData);
        $redisdb  = new \redis();
        $redisdb->connect('127.0.0.1','6379');
        $value = $redisdb->get($postData['wx_openid']);
        $data = [
            'access_token' => $value
        ];
        return successMsg('获取成功',$data);
    }
}
