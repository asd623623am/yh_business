<?php
namespace app\admin\model;
use think\Model;

class House extends Model
{
    protected $table = 'house';
    //定义时间戳字段名;
//    protected $createTime = 'ctime';
//    protected $updateTime = 'utime';
    public static function attributes()
    {
        return  array(
            "id"=>'',               //id
            "owner"=>'',            //业主
            "district_id"=>'',      //小区id
            "complex"=>'',          //区
            "building"=>'',         //楼
            "unit"=>'',             //单元
            "room"=>'',             //房间号
            "tel"=>'',              //联系方式
            "heating_cost"=>0,      //供暖费
            "property_fee"=>0,      //物业费
            'area'=>'',             //面积
            "check_in_at"=>'',      //入住时间
            "utime"=>'',
            "ctime"=>''
        );
    }
    public static function getHouse($data)
    {
        if (!$data)
        {
            return '';
        }
        $house=$data["district"]."-";
        if ($data["complex"])
        {
            $house.=$data["complex"]."区-";
        }
        if ($data["building"])
        {
            $house.=$data["building"]."楼-";
        }
        if ($data["unit"])
        {
            $house.=$data["unit"]."单元-";
        }
        if ($data["room"])
        {
            $house.=$data["room"];
        }
        return $house;
    }

}