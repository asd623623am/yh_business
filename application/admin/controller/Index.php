<?php
namespace  app\admin\controller;
use think\Controller;
use Msg\Msg;
class Index extends Common{
    public function index(){
       return view();
    }

    /**
     * 发送手机短信.
     * @return [type] [description]
     */
    public function indexSend()
    {
        $data = model('Orders')->where(['pay_status'=>0])->select()->toArray();
        if (empty($data)) {
            win('发送成功');
        }
        $home_ids = [];
        foreach ($data as $key => $value) {
            $home_ids[] = $value['home_id'];
        }
        $home_id=array_unique($home_ids);
        $where = [
            'house_id' => array('in',$home_id),
            'status'    => 1
        ];
        $homeData = model('Userhouse')->where($where)->select()->toArray();
        if (empty($homeData)) {
            win('发送成功');
        }
        $user_id = [];
        foreach ($homeData as $k => $v) {
            $user_id[] = $v['user_id'];
        }
        $newuserId=array_values(array_unique($user_id));

        $tel = [];
        $userWhere = [
            'user_id'   => array('in',$newuserId)
        ];
        $userdata = model('User')->where($userWhere)->select()->toArray();
        if (empty($userdata)) {
            fail('发送失败');
        }
        foreach ($userdata as $keys => $vals) {
            $tel[] = $vals['tel'];
        }
        $tels = implode(',', $tel);
         $user = 'AA00639';
         $pws = 'AA0063920';
         // $mobiles="15955553369,15100330586,18701512393";//目标手机号码，多个用半角“,”分隔
    	 $mobiles=$tels;//目标手机号码，多个用半角“,”分隔
    	 $extno = "";
    	 $content="亲爱的业主您好！您还有未交清的物业账单，为保证您的正常物业服务，请您尽快缴费。缴费方式：1进入xxx小程序线上缴费；2前往xxx物业缴费【小码旺铺】";
    	 $sendtime = date('Y-m-d H:i:s',time());

    	 $result=$this->send($mobiles,$extno,$content,$sendtime);
    	 
    	 $xml = simplexml_load_string($result);
    	 if ($xml->returnstatus == 'Success') {
             $this->sendmsg();
    	 	// echo "返回信息提示:：".$xml->message."<br>";
    	 	// echo "返回状态为:".$xml->returnstatus."<br>";
    	 	// echo "返回信息:".$xml->message."<br>";
    	 	// echo "返回余额:".$xml->remainpoint."<br>";
    	 	// echo "返回本次任务ID:".$xml->taskID."<br>";
    	 	// echo "返回成功短信数:".$xml->successCounts."<br>";
    	 	 win('发送成功');
    	 } else {
    	 	fail('发送失败');
    	 }
    	 // echo "返回信息提示:：".$xml->message."<br>";
    	 // echo "返回状态为:".$xml->returnstatus."<br>";
    	 // echo "返回信息:".$xml->message."<br>";
    	 // echo "返回余额:".$xml->remainpoint."<br>";
    	 // echo "返回本次任务ID:".$xml->taskID."<br>";
    	 // echo "返回成功短信数:".$xml->successCounts."<br>";
    	 // exit;
    	// $this->sendPhone();
    }

        
        public function send($mobiles,$extno,$content,$sendtime)
        {
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
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }


        /**
     * 发送自定义的模板消息
     * @param $touser
     * @param $template_id
     * @param $url
     * @param $data
     * @param string $topcolor
     * @return bool
     */
    public function doSend($datas,$accessToken)
    {
        $data = [
                'userName'      => [
                        'value'     => $datas['user_name'],
                        'color'     => '#173177'
                ],
                'address'      => [
                        'value'     => $datas['home_name'],
                        'color'     => '#173177'
                ],
                'pay'      => [
                        'value'     => '人民币'.$datas['fee'].'元',
                        'color'     => '#173177'
                ],
                'remark'      => [
                        'value'     => '请您尽快缴费',
                        'color'     => '#173177'
                ],
                'first'      => [
                        'value'     => '北京延龙物业',
                        'color'     => '#173177'
                ],
        ];
         $template = [
            "touser" => $datas['openid'],
            "template_id" => "cV3j5qeVWEjJtYwmHqbOt8rcBeJZBw60ri-eAYZWaLI",
            "topcolor" => "#FF0000",
            "data"      => $data
        ];

        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $accessToken;
        return $this->request_post($url, urldecode($json_template));


    }



