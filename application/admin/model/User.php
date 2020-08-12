<?php
namespace app\admin\model;
use think\Model;
class User extends Model{
        protected $table='user';

        //定义时间戳字段名;
        protected $createTime=false;
        protected $updateTime=false;
}