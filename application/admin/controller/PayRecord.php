<?php
namespace  app\admin\controller;
use think\Controller;
use think\Db;
use app\admin\model\Order;
use app\admin\model\House;
use app\admin\model\PayRecord as PayRecordModel;

/**
 * 缴费记录管理
 */
class PayRecord extends Common
{
    public function index()
    {
        if(request()->isAjax())
        {
            $page=input('get.page')?:1;
            $limit=input('get.limit')?:10;
            $data=input('get.');
            $where=$this->getAjaxWhere($data);
            $data=Db::table("pay_record")
                ->alias('p')
                ->field("p.*,a.admin_name,'' as user")
                ->join('admin a','a.admin_id = p.operator_id','left')
                ->where($where)
                ->order('id','asc')
                ->page($page,$limit)
                ->select();
            foreach ($data as &$val)
            {
                $val["pay_at"]=date("Y-m-d H:i:s",$val["pay_at"]);
                $val["ctime"]=date("Y-m-d H:i:s",$val["ctime"]);
            }
            unset($val);
            $count=Db::table("pay_record")
                ->alias('p')
                ->where($where)
                ->count();
            $this->layui_success($data,$count);
        }

        $this->assign('types',PayRecordModel::downlist("type"));
        $this->assign('paytypes',PayRecordModel::downlist("paytype"));
        $this->assign('statuss',PayRecordModel::downlist("status"));
        return view();
    }
    private function getAjaxWhere($data)
    {
        $where=[];
        if (isset($data["type"])&&$data["type"]!=='')
        {
            $where["p.type"]=trim($data["type"]);
        }
        if (isset($data["pay_type"])&&$data["pay_type"]!=='')
        {
            $where["p.pay_type"]=trim($data["pay_type"]);
        }
        if (isset($data["payment_sn"])&&$data["payment_sn"]!=='')
        {
            $where["p.payment_sn"]=trim($data["payment_sn"]);
        }
        if (isset($data["status"])&&$data["status"]!=='')
        {
            $where["p.status"]=trim($data["status"]);
        }
        if (isset($data["id"])&&$data["id"]!=='')
        {
            $where["p.id"]=trim($data["id"]);
        }
        return $where?:1;
    }



}