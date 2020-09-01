<?php
namespace app\admin\controller;

/**
 * Notes:PHPExcel
 * Created by PhpStorm.
 * user: bingwoo
 * date: 2020/8/11  
 * time: 下午 01:38
 */
class PHPExcel extends Common{


    /**
     * Notes: 下载门店模板
     * Class: downloadModel
     * user: bingwoo
     * date: 2020/9/1 13:55
     */
    public function downloadStoreModel(){

        //系统域名
        $realm_name = 'business.com';
        //文件名称
        $fieldname = '门店模板.xlsx';
        //图片地址
        $url = 'http://'.$realm_name.'/PHPExcel/'.$fieldname;
        win($url);
    }

}
