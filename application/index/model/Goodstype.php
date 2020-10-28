<?php

namespace app\index\model;
use think\Model;

class Goodstype extends Model{

    protected $table = 'xm_goods_type';
    //定义时间戳字段名;
    protected $createTime = 'create_time';
    protected $updateTime = false;
}