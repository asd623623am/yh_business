<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:104:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\deposit\depositupdateinfo.html";i:1593321771;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1592206685;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1593321400;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1593321473;}*/ ?>
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
            <form class="layui-form">
<input type="text" hidden="hidden" value="<?php echo $data['id']; ?>" name="id">
    <div class="layui-form-item">
        <label class="layui-form-label">户主信息</label>
        <div class="layui-input-inline">
            <input type="text" name="user_name" value="<?php echo $data['user_name']; ?>" id='user_name' lay-verify="required" autocomplete="off"
                   placeholder="请输入缴费金额" class="layui-input">
        </div>
    </div>
    <span id="up" hidden="hidden"><?php echo $data['home_id']; ?></span>
    <span id="upname" hidden="hidden"><?php echo $data['home_name']; ?></span>

	<div class="layui-form-item">
	    <label class="layui-form-label">选择房屋</label>
	    <div class="layui-input-inline">
	        <select name="home_id" id='home'>
	            <option value="">请选择</option>

	            <?php if(is_array($selects) || $selects instanceof \think\Collection || $selects instanceof \think\Paginator): $i = 0; $__LIST__ = $selects;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
	            <option value="<?php echo $v['home_id'].'#'.$v['home_name']; ?>"><?php echo $v['home_name']; ?></option>
	            <?php endforeach; endif; else: echo "" ;endif; ?>

	        </select>
	    </div>
	</div>

    <div class="layui-form-item">
        <label class="layui-form-label">订单标题</label>
        <div class="layui-input-inline">
            <input type="text" name="msg" lay-verify="required" autocomplete="off"
                   placeholder="请输入订单标题" value="<?php echo $data['msg']; ?>" class="layui-input">
        </div>
    </div>

	<div class="layui-form-item layui-form-text">
        <label class="layui-form-label">押金备注</label>
        <div class="layui-input-block">
            <textarea placeholder="请输入内容" name="title" class="layui-textarea" lay-verify="required"><?php echo $data['title']; ?></textarea>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">费用明细</label>
        <div class="layui-input-block">
            <textarea placeholder="请输入内容" name="cost_info" class="layui-textarea" lay-verify="required"><?php echo $data['cost_info']; ?></textarea>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">缴费金额</label>
        <div class="layui-input-inline">
            <input type="text" name="fee" value="<?php echo $data['fee']; ?>" lay-verify="required" autocomplete="off"
                   placeholder="请输入缴费金额" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">退款金额</label>
        <div class="layui-input-inline">
            <input type="text" name="refund_money" value="<?php echo $data['refund_money']; ?>" lay-verify="required" autocomplete="off"
                   placeholder="请输入退款金额" class="layui-input">
        </div>
    </div>
    <!-- <?php if($data['is_show']==1): ?>
    	<div class="layui-form-item">
    	    <label class="layui-form-label">选择缴费</label>
    	    <div class="layui-input-block">
    	       <input type="radio" name="is_show" value="1" title="现金支付">
    	       <input type="radio" name="is_show" value="0" title="否" checked>
    	    </div>
    	</div>
    <?php else: ?> -->
    <!-- <input type="radio" name="brand_show" value="1" title="是">
    <input type="radio" name="brand_show" value="0" title="否" checked> -->
    <!-- <?php endif; ?> -->

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="*">立即修改</button>
            <button type="reset" id='ups' class="layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    $(function(){
        layui.use(['form', 'layer'], function(){
            var form = layui.form;
            var  layer = layui.layer;
            var up = $('#up').text();
            var upname = $('#upname').text();
            $('#ups').click(function(){
                location.href="<?php echo url('Deposit/depositList'); ?>";
            });
            $("#home").find("option[value='"+up+'#'+upname+"']").attr("selected",true);
            form.render();
            $('#user_name').blur(function(){
                var userName = $(this).val();
                if (userName == '') {
                        $('#home').children().first().nextAll().remove();
                        form.render('select');
                        return false;
                    }
                    $.post(
                            '<?php echo url("Deposit/depositHome"); ?>',
                            {user_name:userName},
                            function( data ){
                                if (data.code == 0) {
                                    $('#home').children().first().nextAll().remove();
                                    for(var i=0;i<data.data.length;i++){
                                        $('#home').append('<option value="'+data.data[i].home_id+'">'+data.data[i].home_name+'</option>');
                                        form.render('select');
                                    }
                                } else {
                                    layer.msg(data.msg,{icon:2});
                                }
                            },'json'
                    )
                    return false;
            });
           	

            //监听提交
            form.on('submit(*)', function(data){

                layer.confirm('您确认修改吗？', function(index){
                        
                        $.post(
                                '<?php echo url("Deposit/depositUp"); ?>',
                                data.field,
                                function( data ){
                                    if(data.code==1){
                                      layer.msg(data.font, {
                                        icon: data.code,
                                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                      }, function(){
                                        location.href="<?php echo url('Deposit/depositList'); ?>";
                                      });   
                                  } else {
                                      layer.msg(data.font,{icon:data.code});
                                  }
                                   
                                },'json'
                        )
                        

                });

                return false;

                
            });

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