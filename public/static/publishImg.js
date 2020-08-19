//图片移动
function imgMove(obj) {
    var oUl = document.getElementById(obj);
    var aLi = oUl.getElementsByTagName("li");
    var disX = 0;
    var disY = 0;
    var minZindex = 1;
    var aPos = [];
    var leftbz = 0;
    var topbz = 0;
    for (var i = 0; i < aLi.length; i++) {
        if (leftbz == 5) {
            leftbz = 1;
            topbz += 1;
            var fdiv = (topbz + 1) * 140;
            oUl.style.height = fdiv + 'px';
        }
        else {
            leftbz += 1;
        }
        //var l = aLi[i].offsetLeft;
        //var t = aLi[i].offsetTop;
        //此处注意，我是按照控件算出来的。尴尬。。。/(ㄒoㄒ)/~~
        var l = 130 * (leftbz - 1) +10 *(leftbz - 1)+10;
        var t = 130 * topbz;

        aLi[i].style.top = t + "px";
        aLi[i].style.left = l + "px";
        aPos[i] = { left: l, top: t };
        aLi[i].index = i;


    }
    for (var i = 0; i < aLi.length; i++) {
        aLi[i].style.position = "absolute";
        aLi[i].style.margin = 0;
        // setDrag(aLi[i]);
    }

    //碰撞检测
    function colTest(obj1, obj2) {
        var t1 = obj1.offsetTop;
        var r1 = obj1.offsetWidth + obj1.offsetLeft;
        var b1 = obj1.offsetHeight + obj1.offsetTop;
        var l1 = obj1.offsetLeft;

        var t2 = obj2.offsetTop;
        var r2 = obj2.offsetWidth + obj2.offsetLeft;
        var b2 = obj2.offsetHeight + obj2.offsetTop;
        var l2 = obj2.offsetLeft;

        if (t1 > b2 || r1 < l2 || b1 < t2 || l1 > r2) {
            return false;
        } else {
            return true;
        }
    }
    //勾股定理求距离
    function getDis(obj1, obj2) {
        var a = obj1.offsetLeft - obj2.offsetLeft;
        var b = obj1.offsetTop - obj2.offsetTop;
        return Math.sqrt(Math.pow(a, 2) + Math.pow(b, 2));
    }
    //找到距离最近的
    function findMin(obj) {
        var minDis = 999999999;
        var minIndex = -1;
        for (var i = 0; i < aLi.length; i++) {
            if (obj == aLi[i]) continue;
            if (colTest(obj, aLi[i])) {
                var dis = getDis(obj, aLi[i]);
                if (dis < minDis) {
                    minDis = dis;
                    minIndex = i;
                }
            }
        }
        if (minIndex == -1) {
            return null;
        } else {
            return aLi[minIndex];
        }
    }
}
//图片删除
function deleteElement(Obj) {
    Obj.parentNode.parentNode.removeChild(Obj.parentNode);
}
//描述
function divClick(obj) {
    layer.prompt({ title: '请填新的描述，并确认', formType: 2 }, function (text, index) {
        obj.innerHTML = text;
        layer.close(index);
    });
}

//图片裁剪
layui.config({
    base: "../../static/js/"
}).extend({
    "croppers": "croppers"
});
function croppers_pic(obj) {
    var src = obj.parentNode.childNodes["0"].src;
    layui.use(["croppers"], function () {
        var croppers = layui.croppers;
        croppers.render({
            area: ['900px', '600px']  //弹窗宽度
            , imgUrl: src
            , url: "/user/upload.asp"  //图片上传接口返回和（layui 的upload 模块）返回的JOSN一样
            , done: function (url) { //上传完毕回调
                //更改图片src
                obj.parentNode.childNodes["0"].src = url;
            }
        });
    });
}