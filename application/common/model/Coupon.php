<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: coupon
 * user: bingwoo
 * date: 2020/11/24 20:17
 */
class Coupon extends Model{

    protected $table = 'xm_coupon';
    protected $isMustField = ['store_id'=>'门店id','title'=>'标题','queue_sn'=>'	券码编号'];
    protected $isNoMustField = ['business_id'=>'商户id','type'=>'类型','cancel_type'=>'核销渠道', 'number'=>'发行量',
                                'use_scope'=>'使用范围','goods_type'=>'指定商品', 'min_pay_price'=>'最低消费金额',
                                'discount_price'=>'优惠金额','type_time'=>'时间类型', 'start_time'=>'开始时间',
                                'end_time'=>'结束时间','receive_type'=>'领取类型', 'receice_limit'=>'领取内容',
                                'receice_max_num'=>'	每人最大领取数量','is_use_discount'=>'与会员折扣叠',
                                'status'=>'状态','remark'=>'使用说明'];

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
        if($postData['type_time'] == 2){
            if(empty($postData['start_time']) || empty($postData['end_time'])){
                failMsg('开始时间或结束时间不能为空');
            }else{
                $postData['start_time'] = strtotime($postData['start_time']);
                $postData['end_time'] = strtotime($postData['end_time']);
            }
        }else{
            if(empty($postData['effective_days']) || empty($postData['effective_date'])){
                failMsg('有效天数或几天后生效不能为空');
            }
        }
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
     * Class: getCouponList
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getCouponList($where = [],$field = '*',$page = 0,$limit = 20,$desc = ''){

        $query = $this->field($field);
        if($where){
            $query = $query->where($where);
        }
        if($page){
            $query = $query->page($page,$limit);
        }
        if($desc){
            $query = $query->order($desc);
        }
        $data = $query->select()->toArray();
        $count = $query->count();
        return ['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];

    }

    /**
     * Notes: 获取券码信息
     * Class: getCouponInfo
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getCouponInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 添加券码
     * Class: addCouponData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function addCouponData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
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
    public function editCouponData($id,$saveData,$where = []){

        if($where){
            $ret = $this->where($where)->update($saveData);
        }else{
            $ret = $this->where(['id'=>$id])->update($saveData);
        }
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
    public function delCouponData($id){

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
    public function delCouponDatas($ids){

        $ret = $this->where('id','in',$ids)->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 作废
     * Class: cancelCouponData
     * user: bingwoo
     * date: 2020/11/25 19:23
     */
    public function cancelCouponData($id){

        $ret = $this->save(['status'=>2],['id'=>$id]);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量作废
     * Class: cancelCouponDatas
     * user: bingwoo
     * date: 2020/11/25 19:28
     */
    public function cancelCouponDatas($ids){

        $where['id'] = ['in',$ids];
        $ret = $this->save(['status'=>2],$where);
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

        //券码类型
        if(isset($data['type'])){
            $data['type_name'] = '代金券';
            if($data['type'] == 2){
                $data['type_name'] = '折扣券';
            }else if($data['type'] == 3){
                $data['type_name'] = '满减券';
            }
        }
        //时间类型--券码开始时间--券码结束时间
        if(isset($data['type_time'])){
            if($data['type_time'] == 1){
                $data['type_time_name'] = '天数';
                $startTime = strtotime($data['create_time'])+$data['effective_date']*86400;
                $data['start_time'] = date('Y-m-d',$startTime);
                $endTime = strtotime($data['create_time'])+($data['effective_date']+$data['effective_days'])*86400;
                $data['end_time'] = date('Y-m-d',$endTime);
                $data['is_expirce'] = 1;
                if(time()>$endTime){
                    $data['is_expirce'] = 2;
                    $data['is_expirce_name'] = '<span style="color: red">已过期</span>';
                }else if(time()<$startTime){
                    $data['is_expirce_name'] = '<span style="color: #6E6E6E">未开始</span>';
                }else{
                    $data['is_expirce_name'] = '<span style="color: blue">进行中</span>';
                }
            }else if($data['type_time'] == 2){
                $data['is_expirce'] = 1;
                $data['type_time_name'] = '时间';
                if(isset($data['start_time'])){
                    $data['start_time'] = date('Y-m-d',$data['start_time']);
                }
                if(isset($data['end_time'])){
                    $data['end_time'] = date('Y-m-d',$data['end_time']);
                }
                if(time()>strtotime($data['end_time'])){
                    $data['is_expirce'] = 2;
                    $data['is_expirce_name'] = '<span style="color: red">已过期</span>';
                }else if(time()<strtotime($data['start_time'])){
                    $data['is_expirce_name'] = '<span style="color: #6E6E6E">未开始</span>';
                }else{
                    $data['is_expirce_name'] = '<span style="color: blue">进行中</span>';
                }
            }
        }
        //优惠券状态
        if(isset($data['status'])){
            if($data['status'] == 1){
                $data['status_name'] = '正常';
            }else if($data['status'] == 2){
                $data['status_name'] = '<span style="color: red;">已废弃<span>';
            }
        }
        if(isset($data['cancel_rate'])){
            $data['cancel_rate'] = $data['cancel_rate'].'%';
        }
        return $data;

    }

}