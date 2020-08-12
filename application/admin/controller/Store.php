<?php

namespace app\admin\controller;

use think\Db;
use think\View;


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
            $data=Db::table("xm_store")->select();
            foreach ($data as &$val){
                $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
            }
            unset($val);
            $count=Db::table("xm_store")->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            return view();
        }
    }

    /**
     * Notes: 添加门店信息
     * Class: storeAdd
     * user: bingwoo
     * date: 2020/8/12 10:55
     */
    public function storeAdd(){
        if(check()){
            # 检查名字是否重复
            $data=input('post.');
            if(empty($data)){
                exit('非法操作此页面');
            }
            $where = [
                'district_id'	=> $data['dis'],
                'complex'		=> $data['qu'],
                'home_code'		=> $data['home_code']
            ];
            $info = model('Home')->where($where)->find();
            if ($info != null) {

                fail('已经有此房子，请去修改房屋信息！');

                if (isset($data['status'])) {

                    $arr = explode('-', $data['home_code']);
                    $len = count($arr);
                    if ($len == 3) {
                        $building = $arr[0];
                        $unit = $arr[1];
                        $room = $arr[2];

                    } else {
                        $building = $arr[0];
                        $unit = '';
                        $room = $arr[1];
                    }

                    $insert = [
                        'home_ids'		=> date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT),
                        'district_id'	=> $data['dis'],
                        'district_name'	=> $data['dname'],
                        'complex'		=> $data['qu'],
                        'home_code'		=> $data['home_code'],
                        'building'		=> $building,
                        'unit'			=> $unit,
                        'room'			=> $room,
                        'owner'			=> $data['home_name'],
                        'tel'			=> $data['tel'],
                        'area'			=> $data['area'],
                        'check_in_at'   => $data['ctime'],
                        'is_delete'		=> 1,
                        'ctime'			=> time(),
                        'content'		=> $data['content'],
                    ];


                    $res = model('Home')->allowField(true)->save($insert);
                    if ($res) {
                        $this -> addLog('添加了一个房屋');
                        win('添加成功');
                    } else {
                        fail('添加失败');
                    }
                } else {
                    echo json_encode(['font' => '您确认添加吗？', 'code' => 3]);
                    exit;
                }

            } else {

                $arr = explode('-', $data['home_code']);
                $len = count($arr);
                if ($len == 3) {
                    $building = $arr[0];
                    $unit = $arr[1];
                    $room = $arr[2];

                } else {
                    $building = $arr[0];
                    $unit = '';
                    $room = $arr[1];
                }


                $insert = [
                    'home_ids'		=> date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT),
                    'district_id'	=> $data['dis'],
                    'district_name'	=> $data['dname'],
                    'complex'		=> $data['qu'],
                    'home_code'		=> $data['home_code'],
                    'building'		=> $building,
                    'unit'			=> $unit,
                    'room'			=> $room,
                    'owner'			=> $data['home_name'],
                    'tel'			=> $data['tel'],
                    'area'			=> $data['area'],
                    'check_in_at'   => $data['ctime'],
                    'is_delete'		=> 1,
                    'ctime'			=> time(),
                    'content'		=> $data['content'],
                ];


                $res = model('Home')->allowField(true)->save($insert);
                if($res){
                    $this -> addLog('添加了一个房屋');
                    win('添加成功');
                }else{
                    fail('添加失败');
                }
            }

        }else{
            $district=model('District')->select()->toArray();
            $this->assign('dis',$district);
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
                if(isset($postData['bname'])&&!empty($postData['bname'])){
                    $where = ['bid' => $postData['bid']];
//                    $insert['bid'] = $postData['bid'];
                    $insert['bname'] = $postData['bname'];
                    if(!empty($postData['banner_url'])){
                        $len = strlen($postData['banner_url']);
                        $strDate = substr($postData['banner_url'],1,8);
                        $strname = substr($postData['banner_url'],10,$len-10);
                        $insert['logo'] = $strDate.'/'.$strname;
                    }
                    $insert['update_time'] = time();
                    $res = model('Business')->save($insert,$where);
                    if ($res) {
                        $this -> addLog('修改商户信息');
                        win('修改成功');
                    } else {
                        fail('修改失败');
                    }
                }
            }
        }else{
            $bid=input('get.bid');
            if(empty($bid)){
                exit('非法操作此页面');
            }else{
                $where=[
                    'bid'=>$bid
                ];
            }
            $bsData = model('Business')->field('bid,bname,logo')->where($where)->find()->toArray();
            $this->assign('bs',$bsData);
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

    }


}
