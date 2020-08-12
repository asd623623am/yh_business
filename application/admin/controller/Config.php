<?php
namespace  app\admin\controller;
use think\Db;
use think\Request;

/**
 * 系统配置管理
 */
class Config extends Common
{
    public function index()
    {
        $config=Db::name("config")->find()?:column("config");
        if(request()->isAjax()&&request()->isPost())
        {
            $data=Request::instance()->except('file');
            if (!Db::name("config")->count())
            {
                $ret=Db::name("config")->insert($data);
            }
            else
            {
                $ret=Db::name("config")->update($data);
            }
            if (!$ret)
            {
                $this->ajax_error("修改配置失败!");
            }
            $this->ajax_success();
        }
        $this->assign("config",$config);
        return view();
    }
    public function uploadCert()
    {
        $file = request()->file('file');
        $uploadPath=ROOT_PATH . 'cert';
        if(!file_exists($uploadPath)){
            mkdir($uploadPath,0777,true);
        }
        $info = $file->rule('uniqid')->validate(['size'=>1048576,'ext'=>'pem'])->move($uploadPath);  //上传验证后缀名,以及上传之后移动的地址
        if(!$info) {
            $this->ajax_error("上传失败");
        }
        $this->ajax_success(["file"=>$info->getSaveName()]);
    }
}