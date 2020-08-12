<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 手机号
 */
class Phone extends Common
{
	public function phoneAdd()
	{
		if(check()){
		    $data=input('post.');
		    if(empty($data)){
		        exit('非法操作此页面');
		    }
		    $count=model('Phone')->count();
		   	if ($count>=5) {
		   		fail('最多添加5个电话');
		   	}
		    $data['status'] = 1;
		    $data['ctime']	= time();
		    $info=model('Phone')->allowField(true)->save($data);

		    if($info){
		    	 $this -> addLog('添加一个新的联系方式');
		        win('添加成功');
		    }else{
		        fail('添加失败');
		    }
		}else{
		    return view();
		}
	}

	public function phoneList()
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
		    $feedback_info = model('Phone')->order('phone_id','desc')->page($page,$limit)->select();
		    $count=model('Phone')->count();
		    $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$feedback_info];
		    echo json_encode($info);
		    exit;

		}else{
		    return view();
		}	
	}

	public function phoneDel()
	{
		$phone_id = input('post.phone_id');
		if (empty($phone_id)) {
			fail('非法操作此页面');
		}
		$where=[
		    'phone_id'=>$phone_id
		];
		//删除
		$res=model('Phone')->where($where)->delete();
		if($res){
			$this -> addLog('删除了一个联系方式');
		    win('删除成功');
		}else{
		    fail('删除失败');
		}
	}

	public function phoneUpdateInfo()
	{
		$phone_id=input('get.phone_id');
		if(empty($phone_id)){
		    fail('非法操作此页面');
		}
		$where=[
		    'phone_id'=>$phone_id
		];
		$data=model('Phone')->where($where)->find();
		$this->assign('data',$data);
		return view();
	}

	public function phoneUpdate()
	{
		$data = input('post.');
		if (empty($data)) {
			fail('非法操作此页面');
		}
		$where = [
			'phone_id'	=> $data['phone_id']
		];

		$font = [
			'phone_name'	=> $data['phone_name'],
			'phone'	=> $data['phone'],
		];
		
		$reslut = Db::table('phone')->where($where)->update($font);

		if ($reslut) {
			$this -> addLog('修改了一个联系方式');
		    win('修改成功');
		} else {
		    fail('修改失败');
		}
	}

}