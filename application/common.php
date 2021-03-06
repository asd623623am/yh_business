<?php

use think\Request;
use third\aliyun\SignatureHelper;
use third\email\PHPMailer;
//生成盐值
function createSalt(){
    $str='0123456789asdhfgk=+-*//';
    return substr(str_shuffle($str),rand(1,25),6);
}

//生成密码
function createPwd($pwd,$salt){
    return md5(md5($pwd).md5($salt).'shop');
}

//获取分类信息
function getInfo($cateInfo,$pid=0,$level=0){
    static $info=[];
    foreach($cateInfo as $k=>$v){
        if($v['pid']==$pid){
            $v['level']=$level;
            $info[]=$v;
            getInfo($cateInfo,$v['cate_id'],$v['level']+1);
        }
    }
    return $info;

}

//检验是否ajax和post上传
function check(){
    if(request()->isPost()&&request()->isAjax()){
        return true;
    }
}

//错误信息
function fail($font){
    echo json_encode(['font'=>$font,'code'=>2]);
    exit;
}

//正确
function win($font){
    echo json_encode(['font'=>$font,'code'=>1]);
    exit;
}

//发送手机短信
function sendSms($tel,$code) {

    $params = array ();

    // *** 需用户填写部分 ***
    // fixme 必填：是否启用https
    $security = false;

    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = "LTAItM9ZD9eatvee";
    $accessKeySecret = "h1vtfW6STfQ2DD9fZ0IamLYKXQ5cdu";

    // fixme 必填: 短信接收号码
    $params["PhoneNumbers"] = $tel;

    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = "layui后台管理";

    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    $params["TemplateCode"] = 'SMS_144942559';

    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "code" => $code,
        //"product" => "阿里通信"
    );

    // fixme 可选: 设置发送短信流水号
    $params['OutId'] = "12345";

    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
    $params['SmsUpExtendCode'] = "1234567";


    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }

    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
    $helper = new SignatureHelper();
    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        )),
        $security
    );

    return $content;
}

//发送邮箱
function sendEM($qq,$content){
    //实例化PHPMailer核心类
    $mail = new PHPMailer();

//是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
    $mail->SMTPDebug =0;

//使用smtp鉴权方式发送邮件
    $mail->isSMTP();

//smtp需要鉴权 这个必须是true
    $mail->SMTPAuth=true;

//链接qq域名邮箱的服务器地址
    $mail->Host = 'smtp.163.com';//163邮箱：smtp.163.com

//设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = 'ssl';//163邮箱就注释

//设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
    $mail->Port = 465;//163邮箱：25

//设置smtp的helo消息头 这个可有可无 内容任意
// $mail->Helo = 'Hello smtp.qq.com Server';

//设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
//$mail->Hostname = 'http://localhost/';

//设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
    $mail->CharSet = 'UTF-8';

//设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = 'Vince';

//smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username ='vincecaterD@163.com';

//smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
    $mail->Password = '960320ZB';//163邮箱也有授权码 进入163邮箱帐号获取

//设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From = 'vincecaterD@163.com';

//邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
    $mail->isHTML(true);

//设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    $mail->addAddress($qq);

//添加多个收件人 则多次调用方法即可
// $mail->addAddress('xxx@163.com','爱代码，爱生活世界');

//添加该邮件的主题
    $mail->Subject ='layui后台管理';

//添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = '验证码是'.$content.'5分钟内有效';

//为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
// $mail->addAttachment('./d.jpg','mm.jpg');
//同样该方法可以多次调用 上传多个附件
// $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');
//var_dump($mail);exit;
    $status = $mail->send();

//简单的判断与提示信息
    if($status) {
        return 'ok';
    }else{
        return 'no';
    }
}

//随机生成发送的验证码
function createCode(){
    $str="0123456789102133131231232143112";
    return substr(str_shuffle($str),rand(0,21),6);

}

