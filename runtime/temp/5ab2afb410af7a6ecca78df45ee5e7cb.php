<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:87:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\orders\heatingadd.html";i:1596433447;s:76:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\layout.html";i:1596511401;s:81:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\public\head.html";i:1597028307;s:81:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\public\left.html";i:1597028892;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>小码旺铺物业版管理系统后台</title>
    <link rel="stylesheet" href="__STATIC__/css/layui.css">
    <link rel="icon" href="__STATIC__/admin/images/WechatIMG16.png" type="image/x-icon">
    <script src="__STATIC__/jquery-3.2.1.min.js"></script>
    <script src="__STATIC__/layui.js"></script>
    
</head>
<style type="text/css">
    .layui-table img {
        max-width: 150px;
        min-height: 100%;
    }
    .layui-table-cell{
        height: auto!important;
        white-space: normal;
    }
    .layui-body{
        flex: 1;
    }
</style>

<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <!--头部布局-->
    <style type="text/css">
    .layui-title-class{
        display: block;
        position:absolute;left:50%;
        top:0;
        height: 100%;
        width: 300px;
        transform: translate(-50%, 0%);
        color: #fff;
        font-weight: bold;
    }
</style>
<div class="layui-header">
    <img src="/WechatIMG.png" style="margin-left:20px; margin-top: 5px;" height="50" width="155">
    <a href="/admin.php/Index/Index" class="layui-title-class"><div class="layui-logo">小码旺铺会员版管理系统后台</div></a>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <!-- <ul class="layui-nav layui-layout-left">
        <li class="layui-nav-item"><a href="">邮件管理</a></li>
        <li class="layui-nav-item"><a href="">消息管理</a></li>
        <li class="layui-nav-item"><a href="">授权管理</a></li>
    </ul> -->
    <ul class="layui-nav layui-layout-right">
        <li class="layui-nav-item">
            <a href="javascript:;">
                <!-- <img src="__STATIC__/images/d833c895d143ad4b6eba587980025aafa50f06f6.jpg" class="layui-nav-img"> -->
                欢迎<?php echo \think\Session::get('admin.admin_name'); ?>登录
            </a>
        </li>
        <li class="layui-nav-item"><a href="<?php echo url('Login/logout'); ?>">注销</a></li>
    </ul>
</div>

    <!--左边布局-->
    <style>
    .leftchecked {
        color: #00d6be !important;
        font-weight: bold !important;
    }

    .layui-box1 {
        text-align: center;
        padding: 35px 0;
    }

    .layui-p-b {
        display: flex;
    }

    .layui-nav-itemed>a {
        color: #00d6be !important;
    }
    .layui-aaa img{
        width: 15px;
        height: 15px;
        margin-right:8px;
        vertical-align: middle;
    }

    .tab_icon {}

    .layui-box1 img{
        border-radius: 50%;
    }
    .layui-side-scroll{
      height:90%
    }
</style>


