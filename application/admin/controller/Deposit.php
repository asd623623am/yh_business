<?php
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;
use think\Db;

/**
 * 押金管理.
 */
class Deposit extends Common
{
	public function depositAdd()
	{
		 $model = model('Orders');
        if( request() -> isPost() ){
            $insert = request() -> param();
            $home = $insert['home_id'];

            $where = [
            	'home_id'	=> $insert['home_id'],
            	'is_delete'	=> 1
            ];
            $data = model('Home')->where($where)->find();
            if ($data == null) {
            	fail('没有找到您的房子');
            }
            $res = explode('#', $home);
            $insert['home_id'] = $res[0];
            $insert['home_name'] = $res[1];
            $order_no = date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT);
            $insert['pay_status'] = 0;
            $insert['type'] = 2;
            $insert['status'] = 1;
            $insert['ctime'] = time();
           	$insert['order_no'] = $order_no;
           	$insert['district_id']	= $data['district_id'];
           	$insert['district_name'] = $data['district_name'];
           	$insert['complex']	= $data['complex'];
           	$insert['home_code']	= $data['home_code'];
            if( $model -> insert( $insert ) ){
            	 $this -> addLog('发布了一个新的押金账单');
                win('添加成功');
            }else{
                $this -> fail('添加失败');
            }
        }else{
        
            return view();
        }
	}


	public function depositHome()
	{
		$user_name = input('user_name');
		if (empty($user_name)) {
			fail('非法操作页面');
		}
		$where = [
			'owner'	=> $user_name
		];
		//查看管理员有物业还是供暖权限
		$user = session('admin');
		if ($user['admin_type'] != 1) {
			$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
			$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
		

			$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
			$d_id = [];
			if (!empty($ddata)) {
				foreach ($ddata as $k => $v) {
					$d_id[] = $v['d_id'];
				}

				$where['district_id'] = array('in',$d_id);
			}
		}

		$temp = [];
		$newtemp = [];
		$home = model('Home')->where($where)->select()->toArray();
		if (!empty($home)) {
			foreach ($home as $key => $value) {

				$district = model('District')->where(['id'=>$value['district_id']])->find();
				if ($district != null) {
					$home[$key]['district_name'] = $district->name;
				} else {
					$home[$key]['district_name'] = '';
				}
			}
			foreach ($home as $keys => $values) {
				$newtemp[] = [
					'home_id'	=> $values['home_id'],
					'home_name'	=> $values['district_name'].$values['complex'].$values['home_code']
				];
			}
		}
		
		
		$info=['code'=>0,'msg'=>'','data'=>$newtemp];
		echo json_encode($info);exit;

		$user = model('User')->where(['user_name'=>$user_name])->find();
		if ($user!=null) {
			$where = [
				'user_id'=>$user['user_id'],
				'status'	=>1
			];
			$users = model('Userhouse')->where($where)->select()->toArray();
			$home_id = [];
			foreach ($users as $key => $value) {
				$home_id[] = $value['house_id'];
			}
			$house_obj = model('Home');
			$temp = [];
			foreach ($home_id as $k => $val) {
				$hwhere = [
					'home_id'	=> $val,
					'is_delete'	=> 1,
				];
				$home = model('Home')->where($hwhere)->find();
				if ($home != null) {
					$district = model('District')->where(['id'=>$home->district_id])->find();
					if ($district != null) {
						$home->district_name = $district->name;
						$temp[] = $home;
					} else {
						$home->district_name = '';
						$temp[] = $home;						
					}
					
				}
	
			}
			$newtemp = [];
			foreach ($temp as $keys => $values) {
				$newtemp[] = [
					'home_id'	=> $values->home_id,
					'home_name'	=> $values->district_name.$values->complex.$values->home_code
				];
			}
			$info=['code'=>0,'msg'=>'','data'=>$newtemp];
			echo json_encode($info);exit;
		}
		
			
		
	}

	public function depositList()
	{

		$where = [];

		//查看管理员有物业还是供暖权限
		$user = session('admin');
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


		$district=Db::table("district")->field("id,name")->where($where)->order('id','asc')->select();
	    	$this->assign('districts',$district);
		return view();
	}

	public function depositInfo()
	{
		$p = input('get.page');
		if (empty($p)) {
		    exit('非法操作此页面');
		}
		$page_num = input('get.limit');
		if (empty($page_num)) {
		    exit('非法操作此页面');
		}

		

		//查看管理员有物业还是供暖权限
		$user = session('admin');
		if ($user['admin_type'] != 1) {
			$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
			$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
			$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
			$d_id = [];
			if (!empty($ddata)) {
				foreach ($ddata as $k => $v) {
					$d_id[] = $v['d_id'];
				}

				$where['district_id'] = array('in',$d_id);
			}

			$where['pay_status'] = 0;
			$where['status']	= 1;
			$where['type']	= 2;
		} else {
			$where = [
				'type'	=> 2,
				'pay_status'	=> 0,
				'status'	=> 1
			];
		}




		$data = model('Orders')->page($p, $page_num)->where($where)->select()->toArray();
		foreach ($data as $k => $v) {
			if ($v['pay_status'] == 0) {
				$data[$k]['pay_status'] = '未缴费';
			} else if ($v['pay_status'] == 1) {
				$data[$k]['pay_status'] = '已缴费';
			} else {
				$data[$k]['pay_status'] = '已退款';
			}

			if (!empty($v['pay_type'])) {
				if ($v['pay_type'] == 3) {
					$data[$k]['pay_type'] = '线下支付';
				} else if ($v['pay_type'] == 2) {
					$data[$k]['pay_type'] = 'pos机支付';
				} else {
					$data[$k]['pay_type'] = '小程序支付';
				}
			}
		}
		// foreach ($admin_info as $key => $value) {

		// 	#正常会去找房屋名称
		// 	$admin_info[$key]['home_id'] = $value['home_id'];
		// 	$admin_info[$key]['home_name'] = $value['home_name'];
		// 	if ($value['deposit_status'] == 0) {
		// 		$admin_info[$key]['deposit_status'] = '未缴费';
		// 	} else if ($value['deposit_status'] == 1) {
		// 		$admin_info[$key]['deposit_status'] = '已缴费';
		// 	} else {
		// 		$admin_info[$key]['deposit_status'] = '已退款';
		// 	}

		// 	if ($value['deposit_url'] == 0) {
		// 		$admin_info[$key]['deposit_url'] = '未缴费';
		// 	} else if ($value['deposit_url'] == 1) {
		// 		$admin_info[$key]['deposit_url'] = '线上缴费';
		// 	} else if ($value['deposit_url'] == 2) {
		// 		$admin_info[$key]['deposit_url'] = 'pos机缴费';
		// 	} else {
		// 		$admin_info[$key]['deposit_url'] = '现金缴费';
		// 	}


		// }

		
		$count = model('Orders')->where($where)->count();
		$info = ['code' => 0, 'msg' => '', 'count' => $count, 'data' => $data];
		echo json_encode($info);
	}


	public function depositNewList()
	{
		$p = input('get.page');

		$data = input();
	    if (empty($data)) {
	        fail('非法操作此页面');
	    }
	    $p = $data['page'];
	    unset($data['page']);
	    $page_num = $data['limit'];
	    unset($data['limit']);

	    
	    if (empty($data['district_id'])) {
	    	unset($data['district_id']);
	    }
	    if (empty($data['home_code'])) {
	    	unset($data['home_code']);
	    }
	    if (empty($data['user_name'])) {
	    	unset($data['user_name']);
	    }
	    if (empty($data['complex'])) {
	    	unset($data['complex']);
	    }

	    if (empty($data['order_no'])) {
	    	unset($data['order_no']);
	    }



	    //查看管理员有物业还是供暖权限
	    $user = session('admin');
	    if ($user['admin_type'] != 1) {
	    	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
	    	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
	    	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
	    	$d_id = [];
	    	if (!empty($ddata)) {
	    		foreach ($ddata as $k => $v) {
	    			$d_id[] = $v['d_id'];
	    		}

	    		$data['district_id'] = array('in',$d_id);
	    	}
	    	$data['type'] = 2;
	    	$data['status'] = 1;
	    	$data['pay_status'] = 0;
	    } else {
	    	$data['type'] = 2;
	    	$data['status'] = 1;
	    	$data['pay_status'] = 0;
	    }

		$data['type'] = 2;
		$data['status'] = 1;
		$data['pay_status'] = 0;
		$datas = model('Orders')->page($p, $page_num)->where($data)->select()->toArray();
		foreach ($datas as $k => $v) {
			if ($v['pay_status'] == 0) {
				$datas[$k]['pay_status'] = '未缴费';
			} else if ($v['pay_status'] == 1) {
				$datas[$k]['pay_status'] = '已缴费';
			} else {
				$datas[$k]['pay_status'] = '已退款';
			}

			if (!empty($v['pay_type'])) {
				if ($v['pay_type'] == 3) {
					$datas[$k]['pay_type'] = '线下支付';
				} else if ($v['pay_type'] == 2) {
					$datas[$k]['pay_type'] = 'pos机支付';
				} else {
					$datas[$k]['pay_type'] = '小程序支付';
				}
			}
		}

		$count = model('Orders')->where($data)->count();
		$info = ['code' => 0, 'msg' => '', 'count' => $count, 'data' => $datas];
		echo json_encode($info);
	}


	public function refundList()
	{


		if( request() -> isAjax() ){

		    $p = input('get.page');
		    if (empty($p)) {
		        exit('非法操作此页面');
		    }
		    $page_num = input('get.limit');
		    if (empty($page_num)) {
		        exit('非法操作此页面');
		    }


		    //查看管理员有物业还是供暖权限
		    $user = session('admin');
		    if ($user['admin_type'] != 1) {
		    	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
		    	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
		    	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
		    	$d_id = [];
		    	if (!empty($ddata)) {
		    		foreach ($ddata as $k => $v) {
		    			$d_id[] = $v['d_id'];
		    		}

		    		$where['district_id'] = array('in',$d_id);
		    	}

		    	$where['pay_status'] = 1;
		    	$where['status']	= 1;
		    	$where['type']	= 2;
		    } else {
		    	$where = [
		    		'type'	=> 2,
		    		'pay_status'	=> 1,
		    		'status'	=> 1
		    	];
		    }
		    $data = model('Orders')->page($p, $page_num)->where($where)->select()->toArray();
		    foreach ($data as $k => $v) {
		    	if ($v['pay_status'] == 0) {
		    		$data[$k]['pay_status'] = '未缴费';
		    	} else if ($v['pay_status'] == 1) {
		    		$data[$k]['pay_status'] = '已缴费';
		    	} else {
		    		$data[$k]['pay_status'] = '已退款';
		    	}

		    	if (!empty($v['pay_type'])) {
		    		if ($v['pay_type'] == 3) {
		    			$data[$k]['pay_type'] = '线下支付';
		    		} else if ($v['pay_type'] == 2) {

		    			if ($v['payTypes'] == 0) {
	       					$data[$k]['pay_type'] = '订单支付 -- 刷卡支付';	
	       				} else if ($v['payTypes'] == 1) {
	       					$data[$k]['pay_type'] = '订单支付 -- 微信支付';
	       				} else if ($v['payTypes'] == 2) {
	       					$data[$k]['pay_type'] = '订单支付 -- 支付宝支付';
	       				} else if ($v['payTypes'] == 3) {
	       					$data[$k]['pay_type'] = '订单支付 -- 现金支付';
	       				} else if ($v['payTypes'] == 4) {
	       					$data[$k]['pay_type'] = '订单支付 -- 云闪付支付';
	       				}
		    		} else {
		    			$data[$k]['pay_type'] = '小程序支付';
		    		}
		    	}
		    }
		    // foreach ($admin_info as $key => $value) {

		    // 	#正常会去找房屋名称
		    // 	$admin_info[$key]['home_id'] = $value['home_id'];
		    // 	$admin_info[$key]['home_name'] = $value['home_name'];
		    // 	if ($value['deposit_status'] == 0) {
		    // 		$admin_info[$key]['deposit_status'] = '未缴费';
		    // 	} else if ($value['deposit_status'] == 1) {
		    // 		$admin_info[$key]['deposit_status'] = '已缴费';
		    // 	} else {
		    // 		$admin_info[$key]['deposit_status'] = '已退款';
		    // 	}

		    // 	if ($value['deposit_url'] == 0) {
		    // 		$admin_info[$key]['deposit_url'] = '未缴费';
		    // 	} else if ($value['deposit_url'] == 1) {
		    // 		$admin_info[$key]['deposit_url'] = '线上缴费';
		    // 	} else if ($value['deposit_url'] == 2) {
		    // 		$admin_info[$key]['deposit_url'] = 'pos机缴费';
		    // 	} else {
		    // 		$admin_info[$key]['deposit_url'] = '现金缴费';
		    // 	}


		    // }
		    $count = model('Orders')->where($where)->count();
		    $info = ['code' => 0, 'msg' => '', 'count' => $count, 'data' => $data];
		    echo json_encode($info);exit;

		}else{




				$where = [];

				//查看管理员有物业还是供暖权限
				$user = session('admin');
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

            	$district=Db::table("district")->field("id,name")->where($where)->order('id','asc')->select();
            	$this->assign('districts',$district);
            	return view();
		}


		
	}


	public function refundNewList()
	{
		$p = input('get.page');

		$data = input();
	    if (empty($data)) {
	        fail('非法操作此页面');
	    }
	    $p = $data['page'];
	    unset($data['page']);
	    $page_num = $data['limit'];
	    unset($data['limit']);

	    
	    if (empty($data['district_id'])) {
	    	unset($data['district_id']);
	    }
	    if (empty($data['home_code'])) {
	    	unset($data['home_code']);
	    }
	    if (empty($data['user_name'])) {
	    	unset($data['user_name']);
	    }
	    if (empty($data['complex'])) {
	    	unset($data['complex']);
	    }
	    if (empty($data['order_no'])) {
	    	unset($data['order_no']);
	    }

	    //查看管理员有物业还是供暖权限
	    $user = session('admin');
	    if ($user['admin_type'] != 1) {
	    	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
	    	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
	    	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
	    	$d_id = [];
	    	if (!empty($ddata)) {
	    		foreach ($ddata as $k => $v) {
	    			$d_id[] = $v['d_id'];
	    		}

	    		$data['district_id'] = array('in',$d_id);
	    	}
	    }


		$data['type'] = 2;
		$data['status'] = 1;
		$datas = model('Orders')->page($p, $page_num)->where($data)->select()->toArray();
		foreach ($datas as $k => $v) {
			if ($v['pay_status'] == 0) {
				$datas[$k]['pay_status'] = '未缴费';
			} else if ($v['pay_status'] == 1) {
				$datas[$k]['pay_status'] = '已缴费';
			} else {
				$datas[$k]['pay_status'] = '已退款';
			}

			if (!empty($v['pay_type'])) {
				if ($v['pay_type'] == 3) {
					$datas[$k]['pay_type'] = '线下支付';
				} else if ($v['pay_type'] == 2) {



					if ($v['payTypes'] == 0) {
	       					$datas[$k]['pay_type'] = 'pos机支付 -- 刷卡支付';	
	       				} else if ($v['payTypes'] == 1) {
	       					$datas[$k]['pay_type'] = 'pos机支付 -- 微信支付';
	       				} else if ($v['payTypes'] == 2) {
	       					$datas[$k]['pay_type'] = 'pos机支付 -- 支付宝支付';
	       				} else if ($v['payTypes'] == 3) {
	       					$datas[$k]['pay_type'] = 'pos机支付 -- 现金支付';
	       				} else if ($v['payTypes'] == 4) {
	       					$datas[$k]['pay_type'] = 'pos机支付 -- 云闪付支付';
	       				}

				} else {
					$datas[$k]['pay_type'] = '小程序支付';
				}
			}
		}

		$count = model('Orders')->where($data)->count();
		$info = ['code' => 0, 'msg' => '', 'count' => $count, 'data' => $datas];
		echo json_encode($info);
	}
	public function depositDel()
	{
		$deposit_id=input('post.deposit_id');
		if(empty($deposit_id)){
		    fail('非法操作此页面');
		}
		$where=[
		    'id'=>$deposit_id
		];
		//删除
		$arr=[
		    'status'=>2
		];
		
		$info = model('Orders')->where($where)->setField($arr);
		if($info){
			 $this -> addLog('删除了一个押金账单');
		    win('删除成功');
		}else{
		    fail('删除失败');
		}
	}


	public function depositUpdateInfo()
	{
	    //接受id
	    $id = input('get.id');
	    if (empty($id)) {
	        fail('非法操作此页面');
	    }
	    $where = [
	        'id' => $id
	    ];
	    //查询修改的数据
	    $data = model('Orders')->where($where)->find()->toArray();
	    if (empty($data['pay_status'])) {
	    	$data['is_show'] = 1;
	    } else {
	    	if ($data['pay_status'] != 0) {
	    		$data['is_show'] = 0;
	    	} else {
	    		$data['is_show'] = 1;
	    	}
	    }

	    $where = [
	    	'owner'	=> $data['user_name']
	    ];
	    //查看管理员有物业还是供暖权限
	    $user = session('admin');
	    if ($user['admin_type'] != 1) {
	    	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
	    	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
	    

	    	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
	    	$d_id = [];
	    	if (!empty($ddata)) {
	    		foreach ($ddata as $k => $v) {
	    			$d_id[] = $v['d_id'];
	    		}

	    		$where['district_id'] = array('in',$d_id);
	    	}
	    }

	    $temp = [];
	    $newtemp = [];
	    $home = model('Home')->where($where)->select()->toArray();
	    if (!empty($home)) {
	    	foreach ($home as $key => $value) {

	    		$district = model('District')->where(['id'=>$value['district_id']])->find();
	    		if ($district != null) {
	    			$home[$key]['district_name'] = $district->name;
	    		} else {
	    			$home[$key]['district_name'] = '';
	    		}
	    	}
	    	foreach ($home as $keys => $values) {
	    		$newtemp[] = [
	    			'home_id'	=> $values['home_id'],
	    			'home_name'	=> $values['district_name'].$values['complex'].$values['home_code']
	    		];
	    	}
		}
		
		$page = input('get.page');
		if(empty($page)){
			exit('非法操作');
		}
		$this->assign('page',$page);
	    $this->assign('selects', $newtemp);

	    $this->assign('data', $data);
	    return $this->fetch();
	    // if ($data['pay_status'] == 0 ) {
	    // 	$data['is_show'] = 1; //1是可以修改支付状态的
	    // } else {
	    // 	$data['is_show'] = 0; //0是什么都不可以修改的
	    // }


	    $user = model('User')->where(['user_name'=>$data['user_name']])->find();
	    if ($user!=null) {
	    	$where = [
	    		'user_id'=>$user['user_id'],
	    		'status'	=>1
	    	];
	    	$users = model('Userhouse')->where($where)->select()->toArray();
	    	$home_id = [];
	    	foreach ($users as $key => $value) {
	    		$home_id[] = $value['house_id'];
	    	}
	    	$house_obj = model('Home');
	    	$temp = [];
	    	foreach ($home_id as $k => $val) {
	    		$hwhere = [
	    			'home_id'	=> $val,
	    			'is_delete'	=> 1,
	    		];
	    		$home = model('Home')->where($hwhere)->find();
	    		if ($home != null) {
	    			$district = model('District')->where(['id'=>$home->district_id])->find();
	    			if ($district != null) {
	    				$home->district_name = $district->name;
	    				$temp[] = $home;
	    			} else {
	    				$home->district_name = '';
	    				$temp[] = $home;						
	    			}
	    			
	    		}
	    	
	    	}
	    	$newtemp = [];
	    	foreach ($temp as $keys => $values) {
	    		$newtemp[] = [
	    			'home_id'	=> $values->home_id,
	    			'home_name'	=> $values->district_name.$values->complex.$values->home_code
	    		];
	    	}
	    	$this->assign('selects', $newtemp);
	    } else {
	    	echo '没有找到这个用户';exit;
	    }
	    $this->assign('data', $data);
	    return $this->fetch();
	}

	public function depositUp()
	{
		$data = input('post.');
		if (empty($data)) {
			fail('非法操作此页面');
		}
		$newData = [];
		$temp = explode('#', $data['home_id']);
		$where = [
			'home_id'	=> $temp[0],
			'is_delete'	=> 1
		];
		$res = model('Home')->where($where)->find();
		if ($res == null) {
			fail('没有找到您的房子');
		}

		if (isset($data['is_show'])) {	
			if ($data['is_show'] == 1) {
				$newData = [
					'user_name'	=> $data['user_name'],
					'home_id'	=> $temp[0],
					'home_name'	=> $temp[1],
					'title'		=> $data['title'],
					'fee'	=> $data['fee'],
					'refund_money'	=> $data['refund_money'],
					'pay_status'	=> 1,
					'pay_type' => 3,
					'msg'			=> $data['msg'],
					'district_id'			=> $res['district_id'],
					'district_name'			=> $res['district_name'],
					'complex'			=> $res['complex'],
					'home_code'			=> $res['home_code'],
				];
				
			} else {
				$newData = [
					'user_name'	=> $data['user_name'],
					'home_id'	=> $temp[0],
					'home_name'	=> $temp[1],
					'title'		=> $data['title'],
					'fee'	=> $data['fee'],
					'refund_money'	=> $data['refund_money'],
					'msg'			=> $data['msg'],
					'district_id'			=> $res['district_id'],
					'district_name'			=> $res['district_name'],
					'complex'			=> $res['complex'],
					'home_code'			=> $res['home_code'],
				];
			}


		} else {
			$newData = [
				'user_name'	=> $data['user_name'],
				'home_id'	=> $temp[0],
				'home_name'	=> $temp[1],
				'title'		=> $data['title'],
				'fee'	=> $data['fee'],
				'refund_money'	=> $data['refund_money'],
				'msg'			=> $data['msg'],
				'district_id'			=> $res['district_id'],
				'district_name'			=> $res['district_name'],
				'complex'			=> $res['complex'],
				'home_code'			=> $res['home_code'],
			];
		}
		
		$where = [
			'id'	=> $data['id']
		];
		$info = model('Orders')->where($where)->setField($newData);
		if($info){
			 $this -> addLog('修改了一个押金账单');
		    win('修改成功');
		}else{
		    fail('修改失败');
		}

	}

	public function depositUps()
	{
		$data = input();
		if (empty($data)) {
			fail('非法操作此页面');
		}
		$where = [
		    'id' => $data['id']
		];
		if ($data['status'] == 1) {
			//查询修改的数据
			$data = model('Orders')->where($where)->find()->toArray();
			if ($data['pay_status'] != 0) {
				return json(['font'=>'您已经缴过费了','code'=>3]);
			} else {
				return json(['font'=>'可以添加','code'=>1]);
			}
		} else {
			$update = [
				'order_no'			=> date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT),
				'pay_status'		=> 1,
				'pay_type'			=> 3,
				'pay_fee'			=> $data['name'],
				'finish_at'			=> time(),
			];

			$orderInfo = model('Orders')->where($where)->setField($update);
			if ($orderInfo) {
				 $this -> addLog('线下支付了一个订单');
				win('支付成功');
			} else {
				fail('支付失败');
			}
	}
}
	public function phpqrcode()
	{
		$id = input('id');
		if (empty($id)) {
			fail('非法操作此页面');
		}
		$where = [
		    'id' => $id
		];
		//查询修改的数据
		$data = model('Orders')->where($where)->find()->toArray();
		if ($data['pay_status'] != 0) {
			return json(['font'=>'您已经缴过费了','code'=>3]);
		}

		$data['fee']=str_replace('.', '', $data['fee']);
		$lens = strlen($data['fee']);
		// $data['deposit_money']=sprintf("%012d", $data);//生成12位数，不足前面补0   
		if ($lens < 12) {
			for ($i=0; $i < 12-$lens ; $i++) { 
				$data['fee'] = substr_replace(0, $data['fee'], 1, 0);
			}
		}

		$newData = [
			'order_no'	=> $data['order_no'],
			'deposit_money'	=> $data['fee']
		];
		 $scope =  implode(',',$newData);
		vendor("phpqrcode.phpqrcode");
		        
		        $level = 'L';// 容错级别：L、M、Q、H
		        $size = 4;
		        $qrcode = new \QRcode();
		        ob_start();
		        $qrcode->png($scope,false,$level,$size,2);

		        $imageString = base64_encode(ob_get_contents());
		        ob_end_clean();
		        if (empty($imageString)) {
		        	fail('支付失败');
		        }

		         $font = ['img'=>$imageString,'order_no'=>$data['order_no']];
				 echo json_encode(['font' => $font, 'code' => 1]);
		         // "<img src='data:image/png;base64,{$imageString}'  />";
    }


   


    public function depositRefund()
    {
    	$id = input('id');
    	if (empty($id)) {
    		fail('非法操作此页面');
    	}
    	$where = [
    	    'id' => $id
    	];
    	//查询修改的数据
    	$data = model('Orders')->where($where)->find()->toArray();
    	

    	if ($data['pay_status'] == 0) {
    		return json(['font'=>'您还没有缴费','code'=>3]);
    	} else if ($data['pay_status'] == 2) {
    		return json(['font'=>'您已经退过款了','code'=>1]);
    	}
    	
    	if ($data['pay_type'] == 3) {
    		$where = [
    		    'id' => $id
    		];
    		$newData = [
    			'pay_status'	=> 2
    		];
    		$info = model('Orders')->where($where)->setField($newData);
    		if ($info) {
    			win('退款成功,您这是线下退款');		
    		} else {
    			fail('退款失败');
    		}
    	} else if ($data['pay_type'] == 2) {

    		if ($data['payTypes'] == 0 || $data['payTypes'] == 4) {
    			fail('暂时不支持此方式退款，请去pos上操作！');
    		}

    		# pos机退款.
    		$pay_fee = $this->sprintfs($data['pay_fee']);
			$pay_fee = str_replace('.', '', $pay_fee);
    		$len = strlen($pay_fee);
    		if ($len < 12) {
    			for ($i=0; $i < 12-$len ; $i++) {
    				$pay_fee = substr_replace(0, $pay_fee, 1, 0);
    			}
    		}

    		$refund_money = $this->sprintfs($data['refund_money']);
			$refund_money = str_replace('.', '', $refund_money);
    		$lens = strlen($refund_money);
    		if ($lens < 12) {
    			for ($i=0; $i < 12-$lens ; $i++) {
    				$refund_money = substr_replace(0, $refund_money, 1, 0);
    			}
    		}
    		
    		$order_no_r = $data['transNo'].rand(1111,9999);
    		$newtemp = [
    			'orgNo'	=> 2100,
    			'charset'	=> 'UTF-8',
    			'termNo'	=> 'XA026454',
    			'termType'	=> 'QR',
    			'txtTime'	=> date('YmdHis',time()),
    			// 'txtTime'	=> '20200604183601',
    			// 'random'	=> 7344,
    			'random'	=> rand(1111,9999),
    			'signType'	=> 'MD5',
    			'signValue'	=> '', //签名
    			'transNo'	=> $data['transNo'],
    			'merchantId'	=> '1344296701',
    			// 'amt'		=> $data['pay_fee'],
    			'amt'		=> $pay_fee,
    			// 'refundAmt'	=> $data['refund_money'],
    			'refundAmt'	=> $refund_money,
    			// 'refundAmt'	=> '000000000001',
    			'payType'	=> $data['payTypes'],
    			'refundOrderNo'	=> $order_no_r,
    		];
    		$signWhere = [
    			'orgNo'	=> $newtemp['orgNo'],
    			'termNo'	=> $newtemp['termNo'],
    			'txtTime'	=> $newtemp['txtTime'],
    			'merchantId'	=> $newtemp['merchantId'],
    			'random'	=> $newtemp['random']
    		];
    		$signData=$this->getSign($signWhere);
    		$newtemp['signValue'] = $signData;
    		// $url = 'http://192.168.0.66:8080/YinHeLoan/yinHe/refundOnlineWaOrder.action';
    		$url = 'http://yhyr.com.cn/YinHeLoan/yinHe/refundOnlineWaOrder.action';
    		$res = $this->sendpostss($url,$newtemp);
    		if ($res['returnCode'] == 113) {
    			$where = [
    			    'id' => $id
    			];
    			$newData = [
    				'pay_status'	=> 2,
    				'refundOrderNo'		=> $order_no_r,
    			];
    			$infos = model('Orders')->where($where)->setField($newData);
    			if ($infos) {
    				win('退款成功');		
    			} else {
    				fail('退款失败2');
    			}
    		} else {
    			fail($res['returnMsg']);
    		}
    		
    	} else if ($data['pay_type'] == 1) {
    		
    		$app = model('Applets')->select()->toArray();
    		// if ($data['type'] == 1) {
    		// 	$secret = $app[1]['secret'];
    		// 	$termNo = $app[1]['termNo'];
    		// 	$merId = $app[1]['merId'];
    		// } else {
    			$secret = $app[0]['secret'];
    			$termNo = $app[0]['termNo'];
    			$merId = $app[0]['merId'];
    		// }
    		
    		if (!empty($app)) {
    			$data['refund_money']=str_replace('.', '', $data['refund_money']);
    			$lens = strlen($data['refund_money']);
    			// $data['deposit_money']=sprintf("%012d", $data);//生成12位数，不足前面补0   
    			if ($lens < 12) {
    				for ($i=0; $i < 12-$lens ; $i++) { 
    					$data['refund_money'] = substr_replace(0, $data['refund_money'], 1, 0);
    				}
    			}
    			$time = date('YmdHis',time());
    			$arr = [
    				'orgNo'	=> '2166',
    				'charset'	=> 'UTF-8',
    				'termNo'	=> $termNo,
    				'termType'	=> 'KCWMP',
    				'txtTime'	=> $time,
    				'signType'	=> 'MD5',
    				
    				'transNo'	=> $data['order_no'],
    				'merId'		=> $merId,
    				'amt'		=> intval($data['refund_money']),
    				'payType'	=> 1
    			];


    			$signdata = [
    				'orgNo'	=>'2166',
    				'amt'	=> intval($data['refund_money']),
    				'termNo'=> $termNo,
    				'merId'	=> $merId,
    				'transNo'	=> $data['order_no'],
    				'txtTime'	=> $time
    			];
    			$sign = $this->appgetSign($signdata,$secret);
    			$arr['signValue']=strtoupper($sign);
    			$url = "http://yhyr.com.cn/YinHeLoan/yinHe/refundWmpPay.action";
    			$res = $this->sendpostss($url,$arr);
    			if ($res['returnCode'] == 0000) {
    				$where = [
    				    'id' => $id
    				];
    				$newData = [
    					'pay_status'	=> 2,
    				];
    				$infos = model('Orders')->where($where)->setField($newData);
    				if ($infos) {
    					win('退款成功');		
    				} else {
    					fail('退款失败');
    				}
    			} else {
    				fail($res['returnMsg']);
    			}
    		} else {
    			fail('请您去配置微信小程序参数');
    		}


    	}


    	
    }
	
    public function appgetSign($data,$secret)
    {
    	header('Content-type: text/html; charset=utf-8');
    	
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

	
    // 获取sign
    function getSign($data) {
    	header('Content-type: text/html; charset=utf-8');
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
	 * 打印小票.
	 * @return [type] [description]
	 */
	public function payToken()
	{
		$data = input();
		if (empty($data)) {
			fail('非法操作此页面');
		}

		$where = [
			'id'	=> $data['id']
		];

		$info = model('Orders')->where($where)->find();
		if ($info == null) {
			fail('没有查到您的数据');
		}
		if ($info['pay_status'] == 0) {
			fail('您的订单不是已缴费订单！');
		}
		$home = model('Home')->where(['home_id'=>$info['home_id']])->find();

		if ($home == null) {
			fail('没有查到您的数据');
		}

		if ($info['pay_type'] == 1) {
			$pay_type = '小程序支付';
		} else {

			if ($info['payTypes'] == 0) {
				$pay_type = '订单支付 -- 刷卡支付';	
			} else if ($info['payTypes'] == 1) {
				$pay_type = '订单支付 -- 微信支付';
			} else if ($info['payTypes'] == 2) {
				$pay_type = '订单支付 -- 支付宝支付';
			} else if ($info['payTypes'] == 3) {
				$pay_type = '订单支付 -- 现金支付';
			} else if ($info['payTypes'] == 4) {
				$pay_type = '订单支付 -- 云闪付支付';
			}
		}

		// $payment = model('Payment')->where(['p_id'=>$info['p_id']])->find();
		// if ($payment == null) {
		// 	$title = '';
		// } else {
		// 	$title = $payment['content'];
		// }
		// $DEVICE_NO = 'kdt1078615';
		// $key = '72264';
		// $content = "    .... #小码旺铺订单# ....\n";

		// $content .= "\n";
		// $content .= "\n";
		
		// $content .= "^H2      北京延龙物业收据凭证\n";

		// $content .= "\n";
		// $content .= "\n";
		// $content .= "\n";
		
		// $content .= "订单编号：".$info['order_no']."\n";
		// $content .= "\n";
		// $content .= "--------------------------------\n";
		// $content .= "\n";
		// $content .= "^H1房屋信息：".$home['district_name']." ".$home['complex']."区\n";
		// $content .= "^H1         ".$home['home_code']."\n";
		// $content .= "^H1联系方式：".$home['tel']."\n";
		// $content .= "^H1收费类型：装修押金\n";
		// $content .= "^H1支付方式：".$pay_type."\n";
		// $content .= "^H1可退金额：".$info['refund_money']."\n";
		// $content .= "\n";
		// $content .= "\n";
		// $content .= "\n";
		// $content .= "^H1收费明细：".$info['cost_info']."\n";
		// $content .= "\n";
		// $content .= "\n";
		// $content .= "\n";
		// $content .= "^H1备注：".$info['title']."\n";
		// $content .= "\n";
		// $content .= "\n";
		// $content .= "支付时间：".date('Y.m.d H:i:s',$info['finish_at'])."\n";
		// $content .= "--------------------------------\n";
		// $content .= "\n";
		// $content .= "^H3                        缴费金额";
		// $content .= "^H3                        ".$info['pay_fee']."";
		// $content .="\n";
		// $content .="\n";
		// $content .="\n";
		// $content .="\n";
		// $content .= "...#由小码旺铺提供技术支持#...\n";



		$admin = session('admin');
		$str = model('System')->find();
		if ($str == null) {
			$title = '';
		} else {
			$title = $str['company_name'];
		}
		$content = "    .... #小码旺铺订单# ....\n";

		$content .= "\n";
		$content .= "\n";
		
		$content .= "^B2    收据凭证\n";
		$content .= "\n";
		$content .= "物业存根             请妥善保管\n";
		$content .= "--------------------------------\n";
		$content .= "商户名称：".$title."\n";
		$content .= "商户编号：2020070701\n";
		$content .= "终端编号：\n";
		$content .= "操作员：".$admin['admin_name']."\n";
		$content .= "--------------------------------\n";
		$content .= "订单编号：".$info['order_no']."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
		$content .= "^H2缴费类型：押金\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "支付时间：".date('Y.m.d H:i:s',$info['finish_at'])."\n";
		$content .= "支付方式：".$pay_type."\n";
		
		$content .= "--------------------------------\n";
		
		// $content .= "^H2联系方式：".$home['tel']."\n";
		
		$content .= "^H1收费明细：".$info['cost_info']."\n";
		$content .= "--------------------------------\n";
		$content .= "^H1可退金额：".$info['refund_money']."\n";
		$content .= "--------------------------------\n";
		$content .= "^H1备注：".$info['title']."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "^H2交易金额:".$info['pay_fee'];
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
		$content .= "商户名称：".$title."\n";
		$content .= "商户编号：2020070701\n";
		$content .= "终端编号：\n";
		$content .= "操作员：".$admin['admin_name']."\n";
		$content .= "--------------------------------\n";
		$content .= "订单编号：".$info['order_no']."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
		$content .= "^H2收费类型：押金\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "支付时间：".date('Y.m.d H:i:s',$info['finish_at'])."\n";
		$content .= "支付方式：".$pay_type."\n";
		
		$content .= "--------------------------------\n";
		
		// $content .= "^H2联系方式：".$home['tel']."\n";
		
		$content .= "^H1收费明细：".$info['cost_info']."\n";
		$content .= "--------------------------------\n";
		$content .= "^H1可退金额：".$info['refund_money']."\n";
		$content .= "--------------------------------\n";
		$content .= "^H1备注：".$info['title']."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "^H2交易金额:".$info['pay_fee'];
		$content .= "\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "...#由小码旺铺提供技术支持#...\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "\n";




		// $content .= "^H2合计：xx.0元\n";
		// $content .= "^H2送货地点：北京市海淀区xx路xx号\n";
		// $content .= "^H2联系电话：15999999988888\n";
		// $content .= "^H2订餐时间：2015-09-08 08:08:08\n";
		// $qrlength=chr(strlen('http://open.printcenter.cn'));
		// $content .= "^Q".$qrlength."http://open.printcenter.cn\n";
		// $result = $this->sendSelfFormatOrderInfo($DEVICE_NO, $key, 1,$content);
		// var_dump($result);

		// $DEVICE_NO = $data["config"]['printer_id'];
		// $key = $data["config"]['printer_key'];
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
		$res = json_decode($result,true);
		if ($res['responseCode'] == 1) {
			win('打印小票成功,请稍等！');
		} else {
			fail('打印失败，请联系商家！');
		}
	}

	/**
	 * 计算金额.
	 * @return [type] [description]
	 */
	public function sprintfs($data)
	{
		return sprintf("%1\$.2f", $data);
	}

}