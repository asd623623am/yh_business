<?php
namespace app\common\model;
use think\Request;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: Syncinfo
 * user: bingwoo
 * date: 2020/12/28 10:52
 */
class Syncinfo extends model{

    protected  $url = 'http://restaurant.yinheyun.com.cn/api.php/';

    /**
     * Notes: 首次同步
     * Class: syncFirst
     * user: bingwoo
     * date: 2020/12/28 10:57
     */
    public function syncFirst($postData,$type = []){
        //同步门店信息
        $storeData = $this->syncStoreInfo($postData);
        if(!$storeData){
            failMsg('同步门店信息失败');
        }
        //同步门店用户信息
        if(!isset($type['user'])){
            $uRet = $this->syncUserList($storeData);
            if(!$uRet){
                faileMsg('同步会员信息失败');
            }
        }
        //同步门店订单信息
        if(!isset($type['order'])){
            $oRet = $this->syncOrderList($storeData);
            if(!$oRet){
                failMsg('同步订单信息失败');
            }
        }
        successMsg('同步成功');
    }

    /**
     * Notes: 同步门店信息(单条)
     * Class: syncStoreInfo
     * user: bingwoo
     * date: 2020/12/17 11:35
     */
    public function syncStoreInfo($postStoreData){

        //拉取门店信息
        $url = $this->url.'store/getStoreInfo';
        $data = request_post($url,$postStoreData);
        if($data === false){
            addNoticeLog('拉取门店信息失败',2,'getStoreInfo',$postStoreData,1);
            return false;
        }
        $data = json_decode($data,true);
        $storeModel = new Store();
        $where = [];
        $where['member_secret_key'] = ['=',$postStoreData['member_secret_key']];
        $where['airscan_secret_key'] = ['=',$postStoreData['airscan_secret_key']];
        //验证门店信息是否已同步
        $storeInfo = $storeModel->getStoreInfo($where,'storeid');
        $saveData = [
            'member_secret_key'=>$data['member_secret_key'],
            'airscan_secret_key'=>$data['airscan_secret_key'],
            'name'=>$data['name'],
            'store_no'=>$data['store_no'],
            'logo'=>$data['logo'],
            'address'=>$data['address'],
            'user_name'=>$data['user_name'],
            'user_tel'=>$data['user_tel'],
            'start_business_hours'=>$data['start_business_hours'],
            'end_business_hours'=>$data['end_business_hours'],
            'account'=>$data['account'],
            'password'=>$data['password'],
            'status'=>$data['status'],
            'update_time'=>time(),
        ];
        $ret = $storeModel->editStoreData($storeInfo['storeid'],$saveData);
        if(!$ret){
            addNoticeLog('同步门店信息失败',2,'editStoreData',$saveData,1);
            return false;
        }
        $retData = [
            'member_storeid'=>$storeInfo['storeid'],
            'airscan_storeid'=>$data['storeid']
        ];
        return $retData;
    }

