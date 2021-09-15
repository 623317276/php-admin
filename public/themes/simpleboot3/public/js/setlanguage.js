

 $(function(){
    var lang=sessionStorage.getItem("lang");
        if(lang===null){
            lang="zh";
        }
        loadProperties(lang);
        // $("#lang").val(lang);
        function loadProperties(types) {
            $.i18n.properties({
                name:'language',    //属性文件名     命名格式： 文件名_国家代号.properties
                path:'/themes/simpleboot3/public/assets/lang/',   //注意这里路径是你属性文件的所在文件夹
                mode:'map',
                language: types,     //这就是国家代号 name+language刚好组成属性文件名：strings+zh -> strings_zh.properties
                callback:function(){
                    $("[data-locale]").each(function(){
                        $(this).html($.i18n.prop($(this).data("locale")));
                    });
                    $("[data-placeholder]").each(function(){
                        $(this).attr('placeholder',$.i18n.prop($(this).data("placeholder")));
                    });
                }
            });
        }
        //切换语言
        $('.zh').on('click',function(){
        	lang='zh';
        	$('#xiala').css('display','none');
        	sessionStorage.setItem("lang",lang);
            loadProperties(lang);
            $('#dian').html('中文');
            $('.market p').css('letter-spacing','0.2em');
            $("#wallet",top.document).text('钱包');
             $("#trading",top.document).text('交易');
            $("#condition",top.document).text('行情');
            $("#ucenter",top.document).text('个人中心');
            var iframe=document.getElementById('MainViwe').contentWindow;
            iframe.document.getElementById('transfer1').innerHTML='转账';
            iframe.document.getElementById('tradding1').innerHTML='交易';
            iframe.document.getElementById('entertain1').innerHTML='娱乐';
            iframe.document.getElementById('borrow1').innerHTML='借贷';
            iframe.document.getElementById('life1').innerHTML='生活';
            iframe.document.getElementById('live1').innerHTML='直播';
            iframe.document.getElementById('pay1').innerHTML='支付';
            iframe.document.getElementById('shopping1').innerHTML='购物';
            iframe.document.getElementById('moreapply').innerHTML='更多应用';
            iframe.document.getElementById('social1').innerHTML='社交';
            iframe.document.getElementById('game1').innerHTML='游戏';
            iframe.document.getElementById('educate1').innerHTML='教育';
  
        })
        $('.us').on('click',function(){
        	lang='us';
        	$('#xiala').css('display','none');
        	sessionStorage.setItem("lang",lang);
            loadProperties(lang);
            $('#dian').html('English');
            $('.market p').css('letter-spacing','0px');
           $("#wallet",top.document).text('Wallet');  
            $("#trading",top.document).text('Trading');
            $("#condition",top.document).text('Condition');
            $("#ucenter",top.document).text('Ucenter');
             var iframe=document.getElementById('MainViwe').contentWindow;
            iframe.document.getElementById('transfer1').innerHTML='Transfer';
            iframe.document.getElementById('tradding1').innerHTML='Tradding';
            iframe.document.getElementById('entertain1').innerHTML='Entertain';
            iframe.document.getElementById('borrow1').innerHTML='Borrow';
            iframe.document.getElementById('life1').innerHTML='Life';
            iframe.document.getElementById('live1').innerHTML='Live';
            iframe.document.getElementById('pay1').innerHTML='Pay';
            iframe.document.getElementById('shopping1').innerHTML='Shopping';
            iframe.document.getElementById('moreapply').innerHTML='More appplication';
            iframe.document.getElementById('social1').innerHTML='Social';
            iframe.document.getElementById('game1').innerHTML='Game';
            iframe.document.getElementById('educate1').innerHTML='Educate';
        })
    
})        
