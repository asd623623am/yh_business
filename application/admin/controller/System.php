<?php
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;
use think\Db;

/**
 * 系统管理
 */
class System extends Common
{
	/**
	 * 操作日志.
	 * @return [type] [description]
	 */
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

	public function systemAdd()
	{
		if(check()){
		    $data=input('post.');
		    if(empty($data)){
		        exit('非法操作此页面');
		    }


		    $data_info=model('System')->select()->toArray();
		    
		    
		    if (empty($data_info)) {
		        $data['status'] = 1;
		        $data['ctime']	= time();
		        $info=model('System')->allowField(true)->save($data);

		        if($info){
		        	$this -> addLog('添加了一个新的系统');
		            win('添加成功');
		        }else{
		            fail('添加失败');
		        }
		    } else {

		       $temp = $data_info[0];
		       $where=[
		           'system_id'=>$temp['system_id']
		       ];
		       $arr=[
		            'company_logo'	=> $data['company_logo'],
           			'company_name'	=> $data['company_name'],
           			// 'system_data'	=> $data['system_data'],
           			// 'system_name'	=> $data['system_name']
		       ];
		       
		       $reslut = Db::table('system')->where($where)->update($arr);
		       if($reslut){
		       	$this -> addLog('添加了一个新的系统');
		           win('添加成功');
		       }else{
		           fail('添加失败');
		       }

		    }



		    
		}else{
		    return view();
		}
	}

	public function systemUpload(){
	    // 获取表单上传文件 例如上传了001.jpg
	      $file = request()->file('file');
	        if(empty($file)){
	            exit('非法操作此页面');
	        }
	    //动到框架应用根目录/public/uploads/ 目录下
	     $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	    if($info){
	        // 成功上传后 获取上传信息
	           echo json_encode(['font'=>'上传成功','code'=>1,'src'=>$info->getSaveName()]);
	    }else{
	        // 上传失败获取错误信息
	          fail($file->getError());
	    }
	}

	public function systemList()
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
		    $system_info = model('System')->order('system_id','desc')->page($page,$limit)->select();
		    $count=model('System')->count();
		    $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$system_info];
		    echo json_encode($info);
		    exit;

		}else{
		    return view();
		}
	}

	public function systemDel()
	{
		$system_id = input('post.system_id');
		if (empty($system_id)) {
			fail('非法操作此页面');
		}
		$where=[
		    'system_id'=>$system_id
		];
		//删除
		$res=model('System')->where($where)->delete();
		if($res){
			$this -> addLog('删除了一个系统');
		    win('删除成功');
		}else{
		    fail('删除失败');
		}
	}

	public function systemUpdateInfo()
	{
		$system_id = input('get.system_id');
		if (empty($system_id)) {
			fail('非法操作此页面');
		}
		$where = [
			'system_id'	=> $system_id
		];
		$data = model('System')->where($where)->find();
		$this->assign('data',$data);
		return view();
	}
	public function systemUpdate()
	{
		$data = input('post.');
		if (empty($data)) {
			fail('非法操作此页面');
		}

		$where = [
			'system_id'	=> $data['system_id']
		];

		$font = [
			'company_logo'	=> $data['company_logo'],
			'company_name'	=> $data['company_name'],
			// 'system_data'	=> $data['system_data'],
			// 'system_name'	=> $data['system_name']
		];
		$reslut = Db::table('system')->where($where)->update($font);

		if ($reslut) {
			$this -> addLog('修改了一个的系统');
		    win('修改成功');
		} else {
		    fail('修改失败');
		}
	}



	/**
	 * 微信小程序配置参数.
	 */
	public function WxApplets()
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
		    $data = model('Applets')->page($page,$limit)->select();
		    $count=model('Applets')->count();
		    $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
		    echo json_encode($info);
		    exit;

		}else{
		    return view();
		}
	}

	public function wxappAdd()
	{
		if(check()){
		    $data=input('post.');
		    if(empty($data)){
		        exit('非法操作此页面');
		    }
		    $data_info=model('Applets')->select()->toArray();
		    
		    
		    if (empty($data_info)) {
		        $info=model('Applets')->allowField(true)->save($data);

		        if($info){
		        	$this -> addLog('添加了一个新的微信小程序参数配置');
		            win('添加成功');
		        }else{
		            fail('添加失败');
		        }
		    } else {

		       $temp = $data_info[0];
		       $where=[
		           'id'=>$temp['id']
		       ]; 
		       $reslut = model('Applets')->where($where)->update($data);
		       if($reslut){
		       	$this -> addLog('添加了一个新的微信小程序参数配置');
		           win('添加成功');
		       }else{
		           fail('添加失败');
		       }

		    }



		    
		}else{
		    return view();
		}
	}

	public function wxappDel()
	{
		$id = input('id');
		if (empty($id)) {
			fail('非法操作此页面');
		}

		$res = model('Applets')->where(['id'=>$id])->delete();
		if ($res) {
				$this -> addLog('删除了一个微信小程序参数配置');
			win('删除成功');
		} else {
			fail('删除失败');
		}
	}

	public function wxappUpdateInfo()
	{
		$id = input('get.id');
		if (empty($id)) {
			fail('非法操作此页面');
		}
		$where = [
			'id'	=> $id
		];
		$data = model('Applets')->where($where)->find();
		$this->assign('data',$data);
		return view();
	}

	public function wxappUp()
	{
		$data = input();
		if (empty($data)) {
			fail('非法操作此页面');
		}
		$where = [
			'id'	=> $data['id']
		];

		$update = [
			'termNo'	=> $data['termNo'],
			'merId'		=> $data['merId'],
			'appId'		=> $data['appId'],
			'secret'	=> $data['secret'],
		];

		$res = model('Applets')->where($where)->update($update);
		if ($res) {
				$this -> addLog('修改了一个微信小程序参数配置');
			win('修改成功');
		} else {
			fail('修改失败');
		}
	}
