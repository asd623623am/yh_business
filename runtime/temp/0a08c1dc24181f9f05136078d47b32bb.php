<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:104:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\orders\propertyupdateinfo.html";i:1592557803;s:85:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\layout.html";i:1592206685;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\head.html";i:1591870701;s:90:"E:\PHPserver\wwwroot\default\aliyun_1805\public/../application/admin\view\public\left.html";i:1592371665;}*/ ?>
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
            <form class="layui-form">

<input type="text" value="<?php echo $data['p_id']; ?>" name="p_id" hidden="hidden">
    <div class="layui-form-item">
        <label class="layui-form-label">收款标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" value="<?php echo $data['title']; ?>"  autocomplete="off" placeholder="请输入收款标题" class="layui-input">
        </div>
    </div>
     <span id="dis_id" hidden="hidden"><?php echo $data['d_id']; ?></span>
    <div class="layui-form-item">
        <label class="layui-form-label">收费小区</label>
        <div class="layui-input-block">
          <select name="dis" lay-filter="filter" id="names">
            <option value="">请选择小区</option>
            <?php if(is_array($dis) || $dis instanceof \think\Collection || $dis instanceof \think\Paginator): $i = 0; $__LIST__ = $dis;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
      </div>

      <span id='complexName' hidden="hidden"><?php echo $data['complex']; ?></span>
      <div class="layui-form-item">
         <label class="layui-form-label">收费区</label>
         <div class="layui-input-block">
            <input type="checkbox" id='甲' lay-filter="filter" value="甲" name="" title="甲">
            <input type="checkbox" id='乙' lay-filter="filter" value="乙" name="" title="乙"> 
            <input type="checkbox" id='丙' lay-filter="filter" value="丙" name="" title="丙"> 
            <input type="checkbox" id='丁' lay-filter="filter" value="丁" name="" title="丁"> 
            <input type="checkbox" id='四期' lay-filter="filter" value="四期" name="" title="四期"> 
            <input type="checkbox" id='四期商业' lay-filter="filter" value="四期商业" name="" title="四期商业"> 
            <!-- <input type="checkbox" name="" title="禁用" disabled> -->
         </div>
       </div>

       <div class="layui-form-item">
           <label class="layui-form-label">输入楼号</label>
           <div class="layui-input-block">
               <input type="text" name="home_code" value="<?php echo $data['home_code']; ?>"  autocomplete="off" placeholder="输入楼号" class="layui-input">
           </div>
       </div>
    <div class="layui-form-item">
        <label class="layui-form-label">收费年</label>
        <div class="layui-input-block">
               <input type="text" name="paytime" value="<?php echo $data['paytime']; ?>" autocomplete="off" placeholder="输入收费年" class="layui-input">
           </div>
    </div>  



    <div class="layui-form-item">
        <label class="layui-form-label">物业费/m²</label>
        <div class="layui-input-block">
            <input type="text" name="fee" value="<?php echo $data['fee']; ?>" autocomplete="off" placeholder="请输入物业费" class="layui-input">
        </div>
    </div>

    


    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">期限</label>
            <div class="layui-input-inline" style="width: 100px;">
                <input type="text" name="start_at" value="<?php echo $data['start_at']; ?>" autocomplete="off" id="start_at"  lay-verify="required" class="layui-input">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline" style="width: 100px;">
                <input type="text" name="end_at" value="<?php echo $data['end_at']; ?>" autocomplete="off" id="end_at"  lay-verify="required" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea placeholder="请输入备注" name="content" class="layui-textarea" lay-verify="required"><?php echo $data['content']; ?>
            </textarea>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="*">立即发布</button>
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
              var dis_id = $('#dis_id').text();
              $("#names").find("option[value='"+dis_id+"']").attr("selected",true);
              var complexName = $('#complexName').text();
              var newc = complexName.split('+');
              newc.pop();
              $.each(newc,function(k,v){
                $("#"+v+"").prop("checked",true); 
              })

              form.render();
              $('#up').click(function(){
                  location.href="<?php echo url('Orders/propertyList'); ?>";
              });

                //执行一个laydate实例
                  laydate.render({
                    elem: '#test1' //指定元素
                  });
                  //常规用法
                  laydate.render({
                      elem: '#start_at'
                  });
                  laydate.render({
                      elem: '#end_at'
                  });
                var userName=null;  //定义一个空值
                form.on('select(filter)', function(data){
                    userName=data.elem[data.elem.selectedIndex].text;  //取选中下拉框的文本并赋值给userName
                });
                var qu ='';  //定义一个空值
                form.on('checkbox(filter)', function(data){
                    qu += data.value+'+';
                });   
            //监听提交
            form.on('submit(*)',function(data){
                var info = data.field;

                var arr_box = '';
                $('input[type=checkbox]:checked').each(function() {
                  // arr_box.push($(this).val());
                  arr_box += $(this).val()+'+';
                });

                info.complex = arr_box;
                // info.complex=qu;
                // info.dname=userName;       
                $.post(
                        "<?php echo url('Orders/propertyUp'); ?>",
                        info,
                        function(msg){
                            if(msg.code==1){
                                layer.msg(msg.font, {
                                  icon: msg.code,
                                  time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                }, function(){
                                  location.href="<?php echo url('Orders/propertyList'); ?>";
                                });   
                            } else {
                                layer.msg(msg.font,{icon:msg.code});
                            }


                            console.log(msg);
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