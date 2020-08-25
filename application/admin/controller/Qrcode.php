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
            //生成公众号二维码
            $retQrcode = $this->getQrcodeInfo($postData['storeid'],$postData['tnumber'],$postData['storename']);
            /*@todo 生成小程序二维码和公众号二维码*/
            $insData = [
                'storeid' => $postData['storeid'],
                'storename' => $postData['storename'],
                'tnumber' => $postData['tnumber'],
                'gz_qrcode' => $retQrcode,
                'mini_qrcode' => '',
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
            $retQrcode = $this->getQrcodeInfo($postData['storeid'],$postData['tnumber'],$postData['storename']);
            /*@todo 生成小程序二维码和公众号二维码*/
            if(!empty($postData)){
                $editData = [
                    'storeid'=>$postData['storeid'],
                    'storename'=>$postData['storename'],
                    'tnumber'=>$postData['tnumber'],
                    'gz_qrcode' => $retQrcode,
                    'mini_qrcode' => '',
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
     * Notes: 删除门店二维码
     * Class: storeEdit
     * user: bingwoo
     * date: 2020/8/12 10:55
     */
    public function qrcodeDel(){
        $postData = input('post.');
        $where = [
            'qid' => $postData['qid']
        ];
        /* @todo 删除本地图片文件*/
        $res = model('qrcode')->where($where)->delete();
        if ($res) {
            $this -> addLog('删除二维码'.$postData['tnumber'].'信息');
            win('删除成功');
        } else {
            fail('删除失败');
        }
    }

    /**
     * Notes: 下载公众号二维码
     * Class: downloadQrocde
     * user: bingwoo
     * date: 2020/8/25 15:50
     */
    public function downloadQrocde(){



        $postData = input('post.');
        $qrcodeData = model('qrcode')->field('gz_qrcode')->where(['qid'=>$postData['qid']])->find()->toArray();
        header("Content-type:text/html;charset=utf-8");
        $file_name = 'GZ_'.$postData['storename'].'('.$postData['tnumber'].')'.'.jpg';
        $filepath = ROOT_PATH . 'public' . DS . 'qrcode'.'/'.$qrcodeData['gz_qrcode'];
        //判断文件是否存在
        if(!file_exists($filepath)){
            echo "没有该文件文件";
            return ;
        }
        $ret = $this->getImage($filepath,'images/',$file_name);
        dump($ret);die();
    }

    /**
     * Notes: 获取图片信息
     * Class: getImage
     * user: bingwoo
     * date: 2020/8/25 17:51
     */
    function getImage($url,$save_dir='',$filename='',$type=0){
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(trim($filename)==''){//保存文件名
            $ext=strrchr($url,'.');
            if($ext!='.gif'&&$ext!='.jpg'){
                return array('file_name'=>'','save_path'=>'','error'=>3);
            }
            $filename=time().$ext;
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
    }

    /**
     * Notes: 获取门店桌台二维码
     * Class: getQrcodeInfo
     * user: bingwoo
     * date: 2020/8/25 13:19
     */
    public function getQrcodeInfo($sotrid,$tnumber,$storeName){

        $systemInfo = model('system')->find()->toArray();
        $token = $systemInfo['gz_token'];
        if($token){

            $tiUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$token;
            $scene = [
                'scene_id' => '1234',
                'storeid' => $sotrid,
                'tnumber' => $tnumber
            ];
            $data = [
                'expireSeconds' => 86400,
                'action_name' => 'QR_SCENE',
                'action_info' => $scene
            ];
            $ret = request_post($tiUrl,json_encode($data));
            $ret = @json_decode($ret,true);
            if(!isset($ret['ticket'])){
                return '';
            }
            //通过ticket换取二维码
            $ticket = $ret['ticket'];
            $longtime = 86400;
            $qrcodeUrl = $ret['url'];
            $qrUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket.'&expire_seconds='.$longtime.'&url='.$qrcodeUrl;
            $reqrUrl = $this->updateImg($qrUrl,$tnumber,$storeName);
            return $reqrUrl;
        }



    }

    /**
     * Notes: 上传图片
     * Class: updateImg
     * user: bingwoo
     * date: 2020/8/25 15:48
     */
    private function updateImg($url,$tnumber,$storeName){

        //上传图片
        ob_start();
        //读取文件
        readfile($url);
        //拿到图片  保存到变量操作
        $img  = ob_get_contents();
        //关闭缓冲
        ob_end_clean();
        $folder = ROOT_PATH . 'public' . DS . 'qrcode'.'/'.date("Y-m-d");
        is_dir($folder) OR mkdir($folder, 0777, true);
        $localImage = $folder.'/'.'GZ_'.$storeName.'('.$tnumber.')'.'.jpg'; //存到本地的图片地址名字:门店名称+桌台号
        $fp = fopen($localImage, 'a');// fopen() 函数打开文件或者 URL
        $ret = fwrite($fp, $img);//内容写入文件
        fclose($fp);//关闭文件
        if($ret){
            return date("Y-m-d").'/'.'GZ_'.$storeName.'('.$tnumber.')'.'.jpg';
        }
        die;
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
