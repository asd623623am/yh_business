<?php
namespace app\index\model;
use think\Model;
class House extends Model{
   protected $table='house';
   //定义时间戳字段名;
   protected $createTime='ctime';
   protected $updateTime=false;


}