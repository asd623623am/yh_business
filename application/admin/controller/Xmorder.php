<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 订单管理
 */
class Xmorder extends Common
{	
	/**
	 * 今日订单.
	 * @return [type] [description]
	 */
	public function orderDay()
	{
		if( request() -> isAjax() ){
			$data = input();

			if (empty($data)) {
				fail('非法请求');
			}
			$where = [];

			if ($data['order_status'] != null) {
				$where['order_status'] = $data['order_status'];
			}

			if (!empty($data['pay_id'])) {
				$where['pay_id'] = $data['pay_id'];
			}

			if (!empty($data['tnumber'])) {
				$where['tnumber'] = $data['tnumber'];
			}
			if (!empty($data['price_min']) && !empty($data['price_max'])) {
				$where['pay_fee'] = ['between',[$data['price_min'],$data['price_max']]];

			}
			if(!empty($data['order_type'])){
				$where['order_type'] = $data['order_type'];
			}

			if($data['pay_status'] != null){
				$where['pay_status'] = $data['pay_status'];
			}

			if(!empty($data['pay_fee'])){
				$where['pay_fee'] = $data['pay_fee'];
			}
			if(!empty($data['create_time'])){
				$newstr = substr($data['create_time'],0,strlen($data['create_time'])-2);
				$start = $newstr.'00';
				$end = $newstr.'59';
				$where['create_time'] = ['between time',[$start,$end]];
			}
			if(!empty($data['pay_time'])){
				$newstrs = substr($data['pay_time'],0,strlen($data['pay_time'])-2);
				$starts = $newstrs.'00';
				$ends = $newstrs.'59';
				$where['pay_time'] = ['between time',[$starts,$ends]];
			}

			if(!empty($data['store'])){
				$where['storeid'] = $data['store'];
			}

			if(!empty($data['order'])){
				$where['order_sn'] = $data['order'];
			}
			$admin = session('admin');
			if($admin['admin_type'] == 3){
				$where['storeid'] = $admin['storeid'];
			}
			$where['status'] = 1;
			$where['order_status'] = array('neq',0);
			$order = [];
			if($data['order_fee_type'] != 1){
				$order['pay_fee'] = 'desc';
			}

			if($data['create_time_type'] != 1){
				$order['create_time'] = 'desc';
			}
			$page=$data['page'];
	        if(empty($page)){
	            exit('非法操作此页面');
	        }
	        $limit=$data['limit'];
	        if(empty($limit)){
	            exit('非法操作此页面');
	        }
			$count = model('Xmorder')->where($where)->count();
			$res = model('Xmorder')->where($where)->order($order)->page($page,$limit)->select()->toArray();

			foreach($res as $k=>$v){
				$res[$k]['content'] = '菜品数量：'.$v['goods_amount'].'<br/>'.'总额：'.$v['pay_fee'];

				if($v['pay_id'] == 1 ){
					$res[$k]['pay_id'] = '微信支付';
				}
				$res[$k]['is_new_type'] = 0;
				if($v['is_new'] == 1 && $v['pay_status'] == 2){
					$res[$k]['order_sn'] = '<span style="color:red">'.$v['order_sn'].'</span>';
					$res[$k]['is_new_type'] = 1;
				}

				if($v['pay_status'] == 0){
					$res[$k]['pay_status'] = '未付款';
				} else if($v['pay_status'] == 1){
					$res[$k]['pay_status'] = '付款中';
				} else if($v['pay_status'] == 2){
					$res[$k]['pay_status'] = '已付款';
				} else if($v['pay_status'] == 3){
					$res[$k]['pay_status'] = '已退款';
				}

				if($v['order_status'] == 0){
					$res[$k]['order_status'] = '订单无效';
				} else if($v['order_status'] == 1){
					$res[$k]['order_status'] = '订单确认';
				} else if($v['order_status'] == 2){
					$res[$k]['order_status'] = '订单取消';
				} else if($v['order_status'] == 3){
					$res[$k]['order_status'] = '订单无效';
				} else if($v['order_status'] == 4){
					$res[$k]['order_status'] = '订单退货';
				} else if($v['order_status'] == 5){
					$res[$k]['order_status'] = '订单完成';
				}

				if($v['order_status'] == 1){
                    $res[$k]['invoice_status'] = '已开发票';
                }else{
                    $res[$k]['invoice_status'] = '未开发票';
				}
				
				$res[$k]['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
			}

			$info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$res];
		    echo json_encode($info);
		    exit;

	    }else{

			$admin = session('admin');
			$display = '';
			$select = [];
			if($admin['admin_type'] ==1 || $admin['admin_type'] == 2){
				$display = 1;
				$where = ['status' => 1];
				$data=Db::table("xm_store")->where($where)->select();
				if(!empty($data)){
					foreach($data as $k=>$v){
						$select[] = [
							'storeid'	=> $v['storeid'],
							'name'		=> $v['name']
						];
					}
				}
			} else {
				$display = 2;
			}
			$this->assign('select',$select);
			$this->assign('display',$display);
	        return view();
	    }
	}

