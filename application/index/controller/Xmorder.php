<?php
namespace  app\index\controller;
use think\Controller;
use think\Db;
use think\Request;

/**
 * Created: by PhpStorm.
 * package: app\index\controller
 * Class: Xmorder
 * user: bingwoo
 * date: 2020/10/27 9:32
 */
class Xmorder extends Controller{

    /**
     * Notes: 获取所有新订单数量
     * Class: getUnconfirmedOrder
     * user: bingwoo
     * date: 2020/10/26 15:53
     */
    public function getUnconfirmedOrderList(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        //验证字段
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
        $userInfo = getStoreidByKey($getData['access-token']);
        $newOrderCount = model('xmorder')->where(['storeid'=>$userInfo['storeid'],'pay_status'=>2,'is_new'=>1])->count();
        $data['order_count'] = $newOrderCount;
        return successMsg('',$data);

    }

    /**
     * Notes: 修改订单查看状态（is_new）
     * Class: editUnconfirmedOrderList
     * user: bingwoo
     * date: 2020/10/26 16:09
     */
    public function editUnconfirmedOrderList(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData= input('post.');
        $verifData = ['access-token'];
        verifColumn($verifData,$postData);
        getStoreidByKey($postData['access-token']);
        $editData['is_new'] = 2;
        $orderidArr = [];
        if(isset($postData['order_id_list']) && !empty($postData['order_id_list'])){
            $orderidArr = explode(',',$postData['order_id_list']);
            if(empty($orderidArr)){
                return failMsg('参数有误格式');
            }
        }else{
            $orderData = model('xmorder')->where(['status'=>1,'is_new'=>1])->select();
            if($orderData){
                $orderData = $orderData->toArray();
                foreach ($orderData as $v){
                    $orderidArr[] = $v['orderid'];
                }
            }
        }
        if(!empty($orderidArr)){
            $where['orderid'] = ['in',$orderidArr];
            $ret = model('xmorder')->where($where)->update($editData);
            if(!$ret){
                return failMsg('查阅失败');
            }
        }
        return successMsg('查阅成功');
    }

    /**
     * Notes: 获取订单列表
     * Class: getOrderList
     * user: bingwoo
     * date: 2020/10/26 15:37
     */
    public function getOrderList(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        //验证字段
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
        $userInfo = getStoreidByKey($getData['access-token']);
        $storeid = $userInfo['storeid'];
        $where = [];
        $where['storeid'] = $storeid;
        //分页
        $page = 0;
        if(isset($getData['page']) && !empty($getData['page'])){
            $page = $getData['page'];
        }
        $limit = 20;
        if(isset($getData['limit']) && !empty($getData['limit'])){
            $limit = $getData['limit'];
        }
        //搜索条件桌台号、支付状态、就餐方式、用户id、支付时间
        if(isset($getData['tnumber']) && !empty($getData['tnumber'])){
            $where['tnumber'] = $getData['tnumber'];
        }
        if(isset($getData['pay_status'])){
            if($getData['pay_status'] == 0 || !empty($getData['pay_status'])){
                if($getData['pay_status'] == 1){
                    $where['pay_status'] = array('in',[2,4]);
					$where['refund_fee'] = array('neq','');
                } else {
                    $where['pay_status'] = $getData['pay_status'];                    
                }
            }
        }
        if(isset($getData['order_type']) && !empty($getData['order_type'])){
            $where['order_type'] = $getData['order_type'];
        }
        if(isset($getData['is_new']) && !empty($getData['is_new'])){
            $where['pay_status'] = 2;
            $where['is_new'] = $getData['is_new'];
        }
        if(isset($getData['uid']) && !empty($getData['uid'])){
            $where['uid'] = $getData['uid'];
        }
        if(isset($getData['pay_time']) && !empty($getData['pay_time'])){
            $payData = explode('/',$getData['pay_time']);
            if(count($payData)<2){//格式：年月日
                $sartTime = strtotime($payData[0]);
                $endTime = $sartTime+86399;
            }else{//格式：年月日时分
                $sartTime = strtotime($payData[0]);
                $hour_time = strtotime($payData[1]) - strtotime('today');
                $sartTime = $sartTime+$hour_time;
                if(is_int($hour_time%3600)){
                    $endTime = $sartTime+3599;
                }else{
                    $endTime = $sartTime+59;
                }
            }
            $where['pay_time'] = ['between time', [$sartTime, $endTime]];
        }
        $orderData = model('xmorder')->where($where)->page($page,$limit)->order('orderid desc')->select();
        if(!empty($orderData)){
            $orderData = $orderData->toArray();
            //获取门店信息
            $sData = model('store')->where(['storeid'=>$storeid])->field('storeid,name')->select();
            $skvData = [];
            foreach ($sData as $sk => $sv){
                $skvData[$sv['storeid']] = $sv['name'];
            }
            foreach ($orderData as &$val){
                if($val['pay_status'] == 0){
                    $val['pay_status_name'] = '未付款';
                }else if($val['pay_status'] == 1) {
                    $val['pay_status_name'] = '付款中';
                }else if($val['pay_status'] == 2){
                    $val['pay_status_name'] = '已付款';
                }else if($val['pay_status'] == 3){
                    $val['pay_status_name'] = '已退款';
                }
                $val['store_name'] = $skvData[$val['storeid']];
                $val['goods_count'] = model('xmordergoods')->where(['order_id'=>$val['orderid']])->count();
            }
        }
        $count = model('xmorder')->where($where)->count();
        return successMsg('',$orderData,$count);

    }

