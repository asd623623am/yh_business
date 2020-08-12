<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\index\index.html";i:1591868510;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1593659111;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1593582216;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1593589307;}*/ ?>
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
    <img src="/WechatIMG.png" style="margin-left:20px;" height="66" width="155">
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
                $(elem).find('img').attr('src','__IMG__/tab_'+len+'_active.png');        //参:每个元素
            });
             $(".layui-nav").on("click","li",function () {
                var len = $(this).find('img').attr('src').substr(11,1);
                if($(this).hasClass('layui-nav-itemed')){
                    $(this).find('img').attr('src','__IMG__/tab_'+len+'_active.png')
                }else{
                    $(this).find('img').attr('src','__IMG__/tab_'+len+'.png')
                }
             })
            
        });
    </script>
    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            <style type="text/css">
	.d{
		margin-right:50px;
		float: right;
	}
	#box1{
		float:left;
	}
	.box-parent{
		margin-left: 30%;
	}
	.box-childer{
		margin-left:40%;
	}
	#box2{
		margin-left:10%;
		float:left;
	}
</style>
<script type="text/javascript" src="/static/echarts.min.js"></script>
<form class="layui-form">
    <!-- <div class='a'>
    	<span class="layui-badge" style="width:200px; height:100px; font-size:15px; text-align:center;line-height:100px;" >正在收费缴费单：<font>1000</font></span>
    </div>
    <div class='b'>
    	<span class="layui-badge" style="width:200px; height:100px; font-size:15px; text-align:center;line-height:100px;" >正在收费缴费单：<font>1000</font></span>
    </div> -->
    <div class='d'>
    	<button type="button" id="submet" class="layui-btn layui-btn-normal">一键催费</button>
    </div>
    <div>
    	<div id='box1'>
			<div id="main" style="width: 400px;height:400px;"></div>
			<div class="box-parent"><h1>物业收费情况</h1></div>
			<div class="box-childer"><h2><span style="color:#1AE6BD;" id="wnum">0</span>/<span id="wcount">0</span></h2></div>

    	</div>
    	<div id='box2'>
			<div id="mains" style="width: 400px;height:400px;"></div>    	    		
    		<div class="box-parent"><h1>供暖收费情况</h1></div>
    		<div class="box-childer"><h2><span style="color:#1AE6BD;" id="gnum">0</span>/<span id="gcount">0</span></h2></div>
    	</div>
    </div>

</form>
<script type="text/javascript">
	layui.use(['form','layer','upload'], function(){
		var form = layui.form;
        var layer = layui.layer;
        var upload = layui.upload;
	// 基于准备好的dom，初始化echarts实例
	       var myChart = echarts.init(document.getElementById('main'));
	       var myCharts = echarts.init(document.getElementById('mains'));

	       $.post(
	               "<?php echo url('Index/indexData'); ?>",
	               function(msg){
	               		if (msg.code == 0) {

	               			var w = msg.data.w;
	               			var g = msg.data.g;
	               			var option = options(w.percentage,w.title,w.num,w.count-w.num);
	               			var optionss = options(g.percentage,g.title,g.num,g.count-g.num);
	               			$('#wnum').text(w.num);
	               			$('#wcount').text(w.count);
	               			$('#gnum').text(g.num);
	               			$('#gcount').text(g.count);
	               			// 使用刚指定的配置项和数据显示图表。
	               			myChart.setOption(option);
	               			myCharts.setOption(optionss);

	               			function options(percentage,name,paid,unpaid)
	               			{
	               				// 指定图表的配置项和数据
	               				        var option = {
	               				            tooltip: {
	               				                trigger: 'item',
	               				                formatter: "{a} <br/>{b}: {c} ({d}%)"
	               				            },
	               				            color:["#27D9C8","#D8D8D8"],
	               				            title:{
	               				                text:percentage,
	               				                left:"center",
	               				                top:"50%",
	               				                textStyle:{
	               				                    color:"#27D9C8",
	               				                    fontSize:36,
	               				                    align:"center"
	               				                }
	               				            },
	               				            graphic:{
	               				                type:"text",
	               				                left:"center",
	               				                top:"40%",
	               				                style:{
	               				                    text:name,
	               				                    textAlign:"center",
	               				                    fill:"#333",
	               				                    fontSize:20,
	               				                    fontWeight:700
	               				                }
	               				            },
	               				            series: [
	               				                {
	               				                    name: '缴费情况',
	               				                    type: 'pie',
	               				                    radius: ['65%', '70%'],
	               				                    avoidLabelOverlap: false,
	               				                    label: {
	               				                        normal: {
	               				                            show: false,
	               				                            position: 'center'
	               				                        },
	               				                       
	               				                    },
	               				                    
	               				                    data: [
	               				                        { value: paid, name: '已缴费' },
	               				                        { value: unpaid, name: '未缴费' },
	               				                       
	               				                    ]
	               				                }
	               				            ]
	               				        };

	               				return option;
	               			}
	               		}

	               },'json'
	       )


	       // var percentage= '100%';
	       // var name = '物业缴费完成率';
	       // var num = 100;
	       // var count = 100;
	       // var option = options(percentage,name,num,count);
	       // var options = options(percentage,name,num,count);
	       // $('#wnum').text(num);
	       // $('#wcount').text(count);
	       // $('#gnum').text(num);
	       // $('#gcount').text(count);
	       // // 使用刚指定的配置项和数据显示图表。
	       // myChart.setOption(option);
	       // myCharts.setOption(options);



function options(percentage,name,paid,unpaid)
{
	// 指定图表的配置项和数据
	        var option = {
	            tooltip: {
	                trigger: 'item',
	                formatter: "{a} <br/>{b}: {c} ({d}%)"
	            },
	            color:["#27D9C8","#D8D8D8"],
	            title:{
	                text:percentage,
	                left:"center",
	                top:"50%",
	                textStyle:{
	                    color:"#27D9C8",
	                    fontSize:36,
	                    align:"center"
	                }
	            },
	            graphic:{
	                type:"text",
	                left:"center",
	                top:"40%",
	                style:{
	                    text:name,
	                    textAlign:"center",
	                    fill:"#333",
	                    fontSize:20,
	                    fontWeight:700
	                }
	            },
	            series: [
	                {
	                    name: '缴费情况',
	                    type: 'pie',
	                    radius: ['65%', '70%'],
	                    avoidLabelOverlap: false,
	                    label: {
	                        normal: {
	                            show: false,
	                            position: 'center'
	                        },
	                       
	                    },
	                    
	                    data: [
	                        { value: paid, name: '已缴费' },
	                        { value: unpaid, name: '未缴费' },
	                       
	                    ]
	                }
	            ]
	        };

	return option;
}

$('#submet').click(function(){
	    $.post(
	            "<?php echo url('Index/indexSend'); ?>",
	            function(msg){
	            	if (msg.code == 1) {
	            		layer.msg(msg.font,{icon:msg.code});
	            	} else {
	            		layer.msg(msg.font,{icon:msg.code});
	            	}
	            },'json'
	    )
	    return false;
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