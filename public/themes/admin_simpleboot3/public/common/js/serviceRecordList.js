	function getServiceRecordList(){
		    var _self=this;
		    DMGold.ajax({
				url:"/Manage_Yingxiao/findServiceRecordListAjax",
				async : true,
				data:$("#dataForm").serialize(),
				type:"POST",
				success:function(msg){
					  _self.setServiceRecordList(msg);
				},
				error:function(data){
					dm.desc("提示",'系统繁忙，请稍候再试！');
				}
			});
	}
	
	 //显示数据
	function setServiceRecordList(data){
		     var _self=this;
			$("#serviceRecordList").empty();
			//填充数据
			$("#serviceRecordListTmpl").tmpl({object:data.pageResult}).appendTo("#serviceRecordList");
			
			//初始化分页标签
			DMGold.PageTags.init({
				divId:"paging",   //放入分页的div  的id
				formId:"dataForm",     //表单id
				curPage:data.pageResult.pageIndex,  //当前页
		        totalCount:data.pageResult.recordCount,//总记录数
		        pageCount:data.pageResult.pageTotal,//总页数
		        showPages:10,  //显示记录数
		        url:"/Manage_Yingxiao/findServiceRecordListAjax",  //请求路径
		        toPageCallBack:function(data){   //返回函数
		        	_self.setServiceRecordList(data);
		        }
		     });
	 }
	 