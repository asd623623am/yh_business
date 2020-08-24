<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 日志查询
 */

class Operation extends Common
{
	public function operationLog()
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
		    $data = model('Log')->order('id','desc')->page($page,$limit)->select();
		    $count=model('Log')->count();
		    $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
		    echo json_encode($info);
		    exit;

		}else{
		    return view();
		}	
	}

	/**
	 * 管理员列表.
	 * @return [type] [description]
	 */
	public function adminList()
	{
	    return view();
	}

	/**
	 * 角色列表.
	 * @return [type] [description]
	 */
	public function roleList(){
	     if( request() -> isAjax() ){

	        $page=input('get.page');
	        if(empty($page)){
	            exit('非法操作此页面');
	        }
	        $limit=input('get.limit');
	        if(empty($limit)){
	            exit('非法操作此页面');
			}
			$admin = session('admin');
	        $role_info=model('Role')->where(['status'=>1,'admin_ids'=>$admin['admin_id']])->page($page,$limit)->select();
	        $count=model('Role')->where(['status'=>1,'admin_ids'=>$admin['admin_id']])->count();
	        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$role_info];
	        echo json_encode($info);
	        exit;

	     }else{
	         return view();
	     }
	}
}