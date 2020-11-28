<?php

namespace app\index\controller;
use app\admin\controller\PHPExcel;
use think\Controller;
use think\Request;

class Goods extends Controller{

    /**
     * Notes: 菜品列表
     * user: bingwoo
     */
    public function getGoodsList(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        $verifData = ['access-token'];
        verifColumn($verifData,$getData);
        $storeid = getStoreidByKey($getData['access-token']);
//        $loginInfo = session($getData['access-token']);
//        $storeid = $loginInfo['storeid'];
        $where['storeid'] = $storeid;
        $where['status'] = 1;
        //菜品名称、编号、菜品分类、菜品状态搜索
        if(isset($getData['searchname']) && !empty($getData['searchname'])){
            $where['name|code'] = ['like','%'.$getData['searchname'].'%'];
        }
        if(isset($getData['gtid']) && !empty($getData['gtid'])){
            $where['gtid'] = $getData['gtid'];
        }
        if(isset($getData['is_grounding']) && !empty($getData['is_grounding'])){
            $where['is_grounding'] = $getData['is_grounding'];
        }
        //分页
        $page = 0;
        $limit = 20;
        if(isset($getData['page']) && !empty($getData['page'])){
            $page = $getData['page'];
        }
        if(isset($getData['limit']) && !empty($getData['limit'])){
            $limit = $getData['limit'];
        }
        $data = model('goods')->where($where)->page($page,$limit)->order('gid','desc')->select();
        if(!empty($data)){
            //查询商品分类信息，并转化为key-value
            $gtData = model('goodstype')->where(['storeid'=>$storeid,'status'=>1])->field('gtid,gtname')->select()->toArray();
            $gtkvData = [];
            foreach ($gtData as $gtk => $gtv){
                $gtkvData[$gtv['gtid']] = $gtv['gtname'];
            }
            $data = $data->toArray();
            foreach ($data as &$val){

                //获取分类名称
                $val['gtname'] = isset($gtkvData[$val['gtid']])??'';
                //是否特色0：否1：是
                if($val['is_special'] == 0){
                    $val['is_special'] = "否";
                } else if($val['is_special'] == 1){
                    $val['is_special'] = "是";
                }
                //菜品上架状态 0：已下架1：待审核2：已上架
                $val['groundin'] = '已创建';
                if($val['is_grounding'] == 0){
                    $val['groundin'] = '已下架';
                }elseif ($val['is_grounding'] == 1){
                    $val['groundin'] = '待审核';
                } else if($val['is_grounding'] == 2){
                    $val['groundin'] = '在售';
                } else if($val['is_grounding'] == 3){
                    $val['groundin'] = '已拒绝';
                } else if($val['is_grounding'] == 4){
                    $val['groundin'] = '已创建';
                }
                //菜品状态
                if($val['status'] == 2){
                    $val['status'] = '已删除';
                } else {
                    $val['status'] = '正常';
                }
                //商品图片默认显示第一张
                if(!empty($val['img'])){
                    $img = explode(',',$val['img']);
                    $val['img'] = $img[0];
                }
                $val['gtname'] = '';
                foreach ($gtData as $gtval){
                    if($gtval['gtid'] == $val['gtid']){
                        $val['gtname'] = $gtval['gtname'];
                    }
                }
            }
            unset($val);
        }
        $count = model('goods')->where($where)->count();
        return successMsg('',$data,$count);

    }

    /**
     * Notes: 获取菜品详情
     * Class: getGoodsInfo
     * user: bingwoo
     * date: 2020/10/23 18:28
     */
    public function getGoodsInfo(){

        if(Request::instance()->isGet() == false){
            return failMsg('请求方式有误');
        }
        $getData = input('get.');
        $verifData = ['access-token','gid'];
        verifColumn($verifData,$getData);
//        getStoreidByKey($getData['access-token']);
        $gData = model('goods')->where(['gid'=>$getData['gid']])->find();
        if(empty($gData)){
            return failMsg('商品信息有误');
        }
        $gData = $gData->toArray();
        return successMsg('',$gData);

    }

    /**
     * Notes: 提交菜品
     * Class: addGoods
     * user: bingwoo
     * date: 2020/10/23 12:02
     */
    public function addGoods(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        $verifData = ['access-token','gtid','name','code','selling_price'];
        verifColumn($verifData,$postData);
        $storeid = getStoreidByKey($postData['access-token']);
//        $loginInfo = session($postData['access-token']);
//        $storeid = $loginInfo['storeid'];
        $countName = model('goods')->where(['name'=>$postData['name'],'status'=>1 ])->count();
        if($countName >0){
            return failMsg('该商品名称已存在');
        }
        $countCode = model('goods')->where(['code'=>$postData['code'],'status'=>1 ])->count();
        if($countCode >0){
            return failMsg('该商品编号已存在');
        }
        /* 验证商品必传信息*/
        $insert = [];
        $insert = \app\index\model\Goods::isVerificationField($postData,$insert);
        $insert['storeid'] = $storeid;
        $insert['check_time'] = time();
        $insert['create_time'] = time();
        $insert['update_time'] = time();
        $insert = \app\index\model\Goods::isNoVerificationField($postData,$insert);
        $gid = model('goods')->insertGetId($insert);
        /*@todo 后期规格绑定价格在做处理*/
        if(isset($postData['gstids']) && !empty($postData['gstids'])){
            $gbsData = [];
            $gbsData['storeid'] = $storeid;
            $gbsData['goodsid'] = $gid;
            $gbsData['gstids'] = $postData['gstids'];
            $gbsData['update_time'] = time();
            $gbsData['create_time'] = time();
            model('goodsBingSpec')->save($gbsData);
        }
        if($gid){
            return successMsg('添加成功');
        }else{
            return failMsg('添加失败');
        }
    }

