<?php
namespace app\admin\controller;
use think\Controller;
class Common extends Controller
{
    const STATUS_SUCCESS=0;
    const STATUS_FAILE=-1;

    # 不需要检查权限的控制器 【都需要小写】
    private $no_power_check = [
        '/login/login',
        '/index/index',
        '/login/logout'
    ];


    function _initialize(){
        if(!session("?admin")){
            // $this->jumpError('请先登录',url('Login/login'));
            $this -> redirect('login/login');
        }

        ################## 根据管理员的类型 ，获取管理员的权限
        $admin_info = session('admin');

        if( $admin_info['admin_type'] == 1 ){
            $new = $this -> _getAllMenu();
        }else{
            $new = $this -> _getAdminMenu( $admin_info );
        }
        ################## 根据管理员的类型 ，获取管理员的权限
        $menu=[];
        foreach ($new['menu'] as $v)
        {
            if (isset($v["node_id"]))
            {
                $menu[$v["node_id"]]=$v;
            }

        }
        $img = model('System')->find();

        //区
        $area = [
            '甲',
            '乙',
            '丙',
            '丁',
            '四期',
            '四期商业',
        ];
        $this->assign('area',$area);
        $this -> assign('images',$img['company_logo']);
        $this -> assign('AllMenu' , $menu );

        ##### admin_type 等于1 表示是超级管理员，超级管理员不收权限控制
        /*if( $admin_info['admin_type'] != 1 ){
            # 判断用户是否有权限访问当前的控制器和方法
            $power_list = $new['power_list'];
            
            #获取当前的控制器和方法
            $controller = request() -> controller();

            $action = request() -> action();

            $access = strtolower( '/'  . $controller . '/'.$action);
                
            # 判断是否 是 不需要检查权限的控制器和方法
            if(  !in_array( $access , $this -> no_power_check ) ){
                if( !in_array( $access , $power_list ) ){
                    echo $this -> fetch('public/nopower');
                    exit;
                }
            }
        }*/

    }

    /**
     * h获取管理员对应的权限数据
     * @param $admin_info
     */
    public function _getAdminMenu( $admin_info ){

        # 为了防止每次都查询数据库，把后台权限放到session，下一次使用的时候直接从session读取
        if( !session('?power') ) {
//            echo 'sql';

            # 根据管理员的id 获取管理员对应的角色id
            # 根据角色id查询 角色和权限节点的管理表、
            # 根据权限id查询对应的权限
            $sql = 'SELECT
                admin_id,
                role_name,
                pn.*
            FROM
                admin_role ar
            LEFT JOIN role r ON r.role_id = ar.role_id
            LEFT JOIN role_node rn ON rn.role_id = ar.role_id
            LEFT JOIN power_node pn ON pn.node_id = rn.node_id
            WHERE
                admin_id = ' . $admin_info['admin_id'];

            $menu = model('admin')->query($sql);
            
            // dump($menu);exit;

            if (!empty($menu)) {
                $new = [];
                $power_list = [];
                foreach ($menu as $key => $value) {
                    $power_list[] = strtolower($value['node_url']);
                    if ($value['level'] == 1) {
                        $new[$value['node_id']] = $value;
                    } else {
                        $new[$value['pid']]['son'][] = $value;
                    }
                }
            } else {
                $new = [];
            }

            $roles = model('admin_role')->where(['admin_id'=>$admin_info['admin_id']])->find()->toArray();
            $ro =model('Role')->where(['role_id'=>$roles['role_id']])->find()->toArray();

            $temp = [];
            #判断管理员 是有物业权限还是供暖权限
            foreach ($new as $k => $v) {
                if ($v['node_name'] == '缴费创建') {
                    if ($ro['type'] == 1) {
                        foreach ($v['son'] as $key => $val) {
                            if ($val['node_name'] == '创建物业收费单') {
                                $temp[] = $val;
                            }
                            if ($val['node_name'] == '物业收费单列表') {
                                $temp[] = $val;
                            }
                        }
                    } else if ($ro['type'] == 2) {
                        foreach ($v['son'] as $key => $val) {
                            if ($val['node_name'] == '创建供暖收费单') {
                                $temp[] = $val;
                            }
                            if ($val['node_name'] == '供暖收费单列表') {
                                $temp[] = $val;
                            }
                        }
                        
                    } else {
                        $temp = $v['son'];
                    }

                    $new[$k]['son'] = $temp;
                }
            }
            
            $return = [
                'menu' => $new,
                'power_list' => $power_list
            ];
            # 把用户的左侧菜单和权限列表存入session
            session( 'power' , $return );

            return $return;
        }else{
//            echo 'session';
            return session('power');
        }

    }

