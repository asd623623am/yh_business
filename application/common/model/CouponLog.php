<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: CouponLog
 * user: bingwoo
 * date: 2020/11/26 11:15
 */
class CouponLog extends Model{

    protected $table = 'xm_coupon_log';
    protected $isMustField = ['coupon_id'=>'券码id','coupon_no'=>'券码编号','user_id'=>'会员id','user_name'=>'会员名称',
                              'coupon_name'=>'券码名称','coupon_min_price'=>'最低消费金额','coupon_start_time'=>'券码开始时间',
                              'coupon_end_time'=>'券码结束时间'];
    protected $isNoMustField = ['order_no'=>'订单编号','order_price'=>'实付金额','store_id'=>'门店id','store_name'=>'门店名称',
                                'user_phone'=>'会员电话','cancel_time'=>'核销时间'];

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
     * Notes: 获取券码列表
     * Class: getCouponLogList
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getCouponLogList($where = [],$field = '*',$page = 0,$limit = 20){

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
     * Notes: 获取券码核销数量
     * Class: getCouponLogCount
     * user: bingwoo
     * date: 2020/12/11 10:27
     */
    public function getCouponLogCount($where){
        return  $this->where($where)->count();
    }

    /**
     * Notes: 获取券码信息
     * Class: getCouponInfo
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getCouponLogInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 发券
     * Class: addCouponLogData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function sendCouponLogData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量发送优惠券
     * Class: sendCouponLogDatas
     * user: bingwoo
     * date: 2021/1/7 13:41
     */
    public function sendCouponLogDatas($addDatas){

        $ret = $this->saveAll($addDatas);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改券码
     * Class: editCouponData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function editCouponLogData($id,$saveData){

        $ret = $this->where(['id'=>$id])->update($saveData);
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除券码
     * Class: delCouponData
     * user: bingwoo
     * date: 2020/11/24 20:41
     */
    public function delCouponLogData($id){

        $ret = $this->where(['id'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 删除券码
     * Class: delCouponDatas
     * user: bingwoo
     * date: 2020/11/24 20:41
     */
    public function delCouponLogDatas($ids){

        $ret = $this->where('id','in',$ids)->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 作废
     * Class: cancelCouponLogData
     * user: bingwoo
     * date: 2020/11/25 19:23
     */
    public function cancelCouponData($id){

        $ret = $this->save(['status'=>3],['coupon_id'=>$id]);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量作废
     * Class: cancelCouponLogDatas
     * user: bingwoo
     * date: 2020/11/25 19:28
     */
    public function cancelCouponDatas($ids){

        $where['coupon_id'] = ['in',$ids];
        $ret = $this->save(['status'=>3],$where);
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

        //优惠券状态
        if(isset($data['status'])){
            if($data['status'] == 1){
                $data['status_name'] = '未使用';
            }else if($data['status'] == 2){
                $data['status_name'] = '<span style="color: red">已核销<span>';
            }else if($data['status'] == 3){
                $data['status_name'] = '<span style="color: orangered;">已废弃<span>';
            }else if($data['status'] == 4){
                $data['status_name'] = '<span style="color: orangered;">已过期<span>';
            }
        }
        if(isset($data['cancel_time']) && !empty($data['cancel_time'])){
            $data['cancel_time'] = date('Y-m-d H:i:s',$data['cancel_time']);
        }
        if(isset($data['coupon_start_time'])){
            $data['coupon_start_time'] = date('Y.m.d',$data['coupon_start_time']);
        }
        if(isset($data['coupon_end_time'])){
            $data['coupon_end_time'] = date('Y.m.d',$data['coupon_end_time']);
        }
        return $data;

    }

}