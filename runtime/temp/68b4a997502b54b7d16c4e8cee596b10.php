<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:92:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\data\datalist.html";i:1593408754;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1592206685;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1593321400;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1593414879;}*/ ?>
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
                <img src="/WechatIMG16.png" height="80" width="80">
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
            <option value="">请选择区</option>
            <option value="甲">甲</option>
            <option value="乙">乙</option>
        </select>
    </div>
    <div class="layui-inline">
        <select name="type"  id="search-key" >
            <option value="">请选择收费类型</option>
            <option value="0">物业</option>
            <option value="1">供暖</option>
        </select>
    </div>
    <button class="layui-btn" lay-submit lay-filter="*">搜索</button>
</div>
<button id='send' class="layui-btn layui-btn-normal marginT" lay-submit lay-filter="*">导出Excel</button>
<table class="layui-hide" id="test" lay-filter="testdd"></table>
<!-- <script type="text/html" id="barDemo">
	<a class="layui-btn layui-btn-xs" lay-event="pos">查看</a>
</script> -->
<script>
    layui.use(['table','layer'], function(){
        var table = layui.table;
        var layer = layui.layer;
        var form = layui.form;
        table.render({
            elem: '#test'
            ,url:"<?php echo url('Data/dataList'); ?>"
            ,limit:10
            ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            ,cols: [[
                { width:100, title: '序号', type:'numbers'}
                ,{field:'user', width:100, title: '业主姓名'}
                ,{field:'area', width:100, title: '区'}
                ,{field:'area_num', width:200, title: '楼/单元/户号'}
                ,{field:'volume', width:200, title: '面积'}
                ,{field:'status', width:100, title: '交费状态'}
                ,{field:'deposit_money', width:100, title: '缴费金额'}
                ,{field:'net_money', width:100, title: '实收金额'}
                ,{field:'compensate', width:100, title: '赔偿'}
                ,{field:'year', width:100, title: '交费年'}
                ,{field:'month', width:100, title: '交费月'}
                ,{field:'voucher', width:100, title: '凭证号'}
                // ,{field:'net_deposit_num', width:100, title: '实收人数'}
                
                // ,{field:'owe_num', width:100, title: '欠费人'}
                // ,{field:'owe_money', width:100, title: '欠费金额'}
                ,{field:'ctime', width:200, title: '入住时间'}
                ,{field:'pay_time', width:200, title: '交费日期'}
                // ,{field:'right', width:250,toolbar: '#barDemo', title: '操作'}
            ]]
            ,page: true
        });
        $('#send').click(function(){
        	location.href="<?php echo url('Data/exceAdd'); ?>";
        	 // layer.msg('正在下载Excel表，请稍等',{icon:1});
        });
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
                ,{field:'area', width:100, title: '区'}
                ,{field:'area_num', width:200, title: '楼/单元/户号'}
                ,{field:'volume', width:200, title: '面积'}
                ,{field:'status', width:100, title: '交费状态'}
                ,{field:'deposit_money', width:100, title: '缴费金额'}
                ,{field:'net_money', width:100, title: '实收金额'}
                ,{field:'compensate', width:100, title: '赔偿'}
                ,{field:'year', width:100, title: '交费年'}
                ,{field:'month', width:100, title: '交费月'}
                ,{field:'voucher', width:100, title: '凭证号'}
                // ,{field:'net_deposit_num', width:100, title: '实收人数'}
                
                // ,{field:'owe_num', width:100, title: '欠费人'}
                // ,{field:'owe_money', width:100, title: '欠费金额'}
                ,{field:'ctime', width:200, title: '入住时间'}
                ,{field:'pay_time', width:200, title: '交费日期'}
              ]]
              ,page: true
          });


          console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
          return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
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