	function getServiceList(){
		    var _self=this;
		    DMGold.ajax({
				url:"/Manage_Yingxiao/findServiceListAjax",
				async : true,
				data:$("#dataForm").serialize(),
				type:"POST",
				success:function(msg){
					  _self.setServiceList(msg);
				},
				error:function(data){
					dm.desc("提示",'系统繁忙，请稍候再试！');
				}
			});
	}
	
	 //显示数据
	function setServiceList(data){
		     var _self=this;
			$("#serviceList").empty();
			//填充数据
			if(data.pageResult&&data.pageResult.list.length>0){
				//当前年月
				var nowDate="";
				if(!$("#startDate").val()&&!$("#endDate").val()){
					var myDate = new Date();
					//获取当前年
					var year=myDate.getFullYear();
					//获取当前月
					var month=myDate.getMonth()+1;
					
					//获取当前日
					//var date=myDate.getDate(); 
					
					var nowDate=year+'-'+month;
				}
				
				//开始时间
				var startDate="";
				if($("#startDate").val()){
					startDate=$("#startDate").val();
				}
				
				//结束时间
				var endDate="";
				if($("#endDate").val()){
					endDate=$("#endDate").val();
				}
				
				$("#serviceListTmpl").tmpl({"object":data.pageResult,"nowDateShow":nowDate,"startDateShow":startDate,"endDateShow":endDate}).appendTo("#serviceList");
			}
			
			//初始化分页标签
			DMGold.PageTags.init({
				divId:"paging",   //放入分页的div  的id
				formId:"dataForm",     //表单id
				curPage:data.pageResult.pageIndex,  //当前页
		        totalCount:data.pageResult.recordCount,//总记录数
		        pageCount:data.pageResult.pageTotal,//总页数
		        showPages:10,  //显示记录数
		        url:"/Manage_Yingxiao/findServiceListAjax",  //请求路径
		        toPageCallBack:function(data){   //返回函数
		        	_self.setServiceList(data);
		        }
		     });
	 }
	 