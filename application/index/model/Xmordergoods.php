<?php
namespace app\index\model;
use think\Model;
class Xmordergoods extends Model{
        protected $table='xm_order_goods';

        //定义时间戳字段名;
        protected $createTime=false;
        protected $updateTime=false;
}