#########################pos机
	public function Posconfig()
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
		    $data = model('Posconfg')->page($page,$limit)->select();
		    $count=model('Posconfg')->count();
		    $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
		    echo json_encode($info);
		    exit;

		}else{
		    return view();
		}
	}

	public function posDel()
	{
		$id = input('post.id');
		if (empty($id)) {
			fail('非法操作此页面');
		}
		$where=[
		    'id'=>$id
		];
		//删除
		$res=model('Posconfg')->where($where)->delete();
		if($res){
			$this -> addLog('删除了一个pos配置');
		    win('删除成功');
		}else{
		    fail('删除失败');
		}
	}

	public function posAdd()
	{
		if(check()){
		    $data=input('post.');
		    if(empty($data)){
		        exit('非法操作此页面');
		    }
		    $info=model('Posconfg')->allowField(true)->save($data);

		    if($info){
		    	$this -> addLog('添加了一个新的pos机配置');
		        win('添加成功');
		    }else{
		        fail('添加失败');
		    }

		    
		}else{
		    return view();
		}
	}

	public function posUpdateInfo()
	{
		$id = input('get.id');
		if (empty($id)) {
			fail('非法操作此页面');
		}
		$where = [
			'id'	=> $id
		];
		$data = model('Posconfg')->where($where)->find();
		$this->assign('data',$data);
		return view();
	}

	public function posUp()
	{
		$data = input();
		if (empty($data)) {
			fail('非法操作此页面');
		}
		$where = [
			'id'	=> $data['id']
		];

		$update = [
			'merId'		=> $data['merId'],
			'SN'	=> $data['SN'],
		];

		$res = model('Posconfg')->where($where)->update($update);
		if ($res) {
				$this -> addLog('修改了一个pos机配置');
			win('修改成功');
		} else {
			fail('修改失败');
		}
	}


	###############小程序
	public function ImagesAdd()
	{
		if(check()){
		    $data=input('post.');
		    $insert = ['img'=>$data['banner_url']];
		    $res = model('Img')->insert($insert);
		    if ($res) {
		    	$this -> addLog('添加了一个小程序服务图片');
				win('添加成功');
		    } else {
		    	fail('添加失败');
		    }
		}else{
		    return view();
		}
	}


	public function bannerUpload(){
	    // 获取表单上传文件 例如上传了001.jpg
	      $file = request()->file('file');
	        if(empty($file)){
	            exit('非法操作此页面');
	        }
	    //动到框架应用根目录/public/uploads/ 目录下
	    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	    if($info){
	        // 成功上传后 获取上传信息
	           echo json_encode(['font'=>'上传成功','code'=>1,'src'=>$info->getSaveName()]);

	    }else{
	        // 上传失败获取错误信息
	          fail($file->getError());
	    }

	}
}