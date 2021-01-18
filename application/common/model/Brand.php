<?php
namespace  app\common\model;

use think\Controller;
use think\Db;
use think\Model;

/**
 * 内容管理
 */
class Brand extends Model {

    public function brandAdd(){
        if(check()){
            # 检查名字是否重复
            $data=input('post.');
            if(empty($data)){
                exit('非法操作此页面');
            }
            $info=model('Brand')->allowField(true)->save($data);
            if($info){
                $this -> addLog('发布新的公告通知');
                win('添加成功');
            }else{
                fail('添加失败');
            }
        }else{
            return view();
        }
    }

    public function checkName(){
    $where=[
        'brand_id'=>6
    ];
    $arr=[
        'brand_logo'=>'dasfsd',
        'brand_describe'=>'哈哈哈',
        'brand_show'=>1
    ];

    $info = Db::table('shop_brand')->where($where)->setField($arr);
    dump($info);

   }

    public function brandList(){
        if( request() -> isAjax() ){

            $page=input('get.page');
            if(empty($page)){
                exit('非法操作此页面');
            }
            $limit=input('get.limit');
            if(empty($limit)){
                exit('非法操作此页面');
            }
            // ->order('brand_sort','asc')
            $brand_info=model('Brand')->order('brand_id','desc')->page($page,$limit)->select()->toArray();
            foreach ($brand_info as $key => $value) {
                if ($value['brand_show'] == 1) {
                    $brand_info[$key]['brand_shows'] = '发布';
                }else{
                    $brand_info[$key]['brand_shows'] = '不发布';
                }
            }
            $count=model('Brand')->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$brand_info];
            echo json_encode($info);
            exit;

        }else{
            return view();
        }
    }

    public function brandUpload(){
        // 获取表单上传文件 例如上传了001.jpg
          $file = request()->file('file');
            if(empty($file)){
                exit('非法操作此页面');
            }
        //动到框架应用根目录/public/uploads/ 目录下
         $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
               echo json_encode(['font'=>'上传成功','code'=>1,'src'=>$info->getSaveName()]);

        }else{
            // 上传失败获取错误信息
              fail($file->getError());
        }

    }

    public function brandDel(){
        $brand_id=input('post.brand_id');
        if(empty($brand_id)){
            exit('非法操作此页面');
        }
        $where=[
            'brand_id'=>$brand_id
        ];
        //删除
        $res=model('Brand')->where($where)->delete();
        if($res){
             $this -> addLog('删除了一个公告通知');
            win('删除成功');
        }else{
            fail('删除失败');
        }
    }

    public function brandUpdateInfo(){
        $brand_id=input('get.brand_id');

        if(empty($brand_id)){
            exit('非法操作此页面');
        }
        $where=[
            'brand_id'=>$brand_id
        ];
        $data=model('Brand')->where($where)->find();
        $data['brand_describe'] = str_replace("<br/>", "\r\n", $data['brand_describe']);
        $page = input('get.page');
        if(empty($page)){
            exit('非法操作此页面');
        }
        $this->assign('page',$page);
        $this->assign('data',$data);
        return view();

    }

    public function brandUp(){
        if(check()){
            $data=input('post.');
            if(empty($data)){
                exit('非法操作此页面');
            }

            $where=[
                'brand_id'=>$data['brand_id']
            ];
            $arr=[
                'brand_logo'=>$data['brand_logo'],
                'brand_describe'=>$data['brand_describe'],
                'brand_show'=>$data['brand_show'],
                'brand_title'   => $data['brand_title'],
            ];

            $info = Db::table('brand')->where($where)->setField($arr);
            if($info){
                 $this -> addLog('修改了公告通知');
                win('修改成功');
            }else{
                fail('修改失败');
            }
        }
    }

    public function bannerAdd()
    {
        if(check()){
                   $data=input('post.');
                   if(empty($data)){
                       fail('非法操作此页面');
                   }

                     // $data_info=model('Banner')->select()->toArray();
                     $count=model('Banner')->count();
                     if ($count>=3) {
                         fail('只能添加三个图片');
                     }

                     $data['status'] = 1;
                     $data['ctime'] = time();
                     $info=model('Banner')->allowField(true)->save($data);
                     if($info){
                         $this -> addLog('添加了一个新的轮播图');
                         win('添加成功');
                     }else{
                         fail('添加失败');
                     }
                     /*if (empty($data_info)) {
                         $data['status'] = 1;
                         $data['ctime'] = time();
                         $info=model('Banner')->allowField(true)->save($data);
                         if($info){
                             win('添加成功');
                         }else{
                             fail('添加失败');
                         }
                     } else {

                        $temp = $data_info[0];
                        $where=[
                            'banner_id'=>$temp['banner_id']
                        ];
                        $arr=[
                            'banner_url'=>$data['banner_url'],
                            'status'=>1,
                            'ctime'=>time()
                        ];

                        $info = Db::table('banner')->where($where)->setField($arr);
                        if($info){
                            win('添加成功');
                        }else{
                            fail('添加失败');
                        }

                     }*/

               }else{

                   return view();
               }
    }

    public function bannerUpload(){
        // 获取表单上传文件 例如上传了001.jpg
          $file = request()->file('file');
            if(empty($file)){
                exit('非法操作此页面');
            }
        //动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
               echo json_encode(['font'=>'上传成功','code'=>1,'src'=>$info->getSaveName()]);

        }else{
            // 上传失败获取错误信息
              fail($file->getError());
        }

    }

    public function bannerList()
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
            // ->order('brand_sort','asc')
            $brand_info=model('Banner')->order('banner_id','desc')->page($page,$limit)->select();
            $arr = [];
            foreach ($brand_info as $key => $value) {
               $arr = explode("#",$value->banner_url);
               unset($arr[0]);
               $brand_info[$key]['banner_url'] = $arr;
            }

            $count=model('Banner')->count();
            $info=['code'=>0,'msg'=>'','count'=>$count,'data'=>$brand_info];
            echo json_encode($info);
            exit;

        }else{
            return view();
        }
    }

    public function bannerDel()
    {
        $banner_id=input('post.banner_id');
        if(empty($banner_id)){
            exit('非法操作此页面');
        }
        $where=[
            'banner_id'=>$banner_id
        ];
        //删除
        $res=model('Banner')->where($where)->delete();
        if($res){
             $this -> addLog('删除了一个轮播图');
            win('删除成功');
        }else{
            fail('删除失败');
        }
    }

}