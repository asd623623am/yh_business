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
			
			// $res = model('Xmorder')->where(['pay_fee','between',[1,20]])->select()->toArray();
			// dump($res);exit;
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
				$where['pay_fee'] = ['between',[1,20]];

			}
			 $res = model('Xmorder')->where($where)->select()->toArray();
			dump($res);exit;

			$newstr = substr($data['pay_time'],0,strlen($data['pay_time'])-2);
			$start = $newstr.'00';
			$end = $newstr.'59';

			$res = model('Xmorder')->whereTime('pay_time','between',[$start,$end])->select()->toArray();
			dump($res);exit;
	        $page=input('get.page');
	        if(empty($page)){
	            exit('非法操作此页面');
	        }
	        $limit=input('get.limit');
	        if(empty($limit)){
	            exit('非法操作此页面');
	        }
	        $where = [];
	    }else{
	        return view();
	    }
	}
}