    /**
     * Notes: 修改菜品
     * user: bingwoo
     */
    public function editGoods(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        $verifData = ['access-token','gid','gtid','name','code','selling_price'];
        verifColumn($verifData,$postData);
        $storeid = getStoreidByKey($postData['access-token']);
//        $loginInfo = session($postData['access-token']);
//        $storeid = $loginInfo['storeid'];
        $countName = model('goods')->where(['name'=>$postData['name'],'status'=>1])
            ->where('gid','<>',$postData['gid'])->count();
        if($countName>0){
            return failMsg('该商品名称已存在');
        }
        $countCode = model('goods')->where(['code'=>$postData['code'],'status'=>1 ])
            ->where('gid','<>',$postData['gid'])->count();
        if($countCode >0){
            return failMsg('该商品编号已存在');
        }
        $editData = [];
        $editData = \app\admin\model\Goods::isVerificationField($postData,$editData);
        $editData = \app\admin\model\Goods::isNoVerificationField($postData,$editData);
        $where = ['gid'=>$postData['gid']];
        $res = model('goods')->save($editData,$where);
        if(!empty($postData['gstids'])){
            $gbsCount = model('goodsbingspec')->where(['goodsid'=>$postData['gid'],'storeid'=>$storeid])->count();
            if($gbsCount>0){
                $gbsData = [
                    'gstids'=>$postData['gstids'],
                    'update_time'=>time()
                ];
                model('goodsbingspec')->save($gbsData,['goodsid'=>$postData['gid'],'storeid'=>$storeid]);
            }else{
                $gbsData = [
                    'storeid'=>$storeid,
                    'goodsid'=>$postData['gid'],
                    'gstids'=>$postData['gstids'],
                    'create_time'=>time(),
                    'update_time'=>time(),
                ];
                model('goodsbingspec')->save($gbsData);
            }
        }else{
            model('goodsbingspec')->where(['goodsid'=>$postData['gid'],'storeid'=>$storeid])->delete();
        }
        if ($res) {
            return successMsg('修改成功');
        } else {
            return failMsg('修改失败');
        }
    }

    /**
     * Notes: 删除菜品
     * user: bingwoo
     */
    public function delGoods(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        $verifData = ['access-token','gid'];
        verifColumn($verifData,$postData);
//        getStoreidByKey($postData['access-token']);
        $where = ['gid' => $postData['gid']];
        //设置菜品信息为隐藏，不展示
        $ret = model('goods')->save(['status'=>2],$where);
        if($ret){
            return successMsg('删除成功');
        }else{
            return failMsg('删除失败');
        }
    }

    /**
     * Notes: 菜品上架/下架
     * Class: groundGoods
     * user: bingwoo
     * date: 2020/10/21 11:22
     */
    public function groundGoods(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gid','is_grounding'];
        verifColumn($verifData,$postData);
//        getStoreidByKey($postData['access-token']);
        if(!in_array($postData['is_grounding'],[0,1])){
            return failMsg('字段【'.'is_grounding'.'】传值有误！');
        }
        $saveData = ['is_grounding'=>$postData['is_grounding']];
        //设置菜品信息待审核状态
        $where = ['gid' => $postData['gid']];
        $ret = model('goods')->save($saveData,$where);
        if($ret){
            if($postData['is_grounding'] == 0){
                return successMsg('下架成功');
            }
            return successMsg('上架申请成功');
        }else{
            return failMsg('操作失败');
        }
    }

    /**
     * Notes: 修改商品库存
     * Class: editGoodsStock
     * user: bingwoo
     * date: 2020/10/21 12:01
     */
    public function editGoodsStock(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gid','is_open_stock','stock'];
        verifColumn($verifData,$postData);
//        getStoreidByKey($postData['access-token']);
        if($postData['stock'] == ''){
            $postData['stock'] = 0;
        }
        $editData = [
            'stock'=>$postData['stock'],
        ];
        $editData['is_open_stock'] = $postData['is_open_stock'];
        $editData['is_selling_fragrance'] = 0;
        //如果开启库存，库存清零后自动售罄
        if($editData['stock'] == 0){
            $editData['is_selling_fragrance'] = 1;
        }
        $where = ['gid'=>$postData['gid']];
        $res = model('goods')->save($editData,$where);
        if ($res) {
            return successMsg('修改成功');
        } else {
            return failMsg('修改失败');
        }

    }

    /**
     * Notes: 估清
     * Class: clearStock
     * user: bingwoo
     * date: 2020/10/23 18:12
     */
    public function clearStock(){

        if(Request::instance()->isPost() == false){
            return failMsg('请求方式有误');
        }
        $postData = input('post.');
        //验证字段
        $verifData = ['access-token','gid'];
        verifColumn($verifData,$postData);
//        getStoreidByKey($postData['access-token']);
        $gData = model('goods')->where(['gid'=>$postData['gid']])->field('is_open_stock')->find();
        if(empty($gData)){
            return failMsg('商品信息有误');
        }
        //如果开启库存，库存清零后自动售罄
        $editData['stock'] = 0;
        if($gData['is_open_stock'] == 1){
            $editData['is_selling_fragrance'] = 1;
        }
        $where = ['gid'=>$postData['gid']];
        $res = model('goods')->save($editData,$where);
        if ($res) {
            return successMsg('操作成功');
        } else {
            return failMsg('操作失败');
        }


    }


}