//递归查询分类信息
function getIndexCateInfo($Info,$pid=0){
    $data=[];
    foreach($Info as $k=>$v){
        if($v['pid']==$pid){
            $son=getIndexCateInfo($Info,$v['cate_id']);
            $v['son']=$son;
            $data[]=$v;
        }
    }
    return $data;
}

//获取顶级分类中所有的子类id
function getAllCateId($cate_id,$cateInfo){
    static $c_id=[];
    foreach($cateInfo as $k=>$v){
        if($v['pid']==$cate_id){
            $c_id[]=$v['cate_id'];
            getAllCateId($v['cate_id'],$cateInfo);
        }
    }
    return $c_id;
}

/**
 * 格式化销售数量
 * @param $number
 */
function formatSale( $number ){
    if( $number > 9999 ){
        return '<strong>' . intval( $number / 10000 ) . '万+</strong>';
    }else{
        return $number;
    }
}

/**
 * 格式化价格
 */
function formatMoney( $money ){
    return number_format( $money , 2, '.' , ',' );
}

/**
 * 展示订单状态
 */
function showOrderStatus( $order_status ){

    #订单状态 1、未支付 2、已支付 3、取消  4、商家确认 5、已发货 6、已签收 7、已完成'

    switch( $order_status ){
        case 1:
            return '<span style="color:red">未付款</span>';
            break;
        case 2:
            return '已支付';
            break;
        case 3:
            return '已取消';
            break;
        case 4:
            return '商家确认';
            break;
        case 5:
            return '已发货';
            break;
        case 6:
            return '已签收';
            break;
        case 7:
            return '已完成';
            break;
    }

}

function showOrderOperate( $order_status ,$order_no){
    switch( $order_status ){
        case 1:
            return '<span style="color:red">
                <a  href="'.url('order/createSuccess',['order_no' => $order_no]).'">
                去付款</a></span>';
            break;
        case 2:
            return '<span>提醒商家确认</span>';
            break;
        case 3:
            return '<span>重新购买</span>';
            break;
        case 4:
            return '<span>提醒发货</span>';
            break;
        case 5:
            return '<span>查看物流信息</span>';
            break;
        case 6:
            return '<span>确认收货</span>';
            break;
        case 7:
            return '<span>去评价</span>';
            break;
    }
}

/**
 * Notes: post请求
 * Class: request_post
 * user: bingwoo
 * date: 2020/8/25 10:51
 */
function request_post($url = '', $param = ''){
    if (empty($url) || empty($param)) {
        return false;
    }
    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init(); //初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
    curl_setopt($ch, CURLOPT_POST, true); //post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    curl_setopt($ch, CURLOPT_HEADER, false);//设置header
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch); //运行curl
    curl_close($ch);
    return $data;
}

/**
 * Notes: 发送get请求
 * Class: request_get
 * user: bingwoo
 * date: 2020/8/25 10:52
 */
