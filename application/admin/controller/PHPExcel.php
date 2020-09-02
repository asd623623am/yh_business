<?php
namespace app\admin\controller;

/**
 * Notes:PHPExcel
 * Created by PhpStorm.
 * user: bingwoo
 * date: 2020/8/11  
 * time: 下午 01:38
 */
class PHPExcel extends Common{


    /**
     * Notes: 下载门店模板
     * Class: downloadModel
     * user: bingwoo
     * date: 2020/9/1 13:55
     */
    public function downloadStoreModel(){

        //系统域名
        $realm_name = 'business.com';
        //文件名称
        $fieldname = '门店模板.xlsx';
        //图片地址
        $url = 'http://'.$realm_name.'/PHPExcel/'.$fieldname;
        win($url);
    }

    public function storeupload()
    {
        if(request()->isPost()){
            $file = request()->file('file');        // 获取表单提交过来的文件  
            $error = $_FILES['file']['error'];  // 如果$_FILES['file']['error']>0,表示文件上传失败  
            if(!$error){  
              $dir = ROOT_PATH . 'public' . DS . 'upload';
           
              // 验证文件并移动到框架应用根目录/public/uploads/ 目录下
              $info = $file->validate(['size'=>3145728,'ext'=>'xls,xlsx,csv'])->rule('uniqid')->move($dir);  
              /*判断是否符合验证*/  
              if($info){    //  符合类型  
                $file_type = $info->getExtension();  
                $filename = $dir. DS .$info->getSaveName();
                // $filename = 'E:\PHPserver\wwwroot\default\aliyun_1805\public\upload\5ee87c7f0c9be.xlsx';
                Vendor("PHPExcel.IOFactory");
                 $extension = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
                if ($extension =='xlsx') {
                    $objReader = new \PHPExcel_Reader_Excel2007();
                    $objExcel = $objReader ->load($filename);
    
                } else if ($extension =='xls') {
    
                    $objReader = new \PHPExcel_Reader_Excel5();
                    $objExcel = $objReader->load($filename);
    
    
                }
    
                $excel_array=$objExcel->getsheet(0)->toArray();   //转换为数组格式
                $title = $excel_array[0];
                array_shift($excel_array);
    
                $arr = [];
                foreach ($excel_array as $k => $v) {
                    if($v[0] != null && $v[1] != null){
                        $arr[] =array_combine($title,$v);	
                    }
                }

                $data = [];
                foreach ($arr as $key => $value) {
                    if (!isset($value['门店名称'])) {
                        return fail('缺少门店名称标题');
                    }
    
                    if (!array_key_exists('门店地址',$value)) {
                        return fail('缺少门店地址标题');
                    }
                    if (!array_key_exists('联系人',$value)) {
                        return fail('缺少联系人标题');
                    }
    
                    if (!array_key_exists("联系电话",$value))
                    {
                      return fail('缺少联系电话标题');
                    }
                    
                    if (!array_key_exists('登录账号',$value)) {
                        return fail('缺少登录账号标题');
                    }
                    if (!array_key_exists('登录密码',$value)) {
                        return fail('缺少登录密码标题');
                    }

                    $where = [
                        'name'	=> $value['门店名称'],
                    ];
                    $info = model('Store')->where($where)->find();
                    if (!empty($info)) {
                        fail('有门店名称已存在了！请仔细检查！');
                    } else {

                        $admin_where = [
                            'admin_name'   => $value['登录账号']
                        ];
                        $res = model('Admin')->where($admin_where)->select()->toArray();
                        if(!empty($res)){
                            fail('登录账号已被用！请仔细检查并更换登录账号！');
                        }

                        
                        $storeInfo = model('Store')->field('storeid')->order('storeid desc')->find();
                        $insert['store_no'] = 100001;
                        if(!empty($storeInfo['storeid'])){
                            $len = strlen($storeInfo['storeid']);
                            $strno = substr(100001,0,6-$len);
                            $storeInfo['storeid'] += 1;
                            $insert['store_no'] = $strno.$storeInfo['storeid'];
                        }
                        $insert['aid'] = 0;
                        $insert['name'] = $value['门店名称'];
                        $insert['address'] = $value['门店地址'];
                        $insert['user_name'] = $value['联系人'];
                        $insert['user_tel'] = $value['联系电话'];
                        $start_business_hours = '';
                        if($value['开始营业时间'] != null){
                            $start_business_hours = strtotime($value['开始营业时间']);
                        }
                        $end_business_hours = '';
                        if($value['结束营业时间'] != null){
                            $end_business_hours = strtotime($value['结束营业时间']);
                        }
                        $insert['start_business_hours'] = $start_business_hours;
                        $insert['end_business_hours'] = $end_business_hours;
                        $insert['account'] = $value['登录账号'];
                        $insert['password'] = $value['登录密码'];
                        $insert['create_time'] = time();
                        $insert['update_time'] = time();
                        $insert['status'] = 1;
                        $store_id = model('Store')->insertGetId($insert);
                        if(!$store_id){
                            fail('全部导入失败，请检查导入内容');
                        }

                        $admin_insert = [
                            'admin_type'    =>3,
                            'admin_name'    =>$value['登录账号'],
                            'admin_pwd'     =>$value['登录密码'],
                            'admin_tel'     =>'',
                            'storeid'       => $store_id
                        ];
                        $model = model('Admin');
                        $model->save($admin_insert);
                        $admin_id = $model -> getLastInsID();

                        if(!$admin_id){
                            fail('全部导入失败，请检查导入内容');
                        }

                        $role_ids = model('Role')->where(['type'=>4])->find()->toArray();
                        $roleinsert = [
                            'admin_id'  => $admin_id,
                            'role_id'   => $role_ids['role_id']
                        ];
                        $adminres = model('AdminRole')->insert($roleinsert);
                        if(!$adminres){
                            fail('全部导入失败，请检查导入内容');
                        }
                    }
                }

                $this->addLog('导入了门店信息');
                win('导入成功');
              } else{ //  不符合类型业务  
                $this->error('请选择上传3MB内的excel表格文件...');  
                //echo $file->getError();  
              }  
            }else{  
              $this->error('请选择需要上传的文件...');  
            }  
    
          } 
    }

}
