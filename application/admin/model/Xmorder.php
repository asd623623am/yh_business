<?php
namespace app\admin\model;
use think\Model;
class Xmorder extends Model{
        protected $table='xm_order';

        //定义时间戳字段名;
        protected $createTime=false;
        protected $updateTime=false;
}