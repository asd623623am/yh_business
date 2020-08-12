<?php
namespace  app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;
use think\Db;
use PHPExcel_IOFactory;
use PHPExcel;

/**
 * 数据报表.
 */
class Data extends Common
{
	public function dataList()
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

            /*[
                'user'  => '东杰', //姓名
                'area'  => '甲',     //区
                'area_num'  => '1-1-101',   //区号
                'volume'    => '580',   //面积
                'deposit_num' => '1', //应收人数
                'deposit_money' => '6800', //缴费金额
                'year'      => '2020', //缴费年
                'month'     => '06', //缴费月
                'voucher'   => '001', //凭证号
                'net_deposit_num'   => '1', //实收人数
                'net_money' => '6800', //实收金额
                'compensate'=> '', //赔偿
                'owe_num'   => '0', //欠费人
                'owe_money' => '0.0', //欠费金额
                'ctime'     => '04.10.8', //入住时间
                'pay_time'  => '1月1', //缴费日期
            ],*/
            $temp = [];
            $wheredata = [
                'type' => array('in',[0,1])
            ];
            $wheredata['status'] = 1;
            $data = model('Orders')->where($wheredata)->page($page,$limit)->select()->toArray();



            $count = model('Orders')->where($wheredata)->count();
            foreach ($data as $key => $value) {
                $fee = $value['fee']; //缴费金额
                $voucher = $value['voucher'];   //凭证号
                $deposit_num = '1';         //应收人数
                $pay_fee = $value['pay_fee']; //实收金额
                $compensation = $value['compensation']; //赔偿
                if ($value['pay_status'] == 0) {
                    $net_deposit_num = 0; //实收人数
                    $year      = ''; //缴费年
                    $month     = ''; //缴费月
                    $pay_times  = ''; //缴费时间
                    $owe_num   = 1; //欠费人
                    $owe_money = $value['fee'] - $value['compensation']; //欠费金额
                    $pay_time = date('Y-m-d',$value['start_at']).'--'.date('Y-m-d',$value['end_at']); //缴费周期
                } else {
                    $net_deposit_num = 1; //实收人数
                    $year      = date('Y',$value['finish_at']); //缴费年
                    $month     = date('m',$value['finish_at']); //缴费月
                    $pay_times  = date('Y-m-d',$value['finish_at']); //缴费时间
                    $owe_num   = 0; //欠费人
                    $owe_money = ''; //欠费金额
                    $pay_time  = date('Y-m-d',$value['start_at']).'--'.date('Y-m-d',$value['end_at']);
                }
                $where = [
                    'home_id'    => $value['home_id']
                ];
                $home = model('Home')->where($where)->find();
                $user = $home['owner'];
                $area = $home['complex']; // 区
                $area_num = $home['building'].'-'.$home['unit'].'-'.$home['room'];   //区号
                $volume = $home['area'];   //区号

                $status = '';
                if ($value['pay_status'] == 0) {
                    $status = '未缴费';
                } else if ($value['pay_status'] == 1) {
                    $status = '已缴费';
                } else {
                    $status = '已退款';
                }
                $temp[] = [
                    'user'  => $user, //姓名
                    'area'  => $area,     //区
                    'area_num'  => $area_num,   //区号
                    'volume'    => $volume,   //面积
                    'deposit_num' => $deposit_num, //应收人数
                    'deposit_money' => $fee, //缴费金额
                    'year'      => $year, //缴费年
                    'month'     => $month, //缴费月
                    'voucher'   => $voucher, //凭证号
                    'net_deposit_num'   => $net_deposit_num, //实收人数
                    'net_money' => $pay_fee, //实收金额
                    'compensate'=> $compensation, //赔偿
                    'owe_num'   => $owe_num, //欠费人
                    'owe_money' => $owe_money, //欠费金额
                    'ctime'     => $home['check_in_at'], //入住时间
                    'pay_time'  => $pay_time, //缴费日期
                    'status'    => $status,
                    'pay_times' => $pay_times,// 缴费时间
                ];
            }
			
