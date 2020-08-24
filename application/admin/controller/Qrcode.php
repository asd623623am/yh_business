<?php

namespace app\admin\controller;

use think\Db;


/**
 * Created: by PhpStorm.
 * package: app\admin\controller
 * Class: Qrcode
 * user: bingwoo
 * date: 2020/8/23 14:27
 */
class Qrcode extends Common{


    /**
     * Notes: 获取二维码列表
     * Class: getStoreList
     * user: bingwoo
     * date: 2020/8/12 10:53
     */
    public function qrcodeList(){
        if( request() -> isAjax() ){
            $where = [];
            $where['status'] = 0;
            $getData = input('get.');
            if(!empty($getData['storename'])){
                $where['storename'] = $getData['storename'];
            }
            $data = model('qrcode')->where($where)->select()->toArray();
            $count = model('qrcode')->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            return view();
        }
    }

    /**
     * Notes: 添加门店二维码
     * Class: storeAdd
     * user: bingwoo
     * date: 2020/8/12 10:55
     */
    public function qrcodeAdd(){
        if(check()){
            $postData = input('post.');
            if(empty($postData)){
                exit('非法操作此页面');
            }
            $where = [];
            $where['storeid'] = $postData['storeid'];
            $where['tnumber'] = $postData['tnumber'];
            $count = model('qrcode')->where($where)->count();
            if($count>0){
                fail("该桌台号已存在！");
            }
            /*@todo 生成小程序二维码和公众号二维码*/
            $insData = [
                'storeid' => $postData['storeid'],
                'storename' => $postData['storename'],
                'tnumber' => $postData['tnumber'],
                'gz_qrcode' => '20200814/c2aaf371f4e2f473bfe56b2ca366fa12.jpg',
                'mini_qrcode' => '20200814/c2aaf371f4e2f473bfe56b2ca366fa12.jpg',
                'create_time' => time(),
                'update_time' => time(),
            ];
            $res = model('qrcode')->save($insData);
            if($res){
                $this -> addLog('添加桌台二维码');
                win('添加成功');
            }else{
                fail('添加失败');
            }

        }else{
            $storeData = model('store')->where(['status'=>1])->field('storeid,name')->select()->toArray();
            $this->assign('storeData',$storeData);
            return view();
        }

    }

    /**
     * Notes: 修改门店二维码
     * Class: storeEdit
     * user: bingwoo
     * date: 2020/8/12 10:55
     */
    public function qrcodeEdit(){

        if(check()){
            $postData = input('post.');
            $where = [];
            $where['storeid'] = $postData['storeid'];
            $where['tnumber'] = $postData['tnumber'];
            $where['qid'] = ['<>',$postData['qid']];
            $count = model('qrcode')->where($where)->count();
            if($count>0){
                fail("该桌台号已存在！");
            }
            /*@todo 生成小程序二维码和公众号二维码*/
            if(!empty($postData)){
                $editData = [
                    'storeid'=>$postData['storeid'],
                    'storename'=>$postData['storename'],
                    'tnumber'=>$postData['tnumber'],
                    'gz_qrcode' => '20200814/c2aaf371f4e2f473bfe56b2ca366fa12.jpg',
                    'mini_qrcode' => '20200814/c2aaf371f4e2f473bfe56b2ca366fa12.jpg',
                    'update_time'=>time(),
                ];
                $where = ['qid' => $postData['qid']];
                $res = model('qrcode')->save($editData,$where);
                if ($res) {
                    $this -> addLog('修改二维码信息');
                    win('修改成功');
                } else {
                    fail('修改失败');
                }
            }else{
                $this->getTips();
            }
        }else{
            $qid = input('get.qid');
            if(empty($qid)){
                $this->getTips();
            }else{
                $where = [
                    'qid' => $qid
                ];
            }
            $qrcodeData = model('qrcode')->where($where)->find()->toArray();
            if(empty($qrcodeData)){
                $this->fail('获取信息失败！');
            }
            $storeData = model('store')->where(['status'=>1])->field('storeid,name')->select()->toArray();
            $this->assign('storeData',$storeData);
            $this->assign('qrcode',$qrcodeData);
            return view();
        }
    }

    /**
     * Notes: 修改门店二维码
     * Class: storeEdit
     * user: bingwoo
     * date: 2020/8/12 10:55
     */
    public function qrcodeDel(){
        $postData = input('post.');
        $where = [
            'qid' => $postData['qid']
        ];
        $res = model('qrcode')->where($where)->delete();
        if ($res) {
            $this -> addLog('删除二维码'.$postData['tnumber'].'信息');
            win('删除成功');
        } else {
            fail('删除失败');
        }
    }


    /**
    * Notes: 返回get请求提示
    * Class: getTips
    * user: bingwoo
    * date: 2020/8/13 14:11
    */
    private function getTips(){
        exit('二维码信息有误');
    }
}
