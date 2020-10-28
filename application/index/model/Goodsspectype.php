<?php

namespace app\index\model;

use think\Model;

/**
 * Notes: 菜品规格分类管理
 * Created: by PhpStorm.
 * package: app\admin\model
 * Class: GoodsSpecType
 * user: bingwoo
 * date: 2020/8/17 9:42
 */
class Goodsspectype extends Model{

    protected $table = 'xm_goods_spec_type';

    //定义时间戳字段名;
    protected $createTime = 'create_time';
    protected $update_time = 'update_time';

}
