<?php
namespace  app\admin\controller;

use think\Db;

/**
 * Notes: ，门店活动
 * Created: by PhpStorm.
 * package: app\admin\controller
 * Class: Scontent
 * user: bingwoo
 * date: 2020/8/28 14:37
 */
class Activity extends Common
{
    /**
     * Notes:
     * Class: activityList
     * user: bingwoo
     * date: 2020/8/28 14:38
     */
    public function activityList(){

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

}