	dmCheck.init("#addForm");
	$(function(){
		//初始化列表
		pageTagCallBack();
		//获取所有省
	
		
	});
   function search(){
   	pageTagCallBack();
   }
   var pageTagCallBack = function(){
	 	DMGold.ajax({"formId":"dataForm","serialize":true,"url":"/Manage_Yingxiao/shopperlistajax","success":backFunc});
    }
	  
    //分页跳转回调
	function backFunc(data){
	 	//清空表格数据 
	 	$("#shopTable").empty();
	 	
	 	//填充数据
		$('#shopListTemplate').tmpl(data.pageResult).appendTo("#shopTable");
	 	
		//初始化分页标签
		DMGold.PageTag.init({"divId":"pageId","formId":"dataForm","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
				          "pageCount":data.pageResult.pageTotal,"url":"/Manage_Yingxiao/shopperlistajax","toPageCallBack":backFunc});
	}
 
 	 //锁定/解锁弹出框
 	function lockinfo(userid,islock,loginname){
 		
 		if(islock=='N'){
 			$("#lockid").text("锁定");
 			$("#lockuser").text("锁定"+loginname);
 			$("#hd_islock").val("Y");
 		}else{
 			$("#lockid").text("解锁");
 			$("#lockuser").text("解锁"+loginname);
 			$("#hd_islock").val("N");
 		}
 		$("#hd_userid").val(userid);
		
 		dmCheck.initForAjax("#lockDialog");
  		$("#lockDialog").show();
 	}	
 	 
  	//解锁/锁定
 	function islock(){
 		var userid=$("#hd_userid").val();
 		var islock=$("#hd_islock").val();
 		$.ajax({ 
            type : "POST", 
            url : basePath+"user/per/userPerShowLock.do", 
            data : {"userId":userid,"lockFlag":islock}, 
            success : function(result) {
            	$('#lockDialog').hide();
            	if(result.statu.code == "000000"){
            		dm.alert("操作成功！",{
            			title:"提示",				//弹窗的标提
            			okName:"确定",			//如同alert确定按钮的value
            			picClass:"d_succeed",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
            			notHaveClose:"xxx",
            			callback:function(){	//按确定后执行的函数
            				search();
            			}
            		});
              	}else{
              		dm.alert("操作失败！");
              	}
            } 
        });
 	}
  	
   //弹出店铺新增框
  	function addPerUser(){
  		dmCheck.initForAjax("#addForm");
  		$("#addDialog").show();
  	}
   
  	//判断密码和确认密码是否相同
  	function checkRepassword(msg, isFocus) {
		var repassword =  $("#addForm input[name='repassword']");	  
		if (msg) {
			tip(repassword, msg);
		} else {
			if (!repassword.val() || repassword.val() != $("#addForm input[name='sendPassword']").val()) {
				tip(repassword, "两次输入的密码不一致！");
			} else {
				return true;
			}
		}
		if (!isFocus)	repassword.focus();
		return false;
  	}
  	
  	//店铺新增弹框提交
  	function addPerUserSubmit(){
  	   //店铺名称是否存在
  	  	if(isExitUserName($("#username").val())){
  	  		return;
  	  	}
  	   
  		if((obj = dmCheck.returnObj("#addForm"))){
  			return obj.focus();
  		}
  		if(!checkRepassword()){	
  			return false;
  		}
  		$("#addForm input[name='password']").val(hex_md5($("#addForm input[name='sendPassword']").val()));
  		$("#addForm").submit();
  	}
 
	//获取所有省
	function getAllProvince(){
		// var pro='${shopreg.provinces}';
		DMGold.ajax({"formId":"dataForm","serialize":true,"url":"/Manage_Api/ajaxgetregion","success":pCallBack});
	}
	function pCallBack(data){
		$("#provinces").empty();
		$("#provinces").append("<option value=''>--请选择--</option>");
		for(var i=0;i<data.length;i++){
						$("#provinces").append("<option value='"+data[i].id+"' >"+data[i].name+"</option>");
				}
	}
		
	
	
	//获取省下市
	function changeProvince(pid){
		
		if(pid =='' || pid == null){
			$("#city").empty();
			$("#city").append("<option value=''>--请选择--</option>");
		}else{
			$.ajax({
				type:"POST",
				url:"/Manage_Api/ajaxgetregion",
				data:{"subRegionId":pid},
				success:function(data){
					$("#city").empty();
					$("#city").append("<option value=''>--请选择--</option>");
					for(var i=0;i<data.length;i++){
							$("#city").append("<option value='"+data[i].id+"'>"+data[i].name+"</option>");
					}
				}
			});
		}
		
	}
	
	//判断用户名是否存在
	function isExitUserName(username){
		var isExist=false;
		if(username){
			username='DP'+username;
			$.ajax({ 
				async:false,
	            type : "POST", 
	            url : basePath+"finance/getUserid.do", 
	            data : {"userName":username,"userType":"SHOP"}, 
	            success : function(result) {
	            	if(result.user.code=='000000'){
	            		$("#username").focus();
	            		dmCheck.tip("#username","店铺用户名已存在");
	            		isExist=true;
	            	}
	            }
			});
		}
		 return isExist;
	}
	
    //删除店铺-弹出框
 	function deleteShopDialog(shopId){
 		$("#hd_shopId").val(shopId);
  		$("#deleteShopDialog").show();
 	}
	
	//删除店铺-Ajax
 	function deleteShopAjax(){
 		var shopId=$("#hd_shopId").val();
 		$.ajax({ 
            type : "POST", 
            url : basePath+"shop/deleteShopAjax.do", 
            data : {"userId":shopId}, 
            success : function(result) {
            	$('#deleteShopDialog').hide();
            	if(result.code == "000000"){
            		dm.alert("操作成功！",{
            			title:"提示",				//弹窗的标提
            			okName:"确定",			//如同alert确定按钮的value
            			picClass:"d_succeed",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
            			notHaveClose:"xxx",
            			callback:function(){	//按确定后执行的函数
            				search();
            			}
            		});
              	}else{
              		dm.alert("操作失败！");
              	}
            } 
        });
 	}
	
 	//重置店铺密码-弹出框
 	function resetPwdShopDialog(shopId,shopName){
 		$("#resetShopId").val(shopId);
 		$("#resetShopNameId").html(shopName);
  		$("#resetPwdShopDialog").show();
 	}
	
	//重置店铺密码-Ajax
 	function resetPwdShopAjax(){
 		var shopId=$("#resetShopId").val();
 		$.ajax({ 
            type : "POST", 
            url : basePath+"shop/updateResetShopPwdAjax.do", 
            data : {"userId":shopId}, 
            success : function(result) {
            	$('#resetPwdShopDialog').hide();
            	if(result.code == "000000"){
            		dm.alert("操作成功！",{
            			title:"提示",				//弹窗的标提
            			okName:"确定",			//如同alert确定按钮的value
            			picClass:"d_succeed",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
            			notHaveClose:"xxx",
            			callback:function(){	//按确定后执行的函数
            				search();
            			}
            		});
              	}else{
              		dm.alert("操作失败！");
              	}
            } 
        });
 	}