var inputObj = {
    'isPhone' : false,
    'isValif' : false,
}
var timer = null;
var hasBind = false;
var hasTel = true;
var bindTel = '18792188245';
var wait=0,isloading=false;
$(function(){
    FP.ajaxPost(
        '/my/hasBindMobile',
        null,
        function(v){
            if(v.f){
                return
            }else{
                $('.poupVerifyBox').addClass('g-poupBox-show');                    //弹出层
            }
        },
        function(){
            // FP.showError("请重试");
        }
    );
    //判断是否有收花手机号
/*    if(hasTel){
        $('#SendCode').hide();
        $('#confirmBind').show();
    }else{
        $('#SendCode').show();
        $('#confirmBind').hide();
    }*/

    //更改默认收花号码   填写新号码
    $("#uclogin_mobile").bind("input",function(){
        if(/^1\d{10}$/.test($(this).val())){
            $('#SendCode').show();
            //$('#confirmBind').hide();
            $("#SendCode").prop("disabled",false);
            $('#phoneHolder').html($("#uclogin_mobile").val())
            inputObj.isPhone = true;
        }else{
            //$("#confirmBind").prop("disabled",true);
            $("#SendCode").prop("disabled",true);
            inputObj.isPhone = false;
        }
        if($('#SendCode').text() != '下一步'){
            clearTimeout(timer);
            wait = 0;
            $('#SendCode').text('下一步');
            $("#SendCode").prop("disabled",false);
        }
    });

    //输入验证码
    $('#hiddenInput').on('input',function(){
        var val = $(this).val();
        $('.inputBox .number').text('').removeClass('current');
        $('.inputBox .number').eq(val.length).addClass('current');
        for(var i = 0; i < val.length; i++){
            $('.inputBox .number').eq(i).text(val.charAt(i));
        }
        val.length >= 4 && isSubmit(inputObj.isPhone,true);
    }).on('focus',function(){
        var len = $(this).val().length || 0;
        $('.inputBox .number').eq(len).addClass('current');
    }).on('blur',function(){
        $('.inputBox .number').removeClass('current');
    })

    //验证是否手机号 验证码都存在并正确
    function isSubmit(val1,val2){
        if(val1 && val2){
            $('#submit').removeClass('disabled');
        }else{
            $('#submit').addClass('disabled');
        }
    }

    //收花手机号不一致，点击下一步发送验证码
    $("#SendCode").on(FP.clickEvent,function(){
        if($(this).prop('disabled')) return;
        sendCode();
    });

    //选择语音验证码
    $("#chooseVoice").on(FP.clickEvent,function(){
        if($(this).prop('disabled')) return;
        sendVoiceCode();
    });

    $("#sendAgin").on(FP.clickEvent,function(){
        if($(this).prop('disabled')) return;
        
        sendCode();
        //$('input').blur();

    });

    //收花手机号一致，直接绑定
/*    $('#confirmBind').on(FP.clickEvent,function(){
        alert('aaa')
    });*/

    //点击遮罩层消失
    $('.poupVerifyBox .bgMask').on(FP.clickEvent,function(e){
        $('#hiddenInput').blur();
        $('.poupVerifyBox').removeClass('g-poupBox-show');
    });
    //完成点击关闭
    $('.closeImg').on(FP.clickEvent,function(e){
        $('.poupVerifyBox').removeClass('g-poupBox-show');
    });
    $('.goBack').on(FP.clickEvent,function(e){
        $('.secStep').addClass('hide');
        $('.firstStep').removeClass('hide');
    });

    $("#submit").on(FP.clickEvent,function(){
        if(isloading || $(this).hasClass('disabled')){
            return;
        }
        var data={checkcode:$("#hiddenInput").val()};
        if(data.checkcode==""){
            FP.showError("请填写短信验证码!");
            return;
        }
        isloading=true;
        $("#submit").html("正在绑定...");
        FP.ajaxPost(
            '/my/bindmobilephone',
            data,
            function(v){
                if(v.f){
                    setTimeout(function(){isloading=false;},300);
                    $('.secStep').addClass('hide');
                    $('.firstStep').addClass('hide');
                    $('.lastStep').removeClass('hide');
                    $('#hasBindPhonetel').html($("#uclogin_mobile").val())
                }else{
                    $("#submit").html("确认绑定");
                    isloading=false;
                    FP.showError(v.data);
                }
            },
            function(){
                isloading=false;
                $("#submit").html("确认绑定");
                FP.showError("请重试");
            }
        );
    });

    //发送验证码
    function sendCode(){    
       if(isloading || wait > 0){
            return;
        }
        isloading=true;
        FP.ajaxPost(
            '/my/sendcheckcode',
            {mobile:$("#uclogin_mobile").val()},
            function(v){
                isloading=false;
                if(v.f){
                    $('#hiddenInput').blur();
                    $('#hiddenInput').val('');
                    $('.inputBox .number').text('').removeClass('current');
                    $('.secStep').removeClass('hide');
                    $('.firstStep').addClass('hide');
                    wait = 60;
                    flushTimer();
                }else{
                    FP.showError(v.data);
                    //$('#hiddenInput').focus();
                }
            },
            function(){
                isloading=false;
                FP.showError("请重试");
            }
        );
    }

    //发送语音验证码
    function sendVoiceCode(){    
       if(isloading || wait > 0){
            return;
        }
        isloading=true;
        FP.ajaxPost(
            '/my/sendspeechcode',
            {mobile:$("#uclogin_mobile").val()},
            function(v){
                isloading=false;
                if(v.f){
                    $('#hiddenInput').blur();
                    $('#hiddenInput').val('');
                    $('.inputBox .number').text('').removeClass('current');
                    $('.secStep').removeClass('hide');
                    $('.firstStep').addClass('hide');
                    wait = 60;
                    flushTimer();
                }else{
                    FP.showError(v.data);
                    //$('#hiddenInput').focus();
                }
            },
            function(){
                isloading=false;
                FP.showError("请重试");
            }
        );
    }

    function flushTimer(){
        if(wait<=0){
            $('#SendCode').text("下一步");
            $("#sendAgin").html("重新发送短信验证码");
            $("#sendAgin").prop("disabled",false);
            $("#SendCode").prop("disabled",false);
            $("#chooseVoice").prop("disabled",false);  
            $("#chooseVoice").removeClass('grey');                  
        }else{
            wait--;  
            $('#SendCode').text(wait+"秒");
            $("#sendAgin").text(wait+"秒");
            $('#SendCode').prop('disabled',true);
            $("#sendAgin").prop("disabled",true);
            $("#chooseVoice").prop("disabled",true); 
            $("#chooseVoice").addClass('grey');                  
            timer = setTimeout(function(){flushTimer()},1000);
        }
    }
})
