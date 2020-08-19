<?php
namespace app\admin\controller;
use think\image\Exception;
use think\Db;

/**
 * 角色管理
 * Class Role
 * @package app\admin\controller
 */
class Role extends Common{

    public function roleAdd(){

        # 判断是否是post提交数据
        if( request() -> isPost() ){

            $admin=session('admin');

            if($admin['admin_type'] == 1){
                $admin['admin_type'] = 2;
            }
            //如果是超级管理员就直接添加.
            $is_admin = request() ->param('is_admin');
            if ($is_admin == 1) {
                $inserts = [];
                $inserts['role_name'] = request() ->param('role_name');
                $inserts['is_admin'] = $is_admin;
                $inserts['status'] = 1;
                $inserts['ctime'] = time();
                $info=model('Role')->allowField(true)->save($inserts);
                
                if ($info) {
                    $this -> addLog('添加一个新的角色');
                    win('添加成功');
                } else {
                    fail('添加失败');
                }

                exit;
            }


          
            # 检测请求是否合法
            $this -> checkRequest();

            $role_model = model('Role');

            $role_model -> startTrans();


            try{
                # 添加
                # 写入角色表
                $now = time();
                $insert = [];
                $insert['role_name'] = request() ->param('role_name');
                $insert['is_admin'] = $admin['admin_type'];
                $insert['admin_ids'] = $admin['admin_id'];
                $insert['status'] = 1;
                $insert['ctime'] = $now;
                $role_model -> insert( $insert );
                $role_id = $role_model -> getLastInsID();

                if( $role_id < 1 ){
                    throw new Exception('写入角色表失败');
                }

                # 写角色关联的数据
                $post = request() -> param();
                $power = $post['power'];
                $i = 0 ;
                $new = [];
                foreach( $power as $k => $v ){
                    $new[$i]['role_id'] = $role_id;
                    $new[$i]['node_id'] = $v;
                    $i ++;
                }

                $role_node = model('RoleNode');
                $number = $role_node -> insertAll( $new );

                if( $number < 1 ){
                    throw new Exception('写入关联表失败');
                }


                //写入小区权限.
                // if (isset($post['like'])) {
                //     $like = $post['like'];

                //     $ii = 0 ;
                //     $temp = [];
                //     foreach( $like as $k => $v ){
                //         $temp[$ii]['r_id'] = $role_id; //角色
                //         $temp[$ii]['d_id'] = $v; //小区id
                //         $ii ++;
                //     }
                //     dump($temp);exit;
                //     $role_district = model('RoleDistrict');
                //     $num = $role_district -> insertAll( $temp );
                //     if( $num < 1 ){
                //         throw new Exception('写入关联表失败');
                //     }
                // }

                $role_model -> commit();
                $this -> addLog('添加一个新的角色');
                win('添加成功');

            }catch ( \Exception $e ){
                $msg = $e -> getMessage();

                $role_model -> rollback();

                $this -> fail( $msg );

            }


        }else{
            $data = model('District')->select();
            $this->assign('data',$data);
            return $this -> fetch();
        }

    }



