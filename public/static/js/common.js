$(function () {
    index          = -1;
    var search_box = $(".search_box");
    $("#search_input").keydown(function (event) {
        var key    = event.keyCode;
        var length = search_box.children("a").length;
        if (key == 13) {
            $wd           = $('#search_input').val()
            if ($wd)
                location.href = '/search-index-' + $wd + '.html';
            else
                location.href = '/search-index.html';
            return false;
        }
        if (key == 38) {
            index--;
            if (index == -1) {
                index = length - 1
            }
        } else {
            if (key == 40) {
                index++;
                if (index == length) {
                    index = 0
                }
            }
        }
        if (key == 38 || key == 40) {
            search_box.children("a").removeClass("on");
            search_box.children("a:eq(" + index + ")").addClass("on");
            var chose = $("#search_box").children(".on").children("span.red").text();
            $("#search_input").val(chose);
            var mtype = $("#search_box").children(".on").attr("data-mtype");
            $("#mtype_index").val(mtype)
        }
        $(this).css({"color": "#333"})
    });
    $("#search_input").keyup(function (event) {
        var key = event.keyCode;
        if (key == 38 || key == 40) {
            return false
        }
        var keyword = $(this).val();
        if (keyword != "" && keyword != '请输入搜索内容') {
            var li = "<a href='/search-source-" + keyword + ".html'> 搜索含<span class='red'>" + keyword + "</span>的整站源码</a>\n\
                        <a href='/search-jquery-" + keyword + ".html'> 搜索含<span class='red'>" + keyword + "</span>的网页特效</a>\n\<" +
                     "a href='/search-templates-" + keyword + ".html'> 搜索含<span class='red'>" + keyword + "</span>的网站模版</a>\n\<p class='p-tip'>提示：键盘↑↓键可进行快速选择！</p>";
            $("#search_box").html(li).removeClass("hide")
        } else {
            $("#search_box").html("").addClass("hide")
        }
    });
    $("#search_input").focus(function (event) {
        var key = event.keyCode;
        if (key == 38 || key == 40) {
            return false
        }
        var keyword = $(this).val();

        if (keyword != "" && keyword != '请输入搜索内容') {
            var li = "<a href='/search-source-" + keyword + ".html'> 搜索含<span class='red'>" + keyword + "</span>的整站源码</a>\n\
                        <a href='/search-jquery-" + keyword + ".html'> 搜索含<span class='red'>" + keyword + "</span>的网页特效</a>\n\<" +
                     "a href='/search-templates-" + keyword + ".html'> 搜索含<span class='red'>" + keyword + "</span>的网站模版</a>\n\<p class='p-tip'>提示：键盘↑↓键可进行快速选择！</p>";
            $("#search_box").html(li).removeClass("hide")
        } else {
            $("#search_box").html("").addClass("hide")
        }
    });
    $("#search_input").blur(function () {
        var is_hover = $("#search_input").attr("data-hover");
        if (is_hover != 1) {
            $("#search_box").addClass("hide")
        }
    });
    $("#search_box").hover(function () {
        $("#search_input").attr("data-hover", 1)
    }, function () {
        $("#search_input").attr("data-hover", 0)
    });

});

function soso() {
    $wd = $('#search_input').val()
    if ($wd)
        location.href = '/search-index-' + $wd + '.html';
    else
        location.href = '/search-index.html';
    return false;
}

function soso2() {
    $wd = $('#sh-text').val()
    if ($wd)
        location.href = '/search-index-' + $wd + '.html';
    else
        location.href = '/search-index.html';
    return false;
}

function checkInputKeyup(obj) {
    var val = obj.val();
    $("input[name=keyword],#search_input").val(val);
}

function Backtop() {
    $("html,body").animate({scrollTop: 0}, 500);
}

//微信登录操作
function wxlogin() {
    layer.closeAll();
    layer.load(1);
    try {
        closeclearInterval();
    } catch (e) {
    }
    $.get('/loginApi-loginQRcode.html', {}, function (data) {
        layer.open({
            type:    1,
            title:   '<i class="fa fa-weixin" aria-hidden="true"></i> 微信扫码登录',
            skin:    'layui-layer-rim', //加上边框
            area:    ['250px', '315px'], //宽高
            content: '<div class="qrcodeModel"><img width="200" height="200" id="qrcodeImg" alt="微信扫一扫登录" src="' + data.url + '"><div class="scan-content"><div class="icon-scan"></div><div class="content"><p>打开<span class="strong">微信</span></p><p>扫一扫登录</p></div></div></div>',
            end:     function () {
                window.clearInterval(c);
                window.clearInterval(o);
            }
        });
        var c = setInterval(function () {
            Check(data.key);
        }, 3000);//每3秒执行一次
        var o = setInterval(over, 120000);//2分钟结束
        layer.closeAll('loading');
    });
}

function closeclearInterval() {
    window.clearInterval(c);
    window.clearInterval(o);
}

function Check(wxkey) {
    $.get('/loginApi-wxjudge.html', {key: wxkey}, function (data) {
        //已扫码
        if (data.success == 1) {
            location.reload();
        }
    });
}

function over() {
    layer.msg('二维码已失效，请重新激活', {
        time: 3000
    }, function () {
        layer.closeAll();
        closeclearInterval();
    });
}

//刷新验证码
function reVerify() {
    $('#verify-img').attr('src', $('#verify-img').attr('src') + '?' + Math.random());
}

