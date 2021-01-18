<?php

namespace app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: GoodsType
 * user: bingwoo
 * date: 2020/12/22 14:34
 */
class GoodsType extends Model{

    protected $table='xm_goods_type';
    protected $isMustField = ['storeid'=>'门店id', 'gtname'=>'商品分类名称','sort'=>'排序'];
    protected $isNoMustField = ['status'=>'是否删除'];

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
     * Notes: 获取商品分类列表
     * Class: getGoodsTypeList
     * user: bingwoo
     * date: 2020/12/4 13:30
     */
    public function getGoodsTypeList($where = [],$field = '*',$page = 0,$limit = 20,$order = ''){

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
     * Notes: 获取商品分类详情
     * Class: getGoodsTypeInfo
     * user: bingwoo
     * date: 2020/12/4 13:31
     */
    public function getGoodsTypeInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 添加商品分类信息
     * Class: addGoodsTypeData
     * user: bingwoo
     * date: 2020/12/4 13:32
     */
    public function addGoodsTypeData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改商品分类信息
     * Class: editOrderTypeData
     * user: bingwoo
     * date: 2020/12/4 13:32
     */
    public function editGoodsTypeData($id = '',$saveData,$where = []){

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
     * Notes: 删除商品分类信息
     * Class: delGoodsTypeData
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function delGoodsTypeData($id){

        $ret = $this->where(['id'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除商品分类信息
     * Class: delGoodsTypeDatas
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function delGoodsTypeDatas($ids){

        $ret = $this->where('id','in',$ids)->delete();
        if($ret){
            return true;
        }
        return false;
    }



}