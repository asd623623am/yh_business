<?php

namespace app\admin\model;

use think\Model;

/**
 * Notes:时段商品分类关联表
 * Created: by PhpStorm.
 * package: app\admin\model
 * Class: GoodsType
 * user: dongjie
 * date: 2020/8/17 9:42
 */
class GoodsTimetype extends Model{

   protected $table='xm_goods_time_type';

   //定义时间戳字段名;
   protected $createTime = 'ctime';
   protected $update_time = 'utime';

}