if(typeof FP == 'undefined'){
  FP = {}
}
;(function(){
    function obj(){
        var 
        _hideAllNonBaseMenuItem=false,
        _hideTimeline=false,
        wx_ready=false,
        wx_share={
            title:"Flowerplus花加社区",
            subtitle:"用鲜花点亮美好生活，一起记录那些美好时光",
            image:"http://static2.flowerplus.cn/Static/sns_wx/images/logo.png",
            link:"http://t.flowerplus.cn/sns_wx/",
            whenMenuShareTimeline:null,
            whenMenuShareAppMessage:null
        };

        this.IS_WEIXIN=false;
        this.ajax_timeout = 0;

        this.setAjaxTimeout = function(timeout){
          this.ajax_timeout = timeout;
        };
        //设置微信分享的标题
        this.setWeixinShareTitle = function(v){
          $.extend(wx_share,v);
        };

        // 隐藏微信右上角菜单
        this.hideAllNonBaseMenuItem = function(){
          _hideAllNonBaseMenuItem = true;
          if(wx_ready){
            wx.hideAllNonBaseMenuItem();
          }
        };

        this.hideTimeline = function(){
          _hideTimeline = true;
          if(wx_ready){
            wx.hideMenuItems({
                menuList: ["menuItem:share:timeline"] 
            });
          }
        };

        this.openWxAddress=function(onSuccess,onCancel){
            console.log('openWxAddress');
            if(!wx_ready){
                return;
            }
            console.log('openWxAddress 启动');
            wx.openAddress({
                 success: function (data) { 
                      if(typeof onSuccess == 'function'){
                        onSuccess(data);
                      }
                  },
                 cancel: function (data) { 
                    if(typeof onCancel == 'function'){
                        onCancel(data);
                      }
                }
            });
        };
        this.ajaxPost=function(url,data,success_fun,error_fun){
          $.ajax({
            type : "POST",
            url  : url,
            data : data,
            dataType : "json",
            timeout  : this.ajax_timeout,
            success  : success_fun,
            error    : error_fun 
          });
        };

        this.ajaxGet=function(url,data,success_fun,error_fun){
          $.ajax({
            type : "GET",
            url  : url,
            data : data,
            dataType : "json",
            timeout  : this.ajax_timeout,
            success  : success_fun,
            error    : error_fun 
          });
        };


        var onWeiXinLoaded=function(v){
          wx.config({
              debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
              appId: v.appId, // 必填，公众号的唯一标识
              timestamp: v.timestamp, // 必填，生成签名的时间戳
              nonceStr: v.nonceStr, // 必填，生成签名的随机串
              signature: v.signature,// 必填，签名，见附录1
              jsApiList: ['checkJsApi','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','hideMenuItems','showMenuItems','hideAllNonBaseMenuItem','showAllNonBaseMenuItem','translateVoice','startRecord','stopRecord','onRecordEnd','playVoice','pauseVoice','stopVoice','uploadVoice','downloadVoice','chooseImage','previewImage','uploadImage','downloadImage','getNetworkType','openLocation','getLocation','hideOptionMenu','showOptionMenu','closeWindow','scanQRCode','chooseWXPay','openProductSpecificView','addCard','chooseCard','openCard,openAddress']
          });

          wx.ready(function(){
              wx_ready=true;
              wx.onMenuShareTimeline({
                  title: wx_share.title, // 分享标题
                  link: wx_share.link, // 分享链接
                  imgUrl: wx_share.image, // 分享图标
                  success: function () {
                    if(typeof wx_share.whenMenuShareTimeline == "function"){
                        (wx_share.whenMenuShareTimeline)();
                    }
                  },
                  cancel: function () { 
                  }
              });

              wx.onMenuShareAppMessage({
                  title: wx_share.title, // 分享标题
                  desc: wx_share.subtitle, // 分享描述
                  link: wx_share.link, // 分享链接
                  imgUrl: wx_share.image, // 分享图标
                  type: '', // 分享类型,music、video或link，不填默认为link
                  dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                  success: function () {
                    if(typeof wx_share.whenMenuShareAppMessage == "function"){
                        (wx_share.whenMenuShareAppMessage)();
                    }
                  },
                  cancel: function () { 
                  }
              });

              if(_hideAllNonBaseMenuItem){
                wx.hideAllNonBaseMenuItem();
              }
              if(_hideTimeline){
                 wx.hideMenuItems({
                    menuList: ["menuItem:share:timeline"] 
                });
              }
          });
        };
      this.init=function(_web_base){
        var u=window.navigator.userAgent;
        if(u.indexOf('Android') > -1 || u.indexOf('Adr') > -1){
          this.OS="Android";
        }else if(!!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)){
          this.OS="iOS";
        }

        if(u.toLowerCase().match(/MicroMessenger/i)=="micromessenger"){
          this.IS_WEIXIN=true;
          this.ajaxGet(_web_base+"weixin_js.html",{url:window.location.href},onWeiXinLoaded);
        }
      }
    }
    FP = new obj();
    FP.init(_web_base);
    
    /**
    * ajax请求promise版本
    *
    * @url      请求地址
    * @dataType 查询参数或者post数据 默认值:null
    * @method   请求方法 默认值:GET
    * @method   要求返回的数据类型 默认值:JSON
    * @timeout  超时时间 默认120秒
    *
    *
    * done(func).fail(func).always(func)
    */
    FP.request=function(url, data, method, dataType, timeout){
      var def = $.Deferred();
      $.ajax({
          type : typeof method == 'string' ? method : 'GET',
          url  : url,
          data : typeof data != 'undefined' ? data : null,
          dataType : typeof dataType == 'string' ? dataType : 'JSON',
          timeout  : typeof method == 'number' ? number : 120,
          success : function(data){
                    def.resolve(data);
                },
          error   : function(error){
                    if(error.status == 403){
                        def.reject({ code : 1, error: '需要登录'});
                    }else{
                        def.reject({ code : 2, error: '请求错误'});
                    }
                }
      });
      return def.promise();
    };
    FP.showError=function(errmsg){
      var tips=$(".tips");
      if(tips.length==0){
        tips=$("<div class=\"tips\"></div>").appendTo("body");
      }
      tips.html("<div>"+errmsg+"</div>");
      tips.fadeIn();
      if(typeof window.__ERROR_TIPS_TIMER !="undefined"){
        window.clearTimeout(window.__ERROR_TIPS_TIMER);
      }
      window.__ERROR_TIPS_TIMER=window.setTimeout(function(){
          $(".tips").fadeOut('fast');
        },3000);
      window.setTimeout(function(){
          $(document).one("touchstart",function(e){
          if(e.target.tagName=="BUTTON"){
            return;
          }
          $(".tips").fadeOut("fast");
        });
        },300);
    };
    FP.removeError = function(){
      $(".tips").length > 0 && $(".tips").hide();
    }
    FP.deviceRatio = window.devicePixelRatio; //设备视网膜比例
    //flag为true，则代表开启兼容，如果不支持localStorage，
    //就存进Cookie，由于Cookie限额比较小，谨慎使用
    //如果开启兼容，flag可设为Cookie日期
    FP.setLocalStorage = function(name,value,flag){ 
        if(!localStorage){                              
            if(flag){                                   
                FP.setCookie(name,value,flag);
            }
            FP.showError('不支持localStorage');
            return;
        }
        localStorage.setItem(name,value);
    }
    FP.getLocalStorage = function(name,flag){
        if(!localStorage){
            if(flag){
                return FP.getCookie(name);
            }
            FP.showError('不支持localStorage');
            return "";
        }
        return localStorage.getItem(name);
    }
    FP.removeLocalStorage = function(name,flag){
        if(!localStorage){
            if(flag){
                FP.setCookie(name,'',-1);
            }
            FP.showError('不支持localStorage');
            return;
        }
        localStorage.removeItem(name);
    }
    FP.loading = function(){
      var tips = $(".loading");
      if(tips.length == 0){
        tips = $("<div class=\"loading\"></div>").appendTo("body");
      }
      return {
        show : function (){
            tips.show()
        },
        hide : function(){
           tips.remove()
        }
      }
    }
    FP.getQueryString = function(name){
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }
    // 进行url编码
    FP.encodeUrl = function (url){
        return typeof url === "undefined" ? "" : encodeURIComponent(url);
    };
    // 进行url解码
    FP.decodeUrl = function (url){
        return typeof url === "undefined" ? "" : decodeURIComponent(url);
    };
})();