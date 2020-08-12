<?php
namespace app\admin\model;
use think\Model;
class Log extends Model
{
    protected $table = 'log';
    //定义时间戳字段名;
    protected $createTime = 'ctime';
    protected $updateTime = false;
}