    /**
     * Notes: 批量同步用户信息
     * Class: syncUserList
     * user: bingwoo
     * date: 2020/12/17 14:28
     */
    public function syncUserList($storeData){

        $url = $this->url.'user/getUserList';
        $param = [
            'storeid'=>$storeData['airscan_storeid']
        ];
        $retData = request_post($url,$param);
        if($retData === false){
            addNoticeLog('获取会员信息失败',1,'xm_member',$retData);
            return false;
        }
        $retData = json_decode($retData,true);
        $addData = [];
        if(!empty($retData) && is_array($retData)){
            $userModel = new \app\common\model\User();
            //获取当前门店所有会员信息@todo 收起获取列表需要添加缓存
            $userData = $userModel->getUserListByStoreid($storeData['member_storeid'],'wx_openid');
            foreach ($retData as &$rval){
                //判断：如果用户信息存在，则过滤已存在的用户信息
                if(!empty($userData)){
                    foreach ($userData as $uVal){
                        if(!isset($uVal[$rval['wx_openid']])){
                            $addData = $this->addUserData($rval);
                        }
                    }
                }else{
                    $addData = $this->addUserData($rval);
                }
            }
            if($addData){
                $ret = $userModel->addUserDataAll($addData);
                if($ret){
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    /**
     * Notes: 追加用户信息到数组
     * Class: addUserData
     * user: bingwoo
     * date: 2020/12/24 13:32
     */
    public function addUserData($rval,$type = false){

        $userModel = new \app\common\model\User();
        $data = $userModel->isVerifSyncColumn($rval);
        if(!isset($data['card'])){
            $data['card'] = getSerial();
        }
        if($type){
            return $data;
        }
        $addData[] = $data;
        return $addData;
    }

    /**
     * Notes: 同步会员指定门店订单
     * Class: syncOrder
     * user: bingwoo
     * date: 2020/12/17 16:41
     */
    public function syncOrderList($storeData,$useData = []){

        $postData = [
            'storeid'=>$storeData['airscan_storeid']
        ];
        //注：有wx_openid,则同步指定门店下，指定用户的订单信息
        if(isset($useData['wx_openid'])){
            $postData = [
                'wx_openid'=>$useData['wx_openid'],
            ];
        }
        $url = $this->url.'order/getOrderList';
        $orderData = request_post($url,$postData);
        if($orderData === false){
            addNoticeLog('拉取门店订单列表失败',2,'getOrderList',$postData,1);
            return false;
        }
        $orderData = json_decode($orderData,true);
        //处理订单信息和订单上坡数据
        $addOrder = [];
        $addOrderGoods = [];
        $data = [];
        $oModel = new Order();
        if(!empty($orderData)){
            $omData = [];
            if(isset($useData['wx_openid']) && !empty($useData['wx_openid'])){
                $owhere = [];
                $owhere['wx_openid'] = ['=',$useData['wx_openid']];
                $owhere['member_storeid'] = ['=',$useData['member_storeid']];
                $oData = $oModel->getOrderList($owhere,'order_sn');
                if(!empty($oData['data'])){
                    $omData = $oData['data'];
                }
            }
            foreach ($orderData as &$val){
                if(!empty($omData)){
                    foreach ($omData as $omVal){
                        if(!isset($omVal[$val['order_sn']])){
                            $data = $this->addOrderData($val,$storeData,$addOrder,$addOrderGoods);
                        }
                    }
                }else{
                    $data = $this->addOrderData($val,$storeData,$addOrder,$addOrderGoods);
                }
            }
        }
        //批量添加订单信息
        if(!empty($data['addData'])) {
            $oret = $oModel->addOrderDatas($addOrder);
            if (!$oret) {
                addNoticeLog('添加订单信息失败', 2, 'addOrderDatas', $addOrder, 1);
                return false;
            }
            //批量添加商品信息
            if(!empty($data['addOrderGoods'])){
                $ogModel = new OrderGoods();
                $ogret = $ogModel->addOrderGoodsDatas($addOrderGoods);
                if(!$ogret){
                    addNoticeLog('添加订单商品信息失败',2,'addOrderGoodsDatas',$addOrderGoods,1);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Notes: 追加订单信息到数组
     * Class: addOrderData
     * user: bingwoo
     * date: 2020/12/25 12:02
     */
    public function addOrderData($val,$postData,$addOrder,$addOrderGoods){

        if(!empty($val['goods']) && is_array($val['goods'])){
            foreach ($val['goods'] as &$gval){
                unset($gval['ogid']);
                $addOrderGoods[] = $gval;
            }
        }
        $val['storeid'] = $postData['member_storeid'];
        unset($val['goods']);
        unset($val['orderid']);
        $addOrder[] = $val;
        $data = [
            'addorder' => $addOrder,
            'addOrderGoods' => $addOrderGoods,
        ];
        return $data;
    }

    /**
     * Notes: 同步指定会员信息(单条)备注：拉取点餐会员信息同步到会员
     * Class: syncUserInfo
     * user: bingwoo
     * date: 2020/12/17 9:54
     */
    public function syncUserInfo($postData){
        //拉取会员信息
        $url = $this->url.'user/getUserInfo';
        $param = [
            'third_wx_openid'=>$postData['wx_openid']
        ];
        $data = request_post($url,$param);
        if($data === false){
            addNoticeLog('拉取会员信息失败',2,'getUserInfo',$postData,1);
            return false;
        }
        $data = json_decode($data,true);
        $userModel = new \app\common\model\User();
        $where = [];
        //判断：用户手机号是否存在
        if(!empty($data['phone'])){
            $where['phone'] = ['=',$data['phone']];
        }else{
            $where['third_wx_openid'] = ['=',$data['wx_openid']];
        }
        //检查用户是否存在
        $userInfo = $userModel->getUserInfo($where);
        if(!empty($userInfo)){
            //追加数据
            $editData = $this->addUserData($data,true);
            //修改数据
            $ret = $userModel->editUserData($data['id'],$editData);
            if(!$ret){
                addNoticeLog('更新指定用户信息失败',2,'editUserData',$editData,1);
                return false;
            }
        }else{
            $addData = $this->addUserData($data,true);
            $ret = $userModel->addUserData($addData);
            if(!$ret){
                addNoticeLog('添加指定用户信息失败',2,'addUserData',$addData,1);
                return false;
            }
        }
        return true;
    }

    /**
     * Notes: 更新会员信息到会员系统（备注：用户信息更新，先更新会员，后同步到点餐）
     * Class: syncUserInfoByAirscan
     * user: bingwoo
     * date: 2021/1/15 16:27
     */
    public function syncUserInfoByAirscan($postData){

        $userModel = new \app\common\model\User();
        $where = [];
        //判断：用户手机号是否存在
        if(!empty($postData['phone'])){
            $where['phone'] = ['=',$postData['phone']];
        }else{
            $where['third_wx_openid'] = ['=',$postData['wx_openid']];
        }
        //检查用户是否存在
        $userInfo = $userModel->getUserInfo($where);
        if(!empty($userInfo)){
            //追加数据
            $editData = $this->addUserData($postData,true);
            //修改数据
            $ret = $userModel->editUserData('',$editData,$where);
            if(!$ret){
                addNoticeLog('更新指定用户信息失败',2,'editUserData',$editData,1);
                return false;
            }
        }else{
            $addData = $this->addUserData($postData,true);
            $ret = $userModel->addUserData($addData);
            if(!$ret){
                addNoticeLog('添加指定用户信息失败',2,'addUserData',$addData,1);
                return false;
            }
        }
        return true;
    }

    /**
     * Notes: 同步指定订单信息(单条)
     * Class: syncUserOrderInfo
     * user: bingwoo
     * date: 2020/12/17 9:53
     */
    public function syncOrderInfo($postData){

        $param = [
            'order_sn'=>$postData['order_sn'],
            'type'=>'goods',//返回商品数据
        ];
        //拉取指定订单信息
        $url = $this->url.'order/getOrderInfo';
        $data = request_post($url,$param);
        if($data === false){
            addNoticeLog('拉取指定订单信息失败',2,'getOrderInfo',$postData,1);
        }
        $data = json_decode($data);
        if(!empty($data)){
            $addOrder = [];
            $addOrderGoods = [];
            $OrderRet = $this->addOrderData($data,$postData['member_storeid'],$addOrder,$addOrderGoods);
            $orderModel = new Order();
            $owhere = [];
            $owhere['order_sn'] = ['=',$postData['order_no']];
            //@todo 判断：订单是否存在，存在则修改，不存在则添加
            $orderInfo = $orderModel->getOrderInfo($owhere,'orderid');
            if(!empty($orderInfo)){
                $oRet = $orderModel->editOrderData($orderInfo['orderid'],$OrderRet['addorder']);
            }else{
                $oRet = $orderModel->addOrderData($OrderRet['addorder']);
            }
            if(!$oRet){
                addNoticeLog('添加订单信息失败', 2, 'addOrderDatas', $addOrder, 1);
                return false;
            }
            //@todo 删除已有订单商品记录，从新添加订单信息
            $orderGoodsModel = new OrderGoods();
            $ogwhere = [];
            $owhere['order_sn'] = ['=',$postData['order_no']];
            $edlRet = $orderGoodsModel->delOrderGoodsDatas('',$ogwhere);
            if(!$edlRet){
                addNoticeLog('删除订单商品失败',2,'delOrderGoodsDatas',$addOrderGoods,1);
                return false;
            }
            $ogRet = $orderGoodsModel->addOrderGoodsDatas($OrderRet['addOrderGoods']);
            if($ogRet){
                addNoticeLog('添加订单商品信息失败',2,'addOrderGoodsDatas',$addOrderGoods,1);
                return false;
            }
            return true;
            return false;

        }
        //批量添加订单信息
        if(!empty($addOrder)) {
            $oModel = new Order();
            $oret = $oModel->addOrderDatas($addOrder);
            if (!$oret) {
                addNoticeLog('添加订单信息失败', 2, 'addOrderDatas', $addOrder, 1);
                return false;
            }
        }
        //批量添加商品信息
        if(!empty($addOrderGoods)){
            $ogModel = new OrderGoods();
            $ogret = $ogModel->addOrderGoodsDatas($addOrderGoods);
            if(!$ogret){
                addNoticeLog('添加订单商品信息失败',2,'addOrderGoodsDatas',$addOrderGoods,1);
                return false;
            }
        }
        return true;
    }

}