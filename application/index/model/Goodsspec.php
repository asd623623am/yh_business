<?php

namespace app\index\model;

use think\Model;

/**
 * Notes:商品分类管理
 * Created: by PhpStorm.
 * package: app\admin\model
 * Class: GoodsType
 * user: bingwoo
 * date: 2020/8/17 9:42
 */
class Goodsspec extends Model{

    protected $table = 'xm_goods_spec';

    //定义时间戳字段名;
    protected $createTime = 'create_time';
    protected $update_time = 'update_time';

}
