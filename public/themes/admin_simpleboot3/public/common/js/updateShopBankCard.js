	$(function(){
		//获取所有省
		// getAllProvince();
	});
  //增加校验模式
  dmCheck.init("#publicForm");
  
  //判断用户名是否存在
  function isExitUserName(username){
		if(username){
			$.ajax({ 
	            type : "POST", 
	            url : basePath+"finance/getUserid.do", 
	            data : {"userName":username}, 
	            success : function(result) {
	            	if(result.user.code!='000000'){
	            		$("#shopId").focus();
	            		dmCheck.tip("#shopId","店铺ID不存在");
	            	}
	            }
			});
		}
	}
  function addUserBankCard(){
	  if(!dmCheck.check("#publicForm")){
			return false;
	  }
	  //提交数据
	  DMGold.ajax({"formId":"publicForm","serialize":true,"url":"updateShopBankCardAjax.do",
  		       "success":function(data){
					//显示提示信息
					if("000000"==data.data.code){
						//关闭弹出框
						$.tbox.close();
						dm.alert("操作成功！",{
	            			title:"提示",				//弹窗的标提
	            			okName:"确定",			//如同alert确定按钮的value
	            			picClass:"d_succeed",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
	            			notHaveClose:"xxx",
	            			callback:function(){	//按确定后执行的函数
	            				searchShop();
	            			}
	            		});
					}else if("200055"==data.data.code){
						dmCheck.tip("#shopId","当前店铺ID已绑定银行卡");
						return;
					}else{
						dm.alert(data.data.description,{
	            			title:"提示",				//弹窗的标提
	            			okName:"确定",			//如同alert确定按钮的value
	            			picClass:"d_error",	//这里有三个值 "d_succeed" 显示成功图标,"d_error" 显示错误图标,"d_perfect" 显示信息提示图标,
	            			notHaveClose:"xxx",
	            			callback:function(){	//按确定后执行的函数
	            				searchShop();
	            			}
	            		});
					}
  				}
	  });
  }
 //  //获取所有省
 //  function getAllProvince(){
	// 	//获取所有省
 //    	$.ajax({
	// 			type:"POST",
	// 			url:"/Manage_Api/ajaxgetregion",
	// 			success:function(data){
	// 				pCallBack(data);
	// 			}
	// 		});
	// }
  // function pCallBack(data){
  //       $("#provinces").empty();
  //       $("#provinces").append("<option value=''>请选择</option>");
  //       for(var i=0;i<data.length;i++){
  //                       $("#provinces").append("<option value='"+data[i].id+"' >"+data[i].name+"</option>");
  //               }
  //   }
	//获取市
	 // function changeProvince(pid){
  //   	console.log(pid);
  //   	if(pid =='' || pid == null){
		// 	$("#city").empty();
		// 	$("#city").append("<option value=''>--请选择--</option>");
		// }else{
		// 	$.ajax({
		// 		type:"POST",
		// 		url:"/Manage_Api/ajaxgetregion",
		// 		data:{"subRegionId":pid},
		// 		success:function(data){
		// 			$("#city").empty();
		// 			$("#city").append("<option value=''>--请选择--</option>");
		// 			for(var i=0;i<data.length;i++){
		// 					$("#city").append("<option value='"+data[i].id+"'>"+data[i].name+"</option>");
		// 			}
		// 		}
		// 	});
		// }
  //   }
	
	//获取区域
	// function changeCity(cid){
	// 	if(cid =='' || cid == null){
	// 		$("#area").empty();
	// 		$("#area").append("<option value=''>请选择</option>");
	// 	}else{
	// 		$.ajax({
	// 			type:"POST",
	// 			url:basePath+"audit/getProvince.do",
	// 			data:{"subRegionId":cid,"hasFilter":true},
	// 			success:function(msg){
	// 				var list=msg.data.data.list;
	// 				$("#area").empty();
	// 				$("#area").append("<option value=''>请选择</option>");
	// 				for(var i=0;i<list.length;i++){
	// 					$("#area").append("<option value='"+list[i].id+"'>"+list[i].name+"</option>");
	// 				}
	// 			}
	// 		});
	// 	}
	// }