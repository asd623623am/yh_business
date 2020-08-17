<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:92:"E:\PHPserver\wwwroot\default\yh_business\public/../application/admin\view\data\datalist.html";i:1597301811;s:85:"E:\PHPserver\wwwroot\default\yh_business\public/../application/admin\view\layout.html";i:1597308289;s:90:"E:\PHPserver\wwwroot\default\yh_business\public/../application/admin\view\public\head.html";i:1597301811;s:90:"E:\PHPserver\wwwroot\default\yh_business\public/../application/admin\view\public\left.html";i:1597301811;}*/ ?>
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
            <div class="layui-form-item">
    <span class="layui-breadcrumb">
        <a href='#'>数据报表</a>
        <a><cite> 数据列表</cite></a>
    </span>
</div>

<div class="search-table layui-form">
    <div class="layui-inline">
        <select name="dis"  id="search-district" lay-search="">
            <option value="">请选择小区</option>
            <?php if(is_array($dis) || $dis instanceof \think\Collection || $dis instanceof \think\Paginator): $i = 0; $__LIST__ = $dis;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>
    <div class="layui-inline">
        <select name="qu"  id="search-key" >
            <option value="" >请选择区</option>
            <?php if(is_array($area) || $area instanceof \think\Collection || $area instanceof \think\Paginator): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$area): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo $area; ?>"><?php echo $area; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>
    <div class="layui-inline">
        <select name="type"  id="search-key" >
            <option value="">请选择收费类型</option>
            <option value="0">物业</option>
            <option value="1">供暖</option>
        </select>
    </div>



        <div class="layui-inline">
            <div class="layui-input-inline" style="width: 150px;">
                <input type="text" name="start_at" value="" placeholder="请输入开始时间"  autocomplete="off" id="start_at"   class="layui-input">
            </div>
            <div class="layui-input-inline" style="width: 150px;">
                <input type="text" name="end_at" value="" placeholder="请输入结束时间" autocomplete="off" id="end_at"  class="layui-input">
            </div>
        </div>


    <button class="layui-btn" lay-submit lay-filter="*">搜索</button>
</div>
<button id='send' class="layui-btn layui-btn-normal marginT" lay-submit lay-filter="*">导出台账</button>
<button id='senddatainfo' class="layui-btn layui-btn-normal marginT" lay-submit lay-filter="*">导出月/年报表</button>
<table class="layui-hide" id="test" lay-filter="testdd"></table>
<!-- <script type="text/html" id="barDemo">
	<a class="layui-btn layui-btn-xs" lay-event="pos">查看</a>
</script> -->
<script>
    layui.use(['table','layer','laydate'], function(){
        var table = layui.table;
        var layer = layui.layer;
        var form = layui.form;
        var laydate = layui.laydate;

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

        table.render({
            elem: '#test'
            ,url:"<?php echo url('Data/dataList'); ?>"
            ,limit:10
            ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            ,cols: [[
                { width:100, title: '序号', type:'numbers'}
                ,{field:'user', width:100, title: '业主姓名'}
                ,{field:'area', width:50, title: '区'}
                ,{field:'area_num', width:150, title: '楼/单元/户号'}
                ,{field:'volume', width:100, title: '面积'}
                ,{field:'status', width:100, title: '缴费状态'}
                // ,{field:'status',width:200, title: '缴费状态',templet: function(d){
                //     if (d.status == '未缴费') {
                //         return '<span style="color:#000">'+d.status+'</span>';
                //     } else if (d.status == '已缴费') {
                //         return '<span style="color:#22DD48">'+d.status+'</span>';
                //     } else if (d.status == '已退款') {
                //         return '<span style="color:#FF0000">'+d.status+'</span>';
                //     }
                // }}
                
                ,{field:'deposit_money', width:100, title: '缴费金额'}
                ,{field:'net_money', width:100, title: '实收金额'}
                ,{field:'compensate', width:100, title: '赔偿'}
                ,{field:'pay_times', width:180, title: '缴费时间'}
                // ,{field:'month', width:100, title: '交费月'}
                ,{field:'voucher', width:100, title: '凭证号'}
                // ,{field:'net_deposit_num', width:100, title: '实收人数'}
                
                // ,{field:'owe_num', width:100, title: '欠费人'}
                // ,{field:'owe_money', width:100, title: '欠费金额'}
                ,{field:'ctime', width:150, title: '入住时间'}
                ,{field:'pay_time', width:220, title: '缴费周期'}
                // ,{field:'right', width:250,toolbar: '#barDemo', title: '操作'}
            ]]
            ,page: true
        });
        $('#send').click(function(){
        	location.href="<?php echo url('Data/exceAdd'); ?>";
        	 // layer.msg('正在下载Excel表，请稍等',{icon:1});
          return false;
        });

        $('#senddatainfo').click(function(){
          location.href="<?php echo url('Data/dataExcea'); ?>";
          return false;
        })
        table.on('tool(testdd)', function(obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）

            if (layEvent === 'edit') { //查看
                location.href="<?php echo url('Deposit/depositUpdateInfo'); ?>?deposit_id="+data.deposit_id;
            } else if (layEvent === 'del') { //删除
               
            } else if (layEvent === 'pos') {
            	
            } else if (layEvent === 'refund') {
            }
        })

        form.on('submit(*)', function(data){
          /*$.post(
                  '<?php echo url("Data/dataNewList"); ?>',
                  data.field,
                  function(msg){
                      layer.msg(msg.font,{icon:msg.code});
                       if(msg.code==1){
                       table.reload('test');
                       }
                  },'json'
          )*/
          table.render({
              elem: '#test'
              ,url:"<?php echo url('Data/dataNewList'); ?>"
              ,where:data.field
              ,limit:10
              ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
              ,cols: [[
                  { width:100, title: '序号', type:'numbers'}
                ,{field:'user', width:100, title: '业主姓名'}
                ,{field:'area', width:50, title: '区'}
                ,{field:'area_num', width:150, title: '楼/单元/户号'}
                ,{field:'volume', width:100, title: '面积'}
                ,{field:'status', width:100, title: '缴费状态'}
                // ,{field:'status',width:200, title: '缴费状态',templet: function(d){
                //     if (d.status == '未缴费') {
                //         return '<span style="color:#000">'+d.status+'</span>';
                //     } else if (d.status == '已缴费') {
                //         return '<span style="color:#22DD48">'+d.status+'</span>';
                //     } else if (d.status == '已退款') {
                //         return '<span style="color:#FF0000">'+d.status+'</span>';
                //     }
                // }}
                
                ,{field:'deposit_money', width:100, title: '缴费金额'}
                ,{field:'net_money', width:100, title: '实收金额'}
                ,{field:'compensate', width:100, title: '赔偿'}
                ,{field:'pay_times', width:180, title: '缴费时间'}
                // ,{field:'month', width:100, title: '交费月'}
                ,{field:'voucher', width:100, title: '凭证号'}
                // ,{field:'net_deposit_num', width:100, title: '实收人数'}
                
                // ,{field:'owe_num', width:100, title: '欠费人'}
                // ,{field:'owe_money', width:100, title: '欠费金额'}
                ,{field:'ctime', width:150, title: '入住时间'}
                ,{field:'pay_time', width:220, title: '缴费周期'}
                // ,{field:'right', width:250,toolbar: '#barDemo', title: '操作'}
              ]],
            done: function () {
                $('.layui-table').on('click','tr',function(){
                  $(this).css('background','#ccc').siblings().css('background','#fff');
                });
            }
              ,page: true
          });


          console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
          return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });

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