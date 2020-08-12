<?php
namespace app\index\controller;
use think\Controller;
class Order extends Controller
{
	
	/**
	 * 订单号查询
	 * @return [type] [description]
	 */
	public function orderSel()
	{
		$data = input();
		
		$merId = $data['merId'];
		$SN = $data['SN'];
		unset($data['merId']);
		unset($data['SN']);
		$res = $this->verifySign($data);
		if ($res['code'] !=0) {
			return $this->reply($res['code'],$res['msg']);
		}
		$posWhere = [
			'SN'	=> $SN,
			'merId'	=> $merId
		];
		$posRes = model('Posconfg')->where($posWhere)->find();
		if ($posRes == null) {
			return $this->reply(1,'没有找到您这台设备，请去后台添加设备！');
		}

		$where = [
			'order_no' => $data['order_no'],
			'status'	=> 1
		];
		$orderData = model('Orders')->where($where)->find();
		if ($orderData) {

			if ($orderData['type'] == 2) {
							$fee = $orderData['fee'];
							$newfee = $this->sprintfs($fee);
							$newfee=str_replace('.', '', $newfee);
				    		$len = strlen($newfee);
				    		// $data['deposit_money']=sprintf("%012d", $data);//生成12位数，不足前面补0   
				    		if ($len < 12) {
				    			for ($i=0; $i < 12-$len ; $i++) {
				    				$newfee = substr_replace(0, $newfee, 1, 0);
				    			}
				    		}
							$temp = [
								'order_no'	=> $orderData['order_no'],
								'user_name'	=> $orderData['user_name'],
								'home_id'	=> $orderData['home_id'],
								'home_name'	=> $orderData['home_name'],
								'deposit_status'	=> $orderData['pay_status'],
								'deposit_money'		=> $newfee,
								'cost_info'			=> $orderData['cost_info'],
							];
							return json(['msg'=>'成功','code'=>0,'data'=>$temp]);

			} else {
					// $home = model('Home')->where(['home_id'=>$orderData['home_id']])->find();

					// $fee = $orderData['fee'] * $home->area;
					// $newfee = $fee - $orderData['compensation'];
					
					$newfee = $this->sprintfs($orderData['money']);
					$newfee=str_replace('.', '', $newfee);

		    		$len = strlen($newfee);
		    		// $data['deposit_money']=sprintf("%012d", $data);//生成12位数，不足前面补0   
		    		if ($len < 12) {
		    			for ($i=0; $i < 12-$len ; $i++) {
		    				$newfee = substr_replace(0, $newfee, 1, 0);
		    			}
		    		}
					$temp = [
						'order_no'	=> $orderData['order_no'],
						'user_name'	=> $orderData['user_name'],
						'home_id'	=> $orderData['home_id'],
						'home_name'	=> $orderData['home_name'],
						'deposit_status'	=> $orderData['pay_status'],
						'deposit_money'		=> $newfee,
						'cost_info'			=> $orderData['cost_info'],
					];
					return json(['msg'=>'成功','code'=>0,'data'=>$temp]);
			}
		} else {
			return $this->reply(1,'失败');
		}
	}

