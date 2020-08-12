<?php
namespace app\index\model;
use think\Model;
class District extends Model{
   protected $table='district';
   //定义时间戳字段名;
   protected $createTime='ctime';
   protected $updateTime=false;


}