	/**
	 * 订单详情.
	 */
	public function orderInfo()
	{
		$data = input();
		if(empty($data['orderid'])){
			fail('请求失败！');
		}

		if(empty($data['is_new_type'])){
			fail('非法请求！');
		}

		if($data['is_new_type'] == 1){
			$update = [
				'is_new' => 2,
				'update_time'	=> time()
			];
			$info = model('Xmorder')->where(['orderid'	=> $data['orderid']])->setField($update);
			if(!$info){
				fail('请求失败，请重新请求！');
			}
		}
		$where = [
			'orderid'	=> $data['orderid'],
			'status'	=> 1
		];
		//订单信息.
		$result = model('Xmorder')->where($where)->find();
		if($result == null){
			fail('请求失败！');
		}
		$result = $result->toArray();

		$order = [];
		if($result['order_type'] == 1){
			$order['order_type'] = '堂食';
		} else {
			$order['order_type'] = '外带';
		}

		$order['tnumber'] = $result['tnumber']; //桌号
		$order['order_sn'] = $result['order_sn']; // 订单号
		if($result['pay_id'] == 1){
			$order['pay_id'] = '微信支付';
		}

		$order['create_time'] = date('Y-m-d H:i:s',$result['create_time']);

		$order['pay_time'] = date('Y-m-d H:i:s',$result['pay_time']);

		if($result['pay_status'] == 0){
			$order['pay_status']	= '未付款';
		} else if($result['pay_status'] == 1){
			$order['pay_status']	= '付款中';
		} else if($result['pay_status'] == 2){
			$order['pay_status']	= '已付款';
		} else if($result['pay_status'] == 3){
			$order['pay_status']	= '已退款';
		}
		$order['remark'] = $result['remark'];
		$order['receivables'] = $result['receivables'];

		//门店
		$storeWhere = [
			'storeid'	=> $result['storeid']
		];
		$Store = model('Store')->where($storeWhere)->find();
		if($Store == null){
			$order['sname'] = '';
		} else {
			$order['sname'] = $Store->name;
		}

		$this->assign('order',$order);
		//菜品信息
		$goodWhere = [
			'order_id'	=> $result['order_sn']
		];
		$goods  = model('Xmordergoods')->where($goodWhere)->select()->toArray();
		$goodData = [];
		
		if(!empty($goods)){
			foreach($goods as $k=>$v){

				$gbsid = explode(',',$v['gbsid']);
				$gwhere = [
					'gsid'	=> array('in',$gbsid),
					'status'	=> 0
				];
				$ass=Db::table("xm_goods_spec")->where($gwhere)->select();

				$goodData[] = [
					'goods_name'	=> $v['goods_name'],
					'goods_number'	=> $v['goods_number'],
					'original_price'	=> $v['original_price'],
					'selling_price'	=> $v['selling_price'],
					'son'	=> $ass
				];
			}
		}
		$this->assign('page',$data['page']);
		$this->assign('goods',$goodData);
		return view();
		// dump($goods);
	}

