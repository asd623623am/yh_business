<?php
namespace app\admin\model;
use think\Model;

class Order extends Model
{
    protected $table = 'order';

    /*收费单类型*/
    const PROPERTY_TYPE=0;//物业
    const HEATING_TYPE=1;//供暖

    /*缴费状态*/
    const STATUS_FINISH=1; //已缴费
    const STATUS_NOT_FINISH=0; //未缴费

    /*发布状态*/
    const STATUS_PUBLISH=1; //已发布
    const STATUS_NOT_PUBLISH=0; //未发布

    /*发票状态*/
    const IS_INVOICE=1; //已发布
    const IS_NOT_INVOICE=0; //未发布

    /*支付类型*/
    const PAY_TYPE_CASH=1; //现金支付
    const PAY_TYPE_POSE=2; //POSE机支付
    const PAY_TYPE_WECHAT=3; //微信支付
    public function attributes()
    {
        return  array(
            "id"=>'',               //id
            "status"=>'',       //发布状态
            "pay_status"=>'',       //支付状态
            "title"=>'',            //标题
            "house_id"=>'',         //房屋ID
            "fee"=>'',              //缴费金额
            "pay_fee"=>'',          //已缴金额
            "compensation"=>'',     //赔偿项
            "voucher"=>'',          //凭证号
            "start_at"=>'',         //开始时间
            "end_at"=>'',           //结束时间
            "utime"=>'',
            "ctime"=>''
        );
    }
    public static function downlist($type="",$val="")
    {
        $type_array=[
            "paytype"=>[
                [
                    "val"=>self::PAY_TYPE_CASH,
                    "name"=>"现金支付"
                ],
                [
                    "val"=>self::PAY_TYPE_POSE,
                    "name"=>"POSE机支付"
                ],
                [
                    "val"=>self::PAY_TYPE_WECHAT,
                    "name"=>"微信支付"
                ]
            ],
            "type"=>[
                [
                    "val"=>self::PROPERTY_TYPE,
                    "name"=>"物流收费"
                ],
                [
                    "val"=>self::HEATING_TYPE,
                    "name"=>"供暖收费"
                ]
            ],
            "invoice"=>[
                [
                    "val"=>self::IS_INVOICE,
                    "name"=>"已开发票"
                ],
                [
                    "val"=>self::IS_NOT_INVOICE,
                    "name"=>"未开发票"
                ]
            ]
        ];
        return $type?($val?array_column($type_array[$type],"name","val")[$val]:$type_array[$type]):$type_array;
    }



}