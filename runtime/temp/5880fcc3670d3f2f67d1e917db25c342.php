<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:91:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\business\businesslist.html";i:1597827154;s:76:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\layout.html";i:1597821551;s:81:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\public\head.html";i:1597028307;s:81:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\public\left.html";i:1597028892;}*/ ?>
<!DOCTYPE html>
<html no-cache>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>小码旺铺物业版管理系统后台</title>
    <link rel="stylesheet" href="__STATIC__/css/layui.css">
    <link rel="stylesheet" href="__STATIC__/css/publish.css">
    <link rel="icon" href="__STATIC__/admin/images/WechatIMG16.png" type="image/x-icon">
    <script src="__STATIC__/jquery-3.2.1.min.js"></script>
    <script src="__STATIC__/layui.js"></script>
    <script src="__STATIC__/move.js"></script>
    <script src="__STATIC__/publishImg.js"></script>
    <script src="__STATIC__/croppers.js"></script>
    <script src="__STATIC__/multiSelect.js"></script>

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
            <blockquote class="layui-elem-quote layui-text">
    <span class="layui-breadcrumb">
        <a href='#'>商户管理</a>
        <a><cite> 商户信息列表</cite></a>
    </span>
</blockquote>
<div class="layui-form-item">
</div>
<table class="layui-hide" id="test" lay-filter="testdd"></table>
<script type="text/html" id="barDemo">
    <a class="layui-btn " lay-event="edit">编辑</a>
<!--    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>-->
</script>
<script>
    layui.use(['table','layer'], function(){
        var table = layui.table;
        var layer = layui.layer;
        table.render({
            elem: '#test'
            ,url:"<?php echo url('Business/businessList'); ?>"
            ,limit:10
            ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            ,cols: [[
                {field:'bid', width:'7%', title: 'ID', sort: true}
                ,{field:'bname', width:'19%', title: '商户名称'}
                ,{field:'logo', width:'30.5%', title: '商户Logo',templet:'<div><img src="__PUBLIC__/{{d.logo}}"></div>'}
                ,{field:'create_time', width:'16%', title: '创建时间',align:'center'}
                ,{field:'update_time', width:'16%', title: '更新时间',align:'center'}
                ,{field:'right', width:'12%',toolbar: '#barDemo', title: '操作',align:'center'}
            ]]
            // ,page: true
        });
        //监听单元格编辑
        table.on('edit(testdd)', function(obj){
            var value = obj.value //得到修改后的值
                ,data = obj.data //得到所在行所有键值
                ,field = obj.field; //得到字段
            $.post(
                "<?php echo url('Business/businesseEdit'); ?>",
                {value:value,field:field,admin_id:data.admin_id},
                function(msg){
                    layer.msg(msg.font,{icon:msg.code});
                },'json')
        });
        table.on('tool(testdd)', function(obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            if (layEvent === 'edit') { //查看
                location.href="<?php echo url('Business/businessEdit'); ?>?bid="+data.bid;
            }
        })
        $('.layui-table').on('click','tr',function(){
            $(this).css('background','#ccc').siblings().css('background','#fff');
        });
    })
</script>
        </div>
    </div>

    </div>
<!-- 底部固定区域 -->
    <div class="layui-footer" style="background: #EEEEEE;text-align:center">
        Copyright  ©2020  北京银河一然商务有限公司.  All rights reserved.
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