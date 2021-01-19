<?php
namespace app\api\controller;
use app\admin\model\Xmorder;
use think\Controller;

class Order extends Controller{

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
        $where['storeid'] = ['=',$getData['storeid']];
        $orderData = $orderModel->getOrderList($where);
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
        $orderModel = new Xmorder();
        $where = [];
        $where['order_sn'] = ['=',$getData['order_sn']];
        $data = $orderModel->where($where)->find();
        if($data){
            $data = $data->toArray();
        }
        return json($data);

    }




}