<?php

namespace app\admin\model;
use think\Model;

/**
 * Notes:商品管理
 * Created: by PhpStorm.
 * package: app\admin\model
 * Class: GoodsType
 * user: bingwoo
 * date: 2020/8/17 9:42
 */
class Goods extends Model{
   protected $table='xm_goods';
   //定义时间戳字段名;
   protected $createTime='admin_time';
   protected $updateTime=false;

}