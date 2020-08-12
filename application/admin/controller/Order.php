<?php
namespace  app\admin\controller;
use think\Db;
use app\admin\model\Order as MOrder;
use app\admin\model\House;
use app\admin\model\PayRecord;
use EasyWeChat\Kernel\Messages\Text;
use app\admin\controller\Easy;

/**
 * 缴费管理
 */
class Order extends Common
{
    private function getAjaxWhere($data,$type)
    {
        $where=[];
        $where["o.type"]=$type;
        if (isset($data["district_id"])&&$data["district_id"])
        {
            $where["h.district_id"]=trim($data["district_id"]);
        }
        if (isset($data["invoice_status"])&&$data["invoice_status"]!="")
        {
            $where["o.invoice_status"]=trim($data["invoice_status"]);
        }
        if (isset($data["pay_type"])&&$data["pay_type"])
        {
            $where["o.pay_type"]=trim($data["pay_type"]);
        }
        if (isset($data["owner"])&&$data["owner"])
        {
            $where["h.owner"]=trim($data["owner"]);
        }
        if (isset($data["complex"])&&$data["complex"])
        {
            $where["h.complex"]=trim($data["complex"]);
        }
        if (isset($data["building"])&&$data["building"])
        {
            $where["h.building"]=trim($data["building"]);
        }
        if (isset($data["unit"])&&$data["unit"])
        {
            $where["h.unit"]=trim($data["unit"]);
        }
        if (isset($data["room"])&&$data["room"])
        {
            $where["h.room"]=trim($data["room"]);
        }
        if (isset($data["id"])&&$data["id"])
        {
            $where["o.id"]=trim($data["id"]);
        }
        return $where;
    }
	public function property()
	{
		return $this->orderList(MOrder::PROPERTY_TYPE);
	}
    public function heating()
    {
        return $this->orderList(MOrder::HEATING_TYPE);
    }
    private function orderList($type)
    {
        if(request()->isAjax())
        {
            $page=input('get.page')?:1;
            $limit=input('get.limit')?:10;
            $data=input('get.');
            $where=$this->getAjaxWhere($data,$type);
            $data=Db::table("order")
                ->alias('o')
                ->field("o.*,h.owner,h.tel,h.complex,h.building,h.unit,h.room,h.area,h.district_id,d.name as district")
                ->join('house h','o.house_id = h.id','inner')
                ->join('district d','d.id = h.district_id','left')
                ->where($where)
                ->order('id','asc')
                ->page($page,$limit)
                ->select();
            foreach ($data as &$val)
            {
                $val["start_at"]=date("Y-m-d H:i:s",$val["start_at"]);
                $val["end_at"]=date("Y-m-d H:i:s",$val["end_at"]);
                $val["house"]=House::getHouse($val);
            }
            unset($val);
            $count=Db::table("order")
                ->alias('o')
                ->join('house h','o.house_id = h.id','inner')
                ->where($where)
                ->count();
            $this->layui_success($data,$count);
        }
        $district=Db::table("district")->field("id,name")->order('id','asc')->select();
        $this->assign('districts',$district);
        $this->assign('type',$type);
        $this->assign('paytypes',MOrder::downlist("paytype"));
        $this->assign('invoices',MOrder::downlist("invoice"));
        $this->assign("url",$type==MOrder::PROPERTY_TYPE?url('property'):url('heating'));
        return view('index');
    }
    private function getAllHouse($type)
    {
        $data=Db::table("house")
            ->alias('h')
            ->field("h.id,h.owner,h.complex,h.building,h.unit,h.room,h.area,h.district_id,h.property_fee,h.heating_cost,d.name as district")
            ->join('district d','d.id = h.district_id','left')
            ->order('id','asc')
            ->select();
        foreach ($data as &$v)
        {
            $v["house"]=House::getHouse($v);
            $fee=$type==MOrder::PROPERTY_TYPE?$v["property_fee"]:$v["heating_cost"];
            $v["fee"]=round($fee*$v["area"],2);
        }
        unset($v);
        return $data;
    }
    public function form()
    {
        $id=input('get.id')?:0;
        $type=input('get.type')?:0;
        if ($id)
        {
            $data=Db::table("order")
                ->alias('o')
                ->field("o.*,h.owner,h.tel,h.complex,h.building,h.unit,h.room,h.area,h.district_id,d.name as district")
                ->join('house h','o.house_id = h.id','inner')
                ->join('district d','d.id = h.district_id','left')
                ->where(["o.id"=>$id,"o.type"=>$type])
                ->find();
            if ($data)
            {
                $data["start_at"]=date("Y-m-d",$data["start_at"]);
                $data["end_at"]=date("Y-m-d",$data["end_at"]);
                $data["house"]=House::getHouse($data);
            }
        }
        else
        {
            $data=column("order");
        }
        $data["type"]=$type;
        if(request() -> isPost()&&request()->isAjax())
        {
            if ($data["pay_status"]==MOrder::STATUS_FINISH)
            {
                $this->ajax_error("无法修改,收费单已支付!");
            }
            $data=input('post.');
            $time=time();
            $data["utime"]=$time;
            if(!isset($data["status"]))
            {
                $data["status"]=MOrder::STATUS_NOT_FINISH;
            }
            if (!$data["id"])
            {
                $data["start_at"]=strtotime($data["start_at"]);
                $data["end_at"]=strtotime($data["end_at"]);
                $data["ctime"]=$time;
                $data["order_sn"]=$this->getOrderSn();
                unset($data["id"]);
                $ret=Db::table('order')->insert($data);
            }
            else
            {
                $ret=Db::table('order')->update($data);
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
        $show=array(
            "back_url"=>$type==MOrder::PROPERTY_TYPE?url('property'):url('heating'),
            "submit_url"=>url('form')."?type=".$type,
            "title"=>'创建'.($type==MOrder::PROPERTY_TYPE?'物业':'供暖').'收费单',
            "back_title"=>($type==MOrder::PROPERTY_TYPE?'物业':'供暖').'收费列表',
        );
        $this->assign('type',$type);
        $this->assign('show',$show);
        $this->assign("houseList",$this->getAllHouse($type));
        return view();
    }
	public function del()
	{
		$id = input('post.id');
		if (!$id)
        {
            $this->ajax_error("缺少ID");
        }
        $ret=Db::table('order')->delete($id);;
        if ($ret)
        {
            $this->ajax_success();
        }
        else
        {
            $this->ajax_error(["DELETE FALSE"]);
        }
	}

    /**
     * 切换发布状态
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function switchStatus()
    {
        $id = input('post.id');
        if (!$id)
        {
            $this->ajax_error("缺少ID");
        }
        $order=Db::table('order')->where('id',$id)->find();
        if ($order["pay_status"])
        {
            $this->ajax_error("收费单已支付,无法更改状态!");
        }
        $ret=Db::table('order')->where('id',$id)->setField('status', $order["status"]==1?0:1);
        if ($ret)
        {
            $this->ajax_success();
        }
        else
        {
            $this->ajax_error(["UPDATE FALSE"]);
        }
    }

    /**
     * 开启电子发票
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function invoice()
    {
        $id = input('post.id');
        if (!$id)
        {
            $this->ajax_error("缺少ID");
        }
        $order=Db::table('order')->where('id',$id)->find();
        if (!$order["pay_status"])
        {
            $this->ajax_error("未支付,无法开启发票!");
        }
        if ($order["invoice_status"])
        {
            $this->ajax_error("已开启过发票!");
        }
        $ret=Db::table('order')->where('id',$id)->setField('invoice_status', MOrder::IS_INVOICE);
        if ($ret)
        {
            $this->ajax_success();
        }
        else
        {
            $this->ajax_error(["UPDATE FALSE"]);
        }
    }
    /**
     * 现金支付
     */
    public function cashPay()
    {
        if(!(request() -> isPost()&&request()->isAjax()))
        {
            $this->redirect('index');
        }
        Db::startTrans();
        try{
            $id=(int)input("post.id");
            $pay_at=time();
            if (!$id)
            {
                throw new \Exception("缺少ID");
            }
            $order=Db::table('order')->where('id',$id)->find();
            if (!$order)
            {
                throw new \Exception("没有该收费记录");
            }
            if (!$order["fee"]||$order["pay_status"]||!$order["status"])
            {
                throw new \Exception("非可支付状态!");
            }
            $order_data=array(
                "id"=>$id,
                "pay_fee" => $order["fee"]-$order["compensation"],
                "pay_type"      =>  MOrder::PAY_TYPE_CASH,
                "pay_status"      =>  MOrder::STATUS_FINISH,
                "finish_at"=>$pay_at
            );
            if (!Db::table("order")->update($order_data))
            {
                throw new \Exception("支付失败!");
            }
            $data=array(
                "status"        =>  PayRecord::STATUS_PAY,
                "payment_sn"    =>  $this->getOrderSn(),
                "pay_type"      =>  PayRecord::PAY_TYPE_CASH,
                "order_id"      =>  $id,
                "operator_id"   =>  $this->getOperator(),
                "type"          =>  $order["type"],
                "amount"        =>  $order_data["pay_fee"],
                "pay_at"        =>  $pay_at,
                "ctime"         =>  $pay_at,
                "utime"         =>  $pay_at
            );
            if (!Db::table("pay_record")->insert($data))
            {
                throw new \Exception("支付失败!!");
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->ajax_error($e->getMessage());
        }
        //region 处理支付完成相关逻辑

        //endregion
        $this->ajax_success();

    }

    /**
     * 一键催费
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeArrears()
    {
        $easy=new Easy();
        $arrearsOrder=Db::name("order")
            ->alias("o")
            ->where(["status"=>MOrder::STATUS_FINISH,"pay_status"=>MOrder::STATUS_NOT_FINISH])
            ->select();
        foreach ($arrearsOrder as $val)
        {
            $openId=$val["openid"];
            $message = new Text('Hello world!');

            $result = $easy->sendCustomerService($openId,$message);
            if ($result["errcode"]!==0)
            {
                return false;
            }
        }
    }
}