<?php
   namespace app\admin\model;
   use think\Model;

   class StorePrint extends Model{
       protected $table='xm_store_print';
       //定义时间戳字段名;
       protected $createTime='create_time';
       protected $updateTime='update_time';

       /**
        * @Notes: 验证必传字段
        * @user: bingwoo
        * @date: 2020/8/31  
        * @time: 下午 10:42
        */
       public static function isVerifField($postDate){
           $ismust = ['device_no'=>'设备编号','device_appid'=>'设备id','device_appkey'=>'设备Key','print_url'=>'打印地址'];
           $data = [];
           foreach ($ismust as $k => $val){
               if(empty($postDate[$k])){
                   fail($ismust['$val'].'必传');

               }else{
                   $data[$k] = $postDate[$k];
               }
           }
           return $data;

       }

   }