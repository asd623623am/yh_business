<?php

namespace app\index\model;

use think\Model;

/**
 * Notes:菜品时段表
 * Created: by PhpStorm.
 * package: app\admin\model
 * Class: GoodsType
 * user: dongjie
 * date: 2020/8/17 9:42
 */
class GoodsTime extends Model{

   protected $table='xm_goods_time';

   //定义时间戳字段名;
   protected $createTime = 'ctime';
   protected $update_time = 'utime';

}