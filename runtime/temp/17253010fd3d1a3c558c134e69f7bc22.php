<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:91:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/xmorder/orderday.html";i:1597821832;s:81:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/layout.html";i:1597672422;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/head.html";i:1597672422;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/left.html";i:1597672422;}*/ ?>
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
    .marginTs{
        margin-top: 30px;
    }
    .tempsTest{
        margin-top:5px;
    }
    .red{
        color:red;
    }

    .green{
        color:green;
    }
    .black{
        color:black;
    }

    .images{
        margin: 0 auto;
        width: 150px;
        height: 150px;
    }
    .layui-img-div{
        text-align:center;
    }
    .layui-title-div{
        color: #FF5454;
        font-size: 20px;
        margin: 10px 0;
    }
    .layui-title2-div{
        font-size: 20px;
        margin-bottom: 30px;

    }
    .layui-title3-div{
        font-size: 18px;
        font-weight: bold; 
        margin-bottom: 20px;
    }
    .layui-title4-div{
        font-size: 10px;
    }
</style>

<div class="layui-form-item">
    <span class="layui-breadcrumb">
        <a href='#'>缴费管理</a>
        <a><cite> 物业收费</cite></a>
    </span>
</div>


<div class="search-table layui-form">
    <div class="layui-inline"> <!--  支付时间--> 
  	<input type="text" placeholder='时间' class="layui-input" name="pay_time" id="test1">
	</div>

     <div class="layui-inline tempsTest">
        <select name="order_status"  id="order_status" >
            <option value="" >订单状态</option>
            <option value="0">未确认</option>
            <option value="1">确认</option>
            <option value="2">已取消</option>
            <option value="3">无效</option>
            <option value="4">退货</option>
            <option value="5">已完成</option>
        </select>
    </div>

     <div class="layui-inline tempsTest">
        <select name="pay_id"  id="pay_id" >
            <option value="" >支付方式</option>
            <option value="1">微信支付</option>
        </select>
    </div>

    <div class="layui-inline tempsTest">
        <select name="tnumber"  id="tnumber" >
            <option value="" >桌号</option>
            <option value="1">1号桌</option>
        </select>
    </div>

     <!-- 付款金钱 -->
      <div class="layui-inline">
        <div class="layui-input-inline" style="width: 100px;">
          <input type="text" name="price_min" placeholder="￥金钱" autocomplete="off" class="layui-input">
        </div>
        -
        <div class="layui-input-inline" style="width: 100px;">
          <input type="text" name="price_max" placeholder="￥金钱" autocomplete="off" class="layui-input">
        </div>
      </div>
            
      <div class="layui-inline tempsTest">
        <select name="order_type"  id="order_type" >
            <option value="" >就餐方式</option>
            <option value="1">堂食</option>
            <option value="2">外带</option>
        </select>
    </div>

    <div class="layui-inline tempsTest">
        <select name="pay_status"  id="pay_status" >
            <option value="" >支付状态</option>
            <option value="0" >未付款</option>
            <option value="1">付款中</option>
            <option value="2">已付款</option>
            <option value="3">已退款</option>
        </select>
    </div>

    
    <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
    	<input type="text" placeholder='下单时间' name="create_time" class="layui-input" id="test2">
  	</div>
  	<button type="button" value='1' id='create_time' class="layui-btn layui-btn-xs">↓</button>
    <div class="layui-input-inline" style="width: 100px;">
      <input type="text" name="pay_fee" placeholder="订单金额"  autocomplete="off" class="layui-input">
    </div>
<button type="button" value='1' id='order_fee' class="layui-btn layui-btn-xs">↓</button>


<input type="text" value="<?php echo $display; ?>" id='displays' hidden>
    <div class="layui-inline" id="mendian" style="display: none;">
        <select name="store"  id="store" >
            <option value="" >选择门店</option>
            <?php if(is_array($select) || $select instanceof \think\Collection || $select instanceof \think\Paginator): $i = 0; $__LIST__ = $select;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$select): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $select['storeid']; ?>"><?php echo $select['name']; ?></option>
              <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>

    <div class="layui-input-inline" id="order" style="display: none;">
        <input value="" type="text" name="order" autocomplete="off"
               placeholder="请输入订单号" class="layui-input" style="width: 200px"/>
    </div>

    <button class="layui-btn tempsTest" lay-submit id='sousuo' lay-filter="*">搜索</button>
