<?php
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;


/**
 * 权限
 */
class Power extends Common{

    /**
     * 添加权限节点
     * @return [type] [description]
     */
    public function powerAdd(){

        $model = model('PowerNode');

        if( request() -> isPost() ){
            $insert = request() -> param();
            $insert['ctime'] = time();
            if( $insert['pid'] == '' ){
                $insert['level'] = 1;
            }else{
                $insert['level'] = 2;
            }

            if( $model -> insert( $insert ) ){
                $this -> success();
            }else{
                $this -> fail('添加失败');
            }
        }else{

            # 查询系统现在有的一级菜单
            $where = [
                'pid' => 0 ,
                'level' => 1,
                'status'=>1,
            ];


            $menu = $model -> where( $where ) -> select();
            $this -> assign( 'menu' , $menu );

            return view();
        }
    }

    /**
     * 查询角色节点
     * @return [type] [description]
     */
    public function powerList(){
        if( request() -> isAjax() ){

           $page=input('get.page');
           if(empty($page)){
               exit('非法操作此页面');
           }
           $limit=input('get.limit');
           if(empty($limit)){
               exit('非法操作此页面');
           }
           $role_info=model('PowerNode')->where(['status'=>1])->page($page,$limit)->select();
           $count=model('PowerNode')->where(['status'=>1])->count();
           $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$role_info];
           echo json_encode($info);
           exit;

        }else{
            return view();
        }
    }


    public function powerDel()
    {
        $node_id = input('post.node_id');
        if (empty($node_id)) {
            fail('无法操作');
        }
        $where = [
            'node_id'   => $node_id
        ];
        $reslut = model('PowerNode')->where($where)->delete();
        if($reslut){
            win('删除成功');
        }else{
            fail('删除失败');
        }
    }

    public function powerUpdateInfo(){
        $node_id=input('get.node_id');
        
        if(empty($node_id)){
            exit('非法操作此页面');
        }
        $where=[
            'node_id'=>$node_id
        ];
        $data=model('PowerNode')->where($where)->find();

        # 查询系统现在有的一级菜单
        $where = [
            'pid' => 0 ,
            'level' => 1,
            'status'=>1,
        ];

        $menu = model('PowerNode') -> where( $where ) -> select();
        if($data->pid == 0){
            $data['new_id'] = $data->node_id;
            $data['new_name'] = $data->node_name;
        } else {
            foreach ($menu as $key => $value) {
                if ($data->pid == $value->node_id) {
                    $data['new_id'] = $value->node_id;
                    $data['new_name'] = $value->node_name;
                }
            }
        }
        
        $this -> assign( 'menu' , $menu );

        $this->assign('data',$data);

        return view();

    }

    public function powerUpdate()
    {
        $data = input('post.');
        if( $data['pid'] == '' ){
            $data['level'] = 1;
            $data['pid'] = 0;
        }else{
            $data['level'] = 2;
        }
        $where=[
            'node_id'=>$data['node_id']
        ];
        $arr=[
            'node_name' =>$data['node_name'],
            'node_url'  =>$data['node_url'],
            'pid'       =>$data['pid'],
            'status'    => $data['status'],
            'level'    => $data['level'],
        ];
        $info = model('PowerNode')->where($where)->update($arr);
        if($info){
            win('修改成功');
        }else{
            fail('修改失败');
        }
    }
}