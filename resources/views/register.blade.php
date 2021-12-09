<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@lang('dsy::reg.注册')</title>
    <style>
        @font-face {
            font-family: "cloud-iconfont";
            font-style: normal;
            font-weight: normal;
            src: url("https://at.alicdn.com/t/font_1449502_6f2vhyugrwg.ttf") format("truetype");
        }

        ::-webkit-input-placeholder {
            color: #ffffff;
        }

        .cloud-icons {
            font-family: "cloud-iconfont";
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
        }

        .register {
            width: 100%;
            height: 100%;
        }

        .register-title {
            width: 100%;
            height: 20%;
            margin-top: 20px;
        }

        .ml {
            margin-left: 20px;
        }

        .title {
            color: #ffffff;
            font-size: 24px;
        }

        .content {
            margin-top: 10px;
            color: #333333;
            font-size: 15px;
        }

        .form {
            margin-top: 32px;
        }

        .form-title {
            color: #000;
            font-size: 19px;
            font-weight: 500;
        }

        .username {
            position: relative;
            width: 89%;
            height: 50px;
            border: 1px solid #DDDDDD;
            margin-top: 20px;
            border-radius: 10px;
        }

        .verify {
            position: relative;
            width: 62%;
            height: 50px;
            border: 1px solid #DDDDDD;

            border-radius: 10px;
        }

        .verify-btn {
            /* position: absolute;
            right: 17px;
            top: 231px; */
            width: 25%;
            height: 50px;
            line-height: 50px;
            background: #3688ff;
            border-radius: 10px;
            text-align: center;
        }

        .icon {
            position: absolute;
            width: 10%;
            height: 100%;
            line-height: 50px;
            text-align: center;
            font-size: 20px;
            color: #999999;
            /* background-color: #000000; */
        }

        .username input,
        .verify input {
            position: absolute;
            left: 10%;
            height: 100%;
            width: 90%;
            border: none;
            border-radius: 10px;
            /* color: #bbbbbb; */
            font-size: 14px;
            background: #202127;
            color: #fff;

        }

        .submit-button {
            height: 170px;
        }

        .submit {
            margin-top: 40px;
            width: 94%;
            height: 50px;
            line-height: 50px;
            text-align: center;
            border-radius: 10px;
            font-size: 18px;
            background: linear-gradient(to right, #FFD063, #FFC134);
        }

        .advertisement {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 80px;
            z-index: 99;
            background-color: #e2e2e2;
        }

        .advertisement-img {
            width: 41px;
            height: 41px;
            margin-top: 22px;
            margin-left: 26px;
        }

        .advertisement-content {
            position: absolute;
            top: 20px;
            left: 80px;
            color: #ffffff;
        }

        .advertisement-btn {
            position: absolute;
            right: 35px;
            top: 20px;
            width: 100px;
            text-align: center;
            height: 40px;
            line-height: 40px;
            background: #FFC135;
            border-radius: 20px;
        }

        .close {
            position: absolute;
            top: 4px;
            right: 5px;
            width: 14px;
            height: 14px;
        }

        .select_div {
            position: relative;
            width: 89%;
            height: 50px;
            border: 1px solid #DDDDDD;
            margin-top: 20px;
            border-radius: 10px;
            margin-left: 20px;
            color: #ffffff;
        }

        .select_type {
            width: 40%;
            float: left;
            margin-top: 15px;
            margin-left: 10px;
        }

        .mobile_pre {
            height: 100%;
            border-radius: 10px;
            border: 1px solid #ccc;
            background: #202127;
            color: #fff;
        }
    </style>

</head>

<body style="background: #202127">
<div class="advertisement" id="advertisements" style="background: #1B1C1E;position: fixed;top:0px">
    <div class="advertisement-img">
        <img style="width:100%" src="/img/dz.png" alt="">
    </div>
    <div class="advertisement-content">
        <div style="font-size:12px">大正创投 @lang('dsy::reg.官方APP')</div>
        <div style="font-size:14px;margin-top:5px;">@lang('dsy::reg.技术引领未来')</div>
    </div>


</div>
<div class="register">




    <div class="form">

        <div class="select_div" style="height: 45px">
         <div class="select_type">
                <input type="radio" name="type" value="mobile" checked>@lang('dsy::reg.手机注册')
            </div>
        </div>

        <div class="username ml" id="show_mobile" >
            <i class="cloud-icons icon">&#xe689;</i>
            <input type="text" placeholder="@lang('dsy::reg.请输入手机号或邮箱')" id="mobile" name="mobile" style="width: 80%;left: 15%;">
        </div>

        <div class="ml" style="margin-top: 20px;display: flex;width: 89%;justify-content: space-between;">
            <div class="verify">
                <i class="cloud-icons icon" style="width:15.3%">&#xe6bf;</i>
                <input style="width: 84.7%;left: 15.3%;" placeholder="@lang('dsy::reg.验证码')" type="number" id="text" name="sms" value="">
            </div>
            <div class="verify-btn get-code" id="set"
                 style="margin-left: 1rem; padding:0 10px; background:#3688ff;color:#fff;">@lang('dsy::reg.获取验证码')
            </div>
        </div>
        <div class="username  ml">
            <i class="cloud-icons icon">&#xe777;</i>
            <input placeholder="@lang('dsy::reg.请输入登录密码格式')" type="password" id="password" name="password" value="">
        </div>

        <div class="username  ml">
            <i class="cloud-icons icon">&#xe777;</i>
            <input placeholder="@lang('dsy::reg.请确认登录密码')" type="password" id="repassword" value="">
        </div>

        <div class="username  ml">
            <view class="cloud-icons icon">&#xe693;</view>
            <input placeholder="@lang('dsy::reg.请输入邀请码')" id="code" name="code" value="">
        </div>

        <div class="submit-button ml">
            <div class="submit" id="btn-submit" style="background:#3688ff;color:#fff;">@lang('dsy::reg.注册')</div>
        </div>


    </div>
</div>

<div class="advertisement" id="advertisements" style="background: #1B1C1E">
    <div class="advertisement-img">
        <img style="width:100%" src="/img/dz.png" alt="">
    </div>
    <div class="advertisement-content">
        <div style="font-size:12px">大正创投 @lang('dsy::reg.官方APP')</div>
        <div style="font-size:14px;margin-top:5px;">@lang('dsy::reg.技术引领未来')</div>
    </div>
    <div class="advertisement-btn" style="background:#3688ff;">
        <a style="text-decoration:none;color:#fff;" onclick="appDown()">@lang('dsy::reg.下载')</a>
    </div>
    <div class="close" onclick="closeAd()">
        <img style="width:100%" src="/img/close.png" alt="">
    </div>
</div>
</body>

<script src="https://cdn.bootcdn.net/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/layer/3.1.1/layer.min.js"></script>

<script>

    // 获取url中参数方法
    (function ($) {
        $.getUrlParam = function (name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            // console.log(r)
            if (r != null) return unescape(r[2]);
            return null;
        }
    })(jQuery);

    function appDown() {
        var u = navigator.userAgent;
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
        if(isiOS){
            //alert('ios');
            window.location.href = "{{config('api::settings.ios_download', [])}}"
        }else if(isAndroid){
            //alert('android');
            window.location.href = "{{config('api::settings.android_download', [])}}"
        }
    }

    var type = 'email';
    var lang = $.getUrlParam('locale');
    if (lang == null) {
        lang = 'en_US';
    }
    console.log(lang);

    $("input[type='radio']").click(function () {
        var val = $('input:radio[name="type"]:checked').val();

            $('#show_mobile').show();
            $('#show_email').hide();
            type = 'mobile';
            console.log(type);

    });


    var host = "";
    $('#btn-submit').click(function () {
        var mobile = $("input[name='mobile']").val();
        var email = $("input[name='email']").val();
        var password = $("input[name='password']").val();
        var repassword = $("#repassword").val();
        var code = $("input[name='code']").val();
        var sms = $("input[name='sms']").val();
        var mobile_pre = $('#mobile_pre').val();
        var name = $('#name').val();
        var error_msg = '';

            if (!mobile) {
                error_msg += "@lang('dsy::reg.请填写手机或页面')<br>";
            }

        if (!sms) {
            error_msg += "@lang('dsy::reg.请填写验证码')<br>";
        }
        /*  if (!code) {
              error_msg += "@lang('dsy::reg.请填写推荐码')<br>";
        }*/
        if (!password) {
            error_msg += "@lang('dsy::reg.请填写登录密码')<br>";
        }
        if (error_msg != '') {
            layer.msg(error_msg);
            return false;
        }
        var rule = '';
        //if(!/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,20}$/.test(password)){
        console.log(password.length);
        if (password.length < 8) {
            layer.msg("@lang('dsy::reg.请输入登录密码格式')");
            return false;
        }
        if (password != repassword) {
            layer.msg("@lang('dsy::reg.确认登录密码不相同')");
            return false;
        }
            var dataJson = {
                mobile: mobile,
                password: password,
                password_confirmation:repassword,
                invite_code: code, //邀请码
                code: sms, // 验证码
                mobile_pre: mobile_pre,
                _token: $('meta[name="csrf-token"]').attr('content')
            };


        var uri = '{{route('m.dsy.api.user.register')}}';
        $.ajax({
            url: host + uri,
            type: 'post',
            data: JSON.stringify(dataJson),
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            headers: {
                'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Language': lang,
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer null");
            },
            success: function (res) {
                layer.msg("@lang('dsy::reg.注册成功')");
                setTimeout(function () {
                    appDown();
                }, 1500);
            },
            error:function(res){
                layer.msg(res.responseJSON.message);
            }
        });
    });
    var check = true;

    var code = $.getUrlParam('code');
    var lang = $.getUrlParam('locale');

    console.log(lang)
    $("input[name='code']").val(code)
    // console.log($.getUrlParam('code'))
    if ($.getUrlParam('code') != null) {
        $("input[name='code']").attr('disabled', true);
    }

    $('#set').click(function () {
        var mobile = $('#mobile').val();
        if((/^1[23456789]\d{9}$/.test(mobile))){
            $.ajax({
                url: host + '{{route('m.dsy.api.user.mobile')}}',
                type: 'post',
                data:{mobile:mobile,type:'mobile_register',mobile_pre:'86',_token: $('meta[name="csrf-token"]').attr('content')},
                // contentType: "application/json; charset=utf-8",
                dataType: 'json',
                headers: {
                    'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Language': lang,
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer null");
                },
                success: function (res) {
                    layer.msg("@lang('dsy::reg.已发送')")
                    time()
                },
                error: function (json) {
                    layer.msg("@lang('dsy::reg.网络错误，请重试')")
                }
            });
        }else{
            $.ajax({
                url: host + '{{route('m.dsy.api.user.email')}}',
                type: 'post',
                data:{email:mobile,type:'email_register',mobile_pre:'86',_token: $('meta[name="csrf-token"]').attr('content')},
                // contentType: "application/json; charset=utf-8",
                dataType: 'json',
                headers: {
                    'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Language': lang,
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer null");
                },
                success: function (res) {
                    layer.msg("@lang('dsy::reg.已发送')")
                    time()
                },
                error: function (json) {
                    layer.msg("@lang('dsy::reg.网络错误，请重试')")
                }
            });
        }

    })


    function isValidPhone(phone) {

      /*  if (!/^1[23456789]\d{9}$/.test(phone)) {
            layer.msg("@lang('dsy::reg.请输入正确手机号')")
            return false;
        }*/
        /*if (
            !/^([A-Za-z0-9_\-\.\u4e00-\u9fa5])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,8})$/.test(
                phone
            )
        ) {
            //layer.msg("邮箱格式有误");
            return false;
        }*/
        return true;
    }

    // 倒计时
    function time() {
        var that = $('.get-code');
        var timeo = 60;
        var timeStop = setInterval(function () {
            timeo--;
            if (timeo > 0) {
                that.text(timeo + 's');
                that.attr('disabled', 'disabled'); //禁止点击
                check = false;
            } else {
                timeo = 60; //当减到0时赋值为60
                layer.msg("@lang('dsy::reg.获取验证码')")
                clearInterval(timeStop); //清除定时器
                that.removeAttr('disabled'); //移除属性，可点击
                check = true;
            }
        }, 1000)
    }

    function closeAd() {
        let advertisement = document.getElementById('advertisements')
        advertisement.style.display = "none"
    }

</script>

</html>
