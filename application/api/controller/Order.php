<?php
namespace app\api\controller;
use app\admin\model\Xmorder;
use think\Controller;

class Order extends Controller{


    /**
     * Notes: 获取订单数量
     * Class: getOrderCount
     * user: bingwoo
     * date: 2021/1/19 19:45
     */
    public function getOrderCount(){

        $getData = input('');
        $orderModel = new \app\common\model\Order();
        $where = [];
        if(isset($getData['storeid'])){
            $where['storeid'] = ['=',$getData['storeid']];
        }
        if(isset($getData['wx_openid'])){
            $where['wx_openid'] = ['=',$getData['wx_openid']];
        }
        $orderCount = $orderModel->getOrderCount($where);
        return json($orderCount);
    }

    /**
     * Notes: 获取订单列表
     * Class: getOrderList
     * user: bingwoo
     * date: 2020/12/17 13:42
     */
    public function getOrderList(){

        $getData = input('');
        $orderModel = new \app\common\model\Order();
        $where = [];
        if(isset($getData['storeid'])){
            $where['storeid'] = ['=',$getData['storeid']];
        }
        if(isset($getData['wx_openid'])){
            $where['wx_openid'] = ['=',$getData['wx_openid']];
        }
        $page = 0;
        if(isset($getData['page'])){
            $page = $getData['page'];
        }
        $limit = 20;
        if(isset($getData['limit'])){
            $limit = $getData['limit'];
        }
        $field = '*';
        if(isset($getData['field'])){
            $field = $getData['field'];
        }
        $order = '';
        if(isset($getData['order'])){
            $order = $getData['order'];
        }
        $orderData = $orderModel->getOrderList($where,$field,$page,$limit,$order,false);
        $data = [];
        if($orderData['data']){
            $data = $orderData['data'];
            //获取订单商品数据
            if(isset($getData['goods'])){
                $goodsModel = new \app\common\model\OrderGoods();
                foreach ($data as &$val){
                    $gwhere = [];
                    $gwhere['order_id'] = ['=',$val['order_sn']];
                    $goodsData = $goodsModel->getOrderGoodsList($gwhere);
                    $val['goods'] = $goodsData['data'];
                }
            }
        }
        return json($data);
    }

    /**
     * Notes: 获取订单信息
     * Class: getOrderInfo
     * user: bingwoo
     * date: 2020/12/17 13:33
     */
    public function getOrderInfo(){

        $getData = input('');
        $verifData = ['order_sn','type'];
        verifColumn($verifData,$getData);
        $orderModel = new \app\common\model\Order();
        $where = [];
        $where['order_sn'] = ['=',$getData['order_sn']];
        $orderInfo = $orderModel->getOrderInfo($where,'*','',false);
        if(!empty($orderInfo)){
            $orderInfo['goods'] = [];
            if($getData['type'] == 'goods'){
                $ogWhere = [];
                $ogWhere['order_id'] = ['=',$getData['order_sn']];
                $ogModel = new \app\common\model\OrderGoods();
                $ogData = $ogModel->getOrderGoodsList($ogWhere);
                if(!$ogData['data']){
                    $orderInfo['goods'] = $ogData['data'];
                }
            }
        }
        return json($orderInfo);
    }

    /**
     * Notes: 修改订单信息
     * Class: editOrderInfo
     * user: bingwoo
     * date: 2021/3/8 16:49
     */
    public function editOrderInfo(){

        $postData = input();
        $orderModel = new \app\common\model\Order();
        $where = [];
        addFileLog('editOrderInfo','info',$postData);
        $where['order_sn'] = ['=',$postData['order_sn']];
        $saveData = [
            'order_status' => $postData['order_status'],
            'pay_status' => $postData['pay_status'],
            'pay_id' => $postData['pay_id'],
            'pay_time' => $postData['pay_time'],
        ];
        $ret = $orderModel->editOrderData(1,$saveData,$where);
        if(!$ret){
            failMsg('修改失败');
        }
        successMsg('修改成功');
    }




}