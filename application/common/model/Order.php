<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: Order
 * user: bingwoo
 * date: 2020/12/4 13:16
 */
class Order extends Model{

    protected $table = 'xm_order';
    protected $isMustField = ['storeid'=>'门店id','uid'=>'会员id','order_sn'=>'订单号'];
    protected $isNoMustField = ['pay_trans_no'=>'银河支付的支付流水号','payment_sn'=>'支付回传订单号','order_type'=>'订单类型',
                                'order_status'=>'订单的状态','shipping_status'=>'商品配送情况','pay_status'=>'支付状态',
                                'pay_id'=>'支付方式','pay_time'=>'支付时间','goods_amount'=>'商品总数量','goods_fee'=>'商品总金额',
                                'discount'=>'折扣','pay_fee'=>'支付金额','receivables'=>'应收款','remark'=>'备注',
                                'tnumber'=>'桌号','status'=>'状态','staff'=>'是否是员工','usernum'=>'用餐人数',
                                'openid'=>'微信小程序openid','packagefee'=>'打包费','Invoice'=>'是否开发票',
                                'gz_openid'=>'公众号openid','is_new'=>'是否是新订单','refund_fee'=>'退款金额'];

    /**
     * Notes: 验证必传字段
     * Class: isVerifyMust
     * user: bingwoo
     * date: 2020/11/20 17:20
     */
    public function isVerifyMustField($postData){
        foreach ($this->isMustField as $k => $v){
            if(!isset($postData[$k])){
                failMsg($v.'不能为空');
            }
        }
        return $postData;
    }

    /**
     * Notes: 验证非必传字段
     * Class: isVerifyNoMustField
     * user: bingwoo
     * date: 2020/11/20 17:22
     */
    public function isVerifyNoMustField($postData){
        foreach ($this->isNoMustField as $k => $v){
            if(empty($postData[$k])){
                unset($postData[$k]);
            }
            if(isset($postData[$k])){
                if($postData[$k] == 'on'){
                    $postData[$k] = 2;
                }
            }
        }
        return $postData;
    }

