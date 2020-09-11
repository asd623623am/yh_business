<?php
namespace app\admin\controller;


use think\Db;

/**
 * Notes:菜品管理
 * Created by PhpStorm.
 * user: bingwoo
 * date: 2020/8/11  
 * time: 下午 01:38
 */
class Goods extends Common{

    /**
     * @Notes:菜品分类列表
     * @user: bingwoo
     */
    public function goodsTypeList(){

        $storeid = getStoreid();
        if($storeid != 0){
            $where = ['storeid'=>$storeid];
        }
        $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
        $snData = [];
        if(!empty($storeData)){
            $snData = array_column($storeData,'name','storeid');
        }
        $where['status'] = 1;
        if( request() -> isAjax() ){
            $getData = input('get.');
            $page = $getData['page'];
            if(empty($page)){
                exit('非法操作此页面');
            }
            $limit = $getData['limit'];
            if(empty($limit)){
                exit('非法操作此页面');
            }

            if(!empty($getData['gtname'])){
                $where['gtname'] = $getData['gtname'];
            }
            if($storeid == 0){
                if(!empty($getData['storeids'])){
                    $where['storeid'] = $getData['storeids'];
                }
            }else{
                $where['storeid'] = $storeid;
            }
            $data=Db::table("xm_goods_type")->where($where)->page($page,$limit)->select();
            $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
            $snData = [];
            if(!empty($storeData)){
                $snData = array_column($storeData,'name','storeid');
            }
            foreach ($data as &$val){
                $val['storename'] = '';
                if(isset($snData[$val['storeid']])){
                    $val['storename'] = $snData[$val['storeid']];
                }
                $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                $gcount = Db::table("xm_goods")->where(['gtid'=>$val['gtid'],'status'=>1])->count();
                $val['gcount'] = $gcount."个商品";
            }
            unset($val);
            $count=Db::table("xm_goods_type")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            $this->assign('storeid',$storeid);
            $this->assign('storeData',$storeData);
            return view();
        }
    }

    /**
     * @Notes:添加菜品分类
     * @user: bingwoo
     */
    public function goodsTypeAdd(){

        $storeid = getStoreid();
        $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
        if(check()){
            $postData = input('post.');
            $insert = [
                'gtname' => $postData['gtname'],
                'sort' => empty($postData['sort'])?10:$postData['sort'],
                'create_time' => time(),
                'update_time' => time(),
            ];
            if($storeid == 0){
                $insert['storeid'] = $postData['storeid'];
            }else{
                $insert['storeid'] = $storeid;
            }
            $ret = model('goodsType')->save($insert);
            if($ret){
                $this -> addLog('添加商品分类信息');
                win('添加成功');
            }else{
                fail('添加失败');
            }
        }else{
            $this->assign('storeData',$storeData);
            $this->assign('storeidval',$storeid);
            return view();
        }
    }

