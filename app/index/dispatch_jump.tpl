<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>跳转提示</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #E6EAEB;
            font-family: Arial, '微软雅黑', '宋体', sans-serif
        }
        
        .alert-box {
            position: relative;
            margin: 96px auto 0;
            padding: 180px 55px 22px;
            border-radius: 10px 10px 0 0;
            background: #FFF;
            box-shadow: 0px 0px 8px rgba(102, 102, 102, 0.5);
            width: 346px;
            color: #FFF;
            text-align: center
        }
        
        .alert-box p {
            margin: 0
        }
        
        .alert-circle {
            position: absolute;
            top: -50px;
            margin-left: 50%;
            left: -120px;
            width: 234px;
            height: 234px;
        }
        
        .alert-sec-circle {
            stroke-dashoffset: 0;
            stroke-dasharray: 735;
            transition: stroke-dashoffset 1s linear
        }
        
        .alert-sec-text {
            position: absolute;
            top: 11px;
            margin-left: 50%;
            left: -38px;
            width: 76px;
            color: #000;
            font-size: 68px;
        }
        
        .alert-sec-unit {
            font-size: 24px
        }
        
        .alert-body {
            margin: 60px 0 20px
        }
        
        .alert-head {
            color: #F00;
            font-size: 28px
        }
        
        .alert-concent {
            margin: 25px 0 14px;
            color: #7B7B7B;
            font-size: 18px
        }
        
        .alert-concent p {
            line-height: 27px
        }
        
        .alert-btn {
            display: block;
            border-radius: 10px;
            background-color: #0072c6;
            height: 55px;
            line-height: 55px;
            width: 100%;
            color: #FFF;
            font-size: 20px;
            text-decoration: none;
            letter-spacing: 2px
        }
        
        .alert-btn:hover {
            background-color: #005493
        }
        
        @media only screen and (max-width: 640px) {
            .alert-box {
                margin: 120px auto 0;
                padding: 120px 35px 22px;
                width: 266px;
            }
            
            .alert-circle {
                top: -80px;
            }
            
            .alert-sec-text {
                top: -30px;
            }
        }
        
        .ignore-info {
            padding-top: 15px;
        }
        
        .ignore-info a {
            margin-top: 10px;
            color: #888;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div id="js-alert-box" class="alert-box">
    <svg class="alert-circle">
        <circle cx="117" cy="117" r="108" fill="#FFF" stroke="#0072c6" stroke-width="17"></circle>
        <circle id="js-sec-circle" class="alert-sec-circle" cx="117" cy="117" r="108" fill="transparent" stroke="#F4F1F1" stroke-width="18" transform="rotate(-90 117 117)"></circle>
        <text class="alert-sec-unit" x="78" y="172" fill="#F00">ERROR</text>
    </svg>
    <div id="js-sec-text" class="alert-sec-text"><?php echo($wait);?></div>
    <div class="alert-body">
        <div id="js-alert-head" class="alert-head"><?php echo(strip_tags($msg));?></div>
        <div class="alert-concent">
            <p>页面将在<span id="wait"><?php echo($wait);?></span>秒后自动跳转...</p>
        </div>
        <a class="alert-btn" id="href" href="<?php echo($url);?>">立即跳转</a>
        <p class="ignore-info"><a href="/">取消跳转，返回网站首页>></a></p>
    </div>
</div>
<script type="text/javascript">
    (function () {
        var wait = document.getElementById('wait'), href = document.getElementById('href').href,
            second = document.getElementById('js-sec-text');
        var interval = setInterval(function () {
            var time = --wait.innerHTML;
            second.innerHTML = wait.innerHTML = time;
            var e = Math.round(time / 15 * 735);
            document.getElementById("js-sec-circle").style.strokeDashoffset = e - 735;
            if (time <= 0) {
                location.href = href;
            }
        }, 1000);
    })();
</script>
</body>
</html>