<?php

namespace app\index\controller;
use app\admin\model\Store;
use think\Controller;
use think\Request;

/**
 * Created: by PhpStorm.
 * package: app\index\controller
 * Class: Goodsspec
 * user: bingwoo
 * date: 2020/10/21 9:51
 */
class Goodsspec extends Controller{

    /**
     * Notes: 获取商品规格
     * Class: getGoodsSpecList
     * user: bingwoo
     * date: 2020/10/21 9:51
     */
    public function getGoodsSpecList(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        //验证字段
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
//        $storeid = getStoreidByKey($getData['access-token']);
        $loginInfo = session($getData['access-token']);
        $storeid = $loginInfo['storeid'];
        $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
        $snData = [];
        if(!empty($storeData)){
            $snData = array_column($storeData,'name','storeid');
        }
        $where = [];
        $where['storeid'] = $storeid;
        if(!empty($getData['gstname'])){
            $where['gstname'] = ['like','%'. $getData['gstname'].'%'];
        }
        $where['status'] = 0;
        $page = 0;
        $limit = 20;
        if(isset($getData['page']) && !empty($getData['page'])){
            $page = $getData['page'];
        }
        if(isset($getData['limit']) && !empty($getData['limit'])){
            $limit = $getData['limit'];
        }
        $data = model('goodsspectype')->where($where)->page($page,$limit)->select();
        if(!empty($data)){
            $data = $data->toArray();
            foreach ($data as &$val){
                $val['storename'] = '';
                if(isset($snData[$val['storeid']])){
                    $val['storename'] = $snData[$val['storeid']];
                }
                $gsData = model('goodsspec')->where(['gstid'=>$val['gstid'],'status'=>0])->column('gsname');
                $val['gsname'] = implode(",",$gsData);
                $val['ismorename'] = '单选';
                if($val['is_more'] == 1){
                    $val['ismorename'] = '多选';
                }
                $val['ismustname'] = '否';
                if($val['is_must'] == 1){
                    $val['ismustname'] = '是';
                }
            }
            unset($val);
        }else{
            $data = [];
        }
        $count=model('goodsspectype')->where($where)->count();
        return successMsg('',$data,$count);
    }

    /**
     * Notes: 添加商品规格
     * Class: addGoodsSpec
     * user: bingwoo
     * date: 2020/10/21 9:52
     */
    public function addGoodsSpec(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gstname','gsname'];
        verifColumn($verifData,$postData);
//        $storeid = getStoreidByKey($postData['access-token']);
        $loginInfo = session($postData['access-token']);
        $storeid = $loginInfo['storeid'];
        //验证规格是否存在
        $where['storeid'] = $storeid;
        $where['gstname'] = $postData['gstname'];
        $where['status'] = 0;
        $count = model('goodsspectype')->where($where)->count();
        if($count > 0){
            return failMsg('规格名称已存在');
        }
        $insert = [];
        //添加商品规格分类信息
        $istData = [
            'gstname'=>$postData['gstname'],
            'create_time'=>time(),
            'update_time'=>time(),
            'storeid'=>$storeid,
        ];
        //多选
        if (isset($postData['is_more'])){
            $istData['is_more'] = 1;
        }
        //必选
        if(isset($postData['is_must'])){
            $istData['is_must'] = 1;
        }
        $gstId = model('goodsspectype')->insertGetId($istData);
        //添加商品规格信息
        $insert['gstid'] = $gstId;
        $insert['create_time'] = time();
        $insert['update_time'] = time();
        //批量添加
        $insertAll = [];
        $Econtent = explode("，",$postData['gsname']);
        $Zcontent = explode(",",$postData['gsname']);
        $spec_content = $Econtent;
        if(count($Zcontent)>count($Econtent)){
            $spec_content = $Zcontent;
        }
        foreach ($spec_content as $val){
            $insert['gsname'] = $val;
            $insertAll[] = $insert;
        }
        $ret = model('goodsspec')->insertAll($insertAll);
        if($ret){
            return successMsg('添加成功');
        }else{
            return failMsg('添加失败');
        }
    }

    /**
     * Notes: 修改商品规格
     * Class: editGoodsSpec
     * user: bingwoo
     * date: 2020/10/21 9:53
     */
    public function editGoodsSpec(){
        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gstid','gstname','gsname'];
        verifColumn($verifData,$postData);
//        getStoreidByKey($postData['access-token']);
        $verifData = model('goodsspectype')->where(['gstname'=>$postData['gstname'],'status'=>0])->field('gstid')->select();
        if(!empty($verifData)){
            $verifData = $verifData->toArray();
            foreach ($verifData as $vv){
                if($vv['gstid'] != $postData['gstid']){
                    return failMsg('该规格名称已存在');
                }
            }
        }
        $editData = [
            'gstname'=>$postData['gstname'],
            'update_time'=>time(),
        ];
        $editData['is_more'] = 0;
        if(isset($postData['is_more'])){
            $editData['is_more'] = 1;
        }
        $editData['is_must'] = 0;
        if(isset($postData['is_must'])){
            $editData['is_must'] = 1;
        }
        $where = ['gstid'=>$postData['gstid']];
        //1：修改规格更新时间
        model('goodsspectype')->save($editData,$where);
        //2：删除规格内容
        $updates = [
            'status' => 1
        ];
        model('goodsspec')->save($updates,$where);
        $insertAll = [];
        $Econtent = explode("，",$postData['gsname']);
        $Zcontent = explode("，",$postData['gsname']);
        $spec_content = $Econtent;
        if(count($Zcontent)>count($Econtent)){
            $spec_content = $Zcontent;
        }
        $insert['gstid'] = $postData['gstid'];
        $insert['create_time'] = time();
        $insert['update_time'] = time();
        foreach ($spec_content as $val){
            $new_where = [
                'gstid' => $postData['gstid'],
                'gsname'  => $val,
            ];
            $res = model('goodsspec')->where($new_where)->find();
            if($res != null){
                $updates = [
                    'status' => 0
                ];
                model('goodsspec')->where($new_where)->setField($updates);
            } else {
                $insert['gsname'] = $val;
                $insertAll[] = $insert;
            }
        }

        if(empty($insertAll)){
            return successMsg('修改成功');
        }
        //3：添加规格信息
        $insgs = model('goodsspec')->insertAll($insertAll);
        if ($insgs) {
            return successMsg('修改成功');
        } else {
            return failMsg('修改失败');
        }
    }

    /**
     * Notes: 删除商品规格
     * Class: delGoodsSpec
     * user: bingwoo
     * date: 2020/10/21 9:53
     */
    public function delGoodsSpec(){
        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gstid'];
        verifColumn($verifData,$postData);
//        getStoreidByKey($postData['access-token']);
        //检检查是否有商品在使用该规格
        $count = model('goodsbingspec')->where('gstids', 'like', '%'.$postData['gstid'].'%')->count();
        if($count>0){
            return failMsg('该规格已绑定商品，请删除绑定后在次尝试');
        }
        $where = ['gstid' => $postData['gstid']];
        $update = [
            'status'    => 1
        ];
        $retGs = model('goodsspec')->where($where)->setField($update);
        $retGst = model('goodsspectype')->where($where)->setField($update);
        if($retGs && $retGst){
            return successMsg('删除成功');
        }else{
            return failMsg('删除失败');
        }
    }



}
