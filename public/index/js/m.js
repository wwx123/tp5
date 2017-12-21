;(function(){
    function obj(){
        var 
        _hideAllNonBaseMenuItem=false,
        _hideTimeline=false,
        _hideOptionMenu=false,
        wx_ready=false,
        wx_share={
            title:"FLŌWERPLUS（花+）",
            subtitle:"用鲜花点亮生活，鲜花订阅，每周一次，新客户首单送花瓶哦。",
            image:"http://static2.flowerplus.cn/Static/img/flowerpluslogo2.jpg",
            link:"http://t.flowerplus.cn",
            whenMenuShareTimeline:null,
            whenMenuShareAppMessage:null
        };

        this.IS_WEIXIN=false;
        this.OS="PC";
        this.ajax_timeout = 0;
        this.EVENT_CLICK = typeof document.documentElement.ontouchstart!="undefined"?"touchstart":"click";

        this.setAjaxTimeout = function(timeout){
          this.ajax_timeout = timeout;
          console.log('set ajaxtimeout ' + timeout);
        };
        //设置微信分享的标题
        this.setWeixinShareTitle=function(v){
          $.extend(wx_share,v);
        };

        // 隐藏微信右上角菜单
        this.hideAllNonBaseMenuItem=function(){
          _hideAllNonBaseMenuItem=true;
          if(wx_ready){
            wx.hideAllNonBaseMenuItem();
          }
        };

        this.hideTimeline=function(){
          _hideTimeline=true;
          if(wx_ready){
            wx.hideMenuItems({
                menuList: ["menuItem:share:timeline"] 
            });
          }
        };

        this.hideOptionMenu =function(){
          _hideOptionMenu=true;
          if(wx_ready){
            wx.hideOptionMenu();
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
              if(_hideOptionMenu){
                wx.hideOptionMenu();
              }
          });
        };


        this.toMoney=function(v){
          return Math.round(v*100)/100;
        };
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
        this.request=function(url, data, method, dataType, timeout){
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

        this.showError=function(errmsg){
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

        this.removeError = function(){
            $(".tips").length > 0 && $(".tips").hide();
        }

        this.getSkuValue = function(sku_properties_name,sname){
            var sku=sku_properties_name.split(';'), svalue='';

            for (var i=sku.length-1; i>=0; i--) {
                var sku_item=sku[i].split(':');
                if(sku_item.length!=2){
                    continue;
                }
                if(sku_item[0]==sname){
                    svalue=sku_item[1];
                    break;
                }
            }
            return svalue;
        };

        var supportTouch = function(){
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        }();
        var _that = this;
        (function(){  /*以后点击事件都用 $(element).on(FP.clickEvent,callBack)*/
            _that.clickEvent = supportTouch ? "touchend" :"click";
            var _jqOnEvent = $.fn.on;
            $.fn.on = function(){
              var touchStartY;
              var callback = typeof arguments[1] == 'function' ? arguments[1] : typeof arguments[2] == 'function' ? arguments[2] : false;
              var _this = $(this);
              if(/touchend/.test(arguments[0]) && callback){
                  _this = typeof arguments[1] == 'string' ? _this.find(arguments[1]) : _this;
                  _jqOnEvent.apply(_this, ['touchstart', function(e){
                      touchStartY = e.originalEvent && e.originalEvent.changedTouches[0].clientY;
                  }]);
                  _jqOnEvent.apply(_this, ['touchend', function(e){
                      if (Math.abs(e.originalEvent && (e.originalEvent.changedTouches[0].clientY - touchStartY)) > 10) return;
                      
                      if(e.isTrigger){
                        var callBackEvent = _this.data('callBackEvent');
                        if(callBackEvent) return callBackEvent();
                      }
                      e.preventDefault();
                      callback.apply(e.target, [e]);
                  }]);
                  _this.data('callBackEvent',callback);
              }else{
                  _jqOnEvent.apply(_this, arguments);
              }
              return _this;
            }
        })();
        

        this.fastClick=function(){
            var _old$On = $.fn.on;
            $.fn.on = function(){
                var callBackEvent = this.data('callBackEvent');
                if(/click/.test(arguments[0]) && typeof arguments[1] == 'function' && supportTouch){ // 只扩展支持touch的当前元素的click事件
                    var touchStartY, callback = arguments[1];
                    _old$On.apply(this, ['touchstart', function(e){
                        touchStartY = e.originalEvent.changedTouches[0].clientY;
                    }]);
                    _old$On.apply(this, ['touchend', function(e){
                        if (Math.abs(e.originalEvent.changedTouches[0].clientY - touchStartY) > 10) return;
                        e.preventDefault();
                        callback.apply(this, [e]);
                    }]);
                    this.data('callBackEvent',arguments[1]);
                }else if( callBackEvent && typeof callBackEvent === 'function' ){
                    callBackEvent.apply(this);
                }else{
                    _old$On.apply(this, arguments);
                }
                return this;
            };
            $.fn.click=function(fun){
                $.fn.on.call(this,"click",fun);
            }
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

    var _FP = new obj();
    _FP.init(_web_base);
    window.FP = _FP;
})();

FP.deviceRatio = window.devicePixelRatio; //设备视网膜比例


/*
arg1 : area-城市数据。 （必传参数）
arg2 ：cityObjDef-默认选择的省市区,格式：（可选）
cityObjDef{
  'p' : '省份',
  'c' : '城市',
  'a' ： '区域'
}
arg3 : nodeName-生成弹窗的唯一ID名 （可选）
arg4 : obj-选择地区后显示结果的节点 （可选）
arg5 : wrapBox-弹窗插入的节点 （可选）
 */
FP.createCityChoosePlugin = function(area,cityObjDef,nodeName,obj,wrapBox){
    var bgMask = $(".flick-menu-mask"),poupAreaBox = top.$('#poupBox_'+nodeName);
    wrapBox = wrapBox || $('.page');
    obj = obj || $('.page');
    if( bgMask.length == 0 ){
        wrapBox.append('<div class="flick-menu-mask"></div>');
    }

    if(!poupAreaBox || poupAreaBox.length == 0){
        poupAreaBox = $('<div class="alert-out place_alert" style="display: block;">'+
                    '<div class="alert spec-menu-show" style="display:block">'+
                        '<div class="alert_top borderBottom">'+
                          '<div class="tc">收货地区</div>'+
                          '<ul class="flex chooseadress"><li class="on">请选择</li></ul>'+
                        '<div class="alert_close"><span></span></div>'+
                    '</div>'+
                    '<div class="place_change_list"><ul></ul></div>'+
                  '</div>'+
                '</div>');
        wrapBox.append(poupAreaBox); 
    }
    bgMask.add(poupAreaBox).show(); 
    poupAreaBox.on(FP.clickEvent,'.alert_close',function(){
        $('.flick-menu-mask').hide();
        $('.place_alert').hide();
    })

    function choosep(a,num){
      if(typeof num == "undefined"){
        num = 1;
      }
      if(num==1){
        var name = $(a).html();
        var html = '';
        $.each(area,function(i,v){
          if(v.n==name){
            html+='<li class="on" typep="'+i+'" data-click="changec(this)">'+v.n+'<span class="check-pic"></span></li>';
          }else if(v.n!==''){
            html+='<li typep="'+i+'" data-click="changec(this)">'+v.n+'</li>';
          } 
        });
      }else if(num==2){
        var name = $(a).parent().find('li').eq(0).html();
        var p = $(a).parent().find('li').eq(0).attr('typep');
        var html_one = '<li data-click="choosep(this)">'+name+'</li><li class="on">选择城市</li>';
        $('.chooseadress').html(html_one);
        var html='';
        $.each(area[p].c,function(i,v){
          if(v.n==name){
            html+='<li class="on" typep="'+p+'" typec='+i+' data-click="changea(this)">'+v.n+'<span class="check-pic"></span></li>';
          }else if(v.n!=='选择城市'){
            html+='<li typep="'+p+'" typec='+i+' data-click="changea(this)">'+v.n+'</li>';
          } 
        });
      }
      $('.place_change_list ul').html(html);
      bindSuperLiClick();
      bindSubLiClick();
    }

    function changec(a){
      var name = $(a).text();
      var html_one = '<li data-click="choosep(this)">'+name+'</li><li class="on">选择城市</li>';
      $('.chooseadress').html(html_one);
      var i = $(a).attr('typep');
      var html_two ='';
      $.each(area[i].c,function(j,v){
        if(v.n==name){
          html_two+='<li class="on" typep="'+i+'" typec="'+j+'" data-click="changea(this)">'+v.n+'<span class="check-pic"></span></li>';
        }else if(v.n!=='选择城市'){
          html_two+='<li typep="'+i+'" typec="'+j+'" data-click="changea(this)">'+v.n+'</li>';
        } 
      });
      $('.place_change_list ul').html(html_two);
      bindSuperLiClick();
      bindSubLiClick();
    }

    function changea(a){
      var name = $(a).text();
      var p = $(a).attr('typep');

      var pname = area[p].n;
      //第一个li。name
      var html_one = '<li data-click="choosep(this)" typep='+p+'>'+pname+'</li><li data-click="choosep(this,2)">'+name+'</li><li class="on">选择区县</li>';
      $('.chooseadress').html(html_one);
      //第二个
      var i = $(a).attr('typec');
      var html_two ='';
      $.each(area[p].c[i].a,function(x,v){
        if(v==name){
          html_two+='<li class="on" typep="'+p+'" typec="'+i+'" typea='+x+' name='+v+' data-click="change(this)">'+v+'<span class="check-pic"></span></li>';
        }else if(v!=='选择区县'){
          html_two+='<li typep="'+p+'" typec="'+i+'" typea='+x+' name='+v+' data-click="change(this)">'+v+'</li>';
        } 
      });
      $('.place_change_list ul').html(html_two);
      bindSuperLiClick();
      bindSubLiClick();
    }

    function change(a){
      $(a).parent().find('li').removeClass('on');
      $(a).parent().find('li').each(function(){
        $(this).html($(this).attr('name'));
      });
      var html = $(a).html();
      $(a).addClass('on').html(html+'<span class="check-pic"></span>');
      var p = $(a).attr('typep');
      var c = $(a).attr('typec');
      var a = $(a).attr('typea');
      obj.find('.r_state').html(area[p].n);
      obj.find('.r_city').html(area[p].c[c].n);
      obj.find('.r_district').html(area[p].c[c].a[a]);
      $('.flick-menu-mask').hide();
      $('.place_alert').hide();
    }  

    if(cityObjDef && cityObjDef.p){
        var r_state = cityObjDef.p;
        var r_city = cityObjDef.c;
        var r_district = cityObjDef.a;
        var p = '';
        $.each(area,function(i,v){
          if(v.n==r_state){
            p = i;
          }
        });
        var c = '';
        $.each(area[p].c,function(i,v){
          if(v.n==r_city){
            c = i;
          }
        });
        
        if(r_state!==''){
          var html_one ='<li typep='+p+' tlevel="1" onclick="console.log(FP.createCityChoosePlugin.choosep);">'+r_state+'</li><li tlevel="2">'+r_city+'</li><li class="on">选择区县</li>';
          $('.chooseadress').html(html_one);
        }
        var html_two ='';
        area[p].c[c] && $.each(area[p].c[c].a,function(x,v){
            if(v==r_district){
              html_two+='<li class="on" typep="'+p+'" typec="'+c+'" typea='+x+' name='+v+' onclick="change(this)">'+v+'<span class="check-pic"></span></li>';
            }else if(v!=='选择区县'){
              html_two+='<li typep="'+p+'" typec="'+c+'" typea='+x+' name='+v+' onclick="change(this)">'+v+'</li>';
            } 
        });
        $('.place_change_list ul').html(html_two);
    }else{
        var html = '';
        for (var i  in area) {
            if (area[i].n !== '') {
                html += '<li typep="' + i + '" data-click="changec(this)">' + area[i].n + '</li>';
            }
        }
        $('.place_change_list ul').html(html);
    }
    function bindSuperLiClick(){
        $('.chooseadress').on(FP.clickEvent,'li',function(e){
            eval($(this).attr('data-click'));
        })
    }

    function bindSubLiClick(){
        $('.place_change_list').on(FP.clickEvent,'li',function(e){
            eval($(this).attr('data-click'));
        })
    }
    bindSuperLiClick();
    bindSubLiClick();
}
/*
合成二维码图片
imgList为数组,,参数属性对应如下：
[{
   'url' : url,
   'width': 200,
   'height' : 200,
   'left' : 0,
   'top' : 0
}],
cvsSize传对象，包括width,height属性
wrapBox 图片插入位置
 */
FP.createQrImg = function(imgList,cvsSize,wrapBox){
  try{ 
        if(wrapBox.find('.QRImg_exportImg').length > 0) return;
        wrapBox = wrapBox || $('.page');
        wrapBox.append('<canvas class="QRImg_canvas"></canvas><img class="QRImg_exportImg hide">');
        var canvas = wrapBox.find(".QRImg_canvas")[0];
        var ctx = canvas.getContext("2d");
        var loadCount = 0;
        var defCvsSize = {
          'width' : $(window).width() > 640 ? 640 : $(window).width(),
          'height' : 200
        }
        defCvsSize = $.extend(defCvsSize,cvsSize);
        canvas.width = defCvsSize.width;
        canvas.height = defCvsSize.height;
        canvas.style.height = canvas.height + 'px';
        canvas.style.width = canvas.width + 'px';
        canvas.width *= FP.deviceRatio;
        canvas.height *= FP.deviceRatio;
        ctx.beginPath();
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0,0,defCvsSize.width,defCvsSize.height);
        drawQrImg(imgList[loadCount]);

        function drawQrImg(obj){
            var qr_image = new Image();
            qr_image.crossOrigin = 'anonymous';
            qr_image.src = obj.url;
            if(obj.url){
                qr_image.onload = function(){
                    var scaleN = qr_image.width / qr_image.height;
                    ctx.save();
                    ctx.drawImage(qr_image,obj.left,obj.top,obj.width,obj.width / scaleN); 
                    ctx.restore();
                    exportImage();  
                }
            }else{
                exportImage();  
            } 
        }

        function exportImage(){
            loadCount ++;
            var isExportUrl = (loadCount == imgList.length);
            if(isExportUrl){
                var exportImage = canvas.toDataURL("image/jpeg");
                wrapBox.find('.QRImg_canvas').hide();
                wrapBox.find('.QRImg_exportImg').attr("src",exportImage).show();
            }else{
                drawQrImg(imgList[loadCount]);
            }
        }
    }catch(e){
        FP.showError('QRImg_canvas error');
    }
}
