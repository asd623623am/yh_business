<?php
namespace app\admin\controller;


use think\Db;

/**
 * Notes:商户基础信息
 * Created by PhpStorm.
 * user: bingwoo
 * date: 2020/8/11  
 * time: 下午 01:38
 */
class business extends Common{

    /**
     * @Notes:商户信息列表
     * @user: bingwoo
     */
    public function businessList(){

        if( request() -> isAjax() ){
            $data=Db::table("xm_business")->select();
            foreach ($data as &$val){
                $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
            }
            unset($val);
            $count=Db::table("xm_business")->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            return view();
        }
    }

    /**
     * @Notes:添加商户信息
     * @user: bingwoo
     */
    public function businessEdit(){
        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                if(isset($postData['bname'])&&!empty($postData['bname'])){
                    $where = ['bid' => $postData['bid']];
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

}