</div>




<table class="layui-hide" id="test" lay-filter="test"></table>
<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="pos">详情</a>

</script>
<script src="__STATIC__/public.js"></script>
<script>
    layui.use(['table','layer','element','upload','form','laydate'], function(){
        var table = layui.table;
        var layer = layui.layer;
        var upload = layui.upload;
        var form = layui.form;
        var laydate = layui.laydate;
        var tmer;

        var dis = $('#displays').val();

        if(dis == 1){
            $('#mendian').show();
            $('#order').show();
        }

        form.render('select');
        $('#create_time').click(function(){
        	var create_time = $('#create_time').val();
        	if (create_time == 1) {
        		$('#create_time').text('↑');
        		$('#create_time').val(2);
        	} else {
        		$('#create_time').text('↓');
        		$('#create_time').val(1);
        	}
        });

        $('#order_fee').click(function(){
        	var order_fee = $('#order_fee').val();
        	if (order_fee == 1) {
        		$('#order_fee').text('↑');
        		$('#order_fee').val(2);
        	} else {
        		$('#order_fee').text('↓');
        		$('#order_fee').val(1);
        	}
        });






            var ids = 0;
            
            //自动点击.
            $(document).ready(function(){
                
                $("#sousuo").trigger("click");
                ids = 1;
                if(ids == 1){
                             var page = localStorage.getItem('page');
                            table.reload('test', {
                                page: {
                                    curr: page //重新从第 1 页开始
                                }
                            }); //只重载数据
                            localStorage.clear();
                }
           
            });
            
		//日期选择器
        laydate.render({ 
          elem: '#test1'
          ,type: 'datetime' //默认，可不填
        });
       
        //日期选择器
        laydate.render({ 
          elem: '#test2'
          ,type: 'datetime' //默认，可不填
        });

        //删除和修改
        table.on('tool(test)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的DOM对

            if(layEvent === 'detail'){ //查看
                var pages = $(".layui-laypage-skip").find("input").val() //当前页码值
                location.href="<?php echo url('Orders/paymentUpdateInfo'); ?>?id="+data.id+"&page="+pages;
            } else if(layEvent === 'del'){ //删除
                
            } else if (layEvent == 'refund') {
               
            } else if (layEvent == 'pos') {
                var pages = $(".layui-laypage-skip").find("input").val() //当前页码值
                location.href="<?php echo url('Xmorder/orderInfo'); ?>?orderid="+data.orderid+"&page="+pages;
                return false;
            } else if (layEvent == 'money') {
                    


                    
            } else if (layEvent == 'paytoken') {
                
            }
        })



        form.on('submit(*)', function(data){
            var create_time_type = $('#create_time').val();
            var order_fee_type = $('#order_fee').val();
            data.field.create_time_type = create_time_type;
            data.field.order_fee_type = order_fee_type;

          table.render({
              elem: '#test'
              ,url:"<?php echo url('Xmorder/orderDay'); ?>"
              ,where:data.field
              ,limit:1
              ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
              ,cols: [[
              {field:'orderid', width:100, title: 'orderid'}
                ,{width:100, title: '序号',type:'numbers'}
                ,{field:'order_sn', width:200, title: '订单号'}
                ,{field:'tnumber',width:100, title: '桌号'}
                ,{field:'content',width:200, title: '订单内容'}
                ,{field:'pay_id',width:100, title: '支付方式'}
                ,{field:'pay_status',width:150, title: '状态'}
                ,{field:'right', width:100,toolbar: '#barDemo', title:'操作'}
            ]],
            done: function () {
                $("[data-field='orderid']").css('display','none');
                $('.layui-table').on('click','tr',function(){
                    $(this).css('background','#ccc').siblings().css('background','#fff');
                });
            },page: true
          });
        });

        $('.layui-table').on('click','tr',function(){
            $(this).css('background','#ccc').siblings().css('background','#fff');
        });
    });
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