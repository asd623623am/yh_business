<?php
namespace app\index\model;
use think\Model;
class Qrcode extends Model{

   protected $table='xm_qrcode';

   //定义时间戳字段名;
   protected $createTime='create_time';
   protected $updateTime='update_time';

}
