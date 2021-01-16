<?php
namespace app\admin\model;
use think\Model;
class System extends Model{
   protected $table='system';
   //定义时间戳字段名;
   protected $createTime='ctime';
   protected $updateTime=false;


    /**
     * Notes:
     * Class: getSystemInfo
     * user: bingwoo
     * date: 2021/1/15 11:05
     */
   public function getSystemInfo($where = [],$field = '*'){
       $query = $this;
       if($where){
           $query = $query->where($where);
       }
       $data = $query->field($field)->find();
       if($data){
           $data = $data->toArray();
       }
       return $data;
   }
}