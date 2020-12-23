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

        $getData = input('get.');
        $orderModel = new Xmorder();
        $where = [];
        $where['storeid'] = ['=',$getData['storeid']];
        $data = $orderModel->where($where)->select();
        if($data){
            $data = $data->toArray();
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

        $getData = input('get.');
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