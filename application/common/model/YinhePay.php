<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: YinhePay
 * user: bingwoo
 * date: 2021/1/9 14:53
 */
class YinhePay extends Model{


    /**
     * Notes: 银河支付
     * Class: yinhepay
     * user: bingwoo
     * date: 2021/1/9 15:52
     */
    public function yinhepay($order_no,$money,$openid){

        $systemModel = new System();
        $where = [];
        $where['id'] = ['=',1];
        $systemInfo = $systemModel->getSystemInfo($where);
        $secret = $systemInfo['pay_secret'];
        $termNo = $systemInfo['pay_termNo'];
        $merId = $systemInfo['pay_merId'];
        $appId = $systemInfo['mini_appid'];
        $time = date('YmdHis',time());
        $param = [
            'orgNo'	    =>  '2166',
            'charset'	=>  'UTF-8',
            'termNo'	=>  $termNo, //设备号
            'termType'	=>  'KCWMP',
            'txtTime'	=>  $time,
            'signType'	=>  'MD5',
            'transNo'	=>  $order_no,
            'merId'		=>  $merId, //商户号
            'amt'		=>  intval($money), //交易金额
            'subject'	=>  '会员支付', //标题
            'backUrl'	=>  'http://notify.yhyrpay.com/Order/wxOrderNotify', //异步地址
            'payType'	=>  '1', //支付方式
            'ip'		=>  $_SERVER['REMOTE_ADDR'], //服务端ip
            'appId'		=>  $appId,
            'openId'	=>  $openid,
        ];
        $signdata = [
            'orgNo'	=>'2166',
            'amt'	=> intval($money),
            'termNo'=> $termNo,
            'merId'	=> $merId,
            'transNo'	=> $order_no,
            'txtTime'	=> $time
        ];
        $sign = $this->wxGetSign($signdata, $secret);
        $param['signValue'] = strtoupper($sign);
        $url = 'http://yhyr.com.cn/YinHeLoan/yinHe/yinHeWmpPay.action';
        $ret = send_post_yinhepay($url,$param);
        return $ret;
    }

