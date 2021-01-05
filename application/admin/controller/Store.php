<?php

namespace app\admin\controller;

use think\Db;


/**
 * Created by PhpStorm.
 * package app\admin\controller
 * Class Store
 * user: bingwoo
 * date: 2020/8/12 10:50
 */
class Store extends Common
{

    /**
     * Notes: 获取门店列表
     * Class: getStoreList
     * user: bingwoo
     * date: 2020/8/12 10:53
     */
    public function storeList(){

        if( request() -> isAjax() ){
            $where = ['status' => 1];
            $getData = input('get.');
            if(!empty($getData['storename'])){
                $where['name'] = $getData['storename'];
            }
            if(!empty($getData['code'])){
                $where['store_no'] = $getData['code'];
            }
            if(!empty($getData['user_name'])){
                $where['user_name'] = $getData['user_name'];
            }
            $data=Db::table("xm_store")->where($where)->page($getData['page'],$getData['limit'])->select();
            foreach ($data as &$val){
                $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                $val['start_end_time'] = date('H:i:s',$val['start_business_hours']).'-'.date('H:i:s',$val['end_business_hours']);
            }
            unset($val);
            $count=Db::table("xm_store")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            return view();
        }
    }

    /**
     * Notes: 查看门店信息
     * Class: storeInfo
     * user: bingwoo
     * date: 2020/8/13 14:13
     */
    public function storeInfo(){

        $postData = input('get.');
        if(!empty($postData)){
            $storeId = $postData['storeid'];
            $storeData = model('Store')->where(['storeid'=>$storeId])->find()->toArray();
            if(!empty($storeData)){
                $storeData['start_business_hours'] = date('H:i:s',$storeData['start_business_hours']);
                $storeData['end_business_hours'] = date('H:i:s',$storeData['end_business_hours']);
                $this->assign('store',$storeData);
                $this->assign('page',$postData['page']);
                return  view();
            }
            $this->getTips();
        }
        $this->getTips();
    }

    /**
     * Notes: 添加门店信息
     * Class: storeAdd
     * user: bingwoo
     * date: 2020/8/12 10:55
     */
    public function storeAdd(){
        if(check()){
            $data = input('post.');
            if(empty($data)){
                exit('非法操作此页面');
            }
            ######## 东杰
            $admin_where = [
                'admin_name'   => $data['account']
            ];
            $res = model('Admin')->where($admin_where)->select()->toArray();
            if(!empty($res)){
                fail('登录账号已被用，请更换登录账号！');
            }
            ##########
            $data['name'] = trim($data['name']);
            $where = [
                'name'	=> $data['name'],
            ];
            $info = model('Store')->where($where)->find();
            if (!empty($info)) {
                fail('门店名称已存在！');
            } else {
                $insert = $this->processData($data);
                $storeInfo = model('Store')->field('storeid')->order('storeid desc')->find();
                $insert['store_no'] = 100001;
                if(isset($storeInfo['storeid']) && !empty($storeInfo['storeid'])){
                    $len = strlen($storeInfo['storeid']);
                    $strno = substr(100001,0,6-$len);
                    $storeInfo['storeid'] += 1;
                    $insert['store_no'] = $strno.$storeInfo['storeid'];
                }
                $insert['airscan_secret_key'] = $this->getAirscanKay();//生成点餐密钥
                $insert['create_time'] = time();
                $insert['update_time'] = time();
                $insert['name'] = '郑州机场商业@'.$insert['name'];
                $store_id = model('Store')->insertGetId($insert);
                /* @todo 添加用户和门店权限绑定*/
                if($store_id){
                    //设置门店默认值
                    $update = [
                        'storeid'  => $store_id,
                        'img'       => '',
                        'packing_fee'   => 0,
                        'is_charge'   => 1,
                        'content'   => '',
                        'ctime'     => time()
                    ];
                    Db::table('xm_store_content')->insertGetId($update);
                    $admin_insert = [
                        'admin_type'    =>3,
                        'admin_name'    =>$data['account'],
                        'admin_pwd'     =>$data['passwords'],
                        'admin_tel'     =>'',
                        'storeid'       => $store_id
                    ];
                    $model = model('Admin');
                    $model->save($admin_insert);
                    $admin_id = $model -> getLastInsID();
                    if($admin_id){
                        $role_ids = model('Role')->where(['type'=>4])->find()->toArray();
                        $roleinsert = [
                            'admin_id'  => $admin_id,
                            'role_id'   => $role_ids['role_id']
                        ];
                        $adminres = model('AdminRole')->insert($roleinsert);
                        if($adminres){
                            $this->addLog('添加了一个门店');
                            win('添加成功');
                        }else{
                            fail('添加失败');
                        }
                    } else {
                        fail('添加失败');
                    }
                }else{
                    fail('添加失败');
                }
            }
        }else{
            return view();
        }

    }

