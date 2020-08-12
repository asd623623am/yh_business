<?php
namespace app\admin\model;
use think\Model;

class PayRecord extends Model
{
    protected $table = 'pay_record';

    /*收费单类型*/
    const PROPERTY_TYPE=0;//物业
    const HEATING_TYPE=1;//供暖

    /*支付状态*/
    const STATUS_PAY=1; //已支付
    const STATUS_NOT_PAY=0; //未支付
    const STATUS_REFUND=-1; //已退款

    /*支付类型*/
    const PAY_TYPE_CASH=1; //现金支付
    const PAY_TYPE_POSE=2; //POSE机支付
    const PAY_TYPE_WECHAT=3; //微信支付

    public static function attributes()
    {
        return  array(
            "id"=>'',               //id
            "status"=>'',           //支付状态
            "payment_sn"=>'',       //支付订单号
            "order_id"=>'',         //订单ID
            "type"=>'',             //费用类别
            "pay_type"=>'',         //支付方式
            "amount"=>'',           //支付金额
            "openid"=>'',           //用户OPENID
            "operator_id"=>'',      //后台操作者ID
            "remark"=>'',           //备注
            "pay_at"=>'',           //支付时间
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
            "status"=>[
                [
                    "val"=>self::STATUS_PAY,
                    "name"=>"已支付"
                ],
                [
                    "val"=>self::STATUS_NOT_PAY,
                    "name"=>"待支付"
                ],
                [
                    "val"=>self::STATUS_REFUND,
                    "name"=>"已退款"
                ]
            ]
        ];
        return $type?($val?array_column($type_array[$type],"name","val")[$val]:$type_array[$type]):$type_array;
    }


}