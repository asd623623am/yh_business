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
class Storeprint extends Common
{

    /**
     * Notes: 获取门店列表
     * Class: getStoreList
     * user: bingwoo
     * date: 2020/8/12 10:53
     */
    public function storePrintList(){

        $admin = session('admin');
        if( request() -> isAjax() ){
            $getData = input('get.');
            $where = [];
            if($admin['admin_type'] == 3){
                $where['storeid'] = $admin['storeid'];   
            } else {
                if(!empty($getData['store'])){
                    $where['storeid'] = $getData['store'];
                }
            }
            if(!empty($getData['device_no'])){
                $where['device_no'] = $getData['device_no'];
            }
            
            $data = model('store_print')->where($where)->select()->toArray();
            if(!empty($data)){
                foreach ($data as &$val){
                    $val['type']='GPS打印机';
                }
                unset($val);
                $count = model('store_print')->where($where)->count();
            }else{
                $data = [];
                $count = 0;
            }
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
           
            $is_display = 0;
            if($admin['admin_type'] == 3){
                $is_display = 2; //2是上架权限
            } else {
                $is_display = 1; //1是机场权限
            }

            $xm_store = Db::table('xm_store')->where(['status'=>1])->select();
            $this->assign('store',$xm_store);
            $this->assign('is_display',$is_display);
            return view();
        }
    }

    /**
     * Notes: 查看门店信息
     * Class: storeInfo
     * user: bingwoo
     * date: 2020/8/13 14:13
     */
    public function storePrintInfo(){

        $postData = input('get.');
        if(!empty($postData)){
            $spid = $postData['spid'];
            $storePrintData = model('store_print')->where(['spid'=>$spid])->find();
            if(!empty($storePrintData)){
                $this->assign('print',$storePrintData);
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
    public function storePrintAdd(){
        $storeid = getStoreid();
        if(check()){
            $dataPost = input('post.');
            if(empty($dataPost)){
                exit('非法操作此页面');
            }
            $saveData = \app\admin\model\StorePrint::isVerifField($dataPost);
            $saveData['type'] = 1;
            $saveData['storeid'] = $storeid;
            $res = model('store_print')->save($saveData);
            if($res){
                $this->addLog('添加打印机');
                win('添加成功');
            }else{
                fail('添加失败');
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
    public function storePrintEdit(){
        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                $insert = \app\admin\model\StorePrint::isVerifField($postData);
                $insert['update_time'] = time();
                $where = ['spid' => $postData['spid']];
                $res = model('store_print')->save($insert,$where);
                if ($res) {
                    $this -> addLog('修改打印机信息');
                    win('修改成功');
                } else {
                    fail('修改失败');
                }
            }
        }else{
            $spid = input('get.spid');
            if(empty($spid)){
                $this->getTips();
            }else{
                $where = [
                    'spid' => $spid
                ];
            }
            $storePrintData = model('store_print')->where($where)->find();
            if(empty($storePrintData)){
                $this->fail('获取打印机信息失败！');
            }
            $this->assign('print',$storePrintData);
            return view();
        }
    }

    /**
     * Notes: 删除门店信息
     * Class: storeDel
     * user: bingwoo
     * date: 2020/8/12 10:56
     */
    public function storePrintDel(){
        $postData = input('post.');
        if(!empty($postData)){
            $where = ['spid' => $postData['spid']];
            $res = model('store_print')->where($where)->delete();
            if ($res) {
                $this -> addLog('删除打印机信息');
                win('删除成功');
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
        exit('打印机信息有误');
    }


}
