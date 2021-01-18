<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: Store
 * user: bingwoo
 * date: 2020/11/25 15:08
 */
class Store extends Model{

    protected $table = 'xm_store';
    protected $isMustField = ['name'=>'门店名称','store_no'=>'门店编号','address'=>'门店地址','user_name'=>'联系人',
                              'user_tel'=>'联系电话','start_business_hours'=>'开始营业时间','end_business_hours'=>'结束营业时间',
                              'account'=>'门店账号', 'password'=>'门店密码'];
    protected $isNoMustField = ['member_secret_key'=>'会员密钥','airscan_secret_key'=>'点餐密钥','logo'=>'门店logo',
                                'pos_pay'=>'pos支付','face_pay'=>'人脸支付', 'termNo'=>'设备号','key'=>'密钥','status'=>'营业状态'];

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
     * Notes: 获取门店列表
     * Class: getStoreList
     * user: bingwoo
     * date: 2020/11/25 14:41
     */
    public function getStoreList($where = [],$field = '*',$page = 0,$limit = 20){

        $query = $this->field($field);
        if($where){
            $query = $query->where($where);
        }
        if($page){
            $query = $query->page($page,$limit);
        }
        $data = $query->select()->toArray();

        $count = $query->count();
        return ['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];

    }

    /**
     * Notes: 获取门店详情
     * Class: getStoreInfo
     * user: bingwoo
     * date: 2020/11/25 14:41
     */
    public function getStoreInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 添加门店信息
     * Class: addStoreData
     * user: bingwoo
     * date: 2020/11/25 14:42
     */
    public function addStoreData($addData,$isGetID = false){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        if($isGetID){
            $ret = $this->insertGetId($addData);
        }else{
            $ret = $this->insert($addData);
        }
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改门店信息
     * Class: editStoreData
     * user: bingwoo
     * date: 2020/11/25 14:42
     */
    public function editStoreData($id = '',$saveData,$where = []){

        $ret = false;
        if($id){
            $ret = $this->where(['storeid'=>$id])->update($saveData);
        }
        if($where){
            $ret = $this->where(['storeid'=>$id])->update($saveData);
        }
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除门店信息
     * Class: delStoreData
     * user: bingwoo
     * date: 2020/11/25 14:42
     */
    public function delStoreData($id){

        $ret = $this->where(['storeid'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除门店信息
     * Class: delStoreDatas
     * user: bingwoo
     * date: 2020/11/25 14:43
     */
    public function delStoreDatas($ids){

        $ret = $this->where('storeid','in',$ids)->delete();
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

        //基准价
        if(isset($data['price_type'])){
            $data['price_type_name'] = '会员价';
            if($data['price_type'] == 1){
                $data['price_type_name'] = '零售价';
            }
        }
        //奖励周期
        if(isset($data['reward_time'])){
            $reward_time_name = $this->reward_time_name;
            $data['reward_time_name'] = $reward_time_name[$data['reward_time']];
        }
        //开始-结束时间
        if (isset($data['start_business_hours'])){
            $data['start_business_hours'] = date('H:i:s',$data['start_business_hours']);
        }
        if(isset($data['end_business_hours'])){
            $data['end_business_hours'] = date('H:i:s',$data['end_business_hours']);
        }
        //门店名称
        if(isset($data['name'])){
            $name = explode('@',$data['name']);
            if(count($name) < 2){
                $data['name'] = $name[0];
            } else {
                $data['name'] = $name[1];
            }
        }
        return $data;

    }

}