    /**
     * @Notes:修改菜品分类
     * @user: bingwoo
     */
    public function goodsTypeEdit(){
        $storeid = getStoreid();
        $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                if(isset($postData['gtname'])&&!empty($postData['gtname'])){
                    $where = ['gtid' => $postData['gtid']];
                    $edit['gtname'] = $postData['gtname'];
                    $edit['sort'] = $postData['sort'];
                    $edit['update_time'] = time();
                    if($storeid == 0){
                        $edit['storeid'] = $postData['storeid'];
                    }else{
                        $edit['storeid'] = $storeid;
                    }
                    $res = model('goodsType')->save($edit,$where);
                    if ($res) {
                        $this -> addLog('修改菜品分类信息');
                        win('修改成功');
                    } else {
                        fail('修改失败');
                    }
                }
            }
        }else{
            $gtid=input('get.gtid');
            if(empty($gtid)){
                exit('非法操作此页面');
            }else{
                $where=[
                    'gtid'=>$gtid
                ];
            }
            $gtData = model('goodsType')->field('storeid,gtid,gtname,sort')->where($where)->find();
            $this->assign('gt',$gtData);
            $this->assign('storeData',$storeData);
            $this->assign('storeidval',$storeid);
            return view();
        }
    }

    /**
     * Notes: 删除菜品分类
     * user: bingwoo
     */
    public function goodsTypeDel(){

        $postData = input('post.');
        if(isset($postData['gtid'])&&!empty($postData['gtid'])){
            $count = model('goods')->where(['gtid'=>$postData['gtid'],'status'=>1])->count();
            if($count>0){
                fail('该分类下存在商品，请先删除后在次尝试');
            }
            $where = ['gtid' => $postData['gtid']];
            $ret = model('goodsType')->save(['status'=>2],$where);
            if($ret){
                win($postData['gtname'].'分类删除成功');
            }else{
                fail($postData['gtname'].'分类删失败');
            }
        }else{
            fail('分类信息有误');
        }
    }

    /**
    * Notes: 菜品列表
    * user: bingwoo
    */
    public function goodsList(){

        $storeid = getStoreid();
        $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
        $snData = [];
        if(!empty($storeData)){
            $snData = array_column($storeData,'name','storeid');
        }
        if($storeid != 0){
            $where = ['storeid'=>$storeid];
            $gtWhere = ['storeid'=>$storeid];
        }
        $gtWhere['status'] = 1;
        if( request() -> isAjax() ){
            $getData = input('get.');
            if(!empty($getData['gname'])){
                $where['name'] = $getData['gname'];
            }
            if(!empty($getData['storeids'])){
                $where['storeid'] = $getData['storeids'];
            }
            $gtData = Db::table("xm_goods_type")->where($gtWhere)->select();
            if(empty($gtData)){
                $data = [];
                $count = 0;
            }else{
                if(!empty($getData['gtid'])){
                    $where['gtid'] = $getData['gtid'];
                }else{
                    $gtidArr = array_column($gtData,'gtid');
                    $where['gtid'] = ['in',$gtidArr];
                }
                if($getData['is_grounding']!= null){
                    $where['is_grounding'] = $getData['is_grounding'];
                }
                $where['status'] = 1;
                $data=Db::table("xm_goods")->where($where)->page($getData['page'],$getData['limit'])->order('gid','desc')->select();
                if(!empty($data)){
                    foreach ($data as &$val){

                        $val['storename'] = '';
                        if(isset($snData[$val['storeid']])){
                            $val['storename'] = $snData[$val['storeid']];
                        }
                        $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                        $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
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
                $count=Db::table("xm_goods")->where($where)->count();
            }
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            $gtData = model('goodsType')->where($gtWhere)->select();
            $this->assign('gtData',$gtData);
            $admin = session('admin');
            if($admin['admin_type'] == 3){
                $name = 'shangjia';
            } else {
                $name = 'jichang';
            }
            $this->assign('name',$name);
            $this->assign('storeid',$storeid);
            $this->assign('storeData',$storeData);
            return view();
        }

    }

    /**
     * Notes: 添加菜品
     * user: bingwoo
     */
    public function goodsAdd(){

        $storeid = getStoreid();
        $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
        if(check()){
            $postData = input('post.');
            /* 验证商品必传信息*/
            $insert = [];
            $insert = \app\admin\model\Goods::isVerificationField($postData,$insert);
            $where = [];
            if(!empty($postData)){
                $insert['storeid'] = $postData['storeid'];
                $where['storeid'] = $postData['storeid'];
            }else{
                $insert['storeid'] = $storeid;
                $where['storeid'] = $storeid;
            }
            $where['code'] = $postData['code'];
            $where['status'] = 1;
            $arr = model('goods')->where($where)->select()->toArray();
            if(!empty($arr)){
                fail('菜品编号已使用！');
            }
            $insert['check_time'] = time();
            $insert['create_time'] = time();
            $insert['update_time'] = time();
            $insert = \app\admin\model\Goods::isNoVerificationField($postData,$insert);
            $gid = model('goods')->insertGetId($insert);
            /*@todo 后期规格绑定价格在做处理*/
            $gbsData = [];
            $gbsData['storeid'] = $storeid;
            $gbsData['goodsid'] = $gid;
            $gbsData['gstids'] = $postData['gstids'];
            $gbsData['update_time'] = time();
            $gbsData['create_time'] = time();
            $ret = model('goodsBingSpec')->save($gbsData);
            if($ret){
                $this -> addLog('添加商品信息');
                win('添加成功');
            }else{
                fail('添加失败');
            }
        }else{

            $gtWhere = [];
            $gtWhere['status'] = 1;
            if($storeid != 0){
                $gtWhere['storeid'] = $storeid;
            }
            $gtData = model('goodsType')->where($gtWhere)->select();
            $gstData = model('goodsSpecType')->where(['storeid'=>$storeid])->select();
            $goods = [
                'storeid'=>$storeid
            ];
            $this->assign('goods',$goods);
            $this->assign('gtData',$gtData);
            $this->assign('gstData',$gstData);
            $this->assign('storeData',$storeData);
            $this->assign('storeid',$storeid);
            return view();
        }
    }

    /**
     * Notes: 修改菜品
     * user: bingwoo
     */
    public function goodsEdit(){

        $storeid = getStoreid();
        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                $editData = [];
                $editData = \app\admin\model\Goods::isVerificationField($postData,$editData);
                $editData = \app\admin\model\Goods::isNoVerificationField($postData,$editData);
                $where = ['gid'=>$postData['gid']];
                if(isset($postData['is_special'])){
                    $editData['is_special'] = 1;
                } else {
                    $editData['is_special'] = 0;
                }
                if(isset($postData['is_open_stock'])){
                    $editData['is_open_stock'] = 1;
                } else {
                    $editData['is_open_stock'] = 0;

                }
               $editData['stock'] = $postData['stock'];
 
                $res = model('goods')->save($editData,$where);
                if(!empty($postData['tag2'])){
                    $gbsCount = model('goodsBingSpec')->where(['goodsid'=>$postData['gid'],'storeid'=>$storeid])->count();
                    if($gbsCount>0){
                        $gbsData = [
                            'gstids'=>$postData['tag2'],
                            'update_time'=>time()
                        ];
                        model('goodsBingSpec')->save($gbsData,['goodsid'=>$postData['gid'],'storeid'=>$storeid]);
                    }else{
                        $gbsData = [
                            'storeid'=>$storeid,
                            'goodsid'=>$postData['gid'],
                            'gstids'=>$postData['tag2'],
                            'create_time'=>time(),
                            'update_time'=>time(),
                        ];
                        model('goodsBingSpec')->save($gbsData);
                    }
                }else{
                    model('goodsBingSpec')->where(['goodsid'=>$postData['gid'],'storeid'=>$storeid])->delete();
                }
                if ($res) {
                    $this -> addLog('修改菜品信息');
                    win('修改成功');
                } else {
                    fail('修改失败');
                }
            }
        }
        else{
            $gid=input('get.gid');
            if(empty($gid)){
                exit('非法操作此页面');
            }else{
                $where=[
                    'gid'=>$gid
                ];
            }
            $gData = model('goods')->where($where)->find()->toArray();
            if(empty($gData)){
                fail("菜品信息有误");
            }
            $gtData = model('goodsType')->where(['storeid'=>$gData['storeid'],'status'=>1])->select()->toArray();
            if(empty($gtData)){
                fail("菜品分类信息有误");
            }
            foreach ($gtData as $gtv){
                if($gtv['gtid'] == $gData['gtid']){
                    $gData['gtvalue'] = $gtv['gtname'];
                }
            }
            $gstData = model('goodsSpecType')->where(['storeid'=>$gData['storeid']])->select()->toArray();
            $gsbData = model('goodsBingSpec')->where(['goodsid'=>$gData['gid']])->find();

            $this->assign('gtData',$gtData);
            $this->assign('goods',$gData);
            $this->assign('gstData',$gstData);
            $this->assign('gbsData',$gsbData);
            return view();
        }
    }

    /**
     * Notes: 获取菜品规格分类（注：用于渲染规格默认数据）
     * Class: getGoodsSpecTypeList
     * user: bingwoo
     * date: 2020/8/26 14:11
     */
    public function getGoodsSpecTypeList(){
        $postData = input('post.');
        $gstData = model('goodsSpecType')->where(['storeid'=>$postData['storeid'],'status'=>0])->select()->toArray();
        win($gstData);
    }

    /**
     * Notes: 删除菜品
     * user: bingwoo
     */
    public function goodsDel(){
        $postData = input('post.');
        if(isset($postData['gid'])&&!empty($postData['gid'])){
            $where = ['gid' => $postData['gid']];
            //设置菜品信息为隐藏，不展示
            $ret = model('goods')->save(['status'=>2],$where);
            if($ret){
                win('删除菜品'.$postData['name'].'成功');
            }else{
                fail('删除菜品'.$postData['name'].'失败');
            }
        }else{
            fail('菜品信息有误');
        }
    }

    /**
     * Notes: 批量删除
     * Class: goodsDels
     * user: bingwoo
     * date: 2020/9/4 13:41
     */
    public function goodsDels()
    {
        $postData = input('post.');
        if(!empty($postData)){
            $gid = [];
            foreach($postData['data'] as $k=>$v){
                $gid[] = $v['gid'];
            }
            $delDate = ['status' => 2];
            $where = [
                'gid'   => array('in',$gid)
            ];
            $res = model('goods')->save($delDate,$where);
            if ($res) {
               win('批量删除成功！');
            } else {
                fail('批量删除失败');
            }
        }
        fail('批量删除失败！');
    }

    /**
     * Notes: 上架菜品
     * user: bingwoo
     */
    public function goodsUp(){

        $postData = input('post.');
        if(isset($postData['gid'])&&!empty($postData['gid'])){
            $admin = session('admin');
            if($admin['admin_type'] == 3){
                $saveData = ['is_grounding'=>1];
            } else {
                $saveData = ['is_grounding'=>2];
            }
            $where = ['gid' => $postData['gid']];
            $isS = model('goods')->where($where)->find()->toArray();
            if($isS['is_grounding'] == 2){
                win('此商品已上架');
            }
            //设置菜品信息待审核状态
            $ret = model('goods')->save($saveData,$where);
            if($ret){
                if($admin['admin_type'] == 3){
                    win($postData['name'].'上架申请成功');
                } else {
                    win($postData['name'].'上架成功');
                }
            }else{
                fail($postData['name'].'操作失败');
            }
        }else{
            fail('菜品信息有误');
        }
    }

    public function goodsUps()
    {
        $postData = input('post.');
        if(!empty($postData['data'])){
            $admin = session('admin');
            if($admin['admin_type'] == 3){
                $saveData = ['is_grounding'=>1];
            } else {
                $saveData = ['is_grounding'=>2];
            }

            $gid = [];
            foreach($postData['data'] as $k=>$v){
                if(!empty($v['gid'])){
                    $gid[] = $v['gid'];
                }
            }
            $where = [
                'gid'   => array('in',$gid)
            ];
            $ret = model('goods')->save($saveData,$where);
            if($ret){
                if($admin['admin_type'] == 3){
                    win('批量上架申请成功');
                } else {
                    win('批量上架成功');
                }
                
            }else{
                fail('批量上操作失败');
            }
        }
        fail('菜品信息有误！');
    }

    /**
     * Notes: 下架/拒绝上架菜品
     * user: bingwoo
     */
    public function goodsDown(){

        $postData = input('post.');
        if(isset($postData['gid'])&&!empty($postData['gid'])){
            $where = ['gid' => $postData['gid']];
            //设置菜品信息已下架状态
            $ret = model('goods')->save(['is_grounding'=>0],$where);
            if($ret){
                win($postData['name'].'下架成功');
            }else{
                fail($postData['name'].'下架失败');
            }
        }else{
            fail('菜品信息有误');
        }
    }

    public function goodsDowns()
    {
        $postData = input('post.');
        if(!empty($postData['data'])){
            $gid = [];
            foreach($postData['data'] as $k=>$v){
                if(!empty($v['gid'])){
                    $gid[] = $v['gid'];
                }
            }
            $where = [
                'gid'   => array('in',$gid)
            ];
            //设置菜品信息已下架状态
            $ret = model('goods')->save(['is_grounding'=>0],$where);
            if($ret){
                win('批量下架成功');
            }else{
                fail('批量下架失败');
            }
        }else{
            fail('菜品信息有误');
        }
    }

    /**
     * Notes: 通过菜品上架申请
     * user: bingwoo
     */
    public function goodsPass(){
        $postData = input('post.');
        if(isset($postData['gid'])&&!empty($postData['gid'])){
            $where = ['gid' => $postData['gid']];
            //设置菜品信息已上架状态
            $ret = model('goods')->save(['is_grounding'=>2],$where);
            if($ret){
                win($postData['name'].'上架成功');
            }else{
                fail($postData['name'].'上架失败');
            }
        }else{
            fail('菜品信息有误');
        }
    }

    /**
     * Notes: 盘点菜品库存
     * user: bingwoo
     */
    public function goodsStockList(){

        $storeid = getStoreid();
        $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
        $snData = [];
        if(!empty($storeData)){
            $snData = array_column($storeData,'name','storeid');
        }
        if( request() -> isAjax() ){

            $getData = input('get.');
            $gtwheres['status'] = 1;
            if($storeid != 0 ){
                $where['storeid'] = $storeid;
                $gtwheres['storeid'] = $storeid;
            }else{
                if(!empty($getData['storeids'])){
                    $where['storeid'] = $getData['storeids'];
                }
            }
            $where['status'] = 1;
            if(!empty($getData['gname'])){
                $where['name'] = $getData['gname'];
            }
            $gtData = Db::table("xm_goods_type")->where($gtwheres)->select();
            if(empty($gtData)){
                $data = [];
                $count = 0;
            }else{
                if(!empty($getData['gtid'])){
                    $where['gtid'] = $getData['gtid'];
                }else{
                    $gtidArr = array_column($gtData,'gtid');
                    $where['gtid'] = ['in',$gtidArr];
                }
                $data=Db::table("xm_goods")->where($where)->page($getData['page'],$getData['limit'])->select();
                if(!empty($data)){
                    foreach ($data as &$val){
                        $val['storename'] = '';
                        if(isset($snData[$val['storeid']])){
                            $val['storename'] = $snData[$val['storeid']];
                        }
                        $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                        $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                        $val["check_time"]=date("Y-m-d H:i:s",$val["check_time"]);
                        $val['stock'] = intval($val['stock']);
                        //是否开启库存，0：否1：是
                        if($val['is_open_stock'] == 0){
                            $val['open_stock'] = '<font color="#FF0000">否</font>';
                        }else{
                            $val['open_stock'] = '是';
                        }
                        //是否售罄，0：否1：是
                        if($val['is_selling_fragrance'] == 0){
                            $val['is_selling'] = '否';
                        }else{
                            $val['is_selling'] = '<font color="#FF0000">是</font>';
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
                $count=Db::table("xm_goods")->where($where)->count();
            }
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            if($storeid != 0 ){
                $gtwhere['storeid'] = $storeid;
            }
            $gtwhere['status'] = 1;
            $gtData = model('goodsType')->where($gtwhere)->select();
            $this->assign('gtData',$gtData);
            $this->assign('storeid',$storeid);
            $this->assign('storeData',$storeData);
            return view();
        }
    }

    /**
     * Notes: 修改菜品
     * user: bingwoo
     */
    public function goodsStockEdit(){

        if(check()){
            $postData = input('post.');
            $editData = [
                'stock'=>$postData['stock'],
            ];
            $editData['is_open_stock'] = 0;
            $editData['is_selling_fragrance'] = 0;
            if(isset($postData['is_open_stock'])){
                if($editData['stock'] == 0){
                    $editData['is_selling_fragrance'] = 1;
                }
                $editData['is_open_stock'] = 1;
            }
            $where = ['gid'=>$postData['gid']];
            $res = model('goods')->save($editData,$where);
            if ($res) {
                $this -> addLog('修改菜品信息');
                win('修改成功');
            } else {
                fail('修改失败');
            }
        }else{
            $gid=input('get.gid');
            if(empty($gid)){
                exit('非法操作此页面');
            }else{
                $where=[
                    'gid'=>$gid
                ];
            }
            $gData = model('goods')->where($where)->find()->toArray();
            if(empty($gData)){
                fail("菜品信息有误");
            }
            $gtData = model('goodsType')->where(['storeid'=>$gData['storeid']])->select();
            if(empty($gtData)){
                fail("菜品分类信息有误");
            }
            $this->assign('gtData',$gtData);
            $this->assign('goods',$gData);
            return view();
        }
    }

    /**
     * Notes: 估清菜品库存 注：库存清零
     * user: bingwoo
     */
    public function goodsStockDel(){
        $postData = input('post.');
        if($postData['is_open_stock'] == 0){
            fail("[".$postData['name']."]未开启库存，无法进行估清！");
        }
        $saveData = [
            'is_selling_fragrance' => 1,
            'stock'=>0
        ];
        $where = ['gid'=>$postData['gid']];
        $ret = model('goods')->save($saveData,$where);
        if($ret){
            $this -> addLog("[".$postData['name']."]商品估清");
            win('操作成功');
        }else{
            fail('操作失败');
        }

    }

    /**
     * Notes: 菜品规格列表
     * user: bingwoo
     */
    public function goodsSpecList(){

        $storeid = getStoreid();
        $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
        $snData = [];
        if(!empty($storeData)){
            $snData = array_column($storeData,'name','storeid');
        }
        if( request() -> isAjax() ){
            $getData = input('get.');
            $where = [];
            if($storeid != 0){
                $where['storeid'] = $storeid;
            }else{
                if(!empty($getData['storeids'])){
                    $where['storeid'] = $getData['storeids'];
                }
            }
            if(!empty($getData['gstname'])){
                $where['gstname'] = $getData['gstname'];
            }
            $where['status'] = 0;
            $data=Db::table("xm_goods_spec_type")->where($where)->page($getData['page'],$getData['limit'])->select();
            if(!empty($data)){
                foreach ($data as &$val){
                    $val['storename'] = '';
                    if(isset($snData[$val['storeid']])){
                        $val['storename'] = $snData[$val['storeid']];
                    }
                    $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                    $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                    $gsData = model('goodsSpec')->where(['gstid'=>$val['gstid']])->column('gsname');
                    $val['gsname'] = implode(",",$gsData);
                    $val['ismorename'] = '单选';
                    if($val['is_more'] == 1){
                        $val['ismorename'] = '多选';
                    }
                }
                unset($val);
            }
            $count=Db::table("xm_goods_spec_type")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            $gstData = model('goodsSpecType')->where(['storeid'=>$storeid])->select()->toArray();
            foreach ($gstData as &$gstv){
                $gsData = model('goodsSpec')->where(['gstid'=>$gstv['gstid']])->column('gsname');
                $gstv['$gsData'] = implode(',',$gsData);
            }
            $this->assign('gsData',$gstData);
            $this->assign('storeid',$storeid);
            $this->assign('storeData',$storeData);
            return view();
        }
    }

    /**
     * Notes: 添加规格信息
     * user: bingwoo
     */
    public function goodsSpecAdd(){
        $storeid = getStoreid();
        if(check()){
            $postData = input('post.');
            $insert = [];
            if(empty($postData['gsname']) ){
                fail("规格名称不能为空");
            }
            if(empty($postData['spec_content'])){
                fail("规格内容不能为空");
            }
            //添加商品规格分类信息
            $istData = [
                'gstname'=>$postData['gsname'],
                'create_time'=>time(),
                'update_time'=>time(),
            ];
            if($storeid == 0){
                $istData['storeid'] = $postData['storeid'];
            }else{
                $istData['storeid'] = $storeid;
            }
            if (isset($postData['is_more'])){
                $istData['is_more'] = 1;
            }
            $gstId = model('goodsSpecType')->insertGetId($istData);
            //添加商品规格信息
            $insert['gstid'] = $gstId;
            $insert['create_time'] = time();
            $insert['update_time'] = time();
//            $insert['spec_content'] = $postData['spec_content'];
            //批量添加
            $insertAll = [];
            $Econtent = explode("，",$postData['spec_content']);
            $Zcontent = explode("，",$postData['spec_content']);
            $spec_content = $Econtent;
            if(count($Zcontent)>count($Econtent)){
                $spec_content = $Zcontent;
            }
            foreach ($spec_content as $val){
                $insert['gsname'] = $val;
                $insertAll[] = $insert;
            }
            $ret = model('goodsSpec')->insertAll($insertAll);
            if($ret){
                $this -> addLog('添加规格信息');
                win('添加成功');
            }else{
                fail('添加失败');
            }
        }else{
            $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
            $this->assign('storeData',$storeData);
            $this->assign('storeid',$storeid);
            return view();
        }

    }

    /**
     * Notes: 修改规格信息
     * user: bingwoo
     */
    public function goodsSpecEdit(){

        $storeid = getStoreid();
        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                $editData = [
                    'gstname'=>$postData['gstname'],
                    'update_time'=>time(),
                ];
                $editData['is_more'] = 0;
                if(isset($postData['is_more'])){
                    $editData['is_more'] = 1;
                }
                if($storeid == 0){
                    if(!empty($postData['storeid'])){
                        $editData['storeid'] = $postData['storeid'];
                    }
                }else{
                    $editData['storeid'] = $storeid;
                }
                $where = ['gstid'=>$postData['gstid']];
                //1：修改规格更新时间
                model('goodsSpecType')->save($editData,$where);
                //2：删除分类下规格信息
                model('goodsSpec')->where($where)->delete();
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
                    $insert['gsname'] = $val;
                    $insertAll[] = $insert;
                }
                //3：添加规格信息
                $insgs = model('goodsSpec')->insertAll($insertAll);
                if ($insgs) {
                    $this -> addLog('修改规格信息');
                    win('修改成功');
                } else {
                    fail('修改失败');
                }
            }
        }else{
            $gstid=input('get.gstid');
            if(empty($gstid)){
                exit('非法操作此页面');
            }else{
                $where=[
                    'gstid'=>$gstid
                ];
            }
            $gstData = model('goodsSpecType')->where($where)->find()->toArray();
            if(empty($gstData)){
                fail("规格信息有误");
            }
            $gsData = model('goodsSpec')->where(['gstid'=>$gstid])->column('gsname');
            $gstData['gsname'] = implode(',',$gsData);
            $this->assign('spec',$gstData);
            $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
            $this->assign('storeData',$storeData);
            $this->assign('storeidval',$storeid);
            return view();
        }
    }

    /**
     * Notes: 删除规格信息
     * user: bingwoo
     */
    public function goodsSpecDel(){
        $postData = input('post.');
        if(isset($postData['gstid'])&&!empty($postData['gstid'])){
            $where = ['gstid' => $postData['gstid']];
            $update = [
                'status'    => 1
            ];
            $retGs = model('goodsSpec')->where($where)->setField($update);
            $retGst = model('goodsSpecType')->where($where)->setField($update);
            if($retGs && $retGst){
                win('删除规格'.$postData['gsname'].'成功');
            }else{
                fail('删除规格'.$postData['gsname'].'失败');
            }
        }else{
            fail('规格'.$postData['gstname'].'信息有误');
        }
    }

    /**
     * Notes: 获取门店商品分类
     * Class: getStoreGoodsType
     * user: bingwoo
     * date: 2020/9/4 15:39
     */
    public function getStoreGoodsType(){

        $postData = input('post.');
        $gtData = [];
        if(!empty($postData['storeid'])){
            $where = [];
            $where['status'] = 1;
            $where['storeid'] = $postData['storeid'];
            $gtData = model('goodsType')->where($where)->select()->toArray();
        }
        if(isset($postData['type']) && empty($postData['storeid'])){
            $gtData = model('goodsType')->where(['status'=>1])->select()->toArray();
        }
        $info=['code'=>0,'msg'=>'','data'=>$gtData];
        echo json_encode($info);exit;
    }

    public function excelUps()
    {
        if(check()){
            $postData = input('post.');
            session('storeids',null);
            if(empty($postData['data'])){
                fail('非法请求！请选择门店！');
            }
            session('storeids',$postData['data']); 
            win('OK');
        }else {
            $storeData = model('store')->field('storeid,name')->where(['status'=>1])->select()->toArray();
            $this->assign('storeData',$storeData);
            return view();
        }
        
    }
}
