<?php
namespace app\index\model;
use think\Model;
class Applets extends Model{
        protected $table='applets';

        //定义时间戳字段名;
        protected $createTime=false;
        protected $updateTime=false;
}