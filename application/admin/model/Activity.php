<?php

namespace app\admin\model;
use think\Model;

class Activity extends Model{
   protected $table='xm_store_s';
   //定义时间戳字段名;
   protected $createTime='admin_time';
   protected $updateTime=false;

}