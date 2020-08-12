<?php
namespace app\index\model;
use think\Model;
class Userhouse extends Model{
   protected $table='user_house';
   //定义时间戳字段名;
   protected $createTime='ctime';
   protected $updateTime=false;


}