<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\house\index.html";i:1591174994;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1592206685;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1591870701;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1592371665;}*/ ?>
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
        <a href='#'>房屋管理</a>
        <a><cite> 房屋列表</cite></a>
    </span>
</div>
<div class="search-table layui-form">
    <div class="layui-inline">
        <select name="type"  id="search-district" lay-search="">
            <option value=""></option>
            <?php if(is_array($districts) || $districts instanceof \think\Collection || $districts instanceof \think\Paginator): $i = 0; $__LIST__ = $districts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$district): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo $district['id']; ?>"><?php echo $district['name']; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>
    <div class="layui-inline">
        <select name="type"  id="search-key" >
            <option value="owner" selected>业主</option>
            <option value="id">ID</option>
            <option value="tel">联系方式</option>
        </select>
    </div>
    <div class="layui-inline">
        <input class="layui-input" name="value" id="search-value" autocomplete="off">
    </div>
    <button id="search-btn" class="layui-btn" data-type="reload">搜索</button>
</div>
<table class="layui-hide" id="house" lay-filter="house"></table>
<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="importData" id="importData">导入数据</button>
        <button class="layui-btn layui-btn-sm" lay-event="download">下载模板</button>
        <button class="layui-btn layui-btn-sm" lay-event="insert">新增房屋</button>
    </div>
</script>
<script type="text/html" id="bar">
    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<script>
    layui.use(['table','upload','layer'], function(){

        var table = layui.table;
        var layer = layui.layer;
        var upload = layui.upload;//主要是这个

        table.render({
            elem: '#house',
            url:'<?php echo url("House/index"); ?>',
            limit: '__PAGESIZE__',
            cellMinWidth: 50, //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            toolbar:'#toolbar',
            cols: [[
                // {checkbox: true, fixed: true},
                {field:'id',sort: true, align: 'center', title: 'ID'},
                {field:'owner', align: 'center', title: '业主'},
                {field:'tel', align: 'center', title: '联系方式'},
                {field:'district', align: 'center', title: '小区名称'},
                {field:'complex', align: 'center', title: '区'},
                {field:'building', align: 'center', title: '楼'},
                {field:'unit', align: 'center', title: '单元'},
                {field:'room', align: 'center', title: '房间号'},
                {field:'heating_cost',align: 'center', title: '供暖费/m²'},
                {field:'property_fee',align: 'center', title: '物业费/m²'},
                {field:'area',align: 'center', title: '面积/m²'},
                {field:'check_in_at',align: 'center', title: '入住时间'},
                {field:'ctime',sort: true,align: 'center', title: '创建时间'},
                {field:'right',toolbar: '#bar',align: 'center', title:'操作'},
            ]],
            page: true
        });
        var uploadInst = upload.render({
            elem: '#importData', //绑定元素
            url: '<?php echo url("House/importData"); ?>', //上传接口
            size: '3072',
            accept: "file",
            exts: 'xls|xlsx|xlsm|xlt|xltx|xltm',
            data: {},
            auto: true,
            bindAction: '#btnUpload',                    //auto为false时，点击触发上传
            multiple: false,                             //多文件上传
            //, number: 100                               //multiple:true时有效
            done: function (res) {                      //传输完成的回调
                layer.msg(res.msg);
                table.reload('house', {page: {curr: 1},where: {}}, 'data');
            },
            error: function () {                         //传输失败的回调
                //请求异常回调
            }
        });
        //头工具栏事件
        table.on('toolbar(house)', function(obj){
            var checkStatus = table.checkStatus(obj.config.id);
            switch(obj.event){
                case 'import':
                    var data = checkStatus.data;
                    layer.alert(JSON.stringify(data));
                    break;
                case 'insert':
                    location.href="<?php echo url('form'); ?>?id=0";
                    break;
                case 'download':
                    location.href="<?php echo url('downloadExcel'); ?>";
                    break;
                //自定义头工具栏右侧图标 - 提示
                case 'LAYTABLE_TIPS':
                    layer.alert('这是工具栏右侧自定义的一个图标按钮');
                    break;
            };
        });

        //删除和修改
        table.on('tool(house)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的DOM对象

            if(layEvent === 'edit'){ //查看
                location.href="<?php echo url('form'); ?>?id="+data.id;
            } else if(layEvent === 'del'){ //删除
                layer.confirm('确定要删除该房屋信息吗?', function(index){
                    $.post("<?php echo url('del'); ?>",{id:data.id},function(data){
                        layer.msg(data.msg);
                        if(data.code==0){
                            table.reload('house', {page: {curr: 1},where: {}}, 'data');
                        }
                    },'json');
                });
            }
        });

        $('#search-btn').on('click', function(){
            table.reload('house', {
                page: {
                    curr: 1 //重新从第 1 页开始
                }
                ,where: {
                    keyword: $('#search-value').val(),
                    key:$('#search-key').val(),
                    district_id:$("#search-district").val()
                }
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