<?php
namespace app\admin\controller;


/**
 * Notes:商品绑定规格管理
 * Created by PhpStorm.
 * user: bingwoo
 * date: 2020/8/11  
 * time: 下午 01:38
 */
class GoodsBingSpec extends Common{

    protected $table='xm_goods_bing_spec';

    //定义时间戳字段名;
    protected $createTime = 'create_time';
    protected $update_time = 'update_time';

}
