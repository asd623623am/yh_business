<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 房屋管理
 */
class Orders extends Common
{
	public function propertyList()
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
	        $status = input('get.status');
	        if($status === ''){
	            exit('非法操作此页面');
	        }

	        $where = [];

	        //查看管理员有物业还是供暖权限
	        $user = session('admin');
	        if ($user['admin_type'] != 1) {
	        	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
	        	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
	        	
	        	if ($ro['type'] == 1) {
	        		$where['type'] = $status;
	        	} else if ($ro['type'] == 2) {
	        		$where['type'] = 2; //供暖
	        	} else {
	        		$where['type'] = $status;
	        	}

	        	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
	        	$d_id = [];
	        	if (!empty($ddata)) {
	        		foreach ($ddata as $k => $v) {
	        			$d_id[] = $v['d_id'];
	        		}

	        		$where['d_id'] = array('in',$d_id);
	        		$where['status']	= 1;
	        	}
	        } else {
	        	$where = [
	        		'type'	=> $status,
	        		'status'	=> 1
	        	];
	        }




	        $data = model('Payment')->order('p_id','desc')->where($where)->page($page,$limit)->select()->toArray();
	       $counts=model('Payment')->where(['status'=>1,'type'=>$status])->count();

	       foreach ($data as $k => $v) {
	       		$data[$k]['fanwei'] = $v['d_name'].$v['complex'].$v['home_code'];
	       		$data[$k]['start_at']	= date('Y-m-d',$v['start_at']);
	       		$data[$k]['end_at']	= date('Y-m-d',$v['end_at']);
	       		$countwhere = [
	       			'p_id'	=> $v['p_id'],
	       			'status'	=> 1,
	       		];
	       		$paywhere = [
	       			'p_id'	=> $v['p_id'],
	       			'pay_status'	=> 1,
	       			'status'	=> 1,
	       		];
	       		$count = model('Orders')->where($countwhere)->count();
	       		$paycount = model('Orders')->where($paywhere)->count();
	       		$data[$k]['j'] = $paycount.'/'.$count;
	       }
	        // foreach ($data as $k => $v) {
	        // 	if ($v['pay_status'] == 0) {
	        		
	        // 		$data[$k]['pay_status'] = '未支付';
	        // 	} else if ($v['pay_status'] == 1) {
	        		
	        // 		$data[$k]['pay_status'] = '已支付';
	        // 	} else {
	        		
	        // 		$data[$k]['pay_status'] = '已退款';
	        // 	}

	        // 	if ($v['invoice_status'] == 1) {
	        		
	        // 		$data[$k]['invoice_status'] = '未开发票';
	        // 	} else {
	        		
	        // 		$data[$k]['invoice_status'] = '已开发票';
	        // 	}
	        // 	$data[$k]['type'] = '物业';
	        // 	if ($v['pay_type'] == 1) {
	        // 		$data[$k]['pay_type'] = '在线支付';
	        // 	} else if ($v['pay_type'] == 2) {
	        // 		$data[$k]['pay_type'] = 'pos机支付';
	        // 	} else {
	        // 		$data[$k]['pay_type'] = '线下支付';
	        // 	}

	        // 	$data[$k]['paytime'] = date('Y',$v['paytime']).'年';
	        // 	$data[$k]['start_at'] = date('Y-m-d',$v['start_at']);
	        // 	$data[$k]['end_at'] = date('Y-m-d',$v['end_at']);

	        // 	if ($v['finish_at'] != null) {
	        // 		$data[$k]['finish_at'] = date('Y-m-d',$v['finish_at']);
	        // 	}

	        // }

	       	// $count=model('Orders')->where(['status'=>1,'type'=>0])->count();
	        $info=['code'=>0,'msg'=>'','count'=>$counts,'data'=>$data];
	        echo json_encode($info);
	        exit;

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

