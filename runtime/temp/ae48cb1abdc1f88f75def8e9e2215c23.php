<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/index/index.html";i:1597672422;s:81:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/layout.html";i:1597672422;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/head.html";i:1597672422;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/left.html";i:1597672422;}*/ ?>
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
                <?php if(is_array($AllMenu) || $AllMenu instanceof \think\Collection || $AllMenu instanceof \think\Paginator): $k = 0; $__LIST__ = $AllMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;                $i=0;
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
                    if( isset($v['son']) ){ if(is_array($v['son']) || $v['son'] instanceof \think\Collection || $v['son'] instanceof \think\Paginator): $i = 0; $__LIST__ = $v['son'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;                        $action = explode( '/' , trim($vv['node_url'] , '/' )  );
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
            <style type="text/css">
        .layui-body {
                background: #eee;
                min-width: 880px;
        }

        .layui-footer {
                background: #eee;
        }

        .d {
                display: flex;
                justify-content: space-between;
        }

        .index-title {
                color: #676767;
                line-height: 38px;
                font-size: 18px;
        }

        .countHu {
                display: flex;
                justify-content: space-between;
                margin: 20px 0;
                color: #fff;
        }

        .countHu>div {
                width: 32.5%;
                display: flex;
                justify-content: space-between;
                padding: 30px;
                box-sizing: border-box;
                align-items: center;
                background-size: cover;
        }

        @media (min-width: 1024px) {
                .countHu>div {
                        height: 100px;
                }
        }


        /*>=1024的设备*/
        @media (min-width: 1280px) {
                .countHu>div {
                        height: 120px;
                }
        }

        @media (min-width: 1366px) {
                .countHu>div {
                        height: 150px;
                }

                .countHu>div .left .left-count {
                        margin-top: 8px;
                }
        }

        @media (min-width: 1440px) {
                .countHu>div {
                        height: 180px;
                }

                .countHu>div .left .left-count em {
                        font-size: 36px;
                        margin-right: 12px;
                }
        }

        @media (min-width: 1680px) {
                .countHu>div {
                        height: 200px;
                        justify-content: space-around;
                        font-size: 20px;
                }

                .countHu>div .left .left-count {
                        margin-top: 10px;
                }

                .countHu>div .left .left-count em {
                        font-size: 38px;
                        margin-right: 15px;
                }
        }


        .countHu-house {
                background: url('__IMG__/red.png') no-repeat;

        }

        .countHu-dingdan {

                background: url('__IMG__/blue.png') no-repeat;
        }

        .countHu-jindu {
                background: url('__IMG__/purple.png') no-repeat;
        }

        .countHu>div .left .left-count {
                margin-top: 5px;
        }

        .countHu>div .left .left-count em {
                font-size: 35px;
                font-style: normal;
                margin-right: 10px;
        }

        .countHu>div .right img {
                width: 65px;
                height: 65px;
        }

        .countHu-jindu .left-count {
                font-size: 20px;
                margin-top: 15px !important;
        }

        .char {
                /* display: flex;
                justify-content: space-between; */
        }

        .char>div {
                min-width: 480px;
                width: 100%;
                margin-top: 20px;
                background: #fff;
                padding: 30px;
                box-sizing: border-box;
        }



        .char-title {
                display: flex;
                justify-content: space-between;
                margin-bottom: 30px;
        }

        .char-title-left i {
                border: 2px solid #23DAC3;
        }

        .char-title-left em {
                font-style: normal;
                margin-left: 7px;
                font-weight: bold;
        }

        .char-title-right b {
                font-weight: 100;
                border: 1px solid #ccc;
                border-radius: 15px;
                padding: 3px 10px;
                background: #fff;
                color: rgb(95, 93, 93);
                font-size: 12px;
                cursor: pointer;
        }

        .char-title-right .active {
                border-color: #23DAC3;
                color: #23DAC3;
        }
</style>
<script src="https://code.highcharts.com.cn/highcharts/highcharts.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/exporting.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/series-label.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/oldie.js"></script>
<script src="https://code.highcharts.com.cn/highcharts-plugins/highcharts-zh_CN.js"></script>
<form class="layui-form index">
	<div class='d'>
		<div class="index-title">基本情况 / 基本情况详情</div>
		<button type="button" id="submet" class="layui-btn layui-btn-normal cuiFei">一键催费</button>
	</div>
	<div class="countHu">
		<div class="countHu-house">
			<div class="left">
				<div>&nbsp;房屋数量</div>
				<div class="left-count"><em id='home_num'>0</em>户</div>
			</div>
			<div class="right"><img src="__IMG__/1.1.png" alt=""></div>
		</div>
		<div class="countHu-dingdan">
			<div class="left">
				<div>&nbsp;缴费订单数量</div>
				<div class="left-count"><em id='order_no'>0</em>单</div>
			</div>
			<div class="right"><img src="__IMG__/1.2.png" alt=""></div>
		</div>
		<div class="countHu-jindu">
			<div class="left">
				<div>&nbsp;缴费进度</div>
				<div class="left-count "><span id='pay_count'>0</span>/<span id='count'>0</span></div>
			</div>
			<div class="right"><img src="__IMG__/1.3.png" alt=""></div>
		</div>
	</div>
	<div class="char">
		<!-- <div id="container" style="max-width:400px;height:300px"></div> -->
		<div>
			<div class="char-title char1">
				<div class="char-title-left">
					<i></i>
					<em>交易金额</em>
				</div>
				<div class="char-title-right">
					<b v='w'>本周</b>
					<b class="active" v='m'>本月</b>
					<b v="y">本年</b>
				</div>
			</div>
			<div id="container1"></div>
		</div>
		<div>
			<div class="char-title char2">
				<div class="char-title-left">
					<i></i>
					<em>交易笔数</em>
				</div>
				<div class="char-title-right">
					<b v='w'>本周</b>
					<b class="active" v='m'>本月</b>
					<b v='y'>本年</b>
				</div>
			</div>
			<div id="container2"></div>
		</div>
	</div>

</form>
<script type="text/javascript">
	layui.use(['form', 'layer', 'upload'], function () {
		var form = layui.form;
		var layer = layui.layer;
		$.post(
			"<?php echo url('Index/indexData'); ?>",
			function (msg) {
				if (msg.code == 0) {
					var data = msg.data;
					$('#home_num').text(data.home_num);
					$('#order_no').text(data.order_no);
					$('#pay_count').text(data.pay_count);
					$('#count').text(data.count);
				}
			}, 'json'
		);
		$('#submet').click(function () {
			$.post(
				"<?php echo url('Index/indexSend'); ?>",
				function (msg) {
					if (msg.code == 1) {
						layer.msg(msg.font, { icon: msg.code });
					} else {
						layer.msg(msg.font, { icon: msg.code });
					}
				}, 'json'
			)
			return false;
		});
	})

	$('.char1 b').on('click', function () {
		$(this).addClass('active').siblings().removeClass('active')
		var val = $(this).attr('v');
		majax(val);
		console.log(11)

	})
	$('.char2 b').on('click', function () {
		$(this).addClass('active').siblings().removeClass('active')
		var val = $(this).attr('v');
		pajax(val);
		console.log(22)

	})
	let content = [{ categories: '1', data: 1000 }, { categories: '2', data: 1000 }, { categories: '3', data: 1000 }]

	var pval = $('.char2 .active').attr('v');
	pajax(pval);

var mval1 = $('.char1 .active').attr('v');
	majax(mval1);
	function pajax(vals)
	{
		$.post(
			"<?php echo url('Index/payNumber'); ?>",
			{data:vals},
			function (msg) {
				if (msg.code == 0) {
					ex2(msg.data.a,msg.data.b)
				}
			}, 'json'
		)
			return false;
	}

	function majax(vals)
	{
		$.post(
			"<?php echo url('Index/payMoney'); ?>",
			{data:vals},
			function (msg) {
				if (msg.code == 0) {
					ex1(msg.data.a,msg.data.b)
				}
			}, 'json'
		)
			return false;
	}

	function ex1(categories,datas,max){
			var chart1 = Highcharts.chart('container1', {
				title: {
					text: ' '
				},
				xAxis: {
					min: 0,
					max: max,
					categories: categories
				},
				yAxis: {
					title: {
						text: ''
					},
					lineWidth: 1,	//y轴线宽度
					gridLineWidth: 0,  //横着的每条线
				},
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '',
					data: datas,

					marker: {
						enabled: false
					},
					color: '#23DAC3',
		            lineWidth: 1
				},
					// {
					// 	name: '工人',
					// 	data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
					// }
				],

				responsive: {
					rules: [{
						condition: {
							maxWidth: 500
						},
						chartOptions: {
							legend: {
								layout: 'horizontal',
								align: 'center',
								verticalAlign: 'bottom'
							}
						}
					}]
				},
				credits: {
					enabled: false //不显示LOGO
				},
				exporting: {
					enabled: false //不显示菜单
				},
			});
	}
	function ex2(categories,datas,max)
	{
		Highcharts.chart('container2', {
				title: {
					text: ' '
				},
				xAxis: {
					min: 0,
					max: max,
					categories:categories
				},
				yAxis: {
					title: {
						text: ''
					},
					lineWidth: 1,	//y轴线宽度
					gridLineWidth: 0,  //横着的每条线
				},
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '',
					data: datas,
					marker: {
						enabled: false
					},
					color: '#23DAC3',
		            lineWidth: 1
				},
				],

				responsive: {
					rules: [{
						condition: {
							maxWidth: 500
						},
						chartOptions: {
							legend: {
								layout: 'horizontal',
								align: 'center',
								verticalAlign: 'bottom'
							}
						}
					}]
				},
				credits: {
					enabled: false //不显示LOGO
				},
				exporting: {
					enabled: false //不显示菜单
				},
                legend: {
                        enabled: false // 关闭图例
                },
			});
	}
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