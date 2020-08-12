<?php
namespace app\admin\model;
use think\Model;

class Config extends Model
{
    protected $table = 'config';

    public static function attributes()
    {
        return  array(
            "id"=>'',                   //id
            "wx_appId"=>'',             //微信公众号appid
            "wx_secret"=>'',            //微信公众号secret
            "mini_program_appId"=>'',   //小程序appid
            "mini_program_secret"=>'',  //小程序secret
            "mchId"=>'',                //商户号
            "key"=>'',                  //支付密钥
            "certPath"=>'',             //证书路径
            "keyPath"=>'',              //密钥证书路径
        );
    }



}