    /**
     * Notes: 修改门店信息
     * Class: storeEdit
     * user: bingwoo
     * date: 2020/8/12 10:54
     */
    public function storeEdit(){
        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                $where = ['storeid' => $postData['storeid']];
                $storeData = model('Store')->where($where)->find()->toArray();
                if(empty($storeData)){
                    $this->fail('获取信息失败！');
                }
                $insert = $this->processData($postData);
                $insert['name'] = '郑州机场商业@'.$insert['name'];
                if(!isset($storeData['airscan_secret_key']) || empty($storeData['airscan_secret_key'])){
                    $insert['airscan_secret_key'] = $this->getAirscanKay();
                }
                $res = model('Store')->save($insert,$where);
                if ($res) {
                    $this -> addLog('修改门店信息');
                    win('修改成功');
                } else {
                    fail('修改失败');
                }
            }else{
                $this->getTips();
            }
        }else{
            $storeId=input('get.storeid');
            $page=input('get.page');
            if(empty($storeId)){
                $this->getTips();
            }else{
                $where = [
                    'storeid' => $storeId
                ];
            }
            $storeData = model('Store')->where($where)->find()->toArray();
            if(empty($storeData)){
                $this->fail('获取信息失败！');
            }
            $storeData['start_business_hours'] = date('H:i:s',$storeData['start_business_hours']);
            $storeData['end_business_hours'] = date('H:i:s',$storeData['end_business_hours']);
            $name = explode('@',$storeData['name']);
            if(count($name) < 2){
                $storeData['name'] = $name[0];
            } else {
                $storeData['name'] = $name[1];
            }
            $this->assign('store',$storeData);
            $this->assign('page',$page);
            return view();
        }
    }

    /**
     * Notes: 删除门店信息
     * Class: storeDel
     * user: bingwoo
     * date: 2020/8/12 10:56
     */
    public function storeDel(){
        $postData = input('post.');
        if(!empty($postData)){
            $delDate = ['status' => 0];
            $where = ['storeid' => $postData['storeid']];
            
            $res = model('Store')->save($delDate,$where);
            if ($res) {

                 ###删除商品
                 $gdel = [
                    'status'    => 2
                ];
                model('Goods')->save($gdel,$where);
                
                // ###删除规格
                $sdel = [
                    'status'    => 1
                ];

                model('GoodsBingSpec')->where($where)->delete();
                //分类
                model('GoodsSpecType')->save($sdel,$where);
                $tdel = [
                    'status'    => 2
                ];
                model('GoodsType')->save($tdel,$where);

                $admin = model('Admin')->where($where)->select()->toArray();
                $delId = [];
                foreach($admin as $k=>$v){
                    $delId[] = $v['admin_id'];
                }
                $reslut = model('Admin')->where($where)->delete();
                if($reslut){
                    $wheres = [
                        'admin_id'  => array('in',$delId)
                    ];
                    $ress = model('AdminRole')->where($wheres)->delete();
                    if($ress){
                        $this -> addLog('删除门店信息');
                        win('删除成功');
                    } else {
                        fail('删除失败');
                    }
                } else {
                    fail('删除失败');
                }
            } else {
                fail('删除失败');
            }
        }
        $this->postTips();

    }

    /**
     * 批量删除门店信息
     */
    public function StoreDels()
    {
        $postData = input('post.');
        if(!empty($postData)){
            $storeid = [];
            foreach($postData['data'] as $k=>$v){
                $storeid[] = $v['storeid'];
            }
            $delDate = ['status' => 0];
            $where = [
                'storeid'   => array('in',$storeid)
            ];
            $res = model('Store')->save($delDate,$where);
            if ($res) {
                ###删除商品
                $gdel = [
                    'status'    => 2
                ];
                model('Goods')->save($gdel,$where);
                
                // ###删除规格
                $sdel = [
                    'status'    => 1
                ];
                model('GoodsBingSpec')->where($where)->delete();
                //分类
                model('GoodsSpecType')->save($sdel,$where);
                $tdel = [
                    'status'    => 2
                ];
                model('GoodsType')->save($tdel,$where);

                $admin = model('Admin')->where($where)->select()->toArray();
            
                $delId = [];
                foreach($admin as $k=>$v){
                    $delId[] = $v['admin_id'];
                }
                $reslut = model('Admin')->where($where)->delete();
                if($reslut){
                    $wheres = [
                        'admin_id'  => array('in',$delId)
                    ];
                    $ress = model('AdminRole')->where($wheres)->delete();
                    if($ress){
                        $this -> addLog('删除门店信息');
                        win('删除成功');
                    } else {
                        fail('删除失败');
                    }
                    
                } else {
                    fail('删除失败');
                }
            } else {
                fail('删除失败');
            }
        }
        $this->postTips();
    }
    /**
     * Notes: 处理数据格式
     * Class: processDara
     * user: bingwoo
     * date: 2020/8/13 13:53
     */
    private function processData($postData){
        $insert = [
            'name'		=> trim($postData['name']),
            'address'	=> $postData['address'],
            'user_name'	=> $postData['user_name'],
            'user_tel'	=> (int)$postData['user_tel'],
            'account'	=> $postData['account'],
            'password'	=> $postData['passwords'],
        ];
        //门店logo
        if(!empty($postData['banner_url'])){
            $len = strlen($postData['banner_url']);
            $strDate = substr($postData['banner_url'],1,8);
            $strname = substr($postData['banner_url'],10,$len-10);
            $insert['logo'] = $strDate.'/'.$strname;
        }
        //pos支付
        if(!empty($postData['pos_pay'])){
            $insert['pos_pay'] = $postData['pos_pay'];
        }
        //刷脸支付
        if(!empty($postData['face_pay'])){
            $insert['face_pay'] =$postData['face_pay'];
        }
        //会员密钥
        if(!empty($postData['member_secret_key'])){
            $insert['member_secret_key'] = trim($postData['member_secret_key']);
        }
        //营业时间
        $insert['start_business_hours'] = strtotime(date('Y-m-d').$postData['start_business_hours']);
        $insert['end_business_hours'] = strtotime(date('Y-m-d').$postData['end_business_hours']);
        return $insert;
    }

    /**
     * Notes: 返回post提示信息
     * Class: tips
     * user: bingwoo
     * date: 2020/8/13 14:05
     */
    private function postTips(){
        return fail('门店信息有误');
    }

    /**
     * Notes: 返回get请求提示
     * Class: getTips
     * user: bingwoo
     * date: 2020/8/13 14:11
     */
    private function getTips(){
        exit('门店信息有误');
    }

    /**
     * 门店报表
     */
    public function reportList()
    {
        $stroreData = model('store')->field('storeid,name')->select()->toArray();
        $this->assign('storeData',$stroreData);
        return view();
    }

    /**
     * 门店预览.
     */
    public function preview()
    {
        $data = input();
        if(empty($data)){
            fail('缺少参数！');
        }
        $where = [];
        $r_where = []; //退款条件
        $q_where = []; //全部条件
        $user = session('admin');
        if($user['storeid'] == 0){
            $where['storeid'] = $data['storeid'];
            $r_where['storeid'] = $data['storeid'];
            $q_where['storeid'] = $data['storeid'];
        } else {
            $where['storeid'] = $user['storeid'];
            $r_where['storeid'] = $user['storeid'];
            $q_where['storeid'] = $user['storeid'];
        }
        $start = $data['start_at'].'00:00:01';
        $end = $data['end_at'].'23:59:59';
        $where['status'] = 1;
        $where['pay_status'] = array('in',[2,4]);
        $r_where['pay_status'] = 3;
        $q_where['pay_status'] = array('in',[2,3,4]);
        $where['pay_time'] = ['between time',[$start,$end]];
        $r_where['pay_time'] = ['between time',[$start,$end]];
        $q_where['pay_time'] = ['between time',[$start,$end]];
        $data = model('Xmorder')->where($where)->select()->toarray();
        $rdata = model('Xmorder')->where($r_where)->select()->toarray();
        $qdata = model('Xmorder')->where($q_where)->select()->toarray();
        if(empty($data)){
            fail('暂时还没有订单');
        }
        $money = 0;
        $d_fee = 0;
        $r_count = [];
        $r_fee = 0;
        $br_count = []; //部分退款
        $br_fee = 0; //部分退款
        foreach($qdata as $qk=>$qv){
            $money += $qv['pay_fee'];
            $d_fee += $qv['discount'];
        }
        foreach($data as $k=>$v){
            if($v['refund_fee'] != null){
                $br_fee += $v['refund_fee'];
                $br_count[] = $v;
            }
        }
        $r_count = count($rdata);
        if($r_count != 0){
            foreach($rdata as $rk=>$rv){
                $r_fee += $rv['pay_fee'];
            }
        }
        $temp = [
            'count' => count($qdata), //账单总数
            'money' => $money,  //账单总额
            'd_fee' => $d_fee,  //优惠总额
            'br_count'  => count($br_count), //部分退款单数
            'br_fee'  => $br_fee, //部分退款金额
            'r_count'   => $r_count, //全额退款单数
            'r_fee' =>  $r_fee, //全额退款总额
            'fee'   => $money-$r_fee-$br_fee, //合计
        ];



        //菜品汇总
        $goods_sum = [];
        //菜品明细
        $goods_detailed = [];
        foreach($data as $key=>$val){
                $order_goods_where = [
                    'order_id'  => $val['order_sn'],
                    'is_refund' => 0
                ];
                $a = model('Xmordergoods')->where($order_goods_where)->select()->toArray();
                if(!empty($a)){
                    foreach($a as $ak=>$av){
                        $goods_where = [
                            'gid'  => $av['goods_id'],
                            'status'    => 1
                        ];
                        $gdata = model('Goods')->where($goods_where)->find();
                        if($gdata != null){
                            $good_type_where = [
                                'gtid'  => $gdata['gtid'],
                                'status'    => 1
                            ];
                            $tdata = model('GoodsType')->where($good_type_where)->find();
                            if($tdata != null){
                                $a[$ak]['gtid']  = $tdata['gtid'];
                                $a[$ak]['gtname'] = $tdata['gtname'];
                            } else {
                                $a[$ak]['gtid']  = '';
                                $a[$ak]['gtname'] = '';
                            }
                        } else {
                            $a[$ak]['gtid'] = '';
                            $a[$ak]['gtname']  = '';
                        }
                        $goods_sum[] = $a[$ak];
                    }
                    }
        }
        $goods_detailed = $goods_sum;
        $g_counts = 0;
        $goodsData = $this->array_group_by($goods_sum,'gtname');
        $newgoods = [];
        $g_fee = 0;
        foreach($goodsData as $kk=>$vv){
            $g_money = 0;
            foreach($vv as $ks=>$vs){
                $g_money += $vs['goods_number']*$vs['selling_price'];
                $g_fee += $vs['goods_number']*$vs['selling_price'];
                $g_counts += $vs['goods_number'];
            }
            $newgoods[] = [
                'type_name' => $kk,
                'count'     => count($vv),
                'money'     => $g_money
            ];
        }
        $newgoods[]=[
            'type_name' => '小计',
            'count'     => $g_counts,
            'money'     => $g_fee
        ];

        $new_goods_data = [];
        $d_fee = 0;
        $order_where = [];
        $gd_count = 0;
        $dcount=0;
        foreach($goods_detailed as $gdk=>$gdv){
            $d_fee += $gdv['goods_number']*$gdv['selling_price'];
            $new_goods_data[] = [
                'gtname' => $gdv['gtname'],
                'gname'  => $gdv['goods_name'],
                'count'  => $gdv['goods_number'],
                'money'  => $gdv['goods_number']*$gdv['selling_price'],
            ];
            $order_where[] = $gdv['gtname'];
            $dcount+=$gdv['goods_number'];
        }
        array_multisort($order_where, SORT_ASC, $new_goods_data);
        $new_goods_data[] = [
            'gtname' => '',
            'gname' => '小计',
            'count' => $dcount,
            'money' => $d_fee
        ];
        $temp['type_data'] = [
            'data'  => $newgoods,
        ];
        $temp['d_data'] = [
            'data'  => $new_goods_data,
        ];
        echo json_encode(['font'=>$temp,'code'=>1]);exit;
    }

    private function array_group_by($arr, $key)
	{
		$grouped = [];
		foreach ($arr as $value) {
			$grouped[$value[$key]][] = $value;
		}
		if (func_num_args() > 2) {
			$args = func_get_args();
			foreach ($grouped as $key => $value) {
				$parms = array_merge([$value], array_slice($args, 2, func_num_args()));
				$grouped[$key] = call_user_func_array('array_group_by', $parms);
			}
		}
		return $grouped;
	}

    public function exportExcel()
    {
        $data = input();
        if(empty($data)){
            fail('缺少参数！');
        }



        $store_where = [
            'storeid'=>$data['storeid']
        ];
        $store = model('Store')->where($store_where)->find();
        if($store == null){
            $store['name'] = '测试';
        } else {
            $name = explode('@',$store['name']);
            $store['name'] = $name[1];
        }
        $temp = [];

        $titles = $store['name'].'('.$data['start_at'] .'|'. $data['end_at'].')';
        $fileName = $store['name'];
        $temp['fileName'] = $fileName;
        $temp['tableXls'] = $titles;
        
        $where = [];
        $where['storeid'] = $data['storeid'];
        $start = $data['start_at'].'00:00:00';
        $end = $data['end_at'].'23:59:59';
        $where['status'] = 1;
        $where['pay_status'] = array('in',[2,3,4]);
        $where['pay_time'] = ['between time',[$start,$end]];
        $data = model('Xmorder')->where($where)->select()->toarray();
        if(empty($data)){
            fail('暂时还没有订单');
        }
        
        $money = 0;
        $d_fee = 0;
        $r_count = [];
        $r_fee = 0;
        foreach($data as $k=>$v){
            $money += $v['pay_fee'];
            $d_fee += $v['discount'];
            if($v['pay_status'] == 3){
                $r_count[] = $v;
                if($v['refund_fee'] == null){
                    $r_fee += $v['pay_fee'];
                } else {
                    $r_fee += $v['refund_fee'];
                }
            }
        }
        $temp['arr'] = [
            [
                'name'  => '账单总数',
                'val'   => count($data),
            ],
            [
                'name'  => '账单总额',
                'val'  => $money,
            ],
            [
                'name'  => '优惠总额',
                'val'  => $d_fee,
            ],
            [
                'name'  => '退款单数',
                'val'  => count($r_count),
            ],
            [
                'name'  => '退款总额',
                'val'  => $r_fee,
            ],
            [
                'name'  => '合计',
                'val'  => $money-$r_fee,
            ],
            
        ];
        // $temp = [
        //     'count' => count($data), //账单总数
        //     'money' => $money,  //账单总额
        //     'd_fee' => $d_fee,  //优惠总额
        //     'r_count'   => count($r_count), //退款单数
        //     'r_fee' =>  $r_fee, //退款总额
        //     'fee'   => $money-$r_fee, //合计
        // ];
        // $temp = [];

        //菜品汇总
        $goods_sum = [];
        $gcount=0;
        foreach($data as $key=>$val){
            if($val['pay_status'] !== 3){
                $gcount++;
                $order_goods_where = [
                    'order_id'  => $val['order_sn']
                ];
                $a = model('Xmordergoods')->where($order_goods_where)->find();
                if($a != null){
                    $a = $a->toArray();
                    $goods_where = [
                        'gid'  => $a['goods_id']
                    ];
                    $gdata = model('Goods')->where($goods_where)->find();
                    if($gdata != null){
                        $gdata = $gdata->toArray();

                        $good_type_where = [
                            'gtid'  => $gdata['gtid']
                        ];
                        $tdata = model('GoodsType')->where($good_type_where)->find();
                        if($tdata != null){
                            $a['gtid']  = $tdata['gtid'];
                            $a['gtname'] = $tdata['gtname'];
                        } else {
                            $a['gtid']  = '';
                            $a['gtname'] = '';
                        }
                    } else {
                        $a['gtid']  = '';
                        $a['gtname'] = '';
                    }
                    $a['pay_fee'] = $val['pay_fee'];
                    $goods_sum[] = $a;
                }
            }
        }
        $goodsData = $this->array_group_by($goods_sum,'gtname');
        $newgoods = [];
        $g_fee = 0;
        foreach($goodsData as $kk=>$vv){
            $g_money = 0;
            foreach($vv as $ks=>$vs){
                $g_money += $vs['pay_fee'];
                $g_fee += $vs['pay_fee'];
            }
            $newgoods[] = [
                'type_name' => $kk,
                'count'     => count($vv),
                'money'     => $g_money
            ];
        }
        $newgoods[]=[
            'type_name' => '小计',
            'count'     => $gcount,
            'money'     => $g_fee
        ];



        //菜品明细
        $goods_detailed = [];
        $dcount=0;
        foreach($data as $key=>$val){
            if($val['pay_status'] !== 3){
                $dcount++;
                $order_goods_where = [
                    'order_id'  => $val['order_sn']
                ];
                $a = model('Xmordergoods')->where($order_goods_where)->find();
                if($a != null){
                    $a = $a->toArray();
                    $goods_where = [
                        'gid'  => $a['goods_id']
                    ];
                    $gdata = model('Goods')->where($goods_where)->find();
                    if($gdata != null){
                        $gdata = $gdata->toArray();

                        $good_type_where = [
                            'gtid'  => $gdata['gtid']
                        ];
                        $tdata = model('GoodsType')->where($good_type_where)->find();
                        if($tdata != null){
                            $gdata['gtname'] = $tdata['gtname'];
                        } else {
                            $gdata['gtname'] = '';
                        }
                        $gdata['pay_fee'] = $val['pay_fee'];
                        $goods_detailed[] = $gdata;
                    }
                    
                }
            }
        }
        $goods_arr = $this->array_group_by($goods_detailed,'name');
        // $goods_arr = $this->array_group_by($goods_detailed,'gtname');
        // foreach($goods_arr as $k=>$v){
        //     dump($v);exit;
        // }
        // dump($goods_arr);exit;
        $new_goods_data = [];
        $d_fee = 0;
        $order_where = [];
        foreach($goods_arr as $dk=>$dv){
            $d_money = 0;
            $gtname = '';
            foreach($dv as $dkk=>$dvv){
                $d_fee += $dvv['pay_fee'];
                $d_money += $dvv['pay_fee'];
                $gtname = $dvv['gtname'];
            }
            $order_where[] = $gtname;
            $new_goods_data[] = [
                'gtname' => $gtname,
                'gname'  => $dk,
                'count'  => count($dv),
                'money'  => $d_money,
            ];
        }
        array_multisort($order_where, SORT_ASC, $new_goods_data);
        $new_goods_data[] = [
            'gtname' => '',
            'gname' => '小计',
            'count' => $dcount,
            'money' => $d_fee
        ];
        foreach($new_goods_data as $kks=>$vvs){
            $new_goods_data[$kks]['key'] = $kks;
        }
        $goods_arr_marge = $this->array_group_by($new_goods_data,'gtname');

        $temp['data_marge'] = $goods_arr_marge;

        $temp['type_data'] = [
            'data'  => $newgoods,
        ];
        $temp['d_data'] = [
            'data'  => $new_goods_data,
        ];
        $this->phpExcel($temp);
    }

        /**
     * 导出Excel表
     * @return [type] [description]
     */
    public function phpExcel($data){

        $xlsData = $data['arr'];
        $typeData = $data['type_data']['data'];
        $dData = $data['d_data']['data'];
        $fileName = $data['fileName'];
        $data_marge = $data['data_marge'];
        // dump($fileName);exit;
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

        $objActSheet->setTitle($data['tableXls']);//设置sheet工作表名称
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G");
        $arrHeader = array('账单总汇');
        //合并单元格
        $objExcel->getActiveSheet()->mergeCells('A1:P1');
        $objExcel->getActiveSheet()->mergeCells('A2:P2');

        //设置水平居中
        $objExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //填充标题
            $objActSheet->setCellValue($letter[0].'1',$data['tableXls']);

        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]2","$arrHeader[$i]");

        };

        //填充表格信息
        foreach($xlsData as $k=>$v){
            $k +=3;
            $objActSheet->setCellValue('B'.$k,$v['name']);
            $objActSheet->setCellValue('C'.$k, $v['val']);
            // $objActSheet->setCellValue('C'.$k, $v['home_code']);
            // $objActSheet->setCellValue('D'.$k, $v['name']);
            // $objActSheet->setCellValue('E'.$k, $v['tel']);
            // $objActSheet->setCellValue('F'.$k, $v['area']);
            // $objActSheet->setCellValue('G'.$k, $v['check_in_at']);
            // $objActSheet->setCellValue('D'.$k, $v['email']);
            // $objActSheet->setCellValue('E'.$k, $v['tel']);
            // $objActSheet->setCellValue('F'.$k, $v['position']);
            // $objActSheet->setCellValue('G'.$k, $v['apphang']);
            // $objActSheet->setCellValue('H'.$k, $v['role_id'] == 2?'正式会员':'普通会员');
            // $objActSheet->setCellValue('I'.$k, $v['active'] == 1?'是':'否');
            // 表格高度
            // $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
        //菜品总汇
        $last = end($xlsData);
        $last_key = key($xlsData)+5;
        $objExcel->getActiveSheet()->mergeCells('A'.$last_key.':P'.$last_key);
        $titleHeader = array('菜品总汇');
        $titleHeaderArr = array('分类名称','数量','金额');
        //填充表头信息
        $lenth =  count($titleHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]$last_key","$titleHeader[$i]")->getStyle('A'.$last_key.':P'.$last_key)->getFont()->setBold(true);
        };
        $lenth =  count($titleHeaderArr);
        for($i = 0;$i < $lenth;$i++) {
            $k = $i;
            $k++;
            $objActSheet->setCellValue("$letter[$k]".($last_key+1)."","$titleHeaderArr[$i]");
        };
        foreach($typeData as $kk=>$vv){
            $kk += ($last_key+2);
            $objActSheet->setCellValue('B'.$kk,$vv['type_name']);
            $objActSheet->setCellValue('C'.$kk, $vv['count']);
            $objActSheet->setCellValue('D'.$kk, $vv['money']);
        }

        //菜品销售明细.
        $lasts = end($typeData);
        $last_keys = key($typeData)+$last_key+4;

        $objExcel->getActiveSheet()->mergeCells('A'.$last_keys.':P'.$last_keys);
        $dHeader = array('菜品销售明细');
        $dHeaderArr = array('分类名称','菜品名称','数量','金额');
        //填充表头信息
        $lenth =  count($dHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]$last_keys","$dHeader[$i]")->getStyle('A'.$last_keys.':P'.$last_keys)->getFont()->setBold(true);
        };
        $lenth =  count($titleHeaderArr);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]".($last_keys+1)."","$dHeaderArr[$i]");
        };
        foreach($data_marge as $c=>$l){
            if(count($l)>=2){
                $first = array_shift($l);
                $one = $first['key'] + ($last_keys+2);
                $last = array_pop($l);
                $end = $last['key'] + ($last_keys+2);
                $objExcel->getActiveSheet()->mergeCells('A'.$one.':A'.$end);
            }
        }
        foreach($dData as $dk=>$dv){
            $dk += ($last_keys+2);
            $objActSheet->setCellValue('A'.$dk,$dv['gtname']);
            $objActSheet->setCellValue('B'.$dk, $dv['gname']);
            $objActSheet->setCellValue('C'.$dk, $dv['count']);
            $objActSheet->setCellValue('D'.$dk, $dv['money']);
        }
        $width = array(10,15,20,25,30);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth($width[1]);
        $objActSheet->getColumnDimension('B')->setWidth($width[2]);
        $objActSheet->getColumnDimension('C')->setWidth($width[3]);
        $objActSheet->getColumnDimension('D')->setWidth($width[4]);
        $objActSheet->getColumnDimension('E')->setWidth($width[1]);
        $objActSheet->getColumnDimension('F')->setWidth($width[1]);
        $objActSheet->getColumnDimension('G')->setWidth($width[1]);
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
     * Notes: 获取点餐密钥
     * Class: getAirscanKay
     * user: bingwoo
     * date: 2020/12/15 10:32
     */
    public function getAirscanKay(){

        $key = 'airscan'.date('YmdHis',time()).rand(0000,9999);
        return md5($key);
    }
}
