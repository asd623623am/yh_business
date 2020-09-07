<?php

namespace app\admin\controller;

use think\Db;


/**
 * Created by PhpStorm.
 * package app\admin\controller
 * Class Store
 * user: bingwoo
 * date: 2020/8/12 10:50
 */
class Store extends Common
{

    /**
     * Notes: 获取门店列表
     * Class: getStoreList
     * user: bingwoo
     * date: 2020/8/12 10:53
     */
    public function storeList(){

        if( request() -> isAjax() ){
            $where = ['status' => 1];
            $getData = input('get.');
            if(!empty($getData['storename'])){
                $where['name'] = $getData['storename'];
            }
            if(!empty($getData['code'])){
                $where['store_no'] = $getData['code'];
            }
            if(!empty($getData['user_name'])){
                $where['user_name'] = $getData['user_name'];
            }
            $data=Db::table("xm_store")->where($where)->select();
            foreach ($data as &$val){
                $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                $val['start_end_time'] = date('H:i:s',$val['start_business_hours']).'-'.date('H:i:s',$val['end_business_hours']);
            }
            unset($val);
            $count=Db::table("xm_store")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            return view();
        }
    }

    /**
     * Notes: 查看门店信息
     * Class: storeInfo
     * user: bingwoo
     * date: 2020/8/13 14:13
     */
    public function storeInfo(){

        $postData = input('get.');
        if(!empty($postData)){
            $storeId = $postData['storeid'];
            $storeData = model('Store')->where(['storeid'=>$storeId])->find()->toArray();
            if(!empty($storeData)){
                $storeData['start_business_hours'] = date('H:i:s',$storeData['start_business_hours']);
                $storeData['end_business_hours'] = date('H:i:s',$storeData['end_business_hours']);
                $this->assign('store',$storeData);
                return  view();
            }
            $this->getTips();
        }
        $this->getTips();
    }

    /**
     * Notes: 添加门店信息
     * Class: storeAdd
     * user: bingwoo
     * date: 2020/8/12 10:55
     */
    public function storeAdd(){
        if(check()){
            $data = input('post.');
            if(empty($data)){
                exit('非法操作此页面');
            }
            ######## 东杰
            $admin_where = [
                'admin_name'   => $data['account']
            ];
            $res = model('Admin')->where($admin_where)->select()->toArray();
            if(!empty($res)){
                fail('登录账号已被用，请更换登录账号！');
            }
            ##########
            $where = [
                'name'	=> $data['name'],
            ];
            $info = model('Store')->where($where)->find();
            if (!empty($info)) {
                fail('门店名称已存在！');
            } else {
                $insert = $this->processData($data);
                $storeInfo = model('Store')->field('storeid')->order('storeid desc')->find();
                $insert['store_no'] = 100001;
                if(!empty($storeInfo['storeid'])){
                    $len = strlen($storeInfo['storeid']);
                    $strno = substr(100001,0,6-$len);
                    $storeInfo['storeid'] += 1;
                    $insert['store_no'] = $strno.$storeInfo['storeid'];
                }
                $insert['create_time'] = time();
                $insert['update_time'] = time();
                $store_id = model('Store')->insertGetId($insert);
                /* @todo 添加用户和门店权限绑定*/
                if($store_id){

                    $admin_insert = [
                        'admin_type'    =>3,
                        'admin_name'    =>$data['account'],
                        'admin_pwd'     =>$data['passwords'],
                        'admin_tel'     =>'',
                        'storeid'       => $store_id
                    ];
                    $model = model('Admin');
                    $model->save($admin_insert);
                    $admin_id = $model -> getLastInsID();
                    if($admin_id){
                        $role_ids = model('Role')->where(['type'=>4])->find()->toArray();
                        $roleinsert = [
                            'admin_id'  => $admin_id,
                            'role_id'   => $role_ids['role_id']
                        ];
                        $adminres = model('AdminRole')->insert($roleinsert);
                        if($adminres){
                            $this->addLog('添加了一个门店');
                            win('添加成功');
                        }else{
                            fail('添加失败');
                        }
                    } else {
                        fail('添加失败');
                    }
                }else{
                    fail('添加失败');
                }
            }

        }else{
            return view();
        }

    }

