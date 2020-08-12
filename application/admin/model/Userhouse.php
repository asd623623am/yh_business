<?php
namespace app\admin\model;
use think\Model;
class Userhouse extends Model{
        protected $table='user_house';

        //定义时间戳字段名;
        protected $createTime=false;
        protected $updateTime=false;
}