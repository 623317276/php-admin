(function($){
$.fn.Huploadify = function(opts){
	var itemTemp = '<div id="${fileID}" class="uploadify-queue-item"><div class="uploadify-progress"><div class="uploadify-progress-bar"></div></div><!--<div class="img"><img src="/Uploads/${fileName} " style="width: 200px;"/></div><p>--><span class="up_filename"><input name="face180[]" type="hidden"  value="/Uploads/${fileName} ">${fileName}</span><span class="uploadbtn">涓婁紶</span><span class="delfilebtn">鍒犻櫎</span></div>';
	var defaults = {
		fileTypeExts:'',//鍏佽涓婁紶鐨勬枃浠剁被鍨嬶紝鏍煎紡'*.jpg;*.doc'
		uploader:'',//鏂囦欢鎻愪氦鐨勫湴鍧€
		auto:false,//鏄惁寮€鍚嚜鍔ㄤ笂浼�
		method:'post',//鍙戦€佽姹傜殑鏂瑰紡锛実et鎴杙ost
		multi:true,//鏄惁鍏佽閫夋嫨澶氫釜鏂囦欢
		formData:null,//鍙戦€佺粰鏈嶅姟绔殑鍙傛暟锛屾牸寮忥細{key1:value1,key2:value2}
		fileObjName:'file',//鍦ㄥ悗绔帴鍙楁枃浠剁殑鍙傛暟鍚嶇О锛屽PHP涓殑$_FILES['file']
		fileSizeLimit:1024,//鍏佽涓婁紶鐨勬枃浠跺ぇ灏忥紝鍗曚綅KB
		showUploadedPercent:true,//鏄惁瀹炴椂鏄剧ず涓婁紶鐨勭櫨鍒嗘瘮锛屽20%
		showUploadedSize:false,//鏄惁瀹炴椂鏄剧ず宸蹭笂浼犵殑鏂囦欢澶у皬锛屽1M/2M
		buttonText:'<img src="/Public/images/icon_head_small.png" id="imgpic" width="150px"/>',//涓婁紶鎸夐挳涓婄殑鏂囧瓧
		removeTimeout: 1000,//涓婁紶瀹屾垚鍚庤繘搴︽潯鐨勬秷澶辨椂闂�
		itemTemplate:itemTemp,//涓婁紶闃熷垪鏄剧ず鐨勬ā鏉�
		onUploadStart:null,//涓婁紶寮€濮嬫椂鐨勫姩浣�
		onUploadSuccess:null,//涓婁紶鎴愬姛鐨勫姩浣�
		onUploadComplete:null,//涓婁紶瀹屾垚鐨勫姩浣�
		onUploadError:null, //涓婁紶澶辫触鐨勫姩浣�
		onInit:null,//鍒濆鍖栨椂鐨勫姩浣�
		onCancel:null//鍒犻櫎鎺夋煇涓枃浠跺悗鐨勫洖璋冨嚱鏁帮紝鍙紶鍏ュ弬鏁癴ile
	}
		
	var option = $.extend(defaults,opts);
	
	//灏嗘枃浠剁殑鍗曚綅鐢眀ytes杞崲涓篕B鎴朚B锛岃嫢绗簩涓弬鏁版寚瀹氫负true锛屽垯姘歌繙杞崲涓篕B
	var formatFileSize = function(size,byKB){
		if (size> 1024 * 1024&&!byKB){
			size = (Math.round(size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
		}
		else{
			size = (Math.round(size * 100 / 1024) / 100).toString() + 'KB';
		}
		return size;
	}
	//鏍规嵁鏂囦欢搴忓彿鑾峰彇鏂囦欢
	var getFile = function(index,files){
		for(var i=0;i<files.length;i++){	   
		  if(files[i].index == index){
			  return files[i];
			}
		}
		return false;
	}
	
	//灏嗚緭鍏ョ殑鏂囦欢绫诲瀷瀛楃涓茶浆鍖栦负鏁扮粍,鍘熸牸寮忎负*.jpg;*.png
	var getFileTypes = function(str){
		var result = [];
		var arr1 = str.split(";");
		for(var i=0,len=arr1.length;i<len;i++){
			result.push(arr1[i].split(".").pop());
		}
		return result;
	}
	
	this.each(function(){
		var _this = $(this);
		//鍏堟坊鍔犱笂file鎸夐挳鍜屼笂浼犲垪琛�
		var instanceNumber = $('.uploadify').length+1;
		var inputStr = '<input id="select_btn_'+instanceNumber+'" class="selectbtn" style="display:none;" type="file" name="fileselect[]"';
		inputStr += option.multi ? ' multiple' : '';
		inputStr += ' accept="';
		inputStr += getFileTypes(option.fileTypeExts).join(",");
		inputStr += '"/>';
		inputStr += '<a id="file_upload_'+instanceNumber+'-button" href="javascript:void(0)" class="uploadify-button_reg">';
		inputStr += option.buttonText;
		inputStr += '</a>';
		var uploadFileListStr = '<div id="file_upload_'+instanceNumber+'-queue" class="uploadify-queue"></div>';
		_this.append(inputStr+uploadFileListStr);	
		
		
		//鍒涘缓鏂囦欢瀵硅薄
	  var fileObj = {
		  fileInput: _this.find('.selectbtn'),				//html file鎺т欢
		  uploadFileList : _this.find('.uploadify-queue'),
		  url: option.uploader,						//ajax鍦板潃
		  fileFilter: [],					//杩囨护鍚庣殑鏂囦欢鏁扮粍
		  filter: function(files) {		//閫夋嫨鏂囦欢缁勭殑杩囨护鏂规硶
			  var arr = [];
			  var typeArray = getFileTypes(option.fileTypeExts);
			  if(typeArray.length>0){
				  for(var i=0,len=files.length;i<len;i++){
				  	var thisFile = files[i];
				  		if(parseInt(formatFileSize(thisFile.size,true))>option.fileSizeLimit){
				  			alert('鏂囦欢'+thisFile.name+'澶у皬瓒呭嚭闄愬埗锛�');
				  			continue;
				  		}
						if($.inArray(thisFile.name.split('.').pop(),typeArray)>=0){
							arr.push(thisFile);	
						}
						else{
							alert('鏂囦欢'+thisFile.name+'绫诲瀷涓嶅厑璁革紒');
						}  	
					}	
				}
			  return arr;  	
		  },
		  //鏂囦欢閫夋嫨鍚�
		  onSelect: function(files){
				for(var i=0,len=files.length;i<len;i++){
					var file = files[i];
					//澶勭悊妯℃澘涓娇鐢ㄧ殑鍙橀噺
					var $html = $(option.itemTemplate.replace(/\${fileID}/g,'fileupload_'+instanceNumber+'_'+file.index).replace(/\${fileName}/g,file.name).replace(/\${fileSize}/g,formatFileSize(file.size)).replace(/\${instanceID}/g,_this.attr('id')));
					//濡傛灉鏄嚜鍔ㄤ笂浼狅紝鍘绘帀涓婁紶鎸夐挳
					if(option.auto){
						$html.find('.uploadbtn').remove();
					}
					this.uploadFileList.append($html);
					
					//鍒ゆ柇鏄惁鏄剧ず宸蹭笂浼犳枃浠跺ぇ灏�
					if(option.showUploadedSize){
						var num = '<span class="progressnum"><span class="uploadedsize">0KB</span>/<span class="totalsize">${fileSize}</span><p></span>'.replace(/\${fileSize}/g,formatFileSize(file.size));
						$html.find('.uploadify-progress').after(num);
					}
					
					//鍒ゆ柇鏄惁鏄剧ず涓婁紶鐧惧垎姣�	
					if(option.showUploadedPercent){
						var percentText = '<span class="up_percent">0%</span>';
						$html.find('.uploadify-progress').after(percentText);
					}
					//鍒ゆ柇鏄惁鏄嚜鍔ㄤ笂浼�
					if(option.auto){
						this.funUploadFile(file);
					}
					else{
						//濡傛灉閰嶇疆闈炶嚜鍔ㄤ笂浼狅紝缁戝畾涓婁紶浜嬩欢
					 	$html.find('.uploadbtn').on('click',(function(file){
					 			return function(){fileObj.funUploadFile(file);}
					 		})(file));
					}
					//涓哄垹闄ゆ枃浠舵寜閽粦瀹氬垹闄ゆ枃浠朵簨浠�
			 		$html.find('.delfilebtn').on('click',(function(file){
					 			return function(){fileObj.funDeleteFile(file.index);}
					 		})(file));
			 	}

			 
			},				
		  onProgress: function(file, loaded, total) {
				var eleProgress = _this.find('#fileupload_'+instanceNumber+'_'+file.index+' .uploadify-progress');
				var percent = (loaded / total * 100).toFixed(2) +'%';
				if(option.showUploadedSize){
					eleProgress.nextAll('.progressnum .uploadedsize').text(formatFileSize(loaded));
					eleProgress.nextAll('.progressnum .totalsize').text(formatFileSize(total));
				}
				if(option.showUploadedPercent){
					eleProgress.nextAll('.up_percent').text(percent);	
				}
				eleProgress.children('.uploadify-progress-bar').css('width',percent);
	  	},		//鏂囦欢涓婁紶杩涘害

		  /* 寮€鍙戝弬鏁板拰鍐呯疆鏂规硶鍒嗙晫绾� */
		  
		  //鑾峰彇閫夋嫨鏂囦欢锛宖ile鎺т欢
		  funGetFiles: function(e) {	  
			  // 鑾峰彇鏂囦欢鍒楄〃瀵硅薄
			  var files = e.target.files;
			  //缁х画娣诲姞鏂囦欢
			  files = this.filter(files);
			  for(var i=0,len=files.length;i<len;i++){
			  	this.fileFilter.push(files[i]);	
			  }
			  this.funDealFiles(files);
			  return this;
		  },
		  
		  //閫変腑鏂囦欢鐨勫鐞嗕笌鍥炶皟
		  funDealFiles: function(files) {
			  var fileCount = _this.find('.uploadify-queue .uploadify-queue-item').length;//闃熷垪涓凡缁忔湁鐨勬枃浠朵釜鏁�
			  for(var i=0,len=files.length;i<len;i++){
				  files[i].index = ++fileCount;
				  files[i].id = files[i].index;
				  }
			  //鎵ц閫夋嫨鍥炶皟
			  this.onSelect(files);
			  
			  return this;
		  },
		  
		  //鍒犻櫎瀵瑰簲鐨勬枃浠�
		  funDeleteFile: function(index) {
			  for (var i = 0,len=this.fileFilter.length; i<len; i++) {
					  var file = this.fileFilter[i];
					  if (file.index == index) {
						  this.fileFilter.splice(i,1);
						  _this.find('#fileupload_'+instanceNumber+'_'+index).fadeOut();
						  option.onCancel&&option.onCancel(file);	
						  break;
					  }
			  }
			  return this;
		  },
		  
		  //鏂囦欢涓婁紶
		  funUploadFile: function(file) {
			  var xhr = false;
			  try{
				 xhr=new XMLHttpRequest();//灏濊瘯鍒涘缓 XMLHttpRequest 瀵硅薄锛岄櫎 IE 澶栫殑娴忚鍣ㄩ兘鏀寔杩欎釜鏂规硶銆�
			  }catch(e){	  
				xhr=ActiveXobject("Msxml12.XMLHTTP");//浣跨敤杈冩柊鐗堟湰鐨� IE 鍒涘缓 IE 鍏煎鐨勫璞★紙Msxml2.XMLHTTP锛夈€�
			  }
			  
			  if (xhr.upload) {
				  // 涓婁紶涓�
				  xhr.upload.addEventListener("progress", function(e) {
					  fileObj.onProgress(file, e.loaded, e.total);
				  }, false);
	  
				  // 鏂囦欢涓婁紶鎴愬姛鎴栨槸澶辫触
				  xhr.onreadystatechange = function(e) {
					  if (xhr.readyState == 4) {
						  if (xhr.status == 200) {
							  //鏍℃杩涘害鏉″拰涓婁紶姣斾緥鐨勮宸�
							  var thisfile = _this.find('#fileupload_'+instanceNumber+'_'+file.index);
							  thisfile.find('.uploadify-progress-bar').css('width','100%');
								option.showUploadedSize&&thisfile.find('.uploadedsize').text(thisfile.find('.totalsize').text());
								option.showUploadedPercent&&thisfile.find('.up_percent').text('100%');

							  option.onUploadSuccess&&option.onUploadSuccess(file, xhr.responseText);
							  //鍦ㄦ寚瀹氱殑闂撮殧鏃堕棿鍚庡垹鎺夎繘搴︽潯
							  setTimeout(function(){
							  	_this.find('#fileupload_'+instanceNumber+'_'+file.index).fadeOut();
							  },option.removeTimeout);
						  } else {
							  option.onUploadError&&option.onUploadError(file, xhr.responseText);		
						  }
						  option.onUploadComplete&&option.onUploadComplete(file,xhr.responseText);
						  //娓呴櫎鏂囦欢閫夋嫨妗嗕腑鐨勫凡鏈夊€�
						  fileObj.fileInput.val('');
					  }
				  };
	  
	  			option.onUploadStart&&option.onUploadStart();	
				  // 寮€濮嬩笂浼�
				  xhr.open(option.method, this.url, true);
				  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
				  var fd = new FormData();
				  fd.append(option.fileObjName,file);
				  if(option.formData){
				  	for(key in option.formData){
				  		fd.append(key,option.formData[key]);
				  	}
				  }
				  
				  xhr.send(fd);
			  }	
			  
				  
		  },
		  
		  init: function() {	  
			  //鏂囦欢閫夋嫨鎺т欢閫夋嫨
			  if (this.fileInput.length>0) {
				  this.fileInput.change(function(e) { 
				  	fileObj.funGetFiles(e); 
				  });	
			  }
			  
			  //鐐瑰嚮涓婁紶鎸夐挳鏃惰Е鍙慺ile鐨刢lick浜嬩欢
			  _this.find('.uploadify-button_reg').on('click',function(){
				  _this.find('.selectbtn').trigger('click');
				});
			  
			  option.onInit&&option.onInit();
		  }
  	};

		//鍒濆鍖栨枃浠跺璞�
		fileObj.init();
	}); 
}	

})(jQuery)