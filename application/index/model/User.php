<?php
namespace app\index\model;
use think\Model;

class User extends Model{

    protected $table='user';
    //定义时间戳字段名;
    //protected $createTime='ctime';
    //protected $updateTime=false;

    protected $isNoMustField = ['name'=>'会员名称','phone'=>'手机号', 'sex'=>'性别','birthday'=>'生日','card'=>'会员卡',
                                'storeid'=>'门店id','email'=>'邮箱','grid'=>'会员等级','role'=>'会员角色','points'=>'会员积分',
                                'balance'=>'会员余额', 'wx_name'=>'微信昵称','wx_img'=>'微信头像','wx_openid'=>'微信openid',
                                'wx_token'=>'微信token', 'mini_qrcode'=>'小程序二维码','mini_openid'=>'小程序openid',
                                'remarks'=>'备注', 'label'=>'会员标签','discount'=>'折扣','pay_money'=>'消费金额',
                                'countmoney'=>'累计充值金额', 'is_type'=>'用户类型','status'=>'状态'];

    /**
     * Notes: 验证过滤为空字段
     * Class: isVerifSyncColumn
     * user: bingwoo
     * date: 2020/12/23 20:49
     */
    public function isVerifSyncColumn($postData){
        foreach ($this->isVerifSyncColumn as $k => $val){
            if(empty($postData[$k])){
                unset($postData[$k]);
            }
        }
        return $postData;
    }

    /**
     * Notes: 根据门店id获取会员用户信息
     * Class: getMemberUserByStoreid
     * user: bingwoo
     * date: 2020/12/24 11:58
     */
    public function getUserListByStoreid($storeid,$field){
        $where = [];
        $where['storeid'] = ['=',$storeid];
        $userData = $this->getUserList($where,$field);
        return $userData;
    }

    /**
     * Notes: 获取会员列表
     * Class: getUserList
     * user: bingwoo
     * date: 2020/11/26 17:45
     */
    public function getUserList($where = [],$field = '*',$page = 0,$limit = 20){

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
     * Notes: 获取用户数量
     * Class: getUserCount
     * user: bingwoo
     * date: 2020/12/10 15:53
     */
    public function getUserCount($where){

        return $this->where($where)->count();
    }

    /**
     * Notes: 获取会员信息
     * Class: getUserInfo
     * user: bingwoo
     * date: 2020/11/26 17:45
     */
    public function getUserInfo($where = [],$field = '*',$Order = ''){
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
        $data = $this->changeName($data);
        return $data;
    }

    /**
     * Notes: 添加会员数据
     * Class: addUserData
     * user: bingwoo
     * date: 2020/11/26 17:46
     */
    public function addUserData($addData,$isgetID = false){
        if($isgetID){
            $ret = $this->insertGetId($addData);
        }else{
            $addData['create_time'] = time();
            $addData['update_time'] = time();
            $ret = $this->insert($addData);
        }
        if($ret){
            return $ret;
        }
        return false;
    }

    /**
     * Notes: 批量添加
     * Class: addUserDataAll
     * user: bingwoo
     * date: 2020/12/23 15:56
     */
    public function addUserDataAll($addData){
        $ret = $this->insertAll($addData);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 修改会员信息
     * Class: editUserData
     * user: bingwoo
     * date: 2020/11/26 17:47
     */
    public function editUserData($id,$saveData,$where = []){

        if($where){
            $ret = $this->where($where)->update($saveData);
        }else{
            $ret = $this->where(['uid'=>$id])->update($saveData);
        }
        if($ret){
            return true;
        }
        return false;

    }

    /**
     * Notes: 删除券码信息
     * Class: delUserData
     * user: bingwoo
     * date: 2020/11/26 17:47
     */
    public function delUserData($id){

        $ret = $this->where(['uid'=>$id])->delete();
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * Notes: 转换名称
     * Class: changeName
     * user: bingwoo
     * date: 2020/11/23 11:19
     */
    public function changeName(array $data){

        //性别
        if(isset($data['sex'])){
            $data['sex_name'] = '女';
            if($data['sex'] == 1){
                $data['sex_name'] = '男';
            }
        }
        //会员等级
        if (isset($data['grid'])){
            $data['grid_name'] = '青铜';
            if($data['grid'] == 2){
                $data['grid_name'] = '白银';
            }else if($data['grid'] == 3){
                $data['grid_name'] = '黄金';
            }else if($data['grid'] == 4){
                $data['grid_name'] = '钻石';
            }
        }
        //用户类型
        if(isset($data['is_type'])){
            $data['is_type_name'] = '普通用户';
            if($data['is_type'] == 2){
                $data['is_type_name'] = '会员用户';
            }
        }
        //会员状态
        if(isset($data['status'])){
            if($data['status'] == 1){
                $data['status_name'] = '正常';
            }else if($data['status'] == 2){
                $data['status_name'] = '<span style="color: red">已删除<span>';
            }
        }
        return $data;

    }

}