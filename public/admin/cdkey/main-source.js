function runcode() {
    var prefix = $("#prefix").val();
    var suffix = $("#suffix").val() + " ";
    var number = $("#number").val() ? $("#number").val() : 500;
    var startpos = $("#startpos").val() ? $("#startpos").val() : 1;
    number = parseInt(number) + parseInt(startpos);
    var numberlength = number.toString().length;
    if (numberlength > 7) {
        $(".buttons").append('<div class="alert alert-danger alert-dismissible" style=" margin-top:10px; margin-bottom:0; display:table;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong>卡序号范围：1~9999999!</strong>  请核实输入的起始值及生成条数. 如果需求确实如此, 请联系工具作者修改参数: <a target="_0" href="http://wpa.qq.com/msgrd?v=3&uin=659998662&site=qq&menu=yes">QQ659998662</a> Email:<a href="mailto:659998662@qq.com">659998662@qq.com</a></div>');
        return false
    }
    var plength = $("#length").val() ? $("#length").val() : 15;
    if (plength > 128) {
        $(".buttons").append('<div class="alert alert-danger alert-dismissible" style=" margin-top:10px; margin-bottom:0; display:table;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong>密码位数最大为128位!</strong>  请核实输入的密码位数. 如果需求确实如此, 请联系工具作者修改参数: <a target="_0" href="http://wpa.qq.com/msgrd?v=3&uin=659998662&site=qq&menu=yes">QQ659998662</a> Email:<a href="mailto:57965523@qq.com">659998662@qq.com</a></div>');
        return false
    }
    var code = "",
        cnum = "",
        passn = "",
        zeron = "",
        getpwd = "";
    var char = ["0123456789", "abcdefghijklmnopqrstuvwxyz", "ABCDEFGHIGKLMNOPQRSTUVWXYZ", "_", "-", "#", "/", ".", "'", "!", "(", "[]", "{}", "_", "-", "#", "/", "$", "%", "@", "&", "^"];
    var chars = "";
    var charli = $(".schar");
    for (i = 0; i < charli.length; i++) {
        if (charli.eq(i).is(':checked')) {
            chars += char[i]
        }
    }
    var maxPos = chars.length;

    function genpwd() {
        var pwd = "";
        for (j = 0; j < plength; j++) {
            pwd += chars.charAt(Math.floor(Math.random() * maxPos))
        }
        return pwd
    }
    function putzero(a) {
        var ilength = a.toString().length;
        var zlength = numberlength - ilength;
        var zero = "";
        var zeroJ = ['', '0', '00', '000', '0000', '00000', '000000', '0000000', '00000000', '000000000'];
        zero = zeroJ[parseInt(zlength)];
        return zero
    }
    for (i = startpos; i < number; i++) {
        zeron = putzero(i);
        getpwd = genpwd();
        if (i === number - 1) {
            cnum += prefix + zeron + i + suffix;
            passn += getpwd;
            code += prefix + zeron + i + suffix + getpwd
        } else {
            cnum += prefix + zeron + i + suffix + "\n";
            passn += getpwd + "\n";
            code += prefix + zeron + i + suffix + getpwd + "\n"
        }
        $("#cnum").val(cnum);
        $("#passn").val(passn);
        $("#code").val(code);
        $(".buttons").removeAttr("title");
        $(".buttons > button").removeAttr("disabled")
    }
    return false
}
$(function() {

    $('.form-group .common-options li').click(function () {
        var val = $(this).data('value');
        $(this).parent().parent().find('input').val(val)
    })

    $(".selectall").on("click", function() {
        $(this).parents("fieldset").find("textarea").select()
    });

    $(".export").on("click", function() {
        var t = $(this).attr("ttt");
        var txt = $("#" + t).val();
        var fn = $(this).parents(".modal-content").find(".filename").val();
        $.post("export.php", {
            code: txt,
            filename: fn
        }, function(data) {
            $(".dli").show();
            $(".downloadlist").append('<li>' + data + '</li>')
        })
    });

    $(document).on("click", ".delthis", function() {
        var t = $(this);
        var fn = t.attr("fn");
        var sgl = t.attr("sgl");
        $.post("delete.php", {
            fn: fn,
            sgl: sgl
        }, function(data) {
            if (data) {
                t.removeAttr("title").text("已删除");
                t.removeClass("delthis").addClass("deleted").attr("disabled", "disabled");
                t.parents("li").find(".downloadlink").removeAttr("href").css({
                    "text-decoration": "line-through",
                    "color": "#999"
                })
            } else {
                t.text(data)
            }
        })
    })
});