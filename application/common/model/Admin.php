<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: Admin
 * user: bingwoo
 * date: 2020/12/22 14:40
 */
class Admin extends Model{

    protected $table = 'admin';

    /**
     * Notes: 获取admin列表
     * Class: getAdminList
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getAdminList($where = [],$field = '*',$page = 0,$limit = 20){

        $query = $this->field($field);
        if($where){
            $query = $query->where($where);
        }
        if($page){
            $query = $query->page($page,$limit);
        }
        $data = $query->select()->toArray();
        $count = $query->count();
        return ['code'=>0,'msg'=>'','count'=>$count,'data'=>$data];

    }

    /**
     * Notes: 获取数据条数
     * Class: getAdminCount
     * user: bingwoo
     * date: 2020/12/2 16:56
     */
    public function getAdminCount($where){

        $query = $this;
        if($where){
            $query = $query->where($where);
        }
        $count = $query->count();
        return $count;
    }

    /**
     * Notes: 获取Admin信息
     * Class: getAdminInfo
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getAdminInfo($where = [],$field = '*',$Order = ''){
        $qurey = $this;
        if(!empty($where)){
            $qurey = $this->where($where);
        }
        if(!empty($where)){
            if(!empty($Order)){
                $qurey = $qurey->order($Order);
            }
        }else{
            if(!empty($Order)){
                $qurey = $this->order($Order);
            }
        }
        $data = $qurey->field($field)->find();
        if(empty($data)){
            return false;
        }
        $data = $data->toArray();
        return $data;
    }

    /**
     * Notes: 添加Admin
     * Class: addAdminData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function addAdminData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改Admin
     * Class: editAdminData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function editAdminData($id,$saveData){

        $ret = $this->where(['admin_id'=>$id])->update($saveData);
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除Admin
     * Class: delAdminData
     * user: bingwoo
     * date: 2020/11/24 20:41
     */
    public function delAdminData($id){

        $ret = $this->where(['admin_id'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除Admin
     * Class: delAdminDatas
     * user: bingwoo
     * date: 2020/11/24 20:41
     */
    public function delAdminDatas($ids='',$where){

        $ret = false;
        if($ids){
            $ret = $this->where('admin_id','in',$ids)->delete();
        }
        if($where){
            $ret = $this->where($where)->delete();
        }
        if($ret){
            return true;
        }
        return false;
    }



}