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

				if($v['pay_status'] == 0){
					$res[$k]['pay_status'] = '未付款';
				} else if($v['pay_status'] == 1){
					$res[$k]['pay_status'] = '付款中';
				} else if($v['pay_status'] == 2){
					$res[$k]['pay_status'] = '已付款';
				} else if($v['pay_status'] == 3){
					$res[$k]['pay_status'] = '已退款';
				}
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
			'order_id'	=> $data['orderid']
		];
		$goods  = model('Xmordergoods')->where($goodWhere)->select()->toArray();
		$goodData = [];
		if(!empty($goods)){
			foreach($goods as $k=>$v){
				$goodData[] = [
					'goods_name'	=> $v['goods_name'],
					'goods_number'	=> $v['goods_number'],
					'original_price'	=> $v['original_price'],
					'selling_price'	=> $v['selling_price']
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
		win('退款成功');
	}
}