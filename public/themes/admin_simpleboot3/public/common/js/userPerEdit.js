
 
  
  //判断手机号是否存在
  function isExistPhone(phone){
	  var isExist=false;
	  if(phone){
		  $.ajax({
			    async:false,
				type:"POST",
				url:,
				data:{"phone":phone,"userId":key,"type":'PERSONAL'},
				success:function(msg){
					if(msg.flag.code == '000000'){
						if(msg.flag.data == 'Y'){
							$("#userPhone").focus();
							dmCheck.tip($("#userPhone"),"手机号已存在");
		            		isExist=true;
						}
					}else{
						dm.alert(msg.flag.description);
						isExist=true;
					}
				}
			});
	  }
	  return isExist;
  }
  //判断邮箱是否存在
  function isExistEmail(email){
	  var isExist=false;
	  if(email){
		  $.ajax({
			    async:false,
				type:"POST",
				url:basePath+"shop/isExistEmail.do",
				data : {"email" : email,"userId" : key},
				success : function(msg) {
					if (msg.flag.code == '000000') {
						if (msg.flag.data == 'Y') {
							$("#userEmail").focus();
							dmCheck.tip($("#userEmail"),"邮箱已存在");
							isExist = true;
						}
					} else {
						dm.alert(msg.flag.description);
						isExist = true;
					}
				}
			});
		}
		return isExist;
	}