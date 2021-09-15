/**
 * 活动js
 * huxingwei
 */
var ruleDivNum=0;
var limitRuleDivNum=0;
var divIdS;
		/**
		 * 活动状态枚举
		 */
		var activityStatusEnum={
			"0":"未开始",
			"1":"进行中",
			"2":"待审核",
			"3":"审核不通过",
			"4":"已结束",
			"5":"已作废"
		};
		/**
		 * 活动类型枚举
		 */
		var activityTypeEnum={
			"0":"注册赠送",
			"1":"绑卡赠送",
			"2":"单笔投资赠送",
			"3":"累计投资赠送",
			"4":"首次投资赠送",
			"5":"推荐首次投资赠送",
			"6":"指定用户赠送",
			"7":"登录赠送",
			"8":"用户领取",
			"9":"推荐好友购买新手金赠送"
		};
		/**
		 * 奖励类别
		 */
		var rewardTypeEnum={
			"0":"现金",
			"1":"代金券",
			"2":"加息券"
		};
		/**
		 * 模版限制条件类型
		 */
		var actRuleTmplType={
			"0":"奖励数量",
			"1":"发放策略",
			"2":"领取限制",
			"3":"奖励设置"
		};
	
	/**
	 * 获取活动列表
	 */
	function getActivityList(){
		var _self = this;
		DMGold.ajax({
			url : "/Manage_Yingxiao/activityListAjax",
			async:true,
			data : $("#dataForm").serialize(),
			success : function(data) {
				if (data.code == '000000') {
					_self.showActivityList(data);
				}
			}
		});
	}
	
	/**
	 * 显示列表数据
	 */
	function showActivityList(data){
		var _self = this;
		$("#actList").empty();
		if (data.data.pageResult) {
 			var list = data.data.pageResult.list;
 			for ( var index in list) {
 				//状态重命名
 				list[index].actStatusName = {};
 				 //(状态)枚举数据
 				list[index].actStatusName = _self.activityStatusEnum[list[index]["activityStatus"]];
 				
 				//类型重命名
 				list[index].actTypeName = {};
 				 //类型枚举数据
 				list[index].actTypeName = _self.activityTypeEnum[list[index]["activityType"]];
 				
 			}
 		 }
		//填充数据
		$('#activityTempl').tmpl({object: data.data.pageResult}).appendTo("#actList");
		//初始化分页标签
		DMGold.PageTags.init({
			divId: "pageDiv", //放入分页的div  的id
			formId: "dataForm", //表单id
			curPage: data.data.pageResult.pageIndex, //当前页
			totalCount: data.data.pageResult.recordCount,//总记录数
			pageCount: data.data.pageResult.pageTotal,//总页数
			showPages: 10, //显示记录数
			url: "/Manage_Yingxiao/activityListAjax", //请求路径
			toPageCallBack: function (data) { //返回函数
				_self.showActivityList(data);
			}
		});
	}
	
	/**
	 * 新增页面查询活动规则模版列表
	 */
	function getActRuleTmplList(){
		var _self = this;
		//获取活动类型
		var activityType=$("[name='activityType']").val();
		DMGold.ajax({
			url : "/Manage_Yingxiao/findActRuleTmplList",
			async:true,
			data : {"activityType":activityType},
			success : function(data) {
				if (data.code == '000000') {
					_self.showActRuleTmplList(data);
				}
			}
		});
	}
	
	/**
	 * 新增页面显示活动规则模版列表
	 * @param data
	 * @returns
	 */
	function showActRuleTmplList(data){
		var list = null;
		if (data.data.list != null && data.data.list.length > 0) {
			var _self = this;
 		    list = data.data.list;
 		   for ( var index=0;index<list.length;index++) {
 				//模版限制类型重命名
 				list[index].limitTypeName = {};
 				 //模版限制类型枚举数据
 				list[index].limitTypeName = _self.actRuleTmplType[list[index]["limitType"]];
 				
 				var elements = null;
 				var values=null;
 				var description=list[index].description;
 				if(list[index].templateValue){
 					var templateValue=list[index].templateValue;
 	 				values=templateValue.split("|");
 				}else{
 					values=new Array(list[index].elementNum);
 					for (var int = 0; int < values.length; int++) {
 						values[int]="";
					}
 				}
 				for(var i=0; i<list[index].elementNum; i++){
 					switch (list[index].templateType) {
						case "0":
							var isRepeat="";
							var onblur="";
		 					if(data.data.isAdd == '0' && list[index].limitType == '0'){
		 						isRepeat="chargeCon";
		 						onblur="onblur='checkAmountNum(this,"+ruleDivNum+")' id='rechargeAmount"+ruleDivNum+"'";
		 					}
							elements = "<input type='text' class='text border w160 pl5 "+isRepeat+"' "+onblur+" name='actRuleList["+ruleDivNum+"].actRuleTempleList["+index+"].ruleValue' value='"+values[i]+"' "+list[index].checkRule+">";
							break;
						case "1":
							var checked="";
							if(i == 0){
								checked = "checked";
							}
							elements = '<input type="radio" '+checked+' name="actRuleList['+ruleDivNum+'].actRuleTempleList['+index+'].ruleValue" value="'+values[i]+'" '+list[index].checkRule+'>';
							break;
						case "2":
							elements = "<input type='text' class='text border w160 pl5 date' name='actRuleList["+ruleDivNum+"].actRuleTempleList["+index+"].ruleValue' value='"+values[i]+"' "+list[index].checkRule+" onfocus='WdatePicker({readOnly:true,dateFmt:\"yyyy-MM-dd HH:mm:ss\"})'>";
							break;
						case "3":
							elements = "<input type='checkbox' name='actRuleList["+ruleDivNum+"].actRuleTempleList["+index+"].ruleValue' value='"+values[i]+"' "+list[index].checkRule+">";
							break;
					}
 					description=description.replace(new RegExp("\\{"+i+"\\}","g"), elements); 
 				}
 				
 				list[index].descriptionDesc = description;
 			}
		}
		var activityType=$("[name='activityType']").val();
		$('#activityRuleTempl').tmpl({list:list,num:ruleDivNum,activityType:activityType}).appendTo("#ruleDiv");
		if(list != null){
			for ( var index=0;index<list.length;index++) {
				$("#element"+ruleDivNum+index).html(list[index].descriptionDesc);
			}
		}
		
		// 判断是够可追加活动规则 
		if(data.data.isAdd == '0'){
			$("#addRuleDiv").empty();
			$("#addRuleTmpl").tmpl().appendTo("#addRuleDiv");
		}
	}
	
	/**
	 * 修改页面查询活动规则模版列表
	 * type 1 修改 2查看
	 */
	function getActRuleTmplValueList(type){
		var _self = this;
		//获取活动类型
		var activityId=$("#activityId").val();
		DMGold.ajax({
			url : "activityRuleDetailAjax.do",
			data : {"id":activityId},
			async:true,
			success : function(data) {
				if (data.code == '000000') {
					if(type == 1){
						_self.showActRuleTmplValueEditAct(data);
					}else{
						_self.showActRuleTmplValueShowAct(data);
					}
				}
			}
		});
	}
	
	/**
	 * 修改页面显示活动规则模版列表
	 * @param data
	 * @returns
	 */
	function showActRuleTmplValueEditAct(data){
		var list = null;
		if (data.data.list != null && data.data.list.length > 0) {
			ruleDivNum=data.data.list.length - 1;
			var _self = this;
 		    list = data.data.list;
 		   for ( var index=0;index<list.length;index++) {
 				var actRuleTempleList = list[index].actRuleTempleList;
 				for ( var j=0;j<actRuleTempleList.length;j++) {
 					//模版限制类型重命名
 					actRuleTempleList[j].limitTypeName = {};
					 //模版限制类型枚举数据
 					actRuleTempleList[j].limitTypeName = _self.actRuleTmplType[actRuleTempleList[j]["limitType"]];
					
 					// 转译控件
					var elements = null;
					// 模版默认值
					var values=null;
					// 规则实际值
					var ruleValues=actRuleTempleList[j].ruleValue.split(",");
					// 模版描述
					var description=actRuleTempleList[j].description;
					// 模版默认值根据|切
					if(actRuleTempleList[j].templateValue){
						var templateValue=actRuleTempleList[j].templateValue;
		 				values=templateValue.split("|");
					}
					for(var i=0; i<actRuleTempleList[j].elementNum; i++){
						switch (actRuleTempleList[j].templateType) {
							case "0":
								var isRepeat="";
								var onblur="";
			 					if(actRuleTempleList[j].limitType == '0'){
			 						isRepeat="chargeCon";
			 						onblur="onblur='checkAmountNum(this,"+index+")' id='rechargeAmount"+index+"'";
			 					}
								elements = "<input type='text' class='text border w160 pl5  "+isRepeat+"' "+onblur+" name='actRuleList["+index+"].actRuleTempleList["+j+"].ruleValue' value='"+ruleValues[i]+"' "+actRuleTempleList[j].checkRule+">";
								break;
							case "1":
								var isCheck="";
								for (var int = 0; int < ruleValues.length; int++) {
									if(values[i] == ruleValues[int]){
										isCheck='checked';
									}
								}
								elements = '<input type="radio" name="actRuleList['+index+'].actRuleTempleList['+j+'].ruleValue" '+isCheck+' value="'+values[i]+'" '+actRuleTempleList[j].checkRule+'>';
								break;
							case "2":
								elements = "<input type='text' class='text border w160 pl5 date' name='actRuleList["+index+"].actRuleTempleList["+j+"].ruleValue' value='"+ruleValues[i]+"' "+actRuleTempleList[j].checkRule+" onfocus='WdatePicker({readOnly:true,dateFmt:\"yyyy-MM-dd HH:mm:ss\"})'>";
								break;
							case "3":
								var isCheck="";
								for (var int = 0; int < ruleValues.length; int++) {
									if(values[i] == ruleValues[int]){
										isCheck='checked';
									}
								}
								elements = "<input type='checkbox' name='actRuleList["+index+"].actRuleTempleList["+j+"].ruleValue' "+isCheck+" value='"+values[i]+"' "+actRuleTempleList[j].checkRule+">";
								break;
						}
						description=description.replace(new RegExp("\\{"+i+"\\}","g"), elements); 
					}
					
					actRuleTempleList[j].descriptionDesc = description;
				}
 			}
		}
		$('#editActRuleTmpl').tmpl({list:list}).appendTo("#ruleDiv");
		for ( var index=0;index<list.length;index++) {
			var actRuleTempleList = list[index].actRuleTempleList;
			for ( var j=0;j<actRuleTempleList.length;j++) {
				$("#element"+index+""+j).html(actRuleTempleList[j].descriptionDesc);
			}
		}
	}
	
	/**
	 * 查看页面显示活动规则模版列表
	 * @param data
	 * @returns
	 */
	function showActRuleTmplValueShowAct(data){
		var list = null;
		if (data.data.list != null && data.data.list.length > 0) {
			var _self = this;
 		    list = data.data.list;
 			for ( var index=0;index<list.length;index++) {
 				var actRuleTempleList = list[index].actRuleTempleList;
 				for ( var j=0;j<actRuleTempleList.length;j++) {
 					//模版限制类型重命名
 					actRuleTempleList[j].limitTypeName = {};
					 //模版限制类型枚举数据
 					actRuleTempleList[j].limitTypeName = _self.actRuleTmplType[actRuleTempleList[j]["limitType"]];
					
 					// 转译控件
					var elements = null;
					// 模版默认值
					var values=null;
					// 规则实际值
					var ruleValues=actRuleTempleList[j].ruleValue.split(",");
					// 模版描述
					var description=actRuleTempleList[j].description;
					// 模版默认值根据|切
					if(actRuleTempleList[j].templateValue){
						var templateValue=actRuleTempleList[j].templateValue;
		 				values=templateValue.split("|");
					}
					for(var i=0; i<actRuleTempleList[j].elementNum; i++){
						switch (actRuleTempleList[j].templateType) {
							case "0":
								elements = ruleValues[i];
								break;
							case "1":
								var isCheck="";
								for (var int = 0; int < ruleValues.length; int++) {
									if(values[i] == ruleValues[int]){
										isCheck='checked';
									}
								}
								elements = '<input type="radio" disabled name="actRuleList['+index+'].actRuleTempleList['+j+'].ruleValue" '+isCheck+' value="'+values[i]+'" '+actRuleTempleList[j].checkRule+'>';
								break;
							case "2":
								elements =ruleValues[i];
								break;
							case "3":
								var isCheck="";
								for (var int = 0; int < ruleValues.length; int++) {
									if(values[i] == ruleValues[int]){
										isCheck='checked';
									}
								}
								elements = "<input type='checkbox' disabled name='actRuleList["+index+"].actRuleTempleList["+j+"].ruleValue' "+isCheck+" value='"+values[i]+"' "+actRuleTempleList[j].checkRule+">";
								break;
						}
						description=description.replace(new RegExp("\\{"+i+"\\}","g"), elements); 
					}
					
					actRuleTempleList[j].descriptionDesc = description;
				}
 			}
		}
		$('#editActRuleTmpl').tmpl({list:list}).appendTo("#ruleDiv");
		for ( var index=0;index<list.length;index++) {
			var actRuleTempleList = list[index].actRuleTempleList;
			for ( var j=0;j<actRuleTempleList.length;j++) {
				$("#element"+index+j).html(actRuleTempleList[j].descriptionDesc);
			}
		}
	}
	
	function checkAmountNum(_this,eleNum){
		var before=$("#rechargeAmount"+(eleNum-1));
		var next=$("#rechargeAmount"+(eleNum+1));
		if($(_this).val()){
			if(before && before.val()){
				if($(_this).val() * 1 >= before.val() * 1){
					dmCheck.tip(_this,"此项策略值必须比前一项策略值小");
					//$(_this).focus();
					return false;
				}
			}
			if(next && next.val()){
				if($(_this).val() * 1 <= next.val() * 1){
					dmCheck.tip(_this,"此项策略值必须比后一项策略值大");
					//$(_this).focus();
					return false;
				}
			}
		}
		return true;
	}
	
	function checkBuyAmountNum(_this,eleNum){
		var before=$("#buyAmount"+(eleNum-1));
		var next=$("#buyAmount"+(eleNum+1));
		if($(_this).val()){
			if(before && before.val()){
				if($(_this).val() * 1 >= before.val() * 1){
					dmCheck.tip(_this,"此项购买限制值必须比前一项购买限制值小");
					//$(_this).focus();
					return false;
				}
			}
			if(next && next.val()){
				if($(_this).val() * 1 <= next.val() * 1){
					dmCheck.tip(_this,"此项购买限制值必须比后一项购买限制值大");
					//$(_this).focus();
					return false;
				}
			}
		}
		return true;
	}
	
	function checkTurnAmountNum(_this,eleNum){
		var before=$("#turnAmount"+(eleNum-1));
		var next=$("#turnAmount"+(eleNum+1));
		if($(_this).val()){
			if(before && before.val()){
				if($(_this).val() * 1 >= before.val() * 1){
					dmCheck.tip(_this,"此项转入限制值必须比前一项转入限制值小");
					//$(_this).focus();
					return false;
				}
			}
			if(next && next.val()){
				if($(_this).val() * 1 <= next.val() * 1){
					dmCheck.tip(_this,"此项转入限制值必须比后一项转入限制值大");
					//$(_this).focus();
					return false;
				}
			}
		}
		return true;
	}
	
	 /**
     * 校验买入限制/转入限制
     */
	function checkSendLimit(){
    	var _this=this;
        var  flag= true;
        var buyFlag= true;
        var turnFlag= true;
	    $(".buyAmount").each(function(i,one){
	    	buyFlag=_this.checkBuyAmountNum($(one),i);
	    	if(!buyFlag){
	        	$(one).focus();
	        	flag = false;
	        	return false;
	        }
	    	var buyUl =  $("#buyUl"+i).find("li");
	    	var buyStableUl =  $("#buyStableUl"+i).find("li");
	    	if($(one).val() != '' && buyUl.length == 0 && buyStableUl.length == 0){
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
    			flag = false;
	        	return false;
	    	}else if($(one).val() == '' && (buyUl.length > 0 || buyStableUl.length > 0)){
	    		$(one).focus();
	    		dmCheck.tip($(one),"购买金额限制不能为空");
	    		flag = false;
	        	return false;
	    	}
	    	
	    	turnFlag=_this.checkTurnAmountNum($("#turnAmount"+i),i);
            if(!turnFlag){
            	$("#turnAmount"+i).focus();
            	flag = false;
            	return false;
            }
            
            var turnUl =  $("#turnUl"+i).find("li");
            var turnStableUl =  $("#turnStableUl"+i).find("li");
            if($("#turnAmount"+i).val()!='' && turnUl.length == 0 && turnStableUl.length == 0){
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
    			flag = false;
	        	return false;
	    	}else if($("#turnAmount"+i).val() == '' && (turnUl.length > 0 || turnStableUl.length > 0)){
	    		$("#turnAmount"+i).focus();
	    		dmCheck.tip($("#turnAmount"+i),"转入金额限制不能为空");
	    		flag = false;
	        	return false;
	    	}
            
            if(buyUl.length == 0 && turnUl.length == 0  && turnStableUl.length == 0 && buyStableUl.length == 0){
				dm.confirm("发放策略不能为空!",{
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
				flag = false;
				return false;
			}
	    	
        });
	   
        return flag;
    }
	
	/**
	 * 活动审核、作废、结束
	 */
	function activityAudit(id,status){
		var _this=this;
		if(dmCheck.check("#boxForm")){
			var approveOpinion=$("#approveOpinion").val();
			DMGold.ajax({
				url : "activityAudit.do",
				data : {id:id,activityStatus:status,approveOpinion:approveOpinion,token:$("#token").val()},
				async:true,
				success : function(data) {
					//关闭弹框
					$.tbox.close();
					if (data.code == '000000') {
						_this.getActivityList();
						dm.confirm("操作成功!",{
		    				title:"提示",				//标题
		    				picClass:"d_succeed",
		    				okName:"确定",			//如同confirm确定按钮的value
		    				showClose:false,
		    				showCancel:false,
		    				cancleName:"取消",		//如同confirm取消按钮的value
		    				callback:function(){	//按确定的执行的函数
		    					dm.closeConfirm();
		    				}
		    			});
					}else if("600001"==data.code){
						//重复提交不做处理
					}else{
						dm.confirm("操作失败!",{
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
					}
				}
			});
		}
	}
	
	/**
	 * 修改活动类型
	 */
	function changeActType(){
		$("#ruleDiv").empty();
		$("#addRuleDiv").empty();
		$("#userDiv").empty();
		$("#sourceDiv").empty();
		getActRuleTmplList();
		var activityType=$("[name='activityType']").val();
		if(activityType == '6'){
			$('#userTmpl').tmpl().appendTo("#userDiv");
		}
		
		if(activityType == '2' || activityType == '4' || activityType == '5' || activityType == '8'){
			$('#sourceTmpl').tmpl().appendTo("#sourceDiv");
		}
	}
	
	/**
	 * 增加充值有奖活动规则
	 */
	function addRuleDiv(){
		if (parseInt(limitRuleDivNum) < 5) {
			//$("#deleteDiv"+ruleDivNum).hide();
			ruleDivNum = ruleDivNum * 1 + 1;
			limitRuleDivNum = limitRuleDivNum + 1;
			getActRuleTmplList();
		}else{
			dm.confirm("活动规则最多只能添加6个!",{
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
		}
	}
	
    /**
     * 充值活动检查发放策略
     */
	function checkCon(){
    	var _this=this;
        var flag = true;
        var amountFlag= true;
	    $(".chargeCon").each(function(i,one){
            $(".chargeCon").each(function(j,two){
                if($(one).val() * 1  == $(two).val() * 1 && $(one).attr("name") != $(two).attr("name")){
                    flag = false;
                }
            });
            amountFlag=_this.checkAmountNum($(one),i);
            if(!amountFlag){
            	$(one).focus();
            	return false;
            }
        });
	    
	    if(amountFlag){
	    	if (!flag){
	            dm.confirm("不能设置相同的发放策略!",{
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
	        }
	    }else{
	    	flag = false;
	    }
        return flag;
    }
	
	/**
	 * 移除充值有奖活动规则
	 */
    function deleteRuleDiv(divId){
    	limitRuleDivNum=limitRuleDivNum*1-1;
		$("#"+divId).remove();
		//显示上一个删除按钮
		/*if(ruleDivNum != 0){
			$("#deleteDiv"+ruleDivNum).show();
		}*/
	}
    
    /**
	 * 增加用户弹框
	 */
    function addUserBox(){
		var urlstr="userListPage.do";
		$.tbox.popup(urlstr);
	}
    
    /**
	 * 全选
	 */
	function chooseAllUser(_this){
		if($(_this).is(':checked')){
			$("[name = 'loginNameBox']").attr("checked", true);
		}else{
			$("[name = 'loginNameBox']").attr("checked", false);
		}
		var selectnNum = $("[name = 'loginNameBox']:checked").length;
		$("#selectnNum").text(selectnNum);
	}
	
	/**
	 * 获取用户列表
	 */
	function getUserList(){
		var _self = this;
		DMGold.ajax({
			url : "userListAjax.do",
			async:true,
			data : $("#userForm").serialize(),
			success : function(data) {
				if (data.code == '000000') {
					_self.showUserList(data);
				}
			}
		});
	}
	
	/**
	 * 显示列表数据
	 */
	function showUserList(data){
		var _self = this;
		$("#userList").empty();
		$("#checkAllUserBox").attr("checked", false);
		$("#selectnNum").text("0");
		//翻页时将已选中数字改为0
		$("#selectnNum").text(0);
		 //填充数据
		 $('#userTempl').tmpl({object: data.data.pageResult}).appendTo("#userList");
		 //初始化分页标签
		 DMGold.PageTags.init({
			 divId: "userPaging", //放入分页的div  的id
			 formId: "userForm", //表单id
			 curPage: data.data.pageResult.pageIndex, //当前页
			 totalCount: data.data.pageResult.recordCount,//总记录数
			 pageCount: data.data.pageResult.pageTotal,//总页数
			 showPages: 5, //显示记录数
			 url: basePath + "operations/userListAjax.do", //请求路径
			 toPageCallBack: function (data) { //返回函数
				 _self.showUserList(data);
			 }
		 });
	}

	/**
	 * 选中用户点击事件
	 */
	function checkUserId(){
		var num = $("[name = 'loginNameBox']:checked").length;
		$("#selectnNum").text(num);
	}
	
	/**
	 * 增加选中用户
	 */
	function addSelectUser(){
		var userIdVal=$("#userName").val();
		$("input[name='loginNameBox']").each(function(index,ele) {
			if($(this).is(":checked")){
				var userId=$(this).val();
				if(userIdVal == ""){
					userIdVal=userId;
				}else{
					userIdVal = userIdVal + "," + userId;
				}
			}
        });
		
		if(userIdVal == ""){
			dm.confirm("请选择用户!",{
				title:"提示",				//标题
				picClass:"d_doubt",
				okName:"确定",			//如同confirm确定按钮的value
				showClose:true,
				showCancel:true,
				cancleName:"取消",		//如同confirm取消按钮的value
				callback:function(){	//按确定的执行的函数
					dm.closeConfirm();
				}
			});
		}else{
			$.tbox.close();
			$("#userName").val(userIdVal);
		}
	}
    
    /**
	 * 选择产品弹框
	 */
	function chooseProductBox(type,ruleNum){
		var ids = new Array(); 
		if(type == 'BUY'){
			$(".buyProduct"+ruleNum).each(function(index,ele){
				var id=$(this).val();
				ids[index]=id;
			});
		}else if(type == 'TURN'){
			$(".turnProduct"+ruleNum).each(function(index,ele){
				var id=$(this).val();
				ids[index]=id;
			});
		}
		
		$("#rewardTmpl5").tmpl({type:type,ids:ids,ruleNum:ruleNum}).appendTo("#productBoxDiv");
		findActProductList();
	}
	
	/**
	 * 选择产品弹框
	 */
	function chooseStableProductBox(type,ruleNum){
		var ids = new Array(); 
		if(type == 'BUY'){
			$(".buyDueTimeProduct"+ruleNum).each(function(index,ele){
				var id=$(this).val();
				ids[index]=id;
			});
		}else if(type == 'TURN'){
			$(".turnDueTimeProduct"+ruleNum).each(function(index,ele){
				var id=$(this).val();
				ids[index]=id;
			});
		}
		
		$("#rewardTmpl8").tmpl({type:type,ids:ids,ruleNum:ruleNum}).appendTo("#productBoxDiv");
		findActProductList();
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
			url:basePath+"activity/findActProductList.do",
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
	        url:basePath+"activity/findActProductList.do",  //请求路径
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
		var ruleNum = $("#ruleNum").val();
		var productNum = $("#productNum").val()*1;
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
				$("#rewardTmpl4").tmpl({id:productId,productName:productName,boxType:boxType,ruleNum:ruleNum,productNum:productNum}).appendTo("#"+tagert+ruleNum);
				productNum = productNum + 1;
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
		var productId="";
		var productName="";
		var boxType = $("#boxType").val();
		var tagert="";
		var ruleNum = $("#ruleNum").val();
		var productNum = $("#productNum").val()*1;
		if(boxType == 'BUY'){
			tagert = "buyStableUl";
		}else{
			tagert = "turnStableUl";
		}
		$("[name='productIds']").each(function(){
			if($(this).is(':checked')){
				flag =  true;
				dueTime = $(this).attr("dueTime");
				$("#rewardTmpl7").tmpl({dueTime:dueTime,boxType:boxType,ruleNum:ruleNum,productNum:productNum}).appendTo("#"+tagert+ruleNum);
				productNum = productNum + 1;
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
	 * 增加奖励弹框
	 */
    function addRewardBox(divId,num){
		divIdS = divId;
		var rewardCnt = $("#" + divId).find(".reward-list-id").length;
		if(rewardCnt >= 10)
		{
			dm.confirm("奖励数量不能超过10个!",{
				title:"提示",				//标题
				picClass:"d_doubt",
				okName:"确定",			//如同confirm确定按钮的value
				showClose:false,
				showCancel:false,
				cancleName:"取消",		//如同confirm取消按钮的value
				callback:function(){	//按确定的执行的函数
					dm.closeConfirm();
				}
			});
			return;
		}
		var _this=this;
		var urlstr="/Manage_Operations/rewardlistpage";
		$.tbox.popup(urlstr);
	}
    
    /**
	 * 全选
	 */
	function chooseAllReward(_this){
		if($(_this).is(':checked')){
			$("[name = 'rewardId']").attr("checked", true);
		}else{
			$("[name = 'rewardId']").attr("checked", false);
		}
		var selectnNum = $("[name = 'rewardId']:checked").length;
		$("#selectnNum").text(selectnNum);
	}
	
	/**
	 * 获取奖励列表
	 */
	function getRewardList(){
		var _self = this;
		DMGold.ajax({
			url : "/Manage_Yingxiao/rewardListAjax",
			async:true,
			data : $("#rewardForm").serialize(),
			success : function(data) {
				if (data.code == '000000') {
					_self.showRewardList(data);
				}
			}
		});
	}
	
	/**
	 * 显示列表数据
	 */
	function showRewardList(data){
		var _self = this;
		$("#checkAllRewardBox").attr("checked", false);
		$("#selectnNum").text("0");
		
		$("#rewardList").empty();
		//翻页时将已选中数字改为0
		$("#selectnNum").text(0);
		if (data.data) {
 			var list = data.data.pageResult.list;
 			for ( var index in list) {
 				//状态重命名
 				list[index].rewardTypeName = {};
 				 //(状态)枚举数据
 				list[index].rewardTypeName = _self.rewardTypeEnum[list[index]["rewardType"]];
 			}
 		 }
		 //填充数据
		 $('#rewardTempl').tmpl({list:data.data.pageResult.list,object: data.data.pageResult}).appendTo("#rewardList");
		 //初始化分页标签
		 DMGold.PageTags.init({
			 divId: "rewardPaging", //放入分页的div  的id
			 formId: "rewardForm", //表单id
			 curPage: data.data.pageResult.pageIndex, //当前页
			 totalCount: data.data.pageResult.recordCount,//总记录数
			 pageCount: data.data.pageResult.pageTotal,//总页数
			 showPages: 10, //显示记录数
			 url: "/Manage_Yingxiao/rewardListAjax", //请求路径
			 toPageCallBack: function (data) { //返回函数
				 _self.showRewardList(data);
			 }
		 });
	}
	
	/**
	 * 选中复选框
	 */
	function checkRewardId(_this){
		var rewardCnt = $("#" + divIdS).find(".reward-list-id").length;
		var selectnNum=parseInt($("#selectnNum").text());

		if($(_this).is(":checked")){
			if ((selectnNum+rewardCnt) >= 10){
				//$("input[name='rewardId']").attr("disabled",true);
				$(_this).attr("checked",false);
				return;
			}
			$("#selectnNum").text(selectnNum+1);
		}else{
			$("#selectnNum").text(selectnNum-1);
		}
	}

	/**
	 * 添加所选奖项
	 */
	function addSelectReward(divId,no){
		divIdS = divId;
		var rewardNum=0;
		var data="";
		var flag=true;
		$("input[name='rewardId']").each(function() {
			var newRewardId=$(this).val();
			if($(this).is(":checked")){
				// 判断新增的奖励是否跟已有的重复
				/*if($("#"+divId).html() != "" && $("#"+divId).html() != null) {
					$("#" + divId).find(".reward-list-id").each(function () {
						var oldRewardId = $(this).val();
						if (newRewardId == oldRewardId) {
							flag = false;
						}
					});
				}*/
				
				//如果number!=0则用&连接后面内容
				if(rewardNum != 0){
					data=data+"&id["+rewardNum+"]="+newRewardId;
				}else{
					data="id[0]="+newRewardId;
				}
				rewardNum=rewardNum+1;
			}
        });
		if(rewardNum > 0){
			var rewardCnt = $("#" + divId).find(".reward-list-id").length;
			if ((rewardCnt + rewardNum) > 10){
				dm.confirm("奖励数量不能超过10个!",{
					title:"提示",				//标题
					picClass:"d_doubt",
					okName:"确定",			//如同confirm确定按钮的value
					showClose:false,
					showCancel:false,
					cancleName:"取消",		//如同confirm取消按钮的value
					callback:function(){	//按确定的执行的函数
						dm.closeConfirm();
					}
				});
			}else {
				DMGold.ajax({
					url: "/Manage_Yingxiao/rewardListAjax",
					data: data,
					success: function (data) {
						$.tbox.close();
						if (data.code == '000000') {
							//记录当前活动规则下有几个活动奖励
							var rewardNum = parseInt($("#rewardNum" + no).val());
							$("#rewardTmpl").tmpl({
								list: data.data.pageResult.list,
								num: no,
								rewardNum: rewardNum
							}).appendTo("#" + divId);
							rewardNum = rewardNum + data.data.pageResult.list.length;
							$("#rewardNum" + no).val(rewardNum);
						}else{
							dm.confirm(data.description,{
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
						}
					}
				});
			}
		}else{
			dm.confirm("请选择奖励！",{
				title:"提示",				//标题
				picClass:"d_doubt",
				okName:"确定",			//如同confirm确定按钮的value
				showClose:false,
				showCancel:false,
				cancleName:"取消",		//如同confirm取消按钮的value
				callback:function(){	//按确定的执行的函数
					dm.closeConfirm();
				}
			});
		}
		
	}
	
	/**
	 * 删除奖励
	 */
	function deleteReward(_this,numId){
		$(_this).parents("li:first").remove();
	}
	
	/**
	 * 校验奖励不能为空
	 */
	function checkRewardNum(){
		var flag=true;
		$(".reward-list-div").each(function() {
			var len=$(this).find(".culRewardNum").length;
			if(len == 0){
				flag=false;
			}
        });
		if(!flag){
			dm.confirm("请选择奖励！",{
				title:"提示",				//标题
				picClass:"d_doubt",
				okName:"确定",			//如同confirm确定按钮的value
				showClose:false,
				showCancel:false,
				cancleName:"取消",		//如同confirm取消按钮的value
				callback:function(){	//按确定的执行的函数
					dm.closeConfirm();
				}
			});
		}
		return flag;
	}
	
	/**
	 * 新增活动
	 */
	function activityAdd(){
		//校验表单
		if(dmCheck.check("#addForm") && checkRewardNum() && checkCon() && checkSendLimit()){
			var activityType=$("[name='activityType']").val();
			// 指定用户类型则判断用户名是否存在
			if(activityType == '6'){
				if(!checkUserName()){
					return;
				}else{
					//去掉最后一个";"号
					var receivers=$("#userName").val().trim().replace(/[\r\n]/g,"");
					if(receivers.charAt(receivers.length - 1)==';'){
						receivers=receivers.substring(0,receivers.length-1);
					}
					// 去重
					var names=receivers.split(";");
					var tmp = new Array();
					for(var i=0; i<names.length;i++){
			            //该元素在tmp内部不存在才允许追加
			            if(tmp.indexOf(names[i])==-1){
			                tmp.push(names[i]);
			            }
			        }
			        $("#userName").val(tmp.join(";"));
				}
			}
			DMGold.ajax({
				url : "/Manage_Operations/activityAdd",
				async:true,
				data :$("#addForm").serialize(),
				success : function(data) {
					if (data.code == '000000') {
						dm.confirm("新增成功！",{
							title:"提示",				//标题
							picClass:"d_succeed",
							okName:"确定",			//如同confirm确定按钮的value
							showClose:false,
							showCancel:false,
							cancleName:"取消",		//如同confirm取消按钮的value
							callback:function(){	//按确定的执行的函数
								location.href = "activityListPage";
							}
						});
					}else{
						dm.confirm(data.description,{
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
					}
				}
			});
		}
	}
	/**
	 * 修改活动
	 */
	function activityEdit(){
		//校验表单
		if(dmCheck.check("#editForm") && checkRewardNum() && checkCon() && checkSendLimit()){
			var activityType=$("[name='activityType']").val();
			// 指定用户类型则判断用户名是否存在
			if(activityType == '6'){
				if(!checkUserName()){
					return;
				}else{
					//去掉最后一个";"号
					var receivers=$("#userName").val().trim().replace(/[\r\n]/g,"");
					if(receivers.charAt(receivers.length - 1)==';'){
						receivers=receivers.substring(0,receivers.length-1);
					}
					// 去重
					var names=receivers.split(";");
					var tmp = new Array();
					for(var i=0; i<names.length;i++){
			            //该元素在tmp内部不存在才允许追加
			            if(tmp.indexOf(names[i])==-1){
			                tmp.push(names[i]);
			            }
			        }
			        $("#userName").val(tmp.join(";"));
				}
			}
			DMGold.ajax({
				url : "activityEdit.do",
				async:true,
				data :$("#editForm").serialize(),
				success : function(data) {
					if (data.code == '000000') {
						dm.confirm("修改成功！",{
							title:"提示",				//标题
							picClass:"d_succeed",
							okName:"确定",			//如同confirm确定按钮的value
							showClose:false,
							showCancel:false,
							cancleName:"取消",		//如同confirm取消按钮的value
							callback:function(){	//按确定的执行的函数
								location.href = "activityListPage.do";
							}
						});
					}else{
						dm.confirm(data.description,{
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
					}
				}
			});
		}
	}
	
	/**
	 * 校验用户名
	 */
	function checkUserName(){
		//校验为空操作：
		if(dmCheck.checkOne($("#userName"))){
			var flag = true;
			// 将回车换行替换成空字符
			var arr=$("#userName").val().trim().replace(/[\r\n]/g,"");
			if(arr.charAt(arr.length - 1)==','){//A;B;|A;B
				arr=arr.substring(0,arr.length-1);//A;B
			}
			arr=arr.split(",");
			//alert(arr.length);
			for(var i=0;i<arr.length;i++){//如果输入一个,校验是否存在（暂时：如果多个，一一校验）
				if(arr[i].trim().length>0){
					DMGold.ajax({
			            url:"isExsitOfUser.do",
			            data:{"loginName":arr[i].trim()},
			            async: false,
			            error: function() {
							dm.confirm("检验用户名是否存在失败！",{
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
			            },
			            success: function(data) {
			            	if(data.code != '000000'){
				            	flag = false;
				            	dmCheck.tip($("#userName"),"用户名"+arr[i]+"不存在！");
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
	       			dm.confirm("只支持txt文件！",{
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
	       			  return;
	       		}
	       		text = text.replace(';\r\n',/;/g);
	       		var arr=$("#userName").val().trim().replace(/[\r\n]/g,"");
	       		if(arr){
	       			if(arr.charAt(arr.length - 1)==';'){//A;B;|A;B
						$("#userName").val(arr+text);
					}else{
						$("#userName").val(arr+";"+text);
					}
	       		}else{
	       			$("#userName").val(text);
	       		}
				
	       		
            },
            error: function (data, status, e)//服务器响应失败处理函数
            {
                dm.confirm("导入异常，请联系管理员！",{
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
            }
        });
	  }
	 
    /**
	 * 增加用户来源弹框
	 */
    function addSourceBox(){
    	var sources = new Array(); 
		$(".sourceList").each(function(index,ele){
			var id=$(this).val();
			sources[index]=id;
		});
    	 $('#rewardTmpl9').tmpl({sources:sources}).appendTo("#productBoxDiv");
    	 getSourceList();
	}
    
    /**
	 * 全选
	 */
	function chooseAllSource(_this){
		if($(_this).is(':checked')){
			$("[name = 'singleSourceBox']").attr("checked", true);
		}else{
			$("[name = 'singleSourceBox']").attr("checked", false);
		}
		var selectnNum = $("[name = 'singleSourceBox']:checked").length;
		$("#checkedNum").text(selectnNum);
	}
	
	/**
	 * 选中用户点击事件
	 */
	function checkSingleSource(){
		var num = $("[name = 'singleSourceBox']:checked").length;
		$("#checkedNum").text(num);
	}
	
	/**
	 * 获取用户列表
	 */
	function getSourceList(){
		var _self = this;
		DMGold.ajax({
			url : "findWebSiteSourceList.do",
			async:true,
			data : $("#sourceForm").serialize(),
			success : function(data) {
				if (data.code == '000000') {
					_self.showSourceList(data);
				}
			}
		});
	}
	
	/**
	 * 显示列表数据
	 */
	function showSourceList(data){
		var _self = this;
		$("#sourceList").empty();
		$("#chooseAllSourceBox").attr("checked", false);
		//翻页时将已选中数字改为0
		$("#checkedNum").text(0);
		 //填充数据
		 $('#rewardTmpl10').tmpl(data.data.pageResult).appendTo("#sourceList");
		 //初始化分页标签
		 DMGold.PageTags.init({
			 divId: "sourcePage", //放入分页的div  的id
			 formId: "sourceForm", //表单id
			 curPage: data.data.pageResult.pageIndex, //当前页
			 totalCount: data.data.pageResult.recordCount,//总记录数
			 pageCount: data.data.pageResult.pageTotal,//总页数
			 showPages: 5, //显示记录数
			 url: basePath + "operations/findWebSiteSourceList.do", //请求路径
			 toPageCallBack: function (data) { //返回函数
				 _self.showSourceList(data);
			 }
		 });
	}

	
	/**
	 * 增加选中注册来源
	 */
	function addSelectSource(){
		var flag = true;
		var sourceNum = $("#sourceNum").val() * 1;
		$("input[name='singleSourceBox']").each(function(index,ele) {
			if($(this).is(":checked")){
				$('#rewardTmpl11').tmpl({name: $(this).val(),sourceNum:sourceNum}).appendTo("#sourceUl");
				sourceNum=sourceNum+1;
				flag=false;
			}
        });
		
		if(flag){
			dm.confirm("请选择注册来源!",{
				title:"提示",				//标题
				picClass:"d_doubt",
				okName:"确定",			//如同confirm确定按钮的value
				showClose:true,
				showCancel:true,
				cancleName:"取消",		//如同confirm取消按钮的value
				callback:function(){	//按确定的执行的函数
					dm.closeConfirm();
				}
			});
		}else{
			$("#productBoxDiv").empty();
		}
	}
    



		




