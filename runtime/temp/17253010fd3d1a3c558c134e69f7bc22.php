<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:91:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/xmorder/orderday.html";i:1609912621;s:81:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/layout.html";i:1609395004;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/head.html";i:1602144438;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/left.html";i:1599822484;}*/ ?>
<!DOCTYPE html>
<html no-cache>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <META content="history" name="save">
    <STYLE>
        input {behavior:url(#default#savehistory);}
    </STYLE>
    <title>小码旺铺扫码点餐管理系统</title>
    <link rel="stylesheet" href="__STATIC__/css/layui.css">
    <link rel="stylesheet" href="__STATIC__/css/publish.css">
    <link rel="stylesheet" href="__STATIC__/css/soulTable.css">
    <link rel="icon" href="__STATIC__/admin/images/WechatIMG16.png" type="image/x-icon">
    <script src="__STATIC__/jquery-3.2.1.min.js"></script>
    <script src="__STATIC__/layui.js"></script>
    <script src="__STATIC__/move.js"></script>
    <script src="__STATIC__/publishImg.js"></script>
    <script src="__STATIC__/croppers.js"></script>
    <script src="__STATIC__/multiSelect.js"></script>
    <script src="__STATIC__/layui_extends/selectN.js"></script>
    <script src="__STATIC__/layui_extends/selectM.js"></script>
    <script src="__STATIC__/js/FileSaver.js"></script>
    <script src="__STATIC__/js/jszip.min.js"></script>

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
    <a href="/admin.php/Index/Index" class="layui-title-class"><div class="layui-logo">小码旺铺扫码点餐管理系统</div></a>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <!-- <ul class="layui-nav layui-layout-left">
        <li class="layui-nav-item"><a href="">邮件管理</a></li>
        <li class="layui-nav-item"><a href="">消息管理</a></li>
        <li class="layui-nav-item"><a href="">授权管理</a></li>
    </ul> -->
    <ul class="layui-nav layui-layout-right">
        <li class="layui-nav-item"><a href="javascript:;" id='newOrder'>新订单（<span id="bboobo"></span>）</a></li>
        <!-- <li class="layui-nav-item"><a href="<?php echo url('Xmorder/orderDay'); ?>">新订单（<span id="bboobo"></span>）</a></li> -->
        <li class="layui-nav-item">
            <a href="javascript:;">
                <!-- <img src="__STATIC__/images/d833c895d143ad4b6eba587980025aafa50f06f6.jpg" class="layui-nav-img"> -->
                欢迎<?php echo \think\Session::get('admin.admin_name'); ?>登录
            </a>
        </li>
        <li class="layui-nav-item"><a href="<?php echo url('Login/logout'); ?>">注销</a></li>
    </ul>
</div>
<script>

    $('#newOrder').click(function(){
        localStorage.setItem('newOrder',11);
        location.href="<?php echo url('Xmorder/orderDay'); ?>";
    })

    $.post('<?php echo url("Xmorder/orderCount"); ?>',
        function(msg){
            $('#bboobo').text(msg.count);
        },'json'
    )

    setInterval(function (){

        $.post('<?php echo url("Xmorder/orderCount"); ?>',
        function(msg){
            $('#bboobo').text(msg.count);
        },'json'
    )

},10000)
</script>
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
<blockquote class="layui-elem-quote layui-text">
    <span class="layui-breadcrumb">
        <a href='#'>订单管理</a>
        <a><cite> 订单列表</cite></a>
    </span>
</blockquote>


<div class="search-table layui-form">

     <!-- <div class="layui-inline tempsTest">
        <select name="order_status"  id="order_status" >
            <option value="" >订单状态</option>
            <option value="0">未确认</option>
            <option value="1">确认</option>
            <option value="2">已取消</option>
            <option value="3">无效</option>
            <option value="4">退货</option>
            <option value="5">已完成</option>
        </select>
    </div> -->

     <!-- <div class="layui-inline tempsTest">
        <select name="pay_id"  id="pay_id" >
            <option value="" >支付方式</option>
            <option value="1">微信支付</option>
        </select>
    </div> -->

    <div class="layui-inline tempsTest">
        <select name="tnumber"  id="tnumber" >
            <option value="" >桌号</option>
             <?php if(is_array($tnumber) || $tnumber instanceof \think\Collection || $tnumber instanceof \think\Paginator): $i = 0; $__LIST__ = $tnumber;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tnumber): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $tnumber; ?>"><?php echo $tnumber; ?></option>
              <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
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
            <option value="" >订单状态</option>
            <option value="0" >未付款</option>
            <!-- <option value="1">付款中</option> -->
            <option value="2" selected>已付款</option>
            <option value="3">已退款</option>
            <option value="4">已完成</option>
            <option value="5">部分退款</option>
        </select>
    </div>
    <div class="layui-inline tempsTest"> <!--  支付时间-->
        <input type="text" placeholder='支付时间' class="layui-input" name="pay_time" id="test1">
    </div>
    <button type="button" value='1' id='create_time' class="layui-btn layui-btn-xs">↓</button>
    <div class="layui-inline tempsTest"> <!-- 注意：这一层元素并不是必须的 -->
    	<input type="text" placeholder='支付日期' name="create_time" class="layui-input" id="test2">
  	</div>
    <!-- 付款金钱 -->
    <div class="layui-inline tempsTest">
        <div class="layui-input-inline" style="width: 100px;">
            <input type="text" name="price_min" placeholder="￥金钱" autocomplete="off" class="layui-input">
        </div>
        -
        <div class="layui-input-inline" style="width: 100px;">
            <input type="text" name="price_max" placeholder="￥金钱" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-input-inline tempsTest" style="width: 100px;">
      <input type="text" name="pay_fee" placeholder="订单金额"  autocomplete="off" class="layui-input">
    </div>
    <button type="button" value='1' id='order_fee' class="layui-btn layui-btn-xs">↓</button>
    <input type="text" value="<?php echo $display; ?>" id='displays' hidden>
    <div class="layui-inline tempsTest" id="mendian" style="display: none;">
        <select name="store"  id="store" >
            <option value="" >选择门店</option>
            <?php if(is_array($select) || $select instanceof \think\Collection || $select instanceof \think\Paginator): $i = 0; $__LIST__ = $select;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$select): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $select['storeid']; ?>"><?php echo $select['name']; ?></option>
              <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>
    <div class="layui-input-inline tempsTest" id="order">
        <input value="" type="text" name="order" autocomplete="off"
               placeholder="请输入订单号" class="layui-input" style="width: 200px"/>
    </div>
    <div class="layui-input-inline tempsTest" id="order">
        <input value="" type="text" name="pay_trans_no" autocomplete="off"
               placeholder="请输入交易号" class="layui-input" style="width: 200px"/>
    </div>
    <button class="layui-btn tempsTest" lay-submit id='sousuo' lay-filter="*">搜索</button>
    <button class="layui-btn tempsTest" lay-submit id='excel' style="display: none;" lay-filter="*">导出订单(excel)</button>
