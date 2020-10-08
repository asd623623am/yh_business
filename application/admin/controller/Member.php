<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 会员管理
 */
class Member extends Common
{

	/**
	 * 会员列表.
	 * @return [type] [description]
	 */
	public function memberList()
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
		        
		        $tel = input('get.tel');
		        $where = [];

		        
				$admin = session('admin');
				$where = [];
				if($admin['admin_type'] == 1 || $admin['admin_type'] == 2){
					$where['status'] = 1;
				} else {
					$where['status'] = 1;
					$where['storeid'] = $admin['storeid'];
				}
				if (!empty($tel)) {
		        	$len = strlen($tel);
		        	if ($len == 11) {
		        		$where['phone'] = $tel;
		        	} else {
		        		$where['card'] = $tel;
		        	}
		        }
				$dataInfo = model('Member')->where($where)->page($page,$limit)->select()->toArray();
				foreach($dataInfo as $k=>$v){
					if($v['is_type'] == 1){
						$dataInfo[$k]['is_type'] = '潜在会员';
					} else {
						$dataInfo[$k]['is_type'] = '会员';
					}

					$sdata=Db::table("xm_store")->where(['storeid'=> $v['storeid']])->find();
					$dataInfo[$k]['s_name'] = $sdata['name'];

					$oWhere = [
						'openid' => $v['wx_openid']
					];

					$fee = model('Xmorder')->where($oWhere)->sum('pay_fee');
					$dataInfo[$k]['pay_money'] = $fee;

				}
		       	$count=model('Member')->where($where)->count();
		        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$dataInfo];
		        echo json_encode($info);
		        exit;

		    }else{
		        return view();
		    }
    }

    /**
     * 会员详情.
     * @return [type] [description]
     */
    public function memberUpdateInfo()
    {
    	$data = input();
    	if (empty($data['uid']) || empty($data['page'])) {
    		exit('非法操作此页面');
    	}

    	$where = [
    		'uid'	=> $data['uid'],
    		'status'	=> 1
    	];

    	$info = model('Member')->where($where)->find();
    	if ($info == null) {
    		exit('没有找到您的数据！');
    	}

		$arr = $info->toArray();
		if($arr['is_type'] == 1){
			$arr['is_type'] = '潜在会员';
		} else {
			$arr['is_type'] = '会员';
		}
		// dump($arr);exit;
		$this->assign('uid',$data['uid']);
    	$this->assign('arr',$arr);
    	$this->assign('page',$data['page']);
    	return view();
	}
	
	public function orderList()
	{
		$data = input();
		if(empty($data['uid'])){
			fail('请求有误！');
		}

		if(empty($data['page'])){
			fail('请求有误！');
		}
		if(empty($data['limit'])){
			fail('请求有误！');
		}
		$page = $data['page'];
		$limit = $data['limit'];

		$mWhere = [
			'uid' => $data['uid'],
		];
		$dataInfo = model('Member')->where($mWhere)->find()->toArray();
		$where = [
			'openid' => $dataInfo['wx_openid'],
			'status'	=>1,
			'order_status'	=> array('neq',0),
		];
		$count = model('Xmorder')->where($where)->count();
		$res = model('Xmorder')->where($where)->page($page,$limit)->select()->toArray();

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


			$res[$k]['is_new_type'] = 0;
			if($v['is_new'] == 1 && $v['pay_status'] == 2){
				// $res[$k]['order_transNo'] = '<span style="color:red">'.$v['order_sn']."<br/>交易号：".$v['pay_trans_no'].'</span>';
				// $res[$k]['order_sn'] = '<span style="color:red">'.$v['order_sn'].'</span>';
				$res[$k]['is_new_type'] = 1;
			} else {
				// $res[$k]['order_transNo'] = $v['order_sn']."<br/>交易号：".$v['pay_trans_no'];
			}
		}


		$info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$res];
		echo json_encode($info);
		exit;
	}
}