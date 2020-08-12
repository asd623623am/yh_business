<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;
/**
 * 反馈记录
 */
class Feedback extends Common
{
	public function feedbackAdd()
	{
		if(check()){
		    $data=input('post.');
		    if(empty($data)){
		        exit('非法操作此页面');
		    }
		    $data['status'] = 1;
		    $data['ctime']	= time();
		    $info=model('Feedback')->allowField(true)->save($data);

		    if($info){
		        win('添加成功');
		    }else{
		        fail('添加失败');
		    }
		}else{
		    return view();
		}
	}


	public function feedbackList()
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
		    $feedback_info = model('Feedback')->order('feedback_id','desc')->page($page,$limit)->select();
		    $count=model('Feedback')->count();
		    $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$feedback_info];
		    echo json_encode($info);
		    exit;

		}else{
		    return view();
		}
	}


	public function feedbackDel()
	{
		$feedback_id = input('post.feedback_id');
		if (empty($feedback_id)) {
			fail('非法操作此页面');
		}
		$where=[
		    'feedback_id'=>$feedback_id
		];
		//删除
		$res=model('Feedback')->where($where)->delete();
		if($res){
			 $this -> addLog('删除了一条反馈记录');
		    win('删除成功');
		}else{
		    fail('删除失败');
		}
	}

	public function feedbackUpdateInfo()
	{
		$feedback_id=input('get.feedback_id');
		if(empty($feedback_id)){
		    fail('非法操作此页面');
		}
		$where=[
		    'feedback_id'=>$feedback_id
		];
		$data=model('Feedback')->where($where)->find();
		$this->assign('data',$data);
		return view();
	}


	public function feedbackUpdate()
	{
		$data = input('post.');
		if (empty($data)) {
			fail('非法操作此页面');
		}
		$where = [
			'feedback_id'	=> $data['feedback_id']
		];

		$font = [
			'feedback_content'	=> $data['feedback_content'],
			'user'	=> $data['user'],
		];
		
		$reslut = Db::table('feedback')->where($where)->update($font);

		if ($reslut) {
			 $this -> addLog('修改了一条反馈记录');
		    win('修改成功');
		} else {
		    fail('修改失败');
		}
	}
}