    public function roleList(){
         if( request() -> isAjax() ){

            $page=input('get.page');
            if(empty($page)){
                exit('非法操作此页面');
            }
            $limit=input('get.limit');
            if(empty($limit)){
                exit('非法操作此页面');
            }
            $role_info=model('Role')->where(['status'=>1])->page($page,$limit)->select();
            $count=model('Role')->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$role_info];
            echo json_encode($info);
            exit;

         }else{
             return view();
         }
    }

    public function roleDel()
    {
        $role_id=input('post.role_id');
        if(empty($role_id)){
            exit('非法操作此页面');
        }
        $where=[
            'role_id'=>$role_id
        ];
        //删除
        $res=model('Role')->where($where)->delete();
        if($res){
            $this -> addLog('删除了一个角色');
            win('删除成功');
        }else{
            fail('删除失败');
        }
    }


    public function roleUpdateInfo()
    {
        //接受id
        $role_id = input('get.role_id');
        if (empty($role_id)) {
            exit('非法操作此页面');
        }
        $where = [
            'role_id' => $role_id
        ];
        //查询修改的数据
        $data = model('Role')->where($where)->find();
        $wheres = [
            'r_id' => $role_id
        ];
        // $darr = model('RoleDistrict')->where($wheres)->select()->toArray();
        $code = model('RoleNode')->where($where)->select()->toArray();
        $str = '';
        foreach ($code as $key => $value) {
            $str .= $value['node_id'].'|';
        }
        // $strs = '';
        // foreach ($darr as $k => $v) {
        //     $strs .= $v['d_id'].'|';
        // }
        // $dis = model('District')->select();
        // $this->assign('dis',$dis);
        $this->assign('data', $data);
        $this->assign('code',$str);
        // $this->assign('did',$strs);
        $page = input('get.page');
        if(empty($page)){
            exit('非法操作此页面');
        }
        $this->assign('page',$page);
        
        return $this->fetch();
    }

    public function roleUpdate()
    {
        $data = input('post.');

        if (empty($data)) {
            fail('非法操作此页面');
        }
        $role_id = $data['role_id'];
        $rwhere=[
            'role_id'   => $role_id
        ];

        $font = [
            'role_name'  => '',
            'is_admin'   => '',
            'type'       => '',
        ];
        Db::table('role')->where($rwhere)->update($font);
        $admin = session('admin');
        if($admin['admin_type'] == 1){
            $admin['admin_type'] = 2;
        }
        if ($admin['admin_type'] == 1) {
            $fonts = [
                'role_name'  => $data['role_name'],
                'is_admin'   => $data['is_admin'],
                'type'       => 3
            ];
            
            $reslut = Db::table('role')->where($rwhere)->update($fonts);
            if ($reslut) {
                model('RoleNode')->where($rwhere)->delete();
                $rwheres=[
                    'r_id'   => $role_id
                ];
                // model('RoleDistrict')->where($rwheres)->delete();
                $this -> addLog('修改了一个角色');
                win('修改成功');
                exit;
            } else {
                fail('修改失败');
            }
        }


        $role_id = $data['role_id'];
        $rwhere=[
            'role_id'   => $role_id
        ];
        $font = [
            'role_name'  => $data['role_name'],
            'is_admin'   => $admin['admin_type'],
        ];

        $reslut = Db::table('role')->where($rwhere)->update($font);

        if (!isset($data['power'])) {
                fail('请选择系统权限');
            } else {
                $where_rdel=[
                    'role_id'   => $role_id
                ];

                 $role_node = model('RoleNode');
                 $ndata = $role_node->where($where_rdel)->select()->toArray();
                if (empty($ndata)) {
                        #添加权限角色关联表.
                        $power = $data['power'];
                        $i = 0 ;
                        $new = [];
                        foreach( $power as $k => $v ){
                            $new[$i]['role_id'] = $role_id;
                            $new[$i]['node_id'] = $v;
                            $i ++;
                        }
                        $role_node = model('RoleNode');
                        $number = $role_node -> insertAll( $new );
                        if ($number) {
                            $this -> addLog('修改了一个角色');
                            win('修改成功'); 
                                #添加小区角色关联表.
                                $whered = [
                                    'r_id'  => $role_id,
                                ];

                                $ddata=model('RoleDistrict')->where($whered)->select()->toArray();

                                if (empty($ddata)) {
                                            //写入小区权限.
                                            if (isset($data['like'])) {
                                                $like = $data['like'];

                                                $ii = 0 ;
                                                $temp = [];
                                                foreach( $like as $k => $v ){
                                                    $temp[$ii]['r_id'] = $role_id; //角色
                                                    $temp[$ii]['d_id'] = $v; //小区id
                                                    $ii ++;
                                                }

                                                $role_district = model('RoleDistrict');
                                                $num = $role_district -> insertAll( $temp );
                                                if($num){
                                                    $this -> addLog('修改了一个角色');
                                                    win('修改成功');                                   
                                                } else {
                                                    fail('修改失败');
                                                }
                                        }
                                } else {
                                    model('RoleDistrict')->where($whered)->delete();
                                        //写入小区权限.
                                        if (isset($data['like'])) {
                                            $like = $data['like'];

                                            $ii = 0 ;
                                            $temp = [];
                                            foreach( $like as $k => $v ){
                                                $temp[$ii]['r_id'] = $role_id; //角色
                                                $temp[$ii]['d_id'] = $v; //小区id
                                                $ii ++;
                                            }

                                            $role_district = model('RoleDistrict');
                                            $num = $role_district -> insertAll( $temp );
                                            if($num){
                                                $this -> addLog('修改了一个角色');
                                                win('修改成功');                                   
                                            } else {
                                                fail('修改失败');
                                            }
                                    }
                                }



                        } else {
                            fail('修改失败');
                        }


                } else {
                    $res=model('RoleNode')->where($where_rdel)->delete();
                    if ($res) {
                        
                            #添加权限角色关联表.
                            $power = $data['power'];
                            $i = 0 ;
                            $new = [];
                            foreach( $power as $k => $v ){
                                $new[$i]['role_id'] = $role_id;
                                $new[$i]['node_id'] = $v;
                                $i ++;
                            }
                            $role_node = model('RoleNode');
                            $number = $role_node -> insertAll( $new );
                            if ($number) {
                                    
                                    #添加小区角色关联表.
                                    $whered = [
                                        'r_id'  => $role_id,
                                    ];

                                    $ddata=model('RoleDistrict')->where($whered)->select()->toArray();

                                    if (empty($ddata)) {
                                        $this -> addLog('修改了一个角色');
                                                    win('修改成功');exit;
                                                //写入小区权限.
                                                if (isset($data['like'])) {
                                                    $like = $data['like'];

                                                    $ii = 0 ;
                                                    $temp = [];
                                                    foreach( $like as $k => $v ){
                                                        $temp[$ii]['r_id'] = $role_id; //角色
                                                        $temp[$ii]['d_id'] = $v; //小区id
                                                        $ii ++;
                                                    }

                                                    $role_district = model('RoleDistrict');
                                                    $num = $role_district -> insertAll( $temp );
                                                    if($num){
                                                        $this -> addLog('修改了一个角色');
                                                        win('修改成功');                                   
                                                    } else {
                                                        fail('修改失败');
                                                    }
                                            }
                                    } else {
                                        model('RoleDistrict')->where($whered)->delete();
                                            //写入小区权限.
                                            if (isset($data['like'])) {
                                                $like = $data['like'];

                                                $ii = 0 ;
                                                $temp = [];
                                                foreach( $like as $k => $v ){
                                                    $temp[$ii]['r_id'] = $role_id; //角色
                                                    $temp[$ii]['d_id'] = $v; //小区id
                                                    $ii ++;
                                                }

                                                $role_district = model('RoleDistrict');
                                                $num = $role_district -> insertAll( $temp );
                                                if($num){
                                                    $this -> addLog('修改了一个角色');
                                                    win('修改成功');                                   
                                                } else {
                                                    fail('修改失败');
                                                }
                                        }
                                    }



                            } else {
                                fail('修改失败');
                            }


                    } else {
                        fail('修改失败');
                    }

                }
exit;

                 $res=model('RoleNode')->where($where_rdel)->delete();
                 if ($res) {
                        
                    $power = $data['power'];
                    $i = 0 ;
                    $new = [];
                    foreach( $power as $k => $v ){
                        $new[$i]['role_id'] = $role_id;
                        $new[$i]['node_id'] = $v;
                        $i ++;
                    }
                    $role_node = model('RoleNode');
                    $number = $role_node -> insertAll( $new );
                    
                    if ($number) {
                        $whered = [
                            'r_id'  => $role_id,
                        ];
                        model('RoleDistrict')->where($whered)->delete();
                        //写入小区权限.
                        if (isset($data['like'])) {
                            $like = $data['like'];

                            $ii = 0 ;
                            $temp = [];
                            foreach( $like as $k => $v ){
                                $temp[$ii]['r_id'] = $role_id; //角色
                                $temp[$ii]['d_id'] = $v; //小区id
                                $ii ++;
                            }

                            $role_district = model('RoleDistrict');
                            $num = $role_district -> insertAll( $temp );
                        }

                        $this -> addLog('修改了一个角色');
                        win('修改成功');    
                    } else {
                        fail('修改失败');
                    }
                    

                 } else {
                    fail('修改失败');
                 }
            }


            exit;

        if ($reslut) {

            if (!isset($data['power'])) {
                fail('请选择系统权限');
            } else {
                $where_rdel=[
                    'role_id'   => $role_id
                ];

                 $role_node = model('RoleNode');
                 $res=model('RoleNode')->where($where_rdel)->delete();
                 if ($res) {
                        
                    $power = $data['power'];
                    $i = 0 ;
                    $new = [];
                    foreach( $power as $k => $v ){
                        $new[$i]['role_id'] = $role_id;
                        $new[$i]['node_id'] = $v;
                        $i ++;
                    }
                    $role_node = model('RoleNode');
                    $number = $role_node -> insertAll( $new );
                    $this -> addLog('修改了一个角色');
                    win('修改成功');

                 } else {
                    fail('修改失败');
                 }



               
            }

            
        } else {
            fail('修改失败');
        }

         
        dump($data);exit;
    }


    ####获取小区.
    public function rolePowerList()
    {
      $data = model('District')->select();
      $arr = [
        'font'  => 1,
        'data'  => $data
      ];
      return json($arr);
    }
}