//登录
function login(type) {
    if (type == '1') {
        layer.open({
            type:    1,
            title:   '会员登录',
            skin:    'layui-layer-rim', //加上边框
            area:    ['400px'], //宽高
            content: '<div class="loginFormModel">' +
                     '<div class="inpModel"><input placeholder="会员账号" id="username" type="text" /></div>' +
                     '<div class="inpModel"><input placeholder="会员密码" id="password" type="password" /></div>' +
                     '<div class="inpModel verifycode"><input placeholder="验证码" id="verifycode" type="text" /><img id="verify-img" src="' + verify_src + '" class="verify-code" onclick="reVerify();"></div>' +
                     '<div class="two_weeks"><span class="fl"><a href="#">忘记密码？</a></span><span class="fr"><a href="/register.html">立即注册</a></span><div class="clear"></div></div>' +
                     '<div class="inpModel"><input onclick="login(\'2\');" class="d-btn" type="button" value="确认登录"></div>' +
                     '<div class="foot">' +
                     '<a href="/oauth/github_login.html"><img alt="github一键登录" src="/static/images/github.png" width="16" height="16" style="position: relative;top:3px;"> github登录</a>' +
                     /*'<a href="/login-qq.html"><img alt="QQ一键登录" src="/static/images/qqLogin.png" width="16" height="16" style="position: relative;top:3px;"> QQ登录</a>' +
                      '<a href="/login-weibo.html"><img alt="新浪微博一键登录" src="/static/images/weiboLogin.png" width="16" height="16" style="position: relative;top:3px;"> 微博登录</a>' +
                      '<a onclick="wxlogin();"><img alt="微信扫码登录" src="/static/images/weixinLogin.png" width="16" height="16" style="position: relative;top:3px;"> 微信扫码登录</a>*/'</div></div>'
        });
    } else if (type == '2') {
        layer.load(2);
        var username   = $("#username").val();
        var password   = $("#password").val();
        var verifycode = $("#verifycode").val();

        if (username == '') {
            layer.closeAll('loading');
            layer.msg('会员帐号不能为空');
            $("#username").addClass("Validform_error");
            $("#username").focus();
            return false;
        } else {
            $("#username").removeClass("Validform_error");
        }
        if (password == '') {
            layer.closeAll('loading');
            layer.msg('密码不能为空');
            $("#password").addClass("Validform_error");
            $("#password").focus();
            return false;
        } else {
            $("#password").removeClass("Validform_error");
        }
        if (verifycode == '') {
            layer.closeAll('loading');
            layer.msg('验证码不能为空');
            $("#verifycode").addClass("Validform_error");
            $("#verifycode").focus();
            return false;
        } else {
            $("#verifycode").removeClass("Validform_error");
        }

        $.post("/index/login.html", {'username': username, 'password': password, 'verify': verifycode},
            function (json) {
                layer.closeAll('loading');
                layer.msg(json.msg);
                if (json.status == 1) {
                    layer.closeAll();
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else if (json.status == 0) {
                    reVerify();
                }
            }, 'json');
    }
}

//注册
function register() {
    var username   = $("#username").val();
    var nickname   = $("#nickname").val();
    var password   = $("#password").val();
    var repassword = $("#repassword").val();
    var email      = $("#email").val();
    var mail_code  = $("#mail_code").val();
    if (username == '') {
        layer.msg('会员帐号不能为空');
        $("#username").addClass("Validform_error");
        $("#username").focus();
        return false;
    } else {
        $("#username").removeClass("Validform_error");
    }
    if (nickname == '') {
        layer.msg('会员昵称不能为空');
        $("#nickname").addClass("Validform_error");
        $("#nickname").focus();
        return false;
    } else {
        $("#nickname").removeClass("Validform_error");
    }
    if (password == '') {
        layer.msg('密码不能为空');
        $("#password").addClass("Validform_error");
        $("#password").focus();
        return false;
    } else {
        $("#password").removeClass("Validform_error");
    }
    if (password != repassword) {
        layer.msg('两次密码不相同，请重新输入');
        $("#password").addClass("Validform_error");
        $("#repassword").addClass("Validform_error");
        return false;
    } else {
        $("#password").removeClass("Validform_error");
        $("#repassword").removeClass("Validform_error");
    }
    if (email == '') {
        layer.msg('邮箱不能为空');
        $("#email").addClass("Validform_error");
        $("#email").focus();
        return false;
    }
    var pattern = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
    if (!pattern.test(email)) {
        layer.msg('邮箱格式不正确');
        $("#email").addClass("Validform_error");
        $("#email").focus();
        return false;
    } else {
        $("#email").removeClass("Validform_error");
    }
    if (mail_code == '') {
        layer.msg('验证码不能为空');
        $("#mail_code").addClass("Validform_error");
        $("#mail_code").focus();
        return false;
    } else {
        $("#mail_code").removeClass("Validform_error");
    }
    layer.load(2);

    $.ajax({
        type:    "POST",
        url:     "/index/register.html",
        data:    {
            'username':   username,
            'nickname':   nickname,
            'password':   password,
            'repassword': repassword,
            'email':      email,
            'mail_code':  mail_code
        },
        cache:   false,
        success: function (json) {
            layer.closeAll('loading');
            layer.msg(json.msg);
            if (json.status == 1) {
                layer.closeAll();
                setTimeout(function () {
                    location.href = '/';
                }, 2000);
            } else if (json.status == 0) {
                reVerify();
            }
        }
    }, 'json');
    return false;
}


//充值提示
function investTips(zuanshi) {
    layer.open({
        type:    2,
        title:   '友情提示',
        area:    ['680px', '600px'],
        skin:    'layui-layer-rim', //加上边框
        content: ['/member/investTips/integral/' + zuanshi + '.html', 'no']
    });
}

//图片新窗口打开
$('.open-img img').on('click', function () {
    var url = $(this).attr("src")
    window.open(url);
    return false;
});