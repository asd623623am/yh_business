<?php
namespace  app\common\model;

use think\Model;

/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: Content
 * user: bingwoo
 * date: 2020/12/2 16:19
 */
class Content extends Model{

    protected $table = 'xm_content';

    /**
     * Notes: 获取banner列表
     * Class: getContentList
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getContentList($where = [],$field = '*',$page = 0,$limit = 20){

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
     * Class: getContentCount
     * user: bingwoo
     * date: 2020/12/2 16:56
     */
    public function getContentCount($where){

        $query = $this;
        if($where){
            $query = $query->where($where);
        }
        $count = $query->count();
        return $count;
    }

    /**
     * Notes: 获取banner信息
     * Class: getContentInfo
     * user: bingwoo
     * date: 2020/11/24 20:39
     */
    public function getContentInfo($where = [],$field = '*',$Order = ''){
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
     * Notes: 添加banner
     * Class: addContentData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function addContentData($addData){

        $addData['create_time'] = time();
        $addData['update_time'] = time();
        $ret = $this->insert($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改banner
     * Class: editContentData
     * user: bingwoo
     * date: 2020/11/24 20:40
     */
    public function editContentData($id,$saveData){

        $ret = $this->where(['id'=>$id])->update($saveData);
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除banner
     * Class: delContentData
     * user: bingwoo
     * date: 2020/11/24 20:41
     */
    public function delContentData($id){

        $ret = $this->where(['id'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 批量删除banner
     * Class: delContentDatas
     * user: bingwoo
     * date: 2020/11/24 20:41
     */
    public function delContentDatas($ids){

        $ret = $this->where('id','in',$ids)->delete();
        if($ret){
            return true;
        }
        return false;
    }



}