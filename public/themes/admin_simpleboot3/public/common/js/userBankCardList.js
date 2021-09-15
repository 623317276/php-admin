
  	 $(function(){
  		 searchUser();
  		
  		 //个人银行卡管理
  		 $("#perUserBankCard").click(function(){
  	 		  $(this).addClass("hover");
  	 		  $("#shopBankCard").removeClass("hover"); 
  	 		  
  	 		  $('#dataForm')[0].reset();
  	 		
  	 		  $("#shopTable").hide();
  	 		  $("#userTable").show();
  	 		  
  	 		  $("#searchUserId").show();
  	 		  $("#searchShopId").hide();
  	 		  searchUser();
  	 	  });
  		 
  		  //店铺银行卡管理
  	 	  $("#shopBankCard").click(function(){
  	 		 $(this).addClass("hover");
  	 		 $("#perUserBankCard").removeClass("hover");
  	 		 
  	 		 $('#dataForm')[0].reset();
  	 		 
	 		 $("#userTable").hide();
  	 		 $("#shopTable").show();
  	 		 
  	 	  
 	 		 $("#searchUserId").hide();
 	 		 $("#searchShopId").show();
  	 		 searchShop();
  	 	  });
     });
	 
  	//个人银行卡管理
  	function searchUser(){
 		DMGold.ajax({"formId":"dataForm","serialize":true,"url":"/Manage_Yingxiao/yhangk","success":pageTagCallBackUser});
	}
  	
    //个人银行卡管理-分页跳转回调
 	function pageTagCallBackUser(data){
 		//清空表格数据
 		$("#userBankCardListGrid").empty();
 		//填充数据
		 $('#userBankCardTemplate').tmpl(data.pageResult).appendTo("#userBankCardListGrid"); 
		//初始化分页标签
		DMGold.PageTag.init({"divId":"pageId","formId":"dataForm","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
			          "pageCount":data.pageResult.pageTotal,"url":"/Manage_Yingxiao/yhangk","toPageCallBack":pageTagCallBackUser});
 	}
    
    
 	
 	//店铺银行卡管理
 	function searchShop(){
 		DMGold.ajax({"formId":"dataForm","serialize":true,"url":"/Manage_Api/shopyhk","success":pageTagCallBackShop});
	}
  	
 	//店铺银行卡管理-分页跳转回调
 	function pageTagCallBackShop(data){
 		//清空表格数据
 		$("#shopBankCardListGrid").empty();
 		//填充数据
		 $('#shopBankCardTemplate').tmpl(data.pageResult).appendTo("#shopBankCardListGrid");
		//初始化分页标签
    //初始化分页标签
    DMGold.PageTag.init({"divId":"pageShopId","formId":"dataForm","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
                "pageCount":data.pageResult.pageTotal,"url":"","toPageCallBack":pageTagCallBackShop});
 	}
 	
 	
	  //弹出框
	 function popDiv(url){
	 	   $.tbox.popup(url);
	 }
	  
	  function showFailReason(bankChangeId){
		  //提交数据
		  DMGold.ajax({"formId":"publicForm","data":{bankChangeId:bankChangeId},"url":"findBankCardChangeReasonAjax.do",
	  		       "success":function(data){
		  		    	 $("#remarkId").text(data.data.data.singleResult.reason);
		  		 	     $("#showDialog").show();
	  	     }
		});
	  }