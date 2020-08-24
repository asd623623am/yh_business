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

	public function test()
	{
		$res = $this->QrcodeCreate('111',2);
		dump($res);exit;
		// $tokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET';
		$token = '36_kr1fxhDmONEFrMV9Jf7WyZgy8UBiD5SEIcV2vFabM4FTzLCdEuTC-zG8OQw5FjJRqIxRUewRHM_06g9hRQuAjLiLUPwKccXdQ3Yd9Uer3BE6J8CR7sQlSwS5Y9TC11ko99kR2wOb2HBZUIuFVIJfADAJWX';
		$urls  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
		$data = [
			'action_name'	=> 'QR_LIMIT_STR_SCENE',
			'action_info'	=> [
				'name'	=> '你好啊',
				'url'	=> '哈哈哈'
			],
			'scene_id'	=> 100000,
			'scene_str' => 64
		];
		$ress=$this->curl_post($urls,$data);
		// $res=file_get_contents($urls,);
		dump($ress);exit;
		header("Content-Type:text/html;charset=utf8");
		$ee = urlencode("gQFI8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL1RqallrX1RseXJLek41MU1BeFlYAAIE8aPXVgMEXAIAAA==");//在ticket里打开网页的token
		$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ee";
		echo $url;
	}

	/* 创建二维码 @param - $qrcodeID传递的参数，$qrcodeType二维码类型 默认为临时二维码 @return - 返回二维码图片地址 */
public function QrcodeCreate($qrcodeID, $qrcodeType = 0) {
	if ($qrcodeType == 0) {
	$qrcodeType = 'QR_SCENE';
	} else {
	$qrcodeType = 'QR_LIMIT_SCENE';
	}
	$tempJson = '{"expire_seconds": 1800, "action_name": "' . $qrcodeType . '", "action_info": {"scene": {"scene_id": ' . $qrcodeID . '}}}';
	$access_token = '36_5VVs_3TeL-_5eJm9EKNay_3r6dt2qFihisB-n29o-NUJWiPtrLXKDSJbFpa0WT5FthAO0XqZK4ar5CRM1bOIDehaW9fh6ZxwOknoI7aNLbNGjkhI4oVj9Y5HLd3MYmyj4dd3vK45t2eDpVHzDDFhAJASSU';
	$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $access_token;
	$tempArr = $this->sendpostss ( $url, $tempJson );

	dump($tempArr);exit;
	if (@array_key_exists ( 'ticket', $tempArr )) {
	return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $tempArr ['ticket'];
	} else {
	$this->ErrorLogger ( 'qrcode create falied.' );
	$this->AccessTokenGet ();
	$this->QrcodeCreate ();
	}
}

// 工具函数 //
/* 使用curl来post一个json数据 */
// CURLOPT_SSL_VERIFYPEER,CURLOPT_SSL_VERIFYHOST - 在做https中要用到
// CURLOPT_RETURNTRANSFER - 不以文件流返回，带1
private function JsonPost($url, $jsonData){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($curl);
	dump($reslut);exit;
	if (curl_errno($curl)) {
	$this->ErrorLogger('curl falied. Error Info: '.curl_error($curl));
	}
	curl_close($curl);
	return $result;
	}

		/**
     * 发送post formdata请求
     */
	public function sendpostss($url,$data)
	{
	    $data = @json_encode($data);

	    $headers = [
	        'Content-Type: application/json;charset=utf-8',
	        'Content-Length: ' . strlen($data)
	    ];


		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_TIMEOUT, 8);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		$output = curl_exec($curl);
		curl_close($curl);
		return @json_decode($output, true);
	}

}