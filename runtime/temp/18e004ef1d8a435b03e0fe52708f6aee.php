<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:95:"E:\PHPserver\wwwroot\default\yh_business\public/../application/admin\view\member\memberadd.html";i:1597632273;s:85:"E:\PHPserver\wwwroot\default\yh_business\public/../application/admin\view\layout.html";i:1597308289;s:90:"E:\PHPserver\wwwroot\default\yh_business\public/../application/admin\view\public\head.html";i:1597301811;s:90:"E:\PHPserver\wwwroot\default\yh_business\public/../application/admin\view\public\left.html";i:1597301811;}*/ ?>
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
            <form class="layui-form">

    <div class="layui-form-item">
        <label class="layui-form-label">请选择小区<span style="color: red;">*</span></label>
        <div class="layui-input-block">
          <select name="dis" lay-filter="filter" id="names">
            <option value="">请选择小区</option>
            
          </select>
        </div>
      </div>


      <div class="layui-form-item">
          <label class="layui-form-label">请选择区<span style="color: red;">*</span></label>
          <div class="layui-input-block">
            <select name="qu">
              <option value="">请选择区</option>
              
            </select>
          </div>
        </div>
    <div class="layui-form-item">
        <label class="layui-form-label">姓名<span style="color: red;">*</span></label>
        <div class="layui-input-block">
            <input type="text" name="home_code"  autocomplete="off" placeholder="请输入楼/单元/户号" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">会员等级<span style="color: red;">*</span></label>
        <div class="layui-input-block">
            <input type="text" name="home_name"  autocomplete="off" placeholder="请输入业主姓名" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">折扣<span style="color: red;">*</span></label>
        <div class="layui-input-block">
            <input type="text" name="tel"  autocomplete="off" placeholder="请输入联系方式 多个手机号用，区分" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">手机号码<span style="color: red;">*</span></label>
        <div class="layui-input-block">
            <input type="text" name="area"  autocomplete="off" placeholder="请输入面积" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">请选择门店<span style="color: red;">*</span></label>
        <div class="layui-input-block">
          <select name="qu">
            <option value="">请选择门店</option>
            
          </select>
        </div>
      </div>

    <div class="layui-form-item">
        <label class="layui-form-label">入住时间<span style="color: red;">*</span></label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" name="ctime" id="test1">
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea placeholder="请输入内容" name="content" class="layui-textarea"></textarea>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="*">立即添加</button>
            <button type="reset" id='up' class="layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>

<!-- 示例-970 -->
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    $(function(){
        layui.use(['form','layer','upload','laydate'], function(){
            var form = layui.form;
            var layer = layui.layer;
             var laydate = layui.laydate;
            	$('#up').click(function(){
            	    location.href="<?php echo url('Home/homeList'); ?>";
            	});

                //执行一个laydate实例
                  laydate.render({
                    elem: '#test1' //指定元素
                  });

                var userName=null;  //定义一个空值
                form.on('select(filter)', function(data){
                    userName=data.elem[data.elem.selectedIndex].text;  //取选中下拉框的文本并赋值给userName
                });

            //监听提交
            form.on('submit(*)',function(data){
                var info = data.field;
                info.dname=userName;
                $.post(
                        "<?php echo url('Home/homeAdd'); ?>",
                        info,
                        function(msg){
                            console.log(msg);

                            if (msg.code == 3) {
                                layer.confirm('已经有这个房子了，确定要继续添加吗？', function(index){
                                    info.status = 1;
                                    $.post(
                                            '<?php echo url("Home/homeAdd"); ?>',
                                            info,
                                            function(da){
                                                
                                                if(da.code==1){
                                                    layer.msg(da.font, {
                                                      icon: da.code,
                                                      time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                                    }, function(){
                                                      location.href="<?php echo url('Home/homeList'); ?>";
                                                    });   
                                                } else {
                                                    layer.msg(da.font,{icon:da.code});
                                                }





                                            },'json'
                                    )
                                });
                            } else {
                                if(msg.code==1){
                                    layer.msg(msg.font, {
                                      icon: msg.code,
                                      time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                    }, function(){
                                      location.href="<?php echo url('Home/homeList'); ?>";
                                    });   
                                } else {
                                    layer.msg(msg.font,{icon:msg.code});
                                }
                            }

                            


                            
                        },'json'
                )
                return false;
            })
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