</div>

<table class="layui-hide" id="test" lay-filter="test"></table>
<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="pos">详情</a>
    <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="repticket">补打</a>
    <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="is_ok">完成</a>
</script>
<script type="text/html" id="toolbarDemo">
    <div class="layui-btn-container">
      <button class="layui-btn layui-btn-sm" lay-event="getCheckData">批量已读</button>
    </div>
  </script>
<script src="__STATIC__/public.js"></script>
<script>
    layui.use(['table','layer','soulTable','element','upload','form','laydate'], function(){
        var table = layui.table;
        var layer = layui.layer;
        var upload = layui.upload;
        var form = layui.form;
        var laydate = layui.laydate;
        var soulTable = layui.soulTable;
        var tmer;
        var dis = $('#displays').val();
        if(dis == 1){
            $('#mendian').show();
            $('#order').show();
        }
        form.render('select');
        $('#excel').click(function(){
            location.href="<?php echo url('Xmorder/excel'); ?>";
          return false;
        });
        $('#create_time').click(function(){
        	var create_time = $('#create_time').val();
        	if (create_time == 1) {
        		$('#create_time').text('↑');
        		$('#create_time').val(2);
        	} else {
        		$('#create_time').text('↓');
        		$('#create_time').val(1);
        	}
            $("#sousuo").trigger("click");
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
            $("#sousuo").trigger("click");
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
          ,type: 'date' //默认，可不填
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
                location.href="<?php echo url('Xmorder/orderInfo'); ?>?orderid="+data.orderid+"&page="+pages+"&is_new_type="+data.is_new_type;
                return false;
            } else if (layEvent == 'repticket') {
                //重打小票
                $.post('<?php echo url("Xmorder/repticket"); ?>',
                    {data:data},
                    function(msg){
                        layer.msg(msg.font,{icon:msg.code});
                        if(msg.code==1){

                        }else{

                        }
                    },'json'
                )
                    
            } else if(layEvent == 'is_ok'){
                layer.confirm('确认完成？', {
                    btn: ['确认', '取消'] //可以无限个按钮
                    ,btn3: function(index, layero){
                    }
                    }, function(index, layero){
                        $.post('<?php echo url("Xmorder/isConfirm"); ?>',
                        {data:data},
                        function(msg){
                            layer.msg(msg.font,{icon:msg.code});
                            if(msg.code == 1){
                                table.reload('test');
                            }
                        },'json'
                        )
                    });
            }
        })

         //监听头工具栏事件
         table.on('toolbar(test)', function(obj){
            var checkStatus = table.checkStatus(obj.config.id)
            ,data = checkStatus.data; //获取选中的数据
            switch(obj.event){
            case 'add':
                layer.msg('添加');
            break;
            case 'update':
                if(data.length === 0){
                layer.msg('请选择一行');
                } else if(data.length > 1){
                layer.msg('只能同时编辑一个');
                } else {
                layer.alert('编辑 [id]：'+ checkStatus.data[0].id);
                }
            break;
            case 'getCheckData':
                if(data.length === 0){
                layer.msg('请选择您要操作的数据');
                } else {
                    layer.confirm('确认批量已读', function(index){
                    $.post('<?php echo url("Xmorder/updatetype"); ?>',
                        {data:data},
                        function(msg){
                            layer.msg(msg.font,{icon:msg.code});
                             if(msg.code==1){
                             table.reload('test');

                             $.post('<?php echo url("Xmorder/orderCount"); ?>',
                                function(msg){
                                    $('#bboobo').text(msg.count);
                                },'json'
                            )
                             }
                        },'json'
                    )
                });
                }
            break;
            case 'upp':
            if(data.length === 0){
                layer.msg('请选择您要操作的数据');
                } else {
                    
                    layer.confirm('确认批量操作吗？', function(index){
                    $.post('<?php echo url("Goods/goodsUps"); ?>',
                        {data:data},
                        function(msg){
                            layer.msg(msg.font,{icon:msg.code});
                             if(msg.code==1){
                             table.reload('test');
                             }
                        },'json'
                    )
                });
                }
            break;
            case 'downp':
            if(data.length === 0){
                layer.msg('请选择您要操作的数据');
                } else {
                    layer.confirm('确认批量操作吗？', function(index){
                    $.post('<?php echo url("Goods/goodsDowns"); ?>',
                        {data:data},
                        function(msg){
                            layer.msg(msg.font,{icon:msg.code});
                             if(msg.code==1){
                             table.reload('test');
                             }
                        },'json'
                    )
                });
                }
            break;
            case 'downloadModel'://下载模板
                $.post('<?php echo url("PHPExcel/downloadGoodsModel"); ?>',function(msg){
                    var url = msg.font;
                    var elemIF = document.createElement("iframe");
                    elemIF.src = url;
                    elemIF.style.display = "none";
                    document.body.appendChild(elemIF);
                    layer.msg("模板下载成功",{icon:1});
                },'json');
                break;
            };

        });

        form.on('submit(*)', function(data){

            var neworder = localStorage.getItem('newOrder');
            if(neworder != null){
                data.field.neworder = 1; //1是新订单
            }
            var create_time_type = $('#create_time').val();
            var order_fee_type = $('#order_fee').val();
            data.field.create_time_type = create_time_type;
            data.field.order_fee_type = order_fee_type;
            
          table.render({
              elem: '#test'
              ,url:"<?php echo url('Xmorder/orderDay'); ?>"
              ,where:data.field
              ,toolbar: '#toolbarDemo'
              ,defaultToolbar: []
              ,limit:10
              ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
              ,cols: [[
              {type: 'checkbox',},
              {field:'orderid', width:100, title: 'orderid'}
                ,{width:100, title: '序号',type:'numbers',children: [
                {
                    title: '商品详情表'
                    ,data: function (e) {
                        // d 为当前行数据
                        console.log(e.good_data);
                        return e.good_data;
                    }
                    ,height: 300
                    ,page: false
                    ,cols: [[
                        {field:'goods_name', title: '菜品名称',align:'center'}
                        ,{field:'goods_number',  title: '菜品数量',align:'center'}
                        ,{field:'selling_price',  title: '菜品金额',align:'center'}
                        ,{field:'gsname',  title: '菜品规格',align:'center'}
                        ,{field:'remark',  title: '菜品备注',align:'center'}
                    ]]
                    ,done: function () {
                        // soulTable.render(this);
                    }
                    },
                ]}
                ,{field:'order_transNo', width:380, title: '订单号'}
                ,{field:'is_new_type', width:250, title: 'is_new'}
                ,{field:'tnumber',width:130, title: '桌台号'}
                ,{field:'pay_fee',width:200, title: '订单总额'}
                ,{field:'refund_fee',width:200, title: '退款金额'}
                ,{field:'discount_fee',width:200, title: '优惠金额'}
                ,{field:'goods_amount',width:200, title: '菜品数量'}
                ,{field:'pay_id',width:150, title: '支付方式',align:'center'}
                ,{field:'pay_status',width:150, title: '订单状态',align:'center'}
                ,{field:'pay_time',width:200, title: '支付时间',align:'center'}
                ,{field:'create_time',width:200, title: '支付日期',align:'center'}
                // ,{field:'invoice_status',width:150, title: '开票状态',align:'center'}
                ,{field:'right', width:100,toolbar: '#barDemo', title:'操作',align:'center'}
            ]],
            done: function () {
                $("[data-field='orderid']").css('display','none');
                $("[data-field='is_new_type']").css('display','none');
                $('.layui-table').on('click','tr',function(){
                    $(this).css('background','#ccc').siblings().css('background','#fff');
                });
                soulTable.render(this)
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