    /**
     * Notes: 获取订单详情
     * Class: getOrderInfo
     * user: bingwoo
     * date: 2020/10/27 11:03
     */
    public function getOrderInfo(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        $verifData = ['access-token','order_id'];
        verifColumn($verifData,$getData);
        getStoreidByKey($getData['access-token']);
        $orderData = model('xmorder')->where(['orderid'=>$getData['order_id']])->find();
        if(empty($orderData)){
            return failMsg('订单信息有误');
        }
        $orderData = $orderData->toArray();
        //订单类型：1：堂食2：外带
        $orderData['order_type_name'] = '堂食';
        if($orderData['order_type'] == 2){
            $orderData['order_type_name'] = '外带';
        }
        //支付状态;0未付款;1付款中;2已付款,3已退款
        $orderData['pay_status_name'] = '未付款';
        if($orderData['order_type'] == 1){
            $orderData['pay_status_name'] = '付款中';
        }else if($orderData['order_type'] == 2){
            $orderData['pay_status_name'] = '已付款';
        }else if($orderData['order_type'] == 3){
            $orderData['pay_status_name'] = '已退款';
        } else if($orderData['order_type'] == 4){
            $orderData['pay_status_name'] = '已完成';
        }
        //支付方式
        $orderData['pay_away'] = '微信';
        if($orderData['pay_id'] != 1){
            $orderData['pay_away'] = '其他';
        }
        //转换时间格式:下单时间、更新时间、支付时间
        $orderData['create_time'] = date('Ymd H:i:s',$orderData['create_time']);
        $orderData['update_time'] = date('Ymd H:i:s',$orderData['update_time']);
        $orderData['pay_time'] = date('Ymd H:i:s',$orderData['pay_time']);
        $goodsData = model('xmordergoods')->where(['order_id'=>$orderData['order_sn']])->select();
        if(!empty($goodsData)){
            $goodsData = $goodsData->toArray();
            foreach ($goodsData as &$gv){
                $gv['gsname'] = '';
                if(!empty($gv['gbsid'])){
                    $gbsArr = explode(',',$gv['gbsid']);
                    $gswhere['gstid'] = ['in',$gbsArr];
                    $gstData = model('goodsspectype')->where($gswhere)->field('gstid,gstname')->select();
                    if(!empty($gstData)){
                        $gstData = $gstData->toArray();
                        $dsData = [];
                        foreach ($gstData as $gstv){
                            $dsData[] = [
                                'id'=>$gstv['gstid'],
                                'name'=>$gstv['gstname'],
                            ];
                        }
                        $gv['gstData'] = $dsData;
                    }
                }
            }
            $orderData['goods_data'] = $goodsData;
        }
        return successMsg('',$orderData);
    }