<div class='layui-p-b'>
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree nav-ul" lay-filter="test">
                <?php if(is_array($AllMenu) || $AllMenu instanceof \think\Collection || $AllMenu instanceof \think\Paginator): $k = 0; $__LIST__ = $AllMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;
                $i=0;
                $i++;
                if( strtolower(request()->controller()) == strtolower(ltrim( $v['node_url'] ,'/' )) ){
                    echo '<li class="layui-nav-item layui-nav-itemed">';
                }else{
                    echo '<li class="layui-nav-item ">';
                }
                ?>
                <a class="layui-aaa" href="javascript:;"><span hidden="hidden" class="imgid"><?php echo $k; ?></span><img src="" alt=""><?php echo $v['node_name']; ?></a>
                <dl class="layui-nav-child">
                    <?php
                    if( isset($v['son']) ){ if(is_array($v['son']) || $v['son'] instanceof \think\Collection || $v['son'] instanceof \think\Paginator): $i = 0; $__LIST__ = $v['son'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;
                        $action = explode( '/' , trim($vv['node_url'] , '/' )  );
                        $action_url = strtolower(array_pop( $action ));
                        if( strtolower(request()->action()) ==  $action_url ){
                            echo '<dd>
                            <a class="leftchecked" href="'.url($vv['node_url']).'">'.$vv['node_name'].'</a></dd>';
                        }else{
                            echo '<dd><a href="'.url($vv['node_url']).'">'.$vv['node_name'].'</a></dd>';
                        }
                    endforeach; endif; else: echo "" ;endif; } ?>
                </dl>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <script type="text/javascript">

        $(function () {

            $('.imgid').each(function(i,elem){
                if($(elem).text().length<2){
                    $(elem).text('0'+$(elem).text());
                }
                $(elem).next().attr('src','__IMG__/tab_'+$(elem).text()+'.png')
            });
            $('.layui-nav-itemed').each(function(i,elem){      //一参:下标
                var len = $(elem).find('img').attr('src').substr(11,2);
                $(elem).find('img').attr('src','__IMG__/tab_'+len+'_active.png');        //参:每个元素
            });
             $(".layui-nav").on("click","li",function () {

                var len = $(this).find('img').attr('src').substr(11,2);
                if($(this).hasClass('layui-nav-itemed')){
                    // $(this).siblings().attr('class','layui-nav-item');
                    $('.nav-ul>li').each(function(i,elem){
                        console.log(111)
                        // console.log($(elem)
                        $(elem).attr('class','layui-nav-item');
                        var n = $(elem).find('img').attr('src').substr(11,2);
                        $(elem).find('img').attr('src','__IMG__/tab_'+n+'.png');

                      });
                    $(this).find('img').attr('src','__IMG__/tab_'+len+'_active.png')
                    $(this).attr('class','layui-nav-item layui-nav-itemed');
                }else{
                    $(this).find('img').attr('src','__IMG__/tab_'+len+'.png')
                }
             })
        });
    </script>

    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            <form class="layui-form">


    <div class="layui-form-item">
        <label class="layui-form-label">收款标题<span style="color: red;">*</span></label>
        <div class="layui-input-block">
            <input type="text" name="title"  autocomplete="off" placeholder="请输入收款标题" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">收费小区<span style="color: red;">*</span></label>
        <div class="layui-input-block">
          <select name="dis" lay-filter="filter" id="names">
            <option value="">请选择小区</option>
            <?php if(is_array($dis) || $dis instanceof \think\Collection || $dis instanceof \think\Paginator): $i = 0; $__LIST__ = $dis;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
      </div>


      <div class="layui-form-item">
         <label class="layui-form-label">收费区</label>
         <div class="layui-input-block">
            <?php if(is_array($area) || $area instanceof \think\Collection || $area instanceof \think\Paginator): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$area): $mod = ($i % 2 );++$i;?>
            <input type="checkbox" lay-filter="filter" value="<?php echo $area; ?>" name="" title="<?php echo $area; ?>">
            <?php endforeach; endif; else: echo "" ;endif; ?>
         </div>
       </div>

       <div class="layui-form-item">
           <label class="layui-form-label">楼号</label>
           <div class="layui-input-block">
               <input type="text" name="home_code"  autocomplete="off" placeholder="输入楼号  例：1" class="layui-input">
           </div>
       </div>
    <div class="layui-form-item">
        <label class="layui-form-label">收费年<span style="color: red;">*</span></label>
        <div class="layui-input-block">
               <input type="text" name="paytime"  autocomplete="off" placeholder="输入收费年    例：2020" class="layui-input">
           </div>
    </div>  



   <!--  <div class="layui-form-item">
        <label class="layui-form-label">供暖费/m²</label>
        <div class="layui-input-block">
            <input type="text" name="fee"  autocomplete="off" placeholder="请输入物业费    例：100/100.00" class="layui-input">
        </div>
    </div> -->
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">供暖：<span style="color: red;">*</span></label>
            <div class="layui-input-inline" style="width: 100px;">
                <input type="text" name="fee" value="" placeholder="请输入价格"  autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid">X</div>
            <div class="layui-input-inline" style="width: 80px;">
                <input type="text" name="" value="房屋面积" style="border:0;outline:0;" disabled="disabled" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid">X</div>
            <div class="layui-input-inline" style="width: 100px;">
                <input type="text" name="cost" value="1" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid">系数</div>
        </div>
    </div>
    

    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">缴费周期<span style="color: red;">*</span></label>
            <div class="layui-input-inline" style="width: 100px;">
                <input type="text" name="start_at" value=""  autocomplete="off" id="start_at"  lay-verify="required" class="layui-input">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline" style="width: 100px;">
                <input type="text" name="end_at" value="" autocomplete="off" id="end_at"  lay-verify="required" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea placeholder="请输入备注" name="content" class="layui-textarea" ></textarea>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="*">立即发布</button>
            <button type="reset" id='up' class="layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>

<!-- 示例-970 -->
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    $(function(){
        layui.use(['form','layer','upload','laydate'], function(){
            var form = layui.form;
            var layer = layui.layer;
             var laydate = layui.laydate;
            	$('#up').click(function(){
            	    location.href="<?php echo url('Orders/heatingList'); ?>";
            	});

                //执行一个laydate实例
                  laydate.render({
                    elem: '#test1' //指定元素
                  });
                  //常规用法
                  laydate.render({
                      elem: '#start_at'
                  });
                  laydate.render({
                      elem: '#end_at'
                  });
                var userName=null;  //定义一个空值
                form.on('select(filter)', function(data){
                    userName=data.elem[data.elem.selectedIndex].text;  //取选中下拉框的文本并赋值给userName
                });
                var qu ='';  //定义一个空值
                form.on('checkbox(filter)', function(data){
                    qu += data.value+'+';
                });   
            //监听提交
            form.on('submit(*)',function(data){
                var info = data.field;
                info.complex=qu;
                info.dname=userName;
                $.post(
                        "<?php echo url('Orders/heatingAdd'); ?>",
                        info,
                        function(msg){

                            if(msg.code==1){
                                layer.msg(msg.font, {
                                  icon: msg.code,
                                  time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                }, function(){
                                  location.href="<?php echo url('Orders/heatingList'); ?>";
                                });   
                            } else {
                                layer.msg(msg.font,{icon:msg.code});
                            }


                            console.log(msg);
                        },'json'
                )
                return false;
            })
        });
    })

</script>

        </div>
    </div>

    </div>
    <div class="layui-footer">
        <!-- 底部固定区域 -->
        Copyright©2020北京银河一然商务有限公司.All rights reserved.
      </div>
</div>
<script>
    //JavaScript代码区域
    layui.use('element', function(){
        var element = layui.element;

    });
</script>
</body>
</html>
<script src="__STATIC__/public.js"></script>