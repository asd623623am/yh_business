<?php
namespace app\index\model;
use think\Model;
class Brand extends Model
{
    protected $table = 'brand';
    //定义时间戳字段名;
    protected $createTime = 'brand_time';
    protected $updateTime = false;
}