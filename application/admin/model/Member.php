<?php
namespace app\admin\model;
use think\Model;
class Member extends Model
{
    protected $table = 'xm_user';
    //定义时间戳字段名;
    protected $createTime = false;
    protected $updateTime = false;
}