        //调用发送方法
        public function sendmsg()
        {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=&secret=';

        $appid = 'wxbe2a4ce2a8613368';
        $secret = '5c5e96da676da6f8dc42f66641516ede';
        $token = $this->getToken($appid,$secret);
        // $token = '35_UFXzJLW3BOsascDgnvUxGDzuuTy8Bh2OBB1K8bl3CVSkKomTNHhOb9EQTe-WZyAALgdEl7NZr57dZJSM-JSUYYFdu5Y37mZjEc4xITbtaaZQnJg-30Me2CHy2n0DbgJYsRpNuvMYXvQ-zUZ0WVThAEABAY';
        
        $urls = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$token.'&next_openid=';
        $user = $this->request_get($urls);
        $user = json_decode(stripslashes($user));
        $arr = json_decode(json_encode($user), true);
        foreach ($arr['data']['openid'] as $key => $value) {
            $unidurl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$value.'&lang=zh-CN';
            $uId = $this->request_get($unidurl);
            $uIdInfo[] = json_decode($uId,true);
        }
        #获取 为缴费人的标识.
        $where = [
            'pay_status'    => 0,
            'type'          => array('in',[0,1]),
            'status'        => 1
        ];
        $order = model('Orders')->where($where)->select()->toArray();

        $home_id = 'home_id';
        $orders=$this->assoc_unique($order, $home_id);

        $home_ids = [];
        foreach ($orders as $key => $value) {
            $home_ids[] = $value['home_id'];
        }


        $home_where = [
            'home_id'   => array('in',$home_ids),
            'is_delete'    => 1
        ];

        $home = model('Home')->where($home_where)->select()->toArray();

        $temp = [];
        foreach ($home as $key => $value) {
            $owhere = [
                'user_name' => $value['owner'],
                'district_id'   => $value['district_id'],
                'complex'   => $value['complex'],
                'home_code' => $value['home_code'],
                'home_id'   => $value['home_id'],
                'pay_status'    => 0,
                'status'    => 1
            ];
            $odata = model('Orders')->where($owhere)->order('id desc')->find();

            $uwhere = [
                'house_id'  => $odata['home_id'],
                'status'    => 1
            ];
            $udata = model('Userhouse')->where($uwhere)->find();

            $user = model('User')->where(['user_id'=>$udata['user_id']])->find();


            $temp[] = [
                'city'    => $user['city'],
                'province'    => $user['province'],
                'country'    => $user['country'],
                'nickname'      => $user['nickname'],
                'fee'   => sprintf("%1\$.2f", $odata['fee']*$value['area']-$odata['compensation']),
                'home_id'   => $odata['home_id'],
                'home_name' => $odata['district_name'].$odata['complex'].$odata['home_code'],
                'user_name' => $odata['user_name'],
            ];
        }
        $temps = [];
        foreach ($temp as $k => $v) {
            foreach ($uIdInfo as $kk => $vv) {
                if ($v['city'] == null) {
                    $v['city'] = '';
                }
                if ($vv['city'] == null) {
                    $vv['city'] = '';
                }
                if ($v['nickname'] == null) {
                    $v['nickname'] = '';
                }
                if ($vv['nickname'] == null) {
                    $vv['nickname'] = '';
                }
                if ($v['province'] == null) {
                    $v['province'] = '';
                }
                if ($vv['province'] == null) {
                    $vv['province'] = '';
                }
                if ($v['country'] == null) {
                    $v['country'] = '';
                }
                if ($vv['country'] == null) {
                    $vv['country'] = '';
                }
                if ($v['city'] == $vv['city'] && $v['nickname'] == $vv['nickname'] && $v['province'] == $vv['province'] && $v['country'] == $vv['country']) {
                    $temps[] = [
                        'openid'    => $vv['openid'],
                        'user_name' => $v['user_name'],
                        'home_name' => $v['home_name'],
                        'fee'       => $v['fee'],
                    ];
                }
            }

        }

        for ($i=0; $i < count($temps) ; $i++) { 
            $res = $this->doSend($temps[$i],$token);
        }
         return true;
        }
        

