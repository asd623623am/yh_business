<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:93:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\phone\phoneadd.html";i:1590648225;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1592206685;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1591870701;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1592206641;}*/ ?>
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
    <div class="layui-header">
    <a href="/admin.php/Index/Index"><div class="layui-logo">小码旺铺物业管理系统后台</div></a>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <!-- <ul class="layui-nav layui-layout-left">
        <li class="layui-nav-item"><a href="">邮件管理</a></li>
        <li class="layui-nav-item"><a href="">消息管理</a></li>
        <li class="layui-nav-item"><a href="">授权管理</a></li>
    </ul> -->
    <ul class="layui-nav layui-layout-right">
        <li class="layui-nav-item">
            <a href="javascript:;">
                <img src="__STATIC__/images/d833c895d143ad4b6eba587980025aafa50f06f6.jpg" class="layui-nav-img">
                欢迎<?php echo \think\Session::get('admin.admin_name'); ?>登录
            </a>
        </li>
        <li class="layui-nav-item"><a href="<?php echo url('Login/logout'); ?>">注销</a></li>
    </ul>
</div>
    <!--左边布局-->
    <style>
    .leftchecked{
        color: #ff33eb!important;
        font-weight: bold!important;
    }
    .layui-box1{
        text-align: center;
        padding: 20px 0;
    }
    .layui-p-b{
        display: flex;
    }

</style>

<div class='layui-p-b'>

<div class="layui-side layui-bg-black">
    <div class="layui-box1">
        <a href="/admin.php/Index/Index">
            <img src="/WechatIMG16.png" height="80" width="80">
        </a>
    </div>
    <div class="layui-side-scroll">
        <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
        <ul class="layui-nav layui-nav-tree"  lay-filter="test">

            <!--<li class="layui-nav-item layui-nav-itemed">-->
                <!--<a class="" href="javascript:;">权限节点管理</a>-->
                <!--<dl class="layui-nav-child">-->
                    <!--<dd><a href="<?php echo url('Power/poweradd'); ?>">节点添加</a></dd>-->
                    <!--<dd><a href="<?php echo url('Power/powerList'); ?>">节点列表展示</a></dd>-->
                <!--</dl>-->
            <!--</li>-->

            <?php if(is_array($AllMenu) || $AllMenu instanceof \think\Collection || $AllMenu instanceof \think\Paginator): $i = 0; $__LIST__ = $AllMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;
                if( strtolower(request()->controller()) == strtolower(ltrim( $v['node_url'] ,'/' )) ){
                    echo '<li class="layui-nav-item layui-nav-itemed">';
                }else{
                    echo '<li class="layui-nav-item">';
                }
                ?>
                <a class="" href="javascript:;"><?php echo $v['node_name']; ?></a>
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

    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            <form class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">电话名称</label>
        <div class="layui-input-block">
            <input type="text" name="phone_name"  autocomplete="off" placeholder="请输入名称" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">电话号</label>
        <div class="layui-input-block">
            <input type="text" name="phone"  autocomplete="off" placeholder="请输入电话" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="*">立即添加</button>
        </div>
    </div>
</form>

<!-- 示例-970 -->
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    $(function(){
        layui.use(['form','layer'], function(){
            var form = layui.form;
            var layer = layui.layer;

            //监听提交
            form.on('submit(*)',function(data){
                $.post(
                        "<?php echo url('Phone/phoneAdd'); ?>",
                        data.field,
                        function(msg){

                            if(msg.code==1){
                                layer.msg(msg.font, {
                                  icon: msg.code,
                                  time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                }, function(){
                                  location.href="<?php echo url('Phone/phoneList'); ?>";
                                });   
                            } else {
                                layer.msg(msg.font,{icon:msg.code});
                            }
                            
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