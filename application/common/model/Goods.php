<?php

namespace app\common\model;

use think\Model;

/**
 * Notes:商品管理
 * Created: by PhpStorm.
 * package: app\admin\model
 * Class: GoodsType
 * user: bingwoo
 * date: 2020/8/17 9:42
 */
class Goods extends Model{

    protected $table='xm_goods';
    protected $isMustField = ['name'=>'商品名称', 'code'=>'商品编号', 'gtid'=>'商品分类', 'selling_price'=>'商品售价',];
    protected $isNoMustField = ['sort'=>'排序值','original_price'=>'原价','member_price'=>'会员价','img'=>'商品图片',
                                'staff_price'=>'员工价','is_special'=>'是否为特色菜品','is_open_stock'=>'是否开启库存',
                                'stock'=>'库存','trade_description'=>'商品说明','is_show'=>'是否展示','status'=>'是否删除',
                                'is_selling_fragrance'=>'是否售馨','check_time'=>'盘点时间'];

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
     * Notes: 获取商品列表
     * Class: getGoodsList
     * user: bingwoo
     * date: 2020/12/4 13:30
     */
    public function getGoodsList($where = [],$field = '*',$page = 0,$limit = 20,$order = ''){

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
        $count = $query->count();
        return ['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];

    }

    /**
     * Notes: 获取商品详情
     * Class: getGoodsInfo
     * user: bingwoo
     * date: 2020/12/4 13:31
     */
    public function getGoodsInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 添加商品信息
     * Class: addGoodsData
     * user: bingwoo
     * date: 2020/12/4 13:32
     */
    public function addGoodsData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改商品信息
     * Class: editOrderData
     * user: bingwoo
     * date: 2020/12/4 13:32
     */
    public function editGoodsData($id = '',$saveData,$where = []){

        $ret = false;
        if($id){
            $ret = $this->where(['id'=>$id])->update($saveData);
        }
        if($where){
            $ret = $this->where($where)->update($saveData);
        }
        if($ret){
            return true;
        }
        return false;
    }


    /**
     * Notes: 删除商品信息
     * Class: delGoodsData
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function delGoodsData($id){

        $ret = $this->where(['id'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除商品信息
     * Class: delGoodsDatas
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function delGoodsDatas($ids){

        $ret = $this->where('id','in',$ids)->delete();
        if($ret){
            return true;
        }
        return false;
    }



}