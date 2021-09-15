	
	function getVoucherList(typeFlag){
			DMGold.ajax({
				url:"/Manage_Apil/daijinqjl",
				async : true,
				data:$("#dataForm").serialize(),
				type:"POST",
				success:function(msg){
					setVoucherList(msg);
				}
			});
	 }
	 //显示数据
	function setVoucherList(data){
		if(data.code == 1){
			 dm.alert(data.msg);
		 }else{
		     var _self=this;
			$("#listGrid").empty();
			//填充数据
	    	
	     		 //个人-填充数据模板数据
			 $("#voucherListTmpl").tmpl({msg:data.pageResult.list}).appendTo("#listGrid");
	     	
			 //初始化分页标签
	  			DMGold.PageTag.init({"divId":"pagings","formId":"dataForm","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,"pageCount":data.pageResult.pageTotal,"url":"/Manage_Apil/daijinqjl","toPageCallBack":setVoucherList});
		}
	}
		
  

//页面加载时调用
$(function(){
	    
	     getVoucherList();
	  
	  	 //个人统计-tab
		 $("#personalStatis").click(function(){
			  $("#shopStatis").removeClass("hover"); 
			  $(this).addClass("hover");
			  
			  $("#dataForm")[0].reset();
			  
			  //查询条件
			  $("#userTypeId").val("PERSONAL");
			  $("#personalUserName").html("用户名：");
	   		  $("#personalName").html("用户姓名：");
	   		  
			  //列表字段
	   		  $("#personalToCont").show();
	   		  $("#shopToCont").hide();
	   		  
			  getVoucherList();
		  });
		 
		  //店铺统计-tab
		  $("#shopStatis").click(function(){
			  $("#personalStatis").removeClass("hover"); 
			  $(this).addClass("hover");
			  
			  $("#dataForm")[0].reset();
			  
			  //查询条件
			  $("#userTypeId").val("SHOP");
	  		  $("#personalUserName").html("店铺ID：");
	   		  $("#personalName").html("店铺名称：");
			  
			  //列表字段
	   		  $("#personalToCont").hide();
	   		  $("#shopToCont").show();
	   		  
			  getVoucherList();
		  });
});

