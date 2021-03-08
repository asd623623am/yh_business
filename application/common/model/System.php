<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: System
 * user: bingwoo
 * date: 2021/1/9 10:49
 */
class System extends Model{

    protected $table = 'system';
    protected $isMustField = [];
    protected $isNoMustField = ['company_logo'=>'公司logo','company_name'=>'公司名称','system_data'=>'版权信息',
                                'system_name'=>'系统名称','uname'=>'联系人','utel'=>'联系电话','address'=>'地址',
                                'mini_appid'=>'小程序appid','mini_appsecret'=>'小程序密钥','mini_img'=>'推送小程序的缩略图',
                                'paykey'=>'支付密钥','gz_appid'=>'公众号appid','gz_appsecret'=>'公众号密钥',
                                'gz_token'=>'公众号token','gz_bg_img'=>'公众号消息背景图片','gz_bg_url'=>'公众号背景图片url',
                                'service_phone'=>'客服电话','service_email'=>'客服邮箱','termNo'=>'设备号','merId'=>'商户ID',
                                'status'=>'状态','paykey'=>'支付密钥','airscan_secret_key'=>'点餐密钥',
                                'member_secret_key'=>'会员密钥'];

    /**
     * Notes: 验证必传字段
     * Class: isVerifyMust
     * user: bingwoo
     * date: 2020/11/20 17:20
     */
    public function isVerifyMustField($postData){
        foreach ($this->isMustField as $k => $v){
            if(!isset($postData[$k])){
                failMsg($v.'不能为空');
            }
        }
        return $postData;
    }

    /**
     * Notes: 验证非必传字段
     * Class: isVerifyNoMustField
     * user: bingwoo
     * date: 2020/11/20 17:22
     */
    public function isVerifyNoMustField($postData){
        foreach ($this->isNoMustField as $k => $v){
            //验证邮箱
            if($k == 'service_email'){
                verifEmail($postData[$k]);
            }
            if(empty($postData[$k])){
                unset($postData[$k]);
            }
            if(isset($postData[$k])){
                if($postData[$k] == 'on'){
                    $postData[$k] = 2;
                }
            }
        }
        return $postData;
    }

    /**
     * Notes: 获取系统详情
     * Class: getSystemInfo
     * user: bingwoo
     * date: 2020/11/25 14:41
     */
    public function getSystemInfo($where = [],$field = '*',$Order = ''){
        $qurey = $this;
        if(!empty($where)){
            $qurey = $this->where($where);
        }
        if(!empty($where)){
            if(!empty($Order)){
                $qurey = $qurey->order($Order);
            }
        }else{
            if(!empty($Order)){
                $qurey = $this->order($Order);
            }
        }
        $data = $qurey->field($field)->find();
        if(empty($data)){
            return false;
        }
        $data = $data->toArray();
        $data = $this->changeName($data);
        return $data;
    }

    /**
     * Notes: 修改系统信息
     * Class: editSystemData
     * user: bingwoo
     * date: 2020/11/25 14:42
     */
    public function editSystemData($saveData,$where = []){

        $ret = false;
        if($where){
            $ret = $this->where($where)->update($saveData);
        }
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 转换名称
     * Class: changeName
     * user: bingwoo
     * date: 2020/11/23 11:19
     */
    public function changeName(array $data){

        //公司logo
        if(isset($data['company_logo'])){
            $data['company_logo'] = 'uploads/'.$data['company_logo'];
        }
        //小程序二维码
        if(isset($data['mini_qrcode'])){
            $data['mini_qrcode'] = 'uploads/'.$data['mini_qrcode'];
        }
        return $data;
    }

    /**
     * Notes: 检擦信息是否存在
     * Class: checkInfo
     * user: bingwoo
     * date: 2021/3/6 15:23
     */
    public function checkInfo($postdata){

        if(!$postdata['airscan_secret_key'] || !$postdata['member_secret_key']){
            return false;
        }
        $where = [];
        $where['airscan_secret_key'] = ['=',$postdata['airscan_secret_key']];
        $where['member_secret_key'] = ['=',$postdata['member_secret_key']];
        $sysInfo = $this->where($where)->field('id')->find()->toArray();
        if(!$sysInfo){
            return false;
        }
        return true;
    }

}