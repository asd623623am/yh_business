<?php
namespace app\admin\model;
use think\Model;
class Posconfg extends Model{
        protected $table='posconfg';

        //定义时间戳字段名;
        protected $createTime=false;
        protected $updateTime=false;
}