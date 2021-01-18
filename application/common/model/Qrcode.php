<?php
namespace app\common\model;
use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\api\controller
 * Class: Qrcode
 * user: bingwoo
 * date: 2021/1/12 16:09
 */
class Qrcode extends Model{

    /**
     * Notes: 生成二维码
     * Class: createQrcode
     * user: bingwoo
     * date: 2021/1/12 16:21
     */
    public function createQrcode($name){

        if(!$name){
            failMsg('公众号二维码名称不能为空');
        }
        $systemModel = new \app\common\model\System();
        $token = $this->getAccessToken();
        $qrRet = $this->getQrcodeInfo($token,$name);
        $saveData = [
            'mini_qrcode'=>$qrRet
        ];
        $where = [];
        $where['id'] = ['=',1];
        $sysRet = $systemModel->editSystemData($saveData,$where);
        if(!$sysRet){
            failMsg('公众号二维码生成失败');
        }
        return true;
    }

    /**
     * Notes: 获取token
     * Class: getAccessToken
     * user: bingwoo
     * date: 2021/1/12 16:11
     */
    public function getAccessToken(){

        //获取基础配置信息
        $systemModel = new \app\common\model\System();
        $systemInfo = $systemModel->getSystemInfo([],'mini_appid,mini_appsecret,mini_qrcode');
        if(!$systemInfo){
            failMsg('基础信息不存在');
        }
        if($systemInfo['mini_qrcode']){
            failMsg('公众号二维码已存在，请勿重复生成');
        }
        $appid = $systemInfo['mini_appid'];
        $secret = $systemInfo['mini_appsecret'];
        $type = 'client_credential';
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type='.$type.'&appid='.$appid.'&secret='.$secret;
        $ret = request_get($url);
        $ret = json_decode($ret);
        if(isset($ret->access_token)){
            return $ret->access_token;
        }
        failMsg('token获取失败');
    }

    /**
     * Notes: 获取门店桌台二维码
     * Class: getQrcodeInfo
     * user: bingwoo
     * date: 2020/8/25 13:19
     */
    public function getQrcodeInfo($token,$name){

        $tiUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$token;
        $scene_str = 'page/index/index';
        $scene = [
            'scene_str'=>$scene_str
        ];
        $action_info = [
            'scene'=>$scene
        ];
        $data = [
            'expire_seconds' => 86400,
            'action_name' => 'QR_STR_SCENE',//目前为临时二维码
            //                'action_name' => 'QR_LIMIT_STR_SCENE',//永久二维码
            'action_info' => $action_info
        ];
        $ret = request_post($tiUrl,json_encode($data));
        $ret = @json_decode($ret,true);
        if(!isset($ret['ticket'])){
            return '';
        }
        //通过ticket换取二维码
        $ticket = $ret['ticket'];
        $qrcodeUrl = $ret['url'];
        $qrUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket.'&url='.$qrcodeUrl;
        $reqrUrl = $this->updateImg($qrUrl,$name);
        return $reqrUrl;
    }

    /**
     * Notes: 上传图片
     * Class: updateImg
     * user: bingwoo
     * date: 2020/8/25 15:48
     */
    private function updateImg($url,$name){

        //上传图片
        ob_start();
        //读取文件
        readfile($url);
        //拿到图片  保存到变量操作
        $img  = ob_get_contents();
        //关闭缓冲
        ob_end_clean();
        $folder = ROOT_PATH . 'public' . DS . 'qrcode'.'/'.date("Y-m-d");
        is_dir($folder) OR mkdir($folder, 0777, true);
        $localImage = $folder.'/'.$name.'.png'; //存到本地的图片地址名字:门店名称+桌台号
        $fp = fopen($localImage, 'a');// fopen() 函数打开文件或者 URL
        $ret = fwrite($fp, $img);//内容写入文件
        fclose($fp);//关闭文件
        if($ret){
            return 'qrcode/'.'date("Y-m-d").$name.'.'png';
        }
        die;
    }

}