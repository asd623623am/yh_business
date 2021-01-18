<?php
namespace  app\common\model;
use think\Model;


/**
 * Created: by PhpStorm.
 * package: app\common\model
 * Class: Barcodegen
 * user: bingwoo
 * date: 2020/12/28 17:14
 */
class Barcodegen extends Model{


    /**
     * Notes: 生成条形码
     * Class: barCode
     * user: bingwoo
     * date: 2020/12/28 17:50
     */
    public function barCode($text){

        $file_dir = 'upload/Bar/'.date("Y-m-d",time());  //文件路径
        if(!file_exists($file_dir)) {
            //判断文件是否存在
            mkdir($file_dir,0777,true);                       //不存在则生成
        }
        $imgUrl = $file_dir . '/' . time() . '.png';        //图片路径
        vendor('barcodegen.class.BCGcode128');              //载入依赖包
        vendor('barcodegen.class.BCGDrawing');
        $color_white = new \BCGColor(255, 255, 255);           //定义颜色
        $drawing = new \BCGDrawing('', $color_white);           //赋值颜色
        $code = new \BCGcode128();
        $code->setThickness(30);                 //条码厚度
        $code->parse($text);              //条形码内容
        $drawing->setBarcode($code);
        $drawing->draw();                        //渲染图片
        $drawing->finish($drawing::IMG_FORMAT_PNG);          //生成图片
        $out_arr['code'] = '000000';
        $out_arr['url'] = $imgUrl;
        return $out_arr;
    }
}