<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: OrderGoods
 * user: bingwoo
 * date: 2020/12/8 17:31
 */
class OrderGoods extends Model{

    protected $table = 'xm_order_goods';
    protected $isMustField = ['order_id'=>'订单号','goods_id'=>'商品编号','goods_img'=>'商品图片','goods_name'=>'商品名称',
                              'goods_sn'=>'商品名称','goods_number'=>'商品数量'];
    protected $isNoMustField = ['gbsid'=>'绑定规格id','original_price'=>'原价价格','selling_price'=>'销售价',
                                'staff_price'=>'员工价','remark'=>'备注信息'];

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
     * Notes: 获取订单商品列表
     * Class: getOrderGoodsList
     * user: bingwoo
     * date: 2020/12/8 17:32
     */
    public function getOrderGoodsList($where = [],$field = '*',$page = 0,$limit = 20,$order = ''){

        $query = $this->field($field);
        if($where){
            $query = $query->where($where);
        }
        if($order){
            $query = $query->order($order);
        }
        if($page){
            $query = $query->page($page,$limit);
        }
        $data = $query->select();
        if($data){
            $data = $data->toArray();
            foreach ($data as &$val){
                $val = $this->changeName($val);
            }
        }
        $count = $query->count();
        return ['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];

    }

    /**
     * Notes: 获取订单商品详情
     * Class: getOrderGoodsInfo
     * user: bingwoo
     * date: 2020/12/8 17:32
     */
    public function getOrderGoodsInfo($where = [],$field = '*',$Order = ''){
        $qurey = $this;
        if(!empty($where)){
            $qurey = $qurey->where($where);
        }
        if(!empty($Order)){
            $qurey = $qurey->order($Order);
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
     * Notes: 添加订单商品信息
     * Class: addOrderGoodsData
     * user: bingwoo
     * date: 2020/12/8 17:33
     */
    public function addOrderGoodsData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改订单商品信息
     * Class: editOrderGoodsData
     * user: bingwoo
     * date: 2020/12/8 17:33
     */
    public function editOrderGoodsData($id,$saveData){

        $ret = $this->where(['id'=>$id])->update($saveData);
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除订单商品信息
     * Class: delOrderGoodsData
     * user: bingwoo
     * date: 2020/12/8 17:33
     */
    public function delOrderGoodsData($id){

        $ret = $this->where(['id'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量添加订单商品信息
     * Class: addOrderGoodsDatas
     * user: bingwoo
     * date: 2020/12/17 10:50
     */
    public function addOrderGoodsDatas($addOrderGoods){

        $ret = $this->insertAll($addOrderGoods);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除订单商品信息
     * Class: delOrderGoodsDatas
     * user: bingwoo
     * date: 2020/12/8 17:33
     */
    public function delOrderGoodsDatas($ids,$where = []){

        if($where){
            $ret = $this->where('id','in',$ids)->delete();
        }else{
            $ret = $this->where($where)->delete();
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
     * date: 2020/12/4 13:34
     */
    public function changeName(array $data){

        if(isset($data['goods_img']) && !empty($data['goods_img'])){
            $data['goods_img'] = 'uploads/'.$data['goods_img'];
        }
        $gbsid = explode(',',$data['gbsid']);
        $data['son'] = [];
        if(count($gbsid)>0 && $gbsid[0] != ''){
            $gwhere = [
                'gsid'	=> array('in',$gbsid),
                'status'	=> 0
            ];
            $gsModel = new GoodsSpec();
            $gsData = $gsModel->getGoodsSpaceList($gwhere);
            $data['son'] = $gsData['data'];
            $data['space_name'] = '';
            if(!empty($data['son'])){
                $space_name = [];
                foreach ($data['son'] as $val){
                    $space_name[] = $val['gsname'];
                }
                $data['space_name'] = implode(',',$space_name);
            }
        }
        return $data;
    }

}