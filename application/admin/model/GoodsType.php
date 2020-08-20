<?php

namespace app\admin\model;

use think\Model;

/**
 * Notes:商品分类管理
 * Created: by PhpStorm.
 * package: app\admin\model
 * Class: GoodsType
 * user: bingwoo
 * date: 2020/8/17 9:42
 */
class GoodsType extends Model{

   protected $table='xm_goods_type';

   //定义时间戳字段名;
   protected $createTime = 'create_time';
   protected $update_time = 'update_time';

}