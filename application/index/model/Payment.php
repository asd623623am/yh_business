<?php
namespace app\index\model;
use think\Model;
class Payment extends Model
{
    protected $table = 'payment';
    //定义时间戳字段名;
    protected $createTime = 'ctime';
    protected $updateTime = false;
}