    /**
     * Notes: 报表今日营业额、有效订单、商品种类、商品数量、本月交易记录
     * Class: getReportForm
     * user: bingwoo
     * date: 2020/10/27 9:33
     */
    public function getReportForm(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
        $userInfo = getStoreidByKey($getData['access-token']);
        $storeid = $userInfo['storeid'];
        $data = [
            'order_day'=>0,
            'order_count'=>0,
            'goods_type_count'=>0,
            'goods_count'=>0,
            'order_month'=>0,
        ];
        //获取当日零点的时间戳
        $y = date("Y");
        $m = date("m");
        $d = date("d");
        //获取当日开始时间和结束时间
        $morningTime= mktime(0,0,0,$m,$d,$y);
        $nightTime = $morningTime+86400;
        //今日营业额
        $order_day = model('xmorder')->where(['storeid'=>$storeid,'pay_status'=>2])->
            where('pay_time', 'between time', [$morningTime, $nightTime])->sum('pay_fee');
        if($order_day){
            $data['order_day'] = $order_day;
        }
        $host_url = 'https://airscan.yinheyun.com.cn';
        $data['image'] = $host_url.'/image/index_img.jpg';
        //有效订单
        $data['order_count'] = model('xmorder')->where(['storeid'=>$storeid,'pay_status'=>2])->
            where('pay_time', 'between time', [$morningTime, $nightTime])->count();
        //商品种类
        $data['goods_type_count'] = model('goodstype')->where(['storeid'=>$storeid,'status'=>1])->count();
        //商品数量
        $data['goods_count'] = model('goods')->where(['storeid'=>$storeid,'status'=>1,'is_grounding'=>2])->count();
        //本月交易记录
        $startmonth = strtotime(date("Y-m-1 00:00:00",time()));
        $endmonth = strtotime(date("Y-m-".date('t')." 23:59:59",time()));
        $order_month = model('xmorder')->where(['storeid'=>$storeid,'pay_status'=>2])->
            where('pay_time', 'between time', [$startmonth, $endmonth])->sum('pay_fee');
        if($order_month){
            $data['order_month'] = $order_month;
        }
        return successMsg('',$data);
    }

    /**
     * Notes: 确认完成订单
     * Class: confirmOrder
     * user: bingwoo
     * date: 2020/11/10 16:08
     */
    public function confirmOrder(){
        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        $verifData = ['access-token','order_no'];
        verifColumn($verifData,$postData);
        $where = [
            'order_sn'	=> $postData['order_no'],
            'status'	=> 1
        ];
        $result = model('Xmorder')->where($where)->field('orderid,storeid')->find();
        if(empty($result)){
            return failMsg('没有找到您的订单！');
        }
        $result = $result->toArray();
        $saveData = ['pay_status'=>4];
        $confirmRet = model('Xmorder')->save($saveData,['orderid'=>$result['orderid']]);
        if($confirmRet){
            $this->sendConfirmNotice($result['storeid']);
            return successMsg('操作成功');
        }
        return failMsg('操作失败');

    }

    /**
     * Notes: 发送确认通知
     * Class: sendConfirmNotice
     * user: bingwoo
     * date: 2020/11/10 16:15
     */
    public function sendConfirmNotice($storeid){
        $systemData = model('system')->field('gz_token')->find();
        $storeData = model('store')->where(['storeid'=>$storeid])->field('name')->find();
        if(empty($systemData)){
            return false;
        }
        $systemData = $systemData->toArray();
        $data = [
            'keyword2'      => [
                'value'     => '2元',
                'color'     => '#173177'
            ],
            'keyword1'      => [
                'value'     => 1,
                'color'     => '#173177'
            ],
            'remark'      => [
                'value'     => '感谢您的使用。',
                'color'     => '#173177'
            ],
            'first'      => [
                'value'     => '机场--'.$storeData['name'],
                'color'     => '#173177'
            ],
        ];
        $accessToken = $systemData['gz_token'];
        $template = [
            "touser" => $systemData['gz_token'],
            "template_id" => "XfYvkPO8lkbmNCn3g1aVagcv8i4xrTU8F3a3KBlN2kA",
            "topcolor" => "#FF0000",
            "data"      => $data
        ];
        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $accessToken;
        return $this->request_post($url, urldecode($json_template));
    }

