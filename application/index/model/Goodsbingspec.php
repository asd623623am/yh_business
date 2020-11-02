<?php

namespace app\index\model;

use think\Model;

/**
 * Notes:商品绑定规格管理
 * Created by PhpStorm.
 * user: bingwoo
 * date: 2020/8/11  
 * time: 下午 01:38
 */
class Goodsbingspec extends Model{

    protected $table='xm_goods_bing_spec';

    //定义时间戳字段名;
    protected $createTime = 'create_time';
    protected $update_time = 'update_time';

}