    /**
     * Notes: 获取订单列表
     * Class: getOrderList
     * user: bingwoo
     * date: 2020/12/4 13:30
     */
    public function getOrderList($where = [],$field = '*',$page = 0,$limit = 20,$order = '',$change = false){

        $query = $this->field($field);
        if($where){
            $query = $query->where($where);
        }
        if($order){
            $query = $query->order($order);
        }
        if($page){
            $query = $query->page($page,$limit);
        }
        $data = $query->select()->toArray();
        if($change){
            foreach ($data as &$val){
                $val = $this->changeName($val);
            }
        }
        $count = $query->where($where)->count();
        return ['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];

    }

    /**
     * Notes: 获取订单集合
     * Class: getOrderSum
     * user: bingwoo
     * date: 2020/12/21 15:50
     */
    public function getOrderSum($where = [],$field){
        return $this->where($where)->sum($field);
    }

    /**
     * Notes: 获取订单详情
     * Class: getOrderInfo
     * user: bingwoo
     * date: 2020/12/4 13:31
     */
    public function getOrderInfo($where = [],$field = '*',$Order = ''){
        $qurey = $this;
        if(!empty($where)){
            $qurey = $qurey->where($where);
        }
        if(!empty($Order)){
            $qurey = $qurey->order($Order);
        }
        $data = $qurey->field($field)->find();
        if(empty($data)){
            return false;
        }
        $data = $data->toArray();
        $data = $this->changeName($data);
        return $data;
    }

    /**
     * Notes: 添加订单信息
     * Class: addOrderData
     * user: bingwoo
     * date: 2020/12/4 13:32
     */
    public function addOrderData($addData,$isGetID = false){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        if($isGetID){
            $ret = $this->insertGetId($addData);
        }else{
            $ret = $this->insert($addData);
        }
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改订单信息
     * Class: editOrderData
     * user: bingwoo
     * date: 2020/12/4 13:32
     */
    public function editOrderData($id,$saveData,$where = []){

        if($where){
            $ret = $this->where($where)->update($saveData);
        }else{
            $ret = $this->where(['orderid'=>$id])->update($saveData);
        }
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除订单信息
     * Class: delOrderData
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function delOrderData($id){

        $ret = $this->where(['orderid'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量添加订单信息
     * Class: addOrderDatas
     * user: bingwoo
     * date: 2020/12/17 16:35
     */
    public function addOrderDatas($adddDatas){
        $ret = $this->insertAll($adddDatas);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除订单信息
     * Class: delOrderDatas
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function delOrderDatas($ids){

        $ret = $this->where('orderid','in',$ids)->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 转换名称
     * Class: changeName
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function changeName(array $data){

        //订单类型
        if(isset($data['order_type'])){
            $data['order_type_name'] = '堂食';
            if($data['order_type'] == 2){
                $data['order_type_name'] = '外带';
            }
        }
        //订单的状态
        if(isset($data['order_status'])){
            if($data['order_status'] == 0){
                $data['order_status_name'] = '未确认';
            }else if($data['order_status'] == 1){
                $data['order_status_name'] = '已确认';
            }else if($data['order_status'] == 2){
                $data['order_status_name'] = '已取消';
            }else if($data['order_status'] == 3){
                $data['order_status_name'] = '已无效';
            }else if($data['order_status'] == 4){
                $data['order_status_name'] = '已退款';
            }else if($data['order_status'] == 5){
                $data['order_status_name'] = '已完成';
            }
        }
        //商品配送情况
        if(isset($data['shipping_status'])){
            if($data['shipping_status'] == 0){
                $data['shipping_status_name'] = '未发货';
            }else if($data['shipping_status'] == 1){
                $data['shipping_status_name'] = '已发货';
            }else if($data['shipping_status'] == 2){
                $data['shipping_status_name'] = '已收货';
            }else if($data['shipping_status'] == 4){
                $data['shipping_status_name'] = '已退货';
            }
        }
        //支付状态
        if(isset($data['pay_status'])){
            if($data['pay_status'] == 0){
                $data['pay_status_name'] = '未付款';
            }else if($data['pay_status'] == 1){
                $data['pay_status_name'] = '付款中';
            }else if($data['pay_status'] == 2){
                $data['pay_status_name'] = '已付款';
            }else if($data['pay_status'] == 3){
                $data['pay_status_name'] = '已退款';
            }
        }
        //支付时间
        if (isset($data['pay_time'])){

            $data['is_pay'] = 2;//2不可以退款
            if(!empty($data['pay_time'])){
                $newtime = date('Y-m-d',$data['pay_time']);
                $newtime = $newtime.' 23:59:59';
                if(strtotime($newtime) < time()){
                    $data['is_pay'] = 1; //1是可以退款
                }
                $data['pay_time'] = date('Y-m-d H:i:s',$data['pay_time']);
            }
        }
        //订单状态
        if(isset($data['status'])){
            $data['status_name'] = '正常';
            if($data['status'] == 0){
                $data['status_name'] = '已删除';
            }
        }
        //是否未员工
        if(isset($data['staff'])){
            $data['staff_name'] = '非员工';
            if($data['staff'] == 0){
                $data['staff_name'] = '员工';
            }
        }
        //是否开发票
        if(isset($data['Invoice'])){
            $data['Invoice_name'] = '未开发票';
            if($data['Invoice'] == 0){
                $data['Invoice_name'] = '已开发票';
            }
        }
        //是否是新订单
        if(isset($data['is_new'])){
            $data['is_new_name'] = '新订单';
            if($data['is_new'] == 0){
                $data['is_new_name'] = '非新订单';
            }
        }
        //获取门店名称
        if(isset($data['storeid'])){
            $storeModel = new Store();
            $storeWhere = [];
            $storeWhere['storeid'] = ['=',$data['storeid']];
            $data['sname'] = '';
            $storeInfo = $storeModel->getStoreInfo($storeWhere,'name');
            if($storeInfo){
                $data['sname'] = $storeInfo['name'];
            }
        }
//        //获取会员名称
//        if(isset($data['uid'])){
//            $userModel = new User();
//            $uWhere = [];
//            $uWhere['uid'] = ['=',$data['uid']];
//            $data['uname'] = '';
//            $userInfo = $userModel->getUserInfo($uWhere,'name');
//            if($userInfo){
//                $data['sname'] = $userInfo['name'];
//            }
//        }

        //支付方式名称
        if(isset($data['pay_id'])){
            $data['pay_id_name'] = '微信';
            if($data['pay_id'] != 1){
                $data['pay_id_name'] = '其他';
            }
        }
        return $data;
    }

}