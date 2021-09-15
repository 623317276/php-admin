//初始加载数据
	function search(){
		$("#checkAllBox").attr("checked", false);
		DMGold.ajax({"formId":"form","serialize":true,"url":"/Manage_Api/saveordersist","success":pageTagCallBack});
	}
	function pageTagCallBack(data){
 		//清空表格数据
 		$("#grid").empty();
 		//填充数据
		 $('#temp').tmpl({list:data.pageResult.list,columnStatResult:data.columnStatResult}).appendTo("#grid");
		//初始化分页标签
		DMGold.PageTag.init({"divId":"pageTag","formId":"form","curPage":data.pageResult.pageIndex,"totalCount":data.pageResult.recordCount,
			          "pageCount":data.pageResult.pageTotal,"url":"","toPageCallBack":pageTagCallBack});
 	}
	
	/**
	*	初审审核通过
	*/
	function auditPass(id,status){
		var data = {"ids[0]":id,"status":status};
		 dm.confirm("您确定通过审核？",{
			title:"存金审核确定",				//标题
			okName:"确定",			//如同confirm确定按钮的value
			cancleName:"取消",		//如同confirm取消按钮的value
			picClass:"d_perfect",
            callback:function(){	//按确定的执行的函数	
            	dm.closeConfirm();
            	updateSaveStatus(data);
			}
		});
	}
	
	/**
	 * 确认收款
	 */
	function submitReceive(id){
		var data = {"ids[0]":id,"status":"SAVED"};
		 dm.confirm("您确认收款？",{
			title:"确认收款",				//标题
			okName:"确定",			//如同confirm确定按钮的value
			cancleName:"取消",		//如同confirm取消按钮的value
			picClass:"d_perfect",
           callback:function(){	//按确定的执行的函数	
           	dm.closeConfirm();
           	updateSaveStatus(data);
			}
		});
	}
	
	/**
	*	审核不通过/取消弹框
	*/
	function auditNoPassBox(id,status){
		if(status == 'FAIL'){
			$("#noPassTitle").text("取消");
		}else{
			$("#noPassTitle").text("存金审核确定");
		}
		$("#noPassForm")[0].reset();
		$("#noPassId").val(id);
		$("#noPassStatus").val(status);
		$("#noPassDiv").show();
	}
	
	/**
	*	审核不通过/取消
	*/
	function auditNoPass(){
		if(dmCheck.check("#noPassForm")){
			$("#noPassDiv").hide();
			var data=$("#noPassForm").serialize();
			updateSaveStatus(data);
		}
	}
	
	/**
	* 调用修改提金状态接口
	*/
	function updateSaveStatus(data){
		DMGold.ajax({
    		data:data,
    		async:true,
    		url:"updateSaveStatus.do",
    		success:function(data){
				//显示提示信息
				if("000000"==data.code){
					dm.alert("操作成功！",{"picClass":"d_succeed"});
				}else{
					dm.alert(data.description,{"picClass":"d_error"});
				}
				//刷新用户列表
				search();
			}
    	});
	}
	
	/**
	 * 批量审核弹框
	 */
	function batchAuditBox(){
		var singleBoxs=$(".singleBox:checked");
		if(singleBoxs.length > 0){
			$("#batchAuditForm")[0].reset();
			$("#batchAuditDiv").show();
		}else{
			dm.alert("请选择订单",{"picClass":"d_error"});
		}
		
	}
	
	
	/**
	 * 全选
	 */
	function checkAll(_this){
		if($(_this).is(':checked')){
			$(".singleBox").attr("checked", true);
		}else{
			$(".singleBox").attr("checked", false);
		}
	}
	
	/**
	 * 批量审核通过
	 * @param status
	 */
	function batchAuditPass(status){
		var batchAuditReason = $("#batchAuditReason").val();
		var data="status="+status + "&reason="+batchAuditReason;
		$(".singleBox:checked").each(function(index,element){
			data = data +"&ids["+index+"]="+$(element).val();
		})
		$("#batchAuditDiv").hide();
		updateSaveStatus(data)
	}

	/**
	 * 批量审核不通过
	 * @param status
	 */
	function batchAuditNoPass(status){
		if(dmCheck.check("#batchAuditForm")){
			var batchAuditReason = $("#batchAuditReason").val();
			var data="status="+status + "&reason="+batchAuditReason;
			$(".singleBox:checked").each(function(index,element){
				data = data +"&ids["+index+"]="+$(element).val();
			})
			$("#batchAuditDiv").hide();
			updateSaveStatus(data)
		}
	}
	
	/**
	 * 选择店铺弹框
	 */
	function selectShopBox(){
		$("#selectShopDiv").empty();
		DMGold.ajax({
    		async:true,
    		url:"shopCitiListAjax.do",
    		success:function(data){
				//显示提示信息
				if("000000"==data.code){
					$("#shopTmpl").tmpl(data.data).appendTo("#selectShopDiv");
					findShopList();
				}else{
					dm.alert(data.description,{"picClass":"d_error"});
				}
			}
    	});
	}
	
	/**
	 * 查询店铺列表数据
	 */
	function findShopList(){
		DMGold.ajax({
    		data:$("#shopForm").serialize(),
    		async:true,
    		url:"shopListAjax.do",
    		success:function(data){
				//显示提示信息
				if("000000"==data.code){
					showShopList(data);
				}else{
					dm.alert(data.description,{"picClass":"d_error"});
				}
			}
    	});
	}
	
	/**
	 * 显示店铺列表数据
	 * @param data
	 */
	function showShopList(data){
		//清空表格数据
 		$("#shopList").empty();
 		//填充数据
		 $('#shopListTmpl').tmpl(data.data.pageResult).appendTo("#shopList");
		//初始化分页标签
		DMGold.PageTag.init({"divId":"shopPage","formId":"shopForm","curPage":data.data.pageResult.pageIndex,"totalCount":data.data.pageResult.recordCount,
			          "pageCount":data.data.pageResult.pageTotal,"url":"shopListAjax.do","toPageCallBack":showShopList});
	}
	
	/**
	 * 添加选中店铺
	 */
	function addSelectShop(){
		var checkedRadio=$("input[name='radioShopId']:checked");
		if(checkedRadio.length > 0){
			var shopId=$("input[name='radioShopId']:checked").val();
			$("#shopId").val(shopId);
			var shopName=$("input[name='radioShopId']:checked").attr("shopName");
			var busTime=$("input[name='radioShopId']:checked").attr("busTime");
			var region=$("input[name='radioShopId']:checked").attr("region");
			$("#shopName").text(shopName);
			$("#bussinessTime").text(busTime);
			$("#region").text(region);
			$("#selectShopDiv").empty();
		}else{
			dm.alert("请选择店铺！",{"picClass":"d_error"});
		}
	}
	
	/**
	 * 修改存金订单
	 */
	function updateSave(){
		if(dmCheck.check($("#updateSaveForm"))){
			var minBookWeight=$("#minBookWeight").val();
			var maxBookWeight=$("#maxBookWeight").val();
			if(maxBookWeight*1 <= minBookWeight*1){
				dmCheck.tip($("#maxBookWeight"), "预约最大克重应大于最小克重");
				return false;
			}
			
			$('#updateSaveForm').ajaxSubmit({
				 type:"post",
				 url:basePath+"save/updateSaveOrder.do",
				 dataType:"json",
				 success:function(data){
					 if("000000"==data.code){
						dm.confirm("操作成功！",{
							title:"提示",				//标题
							okName:"确定",			//如同confirm确定按钮的value
							showClose:false,
							showCancel:false,
							picClass:"d_succeed",
				           callback:function(){	//按确定的执行的函数	
				        	   var orderStatus=$("#orderStatus").val();
				        	   if(orderStatus == 'BOOKING'){
				        		   window.location.href="bookingList.do";
				        	   }else{
				        		   window.location.href="waitSaveList.do";
				        	   }
				        	  
							}
						});
					}else{
						dm.alert(data.description,{"picClass":"d_error"});
					}
				 }
		     });
		}
	}
	
	var index = $("#index").val()*1;
	
	/**
	 * 选择黄金凭证  添加删除图标
	 */
	function chooseCertPic(){
		var idStr="certPicFile";
		if($(".certPicClass").length >= 3){
			dm.alert("黄金凭证图片不能超过3张！",{"picClass":"d_error"});
			return;
		}
        var fileType=["PNG","JPG","JPEG"];
        var _this=$("#"+idStr);
        if (!RegExp("\.(" + fileType.join("|") + ")$", "i").test(_this.val().toLowerCase())) {
        	dm.alert("上传的文件格式不正确，请重新上传！",{"picClass":"d_error"});
            _this.val("");
            return false;
        }
        $.ajaxFileUpload({
        	url : basePath + "common/uploadFile.do", // 用于文件上传的服务器端请求地址
            secureuri : false, // 是否需要安全协议，一般设置为false
            fileElementId : idStr, // 文件上传域的ID
            dataType : 'json', // 返回值类型 一般设置为json
            success : function(data, status) // 服务器成功响应处理函数
            {
                if(data.code == '000000'){
                    $("#certPicTmpl").tmpl({index:index,url:data.data.url}).appendTo("#goldCertDiv");
                    index = index + 1;
				 }else{
					 dm.alert(data.description,{"picClass":"d_error"});
				 }
            },
            error : function(data, status, e)// 服务器响应失败处理函数
            {
            	dm.alert("上传失败",{"picClass":"d_error"});
            }
        });
	}
	
	/**
	 * 删除图片
	 * @param _this
	 */
	function delPic(_this){
		$(_this).parent().parent().remove();
	}
	