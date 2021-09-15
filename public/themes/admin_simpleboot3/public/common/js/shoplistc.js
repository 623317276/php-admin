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

$('#one1').on('click',function(e) {
		$("#ids ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_1").show().siblings(".part").hide();
		

});	

$('#one2').on('click',function(e) {
        $("#pageTab").empty();
		$("#ids ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_2").show().siblings(".part").hide();
        // gb.weituo_mairu_list(5,2);

});	


$('#one3').on('click',function(e) {
		$("#ids ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_3").show().siblings(".part").hide();

});	

$('#one4').on('click',function(e) {
		$("#ids ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_4").show().siblings(".part").hide();

});	


$('#one7').on('click',function(e) {
		$("#ids ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_7").show().siblings(".part").hide();

});	


$('#one8').on('click',function(e) {
		$("#ids ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_8").show().siblings(".part").hide();
        
});	

$('#one9').on('click',function(e) {
       $("#ids ul li").removeClass("hover");
		$(this).addClass("hover");
		$("#con_one_9").show().siblings(".part").hide();
        // gb.weituo_chong_list(1,100);
});	


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


 /**充值获取列表 */
gb.weituo_chong_list = function (start,end) {
    this.ajax({
        url: "/Manage_Api/chongzhi",
		cache: false,
        data: {
        	start:start,
        	end:end,
			uid:gid
        },
        success: function (e) {
            console.log(e, e.code, e.data);
            if (e.code == "success") {
				layer.closeAll("loading");
                for(var i = 0; i < e.data["list"].length; i++){
                    var divItem = ''
                        +'<tr>'
                            +'<td>'+e.data["list"][i]["amount"]+'</td>'
                            +'<td>'+e.data["list"][i]["amount"]+'</td>'
                            +'<td>'+e.data["list"][i]["amount"]+'</td>'
                            +'<td>'+e.data["list"][i]["amount"]+'</td>'
                            +'<td>55</td>'
                            +'<td>55</td>'
                            +'<td>55</td>'
						+'</tr>';
        			$("#pageTab7").append(divItem);
                }
				
            } else {
				layer.closeAll("loading");
				var divItem = "<li id='none'><p class='tc f26 g9'>暂无充值信息</p></li>";
				$("#pageTab").append(divItem);
            }
        },
        error: function () {
            fun(-500, null);
        }
    });
}

});