    /**
     * Notes: 退款
     * Class: refundOrder
     * user: bingwoo
     * date: 2020/10/29 9:49
     */
    public function refund(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('post.');
        $verifData = ['access-token','order_no'];
        verifColumn($verifData,$getData);
        getStoreidByKey($getData['access-token']);
        $where = [
            'order_sn'	=> $getData['order_no'],
            'status'	=> 1
        ];
        $str = '订单号:'.$getData['order_no'];
        $result = model('Xmorder')->where($where)->find();
        if(empty($result)){
            return failMsg('没有找到您的订单！');
        }
        $result = $result->toArray();
        if($result['pay_status'] == 0 || $result['pay_status'] == 1 || $result['pay_status'] == 3){
            return failMsg('您还不是已支付订单！');
		}
        $qutorder = [];
        $qutorder['order_sn'] = $result['order_sn'];
        $qutorder['pay_fee'] = $result['pay_fee'];
        $qutorder['gz_openid'] = $result['gz_openid'];
        $app = model('system')->select();
        $storeWheres = [
            'storeid'	=> $result['storeid']
        ];
        $storeData = model('store')->where($storeWheres)->find();
        $qutorder['name'] = $storeData['name'];
        if (!empty($app)) {
            if(empty($app[0]['mini_appsecret']) || empty($app[0]['termNo']) || empty($app[0]['merId'])){

            }
            $secret = $app[0]['mini_appsecret'];
            $termNo = $app[0]['termNo'];
            $merId = $app[0]['merId'];
            $return_fee = $result['pay_fee'];
            $result['pay_fee']=sprintf("%1\$.2f", $result['pay_fee']-$result['refund_fee']);
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
                $wheres = [
                    'order_sn'	=> $getData['order_no'],
                    'status'	=> 1
                ];
                $newData = [
                    'pay_status'	=> 3,
                    'refund_fee'    => $return_fee
                ];
                $infos = model('Xmorder')->where($wheres)->setField($newData);
                if ($infos) {
                    $qutorder['gz_token'] = $app[0]['gz_token'];
                    $this->doSend($qutorder);
                    $str .= ' 状态:退款成功';
                    addLog($str,$getData['access-token']);
                    return successMsg('退款成功');
                } else {
                    $str .= ' 状态:退款成功,更新订单状态失败';
                    addLog($str,$getData['access-token']);
                    return failMsg('退款成功,更新订单状态失败');
                }
            } else {
                $str .= ' 状态:退款失败'.' 原因:'.$res['returnMsg'];
                addLog($str,$getData['access-token']);
                return failMsg($res['returnMsg']);
            }
        } else {
            $str .= ' 状态:退款失败'.' 原因：请您去配置微信小程序参数';
            addLog($str,$getData['access-token']);
            return failMsg('请您去配置微信小程序参数');
        }
    }

    public function oneRefund()
    {
        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('post.');
        $verifData = ['access-token','order_no','ogid','money','num'];
        verifColumn($verifData,$getData);
        getStoreidByKey($getData['access-token']);
		$data = input();
		$where = [
			'order_sn'	=> $data['order_no']
		];
		$res = model('Xmorder')->where($where)->find();
		if($res== null){
            return failMsg('没有找到您的订单！');
		}
		$res = $res->toArray();
		if($res['pay_status'] == 0 || $res['pay_status'] == 1 || $res['pay_status'] == 3){
            return failMsg('您还不是已支付订单！');
		}
		$data['money'] = sprintf("%1\$.2f", ($data['money']*$data['num']));
		$refund_fee = sprintf("%1\$.2f", $data['money']+$res['refund_fee']);
		if($refund_fee > $res['pay_fee']){
            return failMsg('您已超过支付金额了！');
		}

		$str = '订单号:'.$data['order_no'];
		$app = Db::table('system')->select();
		if (!empty($app)) {
            if(empty($app[0]['mini_appsecret']) || empty($app[0]['termNo']) || empty($app[0]['merId'])){

            }
            $secret = $app[0]['mini_appsecret'];
            $termNo = $app[0]['termNo'];
            $merId = $app[0]['merId'];
            $data['money'] = str_replace('.', '', $data['money']);
            $lens = strlen($data['money']);
            if ($lens < 12) {
                for ($i=0; $i < 12-$lens ; $i++) {
                    $data['money'] = substr_replace(0, $data['money'], 1, 0);
                }
			}
			$refundNo = date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT);
            $time = date('YmdHis',time());
            $arr = [
                'orgNo'	=> '2111',
                'charset'	=> 'UTF-8',
                'termNo'	=> $termNo,
                'termType'	=> 'XMWPFB',
                'txtTime'	=> $time,
                'signType'	=> 'MD5',

                'transNo'	=> $res['order_sn'],
                'merId'		=> $merId,
                'amt'		=> intval($data['money']),
				'payType'	=> 1,
				'refundNo'	=> $refundNo,
            ];
            $signdata = [
                'orgNo'	=>'2111',
                'amt'	=> intval($data['money']),
                'termNo'=> $termNo,
                'merId'	=> $merId,
                'transNo'	=> $res['order_sn'],
                'txtTime'	=> $time
            ];
            $sign = $this->appgetSign($signdata,$secret);
            $arr['signValue']=strtoupper($sign);
            $url = "http://yhyr.com.cn/YinHeLoan/yinHe/refundWmpPay.action";
            $ress = $this->sendpostss($url,$arr);
            if ($ress['returnCode'] == 0000) {

				$model = model('Xmorder');
				# 开启事务
				$model -> startTrans();
				try{
					$update = [];
					if($refund_fee == $res['pay_fee']){
						$update = [
							'refund_fee'	=> $refund_fee,
							'pay_status'	=> 3
						];
					} else {
						$update = [
							'refund_fee'	=> $refund_fee
						];
					}
					# 菜品时段表
					$res = $model->where($where)->update($update);
					if( $res < 1 ){
						throw new \Exception('订单表修改失败');
					}
					$xm_where = [
						'ogid'	=> $data['ogid'],
					];
					#写入时段商品商品关联表
					$xmmodel = model('Xmordergoods');
					$xm_res = $xmmodel->where($xm_where)->update(['is_refund'=>1,'refund_no'=>$refundNo]);
					if( $xm_res < 1 ){
						throw new \Exception('订单商品信息表修改失败');
					}
                    $model -> commit();
                    addLog('退款了一个商品【部分退款】',$data['access-token']);
                    return successMsg('部分退款成功');
				}catch ( \Exception $e ){

					$model -> rollback();
                    return failMsg($e->getMessage());
				}
            } else {

                return failMsg($ress['returnMsg']);

            }
        } else {
            return failMsg('请您去配置微信小程序参数');
        }
    }

    /**
     * Notes: 获取密钥
     * Class: appgetSign
     * user: bingwoo
     * date: 2020/10/29 10:12
     */
    public function appgetSign($data,$secret){

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
     * Notes: 发送post formdata请求
     * Class: sendpostss
     * user: bingwoo
     * date: 2020/10/29 10:13
     */
    public function sendpostss($url,array $data){

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
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    public function request_post($url = '', $param = '')
    {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($ch, CURLOPT_POST, true); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HEADER, false);//设置header
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        return $data;
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
    public function doSend($datas){

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

}