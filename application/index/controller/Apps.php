<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Apps extends Controller
{
	public function login()
	{


		//连接本地的 Redis 服务
		 $redis = new \Redis();
		 $redis->connect('127.0.0.1', 6379);
		 $data=input();
		 //验证用户名
		 $where=[
		     'admin_name'=>$data['admin_name']
		 ];

		 if( $admin_obj = model('Admin')->where($where)->find()){
		     $admin_info = $admin_obj -> toArray();
		 }

		 if(empty($admin_info)){
		     return $this->reply(1,'账号或密码错误');
		 }

		 $admin_pwd=$this->createPwd($data['admin_pwd'],$admin_info['salt']);
		 if($admin_pwd!=$admin_info['admin_pwd']){
		     return $this->reply(1,'账号或密码错误');
		 }else{
		     $strs="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
		     $name=substr(str_shuffle($strs),mt_rand(0,strlen($strs)-11),10).time();
		     $admin_str = json_encode($admin_info);
		     //存储session信息
		      $redis->set($name, $admin_str);
		     file_put_contents('./a.log', $name);
		     // session($name,$admin_info);
		     return $this->reply(0,'登录成功',['token'=>$name]);
		 }
	}

	public function index()
	{
		$data = input();
		
		if (empty($data['token'])) {
			return $this->reply(1,'您还没有登录，请您登录');
		}

		//连接本地的 Redis 服务
		 $redis = new \Redis();
		 $redis->connect('127.0.0.1', 6379);
		 $res = $redis->get($data['token']);
		 if (!$res) {
			return $this->reply(1,'您还没有登录，请您登录！');
		 }
		 $user = json_decode($res,true);
		return $this->reply(0,'ok',['user'	=> $user['admin_name']]);
		// $admin = $this->verificationUser($data['token']);
		// if ($admin['code'] == 1 ) {
		// 	return $this->reply(1,$admin['msg']);
		// }
	}

	/**
	 * 列表.
	 * @return [type] [description]
	 */
	public function dataList()
	{
		$data = input();
		
		if (empty($data['token'])) {
			return $this->reply(1,'您还没有登录，请您登录');
		}

		if ($data['type'] == null || empty($data['page'])) {
			return $this->reply(1,'您的请求参数缺少，请重新请求！');
		}
		$admin = $this->verificationUser($data['token']);
		if ($admin['code'] == 1 ) {
			return $this->reply(1,$admin['msg']);
		}
		##小区信息.
		$district = $admin['data']['district_list'];
		$d_id = [];
		foreach ($district as $key => $value) {
			# 小区ID
			$d_id[] = $value['id'];
		}
		$where = [
			'district_id' => array('in',$d_id),
			'type'		  => $data['type'],
			'pay_status'  => 0
		];

		$title = '';
		if($data['type'] == 0){
			$title = '物业费';
		} else {
			$title = '供暖费';
		}


		$limit = 10;
		$list = model('Orders')->where($where)->page($data['page'],$limit)->select()->toArray();
		$temp = [];
		foreach ($list as $k => $v) {
			$home_where = [
				'home_id'	=> $v['home_id']
			];

			$homedata = model('Home')->where($home_where)->find();
			if($homedata == null){
				$area = '0.00';
			} else {
				$area = $homedata['area'];
			}
			$temp[] = [
				'id'	=> $v['id'],
				'name'	=> $v['home_name'],
				'money'	=> $v['money'],
				'title'	=> $title,
				'fee'	=> $v['fee'],
				'area'	=> $area
			];
		}
		return $this->reply(0,'ok',$temp);
	}

	/**
	 * 数据详情.
	 * @return [type] [description]
	 */
	public function dataInfo()
	{
		$data = input();
		
		if (empty($data['token'])) {
			return $this->reply(1,'您还没有登录，请您登录');
		}
		if ($data['id'] == null) {
			return $this->reply(1,'您的请求参数缺少，请重新请求！');
		}
		$admin = $this->verificationUser($data['token']);
		if ($admin['code'] == 1 ) {
			return $this->reply(1,$admin['msg']);
		}
		$where = [
			'id'	=> $data['id'],
			'pay_status'	=> 0,
		];
		
		$order_no = date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT);

		$field = [
			'order_no' => $order_no
		];

		$res = model('Orders')->where($where)->setField($field);
		if (!$res) {
			return $this->reply(2,'系统正忙，请重新请求！');
		}

		$info = model('Orders')->where($where)->find();
		if ($info == null) {
			return $this->reply(2,'系统正忙，请重新请求！');
		}

		$info = $info->toArray();

		$homeWhere = [
			'home_id'	=> $info['home_id']
		];
		$home = model('Home')->where($homeWhere)->find();
		if ($home == null) {
			return $this->reply(2,'系统正忙，请重新请求！');
		}
		$home = $home->toArray();
		if ($info['type'] == 0) {
			$type = '物业缴费';
			$standard = $info['fee'].'X 房屋面积 X'.$info['month'].'个月 +'.$info['cost'].'其他费用';
		} else {
			$type = '供暖缴费';
			$standard = $info['fee'].'X 房屋面积 X'.$info['cost'].'系数';
		}

		$temp = [
			'id'	=> $info['id'],
			'order_no'	=> $info['order_no'],
			'user'		=> $info['user_name'],
			'district'	=> $info['district_name'],
			'complex'	=> $info['complex'],
			'home_code'	=> $info['home_code'],
			'type'		=> $type, 
			'standard'	=> $standard, // 收费标准
			'area'		=> $home['area'], //面积
			'pay_money'		=> sprintf("%1\$.2f", $info['money']+$info['compensation']), //应收金额.
			'compensation'	=> $info['compensation'], //赔偿项
			'money'		=> $info['money']

		];
		return $this->reply(0,'ok',$temp);
	}

	/**
	 * 异步修改订单.
	 */
	public function orderNotifyUp()
	{
		$data = input();
		// $sign = $this->getSign($data);
		// dump($sign);exit;
		
		if (empty($data['token'])) {
			return $this->reply(1,'您还没有登录，请您登录');
		}
		if(empty($data['id']) || empty($data['order_no']) || empty($data['pay_status']) || empty($data['transNo']) || empty($data['payType']) ){
			return $this->reply(1,'非法请求');
		}

		$res = $this->verifySign($data);
		if ($res['code'] !=0) {
			return $this->reply($res['code'],$res['msg']);
		}



		//连接本地的 Redis 服务
		$redis = new \Redis();
		$redis->connect('127.0.0.1', 6379);
		$reslut = $redis->get($data['token']);
		if (!$reslut) {
			return $this->reply(1,'您还没有登录，请您登录！');
		}
		$user = json_decode($reslut,true);

		$arr = [
			'pay_status'	=> $data['pay_status'], //支付状态.
			'pay_type'		=> 2,   //支付状态 
			'transNo'			=> $data['transNo'], //交易号.
			'payTypes'			=> $data['payType'], //pos机支付方式.
			'finish_at'			=> time(),	//支付时间.
			'pay_fee'		=> $data['pay_fee'],	//实付金额  要不要无所谓  因为后期需求原因  数据库已经算出支付总金额字段 这个纯属是为了统一字段
		];
		$where = [
			'id'	=> $data['id'],
			'order_no'	=> $data['order_no']
		];
		$info = model('Orders')->where($where)->setField($arr);
		if($info){
			$datainfo = model('Orders')->where($where)->find();
			if ($datainfo == null) {
				fail('没有查到您的数据');
			}
			if ($datainfo['pay_status'] == 0) {
				fail('您的订单不是已缴费订单！');
			}
			$home = model('Home')->where(['home_id'=>$datainfo['home_id']])->find();

			if ($home == null) {
				fail('没有查到您的数据');
			}
			if ($datainfo['type'] == 0) {
				$str = '物业费';
			} else if ($datainfo['type'] == 1) {
				$str = '供暖费';
			} else {
				$str = '押金';
			}

			if ($datainfo['pay_type'] == 1) {
				$pay_type = '小程序支付';
			} else {

				if ($datainfo['payTypes'] == 0) {
					$pay_type = '订单支付 -- 刷卡支付';	
				} else if ($datainfo['payTypes'] == 1) {
					$pay_type = '订单支付 -- 微信支付';
				} else if ($datainfo['payTypes'] == 2) {
					$pay_type = '订单支付 -- 支付宝支付';
				} else if ($datainfo['payTypes'] == 3) {
					$pay_type = '订单支付 -- 现金支付';
				} else if ($datainfo['payTypes'] == 4) {
					$pay_type = '订单支付 -- 云闪付支付';
				}
				
			}

			$payment = model('Payment')->where(['p_id'=>$datainfo['p_id']])->find();
			if ($payment == null) {
				$title = '';
			} else {
				$title = $payment['content'];
			}

			$strs = model('System')->find();
			if ($strs == null) {
				$titles = '测试订单';
			} else {
				$titles = $strs['company_name'];
			}
			

			$font = [
				'm_name'	=> $titles, //商户名称
				'm_code'	=> '2020070701', //商户编号
				'sn'		=> $data['sn'], //终端编号
				'admin'		=> $user['admin_name'], //操作员
				'order'		=> $datainfo['order_no'], //订单号
				'home'		=> $home['district_name'].$home['complex']."区".$home['home_code'], //房屋信息
				'type'		=> $str, //缴费类型
				'pay_ctime'	=> date('Y.m.d H:i:s',$datainfo['finish_at']), //支付时间
				'pay_type'	=> $pay_type, //支付方式
				'title'		=> $title, //备注
				'money'		=> $datainfo['money'] //缴费金额
			];


			return $this->reply(0,'支付成功',$font);
			exit;


			$content = "    .... #小码旺铺订单# ....\n";

			$content .= "\n";
			$content .= "\n";
			
			$content .= "^B2    收据凭证\n";
			$content .= "\n";
			$content .= "物业存根             请妥善保管\n";
			$content .= "--------------------------------\n";
			$content .= "商户名称：".$titles."\n";
			$content .= "商户编号：2020070701\n";
			$content .= "终端编号：".$data['sn']."\n";
			$content .= "操作员：".$user['admin_name']."\n";
			$content .= "--------------------------------\n";
			$content .= "^H1订单编号：".$datainfo['order_no']."\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
			$content .= "^H2缴费类型：".$str."\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "支付时间：".date('Y.m.d H:i:s',$datainfo['finish_at'])."\n";
			$content .= "支付方式：".$pay_type."\n";
			$content .= "--------------------------------\n";
			$content .= "备注：".$title."\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "^H2缴费金额:";
			$content .= $datainfo['pay_fee'];
			$content .= "\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "...#由小码旺铺提供技术支持#...\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "    .... #小码旺铺订单# ....\n";

			$content .= "\n";
			$content .= "\n";
			
			$content .= "^B2    收据凭证\n";
			$content .= "\n";
			$content .= "业主存根             请妥善保管\n";
			$content .= "--------------------------------\n";
			$content .= "商户名称：".$titles."\n";
			$content .= "商户编号：2020070701\n";
			$content .= "终端编号：".$data['sn']."\n";
			$content .= "操作员：".$user['admin_name']."\n";
			$content .= "--------------------------------\n";
			$content .= "^H1订单编号：".$datainfo['order_no']."\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
			$content .= "^H2缴费类型：".$str."\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "支付时间：".date('Y.m.d H:i:s',$datainfo['finish_at'])."\n";
			$content .= "支付方式：".$pay_type."\n";
			$content .= "--------------------------------\n";
			$content .= "备注：".$title."\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "^H2缴费金额:";
			$content .= $datainfo['pay_fee'];
			$content .= "\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "\n";
			$content .= "...#由小码旺铺提供技术支持#...\n";
			
			$DEVICE_NO = 'kdt1081880';
			$key = '966eb';
			$selfMessage = array(
				'deviceNo'=>$DEVICE_NO,
				'printContent'=>$content,
				'key'=>$key,
				'times'=>1
			);
			$url = "http://open.printcenter.cn:8080/addOrder";
			$options = array(
				'http' => array(
					'header' => "Content-type: application/x-www-form-urlencoded ",
					'method'  => 'POST',
					'content' => http_build_query($selfMessage),
				),
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);



			return $this->reply(0,'支付成功');
		} else {
			return $this->reply(1,'支付失败');
		}
	}

	// 获取sign
	function getSign($data) {
		header('Content-type: text/html; charset=utf-8');
		$secret = "28c8edde3d61a0411511d3b1866f0636";
	    // 对数组的值按key排序
	    ksort($data);
	    // 生成url的形式
	    $param = http_build_query($data);
	    $str = urldecode($param); //解码
		$params = $str.$secret;
		echo $params;
	    // 生成sign
	    $sign = md5($params);
	    return $sign;
	}
	//验证签名.
	function verifySign($data) {
		header('Content-type: text/html; charset=utf-8');
		    // 验证参数中是否有签名
		    if (!isset($data['sign']) || !$data['sign']) {
		    	$font = [
		    		'code'=>1,
		    		'msg'	=> '发送的数据签名不存在'
		    	];
		        return $font;
		        die();
		    }
		    if (!isset($data['time']) || !$data['time']) {
		    	$font = [
		    		'code'=>1,
		    		'msg'	=> '发送的数据参数不合法'
		    	];
		        return $font;
		        die();
		    }
		    // 验证请求， 10分钟失效
		    if (time() - $data['time'] > 600) {
		    	$font = [
		    		'code'=>1,
		    		'msg'	=> '验证失效，请重新发送请求'
		    	];
		        return $font;
		        die();
		    }
		    $secret = "28c8edde3d61a0411511d3b1866f0636";
		    $sign = $data['sign'];
		    unset($data['sign']);
		    ksort($data);
		    $param = http_build_query($data);
		    $str = urldecode($param); //解码
			$params = $str.$secret;
		    // $secret是通过key在api的数据库中查询得到
			$sign2 = strtoupper(md5($params));
		    if ($sign == $sign2) {
		    	$font = [
		    		'code'=>0,
		    		'msg'	=> 'ok'
		    	];
		        return $font;
		    } else {
		    	$font = [
		    		'code'	=>1,
		    		'msg'	=> '签名不正确'
		    	];
		        return $font;
		    }
		}

	/**
	 * 修改赔偿项.
	 */
	public function updateData()
	{
		$data = input();
		
		if (empty($data['token'])) {
			return $this->reply(1,'您还没有登录，请您登录');
		}
		if ($data['id'] == null) {
			return $this->reply(1,'您的请求参数缺少，请重新请求！');
		}
		$admin = $this->verificationUser($data['token']);
		if ($admin['code'] == 1 ) {
			return $this->reply(1,$admin['msg']);
		}
		$where = [
			'id'	=> $data['id'],
			'pay_status'	=> 0
		];
		$order = model('Orders')->where($where)->find();
		if($order == null){
			return $this->reply(1,'系统正忙，请重新请求！');
		}
		$order = $order->toArray();
		// echo '<pre>';
		// print_r($order);exit;
		$money = sprintf("%1\$.2f", $order['compensation'] + $order['money']); //总金额

		if($data['compensation'] > $money){
			return $this->reply(1,'输入有误！赔偿项金额大约应缴金额！');
		}

		$newmoney = sprintf("%1\$.2f", $money - $data['compensation']);
		$update = [
			'compensation'	=> $data['compensation'],
			'money'	=> $newmoney,
		];

		$res = model('Orders')->where($where)->setField($update);
		if($res){
			return $this->reply(0,'编辑成功',$update);
		} else {
			return $this->reply(1,'编辑失败');
		}
	}
	/**
	 * 房屋搜索信息
	 * @return [type] [description]
	 */
	public function homeSel()
	{
		$data = input();
		
		if (empty($data['token'])) {
			return $this->reply(1,'您还没有登录，请您登录');
		}
		$admin = $this->verificationUser($data['token']);
		if ($admin['code'] == 1 ) {
			return $this->reply(1,$admin['msg']);
		}

		$complex = [
			[
				'id'	=> '甲',
				'name'	=> '甲',
			],
			[
				'id'	=> '乙',
				'name'	=> '乙',
			],
			[
				'id'	=> '丙',
				'name'	=> '丙',
			],
			[
				'id'	=> '丁',
				'name'	=> '丁',
			],
			[
				'id'	=> '四期',
				'name'	=> '四期',
			],
			[
				'id'	=> '四期商业',
				'name'	=> '四期商业',
			],
		];

		$temp = [
			'district'	=> $admin['data']['district_list'],
			'complex'	=> $complex
		];
		return $this->reply(0,'ok',$temp);
	}

	/**
	 * 房屋搜索数据.
	 * @return [type] [description]
	 */
	public function homeSelData()
	{
		$data = input();
		
		if (empty($data['token'])) {
			return $this->reply(1,'您还没有登录，请您登录');
		}

		if ($data['type'] == null || empty($data['page'])) {
			return $this->reply(1,'您的请求参数缺少，请重新请求！');
		}
		$admin = $this->verificationUser($data['token']);
		if ($admin['code'] == 1 ) {
			return $this->reply(1,$admin['msg']);
		}

		$where = [];
		if (!empty($data['district'])) {
			//条件.
			$where['district_id'] = $data['district'];
		}
		
		if (!empty($data['complex'])) {
			$where['complex']	= $data['complex'];
		}

		if (!empty($data['home_code'])) {
			$where['home_code'] = $data['home_code'];
		}

		if (!empty($data['user'])) {
			$where['user_name'] = $data['user'];
		}
		$where['type'] = $data['type'];

		$title = '';
		if($data['type'] == 0){
			$title = '物业费';
		} else {
			$title = '供暖费';
		}



		$limit = 10;
		$list = model('Orders')->where($where)->page($data['page'],$limit)->select()->toArray();
		$temp = [];
		foreach ($list as $k => $v) {
			$home_where = [
				'home_id'	=> $v['home_id']
			];

			$homedata = model('Home')->where($home_where)->find();
			if($homedata == null){
				$area = '0.00';
			} else {
				$area = $homedata['area'];
			}
			$temp[] = [
				'id'	=> $v['id'],
				'name'	=> $v['home_name'],
				'money'	=> $v['money'],
				'title'	=> $title,
				'fee'	=> $v['fee'],
				'area'	=> $area
			];
		}
		return $this->reply(0,'ok',$temp);
	}

	/**
	 * 退出
	 */
	public function logout(){
		$data = input();
		if (empty($data['token'])) {
			return $this->reply(1,'缺少token');
		}
	    $redis = new \Redis();
	    $redis->connect('127.0.0.1', 6379);
	    $redis->set($data['token'],null);

	    return $this->reply(0,'退出成功');
	}

	//生成密码
	private function createPwd($pwd,$salt){
	    return md5(md5($pwd).md5($salt).'shop');
	}

	/**
	 * 返回结果
	 * @param  int    $code [description]
	 * @param  string $msg  [description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	private function reply(int $code, string $msg = '',array $data = [])
	{
		return json([
			'code' => $code,
			'msg'  => $msg,
			'data' => $data
		]);
	}

	/**
	 * 验证用户是否登录.
	 * @return [type] [description]
	 */
	private function verificationUser($token)
	{

		//连接本地的 Redis 服务
		 $redis = new \Redis();
		 $redis->connect('127.0.0.1', 6379);
		 $res = $redis->get($token);
		 if (!$res) {
		 	$font = [
		 		'code'	=> 1,
		 		'msg'	=> '您还没有登录，请您登录',
		 	];

		 	return $font;
		 }
		 $user = json_decode($res,true);
		$where = [];
		//查看管理员有物业还是供暖权限
		if ($user['admin_type'] != 1) {
			$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
			$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
			$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
			$d_id = [];
			if (!empty($ddata)) {
				foreach ($ddata as $k => $v) {
					$d_id[] = $v['d_id'];
				}

				$where['id'] = array('in',$d_id);
			}
		}
		$district=Db::table("district")->where($where)->field("id,name")->order('id','asc')->select();
		$font = [
			'code'	=> 0,
			'msg'	=> 'ok',
			'data'	=> [
				'district_list' 	=> $district,
			]
		];
		return $font;
	}
}