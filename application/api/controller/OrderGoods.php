<?php
namespace app\api\controller;
use app\admin\model\Xmordergoods;
use think\Controller;

class OrderGoods extends Controller{

    /**
     * Notes: 获取订单商品信息
     * Class: getOrderGoodsList
     * user: bingwoo
     * date: 2020/12/17 13:38
     */
    public function getOrderGoodsList(){

        $getData = input('get.');
        $ogModel = new Xmordergoods();
        $where = [];
        $where['order_sn'] = ['=',$getData['order_sn']];
        $data = $ogModel->where($where)->select();
        if($data){
            $data = $data->toArray();
        }
        return json($data);
    }




}