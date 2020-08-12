<?php
namespace app\admin\model;
use think\Model;
class Phone extends Model{
   protected $table='phone';
   //定义时间戳字段名;
   protected $createTime='ctime';
   protected $updateTime=false;


}