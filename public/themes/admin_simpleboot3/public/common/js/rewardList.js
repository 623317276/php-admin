
$(function(){
		//初始化列表
		getRewardList();
		
	});
	function getRewardList(){
		    var _self=this;
		    DMGold.ajax({
				url:"/Manage_Yingxiao/torewardlist",
				async : true,
				data:$("#dataForm").serialize(),
				type:"POST",
				success:function(msg){
					   _self.setRewardList(msg);
				},
				error:function(data){
					dm.desc("提示",'系统繁忙，请稍候再试！');
				}
			});
	 }
	 //显示数据
	function setRewardList(data){
		     var _self=this;
			$("#rewardList").empty();
			//填充数据
			$("#rewardListTmpl").tmpl({object:data.data.pageResult}).appendTo("#rewardList");
			
			//初始化分页标签
			DMGold.PageTags.init({
				divId:"paging",   //放入分页的div  的id
				formId:"dataForm",     //表单id
				curPage:data.data.pageResult.pageIndex,  //当前页
		        totalCount:data.data.pageResult.recordCount,//总记录数
		        pageCount:data.data.pageResult.pageTotal,//总页数
		        showPages:10,  //显示记录数
		        url:"/Manage_Yingxiao/torewardlist",  //请求路径
		        toPageCallBack:function(data){   //返回函数
		        	_self.setRewardList(data);
		        }
		     });
	 }
	 
	 //使失效
	function quitDialog(rewardId,rewardCode){
		 this.updateStatusDialog(rewardId,rewardCode);
	 }
	 //使失效-弹出框
	function updateStatusDialog(rewardId,rewardCode){
		 var _self=this;
		 var tipInfo='确认使 ‘'+rewardCode+'’ 奖励失效吗？';
		 dm.confirm(tipInfo,{
				title:"提示",				//标题
				picClass:"d_doubt",
				okName:"确定",			//如同confirm确定按钮的value
				showClose:true,
				showCancel:true,
				cancleName:"取消",		//如同confirm取消按钮的value
				callback:function(){	//按确定的执行的函数
					_self.updateStatusAjax(rewardId,rewardCode);
				}
			});
		}
		//使失效
	function updateStatusAjax(rewardId,rewardCode){
			dm.colse();
			 var message="使"+rewardCode+"失效成功";
			 DMGold.ajax({
				url:basePath+"activity/updateValidity.do",
				async : true,
				data:{"id":rewardId},
				type:"POST",
				success:function(data){
					if(data.code == '000000'){
						getRewardList();
						dm.confirm(message,{
							title:"提示",				//标题
							picClass:"d_succeed",
							okName:"确定",			//如同confirm确定按钮的value
							showClose:true,
							showCancel:true,
							cancleName:"取消",		//如同confirm取消按钮的value
							callback:function(){	//按确定的执行的函数
								dm.colse();
							}
						});
					}else{
						dm.desc("提示",data.description); 
					}
						
				}
			}); 
		}
		
		
	function changeRewardType(type,_this){
			$("#rewardType").val(type);
			$(_this).parent().children().removeClass("on");
			$(_this).addClass("on");
			$("#reward_tab").empty();
			if(type == 0){
				$('#rewardTmpl3').tmpl().appendTo('#reward_tab');
			}else if(type == 1){
				$('#rewardTmpl1').tmpl().appendTo('#reward_tab');
			}else if(type == 2){
				$('#rewardTmpl2').tmpl().appendTo('#reward_tab');
			}
		}
		
		/**
		 * 消费限制改变
		 */
	function changeLimit(value){
			if(value == '0'){
				$("#limitAmount").val("");
				$("#limitAmount").removeAttr("validate");
				$("#limitAmount").attr("disabled","disabled");
			}else if(value == '1'){
				$("#limitAmount").removeAttr("disabled");
				$("#limitAmount").attr("validate","q|amount");
			}
		}
	
	/**
	 * 选择产品弹框
	 */
	function chooseProductBox(type){
		var ids = new Array(); 
		var isShowLive = 1;
		if(type == 'BUY'){
			$("[name='buyProduct']").each(function(index,ele){
				var id=$(this).val();
				ids[index]=id;
			});
			var rewardType = $("#rewardType").val();
			if(rewardType == 2){
				isShowLive = 0;
			}
		}else if(type == 'TURN'){
			$("[name='turnProduct']").each(function(index,ele){
				var id=$(this).val();
				ids[index]=id;
			});
		}
		
		$("#rewardTmpl5").tmpl({type:type,ids:ids,isShowLive:isShowLive}).appendTo("#productBoxDiv");
		findactproductlist();
	}
	
	/**
	 * 选择产品弹框
	 */
	function chooseStableProductBox(type){
		var ids = new Array(); 
		if(type == 'BUY'){
			$("[name='buyDueTimeProduct']").each(function(index,ele){
				var id=$(this).val();
				ids[index]=id;
			});
		}else if(type == 'TURN'){
			$("[name='turnDueTimeProduct']").each(function(index,ele){
				var id=$(this).val();
				ids[index]=id;
			});
		}
		
		$("#rewardTmpl8").tmpl({type:type,ids:ids}).appendTo("#productBoxDiv");
		findactproductlist();
	}
	
	/**
	 * 选中产品点击事件
	 */
	function checkSingleId(){
		var num = $("[name = 'productIds']:checked").length;
		$("#checkedNum").text(num);
	}
	
	// 查询奖励可绑定的产品信息
	function findActProductList(){
		$.ajax({
			url:"/Manage_Yingxiao/findactproductlist",
			data:$("#productForm").serialize(),
			type:"POST",
			success:function(msg){
				 showActProductList(msg);
			},
		});
	}
	
	// 显示奖励可绑定的产品信息
	function showActProductList(data){
		var _self=this;
		$("#checkAllProductBox").attr("checked", false);
		$("#checkedNum").text("0");
		$("#productList").empty();
		//填充数据
		$("#rewardTmpl6").tmpl(data.data.pageResult).appendTo("#productList");
		
		//初始化分页标签
		DMGold.PageTags.init({
			divId:"productPage",   //放入分页的div  的id
			formId:"productForm",     //表单id
			curPage:data.data.pageResult.pageIndex,  //当前页
	        totalCount:data.data.pageResult.recordCount,//总记录数
	        pageCount:data.data.pageResult.pageTotal,//总页数
	        showPages:10,  //显示记录数
	        url:"/Manage_Yingxiao/findactproductlist",  //请求路径
	        toPageCallBack:function(data){   //返回函数
	        	_self.showActProductList(data);
	        }
	     });
	}
	
	/**
	 * 全选
	 */
	function chooseAllProduct(_this){
		if($(_this).is(':checked')){
			$("[name = 'productIds']").attr("checked", true);
		}else{
			$("[name = 'productIds']").attr("checked", false);
		}
		var num = $("[name = 'productIds']:checked").length;
		$("#checkedNum").text(num);
	}
	
	/**
	 * 添加所选奖励
	 */
	function addCheckedReward(){
		var flag = false;
		var productId="";
		var productName="";
		var boxType = $("#boxType").val();
		var tagert="";
		if(boxType == 'BUY'){
			tagert = "buyUl";
		}else{
			tagert = "turnUl";
		}
		$("[name='productIds']").each(function(){
			if($(this).is(':checked')){
				flag =  true;
				productId = $(this).val();
				productName = $(this).attr("productName");
				$("#rewardTmpl4").tmpl({id:productId,productName:productName,boxType:boxType}).appendTo("#"+tagert);
			}
		});
		if(!flag){
			dm.desc("提示","请选择产品！");
			return false;
		}
		$('#productBoxDiv').empty();
	}
	
	/**
	 * 添加所选奖励
	 */
	function addCheckedDueTimeReward(){
		var flag = false;
		var dueTime="";
		var boxType = $("#boxType").val();
		var tagert="";
		if(boxType == 'BUY'){
			tagert = "buyStableUl";
		}else{
			tagert = "turnStableUl";
		}
		$("[name='productIds']").each(function(){
			if($(this).is(':checked')){
				flag =  true;
				dueTime = $(this).attr("dueTime");
				$("#rewardTmpl7").tmpl({dueTime:dueTime,boxType:boxType}).appendTo("#"+tagert);
			}
		});
		if(!flag){
			dm.desc("提示","请选择稳盈金产品！");
			return false;
		}
		$('#productBoxDiv').empty();
	}
	
	function deleteProduct(_this){
		$(_this).parent().remove();
	}
	
		/**
		 * 有效期改变
		 */
	function changeValiTime(val){
			if(val == '0'){
				$("#validity").attr("validate","q|z");
				$("#validity").removeAttr("disabled");
				
				$("#startTime").removeAttr("validate");
				$("#endTime").removeAttr("validate");
				$("#startTime").attr("disabled","disabled");
				$("#endTime").attr("disabled","disabled");
				$("#startTime").val("");
				$("#endTime").val("");
			}else if(val == '1'){
				$("#validity").removeAttr("validate");
				$("#validity").val("");
				$("#validity").attr("disabled","disabled");
				
				$("#startTime").attr("validate","q");
				$("#endTime").attr("validate","q");
				$("#startTime").removeAttr("disabled");
				$("#endTime").removeAttr("disabled");
			}
		}
			
		/**
		 * 新增奖励
		 */
	function addReward(){
			if(dmCheck.check("#dataForm")){
				var rewardType = $("#rewardType").val();
				var buyData = "";
				var turnData = "";
				if(rewardType != 0){
					var costLimit = $("#costLimit").val();
					var buyNum = 0;
					var buyDueTimeNum = 0;
					$("[name='buyProduct']").each(function(){
						var id=$(this).val();
						//如果number!=0则用&连接后面内容
						buyData=buyData+"&buyList["+buyNum+"]="+id;
						buyNum++;
					});
					$("[name='buyDueTimeProduct']").each(function(){
						var id=$(this).val();
						//如果number!=0则用&连接后面内容
						buyData=buyData+"&buyDueTimeList["+buyDueTimeNum+"]="+id;
						buyDueTimeNum++;
					});
					if(costLimit != '' && buyNum == 0 && buyDueTimeNum == 0){
						dm.confirm("购买限制产品不能为空!",{
		    				title:"提示",				//标题
		    				picClass:"d_error",
		    				okName:"确定",			//如同confirm确定按钮的value
		    				showClose:false,
		    				showCancel:false,
		    				cancleName:"取消",		//如同confirm取消按钮的value
		    				callback:function(){	//按确定的执行的函数
		    					dm.closeConfirm();
		    				}
		    			});
						return false;
					}else if((buyDueTimeNum >0 || buyNum > 0) && costLimit == ''){
						dmCheck.tip($("#costLimit"),"购买金额限制不能为空");
						return false;
					}
					
					var turnLimit = $("#turnLimit").val();
					var turnNum = 0;
					$("[name='turnProduct']").each(function(){
						var id=$(this).val();
						//如果number!=0则用&连接后面内容
						turnData=turnData+"&turnList["+turnNum+"]="+id;
						turnNum++;
					});
					var turnDueTimeNum = 0;
					$("[name='turnDueTimeProduct']").each(function(){
						var id=$(this).val();
						//如果number!=0则用&连接后面内容
						turnData=turnData+"&turnDueTimeList["+turnDueTimeNum+"]="+id;
						turnDueTimeNum++;
					});
					if(turnLimit != '' && turnNum == 0 && turnDueTimeNum == 0){
						dm.confirm("转入限制产品不能为空!",{
		    				title:"提示",				//标题
		    				picClass:"d_error",
		    				okName:"确定",			//如同confirm确定按钮的value
		    				showClose:false,
		    				showCancel:false,
		    				cancleName:"取消",		//如同confirm取消按钮的value
		    				callback:function(){	//按确定的执行的函数
		    					dm.closeConfirm();
		    				}
		    			});
						return false;
					}else if((turnNum > 0 || turnDueTimeNum > 0)&& turnLimit==''){
						dmCheck.tip($("#turnLimit"),"购买金额限制不能为空");
						return false;
					}
					
					if(turnNum == 0 && turnDueTimeNum == 0 && buyNum == 0 && buyDueTimeNum == 0){
						dm.confirm("消费限制产品不能为空!",{
		    				title:"提示",				//标题
		    				picClass:"d_error",
		    				okName:"确定",			//如同confirm确定按钮的value
		    				showClose:false,
		    				showCancel:false,
		    				cancleName:"取消",		//如同confirm取消按钮的value
		    				callback:function(){	//按确定的执行的函数
		    					dm.closeConfirm();
		    				}
		    			});
						return false;
					}
				}
				
				DMGold.ajax({
					url:"/Manage_Activity/addreward",
					async:true,
					data:$("#dataForm").serialize()+buyData+turnData,
					success:function(data){
						if(data.code == '000000'){
							dm.confirm("操作成功!",{
			    				title:"提示",				//标题
			    				picClass:"d_succeed",
			    				okName:"确定",			//如同confirm确定按钮的value
			    				showClose:false,
			    				showCancel:false,
			    				cancleName:"取消",		//如同confirm取消按钮的value
			    				callback:function(){	//按确定的执行的函数
			    					location.href = "/Manage_Yingxiao/torewardlist";
			    				}
			    			});
						}else{
							dm.desc("提示",data.description);
						}  
							
					}
				}); 
			}
		}
		
		/**
		 * 修改奖励
		 */
	function updateReward(){
			if(dmCheck.check("#dataForm")){
				var rewardType = $("#rewardType").val();
				var buyData = "";
				var turnData = "";
				if(rewardType != 0){
					var costLimit = $("#costLimit").val();
					var buyNum = 0;
					if(costLimit != ''){
						$("[name='buyProduct']").each(function(){
							var id=$(this).val();
							//如果number!=0则用&连接后面内容
							buyData=buyData+"&buyList["+buyNum+"]="+id;
							buyNum++;
						});
						if(buyNum == 0){
							dm.desc("提示","购买限制产品不能为空！");
							return false;
						}
					}
					
					var turnLimit = $("#turnLimit").val();
					var turnNum = 0;
					if(turnLimit != ''){
						$("[name='turnProduct']").each(function(){
							var id=$(this).val();
							//如果number!=0则用&连接后面内容
							turnData=turnData+"&turnList["+turnNum+"]="+id;
							turnNum++;
						});
						if(turnNum == 0){
							dm.desc("提示","转入限制产品不能为空！");
							return false;
						}
					}
					
					if(turnNum == 0 && buyNum == 0){
						dm.desc("提示","消费限制产品不能为空！");
						return false;
					}
				}
				
				DMGold.ajax({
					url:"/Manage_Activity/updateReward",
					data:$("#dataForm").serialize()+buyData+turnData,
					type:"POST",
					async:true,
					success:function(data){
						if(data.code == '000000'){
							dm.confirm("操作成功!",{
			    				title:"提示",				//标题
			    				picClass:"d_succeed",
			    				okName:"确定",			//如同confirm确定按钮的value
			    				showClose:false,
			    				showCancel:false,
			    				cancleName:"取消",		//如同confirm取消按钮的value
			    				callback:function(){	//按确定的执行的函数
			    					location.href = "/Manage_Yingxiao/toRewardList";
			    				}
			    			});
						}else{
							dm.desc("提示",data.description);
						}
							
					}
				}); 
			}
		}
		
		/**
		 * 查询绑定的活动列表
		 */
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



	function getBindingActList(){
		    var _self=this;
		    DMGold.ajax({
				url:"/Manage_Yingxiao/getBindingActList",
				data:{cid:gid},
				type:"POST",
				success:function(msg){
					  _self.setBindingActList(msg);
				},
				error:function(data){
					 DM.Util.dialogShow('系统繁忙，请稍候再试！');
				}
			});
	 }
	 //显示数据
	function setBindingActList(data){
		     var _self=this;
			$("#detailsActivityList").empty();
			//填充数据
			$("#rewardDetailsActivityTmpl").tmpl({list:data.data.pageResult.list}).appendTo("#detailsActivityList");
	 }
	 /**
	 * 校验商户名
	 */
	function checkUserName(){
		//校验为空操作：
		if(dmCheck.checkOne($("#shopName"))){
			var flag = true;
			// 将回车换行替换成空字符
			var arr=$("#shopName").val().trim().replace(/[\r\n]/g,"");
			if(arr.charAt(arr.length - 1)==';'){//A;B;|A;B
				arr=arr.substring(0,arr.length-1);//A;B
			}
			arr=arr.split(";");
			//alert(arr.length);
			for(var i=0;i<arr.length;i++){//如果输入一个,校验是否存在（暂时：如果多个，一一校验）
				if(arr[i].trim().length>0){
					DMGold.ajax({
			            url:"activity/isExitShopName.do",
			            data:{"userName":arr[i].trim()},
			            async: false,
			            error: function() {
							Dialog.show("检验店铺名是否存在失败！");  
			            },
			            success: function(data) {
			            	if(data.code != '000000'){
				            	flag = false;
				            	dmCheck.tip($("#shopName"),"店铺名"+arr[i]+"不存在！");
			            	}
			          }
			  		});
				}
				if(!flag){
					break;
				}
			}
		    return flag;
		}
	}
	
	/**
	 * 导入用户名
	 * @param fileId
	 * @returns
	 */
	function submitUpload(fileId){
		$.ajaxFileUpload({
            url: basePath+"operations/inputLetter.do?", //用于文件上传的服务器端请求地址
            secureuri: false, //是否需要安全协议，一般设置为false
            fileElementId: fileId, //文件上传域的ID
            dataType: 'json', //返回值类型 一般设置为json
            success: function (data, status)  //服务器成功响应处理函数
            {
            	var text=data.result;
	            if('Formatter Error' == text){
	       			Dialog.show("只支持txt文件!","tip");
	       			  return;
	       		}
	       		text = text.replace(';\r\n',/;/g);
	       		var arr=$("#shopName").val().trim().replace(/[\r\n]/g,"");
	       		if(arr){
	       			if(arr.charAt(arr.length - 1)==';'){//A;B;|A;B
						$("#shopName").val(arr+text);
					}else{
						$("#shopName").val(arr+";"+text);
					}
	       		}else{
	       			$("#shopName").val(text);
	       		}
				
	       		
            },
            error: function (data, status, e)//服务器响应失败处理函数
            {
                Dialog.show("导入异常，请联系管理员！","error");
            }
        });
	  }


