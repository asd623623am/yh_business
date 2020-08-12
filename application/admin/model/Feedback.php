<?php
namespace app\admin\model;
use think\Model;
class Feedback extends Model
{
    protected $table = 'feedback';
    //定义时间戳字段名;
    protected $createTime = 'ctime';
    protected $updateTime = false;
}