<?php
namespace app\admin\model;
use think\Model;
class PowerNode extends Model{
        protected $table='power_node';

        //定义时间戳字段名;
        protected $createTime='ctime';
        protected $updateTime=false;
}