    /**
     * Notes: 修改门店信息
     * Class: storeEdit
     * user: bingwoo
     * date: 2020/8/12 10:54
     */
    public function storeEdit(){
        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                $insert = $this->processData($postData);
                $where = ['storeid' => $postData['storeid']];
                $res = model('Store')->save($insert,$where);
                if ($res) {
                    $this -> addLog('修改门店信息');
                    win('修改成功');
                } else {
                    fail('修改失败');
                }
            }
        }else{
            $storeId=input('get.storeid');
            if(empty($storeId)){
                $this->getTips();
            }else{
                $where = [
                    'storeid' => $storeId
                ];
            }
            $storeData = model('Store')->where($where)->find()->toArray();
            if(empty($storeData)){
                $this->fail('获取信息失败！');
            }
            $storeData['start_business_hours'] = date('H:i:s',$storeData['start_business_hours']);
            $storeData['end_business_hours'] = date('H:i:s',$storeData['end_business_hours']);
            $this->assign('store',$storeData);
            return view();
        }
    }

    /**
     * Notes: 删除门店信息
     * Class: storeDel
     * user: bingwoo
     * date: 2020/8/12 10:56
     */
    public function storeDel(){
        $postData = input('post.');
        if(!empty($postData)){
            $delDate = ['status' => 0];
            $where = ['storeid' => $postData['storeid']];
            
            $res = model('Store')->save($delDate,$where);
            if ($res) {
                $admin = model('Admin')->where($where)->select()->toArray();
                $delId = [];
                foreach($admin as $k=>$v){
                    $delId[] = $v['admin_id'];
                }
                $reslut = model('Admin')->where($where)->delete();
                if($reslut){
                    $wheres = [
                        'admin_id'  => array('in',$delId)
                    ];
                    $ress = model('AdminRole')->where($wheres)->delete();
                    if($ress){
                        $this -> addLog('删除门店信息');
                        win('删除成功');
                    } else {
                        fail('删除失败');
                    }
                } else {
                    fail('删除失败');
                }
            } else {
                fail('删除失败');
            }
        }
        $this->postTips();

    }

    /**
     * 批量删除门店信息
     */
    public function StoreDels()
    {
        $postData = input('post.');
        if(!empty($postData)){
            $storeid = [];
            foreach($postData['data'] as $k=>$v){
                $storeid[] = $v['storeid'];
            }
            $delDate = ['status' => 0];
            $where = [
                'storeid'   => array('in',$storeid)
            ];
            $res = model('Store')->save($delDate,$where);
            if ($res) {

                $admin = model('Admin')->where($where)->select()->toArray();
            
                $delId = [];
                foreach($admin as $k=>$v){
                    $delId[] = $v['admin_id'];
                }

                $reslut = model('Admin')->where($where)->delete();
                if($reslut){
                    $wheres = [
                        'admin_id'  => array('in',$delId)
                    ];
                    $ress = model('AdminRole')->where($wheres)->delete();
                    if($ress){
                        $this -> addLog('删除门店信息');
                        win('删除成功');
                    } else {
                        fail('删除失败');
                    }
                    
                } else {
                    fail('删除失败');
                }
            } else {
                fail('删除失败');
            }
        }
        $this->postTips();
    }
    /**
     * Notes: 处理数据格式
     * Class: processDara
     * user: bingwoo
     * date: 2020/8/13 13:53
     */
    private function processData($postData){
        $insert = [
            'name'		=> $postData['name'],
            'address'	=> $postData['address'],
            'user_name'	=> $postData['user_name'],
            'user_tel'	=> (int)$postData['user_tel'],
            'account'	=> $postData['account'],
            'password'	=> $postData['passwords'],
        ];
        //门店logo
        if(!empty($postData['banner_url'])){
            $len = strlen($postData['banner_url']);
            $strDate = substr($postData['banner_url'],1,8);
            $strname = substr($postData['banner_url'],10,$len-10);
            $insert['logo'] = $strDate.'/'.$strname;
        }
        //pos支付
        if(!empty($postData['pos_pay'])){
            $insert['pos_pay'] = $postData['pos_pay'];
        }
        //刷脸支付
        if(!empty($postData['face_pay'])){
            $insert['face_pay'] =$postData['face_pay'];
        }
        //营业时间
        $insert['start_business_hours'] = strtotime(date('Y-m-d').$postData['start_business_hours']);
        $insert['end_business_hours'] = strtotime(date('Y-m-d').$postData['end_business_hours']);
        return $insert;
    }

    /**
     * Notes: 返回post提示信息
     * Class: tips
     * user: bingwoo
     * date: 2020/8/13 14:05
     */
    private function postTips(){
        return fail('门店信息有误');
    }

    /**
     * Notes: 返回get请求提示
     * Class: getTips
     * user: bingwoo
     * date: 2020/8/13 14:11
     */
    private function getTips(){
        exit('门店信息有误');
    }


}
