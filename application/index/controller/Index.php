<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller
{
	/**
	 * 首页.
	 * @return [type] [description]
	 */
	public function index()
	{
		$datas = input();
		//轮播图
		$dataimg = model('Banner')->select()->toArray();
		foreach ($dataimg as $key => $value) {
			if (!empty($value['banner_url'])) {
			$arr = explode("#",$value['banner_url']);
			$url=str_replace('\\', '/', $arr[1]);
			$dataimg[$key]['banner_url'] = $url;					
			}
			
		}
		$content = model('Brand')->where(['brand_show'=>1])->order('brand_id','desc')->find();
		if ($content == null) {
			$content = [];
		} else {
			$content['brand_logo'] = str_replace('\\','/',$content['brand_logo']);	
		}
		$count = 0;
		if (!empty($datas['open_id'])) {
			$user = model('User')->where(['openId'=>$datas['open_id']])->find();
			if ($user != null) {
				$homewhere = [
					'id'	=> $user['default'],
				];

				$homeData = model('Userhouse')->where($homewhere)->find();

				if ($homeData != null) {
					$where = [
						'is_delete'	=> 1,
						'district_id'	=> $homeData['district_id'],
						'complex'	=> $homeData['complex'],
						'home_code'	=> $homeData['home_code'],
						'owner'		=> $homeData['owner'],
						'tel'		=> array('like','%'.$homeData['tel'].'%'),

					];
					$home = model('Home')->where($where)->find();
					if ($home != null) {
						$order_where = [
							'home_id'	=> $home['home_id'],
							'type'		=> array('in',[0,1]),
							'status'	=> 1,
							'pay_status'	=> 0,
						];
						$order = model('Orders')->where($order_where)->select()->toArray();
						$count = count($order);
					}
				}
			}
		}

		$temp = [
			'img'	=> $dataimg,
			'content'	=> $content,
			'count'	=> $count
		];
		return $this->reply(0,'ok',$temp);
	}

	/**
	 * 物业、供暖缴费接口.
	 * @return [type] [description]
	 */
	public function billInfo()
	{
		$open_id = input('open_id');
		$type = input('type');
		if (empty($open_id) || empty($type)) {
			return $this->reply(1,'非法操作');
		}
		$ownInfo = $this->ownInfo($open_id);
		if ($ownInfo['code'] != 0) {
			return $this->reply($ownInfo['code'],$ownInfo['msg']);
		}

		$user = model('User')->where(['openId'=>$open_id,'status'=>1])->find()->toArray();

		$homewhere = [
			'id'	=> $user['default'],
		];

		$homeData = model('Userhouse')->where($homewhere)->find();

		$where = [
			'is_delete'	=> 1,
			'district_id'	=> $homeData['district_id'],
			'complex'	=> $homeData['complex'],
			'home_code'	=> $homeData['home_code'],
			'owner'		=> $homeData['owner'],
			'tel'		=> array('like','%'.$homeData['tel'].'%'),

		];
		$home = model('Home')->where($where)->find();


		$temp = [];
		//type == 1 是物业缴费,2是供暖缴费
		if ($type ==1) {
			$wheres = [
				'type'	=> 0 ,
				'pay_status'	=> 0,
				'home_id'		=> $home['home_id'],
				'status'		=> 1
			];
			
			$data = model('Orders')->order('id','desc')->where($wheres)->select()->toArray();
			$count = count($data);
			$temps = [];
			$temps['status']	= 3;

			

			if (!empty($data)) {

				$newdata = $data[0];
				
				$homedata = model('Home')->where(['home_id'=>$newdata['home_id']])->find()->toArray();
				// sprintf("%1\$.2f", $newdata['fee']*$homedata['area']-$newdata['compensation'])
			 	$temps = [
			 		'money'	=> sprintf("%1\$.2f", $newdata['money']),
			 		'time'	=> date('Y-m-d',$newdata['end_at']),
			 		'order_no'	=> $newdata['order_no'],
			 		'id'	=> $newdata['id'],
			 		'status'	=> $newdata['pay_status'],
			 	];
			} else {
				$wheres = [
					'type'	=> 0 ,
					'pay_status'	=> 1,
					'home_id'		=> $home['home_id'],
					'status'		=> 1
				];
				$data = model('Orders')->order('id','desc')->where($wheres)->select()->toArray();
				if(!empty($data)){
					$temps = [
						'status'	=> 1,
						'title'		=> $data[0]['title'].'('.$data[0]['paytime'].')',
					];
				}
			}
			$temp = [
				'ownInfo'	=> $ownInfo['data'],
				'data'	=> $temps,
				'count'	=> $count,
			];
			
		} else if ($type == 2) {
			//去查询供暖账单
			$wheres = [
				'type'	=> 1,
				'pay_status'	=> 0,
				'home_id'		=> $home['home_id'],
				'status'		=> 1
			];
	
			$data = model('Orders')->order('id','desc')->where($wheres)->select()->toArray();
			$temps = [];
			$count = count($data);
			$temps['status']	= 3;
			if (!empty($data)) {

				$newdata = $data[0];
				$homedata = model('Home')->where(['home_id'=>$newdata['home_id']])->find()->toArray();
				// sprintf("%1\$.2f", $newdata['fee']*$homedata['area']-$newdata['compensation'])
			 	$temps = [
			 		'money'	=> sprintf("%1\$.2f", $newdata['money']),
			 		'time'	=> date('Y-m-d',$newdata['end_at']),
			 		'order_no'	=> $newdata['order_no'],
			 		'id'	=> $newdata['id'],
			 		'status'	=> $newdata['pay_status'],
			 	];
			} else {
				$wheres = [
					'type'	=> 1,
					'pay_status'	=> 1,
					'home_id'		=> $home['home_id'],
					'status'		=> 1
				];
		
				$data = model('Orders')->order('id','desc')->where($wheres)->select()->toArray();
				if(!empty($data)){
					$temps = [
						'status'	=> 1,
						'title'		=> $data[0]['title'].'('.$data[0]['paytime'].')',
					];
				}
			}
			$temp = [
				'ownInfo'	=> $ownInfo['data'],
				'data'	=> $temps,
				'count'	=> $count,
			];
		}

		return $this->reply(0,'ok',$temp);
	}

	/**
	 * 添加记录反馈.
	 * @return [type] [description]
	 */
	public function feedbackAdd()
	{
		$data = input();
		
		$insert['feedback_content'] = $data['content'];
		$insert['user'] = $data['open_id'];
		$insert['status'] = 1;
		$insert['ctime'] = time();
		$info=model('Feedback')->allowField(true)->save($insert);


		if($info){
		    return $this->reply(0,'反馈成功');
		}else{
		   return $this->reply(1,'反馈失败');
		}
	}

	/**
	 * 文件上传.
	 * @return [type] [description]
	 */
	public function feedbackUpload(){
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

	/**
	 * 绑定住房信息
	 * @return [type] [description]
	 */
	public function homeInfo()
	{
		if (request()->isPost()) {
			$data = input();
			if(empty($data)){
				return $this->reply(1,'非法操作');
			}
			$home = model('Home')->where($data)->select()->toArray();
			if (empty($home)) {
				return $this->reply(2,'异常数据');
			} else {
				return $this->reply(0,'ok',$home);
			}
		} else {
			$district = model('District')->select()->toArray();
			if (empty($district)) {
				return $this->reply(2,'数据异常');
			} else {
				return $this->reply(0,'ok',$district);
			}
		}
		
	}

	/**
	 * 添加住房信息
	 * @return [type] [description]
	 */
	public function homeAdd()
	{
		$data = input();
		if (empty($data)) {
			return $this->reply(2,'缺少入参');
		}
		$where = [];
		$where['district_id'] = $data['district_id'];
		$where['complex'] = $data['complex'];
		$where['home_code'] = $data['home_code'];
		$where['owner'] = $data['owner'];
		$where['tel'] = array('like','%'.$data['tel'].'%');
		// $where = [
		// 	'district_id'	=> $data['district_id'],
		// 	'complex'		=> $data['complex'],
		// 	// 'home_code'		=> $data['building'],
		// 	'home_code'		=> $data['home_code'],
		// ];
		$home = model('Home')->where($where)->find();
		if ($home == null) {
			return $this->reply(2,'没找到您的房子，请重新输入');
		}
		// if ($home['owner'] != $data['owner'] || $home['tel'] != $data['tel']) {
		// 	return $this->reply(2,'房主姓名或联系方式有误');
		// }

		$uwhere = [
			'openId'	=> $data['open_id'],
			'status'	=> 1,
		];
		$user = model('User')->where($uwhere)->find();
		if ($user == null) {
			$insert = [
				'user_name'	=> $data['owner'],
				'openId'	=> $data['open_id'],
				'tel'		=> $data['tel'],
				'is_null'	=> 1,
				// 'default'	=> $home->home_id,
				'status'	=> 1,
				'ctime'		=> time(),
			];
			$res = model('User')->insertGetId($insert);
			if ($res == '') {
				return $this->reply(1,'添加失败');
			}
			$inserts = [
				'user_id'	=> $res,
				'house_id'	=> $home->home_id,
				'district_id'	=> $home->district_id, //小区ID
				'district_name'	=> $home->district_name, //小区名称
				'complex'		=> $home->complex,
				'home_code'		=> $home->home_code,
				'owner'			=> $home->owner,
				'tel'			=> $data['tel'],
				'status'	=> 1,
				'ctime'		=> time(),
			];

			$res = model('Userhouse')->insertGetId($inserts);
			if($res){

				$info = model('User')->where($uwhere)->setField(['default'=>$res]);
				if ($info) {
					return $this->reply(0,'添加成功');
				} else {
					 return $this->reply(1,'添加失败');
				}
			    
			}else{
			   return $this->reply(1,'添加失败');
			}
		} else {
			
			$wheres = [
				'user_id'	=> $user->user_id,
				'district_id'	=> $home->district_id, //小区ID
				'home_code'		=> $home->home_code,
				'complex'		=> $home->complex,
				'owner'			=> $home->owner,
				'tel'			=> $data['tel'],
				'status'		=> 1
			];
			$results = model('Userhouse')->where($wheres)->select()->toArray();
			if (!empty($results)) {
				return $this->reply(2,'您已经添加过这个房子了！');
			}
			$inserts = [
				'user_id'	=> $user->user_id,
				'house_id'	=> $home->home_id,
				'district_id'	=> $home->district_id, //小区ID
				'district_name'	=> $home->district_name, //小区名称
				'home_code'		=> $home->home_code,
				'complex'		=> $home->complex,
				'owner'			=> $home->owner,
				'tel'			=> $data['tel'],
				'status'	=> 1,
				'ctime'		=> time(),
			];
			$res = model('Userhouse')->insertGetId($inserts);
			if ($res) {
				$info = model('User')->where($uwhere)->setField(['default'=>$res]);
				if ($info) {
					return $this->reply(0,'添加成功');
				} else {
					 return $this->reply(1,'添加失败');
				}
			} else {
				return $this->reply(1,'添加失败');
			}
		}
	}

	/**
	 * 我的账单.
	 * @return [type] [description]
	 */
	public function meBill()
	{
		$data = input();
		if (empty($data)) {
			return $this->reply(1,'非法操作');
		}

		$user = model('User')->where(['openId'=>$data['open_id'],'status'=>1])->find();
		if ($user == null) {
			return $this->reply(1,'请绑定');
		}


		$homewhere = [
			'id'	=> $user['default'],
		];

		$homeData = model('Userhouse')->where($homewhere)->find();

		$whereh = [
			'is_delete'	=> 1,
			'district_id'	=> $homeData['district_id'],
			'complex'	=> $homeData['complex'],
			'home_code'	=> $homeData['home_code'],
			'owner'		=> $homeData['owner'],
			'tel'		=> array('like','%'.$homeData['tel'].'%'),

		];
		$home = model('Home')->where($whereh)->find();
		if ($data['type'] == 1) {
			if ($data['status'] == 3) {
					$where = [
						'home_id'	=> $home->home_id,
						'type'		=> 0,
					];
			} else {

				$where = [
					'home_id'	=> $home->home_id,
					'type'		=> 0,
					'pay_status'	=> $data['status'],
				];
			}
		} else if ($data['type'] == 2) {
			if ($data['status'] == 3) {

				$where = [
					'home_id'	=> $home->home_id,
					'type'		=> 1,
				];
			} else {

				$where = [
					'home_id'	=> $home->home_id,
					'pay_status'	=> $data['status'],
					'type'		=> 1,
				];
			}
		} else if ($data['type'] == 3) {
			if ($data['status'] == 3) {
				$where = [
					'home_id'	=> $home->home_id,
					'type'		=> 2,
				];
			} else {

				$where = [
					'home_id'	=> $home->home_id,
					'type'		=> 2,
					'pay_status'	=> $data['status'],
				];
			}
		} else {
			if ($data['status'] == 3) {
				$where = [
					'home_id'	=> $home->home_id,
				];
			} else {

				$where = [
					'home_id'	=> $home->home_id,
					'pay_status'	=> $data['status'],
				];
			}
		}

		$where['status'] = 1;
// echo strtotime('2010-03-24 08:15:42');exit;
		// $wdata = model('Orders')->where($where)->select()->toArray();
		if (!empty($data['date'])) {
			$s = $data['date'];
			$start = $s.'-01-01';
			$end = $s.'-12-30';
			$wdata = model('Orders')->
			where($where)->
			whereTime('ctime','between',[$start,$end])->
			order('id desc')->select()->toArray();
			
		} else {
			$wdata = model('Orders')->where($where)->order('id desc')->select()->toArray();
		}
		$newtemp = [];

		foreach ($wdata as $key => $value) {

			if ($value['type'] == 0) {
				$type = 1;
			} else if ($value['type'] == 1) {
				$type = 2;
			} else {
				$type = 3;
			}
			if ($value['type'] != 2) {
				$wheres = [
					'home_id'	=> $value['home_id']
				];
				$home = model('Home')->where($wheres)->find();
				if ($home == null) {
					return $this->reply(2,'操作有误！');
				}
				if ($value['pay_status'] == 0) {
					$status = 2;

					##################### 保留小数点后两位小数.不够补0
					// $money = sprintf("%1\$.2f", $value['fee']*$home['area'] - $value['compensation']);
					$money = sprintf("%1\$.2f", $value['money']);
					// $money = round($value['fee']*$home['area'] - $value['compensation'],2);
				} else if ($value['pay_status'] == 1) {
					$status = 1;
					$money = sprintf("%1\$.2f", $value['money']);
					// $money = round($value['fee']*$home['area'] - $value['compensation'],2);
				} else {
					$status = 3;
					$money = sprintf("%1\$.2f", $value['money']);
					// $money = round($value['fee']*$home['area'] - $value['compensation'],2);

				}
			} else {
				if ($value['pay_status'] == 0) {
					$status = 2;
					$money = $value['fee'] - $value['compensation'];
				} else if ($value['pay_status'] == 1) {
					$status = 1;
					$money = $value['pay_fee'];
				} else {
					$status = 3;
					$money = $value['pay_fee'];

				}
			}
			
			$newtemp[] = [
				'order_no'	=> $value['order_no'],
				'status'	=> $status,
				'title'		=> $value['title'],
				'money'		=> $money,
				'type'		=> $type,
				'ctime'		=> $value['ctime'],
				'id'		=> $value['id'],
			];
		}
		return $this->reply(0,'ok',$newtemp);


	}

	/**
	 * 账单详情.
	 * @return [type] [description]
	 */
	public function billList()
	{
		$data = input();
		if (empty($data)) {
			return $this->reply(1,'非法操作');
		}

		$where = [
			'id'	=> $data['id']
		];

		$datas = model('Orders')->where($where)->find()->toArray();
		$homedata = model('Home')->where(['home_id'=>$datas['home_id']])->find()->toArray();
		if ($data['type'] == 3) {
			$deposit = model('Orders')->where($where)->find();
			if ($deposit == null) {
				return $this->reply(1,'非法操作',[]);
			}
			
			$temp = [
				'id'	=> $deposit->id,
				'name'	=> $deposit->title,
				'order_no'	=> $deposit->order_no,
				'money'	=> $deposit->fee,
				'paytime' => date('Y-m-d H:i:s',$deposit->paytime),
			];
			if ($deposit->pay_status == 0) {
				$temp['status'] = '未缴费';
			} else if ($deposit->pay_status == 1) {
				$temp['status'] = '已缴费';
			} else {
				$temp['status'] = '退款';
			}
			return $this->reply(0,'ok',$temp);

		} else {
			$temp = [
				'id'	=> $datas['id'],
				'name'	=> $datas['title'],
				'order_no'	=> $datas['order_no'],
				'money'	=> sprintf("%1\$.2f", $datas['money']),
				'paytime' => date('Y-m-d H:i:s',$datas['finish_at']),
				'date'	=> date('Y-m-d',$datas['start_at']).'-'.date('Y-m-d',$datas['end_at'])
			];
			if ($datas['pay_status'] == 0) {
				$temp['status'] = '未缴费';
				$temp['paytime'] = '';
			} else if ($datas['pay_status'] == 1) {
				$temp['status'] = '已缴费';
			} else {
				$temp['status'] = '已退款';
			}
			return $this->reply(0,'ok',$temp);
		}
	}

	/**
	 * 押金
	 * @return [type] [description]
	 */
	public function deposit()
	{
		$open_id = input('open_id');
		if (empty($open_id)) {
			return $this->reply(3,'非法操作');
		}

		$ownInfo = $this->ownInfo($open_id);
		if ($ownInfo['code'] != 0) {
			return $this->reply($ownInfo['code'],$ownInfo['msg']);
		}
		//押金管理

		$where = [
			'id'	=> $ownInfo['data']['default_id'],
			'status'	=> 1,
		];
		$user = model('Userhouse')->where($where)->find();
		if ($user == null) {


			##  status == 3  是没有订单 ==0 是未支付 == 1是已支付 == 2 是已退款
			$temp = [
				'ownInfo'	=> $ownInfo['data'],
				'deposit'	=> ['stauts' => 3],
			];
			return $this->reply(0,'ok',$temp);
		}


		//查房屋id
		$homewhere = [
			'is_delete'	=> 1,
			'district_id'	=> $user['district_id'],
			'complex'	=> $user['complex'],
			'home_code'	=> $user['home_code'],
			'owner'		=> $user['owner'],
			'tel'		=> array('like','%'.$user['tel'].'%'),
		];
		$home = model('Home')->where($homewhere)->find();
		if ($home == null) {
			$temp = [
				'ownInfo'	=> $ownInfo['data'],
				'deposit'	=> ['stauts' => 3],
			];
			return $this->reply(0,'ok',$temp);
		}

		$where = [
			'status'	=> 1,
			'type'		=> 2,
			'home_id'	=> $home['home_id'],
			// 'pay_status' => 0
		];
		$deposit = model('Orders')->where($where)->order('id desc')->find();
		$depositData = [];
		if ($deposit == null) {
			$depositData = [
				'status'		=> 3,
			];
		} else {

			if($deposit->pay_status == 2){
				$deposit->pay_status = 3;
			}


			$depositData = [
				'id'			=> $deposit->id,
				'deposit_money'	=> $deposit->fee, //应缴金额
				'title'			=> $deposit->msg,			//备注
				'cost_info'		=> $deposit->cost_info,	//明细
				'order_no'		=> $deposit->order_no,
				'status'		=> $deposit->pay_status,
			];
		}
		$temp = [
			'ownInfo'	=> $ownInfo['data'],
			'deposit'	=> $depositData
		];
		return $this->reply(0,'ok',$temp);
	}


	/**
	 * 个人详情接口
	 * @return [type] [description]
	 */
	private function ownInfo($open_id)
	{
		// $open_id = input('open_id');
		$font = [];
		if (empty($open_id)) {
			return $font = [
				'code'	=> 3,
				'msg'	=> '非法操作',
			];
		}
		$res = $this->isNull($open_id);
		if ($res['code']==2) {
			return $font = [
				'code'	=> $res['code'],
				'msg'	=> $res['msg'],
			];
		}
		$user = model('User')->where(['openId'=>$open_id,'status'=>1])->find();
		$user_home = model('Userhouse')->where(['id'=>$user['default']])->find();

		$where['district_id'] = $user_home['district_id'];
		$where['complex'] = $user_home['complex'];
		$where['home_code'] = $user_home['home_code'];
		$where['owner'] = $user_home['owner'];
		$where['tel'] = array('like','%'.$user_home['tel'].'%');
		// $where = [
		// 	'district_id'	=> $user_home['district_id'],
		// 	'complex'		=> $user_home['complex'],
		// 	'home_code'		=> $user_home['home_code'],
		// 	'owner'			=> $user_home['owner'],
		// 	'tel'			=> $user_home['tel']
		// ];
		$home = model('Home')->where($where)->find();
		if ($home == null) {
			$user_homes = model('Userhouse')->where(['user_id'=>$user['user_id']])->select()->toArray();
			if(empty($user_homes)){	
				return $font = [
					'code'	=> 3,
					'msg'	=> '没有找到您的房子',
				];
			}
			foreach($user_homes as $k=>$v){
				$wheres['district_id'] = $v['district_id'];
				$wheres['complex'] = $v['complex'];
				$wheres['home_code'] = $v['home_code'];
				$wheres['owner'] = $v['owner'];
				$wheres['tel'] = array('like','%'.$v['tel'].'%');
				$home = model('Home')->where($wheres)->find();
				if($home == null){
					continue; //跳过本次循环
				} else {
					$res = model('User')->where(['openId'=>$open_id,'status'=>1])->setField(['default'=>$v['id']]);
					if(!$res){
						return $font = [
							'code'	=> 3,
							'msg'	=> '没有找到您的房子,请重新登录！',
						];
					}
					$home = $home;
					break; //终止循环
				}
			}

			if($home == null){
				return $font = [
					'code'	=> 3,
					'msg'	=> '没有找到您的房子.',
				];
			}
		}
		$district = model('District')->where(['id'=>$home->district_id])->find();
		if ($district == null) {
			return $font = [
				'code'	=> 3,
				'msg'	=> '没有找到您的房子',
			];
		}
		//个人信息
		$ownInfo = [
			'district' 	=> $district->name, //小区
			'building'	=> $home->building, //楼号
			'unit'		=> $home->unit, 	//单元
			'floor'		=> substr($home->room,0,1),	//楼层
			'room'		=> $home->room,		//房间号
			'owner'		=> $home->owner,	//业主
			'tel'		=> $user_home->tel,		//手机号
			'default_id'	=> $user->default, //默认选中ID
			'home_code'	=> $home->home_code,
			'complex'	=> $home->complex,
		];
		return $font = [
			'code'	=> 0,
			'msg'	=> 'ok',
			'data'	=> $ownInfo
		];
	}

	/**
	 * 房屋列表
	 * @return [type] [description]
	 */
	public function homeList()
	{
		$open_id = input('open_id');
		if (empty($open_id)) {
			return $font = [
				'code'	=> 3,
				'msg'	=> '非法操作',
			];
		}
		$where = [
			'openId'	=> $open_id,
			'status'	=>1
		];
		$userdata = model('User')->where($where)->find();

		$font = [];
		if ($userdata != null) {
			$font = [
				'default'	=> $userdata['default'],
			];
			$uwhere = [
				'user_id'	=> $userdata->user_id 
			];
			$house = model('Userhouse')->where($uwhere)->select()->toArray();
			$data = [];
			if (empty($house)) {
				$data = [];
			} else {
				$data = $house;
			}
			// foreach ($house as $key => $value) {
			// 	$home = model('Home')->where(['home_id'=>$value['house_id']])->find();
			// 	if ($home == null) {
			// 		$data[] = [];
			// 	} else {
			// 		$data[] = $home;	
			// 	}
			// }
			// foreach ($data as $k => $v) {
			// 	$where = [
			// 		'id'	=> $v->district_id
			// 	];
			// 	$ress = model('District')->where($where)->find();
			// 	if ($ress == null) {
			// 		$data[$k]['district_name'] = '';
			// 	}else{
			// 		$data[$k]['district_name'] = $ress->name;
			// 	}
			// }
			$font['data'] = $data;
		} else {
			$font = [];
		}
		
		return $this->reply(0,'ok',$font);
	}

	/**
	 * 切换房屋.
	 * @return [type] [description]
	 */
	public function homeUpdate()
	{
		$data = input();
		if (empty($data['open_id']) || empty($data['id']) ) {
			return $this->reply(1,'非法操作');
		}
		$where = [
			'openId'	=> $data['open_id']
		];
		$up = [
			'default'	=> $data['id']
		];

		$result = model('User')->where($where)->setField($up);
		if ($result) {
			return $this->reply(0,'OK');
        } else {
        	return $this->reply(1,'切换失败');
        }
	}

	/**
	 * 公告通知.
	 * @return [type] [description]
	 */
	public function brandInfo()
	{
		$page=input('page');
		if(empty($page)){
		    return $this->reply(1,'非法操作此页面');
		}
		$limit = 10;
		$data = model('Brand')->where(['brand_show'=>1])->order('brand_id','desc')->page($page,$limit)->select()->toArray();
		foreach ($data as $key => $value) {
			$data[$key]['brand_logo'] = str_replace('\\','/',$value['brand_logo']);
		}
		return $this->reply(0,'ok',$data);
	}

	/**
	 * 公告详情.
	 * @return [type] [description]
	 */
	public function brandList()
	{
		$id = input('brand_id');
		if (empty($id)) {
			return $this->reply(1,'非法操作');
		}
		$data = model('Brand')->where(['brand_id'=>$id,'brand_show'=>1])->find()->toArray();
		$data['brand_logo'] = str_replace('\\', '/', $data['brand_logo']);
		$data['brand_describe'] = explode('<br/>', $data['brand_describe']);
		return $this->reply(0,'ok',$data);
	}

	/**
	 * 个人中心接口.
	 * @return [type] [description]
	 */
	public function ownInfoList()
	{
		$open_id = input('open_id');
		$res = $this->ownInfo($open_id);
		$font = [];
		if ($res['code'] == 2) {
			$font = [];
		} else {
			$font = $res['data'];
		}
		return $this->reply(0,'ok',$font);
	}

	/**
	 * 轮播图.
	 * @return [type] [description]
	 */
	public function bannerImg()
	{
		$data = model('Banner')->select()->toArray();
		foreach ($data as $key => $value) {
			$arr = explode("#",$value['banner_url']);
			$data[$key]['banner_url'] = $arr[1];
		}
		return $this->reply(0,'ok',$data);
	}

	private function isNull($open_id)
	{
		$where = [
			'openId' => $open_id,
			'status'=> 1
		];
		$userdata = model('User')->where($where)->find();
		$font = [];	
		if ($userdata == null) {
			return $font=[
				'code'	=> 2,
				'msg'	=> '您需要绑定',
			];
		} else {
			if ($userdata['is_null'] != 1) {
				return $font=[
					'code'	=> 2,
					'msg'	=> '您需要绑定',
				];
			}
		}

	}
	/**
	 * 联系我们接口.
	 * @return [type] [description]
	 */
    public function phoneData()
    {	
    	$phonedata = model('Phone')->select()->toArray();
    	if (empty($phonedata)) {
    		return $this->reply(1,'空数据');
    	} else {
    		return $this->reply(0,'成功',$phonedata);
    	}
    }

    public function homeUp()
    {
    	$data = input();
		if (empty($data)) {
			return $this->reply(1,'非法操作');
		}
		$where = [
			'district_name'	=> $data['district'],
			'complex'		=> $data['complex'],
			'home_code'		=> $data['home_code'],
			'owner'			=> $data['owner'],
			'tel'			=> $data['tel']
		];
		$home = model('Home')->where($where)->find();
		if ($home == null) {
			return $this->reply(2,'信息填写有误，请填写正确信息！');
		} else {
			$userwhere = [
				'openId'	=> $data['open_id'],
				'status'	=> 1
			];
			$user = model('User')->where($userwhere)->find();
			if ($user == null) {
				return $this->reply(2,'修改失败');
			} else {
				if ($user['default'] == $home['home_id']) {
					return $this->reply(1,'修改成功');
				} else {
					$res = model('User')->where($userwhere)->setField(['default'=>$home['home_id']]);
					if (!$res) {
						return $this->reply(2,'修改失败');
					} else {
						$insert = [
							'user_id'	=> $user->user_id,
							'house_id'	=> $home->home_id,
							'status'	=> 1,
							'ctime'		=> time(),
						];

						$ress = model('Userhouse')->allowField(true)->save($insert);
						if ($ress) {
							return $this->reply(1,'修改成功');
						} else {
							return $this->reply(2,'修改失败');
						}
					}
				}
			}
		}
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
     * 微信授权.
     * @return [type] [description]
     */
    public function getWxinfo()
    {
    	$code = input('code');
    	if(empty($code)){
    		return $this->reply(1,'非法操作此页面');
    	}


    	$user = model('Applets')->find();
    	if ($user == null) {
    		return $this->reply(1,'请去后台添加小程序信息');	
    	}
    	// $appid = $user->appId;
    	$appid = 'wx0731f3694a3fb5ba';
    	$secret = '79752682758fc58b6c48dc4c827a9a05';
    	$url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";
    	$res = file_get_contents($url);

    	$arr = json_decode($res,true);
    	if (isset($arr['errcode'])) {
    		return $this->reply(1,'授权失败,重新授权');
    	}
    	return $this->reply(0,'授权成功',$arr);
    }


    ##修改  小程序的头像和网名，为了和公众号同步！
    public function setUser()
    {
    	$data = input();
    	if (empty($data)) {
    		return $this->reply(1,'非法操作');
    	}

    	$where = [
    		'openId'	=> $data['open_id']
    	];

    	$up = [
       		'nickname'		=> '', //网名
    	];
    	$res = model('User')->where($where)->setField($up);
    	if ($res) {
    		$ups = [
    			'nickname'		=> $data['nickName'], //网名
    			'city'		=> $data['city'], //网名
    			'province'		=> $data['province'], //网名
    			'country'		=> $data['country'], //网名
    		];
    		$ress = model('User')->where($where)->setField($ups);
    		if ($ress) {
    			return $this->reply(0,'成功');
    		} else {
    			return $this->reply(1,'失败');
    		}
    	} else {
    		return $this->reply(1,'失败');
    	}
    	
    }

    /**
     * 返回小程序服务图片
     * @return [type] [description]
     */
    public function serviceImg()
    {
    	$dataimg = model('Img')->order('id desc')->select()->toArray();
    	foreach ($dataimg as $key => $value) {
    		if (!empty($value['img'])) {
    		$arr = explode("#",$value['img']);
    		$url=str_replace('\\', '/', $arr[1]);
    		$dataimg[$key]['img'] = $url;					
    		}
    	}
    	$data = [
    		'img'	=> $dataimg[0]['img']
    	];
    	return $this->reply(0,'成功',$data);
    }

    ###打印电子发票.
    public function payinvoice()
    {
    	$order = input('order_no');
    	if (empty($order)) {
    		return $this->reply(1,'缺少入参');
    	}
    	$time = date('YmdHis',time());
    	$round = rand(1111,9999);

    	$where = [
    		'order_no'	=> $order,
    		'status'	=> 1
    	];
    	$res = model('Orders')->where($where)->find();
    	if ($res == null) {
    		return $this->reply(2,'没有找到您的订单！');
    	}

    	if ($res->money == null) {
    		$money = $res->pay_fee;
    	} else {
    		$money = $res->money;
    	}
    	
    	$temp = [
    		'orgNo'	=> '2100',
    		'charset'	=> 'UTF-8',
    		'termNo'	=> 'PE01185750716',
    		'txtTime'	=> $time,
    		'random'	=> $round,
    		'signType'	=> 'MD5',
    		// 'transNo'	=> '1051410594120200713',
    		'transNo'	=> $order,
    		'merchantId'	=> '1344296701',
    		'invoiceAmt'	=> $money,
    	];
    	$signData = [
    		'orgNo'	=> '2100',
    		'termNo'	=> 'PE01185750716',
    		'txtTime'	=> $time,
    		'merchantId'	=> '1344296701',
    		'random'	=> $round,
    	];
    	$sign = $this->GetSign($signData);
    	$temp['signValue'] = $sign;
    	// $url = 'http://zfyun.com.cn:8080/YinHeLoan/yinHe/invoiceQrCode.action';
    	$url = 'http://yhyr.com.cn/YinHeLoan/yinHe/invoiceQrCode.action';
    	$res = $this->sendpostss($url,$temp);
    	if ($res['returnCode'] == 114) {
    		return $this->reply(0,'ok',['url'=>$res['qrUrl']]);
    	} else {
    		return $this->reply(1,'打印电子发票失败，请稍后再试！');
    	}
    }

    #小票异步回调.
    public function payinvoiceNotify()
    {

    	$data = input();
    	if (empty($data)) {
    		return reply(1,'非法操作');
    	}

    	$where = [
    		'transNo'	=> $data['transNo']
    	];
    	$res = model('Orders')->where($where)->setField(['invoice_status'=>'3']);

    	if ($res) {
    		return $this->reply(0,'成功');
    	} else {
    		return $this->reply(1,'失败');
    	}
    }

    // 生成电子小票sign
    function GetSign($data) {
    	header('Content-type: text/html; charset=utf-8');
    	// $secret = "77c21827d4725764696718349e5044d6";
    	$secret = "77c21827d4725764696718349e5044d6";
        // 对数组的值按key排序
        ksort($data);
        // 生成url的形式
        $param = http_build_query($data);
        $str = urldecode($param); //解码
        $params = $str.$secret;
        // 生成sign
        $sign = md5($params);
        return $sign;
    }

    /**
     * 发送post formdata请求
     */
    public function sendpostss($url,array $data)
    {
        $data = @json_encode($data);

        $headers = [
            'Content-Type: application/json;charset=utf-8',
            'Content-Length: ' . strlen($data)
        ];


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
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

    /**
     * Notes: 更新公众号token（90分钟一刷新）
     * Class: updateToken
     * user: bingwoo
     * date: 2020/8/25 10:53
     */
    public function updateGztoken(){
        $configData = model('system')->find()->toArray();
        $appid = $configData['gz_appid'];
        $secret = $configData['gz_appsecret'];
        //获取token
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $ret = request_get($url);
        if($ret == false){
            /* @todo 添加错误日志*/
            exit();
        }
        $ret = @json_decode($ret,true);
        $where = ['system_id'=>1];
        $savaData = [
            'gz_token' => $ret['access_token']
        ];
        $reSave = model('system')->save($savaData,$where);
        if($reSave == false){
            echo "token刷新失败";
            /* @todo 添加错误日志*/
        }
        echo "token刷新成功";
        exit();
	}
	
	public function getInfoData()
	{
		$data = input();
		file_put_contents('./a3.log',\json_encode($data));
		if($_SERVER['REQUEST_METHOD'] == 'GET'){

			$token = 'M16kxQCL9KGNyOU5';
			$array = array( $token, $data['timestamp'], $data['nonce']);
			sort($array, SORT_STRING);
			$str = implode($array);
			$ress = sha1($str);
			if( $ress == $data['signature'] ){
				echo $data['echostr'];exit;
			}else{
				return null;
			}
			exit;
		} else {
			$url = 'https://possji.com:8088/yinheorder/wxpublic/verifytoken';
			$this->sendpostss($url,$data);
			exit;
		}
	}

}