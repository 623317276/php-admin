 	 $(function(){
  		 search();//jquery 模板demo
  		
  		 //商品排序查询
		 $("#userSort>td").click(function(){
			userSortQuery($(this));
		 });
     }); 
	 
	 function search(){
	 	DMGold.ajax({"formId":"dataForm","serialize":true,"url":"/Manage_Api/userlist","success":pageTagCallBack});
	 }
	 
	 function searchButton(){
	 	var uname = $('#uname').val();//用户名
	 	var name = $('#name').val();//姓名
	 	var phoneId = $('#phoneId').val();//电话
	 	var tuipeple = $('#tuipeple').val();//推荐人
	 	var fupeple = $('#fupeple').val();//服务人
	 	var zcly = $('#zcly').val();//注册来源
	 	var lockFlag = $('#lockFlag').val();//状态
	 	var userType = $('#userType').val();//类别
	 	var channelSurceId = $('#channelSurceId').val();//渠道来源
	 	var startTime = $('#startTime').val();//注册的开始时间
	 	var endTime = $('#endTime').val();//结束时间
	 	$.ajax({ 
            type : "POST", 
            url : "/Manage_Api/userlist", 
            data : {"uname":uname,"name":name,"phoneId":phoneId,"tuipeple":tuipeple,"fupeple":fupeple,"zcly":zcly,"lockFlag":lockFlag,"userType":userType,"channelSurceId":channelSurceId,"startTime":startTime,"endTime":endTime}, 
            success : function(data) {
            	if(data.code == 1){
            		dm.alert(data.msg);
            		return false;  
            	}
            	pageTagCallBack(data);
              	}
          	
        });
		 
	 }
	 //客服搜索
	 function kefuSearchButton(){
		 
		 $("#userSort>td").removeClass("sorting_cur");
		 $("#registerSort").val("");
		 $("#amountSort").val("");
		 $("#followUpTimeSort").val("");
		 
		 
		 //手机号
		 var phoneId=$("#phoneId").val();
		 if(phoneId){
			 if(phoneId.length<11){
				 dmCheck.tip("#phoneId","手机号必须为11位");
				 return;
			 }
		 }
		 search();
	 }
	 //分页跳转回调
	 function pageTagCallBack(data){
	 	if(data.code == 1){
           dm.alert(data.msg);
	 	}else{
	 	//清空表格数据
	 	$("#userTable").empty();
	 	//填充数据
		
		$('#tableTemplate').tmpl(data.pageResult).appendTo("#userTable");
		//初始化分页标签
		DMGold.PageTag.init({"divId":"pageId","formId":"dataForm","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
				          "pageCount":data.pageResult.pageTotal,"url":"/Manage_Api/userlist","toPageCallBack":pageTagCallBack});
		}
	  }
	  //商品排序
	  function userSortQuery(obj){
		       $("#amountSort").val("");
		       $("#registerSort").val("");
		       $("#followUpTimeSort").val("");
			    var name=obj.attr("name");
			    //删除所有的高亮样式
			    $("#userSort>td").removeClass("sorting_cur");
			    //添加高亮样式
			    obj.addClass("sorting_cur");
			    switch(name){
			        case "amount": 
				        //判断点击之前是升序还是降序
				        if(obj.find("i").attr("val")=="up"){
					        //如果是升序，则改为降序
					        $("#amountSort").val("desc");
					        
					        obj.find("i").attr("val","down");
					        obj.find("i").attr("class","sorting_status2");
				        }else{
					        //如果是降序序，则改为升序
					        $("#amountSort").val("asc");
					        
					        obj.find("i").attr("val","up")
					        obj.find("i").attr("class","sorting_status1");
				        }
				        break;
			        case "createTime": 
				        //判断点击之前是升序还是降序
				        if(obj.find("i").attr("val")=="up"){
					        //如果是升序，则改为降序
					        $("#registerSort").val("desc");
					        
					        obj.find("i").attr("val","down");
					        obj.find("i").attr("class","sorting_status2");
				        }else{
					        //如果是降序序，则改为升序
					        $("#registerSort").val("asc");
					        
					        obj.find("i").attr("val","up");
					        obj.find("i").attr("class","sorting_status1");
				        } 
				        break;
			        case "followUpTime": 
				        //判断点击之前是升序还是降序
				        if(obj.find("i").attr("val")=="up"){
					        //如果是升序，则改为降序
					        $("#followUpTimeSort").val("desc");
					        
					        obj.find("i").attr("val","down");
					        obj.find("i").attr("class","sorting_status2");
				        }else{
					        //如果是降序序，则改为升序
					        $("#followUpTimeSort").val("asc");
					        
					        obj.find("i").attr("val","up");
					        obj.find("i").attr("class","sorting_status1");
				        } 
				        break;
			      }
			  search();
	  }
	 //弹出框
	 function popDiv(url){
	 	   $.tbox.popup(url);
	 };
  	
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
 	};	
  	
	 //解锁/锁定
 	function islock(){
 		var userid=$("#hd_userid").val();
 		var islock=$("#hd_islock").val();
 		$.ajax({ 
            type : "POST", 
            url : "userPerShowLock.do", 
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
            				window.location.href="userPerList.do";
            			}
            		});
              	}else{
              		dm.alert(result.statu.description,{
            			title:"提示",				//弹窗的标提
            			okName:"确定",			//如同alert确定按钮的value
            			picClass:"d_error",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
            			notHaveClose:"xxx",
            			callback:function(){	//按确定后执行的函数
            				window.location.href="userPerList.do";
            			}
            		});
              	}
            } 
        });
 	};
  
	 //新增用户弹出框
  	function addPerUser(){
  		$("#mobile").html('<input name="accountName" type="text" id="accountName" validate="q"  maxlength="11"  class="yhgl_input" onblur="checkMobileAndEmail(this.value)"/>');
  		dmCheck.initForAjax("#addForm");
  		$("#addDialog").show();
  	};
  	
	 //校验密码和确认密码是否相同
  	function checkRepassword(msg, isFocus) {
		var repassword =  $("#addForm input[name='repassword']");	  
		if (msg) {
			tip(repassword, msg);
		} else {
			if (!repassword.val() || repassword.val() != $("#addForm input[name='passWord']").val()) {
				tip(repassword, "两次输入的密码不一致！");
			} else {
				return true;
			}
		}
		if (!isFocus){	repassword.focus();}
		return false;
  	};
  	
	 //新增用户
  	// function addPerUserSubmit(){
  	// 	alert();return false;
  	// 	if((obj = dmCheck.returnObj("#addForm"))){	return obj.focus();}
  	// 	if(!checkRepassword()){	return false;}
  	// 	if(!checkMobileAndEmail($("#accountName").val())){ return false;}
  	// 	$("#addUserPwd").val($("#addUserPwd").val());
  	// 	$.ajax({ 
   //          type : "POST", 
   //          url : "/Manage_Activity/smrzjb", 
   //          data : $("#addForm").serialize(), 
   //          success : function(result) { 
   //          	$('#addDialog').hide();
   //          	if(result.code == "000000"){
   //          		dm.alert("新增成功！",{
   //          			title:"提示",				//弹窗的标提
   //          			okName:"确定",			//如同alert确定按钮的value
   //          			picClass:"d_succeed",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
   //          			notHaveClose:"xxx",
   //          			callback:function(){	//按确定后执行的函数
   //          				window.location.href="/Manage_User/userperlist";
   //          			}
   //          		});
   //            	}else{
						
   //            		dm.alert(result.msg,{
   //          			title:"提示",				//弹窗的标提
   //          			okName:"确定",			//如同alert确定按钮的value
   //          			picClass:"d_error",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
   //          			notHaveClose:"xxx",
   //          			callback:function(){	//按确定后执行的函数
   //          				window.location.href="userPerList";
   //          			}
   //          		});
   //            	}
   //          } 
   //      });
  	// };
  	
  	//手机号或邮箱校验
  	function checkMobileAndEmail(content){
  		if(content == ""){
  			$("#accountName").focus();
  			dmCheck.tip("#accountName","手机号不能为空");
    		return false;
  		}else{
  			//非邮箱
  			if(content.indexOf('@')==-1){
  				//判断手机格式是否正确
  				if(dmCheck.regArray.Mobile.test(content)){
  					if(isExistPhone(content)){
  						$("#accountName").focus();
  						dmCheck.tip("#accountName","手机号已存在");
  	  		    		return false;
  					}
  				}else{
  					$("#accountName").focus();
  					dmCheck.tip("#accountName","手机号格式不正确");
  		    		return false;
  				}
  			}else{
  				//判断邮箱格式是否正确
				if(dmCheck.regArray.Email.test(content)){
					if(isExistEmail(content)){
  						$("#accountName").focus();
  						dmCheck.tip("#accountName","邮箱已存在");
  	  		    		return false;
  					}
  				}else{
  					$("#accountName").focus();
  					dmCheck.tip("#accountName","邮箱格式不正确"); 
  		    		return false;
  				}
  			}
  		}
  		return true;
  	};
  	
	
  //判断手机号是否存在
  function isExistPhone(phone){

	  var isExist=false;
	  if(phone){
		  $.ajax({
			  async:false,
				type:"POST",
				url:"/Manage_User/newusercc",
				data:{"phone":phone},
				success:function(data){
					    var obj = null;
						if(data.code == "200001"){
							obj = $("#addForm input[name='accountName']");
							dmCheck.tip("#accountName",data.msg);
						}
					 
				} 
			});
	  }
	  return isExist;
  };
  //判断邮箱是否存在
  function isExistEmail(email){
	  var isExist=false;
	  if(email){
		  $.ajax({
			  async:false,
				type:"POST",
				url:basePath+"shop/isExistEmail.do",
				data : {"email" : email,"userId" : ''},
				success : function(msg) {
						if (msg.flag.code == '000000') {
							if (msg.flag.data == 'Y') {
								isExist = true;
							}
						}
					}
			});
		}
		return isExist;
	};
	//重置登陆密码输入错误次数
	function resetPsc(userId) {
			DMGold.ajax({
				"data" : {'userId' : userId},
				"serialize" : true,
				"url" : "../resetPsc.do",
				"success" : function(data) {
					//显示提示信息
					if ("000000" == data.code) {
						dm.alert(data.description, {
							"picClass" : "d_succeed"
						});
					} else {
						dm.alert(data.description, {
							"picClass" : "d_error"
						});
					}
				}
			});
	};
	//取消新增个人用户
	function addDialogReset() {
		$('#addDialog').hide();
		$('#addForm')[0].reset();
	};
		
	// 关联客服
	function connectCustom(userId,isConnect,type){
			// 关联
			if(isConnect == '0'){
				$.ajax({
					type:"POST",
					url:basePath+"user/connectCustomAjax.do",
					data : {userId:userId},
					success : function(msg) {
						$('#connectTmpl').tmpl(msg).appendTo("#connectDiv");
					}
				});
			}
			// 取消关联
			else if(isConnect == '1'){
				var msg = "";
				if(type == 0){
					msg = "您确定取消该用户个人账号与客服账号的关联？";
				}else{
					msg = "您确定取消该用户个人账号与其他渠道账号的关联？";
				}
				dm.confirm(msg,{
    				title:"提示",				//标题
    				picClass:"d_doubt",
    				okName:"确定",			//如同confirm确定按钮的value
    				showClose:true,
    				showCancel:true,
    				cancleName:"取消",		//如同confirm取消按钮的value
    				callback:function(){	//按确定的执行的函数
    					$.ajax({
    						type:"POST",
    						url:basePath+"user/updateUserConnectCustom.do",
    						data : {userId:userId,type:isConnect},
    						success : function(data) {
    							if(data.code == '000000'){
    								dm.alert("操作成功！",{
    			            			title:"提示",				//弹窗的标提
    			            			okName:"确定",			//如同alert确定按钮的value
    			            			picClass:"d_succeed",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
    			            			notHaveClose:"xxx",
    			            			callback:function(){	//按确定后执行的函数
    			            				window.location.href="userPerList.do";
    			            			}
    			            		});
    			              	}else{
    			              		dm.alert(data.description,{
    			            			title:"提示",				//弹窗的标提
    			            			okName:"确定",			//如同alert确定按钮的value
    			            			picClass:"d_error",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
    			            			notHaveClose:"xxx",
    			            			callback:function(){	//按确定后执行的函数
    			            				window.location.href="userPerList.do";
    			            			}
    			            		});
    			              	}
    						}
    					});
    				}
    			});
			}
		};
		// 改变账户类型
		function changeRadio(type){
			if(type == 0){
				$("#serviceId").attr("disabled",false);
				$("#channelUserName").attr("disabled",true);
			}else if(type == 1){
				$("#serviceId").attr("disabled",true);
				$("#channelUserName").attr("disabled",false);
			}
		}
		
		// 关联客服提交
		function submitConnect(){
			var accountType = $("[name='accountType']:checked").val();
			if(accountType == '0'){
				var serviceId = $("#serviceId").val();
				if(serviceId == null || serviceId==''){
					dmCheck.tip($("#serviceId"),"客服不能为空");
					return ;
				}
			}else if(accountType == '1'){
				var channelUserName = $("#channelUserName").val();
				if(channelUserName == null || channelUserName==''){
					dmCheck.tip($("#channelUserName"),"其他渠道账号不能为空");
					return ;
				}
			}
			
			$.ajax({
				type:"POST",
				url:basePath+"user/updateUserConnectCustom.do",
				data : $("#connectForm").serialize(),
				success : function(data) {
					if(data.code == '000000'){
						dm.alert("操作成功！",{
	            			title:"提示",				//弹窗的标提
	            			okName:"确定",			//如同alert确定按钮的value
	            			picClass:"d_succeed",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
	            			notHaveClose:"xxx",
	            			callback:function(){	//按确定后执行的函数
	            				window.location.href="userPerList.do";
	            			}
	            		});
	              	}else{
	              		dm.alert(data.description,{
	            			picClass:"d_error"
	            		});
	              	}
				}
			});
		}