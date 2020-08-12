<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;

/**
 * 房屋管理
 */
class House extends Common
{
	public function index()
	{
		if(request()->isAjax())
		{
            $page=input('get.page')?:1;
            $limit=input('get.limit')?:10;
            $keyword=input('get.keyword')?:'';
            $district_id=input('get.district_id')?:'';
            $key=input('get.key')?:'owner';
            $where["h.id"]=[">",0];
            if (!empty($keyword))
            {
                $keyword=trim($keyword);
                if ($key=='owner')
                {
                    $keyword=["like","%{$keyword}%"];
                }
                $where["h.".$key]=$keyword;
            }
            if ($district_id)
            {
                $where["district_id"]=$district_id;
            }
            $data=Db::table("house")
                ->alias('h')
                ->field("h.*,d.name as district")
                ->join('district d','d.id = h.district_id','left')
                ->where($where)
                ->order('h.id','asc')
                ->page($page,$limit)
                ->select();
            foreach ($data as &$val)
            {
                $val["ctime"]=date("Y-m-d H:i:s",$val["ctime"]);
                $val["check_in_at"]=date("Y-m-d",$val["check_in_at"]);
            }
            unset($val);
            $count=Db::table("house")->alias('h')->where($where)->count();
            $this->layui_success($data,$count);
		}
        $district=Db::table("district")->field("id,name")->order('id','asc')->select();
        $this->assign('districts',$district);
        return view();
	}
    /**
     * 重构数组,excel第一行的各列作为key
     * @param $array
     * @return array
     */
	private function refactorArray($array)
    {
        $title_array=$array[0];
        array_shift($array);
        $ret=[];
        foreach ($array as $val)
        {
            $data=[];
            for ($i=0;$i<count($val);$i++)
            {
                $data[$title_array[$i]]=$val[$i];
            }
            $ret[]=$data;
        }
        return $ret;
    }

	public function importData()
    {
        if(request() -> isPost()&&request()->isAjax())
        {
            $created_at=time();
            vendor("PHPExcel.PHPExcel");
            //获取表单上传文件
            $file = request()->file('file');
            $uploadPath=ROOT_PATH . 'public/house';
            if(!file_exists($uploadPath)){
                mkdir($uploadPath,0777,true);
            }
            $info = $file->validate(['size'=>1048576,'ext'=>'xls,xlsx'])->move(ROOT_PATH . 'public/house');  //上传验证后缀名,以及上传之后移动的地址
            if($info)
            {
                $exclePath = $info->getSaveName();  //获取文件名
                $file_name =$uploadPath . DS . $exclePath;//上传文件的地址
                $suffix = $info->getExtension();
                //判断哪种类型
                if($suffix=="xlsx"){
                    $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                }else{
                    $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                }
                $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array=$obj_PHPExcel->getSheet(0)->toArray();   //转换为数组格式
                if (count($excel_array)<2)
                {
                    $this->ajax_error("表格数据过少");
                }
                $data=$this->refactorArray($excel_array);
                $insert=[];
                foreach ($data as $val)
                {
                    if (isset($val["小区名称"]))
                    {
                        $district_id=Db::table("district")->where(["name"=>$val["小区名称"]])->value("id");
                    }
                    $insert[]=[
                        "owner"=>$val["业主"]??'',
                        "district_id"=>$district_id??0,
                        "complex"=>$val["区"]??'',
                        "building"=>$val["楼"]??'',
                        "unit"=>$val["单元"]??'',
                        "room"=>$val["房间号"]??'',
                        "tel"=>$val["联系方式"]??'',
                        "heating_cost"=>$val["供暖费/m²"]??0,
                        "property_fee"=>$val["物业费/m²"]??0,
                        "area"=>$val["面积"]??0,
                        "check_in_at"=>isset($val["入住时间"])?strtotime($val["入住时间"]." 00:00:00"):0,
                        "ctime"=>$created_at,
                        "utime"=>$created_at
                    ];
                }
                Db::table("house")->insertAll($insert);
                $this->ajax_success($data,self::STATUS_SUCCESS,"导入成功");
            }else
            {
                $this->ajax_error($file->getError());
            }
        }
    }

    public function form()
    {
        $id=input('get.id')?:0;
        $data=\app\admin\model\House::attributes();
        if ($id)
        {
            $data=Db::table("house")
                ->alias('h')
                ->field("h.*,d.name as district")
                ->join('district d','d.id = h.district_id','left')
                ->where(["h.id"=>$id])->order('id','asc')
                ->find();
            if ($data)
            {
                $data["check_in_at"]=date("Y-m-d",$data["check_in_at"]);
            }
        }
        if(request() -> isPost()&&request()->isAjax())
        {
            $data=input('post.');
            $data["check_in_at"]=strtotime($data["check_in_at"]);
            if (!$data["id"])
            {
                unset($data["id"]);
                $ret=Db::table('house')->insert($data);
            }
            else
            {
                $ret=Db::table('house')->update($data);
            }

            if ($ret)
            {
                $this->ajax_success();
            }
            else
            {
                $this->ajax_error(["UPDATE FALSE"]);
            }
        }
        $district=Db::table("district")->field("id,name")->order('id','asc')->select();
        $this->assign('districts',$district);
        $this->assign('data',$data);
        return view();
    }
    public function downloadExcel()
    {
        $file="house.xlsx";
        $file_lj = str_replace("\\","/",ROOT_PATH.'public/template/');
        $files = $file_lj.$file;
        if(!file_exists($files))
        {
            return "文件不存在";
        }
        else {
            $file1 = fopen($files, "r");
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: " . filesize($files));
            Header("Content-Disposition: attachment; filename=" . $file);
            echo fread($file1, filesize($files));
            fclose($file1);
        }
    }
	public function del()
	{
		$id = input('post.id');
		if (!$id)
        {
            $this->ajax_error("缺少ID");
        }
        $ret=Db::table('house')->delete($id);;
        if ($ret)
        {
            $this->ajax_success();
        }
        else
        {
            $this->ajax_error(["DELETE FALSE"]);
        }
	}
    public function district()
    {
        if(request()->isAjax())
        {
            $page=input('get.page')?:1;
            $limit=input('get.limit')?:10;
            $data=Db::table("district")->order('id','asc')->page($page,$limit)->select();
            foreach ($data as &$val)
            {
                $val["ctime"]=date("Y-m-d H:i:s",$val["ctime"]);
            }
            unset($val);
            $count=Db::table("district")->count();
            $this->layui_success($data,$count);
        } else {
            return view();
        }
        
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
    public function districtEdit()
    {
        if(request() -> isPost()&&request()->isAjax())
        {
            $data=input('post.');
            $data["name"]=trim($data["name"]);
            if ( $data["id"]&&$data["name"])
            {
                if (!Db::table("district")->where(["id"=> $data["id"]])->value("id"))
                {
                    $this->ajax_error($data["name"]."不存在!");
                }
                $now=time();
                $data["utime"]=$now;
                $ret=Db::table('district')->update($data);
                if ($ret)
                {
                    $this->ajax_success();
                }
                else
                {
                    $this->ajax_error(["修改失败"]);
                }
            }
            else
            {
                $this->ajax_error("UPDATE FALSE");
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