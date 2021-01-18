<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: Sms
 * user: bingwoo
 * date: 2020/12/28 13:34
 */
class Sms extends Model{

    /**
     * Notes: 短信案例
     * Class: index
     * user: bingwoo
     * date: 2020/12/28 13:59
     */
    public function index(){

        $tels = "18701188021";
        $xml = $this->sendCodebyPhone($tels);
        if ($xml->returnstatus == 'Success') {
            successMsg('发送成功');
        } else {
            failMsg('发送失败');
        }
    }

    /**
     * Notes: 发送验证码到指定手机
     * Class: sendCodebyPhone
     * user: bingwoo
     * date: 2020/12/28 14:22
     */
    public function sendCodebyPhone($phone){
        //验证手机格式
        verifPhone($phone);
        $mobiles=$phone;
        $extno = "";
        $number = rand(100000,999999);
        $redis = new \Redis();
        $redis->connect('127.0.0.1','6379');
        $redis->set($phone,$number,600);
        $content="尊敬的用户：您的验证码：".$number."。工作人员不会索取，为了保证信息安全，请勿泄露。【小码惠】";
        $sendtime = date('Y-m-d H:i:s',time());
        $result = $this->sendSms($mobiles,$content,$sendtime,$extno);
        $xml = simplexml_load_string($result);
        return $xml;
    }

    /**
     * Notes: 发送短信
     * @param $mobiles  //目标手机号码，多个用半角“,”分隔
     * @param $content  //发送内容
     * @param $sendtime //发送时间
     * @param $extno    //默认空字符串
     * @return array(project)
     */
    public function sendSms($mobiles,$content,$sendtime,$extno = ""){

        $url ="https://dx.ipyy.net/sms.aspx";
        $body=array(
            'action'=>'send',
            'userid'=>'',
            'account'=>'AA00639',
            'password'=>'AA0063920',
            'mobile'=>$mobiles,
            'extno'=>$extno,
            'content'=>$content,
            'sendtime'=>$sendtime
        );
        $result = request_post($url,$body);
        return $result;
    }

    /**
     * Notes: 发送验证码到指定手机（阿里云）
     * Class: aliyunSendSmsCode
     * user: bingwoo
     * date: 2021/1/14 18:17
     */
    public function aliyunSendSmsCode($phone){
        //验证手机格式
        verifPhone($phone);
        $redis = new \Redis();
        $code = rand(100000,999999);
        $redis->connect('127.0.0.1','6379');
        $redis->set($phone,$code,600);
        $ret = sendSms($phone,$code);
        return $ret;
    }

}