        /**
         * 二维数组去重
         * @param  [type] $array2D [description]
         * @return [type]          [description]
         */
        public function assoc_unique($arr, $key) {

        $tmp_arr = array();

        foreach ($arr as $k => $v) {

        if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true

        unset($arr[$k]);

        } else {

        $tmp_arr[] = $v[$key];

        }

        }

        sort($arr); //sort函数对数组进行排序

        return $arr;

        }

    /**
     * @param $appid
     * @param $appsecret
     * @return mixed
     * 获取token
     */
    protected function getToken($appid, $appsecret)
    {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
            $token = $this->request_get($url);
            $token = json_decode(stripslashes($token));
            $arr = json_decode(json_encode($token), true);
            $access_token = $arr['access_token'];


        return $access_token;
    }

        /**
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    function request_post($url = '', $param = '')
    {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        return $data;
    }
    /**
     * 发送get请求
     * @param string $url
     * @return bool|mixed
     */
    function request_get($url = '')
    {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
 




    public function indexData()
    {

        $home_num = model('Home')->where(['is_delete'   => 1])->count();
        // 
        $where = [
            'status'    =>  1,
            'type'      => ['in',[0,1]],
            'pay_status'    => ['in',[0,1]],
        ];
        $count = model('Orders')->where($where)->count();
        // $order_no = model('Orders')->where($where)->whereTime('end_at','>=',date("Y-m-d",time()))->count();
        $order_no = model('Orders')->where($where)->count();
        $wheres = [
            'pay_status'=> 1,
            'status'    =>  1,
            'type'      => ['in',[0,1]],
        ];
        $pay_count = model('Orders')->where($wheres)->count();
        $data =[
            'home_num'  => $home_num, //房屋数量
            'order_no'  => $order_no, //订单数量
            'pay_count' => $pay_count, //已缴费
            'count' => $count, //总数
        ];
         $info=['code'=>0,'msg'=>'','data'=>$data];
        return json($info);
        $wwhere = [
            'pay_status'    => array('in',[1,2]),
            'type'          => 0,
            'status'        => 1
        ];
        //物业未缴费.
        $wdata = model('Orders')->where($wwhere)->count();
        $wwheres = [
            'type'  => 0,
            'status'    => 1,
        ];
        $wcount = model('Orders')->where($wwheres)->count();


        $gwhere = [
            'pay_status'    => array('in',[1,2]),
            'type'          => 1,
            'status'        => 1,
        ];
        $gdata = model('Orders')->where($gwhere)->count();
        $gwheres = [
            'type'  => 1,
            'status'    => 1,
        ];
        $gcount = model('Orders')->where($gwheres)->count();
        // $temp = [];
        // $wcount = []; //物业暖数
        // $gcount = []; //供暖
        // $pay_wcount = []; //物业支付人数
        // $pay_gcount = []; //供暖支付人数

        // foreach ($data as $key => $value) {
        //     if ($value['type'] == 0) {
        //         $wcount[] = $value;
        //     }

        //     if ($value['type'] == 0 && $value['pay_status'] == 1) {
        //         $pay_wcount[] = $value;
        //     }
        //     if ($value['type'] == 1) {
        //         $gcount[] = $value;
        //     }
        //     if ($value['type'] == 1 && $value['pay_status'] == 1) {
        //         $pay_gcount[] = $value;
        //     }
        // }
        $wtemp = [];
        if ($wcount == 0) {
            $wtemp = [
                'percentage'=>"0％",
                'title' => '物业缴费完成率',
                'num'   => $wdata,
                'count' => $wcount,
            ];
        } else {
            $wtemp = [
                'percentage'=>round($wdata/$wcount*100)."％",
                'title' => '物业缴费完成率',
                'num'   => $wdata,
                'count' => $wcount,
            ];
        }
        $gtemp = [];
        if ($gcount == 0) {
            $gtemp = [
                'percentage'=>"0％",
                'title' => '供暖缴费完成率',
                'num'   => $gdata,
                'count' => $gcount,
            ];
        } else {
            $gtemp = [
                'percentage'=>round($gdata/$gcount *100)."％",
                'title' => '供暖缴费完成率',
                'num'   => $gdata,
                'count' => $gcount,
            ];
        }
        $temp = [
            'w' => $wtemp,
            'g' => $gtemp,
        ];
        $info=['code'=>0,'msg'=>'','data'=>$temp];
        echo json_encode($info);
        exit;
    }

    public function payMoney()
    {
        $data = input('data');
        if (empty($data)) {
            fail('非法操作');
        }


        $newwhere = [
            'status'=>1,
            'type'  => array('in',[0,1]),
            'pay_status'    =>1

        ];
        $info = model('Orders')->where($newwhere)->whereTime('finish_at', $data)->select()->toArray();


        $newtemp = [];
        $categories = [];
        $datas = [];

        if ($data == 'w') {
            foreach ($info as $k => $v) {
                $info[$k]['finish_at'] = date('Y-m-d',$v['finish_at']);
                $temp[date('Y-m-d',$v['finish_at'])][] = $v['pay_fee'];
            }



            $res = $this->get_week(time());
            foreach ($res as $key => $val) {

                $time = strtotime($val);
                $weekarray=array("日","一","二","三","四","五","六");
                $w = $weekarray[date("w",$time)];

                $categories[] = $val.'星期'.$w;


                if (!empty($temp[$val])) {
                    $datas[] = array_sum($temp[$val]);
                } else {
                    $datas[] =0;
                }

                $newtemp = [
                    'a' =>$categories,
                    'b' =>$datas,
                    'max' => count($datas)-1,
                ];
            }
        } else if ($data == 'm') {
            $res = $this->getMonthDays();

            foreach ($info as $k => $v) {
                $info[$k]['finish_at'] = date('Y-m-d',$v['finish_at']);
                $temp[date('d',$v['finish_at'])][] = $v['pay_fee'];
            }

            $categories = [];
            $datas = [];
            foreach ($res as $key => $val) {
                $categories[] = $val.'日';
                if (!empty($temp[$val])) {
                    $datas[] = array_sum($temp[$val]);
                } else {
                    $datas[] = 0;
                }
            }

            $newtemp = [
                'a' =>$categories,
                'b' =>$datas,
                'max' => count($datas)-1,
            ];  
        } else if ($data == 'y') {
             $res = $this->getYear();
            foreach ($info as $k => $v) {
                $info[$k]['finish_at'] = date('Y-m-d',$v['finish_at']);
                 $temp[date('m',$v['finish_at'])][] = $v['pay_fee'];
            }
            $categories = [];
            $datas = [];
            foreach ($res as $key => $val) {
                $categories[] = $val.'月';
                if (!empty($temp[$val])) {
                    $datas[] = array_sum($temp[$val]);
                } else {
                    $datas[] = 0;
                }
            }
            $newtemp = [
                'a' =>$categories,
                'b' =>$datas,
                'max' => count($datas)-1,
            ];
        }
        
       


         $info=['code'=>0,'msg'=>'','data'=>$newtemp];
        return json($info);
    }

    public function payNumber()
    {
        $data = input('data');
        if (empty($data)) {
            fail('非法操作');
        }

        $newwhere = [
            'status'=>1,
            'type'  => array('in',[0,1]),
            'pay_status'    =>1

        ];

        $info = model('Orders')->where($newwhere)->whereTime('finish_at', $data)->select()->toArray();
        $temp = [];
        if ($data == 'm') {

            $res = $this->getMonthDays();

            foreach ($info as $k => $v) {
                $info[$k]['finish_at'] = date('Y-m-d',$v['finish_at']);

                $temp[date('d',$v['finish_at'])][] = 1;
            }
            $categories = [];
            $datas = [];
            foreach ($res as $key => $val) {
                $categories[] = $val.'日';
                if (!empty($temp[$val])) {
                    $datas[] = count($temp[$val]);
                } else {
                    $datas[] = 0;
                }
            }

            $temp = [
                'a' =>$categories,
                'b' =>$datas,
                'max' => count($datas)-1,
            ];  
        } else if ($data == 'y') {

            $res = $this->getYear();
            foreach ($info as $k => $v) {
                $info[$k]['finish_at'] = date('Y-m-d',$v['finish_at']);

                $temp[date('m',$v['finish_at'])][] = 1;
            }
            $categories = [];
            $datas = [];
            foreach ($res as $key => $val) {
                $categories[] = $val.'月';
                if (!empty($temp[$val])) {
                    $datas[] = count($temp[$val]);
                } else {
                    $datas[] = 0;
                }
            }
            $temp = [
                'a' =>$categories,
                'b' =>$datas,
                'max' => count($datas)-1,
            ];
        } else if ($data == 'w') {
            $times = $this->get_week(time());
            

            foreach ($info as $k => $v) {
                $info[$k]['finish_at'] = date('Y-m-d',$v['finish_at']);

                $temp[date('Y-m-d',$v['finish_at'])][] = 1;
            }
            $categories = [];
            $datas = [];

            foreach ($times as $key => $val) {


                $time = strtotime($val);
                $weekarray=array("日","一","二","三","四","五","六");
                $w = $weekarray[date("w",$time)];

                $categories[] = $val.'星期'.$w;

                if (!empty($temp[$val])) {
                    $datas[] = count($temp[$val]); 
                } else {
                    $datas[] = 0;
                }
            }
            $temp = [
                'a' =>$categories,
                'b' =>$datas,
                'max' => count($datas)-1,
            ];
        }
        
        $info=['code'=>0,'msg'=>'','data'=>$temp];
        return json($info);
    }


    /**

    * 获取本周所有日期

    */

    function get_week($time = '', $format='Y-m-d'){

    $time = $time != '' ? $time : time();

    //获取当前周几

    $week = date('w', $time);

    $date = [];

    for ($i=1; $i<=7; $i++){        
    // $date[$i] = date($format ,strtotime( '+' . $i-$week .' days', $time));
    $date[$i] = date($format ,strtotime($i-$week .' days',$time));

    }

    return array_values($date);

    }

    /**
 * 获取当前月的所有日期
 * @return array
 */
private function getMonthDays()
{
    $monthDays = [];
    $firstDay = date('Y-m-01', time());
    $i = 0;
    $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
    while (date('Y-m-d', strtotime("$firstDay +$i days")) <= $lastDay) {
    $monthDays[] = date('d', strtotime("$firstDay +$i days"));
    $i++;
    }
    return $monthDays;
}
    /**
     * 获取年
     * @return [type] [description]
     */
    private function getYear()
    {
        return ['01','02','03','04','05','06','07','08','09','10','11','12'];
    }



        /**
     * 计算金额.
     * @return [type] [description]
     */
    public function sprintfs($data)
    {
        return sprintf("%1\$.2f", $data);
    }

}