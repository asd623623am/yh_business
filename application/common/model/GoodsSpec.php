<?php

namespace app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: GoodsSpec
 * user: bingwoo
 * date: 2020/12/8 17:50
 */
class GoodsSpec extends Model{

    protected $table='xm_goods_spec';

    protected $isMustField = ['gstid'=>'规格分类id', 'gsname'=>'商品规格名称'];
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
     * Notes: 获取商品规格列表
     * Class: getGoodsSpaceTypeList
     * user: bingwoo
     * date: 2020/12/8 17:42
     */
    public function getGoodsSpaceList($where = [],$field = '*',$page = 0,$limit = 20,$order = ''){

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
     * Notes: 获取商品规格信息
     * Class: getGoodsSpecInfo
     * user: bingwoo
     * date: 2020/12/8 17:42
     */
    public function getGoodsSpecInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 添加商品规格信息
     * Class: addGoodsSpecData
     * user: bingwoo
     * date: 2020/12/8 17:42
     */
    public function addGoodsSpecData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改商品规格信息
     * Class: editGoodsSpecData
     * user: bingwoo
     * date: 2020/12/8 17:43
     */
    public function editGoodsSpecData($id,$saveData){

        $ret = $this->where(['id'=>$id])->update($saveData);
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除商品规格信息
     * Class: delGoodsSpaceData
     * user: bingwoo
     * date: 2020/12/8 17:45
     */
    public function delGoodsSpaceData($id){

        $ret = $this->where(['id'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除商品规格信息
     * Class: delGoodsSpaceDatas
     * user: bingwoo
     * date: 2020/12/8 17:46
     */
    public function delGoodsSpaceDatas($ids){

        $ret = $this->where('id','in',$ids)->delete();
        if($ret){
            return true;
        }
        return false;
    }



}