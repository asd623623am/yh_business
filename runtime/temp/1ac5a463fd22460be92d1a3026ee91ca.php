<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:89:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\order\form.html";i:1592382592;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1592206685;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1591870701;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1592371665;}*/ ?>
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
    <div class="layui-header">
    <a href="/admin.php/Index/Index"><div class="layui-logo">小码旺铺物业管理系统后台</div></a>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <!-- <ul class="layui-nav layui-layout-left">
        <li class="layui-nav-item"><a href="">邮件管理</a></li>
        <li class="layui-nav-item"><a href="">消息管理</a></li>
        <li class="layui-nav-item"><a href="">授权管理</a></li>
    </ul> -->
    <ul class="layui-nav layui-layout-right">
        <li class="layui-nav-item">
            <a href="javascript:;">
                <img src="__STATIC__/images/d833c895d143ad4b6eba587980025aafa50f06f6.jpg" class="layui-nav-img">
                欢迎<?php echo \think\Session::get('admin.admin_name'); ?>登录
            </a>
        </li>
        <li class="layui-nav-item"><a href="<?php echo url('Login/logout'); ?>">注销</a></li>
    </ul>
</div>
    <!--左边布局-->
    <style>
    .leftchecked{
        color: #00d6be!important;
        font-weight: bold!important;
    }
    .layui-box1{
        text-align: center;
        padding: 20px 0;
    }
    .layui-p-b{
        display: flex;
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
        <ul class="layui-nav layui-nav-tree"  lay-filter="test">

            <!--<li class="layui-nav-item layui-nav-itemed">-->
                <!--<a class="" href="javascript:;">权限节点管理</a>-->
                <!--<dl class="layui-nav-child">-->
                    <!--<dd><a href="<?php echo url('Power/poweradd'); ?>">节点添加</a></dd>-->
                    <!--<dd><a href="<?php echo url('Power/powerList'); ?>">节点列表展示</a></dd>-->
                <!--</dl>-->
            <!--</li>-->

            <?php if(is_array($AllMenu) || $AllMenu instanceof \think\Collection || $AllMenu instanceof \think\Paginator): $i = 0; $__LIST__ = $AllMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;
                if( strtolower(request()->controller()) == strtolower(ltrim( $v['node_url'] ,'/' )) ){
                    echo '<li class="layui-nav-item layui-nav-itemed">';
                }else{
                    echo '<li class="layui-nav-item">';
                }
                ?>

                <a class="" href="javascript:;"><?php echo $v['node_name']; ?></a>
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
        $(".layui-nav").on("click","li",function () {
           console.log(1);
        })
    });
</script>
    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            
<div style="padding: 20px; background-color: #F2F2F2;">
<span class="layui-breadcrumb">
    <a href='#'>收费管理</a>
    <a href='<?php echo $show['back_url']; ?>'><?php echo $show['back_title']; ?></a>
    <a><cite><?php echo $show['title']; ?></cite></a>
</span>
</div>
<div style="padding: 20px; background-color: #F2F2F2;">
    <div class="layui-row layui-col-space15">
        <form class="layui-form">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            <input type="hidden" name="type" value="<?php echo $data['type']; ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">房屋</label>
                <div class="layui-input-block">
                    <?php if(!empty($data["id"])): ?>
                    <input type="hidden" name="house_id" value="<?php echo $data['house_id']; ?>">
                    <input type="text"  value="<?php echo $data['house']; ?>" readonly  class="layui-input">
                    <?php else: ?>
                    <select name="house_id"  lay-filter="get-house" lay-verify="required" lay-search="">
                        <option value=""></option>
                        <?php if(is_array($houseList) || $houseList instanceof \think\Collection || $houseList instanceof \think\Paginator): $i = 0; $__LIST__ = $houseList;if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$house): $mod = ($i % 2 );++$i;?>
                        <option class="choose-house" value="<?php echo $house['id']; ?>" data-fee="<?php echo $house['fee']; ?>"><?php echo $house['owner']; ?>-<?php echo $house['house']; ?></option>
                        <?php endforeach; endif; else: echo "暂时没有数据" ;endif; ?>
                    </select>
                    <?php endif; ?>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">标题</label>
                <div class="layui-input-block">
                    <input type="text" name="title"  value="<?php echo $data['title']; ?>" autocomplete="off" lay-verify="required"  class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">应缴金额</label>
                <div class="layui-input-block">
                    <input type="text" id="fee" name="fee" value="<?php echo $data['fee']; ?>"  autocomplete="off" lay-verify="required|number"  class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">赔偿项</label>
                <div class="layui-input-block">
                    <input type="text" name="compensation" value="<?php echo $data['compensation']; ?>"  autocomplete="off"  class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">凭证号</label>
                <div class="layui-input-block">
                    <input type="text" name="voucher" value="<?php echo $data['voucher']; ?>"  class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">期限</label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="start_at" value="<?php echo $data['start_at']; ?>"  autocomplete="off" id="start_at"  lay-verify="required" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="end_at" value="<?php echo $data['end_at']; ?>" autocomplete="off" id="end_at"  lay-verify="required" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">发布</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="status" lay-filter="switch-status"  lay-skin="switch" lay-text="发布|未发布" <?php if($data['status']==1): ?>checked<?php endif; ?>>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <a class="layui-btn  layui-btn-primary"   href="javascript:history.go(-1)">  <i class="layui-icon">&#xe65c;</i>返回</a>
                    <button class="layui-btn" lay-submit lay-filter="submit"><?php if(empty($data['id'])): ?>添加<?php else: ?>修改<?php endif; ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        layui.use(['form','layer','laydate'], function(){
            var form = layui.form;
            var layer = layui.layer;
            var laydate = layui.laydate;

            //常规用法
            laydate.render({
                elem: '#start_at'
            });
            laydate.render({
                elem: '#end_at'
            });
            //监听提交
            form.on('submit(submit)',function(data){
                $.post("<?php echo $show['submit_url']; ?>",data.field,function(data){
                    layer.msg(data.msg,{time:1000},function(){
                        if(data.code==0) {
                            location.href = "<?php echo $show['back_url']; ?>";
                        }
                    });
                },'json');
                return false;
            });
            form.on('select(get-house)',function(data){
                var fee=$(data.elem[data.elem.selectedIndex]).data("fee");
                $("#fee").val(fee);
            });
            form.on('switch(switch-status)', function(data){
                $("input[name='status']").val(this.checked ?1:0);
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