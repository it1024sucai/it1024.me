;(function(factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports !== 'undefined') {
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(function($) {
    var method = function(ele, options) {
        this.$ele = ele,
        this.defaults = {
            row: 4,       // 行
            column: 4,    // 列
            spacing: 0,   // 空隙
            click: null,  // 点击触发的对象
            time: 2000,   // 匀速运动的时间
            afterClick: function($this) {}, // 点击回调
            onEnd: function(target) {} // 结束时回调
        },
        this.target,
        this.opt = $.extend({},this.defaults, options);
    }
    method.prototype = {
        init: function() {
            var $this = this.$ele;
            var opt = this.opt;
            var row = opt.row;
            var col = opt.column;
            var spacing = opt.spacing;
            var click = opt.click;
            var allNumber = 2 * (row + col) - 4;
            var line = row-2;  // 除去上下的行数
            var length = $this.children('li').length;
            var afterClick = opt.afterClick;
            
            if(length < allNumber){
                for(var i=length;i<=(allNumber-length);i++){
                    $this.append("<li>"+(i+1)+"</li>");
                }
            }
            var children = $this.children('li');
            var width = children.eq(0).width() || 0;
            var height = children.eq(0).height() || 0;

            //元素初始化
            $this.css({
                position:'relative',
                width: col * width + (col-1) * spacing,
                height: row * height + (row-1) * spacing
            });
            children.css({
                position:'absolute'
            });

            if(line == 0){
                initOne();
            }else{
                initTwo();
            }

            // 初始化函数
            // 此时分成4个部分，上、右、下、左
            // 上： 0  ~  col-1
            // 右： col ~ col+line
            // 下： col+line+1 ~ 2*col+line-1
            // 左： else

            // 如果只有两行
            // 此时分成4个部分，上、右、下、左
            function initOne(){
                children.each(function(index){
                    if(index >=0 && index <= (col-1)){
                        $(this).css({
                            top:0,
                            left: index * width + index * spacing
                        });
                    }else{
                        $(this).css({
                            bottom:0,
                            right: index%col*width
                        });
                    }
                });
            }
            // 如果大于两行
            function initTwo(){
                children.each(function(index){
                    if(index >=0 && index <= (col-1)){
                        $(this).css({
                            top:0,
                            left: index * width + index * spacing
                        });
                    }else if(index >= col && index <= (col+line-1)){
                        $(this).css({
                            top:((index+1-col))*(height+spacing),
                            right: 0
                        });
                    }else if(index >= (col+line) && index <= (2*col+line-1)){
                        $(this).css({
                            bottom:0,
                            right:(index-((col+line)))*(width+spacing)
                        });
                    }else{
                        $(this).css({
                            left:0,
                            bottom:(index-(2*col+line-1))*(height+spacing)
                        });
                    }
                });
            }

            var target = $this.target || Math.floor(Math.random()*allNumber+1); // 目标，指定或随机
            var index = 0; // 位置
            var stop ;
            var flg = false; // 抽奖是否正在运行
            /*
                加速度公式
                v1 = v0 + a*t;注意加速度的v代表时间
                此时的t可以我们自己定义，所以是常量，所以只需要求a的值
            */
            var a = -50;
            var v0 = 500;
            var t = 0.0 , v ;
            var time = this.opt.time; // 匀速运行的时间
            
            $(click).on('click', function(){
                if(!flg){
                    flg = true;
                    afterClick && afterClick.call(this, $this);
                    target = $this.target || Math.floor(Math.random()*allNumber+1);
                    speedUp();
                }else{
                    return;
                }
            });
            // 加速
            function speedUp(){
                runner(index);
                if(v <= 50){
                    clearTimeout(stop);
                    v = 50;
                    t = 0.0;
                    uniform(); // 跳转到匀速
                }else{
                    t++;
                    v = v0 + a*t;
                    stop = setTimeout(speedUp, v);
                }
            }
            // 匀速
            function uniform(){
                stop = setTimeout(uniform, v);
                if(t == time/100){
                    clearTimeout(stop);
                    t = 0.0;
                    speedDown();
                }else{
                    t++;
                }
                runner(index);
            }
            // 减速
            function speedDown(){
                var stop3 = setTimeout(speedDown, v);
                if(v >= 300){
                    v = 300;
                    if(index == target-1){
                        clearTimeout(stop3);
                        opt.onEnd(target);
                        flg = false;
                    }
                }else{
                    t++;
                    v = v - a*t;
                }
                runner(index);
            }
            // index++
            function runner(i){
                children.removeClass('on').eq(index).addClass('on');
                i++;
                if(i == allNumber){
                    index = 0;
                }else{
                    index = i;
                }
            }
        },
        setTarget: function(target){
            var $this = this.$ele;
            $this.target = target;
        }
    }
    $.fn.luckdraw = function(opt, target) {
        var luckdraw = new method(this, opt);
        luckdraw.setTarget(target);
        luckdraw.init();
        return this;
    }
}));