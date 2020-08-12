<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:95:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\pay_record\index.html";i:1591780899;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1592206685;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1591870701;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1592371665;}*/ ?>
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
        color: #00d6be!important;
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
<script type="text/javascript">
     
    $(function () {
        $(".layui-nav").on("click","li",function () {
           console.log(1);
        })
    });
</script>
    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            <div class="layui-form-item">
    <span class="layui-breadcrumb">
        <a href='#'>缴费管理</a>
        <a><cite>物业收费</cite></a>
    </span>
</div>
<div class="layui-form layui-card-header layuiadmin-card-header-auto" style="height: auto">
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">ID</label>
            <div class="layui-input-block">
                <input type="text" name="id"  autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">支付订单号</label>
            <div class="layui-input-block">
                <input type="text" name="payment_sn"  placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">支付方式</label>

            <div class="layui-input-block">
                <select name="pay_type">
                    <option value=""></option>
                    <?php if(is_array($paytypes) || $paytypes instanceof \think\Collection || $paytypes instanceof \think\Paginator): $i = 0; $__LIST__ = $paytypes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$paytype): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $paytype['val']; ?>"><?php echo $paytype['name']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>

    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">费用类别</label>
            <div class="layui-input-block">
                <select name="type">
                    <option value=""></option>
                    <?php if(is_array($types) || $types instanceof \think\Collection || $types instanceof \think\Paginator): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $type['val']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">支付状态</label>
            <div class="layui-input-block">
                <select name="status">
                    <option value=""></option>
                    <?php if(is_array($statuss) || $statuss instanceof \think\Collection || $statuss instanceof \think\Paginator): $i = 0; $__LIST__ = $statuss;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$status): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $status['val']; ?>"><?php echo $status['name']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <button class="layui-btn layuiadmin-btn-order" lay-submit lay-filter="submit">
                <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
            </button>
        </div>
    </div>
</div>
<table class="layui-hide" id="order" lay-filter="order"></table>
<script>
    layui.use(['table','layer','form'], function(){

        var table = layui.table;
        var layer = layui.layer;
        var form = layui.form;

        table.render({
            elem: '#order',
            url:'<?php echo url("index"); ?>',
            limit: '__PAGESIZE__',
            cellMinWidth: 50, //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            defaultToolbar: [],
            cols: [[
                // {checkbox: true, fixed: true},
                {field:'id',sort: true, align: 'center', title: 'ID',width:80},
                // {field:'order_id',sort: true, align: 'center', title: '收费单ID',width:80},
                {field:'payment_sn',sort: true, align: 'center', title: '支付订单号'},
                {field:'amount', align: 'center', title: '支付金额'},
                {field:'user', align: 'center', title: '用户',
                    templet: function(d){
                        if (d.operator_id>0)
                        {
                            return '后台操作:['+d.operator_id+']'+d.admin_name;
                        }
                        else
                        {
                            return '用户支付:['+d.operator_id+']'+d.admin_name;
                        }
                    }
                },
                {field:'type', align: 'center', title: '费用类别',
                    templet: function(d){
                        return d.type==0?'<button class="layui-btn layui-btn-normal layui-btn-xs">物业缴费</button>':'<button class="layui-btn layui-btn-warm layui-btn-xs">供暖缴费</button>';
                    }
                },
                {field:'pay_type', align: 'center', title: '支付方式',
                    templet: function(d){
                        switch (d.pay_type)
                        {
                            case 0:
                                return '';
                            case 1:
                                return '<button class="layui-btn layui-btn-radius layui-btn-warm layui-btn-xs">现金支付</button>';
                            case 2:
                                return '<button class="layui-btn layui-btn-radius layui-btn-xs">POSE机支付</button>';
                            case 3:
                                return '<button class="layui-btn layui-btn-radius layui-btn-normal layui-btn-xs">微信支付</button>';
                        }

                    }
                },
                {field:'status', align: 'center', title: '状态',
                    templet: function(d){
                        return d.status==1?'<button class="layui-btn layui-btn-normal layui-btn-xs">已支付</button>':'<button class="layui-btn layui-btn-warm layui-btn-xs">待支付</button>';
                    }
                },
                {field:'remark', align: 'center', title: '备注'},
                {field:'pay_at',align: 'center', title: '支付时间'},
                {field:'ctime',align: 'center', title: '创建时间'},
                // {field:'right',toolbar: '#bar',align: 'center', title:'操作'},
            ]],
            page: true
        });


        form.on('submit(submit)',function(data){
            table.reload('order', {
                page: {
                    curr: 1 //重新从第 1 页开始
                }
                ,where: data.field
            }, 'data');
        });

    });
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