	public function refund()
	{
		$order = input();
		if(empty($order)){
			fail('非法请求！');
		}
		$where = [
			'order_sn'	=> $order['order_no'],
			'status'	=> 1
		];

		$result = model('Xmorder')->where($where)->find();
		
		if($result== null){
			fail('没有找到您的订单！');
		}
		$result = $result->toArray();
		if($result['pay_status'] != 2){
			fail('您还不是已支付订单！');
		}

		$qutorder = [];
		$qutorder['order_sn'] = $result['order_sn'];
		$qutorder['pay_fee'] = $result['pay_fee'];
		$qutorder['gz_openid'] = $result['gz_openid'];
		$app = Db::table('system')->select();
		$storeWheres = [
			'storeid'	=> $result['storeid']
		];
		$storeData = Db::table('xm_store')->where($storeWheres)->find();
		$qutorder['name'] = $storeData['name'];


		if (!empty($app)) {

				if(empty($app[0]['mini_appsecret']) || empty($app[0]['termNo']) || empty($app[0]['merId'])){
					
				}
    			$secret = $app[0]['mini_appsecret'];
    			$termNo = $app[0]['termNo'];
    			$merId = $app[0]['merId'];

    			$result['pay_fee']=str_replace('.', '', $result['pay_fee']);
    			$lens = strlen($result['pay_fee']);
    			// $data['deposit_money']=sprintf("%012d", $data);//生成12位数，不足前面补0   
    			if ($lens < 12) {
    				for ($i=0; $i < 12-$lens ; $i++) { 
    					$result['pay_fee'] = substr_replace(0, $result['pay_fee'], 1, 0);
    				}
				}
    			$time = date('YmdHis',time());
    			$arr = [
    				'orgNo'	=> '2111',
    				'charset'	=> 'UTF-8',
    				'termNo'	=> $termNo,
    				'termType'	=> 'XMWPFB',
    				'txtTime'	=> $time,
    				'signType'	=> 'MD5',
    				
    				'transNo'	=> $result['order_sn'],
    				'merId'		=> $merId,
    				'amt'		=> intval($result['pay_fee']),
    				'payType'	=> 1
    			];


    			$signdata = [
    				'orgNo'	=>'2111',
    				'amt'	=> intval($result['pay_fee']),
    				'termNo'=> $termNo,
    				'merId'	=> $merId,
    				'transNo'	=> $result['order_sn'],
    				'txtTime'	=> $time
    			];
    			$sign = $this->appgetSign($signdata,$secret);
    			$arr['signValue']=strtoupper($sign);
    			$url = "http://yhyr.com.cn/YinHeLoan/yinHe/refundWmpPay.action";
				$res = $this->sendpostss($url,$arr);
    			if ($res['returnCode'] == 0000) {
    				$newData = [
    					'pay_status'	=> 3,
    				];
    				$infos = model('Xmorder')->where($where)->setField($newData);
    				if ($infos) {
						$qutorder['gz_token'] = $app[0]['gz_token'];
						$this->doSend($qutorder);
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

		win('退款成功');
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

	public function excel()
	{
		$admin = session('admin');
		$is_display = 0;
		if($admin['admin_type'] == 3){
			$is_display = 2; //2是上架权限
		} else {
			$is_display = 1; //1是机场权限
		}

		$data = model('Store')->select()->toArray();
		$this->assign('data',$data);
		$this->assign('is_display',$is_display);
		return view();
	}

	   /**
     * 导出数据报表.
     * @return [type] [description]
     */
    public function exportExcelData()
    {
            $datas = input();
            
            if (empty($datas)) {
                fail('非法操作此页面');
			}
			$where = [];
			   $admins = session('admin');
				if($admins['admin_type'] != 3){
					if(!empty($datas['store'])){
					$where['storeid'] = $datas['store'];
					}
				} else {
					$where['storeid'] = $admins['storeid'];
				}
				$where['pay_status'] = $datas['pay_status'];
				$where['status'] = 1;
				$start = $datas['start_at'].' 00:00:00';
				$end = $datas['end_at'].' 23:59:59';
				if(isset($where['storeid'])){
					##打印一个门店

					 //查出数据.
					 $swhere = [
						 'storeid'	=> $where['storeid']
					 ];
					 $storenames = model('Store')->where($swhere)->find()->toArray();
					 $newdata = model('Xmorder')->where($where)->whereTime('pay_time','between',[$start,$end])->select()->toArray();
					 $moneys = 0;
					//给缴费时间的时间戳转换成时间.
					$num = 1;
					foreach ($newdata as $ks => $vs) {
						$newdata[$ks]['sname'] = $storenames['name'];
						$newdata[$ks]['pay_time'] = date('Y-m-d H:i:s',$vs['pay_time']);
						$newdata[$ks]['pay_fee'] = sprintf("%1\$.2f", $vs['pay_fee']);
						$moneys += $vs['pay_fee'];
						$newdata[$ks]['number'] = $num++;
						 $newdata[$ks]['order_sn'] = ' '.$vs['order_sn'];
						 $newdata[$ks]['pay_trans_no'] = ' '.$vs['pay_trans_no'];
						if($vs['pay_id'] == 1){ 
							$newdata[$ks]['pay_id'] = '微信支付';
						}
					}
					$moneys=sprintf("%1\$.2f", $moneys);
	 
					if(!empty($newdata)){
					 $cumulative = [
						 'number'	=> '',
						 'order_sn'	=> '',
						 'pay_trans_no'	=> '',
						 'goods_amount'	=> '',
						 'pay_id'			=> '',
						 'pay_fee'		=> '总计金额：'.$moneys,
						 'pay_time'		=> '',
						 'sname'		=> ''
					 ];
		 
					 array_push($newdata,$cumulative);
					}

				} else {
					####全部打印
					$moneycount = 0;
					$storedata = model('Store')->where(['status'=>1])->select()->toArray();
					if(!empty($storedata)){
						$temp = [];
						foreach($storedata as $k=>$v){
							$where['storeid'] = $v['storeid'];
							$newdata = model('Xmorder')->where($where)->whereTime('pay_time','between',[$start,$end])->select()->toArray();
							if(!empty($newdata)){


								$moneys = 0;
								//给缴费时间的时间戳转换成时间.
								$num = 1;
								foreach ($newdata as $ks => $vs) {
									$newdata[$ks]['sname'] = $v['name'];
									$newdata[$ks]['pay_time'] = date('Y-m-d H:i:s',$vs['pay_time']);
									$newdata[$ks]['pay_fee'] = sprintf("%1\$.2f", $vs['pay_fee']);
									$moneys += $vs['pay_fee'];
									$newdata[$ks]['number'] = $num++;
									 $newdata[$ks]['order_sn'] = ' '.$vs['order_sn'];
									 $newdata[$ks]['pay_trans_no'] = ' '.$vs['pay_trans_no'];
									if($vs['pay_id'] == 1){ 
										$newdata[$ks]['pay_id'] = '微信支付';
									}
								}
								$moneys=sprintf("%1\$.2f", $moneys);
								$moneycount += $moneys;
								if(!empty($newdata)){
								 $cumulative = [
									 'number'	=> '',
									 'order_sn'	=> '',
									 'pay_trans_no'	=> '',
									 'goods_amount'	=> '',
									 'pay_id'			=> '',
									 'pay_fee'		=> '商家总计：'.$moneys,
									 'pay_time'		=> '',
									 'sname'		=> ''
								 ];
					 
								 array_push($newdata,$cumulative);
								}

								$temp[] = $newdata;
							}
						}
					}

					$result = array_reduce($temp, function ($result, $value) {
						return array_merge($result, array_values($value));
					}, array());

					if(!empty($result)){
						$cumulatives = [
							'number'	=> '',
							'order_sn'	=> '',
							'pay_trans_no'	=> '',
							'goods_amount'	=> '',
							'pay_id'			=> '',
							'pay_fee'		=> '总计金额：'.sprintf("%1\$.2f", $moneycount),
							'pay_time'		=> '',
							'sname'		=> ''
						];
			
						array_push($result,$cumulatives);
					   }


					$newdata = $result;

				}			  
				
			   $xlsData = $newdata;
            // $newdata = json_decode($data['data'],true);
               // 文件名和文件类型
               // $fileName = $data['fileName'];
           
               $fileName = $datas['fileName'];
			   if($fileName==""){$fileName=time();}//如果没有给名称 默认为当前时间戳

               // 模拟获取数据
               // $xlsData = self::getData();
               // $xlsData = $this->dataData();

               Vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
                       Vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
                       Vendor('PHPExcel.PHPExcel.Writer.Excel2007');
                       $objExcel = new \PHPExcel();
                       //set document Property
                       $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
                
                       $objActSheet = $objExcel->getActiveSheet();
                       $objActSheet->getStyle('1')->getFont()->setBold(true);
                       $objActSheet->getStyle('2')->getFont()->setBold(true);
                       $objExcel->getActiveSheet()->getStyle(1)->getFont()->setSize(20);
                       // $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->      //设置全局默认的字体大小
                       //   $objExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(20)->setARGB('#FF0000');

					   $datas['tableName'] = $datas['tableName'].'('.$datas['start_at'].'---'.$datas['end_at'].')';
                       $objActSheet->setTitle($datas['tableName']);//设置sheet工作表名称
                       $key = ord("A");
                       $letter =explode(',',"A,B,C,D,E,F,G,H");
                       // $letter =explode(',',"A,B,C,D,E,F,G,H,I,J");
                       // $arrHeader = array('姓名','公司','公司地址','邮箱','电话','职位','行业应用','会员角色','是否验证');
                       $arrHeader = array('序号','订单号','交易号','商品数量','支付方式','支付时间','订单金额',"门店名称");

                       //合并单元格
                       $objExcel->getActiveSheet()->mergeCells('A1:P1');

                       //设置水平居中
                       $objExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                       //填充标题
                            $objActSheet->setCellValue($letter[0].'1',$datas['tableName']);
                       
                       //填充表头信息
                       $lenth =  count($arrHeader);
                       for($i = 0;$i < $lenth;$i++) {
                           $objActSheet->setCellValue("$letter[$i]2","$arrHeader[$i]");

                       };

                       //填充表格信息
                       foreach($xlsData as $k=>$v){
                           $k +=3;
                           $objActSheet->setCellValue('A'.$k,$v['number']);
                           $objActSheet->setCellValue('B'.$k, $v['order_sn']);
                           $objActSheet->setCellValue('C'.$k, $v['pay_trans_no']);
                           $objActSheet->setCellValue('D'.$k, $v['goods_amount']);
                           $objActSheet->setCellValue('E'.$k, $v['pay_id']);
                           $objActSheet->setCellValue('F'.$k, $v['pay_time']);
                           $objActSheet->setCellValue('G'.$k, $v['pay_fee']);
                           $objActSheet->setCellValue('H'.$k, $v['sname']);
                           // 表格高度
                           $objActSheet->getRowDimension($k)->setRowHeight(20);
                       }
                
                       $width = array(10,15,20,25,30);
                       //设置表格的宽度
                       $objActSheet->getColumnDimension('A')->setWidth($width[1]);
                       $objActSheet->getColumnDimension('B')->setWidth($width[3]);
                       $objActSheet->getColumnDimension('C')->setWidth($width[3]);
                       $objActSheet->getColumnDimension('D')->setWidth($width[1]);
                       $objActSheet->getColumnDimension('E')->setWidth($width[1]);
                       $objActSheet->getColumnDimension('F')->setWidth($width[4]);
                       $objActSheet->getColumnDimension('G')->setWidth($width[3]);
                       $objActSheet->getColumnDimension('H')->setWidth($width[4]);
                    //    $objActSheet->getColumnDimension('H')->setWidth($width[1]);
                    //    $objActSheet->getColumnDimension('I')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('K')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('L')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('M')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('N')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('O')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('P')->setWidth($width[1]);
                
                
                       $outfile = $fileName.".xlsx";
                       ob_end_clean();
                       header("Content-Type: application/force-download");
                       header("Content-Type: application/octet-stream");
                       header("Content-Type: application/download");
                       header('Content-Disposition:inline;filename="'.$outfile.'"');
                       header("Content-Transfer-Encoding: binary");
                       header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                       header("Pragma: no-cache");
                       $objWriter->save('php://output');

	}
	

	      /**
     * 发送自定义的模板消息
     * @param $touser
     * @param $template_id
     * @param $url
     * @param $data
     * @param string $topcolor
     * @return bool
     */
    public function doSend($datas)
    {
		$accessToken = $datas['gz_token'];
        $data = [
                'keyword2'      => [
                        'value'     => $datas['pay_fee'].'元',
                        'color'     => '#173177'
                ],
                'keyword1'      => [
                        'value'     => $datas['order_sn'],
                        'color'     => '#173177'
                ],
                'remark'      => [
                        'value'     => '感谢您的使用。',
                        'color'     => '#173177'
                ],
                'first'      => [
                        'value'     => '机场--'.$datas['name'],
                        'color'     => '#173177'
                ],
        ];
         $template = [
            "touser" => $datas['gz_openid'],
            "template_id" => "XfYvkPO8lkbmNCn3g1aVagcv8i4xrTU8F3a3KBlN2kA",
            "topcolor" => "#FF0000",
            "data"      => $data
        ];

        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $accessToken;
        return $this->request_post($url, urldecode($json_template));


	}
	
	        /**
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    function request_post($url = '', $param = '')
    {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        return $data;
    }
}