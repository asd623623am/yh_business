<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:102:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\system\systemupdateinfo.html";i:1593569819;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1592206685;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1593321400;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1593569180;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>【小码旺铺】 -- 物业后台</title>
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
    <a href="/admin.php/Index/Index" class="layui-title-class"><div class="layui-logo">小码旺铺物业版管理系统后台</div></a>
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
</style>


<div class='layui-p-b'>

    <div class="layui-side layui-bg-black">
        <div class="layui-box1">
            <a href="/admin.php/Index/Index">
                <!-- <img src="/WechatIMG16.png" height="80" width="80"> -->
                <img src="__PUBLIC__/<?php echo $images; ?>" height="80" width="80">
            </a>
        </div>
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree" lay-filter="test">

                <!--<li class="layui-nav-item layui-nav-itemed">-->
                <!--<a class="" href="javascript:;">权限节点管理</a>-->
                <!--<dl class="layui-nav-child">-->
                <!--<dd><a href="<?php echo url('Power/poweradd'); ?>">节点添加</a></dd>-->
                <!--<dd><a href="<?php echo url('Power/powerList'); ?>">节点列表展示</a></dd>-->
                <!--</dl>-->
                <!--</li>-->

                <?php if(is_array($AllMenu) || $AllMenu instanceof \think\Collection || $AllMenu instanceof \think\Paginator): $k = 0; $__LIST__ = $AllMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;
                $i=0;
                $i++;
                if( strtolower(request()->controller()) == strtolower(ltrim( $v['node_url'] ,'/' )) ){
                    echo '<li class="layui-nav-item layui-nav-itemed">';
                }else{
                    echo '<li class="layui-nav-item">';
                }
                ?>

                <a class="layui-aaa" href="javascript:;"><img src="__IMG__/tab_<?php echo $k; ?>.png"
                        alt=""><?php echo $v['node_name']; ?></a>
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

            // $('.layui-aaa').click(function (e) {
            //     console.log($(this).parent().hasClass('layui-nav-itemed'))

            //     if ($(this).parent().hasClass('layui-nav-itemed')) {
            //         // console.log(true)
            //         $(this).css('color', 'rgba(255,255,255,.7)')
            //         $(this).parent().removeClass('layui-nav-itemed')
            //     }
            //     else {
            //         // console.log(false)
            //         $('.layui-nav-item').removeClass('layui-nav-itemed')
            //         $(this).parent().addClass('layui-nav-itemed')
            //         console.log($(this).parent().hasClass('layui-nav-itemed'))
            //         $(this).css('color', '#00d6be')
            //     }
            // })
            // 
            $('.layui-nav-itemed').each(function(i,elem){      //一参:下标
                var len = $(elem).find('img').attr('src').substr(11,1);
                $(elem).find('img').attr('src','/image/tab_'+len+'_active.png');        //参:每个元素
            });
             $(".layui-nav").on("click","li",function () {
                var len = $(this).find('img').attr('src').substr(11,1);
                if($(this).hasClass('layui-nav-itemed')){
                    console.log(true);
                    $(this).find('img').attr('src','/image/tab_'+len+'_active.png')
                }else{
                    $(this).find('img').attr('src','/image/tab_'+len+'.png')
                }
             })
            
        });
    </script>
    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            <form class="layui-form">
    <div class="layui-form-item">
        <div class="layui-inline">
            <input type="hidden" name="system_id" value="<?php echo $data['system_id']; ?>">
            <input type="hidden" value="<?php echo $data['company_logo']; ?>" id="logo" name="company_logo">
            <label class="layui-form-label">公司logo</label>
            <button type="button" class="layui-btn" id="myload">
                <i class="layui-icon">&#xe67c;</i>上传图片
            </button>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">公司名称</label>
            <div class="layui-input-inline">
                <input type="tel" name="company_name" value="<?php echo $data['company_name']; ?>" autocomplete="off" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">版权信息</label>
            <div class="layui-input-inline">
                <input type="tel" name="system_data" value="<?php echo $data['system_data']; ?>" autocomplete="off" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">系统名称</label>
            <div class="layui-input-inline">
                <input type="tel" name="system_name" value="<?php echo $data['system_name']; ?>" autocomplete="off" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="*">立即修改</button>
            <button type="reset" id='up' class="layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>

<!-- 示例-970 -->
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    $(function(){
        layui.use(['form','layer','upload'], function(){
            var form = layui.form;
            var layer = layui.layer;
            var upload = layui.upload;

            //文件上传
            var uploadInst = upload.render({
                elem: '#myload' //绑定元素
                ,url: '<?php echo url("System/systemUpload"); ?>' //上传接口
                ,done: function(res){
                    //上传完毕回调
                    layer.msg(res.font,{icon:res.code});
                    if(res.code==1){
                        $('#logo').val(res.src);
                    }
                }
                ,error: function(){
                    //请求异常回调
                }
            });

            $('#up').click(function(){
                location.href="<?php echo url('System/systemList'); ?>";
            });

            //监听提交
            form.on('submit(*)',function(data){

                layer.confirm('您确认修改吗？', function(index){
                        
                       $.post(
                               "<?php echo url('System/systemUpdate'); ?>",
                               data.field,
                               function(msg){

                                   if(msg.code==1){
                                       layer.msg(msg.font, {
                                         icon: msg.code,
                                         time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                       }, function(){
                                         location.href="<?php echo url('System/systemList'); ?>";
                                       });   
                                   } else {
                                       layer.msg(msg.font,{icon:msg.code});
                                   }


                               },'json'
                       )
                       

                });
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