	    	$district=Db::table("district")->where($where)->field("id,name")->order('id','asc')->select();
	    	$this->assign('districts',$district);
	        return view();
	    }
	}


	public function propertyNewList()
	{

	    $data = input();
	    if (empty($data)) {
	        fail('非法操作此页面');
	    }
	    $page = $data['page'];
	    unset($data['page']);
	    $limit = $data['limit'];
	    unset($data['limit']);

	    
	    if (empty($data['d_id'])) {
	    	unset($data['d_id']);
	    }
	    if (empty($data['home_code'])) {
	    	unset($data['home_code']);
	    }
	    if (empty($data['title'])) {
	    	unset($data['title']);
	    }
	    if (empty($data['paytime'])) {
	    	unset($data['paytime']);
	    }

	    //查看管理员有物业还是供暖权限
	    $user = session('admin');
	    if ($user['admin_type'] != 1) {
	    	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
	    	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
	    	
	    	// if ($ro['type'] == 1) {
	    	// 	$data['type'] = 1;
	    	// } else if ($ro['type'] == 2) {
	    	// 	$data['type'] = 2; //供暖
	    	// } else {
	    	// 	$data['type'] = $status;
	    	// }

	    	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
	    	$d_id = [];
	    	if (!empty($ddata)) {
	    		foreach ($ddata as $k => $v) {
	    			$d_id[] = $v['d_id'];
	    		}

	    		$data['d_id'] = array('in',$d_id);
	    	}
	    }

	    if (empty($data['complex'])) {
	    	unset($data['complex']);
	    	$data['status'] = 1;
	    	$data['type']	= 1;
	    	$paInfo = model('Payment')->order('p_id','desc')->where($data)->page($page,$limit)->select()->toArray();
	    	$counts=model('Payment')->where($data)->count();

	    	foreach ($paInfo as $k => $v) {
	    			$paInfo[$k]['fanwei'] = $v['d_name'].$v['complex'].$v['home_code'];
	    			$paInfo[$k]['start_at']	= date('Y-m-d',$v['start_at']);
	    			$paInfo[$k]['end_at']	= date('Y-m-d',$v['end_at']);
	    			$countwhere = [
	    				'p_id'	=> $v['p_id'],
	    				'status'	=> 1,
	    			];
	    			$paywhere = [
	    				'p_id'	=> $v['p_id'],
	    				'pay_status'	=> 1,
	    				'status'	=> 1,
	    			];
	    			$count = model('Orders')->where($countwhere)->count();
	    			$paycount = model('Orders')->where($paywhere)->count();
	    			$paInfo[$k]['j'] = $paycount.'/'.$count;
	    	}


        	$info=['code'=>0,'msg'=>'','count'=>$counts,'data'=>$paInfo];
        	echo json_encode($info);exit;
	    		
	    } else {
	    		$complex = $data['complex'];
	    	    unset($data['complex']);
	    	    $data['status'] = 1;
	    	    $data['type']	= 1;
	    	    $paInfo = model('Payment')->order('p_id','desc')->where($data)->whereLike('complex',"%".$complex."%")->page($page,$limit)->select()->toArray();
	    	   $counts=model('Payment')->where($data)->whereLike('complex',"%".$complex."%")->count();

	    	   foreach ($paInfo as $k => $v) {
	    	   		$paInfo[$k]['fanwei'] = $v['d_name'].$v['complex'].$v['home_code'];
	    	   		$paInfo[$k]['start_at']	= date('Y-m-d',$v['start_at']);
	    	   		$paInfo[$k]['end_at']	= date('Y-m-d',$v['end_at']);
	    	   		$countwhere = [
	    				'p_id'	=> $v['p_id'],
	    				'status'	=> 1,
	    			];
	    			$paywhere = [
	    				'p_id'	=> $v['p_id'],
	    				'pay_status'	=> array('in',[1,2]),
	    				'status'	=> 1,
	    			];
	    			$count = model('Orders')->where($countwhere)->count();
	    			$paycount = model('Orders')->where($paywhere)->count();
	    			$paInfo[$k]['j'] = $paycount.'/'.$count;
	    	   }

	            $info=['code'=>0,'msg'=>'','count'=>$counts,'data'=>$paInfo];
	            echo json_encode($info);exit;
	    }
	}

	public function propertyAdd()
	{		
		if(check()){
           # 检查名字是否重复
           $data=input('post.');
           if(empty($data)){
               exit('非法操作此页面');
           }

           if ($data['home_code'] !== '') {


           		$code = explode('-', $data['home_code']);
      			$num = count($code);
      			if ($num == 1) {

      				$insert = [
      					'title'	=> $data['title'],
      					'type'	=> 1,
      					'complex'	=> $data['complex'],
      					'd_id'	=> $data['dis'],
      					'd_name'=> $data['dname'],
      					'home_code'	=> $data['home_code'],
      					'paytime'	=> $data['paytime'],
      					'fee'	=> $data['fee'],
      					// 'compensation'	=> $data['compensation'],
      					'start_at'	=> strtotime($data['start_at']),
      					'end_at'	=> strtotime($data['end_at']),
      					'content' 	=> $data['content'],
      					'status'	=> 1,
      					'ctime'	=> time(),
      					'building'		=> $code[0],
      					'month'	=> $data['month'],
      					'cost'	=> $data['cost'],
      				];


      			} else if ($num == 2) {

      				$insert = [
      					'title'	=> $data['title'],
      					'type'	=> 1,
      					'complex'	=> $data['complex'],
      					'd_id'	=> $data['dis'],
      					'd_name'=> $data['dname'],
      					'home_code'	=> $data['home_code'],
      					'paytime'	=> $data['paytime'],
      					'fee'	=> $data['fee'],
      					// 'compensation'	=> $data['compensation'],
      					'start_at'	=> strtotime($data['start_at']),
      					'end_at'	=> strtotime($data['end_at']),
      					'status'	=> 1,
      					'ctime'	=> time(),
      					'building'		=> $code[0],
      					'room'			=> $code[1],
      					'content' 	=> $data['content'],
      				];

      				
      			} else {

      				$insert = [
      					'title'	=> $data['title'],
      					'type'	=> 1,
      					'complex'	=> $data['complex'],
      					'd_id'	=> $data['dis'],
      					'd_name'=> $data['dname'],
      					'home_code'	=> $data['home_code'],
      					'paytime'	=> $data['paytime'],
      					'fee'	=> $data['fee'],
      					// 'compensation'	=> $data['compensation'],
      					'start_at'	=> strtotime($data['start_at']),
      					'end_at'	=> strtotime($data['end_at']),
      					'status'	=> 1,
      					'ctime'	=> time(),
      					'building'		=> $code[0],
      					'unit'			=> $code[1],
	      				'room'			=> $code[2],
	      				'content' 	=> $data['content'],
      				];


      				
      			}

           		$id = model('Payment')->insertGetId($insert);

           		if ($id) {
           		

           		$arr = explode('+', $data['complex']);
      			array_pop($arr);
      			$where = [];


      			$code = explode('-', $data['home_code']);
      			$num = count($code);
      			if ($num == 1) {

      				if (empty($data['complex'])) {
      					      				$where = [
      						      				'district_id'	=> $data['dis'],
      						      				'building'		=> $code[0],
      						      				'is_delete'		=> 1
      						      			];
      					
      				} else {
      					      				$where = [
      						      				'district_id'	=> $data['dis'],
      						      				'complex'		=> array('in',$arr),
      						      				'building'		=> $code[0],
      						      				'is_delete'		=> 1
      						      			];
      				}

      				
      			} else if ($num == 2) {
      				$where = [
	      				'district_id'	=> $data['dis'],
	      				'complex'		=> array('in',$arr),
	      				'building'		=> $code[0],
	      				'room'			=> $code[1],
	      				'is_delete'		=> 1
	      			];
      			} else {
      				$where = [
	      				'district_id'	=> $data['dis'],
	      				'complex'		=> array('in',$arr),
	      				'building'		=> $code[0],
	      				'unit'			=> $code[1],
	      				'room'			=> $code[2],
	      				'is_delete'		=> 1
	      			];
      			}
      			$homeData = model('Home')->where($where)->select()->toArray();
      			if (empty($homeData)) {
      				fail('发布失败,请仔细检查发布内容！');
      			}
      			$temp = [];
      			foreach ($homeData as $k => $v) {
      				$temp[] = [
      					'pay_status'	=> 0,
      					'invoice_status'	=> 1,
      					'title'	=> $data['title'],
      					'type'	=> 0,
      					'user_name'	=> $v['owner'],
      					'district_id'	=> $data['dis'],
      					'district_name'	=> $v['district_name'],
      					'complex'		=> $v['complex'],
      					'home_code'		=> $v['home_code'],
      					'home_id'		=> $v['home_id'],
      					'home_name'		=> $v['district_name'].$v['complex'].$v['home_code'],
      					'paytime'		=> $data['paytime'],
      					'fee'			=> $data['fee'],
      					// 'compensation'	=> $data['compensation'],
      					'voucher'		=> 0,
      					'start_at'		=> strtotime($data['start_at']),
      					'end_at'		=> strtotime($data['end_at']),
      					'status'		=> 1,
      					'ctime'		=> time(),
      					'p_id'		=> $id,
      					'month'	=> $data['month'],
      					'cost'	=> $data['cost'],
      					'money'	=> sprintf("%1\$.2f", $data['fee']*$v['area']*$data['month']+$data['cost']),

      				];
      			}
      			$res = model('Orders') -> insertAll( $temp );
      			if ($res < 1) {
      				fail('发布失败,请仔细检查发布内容！');
      			} else {
      				 $this -> addLog('发布了一个新的物业账单');
      				win('发布成功');
      			}


           		} else {
           			fail('发布失败,请仔细检查发布内容！');
           		}
           } else {

           		$insert = [
           			'title'	=> $data['title'],
           			'type'	=> 1,
           			'complex'	=> $data['complex'],
           			'd_id'	=> $data['dis'],
           			'd_name'=> $data['dname'],
           			'home_code'	=> $data['home_code'],
           			'paytime'	=> $data['paytime'],
           			'fee'	=> $data['fee'],
           			// 'compensation'	=> $data['compensation'],
           			'start_at'	=> strtotime($data['start_at']),
           			'end_at'	=> strtotime($data['end_at']),
           			'status'	=> 1,
           			'ctime'	=> time(),
           			'content' 	=> $data['content'],
           			'month'	=> $data['month'],
           			'cost'	=> $data['cost'],
           		];
           		$id = model('Payment')->insertGetId($insert);
           		if ($id) {

           			
           			if (empty($data['complex'])) {
           				$where = [
           					'district_id'	=> $data['dis'],
           					'is_delete'		=> 1
           				];
           			} else {
           				$arr = explode('+', $data['complex']);
           				array_pop($arr);
           				$where = [
           					'district_id'	=> $data['dis'],
           					'complex'		=> array('in',$arr),
           					'is_delete'		=> 1
           				];
           			}
           			
           			$homeData = model('Home')->where($where)->select()->toArray();

           			if (empty($homeData)) {
           				fail('发布失败,请仔细检查发布内容！');
           			}

           			$temp = [];
           			foreach ($homeData as $k => $v) {
           				$temp[] = [
           					'pay_status'	=> 0,
           					'invoice_status'	=> 1,
           					'title'	=> $data['title'],
           					'type'	=> 0,
           					'user_name'	=> $v['owner'],
           					'district_id'	=> $data['dis'],
           					'district_name'	=> $v['district_name'],
           					'complex'		=> $v['complex'],
           					'home_code'		=> $v['home_code'],
           					'home_id'		=> $v['home_id'],
           					'home_name'		=> $v['district_name'].$v['complex'].$v['home_code'],
           					'paytime'		=> $data['paytime'],
           					'fee'			=> $data['fee'],
           					// 'compensation'	=> $data['compensation'],
           					'voucher'		=> 0,
           					'start_at'		=> strtotime($data['start_at']),
           					'end_at'		=> strtotime($data['end_at']),
           					'status'		=> 1,
           					'ctime'		=> time(),
           					'p_id'		=> $id,
           					'month'	=> $data['month'],
           					'cost'	=> $data['cost'],
           					'money'	=> sprintf("%1\$.2f", $data['fee']*$v['area']*$data['month']+$data['cost']),

           				];
           			}
           			$res = model('Orders') -> insertAll( $temp );
           			if ($res < 1) {
           				fail('发布失败,请仔细检查发布内容！');
           			} else {
           				 $this -> addLog('发布了一个新的物业账单');
           				win('发布成功');
           			}



           		} else {
           			fail('发布失败,请仔细检查发布内容！');
           		}
           }
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
        	$district=model('District')->where($where)->select()->toArray();
            $this->assign('dis',$district);
            return view();
        }
	}

	public function propertyDel()
	{
		$id=input('post.id');
		$status = input('post.status');
		
		if(empty($id) || empty($status)){
		    fail('非法操作此页面');
		}
		$where=[
		    'p_id'=>$id
		];

		$wheres=[
		    'p_id'=>$id,
		    'pay_status'	=> 1
		];
		$arr = model('Orders')->where($wheres)->select()->toArray();
		if (!empty($arr)) {



			$info=['code'=>3,'msg'=>'请处先处理已缴费订单，再删除！'];
	        echo json_encode($info);
	        exit;

			if ($status == 1) {
				$info=['code'=>3,'msg'=>'请处先处理已缴费订单，再删除！'];
		        echo json_encode($info);
		        exit;	
			} else {
				//删除
				$arr=[
				    'status'=>2
				];
				
				$info = model('Payment')->where($where)->setField($arr);
				if($info){
				    
					$wheress=[
					    'p_id'=>$id,
					    'pay_status'	=> 0
					];
					$infos = model('Orders')->where($wheress)->setField(['status'=>2]);
					if ($infos) {
						 $this -> addLog('删除了一个物业账单');
						win('删除成功');
					} else {
						fail('删除失败');
					}

				}else{
				    fail('删除失败');
				}
			}
		} else {
			//删除
			$arr=[
			    'status'=>2
			];

			$info = model('Payment')->where($where)->setField($arr);
			if($info){
			    
				$wheress=[
					'p_id'=>$id,
					'status'	=> 1,
				];

				$odata = model('Orders')->where($wheress)->select()->toArray();
				if (empty($odata)) {
					$this -> addLog('删除了一个物业账单');
					win('删除成功');
				} else {
					$infos = model('Orders')->where($wheress)->setField(['status'=>2]);
					if ($infos) {
						 $this -> addLog('删除了一个物业账单');
						win('删除成功');
					} else {
						win('删除成功');
					}
				}
			}else{
			    fail('删除失败');
			}

		}
		
	}

	public function propertyUpdateInfo(){
	    $id=input('get.id');

	    if(empty($id)){
	        exit('非法操作此页面');
	    }

	    $where=[
	        'p_id'=>$id
	    ];
	    $data=model('Payment')->where($where)->find();
	    // if ($data['pay_status'] == 0) {
	    // 	$data['is_show'] = 1;
	    // } else {
	    // 	$data['is_show'] = 2;
	    // }
	    // $data['paytime'] = date('Y-m-d',$data['paytime']);
	    $data['start_at'] = date('Y-m-d',$data['start_at']);
	    $data['end_at'] = date('Y-m-d',$data['end_at']);
	    $district = model('District')->select()->toArray();
		$this->assign('dis',$district);
	    $this->assign('data',$data);
	    return view();

	}

	public function propertyUp(){
	    if(check()){
	        $data=input('post.');
	        if(empty($data)){
	            exit('非法操作此页面');
	        }
	        $where = [
	        	'p_id'	=> $data['p_id'],
	        ];

	        $district=Db::table("district")->where(['id'=>$data['dis']])->find();
	        if (empty($district)) {
	        	$district_name = '';	
	        } else {
	        	$district_name = $district['name'];
	        }
	        if ($data['home_code'] == '') {
	        	$building = '';
	        	$unit = '';
	        	$room = '';
	        } else {
	     		$code = explode('-', $data['home_code']);
				$num = count($code);
				if ($num == 1) {
					$building = $code[0];
					$unit = '';
					$room = '';
				} else if ($num == 2) {
					$building = $code[0];
					$unit = '';
					$room = $code[1];
				} else {
					$building = $code[0];
					$unit = $code[1];
					$room = $code[2];
				}
	        }
	        $update = [
	        	'title'	=> $data['title'],
	        	'd_id'	=> $data['dis'],
	        	'd_name'	=> $district_name,
	        	'home_code'	=> $data['home_code'],
	        	'paytime'	=> $data['paytime'],
	        	'fee'		=> $data['fee'],
	        	// 'compensation'	=> $data['compensation'],
	        	'start_at'	=> strtotime($data['start_at']),
	        	'end_at'	=> strtotime($data['end_at']),
	        	'complex'	=> $data['complex'],
	        	'content'	=> $data['content'],
	        	'building'	=> $building,
	        	'unit'		=> $unit,
	        	'room'		=> $room,
	        ];

	        $info = model('Payment')->where($where)->setField($update);
	        if ($info) {
	        	
	        	$orderWhere = [
	        		'p_id'	=> $data['p_id'],
	        	];
	        	$orederUpdate = [
	        		'title'		=> $data['title'],
	        		'paytime'	=> $data['paytime'],
	        		'fee'		=> $data['fee'],
	        		// 'compensation'	=> $data['compensation'],
	        		'start_at'	=> strtotime($data['start_at']),
	        		'end_at'	=> strtotime($data['end_at']),
	        	];
	        	$orderInfo = model('Orders')->where($orderWhere)->setField($orederUpdate);
	        	if ($orderInfo) {
	        		 $this -> addLog('修改了一个物业账单');
	        		win('修改成功');
	        	} else {
	        		fail('修改失败');
	        	}
	        } else {
	        	fail('修改失败');
	        }
	    }
	}




	public function heatingList()
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
		        $status = input('get.status');
		        if($status === ''){
		            exit('非法操作此页面');
		        }

		        // $where = [
		        // 	'type'	=> $status,
		        // 	'status'	=> 1
		        // ];

		        $where = [];

		        //查看管理员有物业还是供暖权限
		        $user = session('admin');
		        if ($user['admin_type'] != 1) {
		        	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
		        	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
		        	
		        	if ($ro['type'] == 1) {
		        		$where['type'] = $status;
		        	} else if ($ro['type'] == 2) {
		        		$where['type'] = 2; //供暖
		        	} else {
		        		$where['type'] = $status;
		        	}

		        	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
		        	$d_id = [];
		        	if (!empty($ddata)) {
		        		foreach ($ddata as $k => $v) {
		        			$d_id[] = $v['d_id'];
		        		}

		        		$where['d_id'] = array('in',$d_id);
		        		$where['status']	= 1;
		        	}
		        } else {
		        	$where = [
		        		'type'	=> $status,
		        		'status'	=> 1
		        	];
		        }



		        $data = model('Payment')->order('p_id','desc')->where($where)->page($page,$limit)->select()->toArray();
		       $counts=model('Payment')->where(['status'=>1,'type'=>$status])->count();

		       foreach ($data as $k => $v) {
		       		$data[$k]['fanwei'] = $v['d_name'].$v['complex'].$v['home_code'];
		       		$data[$k]['start_at']	= date('Y-m-d',$v['start_at']);
		       		$data[$k]['end_at']	= date('Y-m-d',$v['end_at']);

		       		$countwhere = [
		       			'p_id'	=> $v['p_id'],
		       			'status'	=> 1,
		       		];
		       		$paywhere = [
		       			'p_id'	=> $v['p_id'],
		       			'pay_status'	=> array('in',[1,2]),
		       			'status'	=> 1,
		       		];
		       		$count = model('Orders')->where($countwhere)->count();
		       		$paycount = model('Orders')->where($paywhere)->count();
		       		$data[$k]['j'] = $paycount.'/'.$count;
		       }

		        $info=['code'=>0,'msg'=>'','count'=>$counts,'data'=>$data];
		        echo json_encode($info);
		        exit;

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
		    	$district=Db::table("district")->where($where)->field("id,name")->order('id','asc')->select();
		    	$this->assign('districts',$district);
		        return view();
		    }
	}


	public function heatingAdd()
	{
				if(check()){
		           # 检查名字是否重复
		           $data=input('post.');
		           if(empty($data)){
		               exit('非法操作此页面');
		           }

		           if ($data['home_code'] !== '') {


		           		$code = explode('-', $data['home_code']);
		      			$num = count($code);
		      			if ($num == 1) {

		      				$insert = [
		      					'title'	=> $data['title'],
		      					'type'	=> 2,
		      					'complex'	=> $data['complex'],
		      					'd_id'	=> $data['dis'],
		      					'd_name'=> $data['dname'],
		      					'home_code'	=> $data['home_code'],
		      					'paytime'	=> $data['paytime'],
		      					'fee'	=> $data['fee'],
		      					// 'compensation'	=> $data['compensation'],
		      					'start_at'	=> strtotime($data['start_at']),
		      					'end_at'	=> strtotime($data['end_at']),
		      					'content' 	=> $data['content'],
		      					'status'	=> 1,
		      					'ctime'	=> time(),
		      					'building'		=> $code[0],
		      					'cost'	=> $data['cost']
		      				];


		      			} else if ($num == 2) {

		      				$insert = [
		      					'title'	=> $data['title'],
		      					'type'	=> 2,
		      					'complex'	=> $data['complex'],
		      					'd_id'	=> $data['dis'],
		      					'd_name'=> $data['dname'],
		      					'home_code'	=> $data['home_code'],
		      					'paytime'	=> $data['paytime'],
		      					'fee'	=> $data['fee'],
		      					// 'compensation'	=> $data['compensation'],
		      					'start_at'	=> strtotime($data['start_at']),
		      					'end_at'	=> strtotime($data['end_at']),
		      					'status'	=> 1,
		      					'ctime'	=> time(),
		      					'building'		=> $code[0],
		      					'room'			=> $code[1],
		      					'content' 	=> $data['content'],
		      				];

		      				
		      			} else {

		      				$insert = [
		      					'title'	=> $data['title'],
		      					'type'	=> 2,
		      					'complex'	=> $data['complex'],
		      					'd_id'	=> $data['dis'],
		      					'd_name'=> $data['dname'],
		      					'home_code'	=> $data['home_code'],
		      					'paytime'	=> $data['paytime'],
		      					'fee'	=> $data['fee'],
		      					// 'compensation'	=> $data['compensation'],
		      					'start_at'	=> strtotime($data['start_at']),
		      					'end_at'	=> strtotime($data['end_at']),
		      					'status'	=> 1,
		      					'ctime'	=> time(),
		      					'building'		=> $code[0],
		      					'unit'			=> $code[1],
			      				'room'			=> $code[2],
			      				'content' 	=> $data['content'],
		      				];


		      				
		      			}

		           		$id = model('Payment')->insertGetId($insert);
		           		if ($id) {
		           			
		           		$arr = explode('+', $data['complex']);
		      			array_pop($arr);
		      			$where = [];


		      			$code = explode('-', $data['home_code']);
		      			$num = count($code);
		      			if ($num == 1) {

		      				if (empty($data['complex'])) {
		      					      				$where = [
		      						      				'district_id'	=> $data['dis'],
		      						      				'building'		=> $code[0],
		      						      				'is_delete'		=> 1
		      						      			];
		      					
		      				} else {
		      					      				$where = [
		      						      				'district_id'	=> $data['dis'],
		      						      				'complex'		=> array('in',$arr),
		      						      				'building'		=> $code[0],
		      						      				'is_delete'		=> 1
		      						      			];
		      				}


		      				
		      			} else if ($num == 2) {
		      				$where = [
			      				'district_id'	=> $data['dis'],
			      				'complex'		=> array('in',$arr),
			      				'building'		=> $code[0],
			      				'room'			=> $code[1],
			      				'is_delete'		=> 1
			      			];
		      			} else {
		      				$where = [
			      				'district_id'	=> $data['dis'],
			      				'complex'		=> array('in',$arr),
			      				'building'		=> $code[0],
			      				'unit'			=> $code[1],
			      				'room'			=> $code[2],
			      				'is_delete'		=> 1
			      			];
		      			}
		      			$homeData = model('Home')->where($where)->select()->toArray();
		      			if (empty($homeData)) {
		      				fail('发布失败,请仔细检查发布内容！');
		      			}
		      			$temp = [];
		      			foreach ($homeData as $k => $v) {
		      				$temp[] = [
		      					'pay_status'	=> 0,
		      					'invoice_status'	=> 1,
		      					'title'	=> $data['title'],
		      					'type'	=> 1,
		      					'user_name'	=> $v['owner'],
		      					'district_id'	=> $data['dis'],
		      					'district_name'	=> $v['district_name'],
		      					'complex'		=> $v['complex'],
		      					'home_code'		=> $v['home_code'],
		      					'home_id'		=> $v['home_id'],
		      					'home_name'		=> $v['district_name'].$v['complex'].$v['home_code'],
		      					'paytime'		=> $data['paytime'],
		      					'fee'			=> $data['fee'],
		      					// 'compensation'	=> $data['compensation'],
		      					'voucher'		=> 0,
		      					'start_at'		=> strtotime($data['start_at']),
		      					'end_at'		=> strtotime($data['end_at']),
		      					'status'		=> 1,
		      					'ctime'		=> time(),
		      					'p_id'		=> $id,
		      					'cost'		=> $data['cost'],
		      					'money'		=> sprintf("%1\$.2f", $data['fee']*$v['area']*$data['cost'])

		      				];
		      			}
		      			$res = model('Orders') -> insertAll( $temp );
		      			if ($res < 1) {
		      				fail('发布失败,请仔细检查发布内容！');
		      			} else {
		      				 $this -> addLog('发布了一个新的供暖账单');
		      				win('发布成功');
		      			}


		           		} else {
		           			fail('发布失败,请仔细检查发布内容！');
		           		}
		           } else {

		           		$insert = [
		           			'title'	=> $data['title'],
		           			'type'	=> 2,
		           			'complex'	=> $data['complex'],
		           			'd_id'	=> $data['dis'],
		           			'd_name'=> $data['dname'],
		           			'home_code'	=> $data['home_code'],
		           			'paytime'	=> $data['paytime'],
		           			'fee'	=> $data['fee'],
		           			// 'compensation'	=> $data['compensation'],
		           			'start_at'	=> strtotime($data['start_at']),
		           			'end_at'	=> strtotime($data['end_at']),
		           			'status'	=> 1,
		           			'ctime'	=> time(),
		           			'content' 	=> $data['content'],
		           			'cost'	=> $data['cost']
		           		];
		           		$id = model('Payment')->insertGetId($insert);
		           		if ($id) {

		           			if (empty($data['complex'])) {
		           				$where = [
		           					'district_id'	=> $data['dis'],
		           					'is_delete'		=> 1
		           				];
		           			} else {
		           				$arr = explode('+', $data['complex']);
		           				array_pop($arr);
		           				$where = [
		           					'district_id'	=> $data['dis'],
		           					'complex'		=> array('in',$arr),
		           					'is_delete'		=> 1
		           				];	
		           			}

		           			
		           			$homeData = model('Home')->where($where)->select()->toArray();

		           			if (empty($homeData)) {
		           				fail('发布失败,请仔细检查发布内容！');
		           			}

		           			$temp = [];
		           			foreach ($homeData as $k => $v) {
		           				$temp[] = [
		           					'pay_status'	=> 0,
		           					'invoice_status'	=> 1,
		           					'title'	=> $data['title'],
		           					'type'	=> 1,
		           					'user_name'	=> $v['owner'],
		           					'district_id'	=> $data['dis'],
		           					'district_name'	=> $v['district_name'],
		           					'complex'		=> $v['complex'],
		           					'home_code'		=> $v['home_code'],
		           					'home_id'		=> $v['home_id'],
		           					'home_name'		=> $v['district_name'].$v['complex'].$v['home_code'],
		           					'paytime'		=> $data['paytime'],
		           					'fee'			=> $data['fee'],
		           					// 'compensation'	=> $data['compensation'],
		           					'voucher'		=> 0,
		           					'start_at'		=> strtotime($data['start_at']),
		           					'end_at'		=> strtotime($data['end_at']),
		           					'status'		=> 1,
		           					'ctime'		=> time(),
		           					'p_id'		=> $id,
		           					'cost'		=> $data['cost'],
		           					'money'		=> sprintf("%1\$.2f", $data['fee']*$v['area']*$data['cost'])

		           				];
		           			}
		           			$res = model('Orders') -> insertAll( $temp );
		           			if ($res < 1) {
		           				fail('发布失败,请仔细检查发布内容！');
		           			} else {
		           				 $this -> addLog('发布了一个新的供暖账单');
		           				win('发布成功');
		           			}



		           		} else {
		           			fail('发布失败,请仔细检查发布内容！');
		           		}
		           }
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
		        	$district=model('District')->where($where)->select()->toArray();
		            $this->assign('dis',$district);
		            return view();
		        }
	}

	public function heatingNewList()
	{

	    $data = input();
	    if (empty($data)) {
	        fail('非法操作此页面');
	    }
	    $page = $data['page'];
	    unset($data['page']);
	    $limit = $data['limit'];
	    unset($data['limit']);

	    
	    if (empty($data['d_id'])) {
	    	unset($data['d_id']);
	    }
	    if (empty($data['home_code'])) {
	    	unset($data['home_code']);
	    }
	    if (empty($data['title'])) {
	    	unset($data['title']);
	    }
	    if (empty($data['paytime'])) {
	    	unset($data['paytime']);
	    }


	    //查看管理员有物业还是供暖权限
	    $user = session('admin');
	    if ($user['admin_type'] != 1) {
	    	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
	    	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
	    	
	    	// if ($ro['type'] == 1) {
	    	// 	$data['type'] = 1;
	    	// } else if ($ro['type'] == 2) {
	    	// 	$data['type'] = 2; //供暖
	    	// } else {
	    	// 	$data['type'] = $status;
	    	// }

	    	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
	    	$d_id = [];
	    	if (!empty($ddata)) {
	    		foreach ($ddata as $k => $v) {
	    			$d_id[] = $v['d_id'];
	    		}

	    		$data['d_id'] = array('in',$d_id);
	    	}
	    }





	    if (empty($data['complex'])) {
	    	unset($data['complex']);
	    	$data['status'] = 1;
	    	$data['type']	= 2;
	    	$paInfo = model('Payment')->order('p_id','desc')->where($data)->page($page,$limit)->select()->toArray();
	    	$counts=model('Payment')->where($data)->count();

	    	foreach ($paInfo as $k => $v) {
	    			$paInfo[$k]['fanwei'] = $v['d_name'].$v['complex'].$v['home_code'];
	    			$paInfo[$k]['start_at']	= date('Y-m-d',$v['start_at']);
	    			$paInfo[$k]['end_at']	= date('Y-m-d',$v['end_at']);
	    			$countwhere = [
		       			'p_id'	=> $v['p_id'],
		       			'status'	=> 1,
		       		];
		       		$paywhere = [
		       			'p_id'	=> $v['p_id'],
		       			'pay_status'	=> array('in',[1,2]),
		       			'status'	=> 1,
		       		];
		       		$count = model('Orders')->where($countwhere)->count();
		       		$paycount = model('Orders')->where($paywhere)->count();
		       		$paInfo[$k]['j'] = $paycount.'/'.$count;
	    	}


        	$info=['code'=>0,'msg'=>'','count'=>$counts,'data'=>$paInfo];
        	echo json_encode($info);exit;
	    		
	    } else {
	    		$complex = $data['complex'];
	    	    unset($data['complex']);
	    	    $data['status'] = 1;
	    	    $data['type']	= 2;
	    	    $paInfo = model('Payment')->order('p_id','desc')->where($data)->whereLike('complex',"%".$complex."%")->page($page,$limit)->select()->toArray();
	    	   $counts=model('Payment')->where($data)->whereLike('complex',"%".$complex."%")->count();

	    	   foreach ($paInfo as $k => $v) {
	    	   		$paInfo[$k]['fanwei'] = $v['d_name'].$v['complex'].$v['home_code'];
	    	   		$paInfo[$k]['start_at']	= date('Y-m-d',$v['start_at']);
	    	   		$paInfo[$k]['end_at']	= date('Y-m-d',$v['end_at']);
	    	   		$countwhere = [
		       			'p_id'	=> $v['p_id'],
		       			'status'	=> 1,
		       		];
		       		$paywhere = [
		       			'p_id'	=> $v['p_id'],
		       			'pay_status'	=> array('in',[1,2]),
		       			'status'	=> 1,
		       		];
		       		$count = model('Orders')->where($countwhere)->count();
		       		$paycount = model('Orders')->where($paywhere)->count();
		       		$paInfo[$k]['j'] = $paycount.'/'.$count;
	    	   }

	            $info=['code'=>0,'msg'=>'','count'=>$counts,'data'=>$paInfo];
	            echo json_encode($info);exit;
	    }
	}

	public function heatingDel()
	{
		$id=input('post.id');
		$status = input('post.status');
		
		if(empty($id) || empty($status)){
		    fail('非法操作此页面');
		}
		$where=[
		    'p_id'=>$id
		];

		$wheres=[
		    'p_id'=>$id,
		    'pay_status'	=> array('in',[1,2])
		];
		$arr = model('Orders')->where($wheres)->select()->toArray();
		if (!empty($arr)) {
			if ($status == 1) {
				$info=['code'=>3,'msg'=>'有订单，确认删除吗？'];
		        echo json_encode($info);
		        exit;	
			} else {
				//删除
				$arr=[
				    'status'=>2
				];
				
				$info = model('Payment')->where($where)->setField($arr);
				if($info){
				    
					$wheress=[
					    'p_id'=>$id,
					    'pay_status'	=> 0
					];
					$infos = model('Orders')->where($wheress)->setField(['status'=>2]);
					if ($infos) {
						 $this -> addLog('删除了一个供暖账单');
						win('删除成功');
					} else {
						fail('删除失败');
					}

				}else{
				    fail('删除失败');
				}
			}
		} else {
			//删除
			$arr=[
			    'status'=>2
			];
			
			$info = model('Payment')->where($where)->setField($arr);

			if($info){
			    
				$wheress=[
				    'p_id'=>$id,
				];
				$infos = model('Orders')->where($wheress)->setField(['status'=>2]);
				if ($infos) {
					 $this -> addLog('删除了一个供暖账单');
					win('删除成功');
				} else {
					fail('删除失败');
				}

			}else{
			    fail('删除失败');
			}

		}
	}

	public function heatingUpdateInfo()
	{
		    $id=input('get.id');

		    if(empty($id)){
		        exit('非法操作此页面');
		    }

		    $where=[
		        'p_id'=>$id
		    ];
		    $data=model('Payment')->where($where)->find();
		    // if ($data['pay_status'] == 0) {
		    // 	$data['is_show'] = 1;
		    // } else {
		    // 	$data['is_show'] = 2;
		    // }
		    // $data['paytime'] = date('Y-m-d',$data['paytime']);
		    $data['start_at'] = date('Y-m-d',$data['start_at']);
		    $data['end_at'] = date('Y-m-d',$data['end_at']);
		    $district = model('District')->select()->toArray();
			$this->assign('dis',$district);
		    $this->assign('data',$data);
		    return view();
	}

	public function heatingUp()
	{
		 if(check()){
	        $data=input('post.');
	        if(empty($data)){
	            exit('非法操作此页面');
	        }
	        $where = [
	        	'p_id'	=> $data['p_id'],
	        ];

	        $district=Db::table("district")->where(['id'=>$data['dis']])->find();
	        if (empty($district)) {
	        	$district_name = '';	
	        } else {
	        	$district_name = $district['name'];
	        }
	        if ($data['home_code'] == '') {
	        	$building = '';
	        	$unit = '';
	        	$room = '';
	        } else {
	     		$code = explode('-', $data['home_code']);
				$num = count($code);
				if ($num == 1) {
					$building = $code[0];
					$unit = '';
					$room = '';
				} else if ($num == 2) {
					$building = $code[0];
					$unit = '';
					$room = $code[1];
				} else {
					$building = $code[0];
					$unit = $code[1];
					$room = $code[2];
				}
	        }
	        $update = [
	        	'title'	=> $data['title'],
	        	'd_id'	=> $data['dis'],
	        	'd_name'	=> $district_name,
	        	'home_code'	=> $data['home_code'],
	        	'paytime'	=> $data['paytime'],
	        	'fee'		=> $data['fee'],
	        	// 'compensation'	=> $data['compensation'],
	        	'start_at'	=> strtotime($data['start_at']),
	        	'end_at'	=> strtotime($data['end_at']),
	        	'complex'	=> $data['complex'],
	        	'content'	=> $data['content'],
	        	'building'	=> $building,
	        	'unit'		=> $unit,
	        	'room'		=> $room,
	        ];

	        $info = model('Payment')->where($where)->setField($update);
	        if ($info) {
	        	
	        	$orderWhere = [
	        		'p_id'	=> $data['p_id'],
	        	];
	        	$orederUpdate = [
	        		'title'		=> $data['title'],
	        		'paytime'	=> $data['paytime'],
	        		'fee'		=> $data['fee'],
	        		// 'compensation'	=> $data['compensation'],
	        		'start_at'	=> strtotime($data['start_at']),
	        		'end_at'	=> strtotime($data['end_at']),
	        	];
	        	$orderInfo = model('Orders')->where($orderWhere)->setField($orederUpdate);
	        	if ($orderInfo) {
	        		 $this -> addLog('修改了一个供暖账单');
	        		win('修改成功');
	        	} else {
	        		fail('修改失败');
	        	}
	        } else {
	        	fail('修改失败');
	        }
	    }
	}


	/**
	 * 缴费列表.
	 * @return [type] [description]
	 */
	public function paymentList()
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
	    
	        $where = [
	        	'type'	=> array('in',[0,1])
	        ];
	       $data = model('Orders')->order('id','desc')->where($where)->page($page,$limit)->select()->toArray();
	       $count=model('Orders')->where($where)->count();
	       $i= ($page-1) * ($limit);
	       // $ii = $i+1;
	       foreach ($data as $k => $v) {
	       	
	        $i++;
	        $data[$k]['num'] = $i;
	       		if ($v['invoice_status'] == 1) {
	       			$data[$k]['invoice_status'] = '未开发票';
	       		} else {
	       			$data[$k]['invoice_status'] = '已开发票';
	       		}

	       		if ($v['type'] == 0) {
	       			$data[$k]['type'] = '物业';
	       		} else {
	       			$data[$k]['type'] = '供暖';
	       		}

	       		if ($v['pay_status'] == 0) {
	       			$data[$k]['pay_status'] = '未缴费';
	       		} else if($v['pay_status'] == 1){
	       			$data[$k]['pay_status'] = '已缴费';
	       		} else {
	       			$data[$k]['pay_status'] = '已退款';
	       		}

	       		if ($v['status'] == 1) {
	       			$data[$k]['status'] = '正常';
	       		} else {
	       			$data[$k]['status'] = '已删除';
	       		}
	       		if (!empty($v['pay_type'])) {
	       			if ($v['pay_type'] == 1) {
	       				$data[$k]['pay_type'] = '小程序支付';
	       			} else if ($v['pay_type'] == 2) {
	       				$data[$k]['pay_type'] = '订单支付支付';
	       			} else {
	       				$data[$k]['pay_type'] = '线下支付';
	       			}
	       		}
	       		if (!empty($v['finish_at'])) {
	       			$data[$k]['finish_at'] = date('Y-m-d H:i:s',$v['finish_at']);
	       		}
	       		$newwhere = [
	       			'home_id'	=> $v['home_id']
	       		];
	       		$homedata = model('Home')->where($newwhere)->find()->toArray();

	       		$data[$k]['area'] = $homedata['area'];
	       		// $data[$k]['newfee'] = $v['fee']*$homedata['area'];
	       		// $data[$k]['fanwei'] = $v['d_name'].$v['complex'].$v['home_code'];
	       		// $data[$k]['start_at']	= date('Y-m-d',$v['start_at']);
	       		// $data[$k]['end_at']	= date('Y-m-d',$v['end_at']);
	       		// $data[$k]['j'] = '100/1000';
	       		
	       }
	  
	        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
	        echo json_encode($info);
	        exit;

	    }else{
	    	$district=Db::table("district")->field("id,name")->order('id','asc')->select();
	    	$this->assign('districts',$district);
	        return view();
	    }
	}




	public function paymentNewList()
	{
		$data = input();
		if (empty($data)) {
		    fail('非法操作此页面');
		}
		$page = $data['page'];
		unset($data['page']);
		$limit = $data['limit'];
		unset($data['limit']);
		$where = [];
		if (empty($data['district_id'])) {
			// 查看管理员有物业还是供暖权限
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
			} else {
				unset($data['district_id']);
			}
		} else {
			$where['district_id'] = $data['district_id'];
		}
		if (empty($data['complex'])) {
			unset($data['complex']);
		} else {
			$where['complex'] = $data['complex'];
		}
		if (empty($data['home_code'])) {
			unset($data['home_code']);
		} else {
			$where['home_code'] = $data['home_code'];
		}

		if (empty($data['pay_type'])) {
			unset($data['pay_type']);
		} else {
			$where['pay_type'] = $data['pay_type'];
		}

		if (empty($data['pay_status'])) {
			unset($data['pay_status']);
		} else {
			if ($data['pay_status'] == 2) {
				$where['pay_status'] = 0;
			} else if ($data['pay_status'] == 3) {
				$where['pay_status'] = 2;
			} else {
				$where['pay_status'] = $data['pay_status'];	
			}
		}
		if (empty($data['invoice_status'])) {
			unset($data['invoice_status']);
		} else {
			$where['invoice_status'] = $data['invoice_status'];
		}

		// if ($data['type'] == 0 || $data['type'] == 1) {
		// 	$where['type'] = $data['type'];
		// } else  {
		// 	$where['type'] = array('in',[0,1]);
		// }
		if (empty($data['type'])) {
			unset($data['type']);
			$where['type'] = array('in',[0,1]);
		} else {
			if ($data['type'] == 2) {
				$where['type'] = 0;
			} else {
				$where['type'] = $data['type'];	
			}
			
		}
		if (empty($data['title'])) {
			unset($data['title']);
		} else {
			$where['title'] = $data['title'];
		}
		if (empty($data['user_name'])) {
			unset($data['user_name']);
		} else {
			$where['user_name'] = $data['user_name'];
		}
		if (empty($data['home_code'])) {
			unset($data['home_code']);
		} else {
			$where['home_code'] = $data['home_code'];
		}
		// if (empty($data['tel'])) {
		// 	unset($data['tel']);
		// } else {
		// 	$where['tel'] = $data['tel'];
		// }
		
		// dump($where);exit;


		// 查看管理员有物业还是供暖权限
		$user = session('admin');
		if ($user['admin_type'] != 1) {
			$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
			$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
			if ($ro['type'] == 1) {
				$where['type'] = 0; //物业
			} else if ($ro['type'] == 2) {
				$where['type'] = 1; //供暖
			} else {
				if (empty($data['type'])) {
					unset($data['type']);
					$where['type'] = array('in',[0,1]);
				} else {
					if ($data['type'] == 2) {
						$where['type'] = 0;
					} else {
						$where['type'] = $data['type'];	
					}
					
				}
			}

			$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
			$d_id = [];
			if (!empty($ddata)) {
				foreach ($ddata as $k => $v) {
					$d_id[] = $v['d_id'];
				}

				$where['district_id'] = array('in',$d_id);
			}
		}
		//判断是否是历史订单.
		if(empty($data['historyOrder'])){
			$data = model('Orders')->order('id','desc')->where($where)->page($page,$limit)->select()->toArray();
        	$count=model('Orders')->where($where)->count();
		} else {

			if($data['historyOrder'] == 2){
				$data = model('Orders')->order('id','desc')->where($where)->where("fee is null or fee=''")->page($page,$limit)->select()->toArray();
        		$count=model('Orders')->where($where)->where("fee is null or fee=''")->count();
			} else {
				$data = model('Orders')->order('id','desc')->where($where)->page($page,$limit)->select()->toArray();
        		$count=model('Orders')->where($where)->count();
			}
			
			// $where['fee']=  '';
			// $where['cost'] = '';
		}
		// dump($data);exit;
		//查看管理员有物业还是供暖权限
		// $user = session('admin');
		// $roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
		// $ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
		// if ($ro['type'] == 1) {
		// 	$where['type'] = 0; //物业
		// } else if ($ro['type'] == 2) {
		// 	$where['type'] = 1; //供暖
		// } else {
		// 	$where = [
		// 		'type'	=> array('in',[0,1])
		// 	];	
		// }

		// $ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
		// $d_id = [];
		// if (!empty($ddata)) {
		// 	foreach ($ddata as $k => $v) {
		// 		$d_id[] = $v['d_id'];
		// 	}

		// 	$where['district_id'] = array('in',$d_id);
		// }
	    // $data = model('Orders')->order('id','desc')->where($where)->page($page,$limit)->select()->toArray();
        // $count=model('Orders')->where($where)->count();


        foreach ($data as $k => $v) {
	       		if ($v['invoice_status'] == 1) {
	       			$data[$k]['invoice_status'] = '未开发票';
	       		} else if ($v['invoice_status'] == 2) {
	       			$data[$k]['invoice_status'] = '手动发票';
	       		}else if ($v['invoice_status'] == 3) {
	       			$data[$k]['invoice_status'] = '电子发票';
	       		}

	       		if ($v['type'] == 0) {
	       			$data[$k]['type'] = '物业';
	       		} else {
	       			$data[$k]['type'] = '供暖';
	       		}

	       		if ($v['pay_status'] == 0) {
	       			$data[$k]['pay_status'] = '未缴费';
	       		} else if($v['pay_status'] == 1){
	       			$data[$k]['pay_status'] = '已缴费';
	       		} else {
	       			$data[$k]['pay_status'] = '已退款';
	       		}

	       		if ($v['status'] == 1) {
	       			$data[$k]['status'] = '正常';
	       		} else {
	       			$data[$k]['status'] = '已删除';
	       		}
	       		if (!empty($v['pay_type'])) {
	       			if ($v['pay_type'] == 1) {
	       				$data[$k]['pay_type'] = '小程序支付';
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
	       				$data[$k]['pay_type'] = '线下支付';
	       			}
	       		}
	       		if (!empty($v['finish_at'])) {
	       			$data[$k]['finish_at'] = date('Y-m-d H:i:s',$v['finish_at']);
	       		}
	       		$newwhere = [
	       			'home_id'	=> $v['home_id']
	       		];
	       		$homedata = model('Home')->where($newwhere)->find()->toArray();

	       		$data[$k]['area'] = $homedata['area'];
	       		// $data[$k]['newfee'] = $v['fee']*$homedata['area'];
	       		// $data[$k]['fanwei'] = $v['d_name'].$v['complex'].$v['home_code'];
	       		// $data[$k]['start_at']	= date('Y-m-d',$v['start_at']);
	       		// $data[$k]['end_at']	= date('Y-m-d',$v['end_at']);
	       		// $data[$k]['j'] = '100/1000';
	       		if ($v['type'] == 0) {
	       			$data[$k]['charge'] = $v['fee'].'X 房屋面积 X'.$v['month'].'个月 +'.$v['cost'].'其他费用';
	       		} else {
	       			$data[$k]['charge'] = $v['fee'].'X 房屋面积 X'.$v['cost'].'系数';
	       		}
	       		$data[$k]['pay_money'] = sprintf("%1\$.2f", $v['money']+$v['compensation']);
	       }
	  
	        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
	        echo json_encode($info);
	        exit;

	}


	public function paymentDel()
	{
		$id=input('post.id');
		
		if(empty($id)){
		    fail('非法操作此页面');
		}
		$where=[
		    'id'=>$id
		];
		$arr = model('Orders')->where($where)->find();

		if ($arr == null) {
			fail('删除失败');
		} else {
			if ($arr['status'] == 2) {
				win('您已将删除过这条记录了');
			}
		}
						
		$info = model('Orders')->where($where)->setField(['status' => 2]);
		if ($info) {
			 $this -> addLog('删除了一个缴费订单');
			win('删除成功');
		} else {
			fail('删除失败');
		}
	}

	public function paymentAdd()
	{
		 if(check()){
            # 检查名字是否重复
            $data=input('post.');
            if(empty($data)){
                exit('非法操作此页面');
            }
            if (empty($data['home_code'])) {
            	fail('非法操作此页面');
            }
            $where = [
            	'district_id'	=> $data['dis'],
            	'complex'		=> $data['sex'],
            	'home_code'		=> $data['home_code']
            ];
            $homedata = model('Home')->where($where)->find();
            if ($homedata == null) {
            	fail('请仔细检查发布内容！');
            }
            if(empty($data['compensation'])){
            	$data['compensation'] = 0;
            }
            if ($data['type'] == 0) {
            	$insert = [
		           	'pay_status'	=> 0,
		           	'invoice_status'	=> 1,
		           	'title'			=> $data['title'],
		           	'type'			=> $data['type'],
		           	'user_name'		=> $homedata['owner'],
		           	'district_id'	=> $homedata['district_id'],
		           	'district_name'	=> $homedata['district_name'],
		           	'complex'		=> $homedata['complex'],
		           	'home_code'		=> $homedata['home_code'],
		           	'home_id'		=> $homedata['home_id'],
		           	'home_name'		=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
		           	'paytime'		=> $data['paytime'],
		           	'fee'			=> $data['wfee'],
		           	'compensation'	=> $data['compensation'],
		           	'voucher'		=> $data['voucher'],
		           	'start_at'		=> strtotime($data['start_at']),
		           	'end_at'		=> strtotime($data['end_at']),
		           	'status'		=> 1,
		           	'ctime'		=> time(),
		           	'month'	=> $data['month'],
		           	'cost'	=> $data['wcost'],
		           	'money'	=> sprintf("%1\$.2f", $data['wfee']*$homedata['area']*$data['month']+$data['wcost']-$data['compensation']),
	           ];
            } else {
            	$insert = [
            		'pay_status'	=> 0,
            		'invoice_status'	=> 1,
            		'title'			=> $data['title'],
            		'type'			=> $data['type'],
            		'user_name'		=> $homedata['owner'],
            		'district_id'	=> $homedata['district_id'],
            		'district_name'	=> $homedata['district_name'],
            		'complex'		=> $homedata['complex'],
            		'home_code'		=> $homedata['home_code'],
            		'home_id'		=> $homedata['home_id'],
            		'home_name'		=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
            		'paytime'		=> $data['paytime'],
            		'fee'			=> $data['gfee'],
            		'compensation'	=> $data['compensation'],
            		'voucher'		=> $data['voucher'],
            		'start_at'		=> strtotime($data['start_at']),
            		'end_at'		=> strtotime($data['end_at']),
            		'status'		=> 1,
            		'ctime'		=> time(),
            		'cost'		=> $data['gcost'],
            		'money'		=> sprintf("%1\$.2f", $data['gfee']*$homedata['area']*$data['gcost']-$data['compensation'])
            	];
            }
	        $info=model('Orders')->allowField(true)->save($insert);
	          if($info){
	          	 $this -> addLog('添加了一个新的缴费订单');
	            win('发布成功');
	        }else{
	            fail('发布失败');
	        }
	        exit;

            $orderWhere = [
            	'home_id'	=> $homedata['home_id'],
            	'type'		=> $data['type'],
            ];
            $orderData = model('Orders')->where($orderWhere)->select()->toArray();
            if (!empty($orderData)) {
            	$orederUpdate = [
            		'title'			=> $data['title'],
            		'type'			=> $data['type'],
            		'user_name'		=> $homedata['owner'],
            		'district_id'	=> $homedata['district_id'],
            		'district_name'	=> $homedata['district_name'],
            		'complex'		=> $homedata['complex'],
            		'home_code'		=> $homedata['home_code'],
            		'home_id'		=> $homedata['home_id'],
            		'home_name'		=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
            		'paytime'		=> $data['paytime'],
            		'fee'			=> $data['fee'],
            		'compensation'	=> $data['compensation'],
            		'voucher'		=> $data['voucher'],
            		'start_at'		=> strtotime($data['start_at']),
            		'end_at'		=> strtotime($data['end_at']),
            	];
            	$orderInfo = model('Orders')->where($orderWhere)->setField($orederUpdate);
            	if ($orderInfo) {
            		 $this -> addLog('添加了一个新的缴费订单');
            		win('发布成功');
            	} else {
            		fail('发布失败');
            	}
            } else {
            	$insert = [
            	'pay_status'	=> 0,
            	'invoice_status'	=> 1,
            	'title'			=> $data['title'],
            	'type'			=> $data['type'],
            	'user_name'		=> $homedata['owner'],
            	'district_id'	=> $homedata['district_id'],
            	'district_name'	=> $homedata['district_name'],
            	'complex'		=> $homedata['complex'],
            	'home_code'		=> $homedata['home_code'],
            	'home_id'		=> $homedata['home_id'],
            	'home_name'		=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
            	'paytime'		=> $data['paytime'],
            	'fee'			=> $data['fee'],
            	'compensation'	=> $data['compensation'],
            	'voucher'		=> $data['voucher'],
            	'start_at'		=> strtotime($data['start_at']),
            	'end_at'		=> strtotime($data['end_at']),
            	'status'		=> 1,
            	'ctime'		=> time(),
            ];
		        $info=model('Orders')->allowField(true)->save($insert);
		          if($info){
		          	 $this -> addLog('添加了一个新的缴费订单');
		            win('发布成功');
		        }else{
		            fail('发布失败');
		        }
            }   
        }else{
        	
        	//查看管理员有物业还是供暖权限
        	$user = session('admin');

        	$where = [];
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
        		
        		$this->assign('ro',$ro['type']);
        	} else {
        		$this->assign('ro',3);
        	}


        	

        	$district=Db::table("district")->field("id,name")->where($where)->order('id','asc')->select();
        	$this->assign('dis',$district);


        	
            return view();
        }
	}


	public function paymentUpdateInfo()
	{
		$id = input();
		 if(empty($id)){
	        exit('非法操作此页面');
	    }
	    $where=[
	        'id'=>$id['id']
	    ];
	    $data=model('Orders')->where($where)->find();
	    $data['start_at'] = date('Y-m-d',$data['start_at']);
	    $data['end_at'] = date('Y-m-d',$data['end_at']);
	 //    $district = model('District')->select()->toArray();
		// $this->assign('dis',$district);
	    $this->assign('data',$data);
		$this->assign('page',$id['page']);

	    $wheres = [];
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
	    		$wheres['id'] = array('in',$d_id);
	    	}
	    	
	    	$this->assign('ro',$ro['type']);
	    } else {
	    	$this->assign('ro',3);
	    }




	    
	    // $roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
	    // $ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
	    // $this->assign('ro',$ro);

	    // $ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
	    
	    // $d_id = [];
	    // if (!empty($ddata)) {
	    // 	foreach ($ddata as $k => $v) {
	    // 		$d_id[] = $v['d_id'];
	    // 	}

	    // 	$wheres['id'] = array('in',$d_id);
	    // }

	    $district=Db::table("district")->field("id,name")->where($wheres)->order('id','asc')->select();
	    $this->assign('dis',$district);


	    return view();
	}

	public function paymentUp()
	{
		$data = input();
		if (empty($data)) {
			fail('非法操作此页面');
		}
		  $where = [
            	'district_id'	=> $data['dis'],
            	'complex'		=> $data['complex'],
            	'home_code'		=> $data['home_code']
            ];
            $homedata = model('Home')->where($where)->find();
            if ($homedata == null) {
            	fail('请仔细检查发布内容！');
            }

            $updatewhere = [
				'id'	=> $data['id']
			];


			if(empty($data['compensation'])){
            	$data['compensation'] = 0;
            }
            if ($data['type'] == 0) {
            	$insert = [
		           	'invoice_status'	=> $data['invoice_status'],
		           	'title'			=> $data['title'],
		           	'type'			=> $data['type'],
		           	'user_name'		=> $homedata['owner'],
		           	'district_id'	=> $homedata['district_id'],
		           	'district_name'	=> $homedata['district_name'],
		           	'complex'		=> $homedata['complex'],
		           	'home_code'		=> $homedata['home_code'],
		           	'home_id'		=> $homedata['home_id'],
		           	'home_name'		=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
		           	'paytime'		=> $data['paytime'],
		           	'fee'			=> $data['wfee'],
		           	'compensation'	=> $data['compensation'],
		           	'voucher'		=> $data['voucher'],
		           	'start_at'		=> strtotime($data['start_at']),
		           	'end_at'		=> strtotime($data['end_at']),
		           	'month'	=> $data['month'],
		           	'cost'	=> $data['wcost'],
		           	'money'	=> sprintf("%1\$.2f", $data['wfee']*$homedata['area']*$data['month']+$data['wcost']-$data['compensation']),
	           ];
            } else {
            	$insert = [
            		'invoice_status'	=> $data['invoice_status'],
            		'title'			=> $data['title'],
            		'type'			=> $data['type'],
            		'user_name'		=> $homedata['owner'],
            		'district_id'	=> $homedata['district_id'],
            		'district_name'	=> $homedata['district_name'],
            		'complex'		=> $homedata['complex'],
            		'home_code'		=> $homedata['home_code'],
            		'home_id'		=> $homedata['home_id'],
            		'home_name'		=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
            		'paytime'		=> $data['paytime'],
            		'fee'			=> $data['gfee'],
            		'compensation'	=> $data['compensation'],
            		'voucher'		=> $data['voucher'],
            		'start_at'		=> strtotime($data['start_at']),
            		'end_at'		=> strtotime($data['end_at']),
            		'cost'		=> $data['gcost'],
            		'money'		=> sprintf("%1\$.2f", $data['gfee']*$homedata['area']*$data['gcost']-$data['compensation'])
            	];
            }
			$orderInfo = model('Orders')->where($updatewhere)->setField($insert);
			if ($orderInfo) {
				 $this -> addLog('修改了一个缴费订单');
				win('修改成功');
			} else {
				fail('修改失败');
			}


			dump($insert);exit;
            if (isset($data['pay_status'])) {
            	if ($data['pay_status'] == 0) {
            		$update = [
            			'invoice_status'	=> $data['invoice_status'],
            			'title'				=> $data['title'],
            			'type'				=> $data['type'],
            			'district_id'		=> $data['dis'],
            			'user_name'			=> $homedata['owner'],
            			'district_name'		=> $homedata['district_name'],
            			'complex'			=> $data['complex'],
            			'home_code'			=> $data['home_code'],
            			'home_id'			=> $homedata['home_id'],
            			'home_name'			=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
            			'paytime'			=> $data['paytime'],
            			'fee'				=> $data['fee'],
            			'compensation'		=> $data['compensation'],
            			'voucher'			=> $data['voucher'],
            			'start_at'			=> strtotime($data['start_at']),
            			'end_at'			=> strtotime($data['end_at']),
            		];
            	} else {
            		$update = [
            			'invoice_status'	=> $data['invoice_status'],
            			'title'				=> $data['title'],
            			'type'				=> $data['type'],
            			'district_id'		=> $data['dis'],
            			'user_name'			=> $homedata['owner'],
            			'district_name'		=> $homedata['district_name'],
            			'complex'			=> $data['complex'],
            			'home_code'			=> $data['home_code'],
            			'home_id'			=> $homedata['home_id'],
            			'home_name'			=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
            			'paytime'			=> $data['paytime'],
            			'fee'				=> $data['fee'],
            			'compensation'		=> $data['compensation'],
            			'voucher'			=> $data['voucher'],
            			'start_at'			=> strtotime($data['start_at']),
            			'end_at'			=> strtotime($data['end_at']),
            			'order_no'			=> date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT),
            			'pay_status'		=> 1,
            			'pay_type'			=> 3,
            			'pay_fee'			=> $data['money'],
            			'finish_at'			=> time(),
            		];
            	}
            } else {
            	$update = [
				'invoice_status'	=> $data['invoice_status'],
				'title'				=> $data['title'],
				'type'				=> $data['type'],
				'district_id'		=> $data['dis'],
				'user_name'			=> $homedata['owner'],
				'district_name'		=> $homedata['district_name'],
				'complex'			=> $data['complex'],
				'home_code'			=> $data['home_code'],
				'home_id'			=> $homedata['home_id'],
				'home_name'			=> $homedata['district_name'].$homedata['complex'].$homedata['home_code'],
				'paytime'			=> $data['paytime'],
				'fee'				=> $data['fee'],
				'compensation'		=> $data['compensation'],
				'voucher'			=> $data['voucher'],
				'start_at'			=> strtotime($data['start_at']),
				'end_at'			=> strtotime($data['end_at']),
			];
            }
            // 'month'	=> $data['month'],
            // 'cost'	=> $data['wcost'],
            // 'money'	=> sprintf("%1\$.2f", $data['wfee']*$homedata['area']*$data['month']+$data['wcost']-$data['compensation']),
            dump($update);exit;
            $orderInfo = model('Orders')->where($updatewhere)->setField($update);
            if ($orderInfo) {
            	 $this -> addLog('修改了一个缴费订单');
            	win('修改成功');
            } else {
            	fail('修改失败');
            }
	}
    public function paysel()
    {

    }
	public function paymentUps()
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

	/**
	 * 退款
	 * @return [type] [description]
	 */
	public function orderRefund()
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
    			'refundAmt'	=> $pay_fee,
    			// 'refundAmt'	=> '000000000001',
    			'payType'	=> $data['payTypes'],
    			'refundOrderNo'	=> $order_no_r,
    		];
    		$signWhere = [
    			'orgNo'	=> $newtemp['orgNo'],
    			// 'termNo'	=> $newtemp['termNo'],
    			'termNo'	=> 'XA026454',
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
    				fail('退款失败');
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
    			$data['pay_fee']=str_replace('.', '', $data['pay_fee']);
    			$lens = strlen($data['pay_fee']);
    			// $data['deposit_money']=sprintf("%012d", $data);//生成12位数，不足前面补0   
    			if ($lens < 12) {
    				for ($i=0; $i < 12-$lens ; $i++) { 
    					$data['pay_fee'] = substr_replace(0, $data['pay_fee'], 1, 0);
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
    				'amt'		=> intval($data['pay_fee']),
    				'payType'	=> 1
    			];


    			$signdata = [
    				'orgNo'	=>'2166',
    				'amt'	=> intval($data['pay_fee']),
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

		// $home = model('Home')->where(['home_id'=>$data['home_id']])->find()->toArray();
		// $pay_money = $home['area'] * $data['fee'] - $data['compensation'];
		$pay_money = sprintf("%1\$.2f", $data['money']);
		$pay_money=str_replace('.', '', $pay_money);
		$lens = strlen($pay_money);
		// $data['deposit_money']=sprintf("%012d", $data);//生成12位数，不足前面补0   
		if ($lens < 12) {
			for ($i=0; $i < 12-$lens ; $i++) { 
				$pay_money = substr_replace(0, $pay_money, 1, 0);
			}
		}
		$order_no = date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT);

		$res = model('Orders')->where($where)->setField(['order_no'=>$order_no]);
		if (!$res) {
			fail('支付失败，请重新尝试！');
		}
		$newData = [
			'order_no'	=> $order_no,
			'deposit_money'	=> $pay_money
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

		         $font = ['img'=>$imageString,'order_no'=>$order_no];
				 echo json_encode(['font' => $font, 'code' => 1]);
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

    public function appgetSign($data,$secret)
    {
    	header('Content-type: text/html; charset=utf-8');
    	// $arr = model('Applets')->find();
    	// if ($arr == null) {
    	// 	return '';
    	// }
    	// $secret = $arr['secret'];
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
	 * 计算金额.
	 * @return [type] [description]
	 */
	public function sprintfs($data)
	{
		return sprintf("%1\$.2f", $data);
	}
}