	/**
	 * 订单回调地址.
	 * @return [type] [description]
	 */
	public function posOrderNotify()
	{
		$data = input();
		if (empty($data)) {
			return $this->reply(1,'非法操作');
		}
		#验签
		$res = $this->verifySign($data);
		if ($res['code'] !=0) {
			return $this->reply($res['code'],$res['msg']);
		}
		
		if ($data['deposit_status'] == 1) {
			$where = [
				'order_no' => $data['order_no'],
				'status'	=> 1	
			];


	    	$var = floatval($data['deposit_money']);

	    	$var = $var / 100.00;
			$money = $this->sprintfs($var);
			$arr = [
				'pay_status'	=> $data['deposit_status'],
				'pay_type'		=> 2,
				'transNo'			=> $data['transNo'],
				'payTypes'			=> $data['payType'],
				'finish_at'			=> time(),
				'pay_fee'			=> $money,
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
					$content .= "订单编号：".$datainfo['order_no']."\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
					$content .= "^H2缴费类型：押金\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "支付时间：".date('Y.m.d H:i:s',$datainfo['finish_at'])."\n";
					$content .= "支付方式：".$pay_type."\n";
					
					$content .= "--------------------------------\n";
					
					// $content .= "^H2联系方式：".$home['tel']."\n";
					
					$content .= "^H1收费明细：".$datainfo['cost_info']."\n";
					$content .= "--------------------------------\n";
					$content .= "^H1可退金额：".$datainfo['refund_money']."\n";
					$content .= "--------------------------------\n";
					$content .= "^H1备注：".$datainfo['title']."\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "^H2交易金额:".$datainfo['pay_fee'];
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
					$content .= "订单编号：".$datainfo['order_no']."\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "^H2房屋信息:".$home['district_name'].$home['complex']."区".$home['home_code']."\n";
					$content .= "^H2收费类型：押金\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "支付时间：".date('Y.m.d H:i:s',$datainfo['finish_at'])."\n";
					$content .= "支付方式：".$pay_type."\n";
					
					$content .= "--------------------------------\n";
					
					// $content .= "^H2联系方式：".$home['tel']."\n";
					
					$content .= "^H1收费明细：".$datainfo['cost_info']."\n";
					$content .= "--------------------------------\n";
					$content .= "^H1可退金额：".$datainfo['refund_money']."\n";
					$content .= "--------------------------------\n";
					$content .= "^H1备注：".$datainfo['title']."\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "^H2交易金额:".$datainfo['pay_fee'];
					$content .= "\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "...#由小码旺铺提供技术支持#...\n";
					$content .= "\n";
					$content .= "\n";
					$content .= "\n";

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
			}else{
			    return $this->reply(1,'支付失败');
			}
		} else {

			$where = [
				'order_no' => $data['order_no'],
				'status'	=> 1	
			];
			

			$arr = [
				'pay_status'	=> $data['deposit_status'],
				'refundOrderNo'		=> $data['transNo'].rand(1111,9999),
			];

			$info = model('Orders')->where($where)->setField($arr);
			if($info){
			    return $this->reply(0,'退款成功');
			}else{
			    return $this->reply(1,'退款失败');
			}
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
	    // 生成sign
	    $sign = md5($params);
	    return $sign;
	}

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
		    unset($data['returnCode']);
		    unset($data['returnMsg']);
		    ksort($data);
		    $param = http_build_query($data);
		    $str = urldecode($param); //解码
		    $params = $str.$secret;
		    // $secret是通过key在api的数据库中查询得到
		    $sign2 = md5($params);
		   
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
	 * 微信支付接口.
	 * @return [type] [description]
	 */
	public function wxPayment()
	{
		$data = input();
		if (empty($data['id'])) {
			return $this->reply(1,'非法请求');
		}
		$data['money']=str_replace('.', '', $data['money']);
		$lens = strlen($data['money']);
		// $data['deposit_money']=sprintf("%012d", $data);//生成12位数，不足前面补0   
		if ($lens < 12) {
			for ($i=0; $i < 12-$lens ; $i++) { 
				$data['money'] = substr_replace(0, $data['money'], 1, 0);
			}
		}
		$time = date('YmdHis',time());
		$order_no_r = '';
		$arr = model('Orders')->where(['id'=>$data['id'],'status'=>1])->find();
		$titleMsg = '';
		// return $this->reply(0,'聪哥',$arr);
		if ($arr == null) {
			return $this->reply(1,'操作失败，请联系工作人员！');
		} else {
			
				$orders = date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT);
				$info = model('Orders')->where(['id'=>$data['id']])->setField(['order_no'=>$orders]);
				if ($info) {
					$order_no_r = $orders;
				} else {
					return $this->reply(1,'操作失败，请联系工作人员！');
				}

				if($arr['type'] == 0){
					$titleMsg = '小码旺铺微信支付-物业缴费';
				} else if ($arr['type'] == 1) {
					$titleMsg = '小码旺铺微信支付-供暖缴费';
				} else {
					$titleMsg = '小码旺铺微信支付-押金缴费';
				}
			
		}
		$app = model('Applets')->select()->toArray();
		//供暖是调金立的秘钥，剩下调我们的。
		// if ($arr['type'] == 1) {
		// 	$secret = $app[1]['secret'];
		// 	$termNo = $app[1]['termNo'];
		// 	$merId = $app[1]['merId'];
		// } else {
			$secret = $app[0]['secret'];
			$termNo = $app[0]['termNo'];
			$merId = $app[0]['merId'];
		// }
		// $secret = $app[1]['secret'];
		// $termNo = $app[1]['termNo'];
		// $merId = $app[1]['merId'];

		$temp = [
			'orgNo'	=> '2166',
			'charset'	=> 'UTF-8',
			'termNo'	=> $termNo, //设备号
			'termType'	=> 'KCWMP',
			'txtTime'	=> $time,
			'signType'	=> 'MD5',
			// 'signValue'	=> '',http://notify.yhyrpay.com/Order/posOrderNotify 
			'transNo'	=> $order_no_r,
			'merId'		=> $merId, //商户号
			'amt'		=>intval($data['money']), //交易金额
			'subject'	=> $titleMsg, //标题
			'backUrl'	=> 'http://notify.yhyrpay.com/Order/wxOrderNotify', //异步地址
			'payType'	=> '1', //支付方式
			'ip'		=> $_SERVER['REMOTE_ADDR'], //服务端ip
			// 'appId'		=> 'wxe3f1dbde286a46d6',
			'appId'		=> 'wx0731f3694a3fb5ba',
			'openId'	=> $data['open_id'],
		];
		$signdata = [
			'orgNo'	=>'2166',
			'amt'	=> intval($data['money']),
			'termNo'=> $termNo,
			'merId'	=> $merId,
			'transNo'	=> $order_no_r,
			'txtTime'	=> $time
		];
		$sign = $this->wxGetSign($signdata,$secret);
		$temp['signValue'] = strtoupper($sign);
		$url = 'http://yhyr.com.cn/YinHeLoan/yinHe/yinHeWmpPay.action';
		$res = $this->sendpostss($url,$temp);
		if ($res['returnCode'] == 0000) {
			$temps = [
				'appId'	=> $res['appId'],
				'timeStamp'	=> $res['timeStamp'],
				'nonceStr'	=> $res['nonceStr'],
				'package'	=> $res['package'],
				'signType'	=> $res['signType'],
				'paySign'	=> $res['paySign'],
				'transNo'	=> $order_no_r,
			];
			return $this->reply(0,'成功',$temps);
		} else {
			return $this->reply(1,$res['returnMsg']);
		}
	}


