<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: AdminRole
 * user: bingwoo
 * date: 2020/12/22 14:46
 */
class AdminRole extends Model{

    protected $table = 'admin_role';

    /**
     * Notes: 获取adminRole列表
     * Class: getAdminRoleList
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getAdminRoleList($where = [],$field = '*',$page = 0,$limit = 20){

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
     * Class: getAdminRoleCount
     * user: bingwoo
     * date: 2020/12/2 16:56
     */
    public function getAdminRoleCount($where){

        $query = $this;
        if($where){
            $query = $query->where($where);
        }
        $count = $query->count();
        return $count;
    }

    /**
     * Notes: 获取AdminRole信息
     * Class: getAdminRoleInfo
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getAdminRoleInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 添加AdminRole
     * Class: addAdminData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function addAdminRoleData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改AdminRole
     * Class: editAdminRoleData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function editAdminRoleData($id,$saveData){

        $ret = $this->where(['admin_id'=>$id])->update($saveData);
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除AdminRole
     * Class: delAdminRoleData
     * user: bingwoo
     * date: 2020/11/24 20:41
     */
    public function delAdminRoleData($id){

        $ret = $this->where(['admin_id'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除AdminRole
     * Class: delAdminRoleDatas
     * user: bingwoo
     * date: 2020/11/24 20:41
     */
    public function delAdminRoleDatas($ids='',$where){

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