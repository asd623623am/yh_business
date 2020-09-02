<?php
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;
use think\Db;

/**
 * 上架审核.
 */
class Goodstrial extends Common
{
    public function goodLists()
    {
        if( request() -> isAjax() ){
            $page=input('get.page');
            if(empty($page)){
                exit('非法操作此页面');
            }
            $limit=input('get.limit');
            if(empty($limit)){
                exit('非法操作此页面');
            }
            $input = input();
            $where = [];
            if(!empty($input['store'])){
                $where['storeid']=$input['store'];
            }
            if(!empty($input['gitid'])){
                $where['gtid'] = $input['gitid'];
            }
            if(!empty($input['name'])){
                $where['name'] = $input['name'];
            }
            if($input['is_grounding'] != null){
                $where['is_grounding'] = $input['is_grounding'];
            }
            $where['status'] = 1;
            $data=Db::table("xm_goods")->where($where)->page($page,$limit)->select();
            $temp = [];
            $type = Db::table('xm_goods_type')->where(['status'=>1])->select();
            foreach($data as $k=>$v){
                $img = '';
                if($v['img'] != null){
                    $arr = explode(",",$v['img']);
                    $img = $arr[0];
                } else {
                    $img = 'WechatIMG16.png';
                }
                $gtname = '';
                foreach($type as $kk=>$vv){
                    if($v['gtid'] == $vv['gtid']){
                        $gtname = $vv['gtname'];
                    }
                }
                if($v['is_open_stock'] == 0){
                    $stock = '未开启';
                } else {
                    $stock = $v['stock'];
                }
                $is_grounding = '';
                if($v['is_grounding'] == 0){
                    $is_grounding = '未上架';
                } else if($v['is_grounding'] == 1){
                    $is_grounding = '待审核';
                } else if($v['is_grounding'] == 2){
                    if($v['is_selling_fragrance'] == 0){
                        $is_grounding = '在售';
                    } else {
                        $is_grounding = '售罄';
                    }
                } else if($v['is_grounding'] == 3){
                    $is_grounding = '已拒绝';
                }

                $temp[] = [
                    'gid'   => $v['gid'],
                    'img'   => $img,
                    'name'  => $v['name'],
                    'gtname'    => $gtname,
                    'stock' => $stock,
                    'selling_price'  => $v['selling_price'],
                    'original_price'  => $v['original_price'],
                    'member_price'  => $v['member_price'],
                    'staff_price'  => $v['staff_price'],
                    'is_grounding' => $is_grounding,
                ];
            }
            $count=Db::table("xm_goods")->where($where)->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$temp];
            echo json_encode($info);
            exit;
        }else{
            $data=Db::table("xm_goods_type")->where(['status'=>1])->select();
            $xm_store = Db::table('xm_store')->where(['status'=>1])->select();
            $this->assign('type',$data);
            $this->assign('store',$xm_store);
            return view();
        }
    }

    public function goodstrialUp()
    {
        $input = input();
        if(empty($input['type']) || empty($input['gid'])){
            fail('缺少参数，非法操作！');
        }
        $update = [];
        if($input['type'] == 1){
            $update = [
                'is_grounding'  => 2,
                'update_time'   => time()
            ];
        } else if($input['type'] == 2){
            $update = [
                'is_grounding'  => 0,
                'update_time'   => time()
            ];
        } else if($input['type'] == 3){
            $update = [
                'is_grounding'  => 3,
                'update_time'   => time()
            ];
        }
        $where = [
            'gid'   => $input['gid']
        ];
        $info = Db::table('xm_goods')->where($where)->setField($update);
        if($info){
                $this -> addLog('操作了上架审核');
            win('成功');
        }else{
            fail('失败');
        }
    }

    public function goodstrialUps()
    {
        $input = input();
        if(empty($input['type']) || empty($input['data'])){
            fail('缺少参数，非法操作！');
        }
        $update = [];
        if($input['type'] == 2){
            $update = [
                'is_grounding'  => 2,
                'update_time'   => time()
            ];
        } else if($input['type'] == 3){
            $update = [
                'is_grounding'  => 3,
                'update_time'   => time()
            ];
        }

        $gid = [];
        foreach($input['data'] as $k=>$v){
            $gid[] = $v['gid'];
        }

        $where = [
            'gid'   => array('in',$gid)
        ];
        $res = model('goods')->save($update,$where);
        if($input['type'] ==2){
            if ($res) {
                win('批量通过成功！');
             } else {
                 fail('批量通过失败');
             }
        } else if($input['type'] == 3){
            if ($res) {
                win('批量拒绝成功！');
             } else {
                 fail('批量拒绝失败');
             }
        }
        
    }

    public function goodInfo()
    {
        $data = input();
        if(empty($data)){
            exit('非法操作！');
        }
        $where = [
    		'gid'	=> $data['gid'],
    		'status'	=> 1
    	];

    	$info = Db::table("xm_goods")->where($where)->find();
    	if ($info == null) {
    		exit('没有找到您的数据！');
        }
        $temp = [];
        $xm_store = Db::table("xm_store")->where(['storeid'=>$info['storeid']])->find();
        $sname = '';
        if($xm_store != null){
            $sname = $xm_store['name'];
        }
        $is_grounding = '';
        if($info['is_grounding'] == 0){
            $is_grounding = '已下架';
        } else if($info['is_grounding'] == 1){
            $is_grounding = '待审核';
        } else if($info['is_grounding'] == 2){
            if($info['is_selling_fragrance'] == 0){
                $is_grounding = '在售';
            } else {
                $is_grounding = '售罄';
            }
        }


        $type = Db::table("xm_goods_type")->where(['status'=>1])->select();
        $gtname = '';
        foreach($type as $kk=>$vv){
            if($info['gtid'] == $vv['gtid']){
                $gtname = $vv['gtname'];
            }
        }
        $img = '';
        if($info['img'] != null){
            $arr = explode(",",$info['img']);
            $img = $arr[0];
        } else {
            $img = 'WechatIMG16.png';
        }

        $stock = '';
        if($info['is_open_stock'] == 0){
            $stock = '未开启';
        } else {
            $stock = $info['stock'];
        }

        $is_special = '';
        if($info['is_special'] == 0){
            $is_special = '否';
        } else {
            $is_special = '是';
        }

        $temp = [
            'sname'   => $sname,
            'img'   => $img,
            'name'  => $info['name'],
            'gtname'    => $gtname,
            'stock' => $stock,
            'original_price'    => $info['original_price'],
            'selling_price'     => $info['selling_price'],
            'member_price'      => $info['member_price'],
            'staff_price'       => $info['staff_price'],
            'is_special'        => $is_special,
            'is_grounding' => $is_grounding,
            'trade_description' => $info['trade_description'],
        ];
    	$this->assign('arr',$temp);
    	$this->assign('page',$data['page']);
    	return view();
    }
}