	
	function getActivityRewardList(){
		
		DMGold.ajax({
			url:"/Manage_Yingxiao/activityRewardList",
			async : true,
			data:$("#dataForm").serialize(),
			type:"POST",
			success:function(msg){
				setActivityRewardList(msg);
			}
		});
	 }
	 //显示数据
	function setActivityRewardList(data){
		    var _self=this;
		    $("#activityList").empty();
					//填充数据
			$("#activityTmpl").tmpl({object:data.data}).appendTo("#activityList");
			
			//初始化分页标签
			DMGold.PageTags.init({
				divId:"paging",   //放入分页的div  的id
				formId:"dataForm",     //表单id
				curPage:data.data.pageResult.pageIndex,  //当前页
		        totalCount:data.data.pageResult.recordCount,//总记录数
		        pageCount:data.data.pageResult.pageTotal,//总页数
		        showPages:10,  //显示记录数
		        url:"/Manage_Yingxiao/activityRewardList",  //请求路径 
		        toPageCallBack:function(data){   //返回函数
		        	_self.setActivityRewardList(data);
		        }
		     });
	 }

//页面加载时调用
$(function(){
	getActivityRewardList();
});

