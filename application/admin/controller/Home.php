<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 会员管理
 */
class Home extends Common
{

	public function district()
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
	        $data=Db::table("district")->order('id','asc')->page($page,$limit)->select();
            foreach ($data as &$val)
            {
                $val["ctime"]=date("Y-m-d H:i:s",$val["ctime"]);
            }
            unset($val);
            $count=Db::table("district")->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];
	        echo json_encode($info);
	        exit;

	    }else{
	        return view();
	    }
	}

	public function homeList()
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
	        // ->order('home_id','desc')
	        $home_info = model('Home')->where(['is_delete'=>1])->page($page,$limit)->select()->toArray();
	       $count=model('Home')->where(['is_delete'=>1])->count();
	        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$home_info];
	        echo json_encode($info);
	        exit;

	    }else{
	    	$district=Db::table("district")->field("id,name")->order('id','asc')->select();
	    	$this->assign('districts',$district);
	        return view();
	    }
	}

	public function dataNewList()
	{

	    $data = input();
	    if (empty($data)) {
	        fail('非法操作此页面');
	    }
	    $page = $data['page'];
	    unset($data['page']);
	    $limit = $data['limit'];
	    unset($data['limit']);

	    if (empty($data['district_id'])) {
	    	unset($data['district_id']);
	    }
	    if (empty($data['complex'])) {
	    	unset($data['complex']);
	    }
	    if (empty($data['home_code'])) {
	    	unset($data['home_code']);
	    }
	    if (empty($data['owner'])) {
	    	unset($data['owner']);
	    }
	    if (empty($data['tel'])) {
	    	unset($data['tel']);
	    } else {
	    	$data['tel'] = array('like','%'.$data['tel'].'%');
	    }

	    $data['is_delete'] = 1;
	    // ->order('home_id','desc')
	   $home_info = model('Home')->where($data)->page($page,$limit)->select()->toArray();

	   $count=model('Home')->where($data)->count();
        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$home_info];
        echo json_encode($info);

	}

	//导出
	public function homeupload(){  
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
	    		$arr[] =array_combine($title,$v);
	    	}
	    	$data = [];
	    	foreach ($arr as $key => $value) {
	    		if (!isset($value['小区名称'])) {
	    			return fail('缺少小区名称标题');
	    		}

	    		if (!array_key_exists('楼/单元/户号',$value)) {
	    			return fail('缺少楼/单元/户号标题');
	    		}
	    		if (!array_key_exists('房主姓名',$value)) {
	    			return fail('缺少房主姓名标题');
	    		}

	    		if (!array_key_exists("联系方式",$value))
	    		{
	    		  return fail('缺少联系方式标题');
	    		}
	    		
	    		if (!array_key_exists('面积',$value)) {
	    			return fail('缺少面积标题');
	    		}
	    		if (!array_key_exists('入住时间',$value)) {
	    			return fail('缺少入住时间标题');
	    		}
	    		if (!array_key_exists('区',$value)) {
	    			return fail('缺少区标题');
	    		}

	    		
				$where = [
					'name'	=> $value['小区名称']
				];
				$district = model('District')->where($where)->find();
				if ($district ==null) {
					$insert = [
						'name'	=> $value['小区名称'],
						'ctime'	=> time()
					];
					$res = model('District')->insertGetId($insert);
					if ($res) {
						$id = $res;
					} else {
						fail('导入失败');
					}
				} else {
					$id = $district->id;
				}
	    		$newarr = explode('-', $value['楼/单元/户号']);
	    		
	    		
	    		$count = count($newarr);
	    		if ($count == 3) {
	    			$building = $newarr[0];
	    			$unit = $newarr[1];
	    			$room = $newarr[2];
	    		} else {
	    			$building = $newarr[0];
	    			$unit = '';
	    			$room = $newarr[1];
	    		}

	    		
	    		$data[] = [
	    			'home_ids'		=> date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT),
	    			'district_name'	=> $value['小区名称'],
	    			'complex'		=> $value['区'],
	    			'district_id'	=> $id,
	    			'home_code'		=> $value['楼/单元/户号'],
	    			'owner'		=> $value['房主姓名'],
	    			'tel'		=> $value['联系方式'],
	    			'area'		=> $value['面积'],
	    			'check_in_at' => $value['入住时间'],
	    			'is_delete'	=> 1,
	    			'building' 	=> $building,
	    			'unit' 		=> $unit,
	    			'room' 		=> $room,
	    			'ctime'		=> time()
	    		];
	    	}
	    	$res = model('Home') -> insertAll( $data );

	    	if ($res < 1) {
	    		fail('导入失败');
	    	} else {
	    		win('导入成功');
	    	}

	      } else{ //  不符合类型业务  
	        $this->error('请选择上传3MB内的excel表格文件...');  
	        //echo $file->getError();  
	      }  
	    }else{  
	      $this->error('请选择需要上传的文件...');  
	    }  

	  }  
	}

	/**
	 * 删除.
	 * @return [type] [description]
	 */
	public function homeDel()
	{
		$home_id=input('post.home_id');
		if(empty($home_id)){
		    fail('非法操作此页面');
		}
		$where=[
		    'home_id'=>$home_id
		];
		//删除
		$arr=[
		    'is_delete'=>2
		];
		
		$info = model('Home')->where($where)->setField($arr);
		if($info){
			 $this -> addLog('删除了一个房屋');
		    win('删除成功');
		}else{
		    fail('删除失败');
		}
	}


	/**
	 * 修改.
	 * @return [type] [description]
	 */
	public function homeUpdateInfo()
	{
		$home_id=input('get.home_id');

		if(empty($home_id)){
		    exit('非法操作此页面');
		}
		$where=[
		    'home_id'=>$home_id
		];
		$data=model('Home')->where($where)->find();
		$data['check_in_at'] = $data['check_in_at'];
		$district = model('District')->select()->toArray();

		$page = input('get.page');
		if(empty($page)){
			exit('非法操作此页面');
		}
		$this->assign('page',$page);
		$this->assign('select',$district);
		$this->assign('data',$data);
		return view();
	}


	public function homeUp()
	{
		$data = input();
		if (empty($data)) {
			fail('非法操作此页面');
		}
		$where = [
			'home_id'	=> $data['home_id']
		];


		$arr = explode('-', $data['home_code']);
		$len = count($arr);
		if ($len == 3) {
			$building = $arr[0];
			$unit = $arr[1];
			$room = $arr[2];

		} else {
			$building = $arr[0];
			$unit = '';
			$room = $arr[1];
		}
		$district = model('District')->where(['id'=>$data['dis']])->find();
		if ($district == null) {
			$name = '';
		} else {
			$name = $district->name;
		}
		$insert = [
			'district_id'	=> $data['dis'],
			'district_name'	=> $name,
			'complex'		=> $data['qu'],
			'home_code'		=> $data['home_code'],
			'building'		=> $building,
			'unit'			=> $unit,
			'room'			=> $room,
			'owner'			=> $data['home_name'],
			'tel'			=> $data['tel'],
			'area'			=> $data['area'],
			'check_in_at'   => $data['ctime'],
			'is_delete'		=> 1,
			'content'		=> $data['content'],
		];
		$info = model('Home')->where($where)->setField($insert);
		if($info){
			 $this -> addLog('修改了一个房屋');
		    win('修改成功');
		}else{
		    fail('修改失败');
		}
	}

	/**
	 * 添加.
	 * @return [type] [description]
	 */
	public function homeAdd()
	{
		 if(check()){
            # 检查名字是否重复
            $data=input('post.');
            if(empty($data)){
                exit('非法操作此页面');
            }
            $where = [
            	'district_id'	=> $data['dis'],
            	'complex'		=> $data['qu'],
            	'home_code'		=> $data['home_code']
            ];
            $info = model('Home')->where($where)->find();
            if ($info != null) {

            	fail('已经有此房子，请去修改房屋信息！');

            	if (isset($data['status'])) {

            		$arr = explode('-', $data['home_code']);
            		$len = count($arr);
            		if ($len == 3) {
            			$building = $arr[0];
            			$unit = $arr[1];
            			$room = $arr[2];

            		} else {
            			$building = $arr[0];
            			$unit = '';
            			$room = $arr[1];
            		}

            		$insert = [
            			'home_ids'		=> date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT),
            			'district_id'	=> $data['dis'],
            			'district_name'	=> $data['dname'],
            			'complex'		=> $data['qu'],
            			'home_code'		=> $data['home_code'],
            			'building'		=> $building,
            			'unit'			=> $unit,
            			'room'			=> $room,
            			'owner'			=> $data['home_name'],
            			'tel'			=> $data['tel'],
            			'area'			=> $data['area'],
            			'check_in_at'   => $data['ctime'],
            			'is_delete'		=> 1,
            			'ctime'			=> time(),
            			'content'		=> $data['content'],
            		];


            		$res = model('Home')->allowField(true)->save($insert);
            		if ($res) {
            			 $this -> addLog('添加了一个房屋');
            			win('添加成功');
            		} else {
            			fail('添加失败');
            		}
            	} else {
    			 	echo json_encode(['font' => '您确认添加吗？', 'code' => 3]);
    		        exit;
            	}

            } else {
            	
            	$arr = explode('-', $data['home_code']);
            		$len = count($arr);
            		if ($len == 3) {
            			$building = $arr[0];
            			$unit = $arr[1];
            			$room = $arr[2];

            		} else {
            			$building = $arr[0];
            			$unit = '';
            			$room = $arr[1];
            		}


            	$insert = [
            		'home_ids'		=> date('YmdHis') . str_pad(mt_rand(1, 999999), 5, '0', STR_PAD_LEFT),
        			'district_id'	=> $data['dis'],
        			'district_name'	=> $data['dname'],
        			'complex'		=> $data['qu'],
        			'home_code'		=> $data['home_code'],
        			'building'		=> $building,
        			'unit'			=> $unit,
        			'room'			=> $room,
        			'owner'			=> $data['home_name'],
        			'tel'			=> $data['tel'],
        			'area'			=> $data['area'],
        			'check_in_at'   => $data['ctime'],
        			'is_delete'		=> 1,
        			'ctime'			=> time(),
        			'content'		=> $data['content'],
        		];


        		$res = model('Home')->allowField(true)->save($insert);
            	if($res){
            		 $this -> addLog('添加了一个房屋');
            	    win('添加成功');
            	}else{
            	    fail('添加失败');
            	}	
            }
            
        }else{
        	$district=model('District')->select()->toArray();
            $this->assign('dis',$district);
            return view();
        }
	}


	public function exceAdd()
	{
		if(check()){
		    $data=input('post.');
		    dump($data);exit;
		    if(empty($data)){
		        exit('非法操作此页面');
		    }
		   	$str = json_encode($data);
		   	$url = url('Data/exportExcel');
		    // echo $url;exit;
		    // header("refresh:2;url='sel.php'");
		    header('Location:'.$url.'?data='.$str);
		    /*if($info){
		        win('添加成功');
		    }else{
		        fail('添加失败');
		    }*/
		}else{
            $district=model('District')->select()->toArray();
            $this->assign('dis',$district);
		    return view();
		}
	}

		/**
		 * 导出Excel表
		 * @return [type] [description]
		 */
		public function exportExcel()
		{
				$datas = input();
				
				if (empty($datas)) {
					fail('非法操作此页面');
				}

	           if (!empty($datas['dis']) || !empty($datas['qu'])) {
	           		$hwhere = [];
	           		if (!empty($datas['dis'])) {
	           		    $hwhere['district_id'] = $datas['dis'];
	           		}
	           		if (!empty($datas['qu'])) {
	           		    $hwhere['complex'] = $datas['qu'];
	           		}	
	           } else {
	           	 $hwhere = [];
	           }
			   $homedata = model('Home')->where($hwhere)->select()->toArray();
	           $temp = [];
	           foreach ($homedata as $key => $value) {
	           	$temp[] = [
	           		'district_name'	=> $value['district_name'],
	           		'qu'			=> $value['complex'],
	           		'home_code'		=> $value['home_code'],
	           		'name'			=> $value['owner'],
	           		'tel'			=> $value['tel'],
	           		'area'			=> $value['area'],
	           		'check_in_at'	=> $value['check_in_at'],
	           	];
	           }
	            $xlsData = $temp;
				$fileName = $datas['fileName'];
				   if($fileName==""){$fileName=time();}//如果没有给名称 默认为当前时间戳
				// 模拟获取数据
				// $xlsData = self::getData();
				// $xlsData = $this->dataData();

				Vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
				       Vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
				       Vendor('PHPExcel.PHPExcel.Writer.Excel2007');
				       $objExcel = new \PHPExcel();
				       //set document Property
				       $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');

				       $objActSheet = $objExcel->getActiveSheet();
				       $objActSheet->getStyle('1')->getFont()->setBold(true);
				       $objActSheet->getStyle('2')->getFont()->setBold(true);
				       $objExcel->getActiveSheet()->getStyle(1)->getFont()->setSize(20);
				       // $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->      //设置全局默认的字体大小
				       //   $objExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(20)->setARGB('#FF0000');


				       $objActSheet->setTitle($datas['tableName']);//设置sheet工作表名称
				       $key = ord("A");
				       $letter =explode(',',"A,B,C,D,E,F,G");
				       // $letter =explode(',',"A,B,C,D,E,F,G,H,I,J");
				       // $arrHeader = array('姓名','公司','公司地址','邮箱','电话','职位','行业应用','会员角色','是否验证');
				       $arrHeader = array('小区名称','区','楼号','房主姓名','联系方式','面积','入住时间');
				       //合并单元格
				       $objExcel->getActiveSheet()->mergeCells('A1:P1');

				       //设置水平居中
				       $objExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				       //填充标题
				       		$objActSheet->setCellValue($letter[0].'1',$datas['tableName']);
				       
				       //填充表头信息
				       $lenth =  count($arrHeader);
				       for($i = 0;$i < $lenth;$i++) {
				           $objActSheet->setCellValue("$letter[$i]2","$arrHeader[$i]");

				       };


				       //填充表格信息
				       foreach($xlsData as $k=>$v){
				           $k +=3;
				           $objActSheet->setCellValue('A'.$k,$v['district_name']);
				           $objActSheet->setCellValue('B'.$k, $v['qu']);
				           $objActSheet->setCellValue('C'.$k, $v['home_code']);
				           $objActSheet->setCellValue('D'.$k, $v['name']);
				           $objActSheet->setCellValue('E'.$k, $v['tel']);
				           $objActSheet->setCellValue('F'.$k, $v['area']);
				           $objActSheet->setCellValue('G'.$k, $v['check_in_at']);
				           // $objActSheet->setCellValue('D'.$k, $v['email']);
				           // $objActSheet->setCellValue('E'.$k, $v['tel']);
				           // $objActSheet->setCellValue('F'.$k, $v['position']);
				           // $objActSheet->setCellValue('G'.$k, $v['apphang']);
				           // $objActSheet->setCellValue('H'.$k, $v['role_id'] == 2?'正式会员':'普通会员');
				           // $objActSheet->setCellValue('I'.$k, $v['active'] == 1?'是':'否');
				           // 表格高度
				           $objActSheet->getRowDimension($k)->setRowHeight(20);
				       }

				       $width = array(10,15,20,25,30);
				       //设置表格的宽度
				       $objActSheet->getColumnDimension('A')->setWidth($width[1]);
				       $objActSheet->getColumnDimension('B')->setWidth($width[2]);
				       $objActSheet->getColumnDimension('C')->setWidth($width[3]);
				       $objActSheet->getColumnDimension('D')->setWidth($width[4]);
				       $objActSheet->getColumnDimension('E')->setWidth($width[1]);
				       $objActSheet->getColumnDimension('F')->setWidth($width[1]);
				       $objActSheet->getColumnDimension('G')->setWidth($width[1]);


				       $outfile = $fileName.".xlsx";
				       ob_end_clean();
				       header("Content-Type: application/force-download");
				       header("Content-Type: application/octet-stream");
				       header("Content-Type: application/download");
				       header('Content-Disposition:inline;filename="'.$outfile.'"');
				       header("Content-Transfer-Encoding: binary");
				       header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				       header("Pragma: no-cache");
				       $objWriter->save('php://output');

		}

		public function districtAdd()
		{
		    if(request() -> isPost()&&request()->isAjax())
		    {
		        $data=input('post.');
		        $data["name"]=trim($data["name"]);
		        if ($data["name"])
		        {
		            if (Db::table("district")->where(["name"=>$data["name"]])->value("id"))
		            {
		                $this->ajax_error($data["name"]."已存在!");
		            }
		            $now=time();
		            $data["ctime"]=$now;
		            $data["utime"]=$now;
		            $ret=Db::table('district')->insert($data);
		            if ($ret)
		            {
		                $this->ajax_success();
		            }
		            else
		            {
		                $this->ajax_error(["添加失败"]);
		            }
		        }
		        else
		        {
		            $this->ajax_error("小区名称不能为空");
		        }
		    }
		    else
		    {
		        $this->redirect('district');
		    }

		}


		public function districtDel()
		{
		    if(request() -> isPost()&&request()->isAjax())
		    {
		        $id=input('post.id');
		        if ( $id)
		        {
		            if (!Db::table("district")->where(["id"=> $id])->value("id"))
		            {
		                $this->ajax_error("DELETE FALSE");
		            }
		            $ret=Db::table('district')->delete($id);;
		            if ($ret)
		            {
		                $this->ajax_success();
		            }
		            else
		            {
		                $this->ajax_error(["DELETE FALSE"]);
		            }
		        }
		        else
		        {
		            $this->ajax_error("DELETE FALSE");
		        }
		    }
		    else
		    {
		        $this->redirect('district');
		    }
		}

}