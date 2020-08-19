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

        $storeid = 1;
        $where = ['storeid'=>$storeid];
        if( request() -> isAjax() ){
            $page=input('get.page');
            if(empty($page)){
                exit('非法操作此页面');
            }
            $limit=input('get.limit');
            if(empty($limit)){
                exit('非法操作此页面');
            }
            $data=Db::table("xm_goods_type")->where($where)->page($page,$limit)->select();
            foreach ($data as &$val){
                $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                $gcount = Db::table("xm_goods")->where(['gtid'=>$val['gtid']])->count();

                $val['gcount'] = $gcount."个商品";
            }
            unset($val);
            $count=Db::table("xm_goods_type")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            return view();
        }
    }

    /**
     * @Notes:添加菜品分类
     * @user: bingwoo
     */
    public function goodsTypeAdd(){

        $storeid = 1;
        if(check()){
            $postData = input('post.');
            $insert = [
                'storeid' => $storeid,
                'gtname' => $postData['gtname'],
                'sort' => empty($postData['sort'])?10:$postData['sort'],
                'create_time' => time(),
                'update_time' => time(),
            ];
            $ret = model('goodsType')->save($insert);
            if($ret){
                $this -> addLog('添加商品分类信息');
                win('添加成功');
            }else{
                fail('添加失败');
            }
        }else{
            return view();
        }
    }

    /**
     * @Notes:修改菜品分类
     * @user: bingwoo
     */
    public function goodsTypeEdit(){
        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                if(isset($postData['gtname'])&&!empty($postData['gtname'])){
                    $where = ['gtid' => $postData['gtid']];
                    $edit['gtname'] = $postData['gtname'];
                    $edit['sort'] = $postData['sort'];
                    $edit['update_time'] = time();
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
            $gtData = model('goodsType')->field('gtid,gtname,sort')->where($where)->find();
            $this->assign('gt',$gtData);
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
            $count = model('goods')->where(['gtid'=>$postData['gtid']])->count();
            if($count>0){
                fail('该分类下存在商品，请先删除后在次尝试');
            }
            $where = ['gtid' => $postData['gtid']];
            $ret = model('goodsType')->save(['status'=>2],$where);
            if($ret){
                win('删除分类'.$postData['gtname'].'成功');
            }else{
                fail('删除分类'.$postData['gtname'].'失败');
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

        $storeid = 1;
        if( request() -> isAjax() ){
            $where = ['storeid'=>$storeid,'is_show'=>1];
            $getData = input('get.');
            if(!empty($getData['gtid'])){
                $where['gtid'] = $getData['gtid'];
            }
            if(!empty($getData['gname'])){
                $where['name'] = $getData['gname'];
            }
            $data=Db::table("xm_goods")->where($where)->page($getData['page'],$getData['limit'])->select();
            if(!empty($data)){
                foreach ($data as &$val){
                    $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                    $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                    $gtData = Db::table("xm_goods_type")->where(['gtid'=>$val['gtid']])->find();
                    if(!empty($gtData)){
                        $val['gtname'] = $gtData['gtname'];
                    }else{
                        fail('商品列表加载失败');
                    }
                    $val['is_special'] = "否";
                    if($val['is_special'] == 1){
                        $val['is_special'] = "是";
                    }
                    //菜品上架状态 0：已下架1：待审核2：已上架
                    $val['groundin'] = '已下架';
                    if($val['is_grounding'] == 1){
                        $val['groundin'] = '待审核';
                    }elseif ($val['is_grounding'] == 1){
                        $val['groundin'] = '已上架';
                    }
                    //合并显示商品价格
                    $val['price'] = "售价:".$val['selling_price']."原价:".$val['original_price']."会员价:".$val['member_price']."员工价:".$val['staff_price'];
                    //商品图片默认显示第一张
                    if(!empty($val['img'])){
                        $img = explode(',',$val['img']);
                        $val['img'] = $img[0];
                    }
                }
                unset($val);
            }
            $count=Db::table("xm_goods")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            $gtData = model('goodsType')->where(['storeid'=>$storeid])->select();
            if(empty($gtData)){
                fail("菜品分类信息有误");
            }
            $this->assign('gtData',$gtData);
            return view();
        }

    }

    /**
     * Notes: 添加菜品
     * user: bingwoo
     */
    public function goodsAdd(){

        $storeid = 1;
        if(check()){
            $postData = input('post.');
            /* 验证商品必传信息*/
            $insert = [];
            $insert = \app\admin\model\Goods::isVerificationField($postData,$insert);
            $insert['storeid'] = $storeid;
            $insert['check_time'] = time();
            $insert['create_time'] = time();
            $insert = \app\admin\model\Goods::isNoVerificationField($postData,$insert);
            $ret = model('goods')->allowField(true)->save($insert);
            /*@todo 后期规格绑定价格在做处理*/
            if($ret){
                $this -> addLog('添加商品信息');
                win('添加成功');
            }else{
                fail('添加失败');
            }
        }else{
            $gtData = model('goodsType')->where(['storeid'=>$storeid])->select();
            $gsData = model('goodsSpec')->where(['storeid'=>$storeid])->select();
            $this->assign('gtData',$gtData);
            $this->assign('gsData',$gsData);
            return view();
        }
    }

    /**
     * Notes: 修改菜品
     * user: bingwoo
     */
    public function goodsEdit(){

        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                $editData = [];
                \app\admin\model\Goods::isVerificationField($postData,$editData);
                $editData = \app\admin\model\Goods::isNoVerificationField($postData,$editData);
                $where = ['gid'=>$postData['gid']];
                $res = model('goods')->save($editData,$where);
                if ($res) {
                    $this -> addLog('修改菜品信息');
                    win('修改成功');
                } else {
                    fail('修改失败');
                }
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
     * Notes: 删除菜品
     * user: bingwoo
     */
    public function goodsDel(){
        $postData = input('get.');
        if(isset($postData['gid'])&&!empty($postData['gid'])){
            $where = ['gid' => $postData['gid']];
            //设置菜品信息为隐藏，不展示
            $ret = model('goods')->save(['is_show'=>0],$where);
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
     * Notes: 上架菜品
     * user: bingwoo
     */
    public function goodsUp(){

        $postData = input('post.');
        if(isset($postData['gid'])&&!empty($postData['gid'])){
            $where = ['gid' => $postData['gid']];
            $saveData = ['is_grounding'=>1];
            //设置菜品信息待审核状态
            $ret = model('goods')->save($saveData,$where);
            if($ret){
                win($postData['name'].'上架申请成功');
            }else{
                fail($postData['name'].'上架申请失败');
            }
        }else{
            fail('菜品信息有误');
        }
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

        $storeid = 1;
        if( request() -> isAjax() ){
            $where = ['storeid'=>$storeid,'is_show'=>1];
            $getData = input('get.');
            if(!empty($getData['gtid'])){
                $where['gtid'] = $getData['gtid'];
            }
            if(!empty($getData['gname'])){
                $where['name'] = $getData['gname'];
            }
            $data=Db::table("xm_goods")->where($where)->page($getData['page'],$getData['limit'])->select();
            if(!empty($data)){
                foreach ($data as &$val){
                    $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                    $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                    $val["check_time"]=date("Y-m-d H:i:s",$val["check_time"]);
                    $gtData = Db::table("xm_goods_type")->where(['gtid'=>$val['gtid']])->find();
                    if(!empty($gtData)){
                        $val['gtname'] = $gtData['gtname'];
                    }else{
                        fail('商品列表加载失败');
                    }
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
                }
                unset($val);
            }
            $count=Db::table("xm_goods")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            $gtData = model('goodsType')->where(['storeid'=>$storeid])->select();
            if(empty($gtData)){
                fail("菜品分类信息有误");
            }
            $this->assign('gtData',$gtData);
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

        $storeid = 1;
        if( request() -> isAjax() ){
            $where = ['storeid'=>$storeid];
            $getData = input('get.');
            if(!empty($getData['gsname'])){
                $where['gsname'] = $getData['gsname'];
            }
            $data=Db::table("xm_goods_spec")->where($where)->page($getData['page'],$getData['limit'])->select();
            if(!empty($data)){
                foreach ($data as &$val){
                    $val["create_time"]=date("Y-m-d H:i:s",$val["create_time"]);
                    $val["update_time"]=date("Y-m-d H:i:s",$val["update_time"]);
                    $val['ismorename'] = '单选';
                    if($val['is_more'] == 1){
                        $val['ismorename'] = '多选';
                    }
                }
                unset($val);
            }
            $count=Db::table("xm_goods_spec")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
            echo json_encode($info);
            exit;
        }else{
            $gsData = model('goodsSpec')->where(['storeid'=>$storeid])->select();
            if(empty($gsData)){
                fail("规格信息有误");
            }
            $this->assign('gsData',$gsData);
            return view();
        }
    }

    /**
     * Notes: 添加规格信息
     * user: bingwoo
     */
    public function goodsSpecAdd(){
        $storeid = 1;
        if(check()){
            $postData = input('post.');
            $insert = [];
            if(empty($postData['gsname']) ){
                fail("规格名称不能为空");
            }
            if(empty($postData['spec_content'])){
                fail("规格内容不能为空");
            }
            if (isset($postData['is_more'])){
                $insert['is_more'] = 1;
            }
            $insert['storeid'] = $storeid;
            $insert['gsname'] = $postData['gsname'];
            $insert['spec_content'] = $postData['spec_content'];
            $ret = model('goodsSpec')->allowField(true)->save($insert);
            if($ret){
                $this -> addLog('添加规格信息');
                win('添加成功');
            }else{
                fail('添加失败');
            }
        }else{
            return view();
        }

    }

    /**
     * Notes: 修改规格信息
     * user: bingwoo
     */
    public function goodsSpecEdit(){

        if(check()){
            $postData = input('post.');
            if(!empty($postData)){
                $editData = [
                    'gsname'=>$postData['gsname'],
                    'spec_content'=>$postData['spec_content'],
                    'update_time'=>time(),
                ];
                $editData['is_more'] = 0;
                if(isset($postData['is_more'])){
                    $editData['is_more'] = 1;
                }
                $where = ['gsid'=>$postData['gsid']];
                $res = model('goodsSpec')->save($editData,$where);
                if ($res) {
                    $this -> addLog('修改规格信息');
                    win('修改成功');
                } else {
                    fail('修改失败');
                }
            }
        }else{
            $gsid=input('get.gsid');
            if(empty($gsid)){
                exit('非法操作此页面');
            }else{
                $where=[
                    'gsid'=>$gsid
                ];
            }
            $gsData = model('goodsSpec')->where($where)->find()->toArray();
            if(empty($gsData)){
                fail("规格信息有误");
            }
            $this->assign('spec',$gsData);
            return view();
        }
    }

    /**
     * Notes: 删除规格信息
     * user: bingwoo
     */
    public function goodsSpecDel(){
        $postData = input('post.');
        if(isset($postData['gsid'])&&!empty($postData['gsid'])){

            $ret = model('goodsSpec')->where(['gsid' => $postData['gsid']])->delete();
            if($ret){
                win('删除规格'.$postData['gsname'].'成功');
            }else{
                fail('删除规格'.$postData['gsname'].'失败');
            }
        }else{
            fail('规格'.$postData['gsname'].'信息有误');
        }
    }
}