	/**
	 * wx异步回调地址.
	 * @return [type] [description]
	 */
	public function wxOrderNotify()
	{
		$data = input();
		if (empty($data)) {
			return $this->reply(1,'非法操作');
		}
		if ($data['returnCode'] == 0000) {


			$wheresign = [
				'order_no'	=> $data['transNo']
			];

			$datainfos = model('Orders')->where($wheresign)->find();
			if ($datainfos == null) {
				return $this->reply(1,'没有查到您的数据');
			}

			$app = model('Applets')->select()->toArray();
			// if ($datainfos['type'] == 1) {
			// 	$secret = $app[1]['secret'];
			// 	$termNo = $app[1]['termNo'];
			// 	$merId = $app[1]['merId'];
			// } else {
				$secret = $app[0]['secret'];
				$termNo = $app[0]['termNo'];
				$merId = $app[0]['merId'];
			// }
			#验签
			$res = $this->wxverifySign($data,$secret);
			if ($res['code'] !=0) {
				return $this->reply($res['code'],$res['msg']);
			}
			$where = [
				'order_no'	=> $data['transNo']
			];
			$temp = [
				'pay_status'	=> 1,
				'pay_type'		=> 1,
				'finish_at'		=> time(),
				'pay_fee'		=> $data['amt']
			];
			$info = model('Orders')->where($where)->update($temp);
			if ($info) {


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
				$content .= "终端编号：\n";
				$content .= "操作员号：\n";
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
		


		return $this->reply(0,'成功',$data);
		echo '微信异步回调';
	}

	// 生成微信sign
	function wxGetSign($data,$secret) {
		header('Content-type: text/html; charset=utf-8');
		// $secret = "e71af45c6ba018b21184a5348bfb26d1";
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


	function wxverifySign($data,$secret) {
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
		    // if (!isset($data['txtTime']) || !$data['txtTime']) {
		    // 	$font = [
		    // 		'code'=>1,
		    // 		'msg'	=> '发送的数据参数不合法'
		    // 	];
		    //     return $font;
		    //     die();
		    // }
		    // 验证请求， 10分钟失效
		    // if (time() - $data['time'] > 600) {
		    // 	$font = [
		    // 		'code'=>1,
		    // 		'msg'	=> '验证失效，请重新发送请求'
		    // 	];
		    //     return $font;
		    //     die();
		    // }
		    // $secret = "e71af45c6ba018b21184a5348bfb26d1";

		    // dump($data);exit;

		    $sign = $data['sign'];
		    unset($data['sign']);
		    ksort($data);
		    $param = http_build_query($data);
		    $str = urldecode($param); //解码
		    $params = $str.$secret;
		    $sign2 = strtoupper(md5($params));
		    // $secret是通过key在api的数据库中查询得到
		   
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


	public function testCurl()
	{
		$data = [
			'deppsit_money'	=> '1.0',
			'order_no'		=> '2020062209185323440',
			'time'			=> '1592791794300',
			'sign'			=> 'cb23400b62122e96cb8f9e6c9dcd4cfe'
		];
		$url = 'http://notify.yhyrpay.com/order/orderSel';
		$res = $this->sendpostss($url,$data);
		dump($res);exit;
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