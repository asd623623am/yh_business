<?php
namespace  app\index\controller;
use think\Controller;
use think\Request;

/**
 * Created: by PhpStorm.
 * package: app\index\controller
 * Class: Member
 * user: bingwoo
 * date: 2020/10/26 9:57
 */
class Member extends Controller{

    /**
     * Notes: 会员列表
     * Class: getMemberList
     * user: bingwoo
     * date: 2020/10/26 9:57
     */
	public function getMemberList(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        //验证字段
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
        $storeid = getStoreidByKey($getData['access-token']);
        $where = [];
        $page = 0;
        if(isset($getData['page']) && !empty($getData['page'])){
            $page = $getData['page'];
        }
        $limit = 20;
        if(isset($getData['limit']) && !empty($getData['limit'])){
            $limit = $getData['limit'];
        }
        $where['status'] = 1;
        $where['storeid'] = $storeid;
        if(isset($getData['searchname']) && !empty($getData['searchname'])){
            $where['phone|card'] = ['like','%'.$getData['searchname'].'%'];
        }
        $dataInfo = model('Member')->where($where)->page($page,$limit)
            ->field('uid,name,phone,grid,balance,wx_name,card')->select()->toArray();
        $data = [];
        if(!empty($dataInfo)){
            foreach($dataInfo as $k=>$v){
                if($v['griud'] = 1){
                    $dataInfo[$k]['griudNmae'] = '铜卡';
                }else if($v['griud'] = 2){
                    $dataInfo[$k]['griudNmae'] = '银卡';
                }else if($v['griud'] = 3){
                    $dataInfo[$k]['griudNmae'] = '金卡';
                }
            }
            $data = $dataInfo;
        }
        $count=  model('Member')->where($where)->count();
        return successMsg('',$data,$count);
    }

    /**
     * Notes: 会员详情
     * Class: memberUpdateInfo
     * user: bingwoo
     * date: 2020/10/26 11:05
     */
    public function getMemberInfo(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        //验证字段
        $verifData = ['access-token','uid'];
        verifColumn($verifData,$getData);
    	$where = [
    		'uid'	=> $getData['uid'],
    		'status'	=> 1
    	];

    	$info = model('Member')->where($where)->find();
    	if (empty($info)) {
    	    return failMsg('没有找到您的数据');
    	}
		$arr = $info->toArray();
		if($arr['is_type'] == 1){
			$arr['is_type'] = '潜在会员';
		} else {
			$arr['is_type'] = '会员';
		}
		$oWhere = [
			'uid' => $getData['uid'],
			'pay_status'	=> 2
		];
        $fee = 0;
        $arr['oeder_list'] = [];
        $uOrder = model('xmorder')->where($oWhere)
            ->field('orderid,order_sn,order_type,tnumber,userNum,pay_fee,pay_status')->select();
        if($uOrder){
            $uOrder = $uOrder->toArray();
            foreach ($uOrder as &$v){
                $fee += $v['pay_fee'];
                $v['goods_count'] = model('xmordergoods')->where(['order_id'=>$v['orderid']])->count();
            }
            $arr['oeder_list'] = $uOrder;
        }
        $arr['pay_money'] = $fee;
		return successMsg('',$arr);

	}

    /**
     * Notes: 删除会员信息
     * Class: delMember
     * user: bingwoo
     * date: 2020/10/26 11:37
     */
        public function delMember()
	{
        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('post.');
        //验证字段
        $verifData = ['access-token','uid'];
        verifColumn($verifData,$getData);
        getStoreidByKey($getData['access-token']);
        $where = [
            'uid' =>$getData['uid']
        ];
        $ret = model('member')->save(['status'=>2],$where);
        if($ret){

            return successMsg('删除成功');
        }else{
            return failMsg("删除失败");
        }
	}
}