function request_get($url = ''){
    if (empty($url)) {
        return false;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
 * Notes: 获取当前登录用户的门店的id
 * Class: getStoreid
 * user: bingwoo
 * date: 2020/8/26 17:15
 */
function getStoreid(){

    $loginInfo = session('admin');
    return $loginInfo['storeid'];
}

/**
 * Notes: 错误信息
 * Class: failMsg
 * user: bingwoo
 * date: 2020/12/21 15:34
 */
function failMsg($font){
    echo json_encode(['code'=>2,'font'=>$font]);
    exit;
}

/**
 * Notes: 正确
 * Class: successMsg
 * user: bingwoo
 * date: 2020/12/21 15:33
 */
function successMsg($font,$data = []){
    echo json_encode(['code'=>1,'font'=>$font,'data'=>$data]);
    exit;
}
/**
 * Notes: 根据登录账号名称获取账号所属门店id
 * Class: getStoreidByName
 * user: bingwoo
 * date: 2020/10/20 13:48
 */
function getStoreidByKey($key){
    $redisdb  = new \redis();
    $redisdb->connect('127.0.0.1','6379');
    $loginInfo = $redisdb->get($key);
    if(empty($loginInfo)){
        echo json_encode(['status'=>-1,'msg'=>'请登录']);
        exit;
    }
    return json_decode($loginInfo,true);
}

/**
 * Notes: 验证字段
 * Class: verifColumn
 * user: bingwoo
 * date: 2020/10/20 13:57
 */
function verifColumn($vericolumn,$postData){

    foreach ($vericolumn as $v){
        if(!isset($postData[$v])){
            echo json_encode(['code'=>-1,'msg'=>'字段['.$v.']必传']);
            exit;
        }
    }
}

/**
 * Notes: 添加日志
 * Class: addLog
 * user: bingwoo
 * date: 2020/12/14 14:23
 */
function addLog($str,$accesstoken = ''){

    $redisdb  = new \redis();
    $admin_name = '未知';
    if($accesstoken){
        $redisdb->connect('127.0.0.1','6379');
        $data = $redisdb->get($accesstoken);
        if($data){
            $user = json_decode($data,true);
            $admin_name = $user['admin_name'];
        }
    }
    $insert = [
        'name'  => $admin_name,
        'content'   => $str,
        'source'   => '手机端退款',
        'ctime'     => time()
    ];
    model('Log')->allowField(true)->save($insert);
}

/**
 * 发送post formdata请求(yinhepay)
 */
function send_post_yinhepay($url,array $data)
{
    $data = @json_encode($data);
    $headers = [
        'Content-Type: application/json;charset=utf-8',
        'Content-Length: ' . strlen($data)
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_TIMEOUT, 8);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $output = curl_exec($curl);
    curl_close($curl);
    return @json_decode($output, true);
}

function key_verify($postData){
    $system = new \app\common\model\System();
    if(!$postData['airscan_secret_key'] || !$postData['member_secret_key']){
        addFileLog('getStoreList','error',$postData);
        return false;
    }
    $where = [];
    $where['airscan_secret_key'] = ['=',$postData['airscan_secret_key']];
    $where['member_secret_key'] = ['=',$postData['member_secret_key']];
    $sysInfo = $system->where($where)->field('system_id')->find();
    if(!$sysInfo){
        addFileLog('getStoreList','error2',$sysInfo);
        return false;
    }
    return true;
}

/**
 * Notes: 打印log日志
 * Class: addFileLog
 * param: 文件名 类型 日志内容
 * user: bingwoo
 * date: 2021/2/25 9:56
 */
function addFileLog($basename = null,$type = null,$msg = null){
    if(is_array($msg)){
        $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
    }
    $msg = '['.date("Y-m-d H:i:s").']'.'文件名：'.$basename.'，类型：'.$type.'，'.'[info]：'.$msg;
    $rootpath = ROOT_PATH.DS.'public'. DS .'logs'. DS;
    makedir($rootpath);
    // 日志文件名：日期.txt
    $path = $rootpath.$basename.'.log';

    file_put_contents($path, $msg.PHP_EOL,FILE_APPEND);
}

/**
 * Notes: 创建文件
 * Class: makedir
 * param:  $path [目录路径]
 * user: bingwoo
 * date: 2021/2/25 9:58
 */
function makedir($path){
    $arr=array();
    while(!is_dir($path)){
        array_push($arr,$path);//把路径中的各级父目录压入到数组中去，直接有父目录存在为止（即上面一行is_dir判断出来有目录，条件为假退出while循环）
        $path=dirname($path);//父目录
    }
    if(empty($arr)){//arr为空证明上面的while循环没有执行，即目录已经存在
        // echo $path,'已经存在';
        return true;
    }
    while(count($arr)){
        $parentdir=array_pop($arr);//弹出最后一个数组单元
        mkdir($parentdir);//从父目录往下创建
    }
}