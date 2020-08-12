<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 缴费订单.
 */
class Orderno extends Common
{
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
	        $where = [];

	        //查看管理员有物业还是供暖权限
	        $user = session('admin');
	        if ($user['admin_type'] != 1) {
	        	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
	        	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
	        	
	        	if ($ro['type'] == 1) {
	        		$where['type'] = 0; //物业
	        	} else if ($ro['type'] == 2) {
	        		$where['type'] = 1; //供暖
	        	} else {
	        		$where = [
	        			'type'	=> array('in',[0,1])
	        		];	
	        	}

	        	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
	        	$d_id = [];
	        	if (!empty($ddata)) {
	        		foreach ($ddata as $k => $v) {
	        			$d_id[] = $v['d_id'];
	        		}

	        		$where['district_id'] = array('in',$d_id);
	        	}
	        } else {
	        	$where = [
	        		'type'	=> array('in',[0,1])
	        	];	
	        }


	       $data = model('Orders')->order('id','desc')->where($where)->page($page,$limit)->select()->toArray();
	       $count=model('Orders')->where($where)->count();
	       $i= ($page-1) * ($limit);
	       // $ii = $i+1;
	       foreach ($data as $k => $v) {
	       	
	        $i++;
	        $data[$k]['num'] = $i;
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
		if ($info['type'] == 0) {
			$str = '物业费';
		} else {
			$str = '供暖费';
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

		$payment = model('Payment')->where(['p_id'=>$info['p_id']])->find();
		if ($payment == null) {
			$title = '';
		} else {
			$title = $payment['content'];
		}
		$admin = session('admin');
		$strs = model('System')->find();
		if ($strs == null) {
			$titles = '测试订单';
		} else {
			$titles = $strs['company_name'];
		}
		// $DEVICE_NO = 'kdt1078615';
		// $key = '72264';
		$content = "    .... #小码旺铺订单# ....\n";

		$content .= "\n";
		$content .= "\n";
		
		$content .= "^B2    收据凭证\n";
		$content .= "\n";
		$content .= "物业存根             请妥善保管\n";
		$content .= "--------------------------------\n";
		$content .= "商户名称：".$titles."\n";
		$content .= "商户编号：2020070701\n";
		$content .= "终端编号：\n";
		$content .= "操作员：".$admin['admin_name']."\n";
		$content .= "--------------------------------\n";
		$content .= "^H1订单编号：".$info['order_no']."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
		$content .= "^H2缴费类型：".$str."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "支付时间：".date('Y.m.d H:i:s',$info['finish_at'])."\n";
		$content .= "支付方式：".$pay_type."\n";
		$content .= "--------------------------------\n";
		$content .= "备注：".$title."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "^H2缴费金额:";
		$content .= $info['pay_fee'];
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
		$content .= "终端编号：\n";
		$content .= "操作员：".$admin['admin_name']."\n";
		$content .= "--------------------------------\n";
		$content .= "^H1订单编号：".$info['order_no']."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
		$content .= "^H2缴费类型：".$str."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "支付时间：".date('Y.m.d H:i:s',$info['finish_at'])."\n";
		$content .= "支付方式：".$pay_type."\n";
		$content .= "--------------------------------\n";
		$content .= "备注：".$title."\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "^H2缴费金额:";
		$content .= $info['pay_fee'];
		$content .= "\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "\n";
		$content .= "...#由小码旺铺提供技术支持#...\n";
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


	public function sendSelfFormatOrderInfo($device_no,$key,$times,$orderInfo)
	{
			$selfMessage = array(
				'deviceNo'=>$device_no,  
				'printContent'=>$orderInfo,
				'key'=>$key,
				'times'=>$times
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
			
			return $result;
		}


	#查询订单是否支付.
	public function orderSel()
	{
		$order = input('order');
		if (empty($order)) {
			return json(['font' => '非法操作此页面', 'code' => 2]);
			exit;
		}
		$where = [
			'order_no'	=> $order
		];

		$data = model('Orders')->where($where)->find();
		if ($data == null) {
			return json(['font' => '没有找到您的订单', 'code' => 2]);
			exit;
		}




		if ($data['pay_status'] == 1) {
			$home = model('Home')->where(['home_id'=>$data['home_id']])->find();

			if ($home == null) {
				fail('没有查到您的数据');
			}


			if ($data['type'] == 2) {
				if ($data['pay_type'] == 1) {
					$pay_type = '小程序支付';
				} else {

					if ($data['payTypes'] == 0) {
						$pay_type = '订单支付 -- 刷卡支付';	
					} else if ($data['payTypes'] == 1) {
						$pay_type = '订单支付 -- 微信支付';
					} else if ($data['payTypes'] == 2) {
						$pay_type = '订单支付 -- 支付宝支付';
					} else if ($data['payTypes'] == 3) {
						$pay_type = '订单支付 -- 现金支付';
					} else if ($data['payTypes'] == 4) {
						$pay_type = '订单支付 -- 云闪付支付';
					}
				}

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
				$content .= "操作员号：\n";
				$content .= "--------------------------------\n";
				$content .= "订单编号：".$data['order_no']."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
				$content .= "^H2缴费类型：押金\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "支付时间：".date('Y.m.d H:i:s',$data['finish_at'])."\n";
				$content .= "支付方式：".$pay_type."\n";
				
				$content .= "--------------------------------\n";
				
				// $content .= "^H2联系方式：".$home['tel']."\n";
				
				$content .= "^H1收费明细：".$data['cost_info']."\n";
				$content .= "--------------------------------\n";
				$content .= "^H1可退金额：".$data['refund_money']."\n";
				$content .= "--------------------------------\n";
				$content .= "^H1备注：".$data['title']."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "^H2交易金额:".$data['pay_fee'];
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
				$content .= "操作员号：\n";
				$content .= "--------------------------------\n";
				$content .= "订单编号：".$data['order_no']."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
				$content .= "^H2收费类型：押金\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "支付时间：".date('Y.m.d H:i:s',$data['finish_at'])."\n";
				$content .= "支付方式：".$pay_type."\n";
				
				$content .= "--------------------------------\n";
				
				// $content .= "^H2联系方式：".$home['tel']."\n";
				
				$content .= "^H1收费明细：".$data['cost_info']."\n";
				$content .= "--------------------------------\n";
				$content .= "^H1可退金额：".$data['refund_money']."\n";
				$content .= "--------------------------------\n";
				$content .= "^H1备注：".$data['title']."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "^H2交易金额:".$data['pay_fee'];
				$content .= "\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "...#由小码旺铺提供技术支持#...\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "\n";
				
			} else {


				if ($data['type'] == 0) {
					$str = '物业费';
				} else {
					$str = '供暖费';
				}

				if ($data['pay_type'] == 1) {
					$pay_type = '小程序支付';
				} else {

					if ($data['payTypes'] == 0) {
						$pay_type = '订单支付 -- 刷卡支付';	
					} else if ($data['payTypes'] == 1) {
						$pay_type = '订单支付 -- 微信支付';
					} else if ($data['payTypes'] == 2) {
						$pay_type = '订单支付 -- 支付宝支付';
					} else if ($data['payTypes'] == 3) {
						$pay_type = '订单支付 -- 现金支付';
					} else if ($data['payTypes'] == 4) {
						$pay_type = '订单支付 -- 云闪付支付';
					}
					
				}

				$payment = model('Payment')->where(['p_id'=>$data['p_id']])->find();
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
				// $DEVICE_NO = 'kdt1078615';
				// $key = '72264';
				$content = "    .... #小码旺铺订单# ....\n";

				$content .= "\n";
				$content .= "\n";
				
				$content .= "^B2    收据凭证\n";
				$content .= "\n";
				$content .= "物业存根             请妥善保管\n";
				$content .= "--------------------------------\n";
				$content .= "商户名称：".$titles."\n";
				$content .= "商户编号：2020070701\n";
				$content .= "终端编号：\n";
				$content .= "操作员号：\n";
				$content .= "--------------------------------\n";
				$content .= "^H1订单编号：".$data['order_no']."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
				$content .= "^H2缴费类型：".$str."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "支付时间：".date('Y.m.d H:i:s',$data['finish_at'])."\n";
				$content .= "支付方式：".$pay_type."\n";
				$content .= "--------------------------------\n";
				$content .= "备注：".$title."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "^H2缴费金额:";
				$content .= $data['pay_fee'];
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
				$content .= "终端编号：\n";
				$content .= "操作员号：\n";
				$content .= "--------------------------------\n";
				$content .= "^H1订单编号：".$data['order_no']."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
				$content .= "^H2缴费类型：".$str."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "支付时间：".date('Y.m.d H:i:s',$data['finish_at'])."\n";
				$content .= "支付方式：".$pay_type."\n";
				$content .= "--------------------------------\n";
				$content .= "备注：".$title."\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "^H2缴费金额:";
				$content .= $data['pay_fee'];
				$content .= "\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "\n";
				$content .= "...#由小码旺铺提供技术支持#...\n";



			}






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
			return json(['font' => '缴费成功', 'code' => 1]);
			exit;
		} else {
			return json(['font' => '', 'code' => 3]);
			exit;
		}
	}


	/**
	 * 历史订单页面.
	 * @return [type] [description]
	 */
	public function historyPaymentList()
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
		        $where = [];

		        //查看管理员有物业还是供暖权限
		        $user = session('admin');
		        if ($user['admin_type'] != 1) {
		        	$roles = model('admin_role')->where(['admin_id'=>$user['admin_id']])->find()->toArray();
		        	$ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();
		        	
		        	if ($ro['type'] == 1) {
		        		$where['type'] = 0; //物业
		        	} else if ($ro['type'] == 2) {
		        		$where['type'] = 1; //供暖
		        	} else {
		        		$where = [
		        			'type'	=> array('in',[0,1])
		        		];	
		        	}

		        	$ddata = model('RoleDistrict')->where(['r_id'=>$ro['role_id']])->select()->toArray(); //小区权限
		        	$d_id = [];
		        	if (!empty($ddata)) {
		        		foreach ($ddata as $k => $v) {
		        			$d_id[] = $v['d_id'];
		        		}

		        		$where['district_id'] = array('in',$d_id);
		        	}
		        } else {
		        	$where = [
		        		'type'	=> array('in',[0,1])
		        	];	
		        }


		       $data = model('Orders')->order('id','desc')->where($where)->page($page,$limit)->select()->toArray();
		       $count=model('Orders')->where($where)->count();
		       $i= ($page-1) * ($limit);
		       // $ii = $i+1;
		       foreach ($data as $k => $v) {
		       	
		        $i++;
		        $data[$k]['num'] = $i;
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


	/**
	 * 上传历史订单页面.
	 * @return [type] [description]
	 */
	public function historyUpload()
	{

			  if(request()->isPost()){
			    $file = request()->file('file');        // 获取表单提交过来的文件  
			    $error = $_FILES['file']['error'];  // 如果$_FILES['file']['error']>0,表示文件上传失败  
			    if(!$error){  
			      $dir = ROOT_PATH . 'public' . DS . 'upload';
			   
			      // 验证文件并移动到框架应用根目录/public/uploads/ 目录下
			      $info = $file->validate(['size'=>3145728,'ext'=>'xls,xlsx,csv'])->rule('uniqid')->move($dir);  
			      /*判断是否符合验证*/  
			      if($info){    //  符合类型  
			        $file_type = $info->getExtension();  
			        $filename = $dir. DS .$info->getSaveName();
			        // $filename = 'E:\PHPserver\wwwroot\default\aliyun_1805\public\upload\5ee87c7f0c9be.xlsx';
			        Vendor("PHPExcel.IOFactory");
			         $extension = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
			        if ($extension =='xlsx') {
		                $objReader = new \PHPExcel_Reader_Excel2007();
		                $objExcel = $objReader ->load($filename);

		            } else if ($extension =='xls') {

		                $objReader = new \PHPExcel_Reader_Excel5();
		                $objExcel = $objReader->load($filename);


		            }

		            $excel_array=$objExcel->getsheet(0)->toArray();   //转换为数组格式
		            array_shift($excel_array);
		            $title = $excel_array[0];
		            array_shift($excel_array);
			    	$arr = [];
			    	foreach ($excel_array as $k => $v) {
			    		$arr[] =array_combine($title,$v);
			    	}
			    	$data = [];
			    	foreach ($arr as $key => $value) {
			    		if (!isset($value['小区名称'])) {
			    			return fail('缺少小区名称标题');
			    		}

			    		if (!array_key_exists('楼/单元/户号',$value)) {
			    			return fail('缺少楼/单元/户号标题');
			    		}
			    		if (!array_key_exists('房主姓名',$value)) {
			    			return fail('缺少房主姓名标题');
			    		}

			    		if (!array_key_exists("缴费类别",$value))
			    		{
			    		  return fail('缺少缴费类别标题');
			    		}
			    		
			    		if (!array_key_exists('收费年',$value)) {
			    			return fail('缺少收费年标题');
			    		}
			    		
			    		if (!array_key_exists('欠费金额',$value)) {
			    			return fail('缺少欠费金额标题');
			    		}
			    		if (!array_key_exists('欠费户数',$value)) {
			    			return fail('缺少欠费户数标题');
			    		}
			    		if (!array_key_exists('赔偿', $value)) {
			    			return fail('缺少赔偿标题');
			    		}
			    		if (!array_key_exists('凭证号', $value)) {
			    			return fail('缺少凭证号标题');
			    		}
			    		if (!array_key_exists('区', $value)) {
			    			return fail('缺少区标题');
			    		}
			    		if ($value['欠费户数'] != 0) {
			    						$where = [
			    							'name'	=> $value['小区名称']
			    						];
			    						$district = model('District')->where($where)->find();
			    						if ($district ==null) {
			    							return fail('请检查您的小区名称，没有找到小区');
			    						} else {
			    							$id = $district->id;
			    						}

			    						$homewhere = [
			    							'district_id'	=> $id,
			    							'complex'		=> $value['区'],
			    							'home_code'		=> $value['楼/单元/户号'],
			    							'owner'			=> $value['房主姓名']
			    						];

			    						$homeInfo = model('Home')->where($homewhere)->find();
			    						if ($homeInfo == null) {
			    							return fail('请检查您的录入信息，没有找到您的房子!');
			    						} else {
			    							$homeId = $homeInfo->home_id;
			    						}

			    			    		$newarr = explode('-', $value['楼/单元/户号']);
			    			    		
			    			    		
			    			    		$count = count($newarr);
			    			    		if ($count == 3) {
			    			    			$building = $newarr[0];
			    			    			$unit = $newarr[1];
			    			    			$room = $newarr[2];
			    			    		} else {
			    			    			$building = $newarr[0];
			    			    			$unit = '';
			    			    			$room = $newarr[1];
			    			    		}

			    			    		if ($value['缴费类别'] == '物业') {
			    			    			$type = 0;
			    			    		} else {
			    			    			$type = 1;
			    			    		}

			    			    		$data[] = [
			    			    			'pay_status'	=> 0,
			    			    			'invoice_status'	=> 1,
			    			    			'type'	=> $type,
			    			    			'user_name'	=> $value['房主姓名'],
			    			    			'district_id'	=> $id,
			    			    			'district_name'	=> $value['小区名称'],
			    			    			'complex'		=> $value['区'],
			    			    			'home_code'		=> $value['楼/单元/户号'],
			    			    			'home_id'		=> $homeId,
			    			    			'home_name'		=> $value['小区名称'].$value['区'].$value['楼/单元/户号'],
			    			    			'paytime'		=> $value['收费年'],
			    			    			// 'fee'			=> $data['fee'],
			    			    			'compensation'	=> $value['赔偿'],
			    			    			'voucher'		=> $value['凭证号'],
			    			    			// 'start_at'		=> strtotime($data['start_at']),
			    			    			// 'end_at'		=> strtotime($data['end_at']),
			    			    			'status'		=> 1,
			    			    			'ctime'		=> time(),
			    			    			// 'p_id'		=> $id,
			    			    			// 'month'	=> $data['month'],
			    			    			// 'cost'	=> $data['cost'],
			    			    			'money'	=> sprintf("%1\$.2f", $value['欠费金额']),

			    			    		];


			    			    		// $data[] = [
			    			    		// 	'home_ids'		=> date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT),
			    			    		// 	'district_name'	=> $value['小区名称'],
			    			    		// 	'complex'		=> $value['区'],
			    			    		// 	'district_id'	=> $id,
			    			    		// 	'home_code'		=> $value['楼/单元/户号'],
			    			    		// 	'owner'		=> $value['房主姓名'],
			    			    		// 	'tel'		=> $value['联系方式'],
			    			    		// 	'area'		=> $value['面积'],
			    			    		// 	'check_in_at' => $value['入住时间'],
			    			    		// 	'is_delete'	=> 1,
			    			    		// 	'building' 	=> $building,
			    			    		// 	'unit' 		=> $unit,
			    			    		// 	'room' 		=> $room,
			    			    		// 	'ctime'		=> time()
			    			    		// ];
			    		}
			    		
						
			    	}

			    	
			    	$res = model('Orders') -> insertAll( $data );

			    	if ($res < 1) {
			    		fail('导入失败');
			    	} else {
			    		win('导入成功');
			    	}

			      } else{ //  不符合类型业务  
			        $this->error('请选择上传3MB内的excel表格文件...');  
			        //echo $file->getError();  
			      }  
			    }else{  
			      $this->error('请选择需要上传的文件...');  
			    }  

			  }  

	}
}