    /**
     * 获取所有的权限节点
     * @return array
     */
    private function _getAllMenu(){
        ################################### 获取系统所有的权限节点######################


        # 为了防止每次都查询数据库，把后台权限放到session，下一次使用的时候直接从session读取
        if( !session('?power')||1){

//            echo 'sql';

            # 获取左侧菜单列表
            $menu_model = model('PowerNode');

            $where = [
                'status' => 1
            ];


            $menu_obj = $menu_model -> where( $where ) ->order("level asc")-> select();
            $menu = collection( $menu_obj ) -> toArray();
 
            if( !empty( $menu) ){
                $new = [];
                $power_list = [];
                foreach( $menu as $key => $value ){
                    $power_list[] =  strtolower($value['node_url']);
                    if( $value['level'] == 1 ){
                        $new[$value['node_id']] = $value;
                    }else{
                        $new[$value['pid']]['son'][] = $value;
                    }
                }
            }else{
                $new = [];
            }
            $return = [
                'menu' => $new ,
                'power_list' => $power_list
            ];
            $res = $return['menu'];
            $temp = [];
            foreach ($res as $k => $v) {
                if ($v['node_name'] == '基本情况') {
                    $temp['0'] = $v;
                }
                if ($v['node_name'] == '房屋管理') {
                    $temp['1'] = $v;
                    $son = $temp[1]['son'];
                    $key = array_column($son, 'node_id');
                    array_multisort($key, SORT_ASC, $temp[1]['son']);

                }
                if ($v['node_name'] == '缴费创建') {
                    $temp['2'] = $v;
                     $son = $temp[2]['son'];
                     $sontemp = [];
                     foreach ($son as $kk => $vv) {
                         if ($vv['node_name'] == '创建物业收费单') {
                            $sontemp[0] = $vv;
                        }
                        if ($vv['node_name'] == '物业收费单列表') {
                            $sontemp[1] = $vv;
                        }
                        if ($vv['node_name'] == '创建供暖收费单') {
                            $sontemp[2] = $vv;
                        }
                        if ($vv['node_name'] == '供暖收费单列表') {
                            $sontemp[3] = $vv;
                        }
                        if ($vv['node_name'] == '缴费订单列表') {
                            $sontemp[4] = $vv;
                        }
                     }
                     ksort($sontemp);
                     $temp[2]['son'] = $sontemp;
                    // $key = array_column($son, 'node_name');
                    // array_multisort($key, SORT_ASC, $temp[1]['son']);

                }
                 if ($v['node_name'] == '缴费订单') {
                    $temp['3'] = $v;
                }
                if ($v['node_name'] == '押金管理') {
                    $temp['4'] = $v;
                    $sontemp = [];
                    $son = $temp[4]['son'];
                    foreach ($son as $kk => $vv) {
                        if ($vv['node_name'] == '创建押金订单') {
                            $sontemp[0] = $vv;
                        }
                        if ($vv['node_name'] == '押金缴费') {
                            $sontemp[1] = $vv;
                        }
                        if ($vv['node_name'] == '押金退款') {
                            $sontemp[2] = $vv;
                        }
                    }
                    ksort($sontemp);
                    $temp[4]['son'] = $sontemp;
                }
                if ($v['node_name'] == '数据报表') {
                    $temp['5'] = $v;
                }
//                if ($v['node_name'] == '内容管理') {
//                    $temp['6'] = $v;
//                }
//                if ($v['node_name'] == '物业客服') {
//                    $temp['7'] = $v;
//                }
//                if ($v['node_name'] == '管理员管理') {
//                    $temp['8'] = $v;
//                }
//                if ($v['node_name'] == '角色管理') {
//                    $temp['9'] = $v;
//                }
//                if ($v['node_name'] == '反馈记录') {
//                    $temp['10'] = $v;
//                }
//                if ($v['node_name'] == '权限管理') {
//                    $temp['11'] = $v;
//                     $son = $temp[11]['son'];
//                    $key = array_column($son, 'node_id');
//                    array_multisort($key, SORT_ASC, $temp[11]['son']);
//                }
               if ($v['node_name'] == '系统管理') {
                   $temp['12'] = $v;
               }
//                if($v['node_name'] == '权限节点管理'){
//                    $temp['13'] = $v;
//                }
                if($v['node_name'] == '商户基础信息'){
                    $temp['14'] = $v;
                }
                if($v['node_name'] == '门店管理'){
                    $temp['15'] = $v;
                }
            }
            ksort($temp);
            $return['menu'] = $temp;
            # 把用户的左侧菜单和权限列表存入session
            session( 'power' , $return );

            return $return;
        }else{
//            echo 'session';
            return session('power');
        }
        ################################### 获取系统所有的权限节点######################
    }


    /**
     * 失败时候的返回
     */
    protected function fail( $msg = 'fail' , $status = 1 , $data = [] ){

        $arr = [
            'status' => $status,
            'msg' =>$msg,
            'data' => $data
        ];

        echo json_encode( $arr );
        exit;

    }

    /**
     * 失败时候的返回
     */
    protected function success( $data = [] , $status = 1000 , $msg = 'success' ){

        $arr = [
            'status' => $status,
            'msg' =>$msg,
            'data' => $data
        ];

        echo json_encode( $arr );
        exit;

    }

    public function checkRequest(){
        if(  ! request() -> isAjax() && !request() ->isPost() ){
            $this -> fail('非法请求');
        }
    }
    protected function ajax_success($data=[],$code=self::STATUS_SUCCESS,$msg='操作成功')
    {
        $ret = [
            'code' => $code,
            'msg' =>$msg,
            'data' => $data
        ];
        exit( json_encode($ret));
    }
    protected function ajax_error($msg,$code=self::STATUS_FAILE,$data=[])
    {
        $ret = [
            'code' => $code,
            'msg' =>$msg,
            'data' => $data
        ];
        exit( json_encode($ret));
    }
    protected function layui_success($data,$count,$code=self::STATUS_SUCCESS,$msg='操作成功')
    {
        $ret = [
            'code' => $code,
            'count'=>$count,
            'msg' =>$msg,
            'data' => $data
        ];
        exit( json_encode($ret));
    }
    protected function debug_data($data)
    {
        echo "<pre>";
        if (is_array($data))
        {
            print_r($data);
        }
        else
        {
            echo $data;
        }
        die;
    }
    protected function getOperator()
    {
        $admin=session('admin');
        return $admin["admin_id"];
    }
    protected function getOrderSn()
    {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    public function addLog($str)
    {
        $user = session('admin');
        $insert = [
            'name'  => $user['admin_name'],
            'content'   => $str,
            'ctime'     => time()
        ];
        $info = model('Log')->allowField(true)->save($insert);
    }

}