		    $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$temp];
		    echo json_encode($info);
		    exit;

		}else{
            $district=model('District')->select()->toArray();
            $this->assign('dis',$district);
		    return view();
		}
	}

    public function dataNewList()
    {

        $data = input();
        if (empty($data)) {
            fail('非法操作此页面');
        }

        $where = [];
        if ($data['type'] !== '') {
            $where['type'] = $data['type'];
        } else {
            $where['type'] = array('in',[0,1]);
        }

        if (!empty($data['dis'])) {
            $where['district_id'] =  $data['dis'];
        }

        if (!empty($data['qu'])) {
            $where['complex'] = $data['qu'];
        }
        $where['status'] = 1;
        if (!empty($data['start_at']) && !empty($data['end_at'])) {
                $datas = model('Orders')->page($data['page'],$data['limit'])->
                where($where)->
                whereTime('finish_at', 'between', [$data['start_at'], $data['end_at']])->
                select()->toArray();
                $count = model('Orders')->where($where)->
                whereTime('finish_at', 'between', [$data['start_at'], $data['end_at']])->
                count();                                  
        } else {
            $datas = model('Orders')->page($data['page'],$data['limit'])->where($where)->select()->toArray();
            $count = model('Orders')->where($where)->count();            
        }


        $temp = [];
        
         foreach ($datas as $key => $value) {
             $fee = $value['fee']; //缴费金额
             $voucher = $value['voucher'];   //凭证号
             $deposit_num = '1';         //应收人数
             $pay_fee = $value['pay_fee']; //实收金额
             $compensation = $value['compensation']; //赔偿
             if ($value['pay_status'] == 0) {
                 $net_deposit_num = 0; //实收人数
                 $year      = ''; //缴费年
                 $month     = ''; //缴费月
                 $pay_times  = ''; //缴费时间
                 $owe_num   = 1; //欠费人
                 $owe_money = $value['fee'] - $value['compensation']; //欠费金额
                 $pay_time = date('Y-m-d',$value['start_at']).'--'.date('Y-m-d',$value['end_at']);
             } else {
                 $net_deposit_num = 1; //实收人数
                 $year      = date('Y',$value['finish_at']); //缴费年
                 $month     = date('m',$value['finish_at']); //缴费月
                 $owe_num   = 0; //欠费人
                 $owe_money = ''; //欠费金额
                 $pay_times  = date('Y-m-d H:i:s',$value['finish_at']);
                 $pay_time = date('Y-m-d',$value['start_at']).'--'.date('Y-m-d',$value['end_at']);
             }
             $where = [
                 'home_id'    => $value['home_id']
             ];
             $home = model('Home')->where($where)->find();
             $user = $home['owner'];
             $area = $home['complex']; // 区
             $area_num = $home['building'].'-'.$home['unit'].'-'.$home['room'];   //区号
             $volume = $home['area'];   //区号

             $status = '';
             if ($value['pay_status'] == 0) {
                 $status = '未缴费';
             } else if ($value['pay_status'] == 1) {
                 $status = '已缴费';
             } else {
                 $status = '未缴费';
             }

             $temp[] = [
                 'user'  => $user, //姓名
                 'area'  => $area,     //区
                 'area_num'  => $area_num,   //区号
                 'volume'    => $volume,   //面积
                 'deposit_num' => $deposit_num, //应收人数
                 'deposit_money' => $fee, //缴费金额
                 'year'      => $year, //缴费年
                 'month'     => $month, //缴费月
                 'voucher'   => $voucher, //凭证号
                 'net_deposit_num'   => $net_deposit_num, //实收人数
                 'net_money' => $pay_fee, //实收金额
                 'compensate'=> $compensation, //赔偿
                 'owe_num'   => $owe_num, //欠费人
                 'owe_money' => $owe_money, //欠费金额
                 'ctime'     => $home['check_in_at'], //入住时间
                 'pay_time'  => $pay_time, //缴费日期
                 'status'    => $status,
                 'pay_times' => $pay_times,
             ];
         }

        
        $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$temp];
        echo json_encode($info);
        exit;

        print_r($data);
    }

	public function exceAdd()
	{
		if(check()){
		    $data=input('post.');
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

            $where = [];
            if ($datas['excel_show'] !== '') {
                $where['type'] = $datas['excel_show'];
            } else {
                $where['type'] = array('in',[0,1]);
            }

            if (!empty($datas['xiaoqu'])) {
                $where['district_id'] =  $datas['xiaoqu'];
            }

            if (!empty($datas['qu'])) {
                $where['complex'] = $datas['qu'];
            }

            if (empty($datas['paytime'])) {
                $where['paytime'] = date("Y",time());
            } else {
                $where['paytime'] = $datas['paytime'];
            }
            $where['status'] = 1;
            $datas['tableName'] = $datas['tableName'].'('.$where['paytime'].')';
            /*if (!empty($datas['start_at']) && !empty($datas['end_at'])) {
                    $newdata = model('Orders')->
                    where($where)->
                    whereTime('finish_at', 'between', [$datas['start_at'], $datas['end_at']])->
                    select()->toArray();
                                                      
            } else {
                $newdata = model('Orders')->where($where)->select()->toArray();           
            }
*/
            $newdata = model('Orders')->where($where)->select()->toArray();
             $temp = [];
        
         foreach ($newdata as $key => $value) {
             $fee = sprintf("%1\$.2f", $value['money']+$value['compensation']); //缴费金额sprintf("%1\$.2f", $v['money']+$v['compensation']);
             $voucher = $value['voucher'];   //凭证号
             $deposit_num = '1';         //应收人数
             $pay_status = ''; //缴费状态
             $compensation = $value['compensation']; //赔偿
             if ($value['pay_status'] == 0) {
                $pay_status = '未缴费'; //缴费状态
                 $net_deposit_num = 0; //实收人数
                 $year      = ''; //缴费年
                 $month     = ''; //缴费月
                 $owe_num   = 1; //欠费人
                 $owe_money = sprintf("%1\$.2f", $value['money']); //欠费金额
                 $pay_time = ''; //欠费金额
                 $pay_fee = 0; //实收金额
             } else if($value['pay_status'] == 1) {
                $pay_status = '已缴费'; //缴费状态
                $net_deposit_num = 1; //实收人数
                 $year      = date('Y',$value['finish_at']); //缴费年
                 $month     = date('m',$value['finish_at']); //缴费月
                 $owe_num   = 0; //欠费人
                 $owe_money = ''; //欠费金额
                 $pay_time  = date('Y-m-d H:i:s',$value['finish_at']);
                 $pay_fee = sprintf("%1\$.2f", $value['money']); //实收金额
             } else {

                $pay_status = '已退款'; //缴费状态
                $net_deposit_num = 1; //实收人数
                $year      = date('Y',$value['finish_at']); //缴费年
                $month     = date('m',$value['finish_at']); //缴费月
                $owe_num   = 0; //欠费人
                $owe_money = ''; //欠费金额
                $pay_time  = date('Y-m-d H:i:s',$value['finish_at']);
                $pay_fee = sprintf("%1\$.2f", $value['money']); //实收金额
             }
             $where = [
                 'home_id'    => $value['home_id']
             ];
             $home = model('Home')->where($where)->find();
             $user = $home['owner'];
             $area = $home['complex']; // 区
             $area_num = $home['building'].'-'.$home['unit'].'-'.$home['room'];   //区号
             $volume = $home['area'];   //区号
             $temp[] = [
                 'user'  => $user, //姓名
                 'area'  => $area,     //区
                 'area_num'  => $area_num,   //区号
                 'volume'    => $volume,   //面积
                 'deposit_num' => $deposit_num, //应收人数
                 'deposit_money' => $fee, //缴费金额
                 'year'      => $year, //缴费年
                 'month'     => $month, //缴费月
                 'voucher'   => $voucher, //凭证号
                 'net_deposit_num'   => $net_deposit_num, //实收人数
                 'net_money' => $pay_fee, //实收金额
                 'compensate'=> $compensation, //赔偿
                 'owe_num'   => $owe_num, //欠费人
                 'owe_money' => $owe_money, //欠费金额
                 'ctime'     => $home['check_in_at'], //入住时间
                 'pay_time'  => $pay_time, //缴费日期
                 'pay_status'=> $pay_status, //缴费状态
             ];
         }

            $xlsData = $temp;
            
			// $newdata = json_decode($data['data'],true);
		       // 文件名和文件类型
		       // $fileName = $data['fileName'];
		   
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
		               $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q");
		               // $letter =explode(',',"A,B,C,D,E,F,G,H,I,J");
		               // $arrHeader = array('姓名','公司','公司地址','邮箱','电话','职位','行业应用','会员角色','是否验证');
		               $arrHeader = array('姓名','区','区楼号','面积','缴费状态','应收金额','交费年','交费月','凭证号','实收金额','赔偿','欠费金额','入住时间','交费日期');
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
		                   $objActSheet->setCellValue('A'.$k,$v['user']);
		                   $objActSheet->setCellValue('B'.$k, $v['area']);
		                   $objActSheet->setCellValue('C'.$k, $v['area_num']);
		                   $objActSheet->setCellValue('D'.$k, $v['volume']);
		                   $objActSheet->setCellValue('E'.$k, $v['pay_status']);
		                   $objActSheet->setCellValue('F'.$k, $v['deposit_money']);
		                   $objActSheet->setCellValue('G'.$k, $v['year']);
		                   $objActSheet->setCellValue('H'.$k, $v['month']);
		                   $objActSheet->setCellValue('I'.$k, $v['voucher']);
		                //    $objActSheet->setCellValue('J'.$k, $v['net_deposit_num']);
		                   $objActSheet->setCellValue('J'.$k, $v['net_money']);
		                   $objActSheet->setCellValue('K'.$k, $v['compensate']);
		                //    $objActSheet->setCellValue('M'.$k, $v['owe_num']);
		                   $objActSheet->setCellValue('L'.$k, $v['owe_money']);
		                   $objActSheet->setCellValue('M'.$k, $v['ctime']);
		                   $objActSheet->setCellValue('N'.$k, $v['pay_time']);
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
		               $objActSheet->getColumnDimension('H')->setWidth($width[1]);
		               $objActSheet->getColumnDimension('I')->setWidth($width[1]);
		               $objActSheet->getColumnDimension('K')->setWidth($width[1]);
		               $objActSheet->getColumnDimension('L')->setWidth($width[1]);
		               $objActSheet->getColumnDimension('M')->setWidth($width[1]);
		               $objActSheet->getColumnDimension('N')->setWidth($width[1]);
		               $objActSheet->getColumnDimension('O')->setWidth($width[1]);
		               $objActSheet->getColumnDimension('P')->setWidth($width[1]);
		        
		        
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
   
   /**
    * 数据报表页面.
    * @return [type] [description]
    */
   public function dataExcea()
   {
        if(check()){
            $data=input('post.');
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
     * 导出数据报表.
     * @return [type] [description]
     */
    public function exportExcelData()
    {
            $datas = input();
            
            if (empty($datas)) {
                fail('非法操作此页面');
            }

               $where = [];
               if ($datas['excel_show'] !== '') {
                   $where['type'] = $datas['excel_show'];
               } else {
                   $where['type'] = array('in',[0,1]);
               }

               if (!empty($datas['xiaoqu'])) {
                   $where['district_id'] =  $datas['xiaoqu'];
               }

               if (!empty($datas['qu'])) {
                   $where['complex'] = $datas['qu'];
               }
               //查出数据.
               $newdata = model('Orders')->where($where)->order('paytime asc')->select()->toArray();
               //给缴费时间的时间戳转换成时间.
               foreach ($newdata as $ks => $vs) {
                   $newdata[$ks]['finish_at'] = date('Y-m-d H:i:s',$vs['finish_at']);
               }
               //根据paytime字段分组.
               $result = array();
               foreach($newdata as $k=>$v){
                   $result[$v['paytime']][] = $v;
               }

               $title = '';
               if ($datas['excel_show'] == 0) {
                    $title = '年物业费';   
               } else {
                    $title = '年供暖费';
               }

               //月的时间
               if (empty($datas['start_at'])) {
                    $time = $this->getthemonth(date('Y-m-d',time()));
                    $s = $time[0];
                    $e = $time[1];
                    $date = explode('-',$time[0]);
                    $ys = $date[0].'-01';
                    $ye = $date[0].'-12';
               } else {
                    $time = $this->getthemonth($datas['start_at']);
                    $s = $time[0];
                    $e = $time[1];
                    $date = explode('-',$time[0]);
                    $ys = $date[0].'-01';
                    $ye = $date[0].'-12';
               }



               $temp = [];
                foreach ($result as $kk => $vv) {

                    $temp[$kk]['name'] = $kk.$title; //年度物业费.
                    $temp[$kk]['count'] = count($vv); //年初陈欠户数
                    $temp[$kk]['money'] = 0;

                    $wdata = array_filter($vv, function($v) use ($s, $e) {
                         return $v['finish_at'] >= $s && $v['finish_at'] <= $e;
                    });

                    $temp[$kk]['mcount'] = count($wdata); //本月收回户数
                    $temp[$kk]['mmoney'] = 0; //本月收回金额
                    foreach ($wdata as $i => $val) {
                        $temp[$kk]['mmoney'] += $val['money'];
                    }




                    $ydata = array_filter($vv, function($v) use ($ys, $ye) {
                         return $v['finish_at'] >= $ys && $v['finish_at'] <= $ye;
                    });
                    $temp[$kk]['ycount']= count($ydata); //本年累计户数
                    $temp[$kk]['ymoney']= 0;    //本年累计金额.

                    foreach ($wdata as $u => $y) {
                        $temp[$kk]['ymoney'] += $y['money'];
                    }

                    foreach ($vv as $key => $value) {
                        $temp[$kk]['money'] += $value['money']; //年初陈欠金额  
                    }


                    $temp[$kk]['mycount'] = $temp[$kk]['count'] - $temp[$kk]['ycount'];
                    $temp[$kk]['mymoney'] = $temp[$kk]['money'] - $temp[$kk]['ymoney'];

                }
// Cumulative
                $temp = array_values($temp);





                if(!empty($temp)){
                    $cumulative = [
                        'count' => 0,
                        'money' => 0,
                        'mcount'    => 0,
                        'mmoney'    => 0,
                        'ycount'    => 0,
                        'ymoney'    => 0,
                        'mycount'   => 0,
                        'mymoney'   => 0,
                    ];
                    foreach ($temp as $t => $l) {
                   
                        $cumulative['name'] = '累计回收'.$title;
                        $cumulative['count'] += $temp[$t]['count'];
                        $cumulative['money'] += $l['money'];
                        $cumulative['mcount'] += $l['mcount'];
                        $cumulative['mmoney'] += $l['mmoney'];
                        $cumulative['ycount'] += $l['ycount'];
                        $cumulative['ymoney'] += $l['ymoney'];
                        $cumulative['mycount'] += $l['mycount'];
                        $cumulative['mymoney'] += $l['mymoney'];
                        
                    }
                    array_push($temp,$cumulative);
                }

            //    echo '<pre>';
            //     print_r($temp);
            //     exit;
                
            //    dump($temp);exit;

            //    dump($temp);exit;
            //    foreach ($newdata as $k => $v) {
            //        dump($v);exit;
            //        // array_sum
            //    }
            //    dump($newdata);exit;
            //    if (empty($datas['start_at'])) {
            //         $time = $this->getthemonth(date('Y-m-d',time()));
            //         $where['start_at'] = $time[0];
            //         $where['end_at'] = $time[1];
            //    } else {
            //         $time = $this->getthemonth($datas['start_at']);
            //         $where['start_at'] = $time[0];
            //         $where['end_at'] = $time[1];
            //    }
            //    if (!empty($datas['start_at']) && !empty($datas['end_at'])) {
            //            $newdata = model('Orders')->
            //            where($where)->
            //            whereTime('finish_at', 'between', [$datas['start_at'], $datas['end_at']])->
            //            select()->toArray();
                                                         
            //    } else {
            //        $newdata = model('Orders')->where($where)->select()->toArray();           
            //    }
            //     dump($newdata);exit;
            //    // $newdata = model('Orders')->where($where)->select()->toArray();
            //     $temp = [];
           
            // foreach ($newdata as $key => $value) {
            //     $fee = sprintf("%1\$.2f", $value['money']+$value['compensation']); //缴费金额sprintf("%1\$.2f", $v['money']+$v['compensation']);
            //     $voucher = $value['voucher'];   //凭证号
            //     $deposit_num = '1';         //应收人数
            //     $compensation = $value['compensation']; //赔偿
            //     if ($value['pay_status'] == 0) {
            //         $net_deposit_num = 0; //实收人数
            //         $year      = ''; //缴费年
            //         $month     = ''; //缴费月
            //         $owe_num   = 1; //欠费人
            //         $owe_money = sprintf("%1\$.2f", $value['money']); //欠费金额
            //         $pay_time = ''; //欠费金额
            //         $pay_fee = 0; //实收金额
            //     } else {
            //         $net_deposit_num = 1; //实收人数
            //         $year      = date('Y',$value['finish_at']); //缴费年
            //         $month     = date('m',$value['finish_at']); //缴费月
            //         $owe_num   = 0; //欠费人
            //         $owe_money = ''; //欠费金额
            //         $pay_time  = date('Y-m-d H:i:s',$value['finish_at']);
            //         $pay_fee = sprintf("%1\$.2f", $value['money']); //实收金额
            //     }
            //     $where = [
            //         'home_id'    => $value['home_id']
            //     ];
            //     $home = model('Home')->where($where)->find();
            //     $user = $home['owner'];
            //     $area = $home['complex']; // 区
            //     $area_num = $home['building'].'-'.$home['unit'].'-'.$home['room'];   //区号
            //     $volume = $home['area'];   //区号
            //     $temp[] = [
            //         'user'  => $user, //姓名
            //         'area'  => $area,     //区
            //         'area_num'  => $area_num,   //区号
            //         'volume'    => $volume,   //面积
            //         'deposit_num' => $deposit_num, //应收人数
            //         'deposit_money' => $fee, //缴费金额
            //         'year'      => $year, //缴费年
            //         'month'     => $month, //缴费月
            //         'voucher'   => $voucher, //凭证号
            //         'net_deposit_num'   => $net_deposit_num, //实收人数
            //         'net_money' => $pay_fee, //实收金额
            //         'compensate'=> $compensation, //赔偿
            //         'owe_num'   => $owe_num, //欠费人
            //         'owe_money' => $owe_money, //欠费金额
            //         'ctime'     => $home['check_in_at'], //入住时间
            //         'pay_time'  => $pay_time, //缴费日期
            //     ];
            // }
               $xlsData = $temp;
            // $newdata = json_decode($data['data'],true);
               // 文件名和文件类型
               // $fileName = $data['fileName'];
           
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
                       $letter =explode(',',"A,B,C,D,E,F,G,H,I");
                       // $letter =explode(',',"A,B,C,D,E,F,G,H,I,J");
                       // $arrHeader = array('姓名','公司','公司地址','邮箱','电话','职位','行业应用','会员角色','是否验证');
                       $arrHeader = array('年度'.$title,'年初陈欠户数','年初陈欠金额','本月收回户数','本月收回金额','本年累计户数','本年累计金额','月末陈欠户数','月末陈欠金额');

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
                           $objActSheet->setCellValue('A'.$k,$v['name']);
                           $objActSheet->setCellValue('B'.$k, $v['count']);
                           $objActSheet->setCellValue('C'.$k, $v['money']);
                           $objActSheet->setCellValue('D'.$k, $v['mcount']);
                           $objActSheet->setCellValue('E'.$k, $v['mmoney']);
                           $objActSheet->setCellValue('F'.$k, $v['ycount']);
                           $objActSheet->setCellValue('G'.$k, $v['ymoney']);
                           $objActSheet->setCellValue('H'.$k, $v['mycount']);
                           $objActSheet->setCellValue('I'.$k, $v['mymoney']);
                           // $objActSheet->setCellValue('J'.$k, $v['net_deposit_num']);
                           // $objActSheet->setCellValue('K'.$k, $v['net_money']);
                           // $objActSheet->setCellValue('L'.$k, $v['compensate']);
                           // $objActSheet->setCellValue('M'.$k, $v['owe_num']);
                           // $objActSheet->setCellValue('N'.$k, $v['owe_money']);
                           // $objActSheet->setCellValue('O'.$k, $v['ctime']);
                           // $objActSheet->setCellValue('P'.$k, $v['pay_time']);
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
                       $objActSheet->getColumnDimension('H')->setWidth($width[1]);
                       $objActSheet->getColumnDimension('I')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('K')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('L')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('M')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('N')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('O')->setWidth($width[1]);
                       // $objActSheet->getColumnDimension('P')->setWidth($width[1]);
                
                
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

    /**
     * 获取本月的第一天和最后一天
     * @param  [type] $date [description]
     * @return [type]       [description]
     */
    public  function getthemonth($date)
    {
    $firstday = date('Y-m-01', strtotime($date));
    $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    return array($firstday,$lastday);
    }

	protected function getData()
    {
        $studentList = [
            [
                'stuNo' => '20190101',
                'name' => '贾东杰',
                'class' => '1班'
            ], [
                'stuNo' => '20190102',
                'name' => '志豪',
                'class' => '1班'
            ], [
                'stuNo' => '20190103',
                'name' => '1111',
                'class' => '1班'
            ]
        ];

        return $studentList;
    }

    protected function dataData()
    {
    	$data = [
    		[
    			'user'	=> '东杰', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-101', 	//区号
    			'volume'	=> '580',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '6800', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '6800', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '志豪', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-102', 	//区号
    			'volume'	=> '180',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '800', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '800', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '海洋', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-103', 	//区号
    			'volume'	=> '80',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '600', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '600', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '福军', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-104', 	//区号
    			'volume'	=> '280',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '1000', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '1000', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '石锐', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-105', 	//区号
    			'volume'	=> '380',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '1200', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '1200', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '虎虎', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-106', 	//区号
    			'volume'	=> '180',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '6800', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '6800', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '郝飞', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-108', 	//区号
    			'volume'	=> '100',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '900', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '900', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '晓飞', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-109', 	//区号
    			'volume'	=> '100',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '900', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '900', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '星亮', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-101', 	//区号
    			'volume'	=> '100',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '900', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '900', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],
    		[
    			'user'	=> '安宁', //姓名
    			'area'	=> '甲',		//区
    			'area_num'	=> '1-1-101', 	//区号
    			'volume'	=> '100',	//面积
    			'deposit_num' => '1', //应收人数
    			'deposit_money' => '900', //缴费金额
    			'year'		=> '2020', //缴费年
    			'month'		=> '06', //缴费月
    			'voucher'	=> '001', //凭证号
    			'net_deposit_num'	=> '1', //实收人数
    			'net_money'	=> '900', //实收金额
    			'compensate'=> '', //赔偿
    			'owe_num'	=> '0', //欠费人
    			'owe_money'	=> '0.0', //欠费金额
    			'ctime'		=> '04.10.8', //入住时间
    			'pay_time'	=> '1月1', //缴费日期
    		],

    	];
    	return $data;
    }
}