    /**
     * Notes: 银河支付异步通知
     * Class: yinhePayNotic
     * user: bingwoo
     * date: 2021/1/9 15:52
     */
    public function yinhePayNotic(){
        $postData = input('');
        if(!$postData){
            failMsg('异步通知信息有误');
        }
        if($postData['returnCode'] != 0000){
            failMsg('支付失败');
        }
        $where = [
            'order_no'	=> $postData['transNo']
        ];
        if($userInfo['name']){
            $postData['user_name'] = $userInfo['name'];
        }
        if($userInfo['phone']){
            $postData['phone'] = $userInfo['phone'];
        }
        if($userInfo['wx_openid']){
            $postData['wx_openid'] = $userInfo['wx_openid'];
        }
        $postData['recharge_first'] = $userInfo['balance'];
        $rlModel = new RechargeLog();
        $rlModel->transaction();
        $countmoney = $rrInfo['recharge_monery']+$userInfo['countmoney'];
        $balance = $userInfo['balance'];
        $points = $userInfo['points'];
        //是否赠送余额1：否2：是
        if($rrInfo['is_give_monery'] == 2){
            $this->IS_UPDATE_USER = 2;
            $countmoney += $rrInfo['give_monery'];
            $balance += $rrInfo['give_monery'];
        }
        //是否赠送积分1：否2：是
        if($rrInfo['is_give_integral'] == 2){
            $this->IS_UPDATE_USER = 2;
            $points += $rrInfo['give_integral'];
            $ilModel = new IntegralLog();
            $addIntegralData = [
                'intregral_no'=>$intregral_no,
                'store_id'=>1,
                'uid'=>$postData['uid'],
                'store_name'=>1,
                'user_name'=>1,
                'user_phone'=>1,
                'change_integral'=>$rrInfo['give_integral'],
                'source'=>2,
            ];
            $ilRet = $ilModel->addIntegralLogData($addIntegralData);
            if(!$ilRet){
                $rlModel->rollback();
                failMsg('更新积分失败');
            }
        }
        $saveUserData = [
            'countmoney' =>$countmoney,
            'points' =>$points,
        ];
        //判断是否更新用户信息
        if($this->IS_UPDATE_USER == 2){
            $userRet = $userModel->editUserData($postData['uid'],$saveUserData);
            if(!$userRet){
                failMsg('更新用户信息失败');
            }
        }
        //是否赠送优惠券1：否2：是
        if($rrInfo['is_give_integral'] == 2 && $rrInfo['queues']){
            $couponModel = new \app\common\model\Coupon();
            $couWhere = [];
            $queues = explode(',',$rrInfo['queues']);
            $couWhere['id'] = ['in',$queues];
            $couponData = $couponModel->getCouponList($couWhere);
            if($couponData){
                $clModel = new CouponLog();
                foreach ($couponData['data'] as $cVal){
                    //判断优惠券时间类型1：天数2：日期
                    if($cVal['type_time'] == 1){
                        $coupon_start_time = strtotime( $cVal['create_time'])+$cVal['effective_date']*86400;
                        $coupon_end_time = $coupon_start_time+$cVal['effective_date']*86400;
                    }else{
                        $coupon_start_time = $cVal['start_time'];
                        $coupon_end_time = $cVal['end_time'];
                    }
                    $addCouponInfo = [
                        'user_id'=>$postData['uid'],
                        'user_name'=>$userInfo['name'],
                        'user_phone'=>$userInfo['phone'],
                        'coupon_id'=>$cVal['id'],
                        'coupon_no'=>$cVal['queue_sn'],
                        'coupon_name'=>$cVal['title'],
                        'coupon_min_price'=>$cVal['min_pay_price'],
                        'discount_price'=>$cVal['discount_price'],
                        'coupon_start_time'=>$coupon_start_time,
                        'coupon_end_time'=>$coupon_end_time,
                        'status'=>1,
                        'create_time'=>time(),
                        'update_time'=>time(),
                    ];
                    $addCouponInfo = $clModel->isVerifyMustField($addCouponInfo);
                    $addCouponInfo = $clModel->isVerifyNoMustField($addCouponInfo);
                    $addCouponData[] = $addCouponInfo;
                }
                $clRet = $clModel->sendCouponLogDatas($addCouponData);
                if(!$clRet){
                    $rlModel->rollback();
                    failMsg('发送优惠券失败');
                }
            }
        }
        $postData['recharge_first'] = $userInfo['balance'];
        $postData['recharge_monery'] = $rrInfo['recharge_monery'];
        $postData['recharge_end'] = $balance+$rrInfo['recharge_monery'];
        $postData['recharge_no'] = getSerial();
        $postData['user_phone'] = $postData['phone']??'';
        unset($postData['phone']);
        $postData = $rlModel->isVerifyMustField($postData);
        $postData = $rlModel->isVerifyNoMustField($postData);
        $ret = $rlModel->addRechargeLogData($postData);
        if(!$ret){
            $rlModel->rollback();
            failMsg('充值失败');
        }
        $rlModel->commit();
        successMsg('充值成功');
        if(!$getData){
            addNoticeErrorLog('支付异步通知',$getData);
        }
    }

    /**
     * Notes: 生成微信sign
     * Class: wxGetSign
     * user: bingwoo
     * date: 2021/1/9 15:15
     */
    public function wxGetSign($data,$secret) {
        header('Content-type: text/html; charset=utf-8');
        // 对数组的值按key排序
        ksort($data);
        // 生成url的形式
        $param = http_build_query($data);
        $str = urldecode($param); //解码
        $params = $str.$secret;
        // 生成sign
        $sign = md5($params);
        return $sign;
    }



}