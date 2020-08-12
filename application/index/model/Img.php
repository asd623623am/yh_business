<?php
namespace app\index\model;
use think\Model;
class Img extends Model
{
    protected $table = 'images';
    //定义时间戳字段名;
    protected $createTime = 'ctime';
    protected $updateTime = false;
}