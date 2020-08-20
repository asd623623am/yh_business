<?php

namespace app\admin\model;

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

    /**
     * Notes: 验证商品必传信息
     * Class: isVerificationField
     * user: bingwoo
     * date: 2020/8/17 21:55
     */
    public static function  isVerificationField($data,$saveData){

        $mustField = [
            'name'=>'商品名称',
            'code'=>'商品编号',
            'gtid'=>'商品分类',
            'selling_price'=>'商品售价',
        ];
        foreach ($mustField as $k => $val){
            if(isset($data[$k]) && empty($data[$k])){
                fail($val.'不能为空') ;
            }else{
                $saveData[$k] = $data[$k];
            }
        }
        return $saveData;
    }

    /**
     * Notes: 验证商品非必传信息
     * Class: isNOVerificationField
     * user: bingwoo
     * date: 2020/8/17 21:55
     */
    public static function  isNoVerificationField($data,$saveData){

        $isNoVerifield = ['sort','original_price','gsids','member_price','img','staff_price','is_special',
            'is_open_stock','stock','trade_description'];
        foreach ($isNoVerifield as $val){
            if(!empty($data[$val])){
                if($val == 'is_open_stock' || $val == 'is_special' ){
                    $saveData[$val] = 1;
                }else{
                    $saveData[$val] = $data[$val];
                }
            }
        }
        return $saveData;
    }
}