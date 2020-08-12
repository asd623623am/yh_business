<?php
namespace app\index\model;
use think\Model;
class Banner extends Model
{
    protected $table = 'banner';
    //定义时间戳字段名;
    protected $createTime = 'ctime';
    protected $updateTime = false;
}