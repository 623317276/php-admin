$(document).ready(function(){
	var gb = {};
gb.IsPC = function() {
  var userAgentInfo = navigator.userAgent;
  var Agents = ["Android", "iPhone",
        "SymbianOS", "Windows Phone",
        "iPad", "iPod"];
  var flag = true;
  for (var v = 0; v < Agents.length; v++) {
    if (userAgentInfo.indexOf(Agents[v]) > 0) {
      flag = false;
      break;
    }
  }
  return flag;
}


function GetRequest() {   
   var url = location.search; //获取url中"?"符后的字串   
   var theRequest = new Object();   
   if (url.indexOf("?") != -1) {   
      var str = url.substr(1);   
      strs = str.split("&");   
      for(var i = 0; i < strs.length; i ++) {   
         theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);   
      }   
   }   
   return theRequest;   
} 

var Request = new Object();
Request = GetRequest();
var gid;
gid = Request['key'];
gb.isLogin = false;

$('#one1').on('click',function(e) {
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_1").show().siblings(".part").hide();
		

});	

$('#one2').on('click',function(e) {
        $("#pageTsab").empty();
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_2").show().siblings(".part").hide();
        gb.weituos_get_list();
		

});	


$('#one3').on('click',function(e) {
	    $("#maitab").empty();
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_3").show().siblings(".part").hide();
		gb.weituo_maichu_list();

});	

$('#one4').on('click',function(e) {
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_4").show().siblings(".part").hide();

});	


$('#one5').on('click',function(e) {
	    $("#tijintab").empty();
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_5").show().siblings(".part").hide();
		gb.weituo_tijin_list();

});	


$('#one6').on('click',function(e) {
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_6").show().siblings(".part").hide();
        
});	

$('#one7').on('click',function(e) {
        $("#pageTab7").empty();
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_7").show().siblings(".part").hide();
        gb.weituo_chong_list();
});	


$('#one8').on('click',function(e) {
	    $("#tixianjl").empty();
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_8").show().siblings(".part").hide();
		gb.weituo_tixianjldss_list();
});	


$('#one9').on('click',function(e) {
		$("nav ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_9").show().siblings(".part").hide();
    gb.weituo_youhui_list();
});	

// $('#xg').on('click',function(e) {
// 		$("nav ul li").removeClass("hover");
// 		$(this).addClass("hover");
// 		$("#con_one_xg").show().siblings(".part").hide();
//         $("#xg").hide();
// });
// $('#qx').click(function(){
//     $("#con_one_xg").hide();
//     $("#con_one_1").show();
//     $("#xg").show();
    
// })	
// $('#fh').click(function(){
//     $("#con_one_xg").hide();
//     $("#con_one_1").show();
//     $("#xg").show();
    
// })  

        /**获取列表 */
gb.weituo_mairu_list = function (start,end) {
    this.ajax({
        url: "/Manage_Api/record",
		cache: false,
        data: {
        	start:start,
        	end:end
        },
        success: function (e) {
            console.log(e, e.status_code, e.data);
            if (e.status_code == "success") {
				layer.closeAll("loading");
                for(var i = 0; i < e.data["list"].length; i++){
					
					// var zt = "";
					// var ti = "";
     //                if (e.data["list"][i]["payid"] == "1"){
     //                    zt = '<img src="skin/style/images/zhi.jpg" width="80px"/>';
     //                }else {
     //                    zt = '<img src="skin/style/images/wzhi.jpg" width="80px"/>';
     //                }
						
                    var divItem = ''
      //               	+'<a href="modules.php?app=jiaoyixq&id='+e.data["list"][i]["id"]+'" class="item" data-v-ecaca2b0="">'
      //                       +'<div class="deal-info">'
						// 	+'<div class="deal-info-top">'
						// 	+'<div class="nickname">misisss</div> '
						// 	+'<div class="deal-price"><span>1.00</span>&nbsp;CNY</div></div>'
						// 	+'<div class="deal-info-bottom"><div class="deal-text">'
						// 	+'<div class="deal-info-text">交易次数： 10 次 |  &nbsp;'+zt+'</div> '
						// 	+'<div class="deal-info-text">限额 '+e.data["list"][i]["minnum"]+'-'+e.data["list"][i]["maxnum"]+'&nbsp;CNY</div>'
						// 	+'</div>'
						// 	+'<div class="deal-btn">'
						// 	+'<div class="btncc">购买</div>'
						// 	+'</div></div></div>'
						// +'</a>';
                        +'<tr>'
                            +'<td></td>'
                            +'<td></td>'
                            +'<td></td>'
                            +'<td></td>'
                            +'<td></td>'
                            +'<td></td>'
                            +'<td></td>'
                            +'<td></td>'
						+'</tr>';
        			$("#pageTab").append(divItem);
                }
				
            } else {
				layer.closeAll("loading");
				var divItem = "<li id='none'><p class='tc f26 g9'>暂无交易</p></li>";
				$("#pageTab").append(divItem);
            }
        },
        error: function () {
            fun(-500, null);
        }
    });
}




//买金记录分页
gb.weituos_get_list = function(){
   DMGold.ajax({"formId":"formids","serialize":true,"url":"/Manage_Api/maijin","success":backFunmais});
}
function backFunmais(data){
$("#pageTsab").empty();
$('#almmjin').tmpl(data.pageResult).appendTo("#pageTsab");
DMGold.PageTag.init({"divId":"pagsseId","formId":"formids","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
"pageCount":data.pageResult.pageTotal,"url":"/Manage_Api/maijin","toPageCallBack":backFunmais});
}


//卖金记录分页
gb.weituo_maichu_list = function(){
   DMGold.ajax({"formId":"formids","serialize":true,"url":"/Manage_Yingxiao/maichujin","success":backFunmai});
}
function backFunmai(data){
$("#maitab").empty();
$('#allmaijin').tmpl(data.pageResult).appendTo("#maitab");
DMGold.PageTag.init({"divId":"pagemaiId","formId":"formids","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
"pageCount":data.pageResult.pageTotal,"url":"/Manage_Yingxiao/maichujin","toPageCallBack":backFunmai});
}


//提金记录分页
gb.weituo_tijin_list = function(){
   DMGold.ajax({"formId":"formids","serialize":true,"url":"/Manage_Yingxiao/tijin","success":backFuna});
}
function backFuna(data){
$("#tijintab").empty();
$('#todayData').tmpl(data.pageResult).appendTo("#tijintab");
DMGold.PageTag.init({"divId":"pageti","formId":"formids","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
"pageCount":data.pageResult.pageTotal,"url":"/Manage_Yingxiao/tijin","toPageCallBack":backFuna});
}


//充值记录分页
gb.weituo_chong_list = function(){
   DMGold.ajax({"formId":"formids","serialize":true,"url":"/Manage_Yingxiao/chongzhijilu","success":backFuncss});
}
function backFuncss(data){
$("#pageTab7").empty();
$('#allchongzhi').tmpl(data.pageResult).appendTo("#pageTab7");
DMGold.PageTag.init({"divId":"pageIs","formId":"formids","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
"pageCount":data.pageResult.pageTotal,"url":"/Manage_Yingxiao/chongzhijilu","toPageCallBack":backFuncss});
}


//提现记录分页
gb.weituo_tixianjldss_list = function(){
   DMGold.ajax({"formId":"formids","serialize":true,"url":"/Manage_Yingxiao/tixianjl","success":backFunct});
}
function backFunct(data){ 
$("#tixianjl").empty();
$('#alltixian').tmpl(data.pageResult).appendTo("#tixianjl");
DMGold.PageTag.init({"divId":"pageIds","formId":"formids","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
"pageCount":data.pageResult.pageTotal,"url":"/Manage_Yingxiao/tixianjl","toPageCallBack":backFunce});
}

//优惠券记录分页
gb.weituo_youhui_list = function(){
   DMGold.ajax({"formId":"formids","serialize":true,"url":"/Manage_Yingxiao/youhui","success":backFunce});
}
function backFunce(data){ 
$("#youhui").empty();
$('#youhuilist').tmpl({msg:data.pageResult.list}).appendTo("#youhui");
DMGold.PageTag.init({"divId":"pageIdss","formId":"formids","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
"pageCount":data.pageResult.pageTotal,"url":"/Manage_Yingxiao/youhui","toPageCallBack":backFunce}); 
}



gb.zhuceMccxDianji = function(){
	$('.mccx').on('touchend',function(e) {
		var idTxt = $(this).parent().children(":first").children(":first").html();
     	gb.weituo_maichu_del(idTxt);
	});
	
	$('.mccx').on('click',function(e) {
		var idTxt = $(this).parent().children(":first").children(":first").html();
     	gb.weituo_maichu_del(idTxt);
	});
	
}

 gb.ajax = function (obj) {
    var defaultObj = {
        url: "",
        type: "POST",
        // async:true,
        contentType: "application/x-www-form-urlencoded",
        data: {},
        dataType: "JSON",
        // success:null,
        // error:null,
    };
    // main = this;

    obj = obj || {};
    obj.url = obj.url || defaultObj.url;
    obj.type = obj.type || defaultObj.type;
    obj.async = obj.async || defaultObj.async;
    obj.contentType = obj.contentType || defaultObj.contentType;
    obj.data = obj.data || defaultObj.data;
    obj.dataType = obj.dataType || defaultObj.dataType;
    // obj.success = obj.success || defaultObj.success;
    // obj.error = obj.error || defaultObj.error;
    // obj.async = obj.async?true:false;
    obj.type = obj.type.toUpperCase();
    obj.dataType = obj.dataType.toUpperCase();
    var XHR = new XMLHttpRequest();
    XHR.onreadystatechange = function () {
        if (XHR.readyState == 4) {
            if (XHR.status == 200) {
                if (obj.success) {
                    if (obj.dataType == "JSON") {
                        obj.success(JSON.parse(XHR.responseText));
                    } else {
                        obj.success(XHR.responseText);
                    }
                }
            } else {
                if (obj.error) {
                    obj.error();
                }
            }
        }
    };
    XHR.open(obj.type, obj.url, true);
    XHR.setRequestHeader("Content-type", obj.contentType);
    if (obj.type == "POST") {
        if (typeof obj.data === "object") {
            XHR.send(this.urlEncode(obj.data));

        } else {
            XHR.send(obj.data);
        }
    } else {
        XHR.send();
    }
};
gb.urlEncode = function (param, key, encode) {
    if (param == null) return "";
    var paramStr = "";
    var t = typeof (param);
    if (t == "string" || t == "number" || t == "boolean") {
        paramStr += "&" + key + "=" + ((encode == null || encode) ? encodeURIComponent(param) : param);
    } else {
        for (var i in param) {
            var k = key == null ? i : key + (param instanceof Array ? "[" + i + "]" : "." + i);
            paramStr += this.urlEncode(param[i], k, encode)
        }
    }
    return paramStr;
}

});