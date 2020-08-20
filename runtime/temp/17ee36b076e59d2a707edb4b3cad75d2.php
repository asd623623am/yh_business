<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:89:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/goods/goodsadd.html";i:1597830764;s:81:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/layout.html";i:1597672422;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/head.html";i:1597672422;s:86:"/Applications/MxSrvs/www/yh_business/public/../application/admin/view/public/left.html";i:1597672422;}*/ ?>
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
            <blockquote class="layui-elem-quote layui-text">
    <span class="layui-breadcrumb">
        <a href='#'>菜品管理</a>
        <a><cite> 菜品添加</cite></a>
    </span>
</blockquote>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>菜品添加</legend>
</fieldset>
<form class="layui-form">
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">菜品图片</label>
            <button type="button" class="layui-btn" id="addZmImg"><i class="layui-icon">&#xe67c;</i>上传图片</button>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline" style="margin-left: 100px;">
            <input type="hidden" id="logo" name="img">
            <ul id="imgZmList"></ul>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">菜品名称<span style="color: red;">*</span></label>
            <div class="layui-input-block">
                <input type="text" name="name"  autocomplete="off" placeholder="请输入菜品名称" class="layui-input">
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">菜品编号<span style="color: red;">*</span></label>
            <div class="layui-input-block">
                <input type="text" name="code"  autocomplete="off" placeholder="请输入菜品编号" class="layui-input">
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">菜品分类<span style="color: red;">*</span></label>
        <div class="layui-input-inline" id="app">
            <select name="gtid" id="gtid" lay-verify="required" lay-search="">
                <option value="" >请选择菜品分类</option>
                <?php if(is_array($gtData) || $gtData instanceof \think\Collection || $gtData instanceof \think\Paginator): $i = 0; $__LIST__ = $gtData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gtData): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $gtData['gtid']; ?>"><?php echo $gtData['gtname']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">菜品规格</label>
        <div class="layui-input-inline" >
            <select name="gsids" id="gsids"  multiple="multiple" lay-filter="test222" >
                <option value="" >请选择规格</option>
                <?php if(is_array($gsData) || $gsData instanceof \think\Collection || $gsData instanceof \think\Paginator): $i = 0; $__LIST__ = $gsData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gsData): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $gsData['gsid']; ?>"><?php echo $gsData['gsname']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
        <button type="button" style="display: none" class="layui-btn layui-btn-normal" id="get-val">获取选中值</button>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">菜品售价<span style="color: red;">*</span></label>
            <div class="layui-input-block">
                <input type="text" name="selling_price"  autocomplete="off" placeholder="请输入售价" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">菜品原价</label>
            <div class="layui-input-block">
                <input type="text" name="original_price"  autocomplete="off" placeholder="请输入原价" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">会员价</label>
            <div class="layui-input-block">
                <input type="text" name="member_price"  autocomplete="off" placeholder="请输入会员价" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">员工价</label>
            <div class="layui-input-block">
                <input type="text" name="staff_price"  autocomplete="off" placeholder="请输入员工价" class="layui-input">
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">菜品排序</label>
            <div class="layui-input-block">
                <input type="text" name="sort"  autocomplete="off" placeholder="请输入排序值（默认10）" class="layui-input">
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">特色菜品</label>
        <div class="layui-input-block">
            <input type="checkbox" name="is_special" lay-skin="switch" lay-text="ON|OFF">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">开启库存</label>
        <div class="layui-input-block">
            <input type="checkbox" name="is_open_stock" lay-skin="switch" lay-text="ON|OFF">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">库存数量</label>
            <div class="layui-input-block">
                <input type="text" name="stock"  autocomplete="off" placeholder="请输入库存数量" class="layui-input">
            </div>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">菜品介绍</label>
        <div class="layui-input-block">
            <textarea name="trade_description" placeholder="请输入内容" class="layui-textarea"></textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="*">立即添加</button>
            <button type="reset" id='up' class="layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
    <script>
        $(function(){
            layui.use(['form','layer','upload','element','jquery' ], function(){
                var form = layui.form;
                var layer = layui.layer;
                var upload = layui.upload;
                var $ = layui.jquery;
                multiSelect = function() {};
                var uploads = upload.render({
                    elem: '#addZmImg'
                    ,url: '<?php echo url("Brand/bannerUpload"); ?>'
                    ,multiple: true
                    // ,acceptMime:'image/jpg, image/png'
                    // ,number:5
                    ,before: function(obj){
                        //预读本地文件示例，不支持ie8
                        $("#imgZmList").height(110);
                        obj.preview(function(index, file, result){
                            $('#imgZmList').append('<li style="position:relative;width:112px;height: 110px;">' +
                                '<img name="imgZmList" src="' + result + '"width="112" height="112" left="170">' +
                                '<div class="img_close" onclick="deleteElement(this)">X</div></li>');
                            imgMove("imgZmList");
                        });
                        return false;
                    },allDone: function(obj){ //当文件全部被提交后，才触发
                    },done: function(res){
                        if(res.code==1){
                            var strLen = $('#logo').val();
                            if(strLen.length == 0){

                                $('#logo').val(res.src);
                            }else{
                                $('#logo').val($('#logo').val()+','+res.src);
                            }
                        }
                    }
                });
                form.on('submit(*)',function(data){
                    //获取所有规格，多个以逗号分隔,如"1,2,3"
                    var gsids = '';
                    $('select[multiple] option:selected').each(function() {
                        // gsids.push($(this).val());
                        gsids += $(this).val() + ",";
                    });
                    data.field.gsids = gsids;
                    var info = data.field;
                    console.log(info);
                    $.post(
                        "<?php echo url('Goods/goodsAdd'); ?>",
                        info,
                        function(msg){
                            if(msg.code ==1){
                                layer.msg(msg.font);
                                location.href="<?php echo url('Goods/goodsList'); ?>";
                            }else{
                                layer.msg(msg.font, {icon: 5});
                            }
                        },'json'
                    )
                    return false;
                });
            });
        })
    </script>
</form>

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