<?php

namespace app\admin\model;
use think\Model;

class Store extends Model{

   protected $table='xm_store';

   protected $createTime='create_time';
   protected $updateTime='update_time';


   public static function  isVerificationField($data){
       $mustField = [
           'name'=>'门店名称',
           'address'=>'门店地址',
           'user_name'=>'联系人',
           'usertel'=>'联系电话',
//           'start_business_hours'=>'营业开始时间',
//           'end_business_hours'=>'营业结束时间',
           'account'=>'登录账号',
           'passwords'=>'登录密码',
       ];
       foreach ($mustField as $k => $val){
           if(isset($data[$k]) && empty($data[$k])){
               fail($val.'不能为空') ;
           }
       }
       return true;
   }

}