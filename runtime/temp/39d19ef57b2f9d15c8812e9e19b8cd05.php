<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:94:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/role/roleupdateinfo.html";i:1597803311;s:81:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/layout.html";i:1597672422;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/head.html";i:1597672422;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/left.html";i:1597672422;}*/ ?>
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
            <style>
    .big-size span {
        font-size: 18px;

    }

    .input-left {
        left: -55px;
        top: 10px;
    }

    .layui-form-item {
        margin-bottom: 25px;
    }
</style>
<form class="layui-form">
    <input type="hidden" id='role_id' name='role_id' value="<?php echo $data['role_id']; ?>">
    <input type="text" id='page' hidden="hidden" value="<?php echo $page; ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">角色名称</label>
        <div class="layui-input-inline">
            <input type="text" name="role_name" lay-verify="required" autocomplete="off"
                   placeholder="请输入角色名称" value="<?php echo $data['role_name']; ?>" class="layui-input">
        </div>
    </div>
    <span id='typeclass' hidden="hidden"><?php echo $data['is_admin']; ?></span>
    <!-- <div class="layui-form-item">
        <label class="layui-form-label">超级管理员</label>
        <div class="layui-input-block">
            <input type="radio" name="is_admin" value="1" title="是" >
            <input type="radio" name="is_admin" value="2" title="否" checked>
        </div>
    </div> -->

    <input type="text" hidden="hidden" id='codeNo' value="<?php echo $code; ?>" name="">


    <hr/>

  

     <!-- <input type="text" value="<?php echo $data['type']; ?>" hidden="hidden" id='types' name="">
     <div class="layui-form-item">
        <label class="layui-form-label">选择分类</label>
        <div class="layui-input-block">
            <input type="radio" name="type" value="1" title="物业" >
            <input type="radio" name="type" value="2" title="供暖" >
            <input type="radio" name="type" value="3" title="全部">
        </div>
    </div> -->


    <!-- <div class="layui-form-item">
        <label class="layui-form-label">是否启用</label>
        <div class="layui-input-block">
            <input type="radio" name="status" value="1" title="是" checked="">
            <input type="radio" name="status" value="2" title="否">
        </div>
    </div> -->
    系统权限
    <hr/>
    <div style="margin-left: 4%">
        <?php if(is_array($AllMenu) || $AllMenu instanceof \think\Collection || $AllMenu instanceof \think\Paginator): $i = 0; $__LIST__ = $AllMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>

        <div class="layui-form-item" pane="">
            <label class="layui-form-label big-size">
                <input type="checkbox" class='parent bbbb' name="power[]"
                       lay-skin="primary" value="<?php echo $v['node_id']; ?>" title="<?php echo $v['node_name']; ?>">
            </label><br/><br/>
            <div class="layui-input-block input-left">
                <?php if(is_array($v["son"]) || $v["son"] instanceof \think\Collection || $v["son"] instanceof \think\Paginator): $i = 0; $__LIST__ = $v["son"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;?>
                <input type="checkbox" class="aaaaa bbbb" name="power[]"
                       lay-skin="primary" value="<?php echo $vv['node_id']; ?>" lay-filter="two" title="<?php echo $vv['node_name']; ?>">
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>


        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="submit">立即修改</button>
            <button type="reset" id='up' class="layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form;
        var layer = layui.layer;

        $('#up').click(function(){
            var page = $('#page').val();
                    localStorage.setItem('page',page);
                    window.history.back();
            // location.href="<?php echo url('Operation/roleList'); ?>";
        });

        //复选框赋值.
        $('#tag input[type=checkbox]').each(function(){
            var value = this.value;

            var did = $('#did').val();
            var arrs = did.split("|");
            arrs.pop();


            for (var i=0; i<arrs.length; i++) {
                if (value == arrs[i]) {
                    this.checked = true;
                }
            }
        })

        

        var code = $('#codeNo').val();
        var arr = code.split("|");
        arr.pop();

        //单选框赋值
         var typeclass = $('#typeclass').text();
         
        $("input[name=is_admin][value="+typeclass+"]").prop("checked","true");

        var types = $('#types').val();
         if(typeclass == 1){
             types = 3;
         }
        
         $("input[name=type][value="+types+"]").prop("checked","true");


        form.render();
        // jQuery.inArray("John", arr);  //3
        $('.bbbb').each(function(i,item){
            
            var a = $.inArray($(item).val(), arr);
            if (a != -1) {
                console.log(a);
                console.log($(item));
                $(item).next('div').addClass('layui-form-checked');
            }
            // form.render();
        })
        console.log(arr);
        // 给父级添加点击事件
        $('.parent').click(function(){
            if( $(this).prop('checked') ==  true ){
                $(this).prop('checked' , false);
                $(this).parents('.layui-form-item').
                children('.layui-input-block').find('input').prop('checked' , false);
            }else{
                $(this).prop('checked' , true);
                $(this).parents('.layui-form-item').
                children('.layui-input-block').find('input').prop('checked' , true);
            }
            form.render();
        });

        // 二级菜单添加点击事件
        form.on('checkbox(two)', function(data){
            console.log(data);
            var mark = 0;
            //获取同级的所有二级菜单是否有选中的，有选中的化，让父级还是选中的状态
            data.othis.parent('.layui-input-block').find('input').each(function(){
                if( $(this).prop('checked') == true ){
                    mark = 1;
                }
            });

            if( data.elem.checked == true ){
                data.elem.checked =  true ;
                data.othis.parents('.layui-form-item').
                find('.layui-form-label').find('input').prop('checked' , true);
            }else{
                data.elem.checked =  false ;
                if( mark !=  1 ){
                    data.othis.parents('.layui-form-item').
                    find('.layui-form-label').find('input').prop('checked' , false);
                }
            }
            form.render();
        });
        
        form.on('submit(submit)' , function(data){
            $.ajax({
                url:'<?php echo url('Role/roleUpdate'); ?>',
                data:data.field,
                type:'post',
                dataType:'json',
                success:function( json_info ){


                    if(json_info.code==1){
                        layer.msg(json_info.font, {
                          icon: json_info.code,
                          time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        }, function(){
                            var page = $('#page').val();
                            localStorage.setItem('page',page);
                            window.history.back();
                        //   location.href="<?php echo url('Operation/roleList'); ?>";
                        });   
                    } else {
                        layer.msg(json_info.font,{icon:json_info.code});
                    }


                    
                }
            })
            return false;
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