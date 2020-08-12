<?php
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;
class Login extends Controller{
    public function Login(){
        if(check()){
            $data=input('post.');
            //验证验证码是否正确
           /* if( $data['mycode'] != '8888' ){
                if(!captcha_check($data['mycode'])){
                    fail('验证码错误');
                }
            }*/
            //验证用户名
            $where=[
                'admin_name'=>$data['admin_name']
            ];

            if( $admin_obj = model('Admin')->where($where)->find()){
                $admin_info = $admin_obj -> toArray();
            }

            if(empty($admin_info)){
                fail('账号或密码错误');
            }

            $admin_pwd=createPwd($data['admin_pwd'],$admin_info['salt']);
            if($admin_pwd!=$admin_info['admin_pwd']){
                fail('账号或密码错误');
            }else{
                //存储session信息
                session('admin',$admin_info);

                win('登陆成功');
            }
        }else{
//            session('admin', null);
            if( session('?admin') ){
                $this -> redirect('index/index');
            }
            $this->view->engine->layout(false);
            return $this->fetch();
        }
    }

    /**
     * 退出
     */
    public function logout(){
        session(null);
        $this -> redirect('login/login');
    }
}
