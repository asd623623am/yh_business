<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 会员管理
 */
class Member extends Common
{

	/**
	 * 会员列表.
	 * @return [type] [description]
	 */
	public function memberList()
	{
			if( request() -> isAjax() ){

		        $page=input('get.page');
		        if(empty($page)){
		            exit('非法操作此页面');
		        }
		        $limit=input('get.limit');
		        if(empty($limit)){
		            exit('非法操作此页面');
		        }
		        // ->order('home_id','desc')
		        $home_info = model('Home')->where(['is_delete'=>1])->page($page,$limit)->select()->toArray();
		       $count=model('Home')->where(['is_delete'=>1])->count();
		        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$home_info];
		        echo json_encode($info);
		        exit;

		    }else{
		        return view();
		    }
    }
}