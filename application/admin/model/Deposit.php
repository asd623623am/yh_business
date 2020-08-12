<?php
namespace app\admin\model;
use think\Model;
class Deposit extends Model
{
    protected $table = 'deposit';
    //定义时间戳字段名;
    protected $createTime = 'ctime';
    protected $updateTime = false;
}