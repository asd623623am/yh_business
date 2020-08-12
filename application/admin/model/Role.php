<?php
namespace app\admin\model;
use think\Model;
class Role extends Model{
        protected $table='role';

    //定义时间戳字段名;
    protected $createTime='ctime';
    protected $updateTime=false;
}