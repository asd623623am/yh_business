<?php
namespace app\index\model;
use think\Model;
class User extends Model{
   protected $table='user';
   //定义时间戳字段名;
   protected $createTime='ctime';
   protected $updateTime=false;


}