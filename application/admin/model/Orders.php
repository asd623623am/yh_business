<?php
namespace app\admin\model;
use think\Model;
class Orders extends Model
{
    protected $table = 'order_no';
    //定义时间戳字段名;
    protected $createTime = 'ctime';
    protected $updateTime = false;
}