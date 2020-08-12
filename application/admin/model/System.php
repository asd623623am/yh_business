<?php
namespace app\admin\model;
use think\Model;
class System extends Model{
   protected $table='system';
   //定义时间戳字段名;
   protected $createTime='ctime';
   protected $updateTime=false;


}