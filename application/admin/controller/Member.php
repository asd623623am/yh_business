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
		        
		        $tel = input('get.tel');
		        $where = [];

		        if (!empty($tel)) {
		        	$len = strlen($tel);
		        	if ($len == 11) {
		        		$where = [
		        			'phone'	=> $tel
		        		];
		        	} else {
		        		$where = [
		        			'card'	=> $tel
		        		];
		        	}
		        }

		        $where['status']	= 1;
		        $dataInfo = model('Member')->where($where)->page($page,$limit)->select()->toArray();
		       $count=model('Member')->where($where)->count();
		        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$dataInfo];
		        echo json_encode($info);
		        exit;

		    }else{
		        return view();
		    }
    }

    /**
     * 会员详情.
     * @return [type] [description]
     */
    public function memberUpdateInfo()
    {
    	$data = input();
    	if (empty($data['uid']) || empty($data['page'])) {
    		exit('非法操作此页面');
    	}

    	$where = [
    		'uid'	=> $data['uid'],
    		'status'	=> 1
    	];

    	$info = model('Member')->where($where)->find();
    	if ($info == null) {
    		exit('没有找到您的数据！');
    	}

    	$arr = $info->toArray();
    	$this->assign('arr',$arr);
    	$this->assign('page',$data['page']);
    	return view();
    }
}