$(".pick-area4").pickArea({
    "format":"province/city/county", //格式
    "display":"block",
    "width":"420",//设置“省市县”文本边框的宽度
    "height":"48",//设置“省市县”文本边框的高度
    "borderColor":"#435abd",//设置“省市县”文本边框的色值
    "arrowColor":"#435abd",//设置下拉箭头颜色
    "listBdColor":"#435abd",//设置下拉框父元素ul的border色值
    "color":"#435abd",//设置“省市县”字体颜色
    "fontSize":"20px",//设置字体大小
    "hoverColor":"#435abd",
    "paddingLeft":"50px",//设置“省”位置处的span相较于边框的距离
    "arrowRight":"30px",//设置下拉箭头距离边框右侧的距离
    "maxHeight":"300px",
    //"preSet":"",//数据初始化 ；这里的数据初始化有两种方式，第一种是用preSet属性设置，第二种是在a标签里使用name属性设置
    "getVal":function(){//这个方法是每次选中一个省、市或者县之后，执行的方法
        reslfx = $(".pick-area-hidden").val();
        $("#ssq").val(reslfx); 
        // console.log(reslfx)
        shu(reslfx);
        // return reslfx;
        // console.log($(".pick-area-dom").val())

        // thisdom = $("."+$(".pick-area-dom").val());//返回的是调用这个插件的元素pick-area，$(".pick-area-dom").val()的值是该元素的另一个class名，这个class名在dom结构中是唯一的，不会有重复，可以通过这个class名来识别这个pick-area
        // thisdom.next().val($(".pick-area-hidden").val());//$(".pick-area-hidden").val()是页面中隐藏域的值，存放着每次选中一个省、市或者县的时候，当前文本存放的省市县的最新值，每点击一次下拉框里的li，这个值就会立即更新
    }
});

//单选多选
$(function () {
    var initSelectBox = function(selector, selectCallback) {
        function clearBubble(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            } else {
                e.cancelBubble = true;
            }

            if (e.preventDefault) {
                e.preventDefault();
            } else {
                e.returnValue = false;
            }
        }
        var $container = $(selector);
        //  框选事件
        $container
            .on('mousedown', function(eventDown) {
                //  设置选择的标识
                var isSelect = true;
                //  创建选框节点
                var $selectBoxDashed = $('<div class="select-box-dashed"></div>');
                $('body').append($selectBoxDashed);
                //  设置选框的初始位置
                var startX = eventDown.x || eventDown.clientX;
                var startY = eventDown.y || eventDown.clientY;
                $selectBoxDashed.css({
                    left: startX,
                    top : startY
                });
                //  根据鼠标移动，设置选框宽高
                var _x = null;
                var _y = null;
                //  清除事件冒泡、捕获
                clearBubble(eventDown);
                //  监听鼠标移动事件
                $(selector).on('mousemove', function(eventMove) {
                    //  设置选框可见
                    $selectBoxDashed.css('display', 'block');
                    //  根据鼠标移动，设置选框的位置、宽高
                    _x = eventMove.x || eventMove.clientX;
                    _y = eventMove.y || eventMove.clientY;
                    //  暂存选框的位置及宽高，用于将 select-item 选中
                    var _left   = Math.min(_x, startX);
                    var _top    = Math.min(_y, startY);
                    var _width  = Math.abs(_x - startX);
                    var _height = Math.abs(_y - startY);
                    $selectBoxDashed.css({
                        left  : _left,
                        top   : _top,
                        width : _width,
                        height: _height
                    });
                    //  遍历容器中的选项，进行选中操作
                    $(selector).find('.select-item').each(function() {
                        var $item = $(this);
                        var itemX_pos = $item.prop('offsetWidth') + $item.prop('offsetLeft');
                        var itemY_pos = $item.prop('offsetHeight') + $item.prop('offsetTop');
                        //  判断 select-item 是否与选框有交集，添加选中的效果（ temp-selected ，在事件 mouseup 之后将 temp-selected 替换为 selected）
                        var condition1 = itemX_pos > _left;
                        var condition2 = itemY_pos > _top;
                        var condition3 = $item.prop('offsetLeft') < (_left + _width);
                        var condition4 = $item.prop('offsetTop') < (_top + _height);
                        if (condition1 && condition2 && condition3 && condition4) {
                            $item.addClass('temp-selected');
                        } else {
                            $item.removeClass('temp-selected');
                        }
                    });
                    //  清除事件冒泡、捕获
                    clearBubble(eventMove);
                });

                $(document).on('mouseup', function() {
                    $(selector).off('mousemove');
                    $(selector)
                        .find('.select-item.temp-selected')
                        .removeClass('temp-selected')
                        .addClass('selected');
                    $selectBoxDashed.remove();

                    if (selectCallback) {
                        selectCallback();
                    }
                });
            })
            //  点选切换选中事件
            .on('click', '.select-item', function() {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                } else {
                    $(this).addClass('selected');
                }
            })
            //  点选全选全不选
            .on('click', '.toggle-all-btn', function() {
                if ($(this).attr('data-all')) {
                    $(this).removeAttr('data-all');
                    $container.find('.select-item').removeClass('selected');
                } else {
                    $(this).attr('data-all', 1);
                    $container.find('.select-item').addClass('selected');
                }
            });
    };

    initSelectBox('.lfx_container');



// 发布时候调用下就OK了
    function show1(){
        var $elements = $('.selected');
        var len = $elements.length;
        // console.log('有 ' + len + ' 个相同class');
        var newarr = [];
        $elements.each(function() {
            var $this = $(this);
            // console.log($this.prop('tagName'));
            // console.log($this.text())
            // var eee =  $(this).children(".selected").children("input")
            var eee =  $(this).children().val();
            

            newarr.push(eee);
            // console.log(newarr)       
        });
      
        var strs = newarr.join(",");
        $("#jwid").val(strs)
    }
setInterval(show1,500);
//每隔1秒调用一次show1函数
})
//上传
jQuery(function(){
    upload_start();
});
