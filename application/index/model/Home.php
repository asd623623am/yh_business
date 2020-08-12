<?php
namespace app\index\model;
use think\Model;
class Home extends Model{
   protected $table='home';
   //定义时间戳字段名;
   protected $createTime='ctime';
   protected $updateTime=false;


}