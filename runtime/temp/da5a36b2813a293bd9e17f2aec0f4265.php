<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\home\homelist.html";i:1597732039;s:76:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\layout.html";i:1597803622;s:81:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\public\head.html";i:1597028307;s:81:"D:\phpstudy_pro\WWW\yh_business\public/../application/admin\view\public\left.html";i:1597028892;}*/ ?>
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
    <script src="__STATIC__/multiSelect.js"></script>

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
            <style type="text/css">
    .marginTs{
        margin-top: 30px;
    }
</style>
<div class="layui-form-item">
    <span class="layui-breadcrumb">
        <a href='#'>会员管理</a>
        <a><cite> 会员列表</cite></a>
    </span>
</div>


<div class="search-table layui-form">
    <div class="layui-input-inline">
      <input type="text" name="owner" required  placeholder="请输入会员姓名" autocomplete="off" class="layui-input">
    </div>
    <div class="layui-inline">
        <select name="complex"  id="search-key" >
            <option value="" >请选择性别</option>
            <?php if(is_array($area) || $area instanceof \think\Collection || $area instanceof \think\Paginator): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$area): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo $area; ?>"><?php echo $area; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>
    <div class="layui-input-inline">
     <input type="text" name="tel" required  placeholder="请输入手机号" autocomplete="off" class="layui-input">
    </div>
    <div class="layui-inline">
        <select name="complex"  id="search-key" >
            <option value="" >请选择等级</option>
            <?php if(is_array($area) || $area instanceof \think\Collection || $area instanceof \think\Paginator): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$area): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo $area; ?>"><?php echo $area; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>
    <div class="layui-input-inline">
      <input type="text" name="owner" required  placeholder="请输入会员卡号" autocomplete="off" class="layui-input">
    </div>
    <button class="layui-btn" id="sousuo" lay-submit lay-filter="*">搜索</button>
</div>
<div class="marginTs">
<button class="layui-btn layui-btn-normal" id='submit' lay-submit lay-filter="*">立即添加</button>
<button type="button" class="layui-btn" id="myload" style="background-color:#1E9FFF">
  <i class="layui-icon">&#xe67c;</i>导入Excel表
</button>
<button class="layui-btn layui-btn-normal" id='dexcel' lay-submit lay-filter="*">导出Excel表</button>
</div>
<table class="layui-hide" id="test" lay-filter="test"></table>
<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="detail">详情</a>
    <a class="layui-btn layui-btn-xs" lay-event="detail">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<script>
    layui.use(['table','layer','upload','form'], function(){
        var table = layui.table;
        var layer = layui.layer;
        var upload = layui.upload;
        var form = layui.form;

        //文件上传
        var uploadInst = upload.render({
            elem: '#myload' //绑定元素
            ,url: '<?php echo url("Home/homeupload"); ?>' //上传接口
            ,exts: 'xls|xlsx|xlsm|xlt|xltx|xltm'
            ,done: function(res){
                //上传完毕回调
                //console.log(msg);
                layer.msg(res.font,{icon:res.code});
                if(res.code==1){
                  table.reload('test');
                }
            }
            ,error: function(){
                //请求异常回调
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
         $('#submit').click(function(){
            location.href="<?php echo url('Home/homeAdd'); ?>";
        })

         $('#dexcel').click(function(){
            location.href="<?php echo url('Home/exceAdd'); ?>";
         });
        //删除和修改
        table.on('tool(test)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的DOM对象

            if(layEvent === 'detail'){ //查看
                var pages = $(".layui-laypage-skip").find("input").val() //当前页码值
                location.href="<?php echo url('Home/homeUpdateInfo'); ?>?home_id="+data.home_id+"&page="+pages;
            } else if(layEvent === 'del'){ //删除
                layer.confirm('真的删除行么', function(index){
                    $.post(
                            '<?php echo url("Home/homeDel"); ?>',
                            {home_id:data.home_id},
                            function(msg){
                                layer.msg(msg.font,{icon:msg.code});
                                 if(msg.code==1){
                                 table.reload('test');
                                 }
                            },'json'
                    )
                });
            }
        })
        form.on('submit(*)', function(data){
          table.render({
              elem: '#test'
              ,url:"<?php echo url('Home/dataNewList'); ?>"
              ,where:data.field
              ,limit:10
              ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
              ,cols: [[
                {field:'home_id', width:200, title: '微信信息'}
                ,{field:'home_id', width:200, title: '微信信息'}
                ,{width:100, title: '姓名',type:'numbers'}
                ,{field:'home_ids',width:200, title: '性别'}
                ,{field:'district_name',width:100, title: '生日'}
                ,{field:'complex',width:100, title: '会员卡号'}
                ,{field:'home_code',width:150, title: '手机'}
                ,{field:'owner',width:100, title: '等级'}
                ,{field:'tel',width:100, title: '联系方式'}
                ,{field:'area',width:100, title: '面积'}
                ,{field:'check_in_at',width:150, title: '入住时间'}
                ,{field:'content',width:200, title: '备注'}
                ,{field:'ctime',width:200, title: '添加时间'}
                ,{field:'right', width:150,toolbar: '#barDemo', title:'操作'}
            ]],
            done: function () {
                $("[data-field='home_id']").css('display','none');
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