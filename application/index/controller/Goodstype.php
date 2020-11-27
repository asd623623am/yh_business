<?php

namespace app\index\controller;
use think\Controller;
use think\Request;

/**
 * Created: by PhpStorm.
 * package: app\index\controller
 * Class: Goodstype
 * user: bingwoo
 * date: 2020/10/20 11:21
 */
class Goodstype extends Controller{


    /**
     * Notes: 获取菜品分类信息
     * Class: getGoodsTypeList
     * user: bingwoo
     * date: 2020/10/20 11:06
     */
    public function getGoodsTypeList(){
        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
//        $storeid = getStoreidByKey($getData['access-token']);
        $loginInfo = session($getData['access-token']);
        $storeid = $loginInfo['storeid'];
        $gtWhere = [];
        $gtWhere['storeid'] = $storeid;
        if(isset($getData['gtname']) && !empty($getData['gtname'])){
            $gtWhere['gtname'] = $getData['gtname'];
        }
        $goodsData = model('goodstype')->where($gtWhere)->select();
        if($goodsData){
            $goodsData = $goodsData->toArray();
            foreach ($goodsData as &$v){
                $v['update_time']  = date('Y-m-d H:i:s',$v['update_time']);
            }
        }
        return successMsg('返回成功',$goodsData);

    }

    /**
     * Notes: 添加菜品分类
     * Class: addGoodsType
     * user: bingwoo
     * date: 2020/10/20 11:07
     */
    public function addGoodsType(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gtname'];
        verifColumn($verifData,$postData);
//        $storeid = getStoreidByKey($postData['access-token']);
        $loginInfo = session($postData['access-token']);
        $storeid = $loginInfo['storeid'];
        $count = model('goodstype')->where(['gtname'=>$postData['gtname']])->count();
        if($count >0){
            return failMsg('分类名称已存在！');
        }
        $insert = [
            'gtname' => $postData['gtname'],
            'sort' => empty($postData['sort'])?10:$postData['sort'],
            'create_time' => time(),
            'update_time' => time(),
            'storeid' => $storeid,
        ];
        $ret = model('goodstype')->save($insert);
        if($ret){
            return successMsg('添加成功');
        }else{
            return failMsg('添加失败');
        }
    }

    /**
     * Notes: 修改商品分类
     * Class: editGoodsType
     * user: bingwoo
     * date: 2020/10/20 11:07
     */
    public function editGoodsType(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gtid','gtname','sort'];
        verifColumn($verifData,$postData);
//        $storeid = getStoreidByKey($postData['access-token']);
        $loginInfo = session($postData['access-token']);
        $storeid = $loginInfo['storeid'];
        //判断修改后分类分成是否存在
        $gtData = model('goodstype')->where(['gtname'=>$postData['gtname']])->field('gtid')->select();
        if(!empty($gtData)){
            foreach ($gtData as $v){
                if($v['gtid'] != $postData['gtid']){
                    return failMsg('商品名称已存在！');
                }
            }
        }
        $where = ['gtid' => $postData['gtid']];
        $edit['gtname'] = $postData['gtname'];
        $edit['sort'] = $postData['sort'];
        $edit['update_time'] = time();
        $edit['storeid'] = $storeid;
        $res = model('goodstype')->save($edit,$where);
        if ($res) {
            return successMsg('修改成功');
        } else {
            return failMsg('修改失败');
        }
    }

    /**
     * Notes: 删除商品分类
     * Class: delGoodsType
     * user: bingwoo
     * date: 2020/10/20 11:08
     */
    public function delGoodsType(){
        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gtid'];
        verifColumn($verifData,$postData);
//        $storeid = getStoreidByKey($postData['access-token']);
        $loginInfo = session($postData['access-token']);
        $storeid = $loginInfo['storeid'];
        $count = model('goods')->where(['gtid'=>$postData['gtid'],'status'=>1])->count();
        if($count>0){
            return failMsg('该分类下存在商品，请先删除后在次尝试');
        }
        $where = ['gtid' => $postData['gtid']];
        $ret = model('goodstype')->where($where)->delete();
        if($ret){
            return successMsg('删除成功');
        }else{
            return failMsg('删除失败');
        }
    }

}
