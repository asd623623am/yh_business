<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: ShoppingCart
 * user: bingwoo
 * date: 2020/12/4 13:16
 */
class shoppingCart extends Model{

    protected $table = 'xm_shopping_cart';
    protected $isMustField = ['storeid'=>'门店id','uid'=>'会员id','gid'=>'商品id','goods_number'=>'商品数量',
                              'encryption_key'=>'整条商品信息MD5'];
    protected $isNoMustField = ['gsids'=>'菜品规格id','remark'=>'备注信息','openid'=>'微信小程序openid'];

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
     * Notes: 获取购物车列表
     * Class: getShoppingCartList
     * user: bingwoo
     * date: 2020/12/4 13:30
     */
    public function getShoppingCartList($where = [],$field = '*',$page = 0,$limit = 20,$order = '',$change = false){

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
        $data = $query->select()->toArray();
        if($change){
            foreach ($data as &$val){
                $val = $this->changeName($val);
            }
        }
        $count = $query->where($where)->count();
        return ['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];

    }

    /**
     * Notes: 获取购物车详情
     * Class: getShoppingCartInfo
     * user: bingwoo
     * date: 2020/12/4 13:31
     */
    public function getShoppingCartInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 添加购物车信息
     * Class: addShoppingCartData
     * user: bingwoo
     * date: 2020/12/4 13:32
     */
    public function addShoppingCartData($addData,$isGetID = false){

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
     * Notes: 修改购物车信息
     * Class: editShoppingCartData
     * user: bingwoo
     * date: 2020/12/4 13:32
     */
    public function editShoppingCartData($id,$saveData,$where = []){

        if($where){
            $ret = $this->where($where)->update($saveData);
        }else{
            $ret = $this->where(['orderid'=>$id])->update($saveData);
        }
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除购物车信息
     * Class: delShoppingCartData
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function delShoppingCartData($id){

        $ret = $this->where(['orderid'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量添加购物车信息
     * Class: addShoppingCartDatas
     * user: bingwoo
     * date: 2020/12/17 16:35
     */
    public function addShoppingCartDatas($adddDatas){
        $ret = $this->insertAll($adddDatas);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除购物车信息
     * Class: delShoppingCartDatas
     * user: bingwoo
     * date: 2020/12/4 13:34
     */
    public function delShoppingCartDatas($ids){

        $ret = $this->where('orderid','in',$ids)->delete();
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


    }

}