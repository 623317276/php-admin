

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
                path:'../lang/',   //注意这里路径是你属性文件的所在文件夹
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
        $("#toggleLang").on('click',function(){
            if(lang == null){
                //null 已经是中文了
                lang = 'us';
            }else{
                lang == 'zh' ? lang ='us' : lang = 'zh';
            }
            sessionStorage.setItem("lang",lang);
            loadProperties(lang);
        });
    
})        
