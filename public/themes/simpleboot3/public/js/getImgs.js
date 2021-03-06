 
//url由你自己设置，我将原本的url删掉了，换上你自己要上传的地址
var url = 'http://weixin.qudong.com/api/upload/upload';

var task;
var photos = [];
var this_phoneNum = localStorage.getItem('phoneNum');

var finish = document.getElementById('finish');
var max = 9; //照片的最大数目
var photosNum;
var camera_photos = [];
var gallery_photos = [];

function createUploader() {
	task = plus.uploader.createUpload(url, {
		method: 'POST'
	}, function(data, status) {
		if(status == 200) {
			plus.nativeUI.closeWaiting();
			var page = plus.webview.getWebviewById('view/dynamics/dynamics.html');
			mui.fire(page, 'refresh', {});
			mui.openWindow({
				id: 'new_file.html'
			});
		} else {
			mui.alert(status);
		}
	});
//mui('header').on('tap','#finish',function(){
//	
//	var files;
//	var content = document.getElementById('content').value;
//	var token=localStorage.getItem('token');
//	var len = photos.length;
//	var img=photos;
//	var length=img.length;
//	console.log(length);
////	img=img.substr(10,153);
//	console.log(content);
//	console.log(JSON.stringify(img[0]));
//		mui.post(url,{content:content,token:token,img:img},function(e){
//		console.log(895);
//		console.log(img);
//		if(e.code==200){
//			mui.toast(e.msg);
//		}else{
//			mui.toast(e.msg);
//		}
//	}) 	
//	
//	
//})
//	
//		
//}

finish.addEventListener('tap', function() {
	var files;
	var content = document.getElementById('content').value;
	var token='MTErMTc3ODM0NTIzMDUrZTM1Y2Y3YjY2NDQ5ZGY1NjVmOTNjNjA3ZDVhODFkMDk=';
//		var img = document.getElementById('image').getAttribute('src');
	var len = photos.length;
	task.addData('token',token);
//	task.addData('objectID', this_phoneNum);
	task.addData('content', content); 
//	task.addData('num', '' + len);
	console.log(len);
	var s=JSON.stringify(photos);
	console.log(s);
	console.log(content);
	console.log(token);
	for(var i = 0; i < len; i++) {
		var j = i + 1;
		var temp = 'phone' + j;
		task.addFile(photos[i].path, {
			key: temp
		});
	}
	task.start();
	plus.nativeUI.showWaiting();
});

//从相机中选取图片
function clickCamera() {
	var c = plus.camera.getCamera();
	c.captureImage(function(e) {
		plus.io.resolveLocalFileSystemURL(e, function(entry) {

			var path = entry.toLocalURL();
			var name = path.substr(e.lastIndexOf('/') + 1);

			//压缩图片到内存
			plus.zip.compressImage({
				src: path,
				dst: '_doc/' + path,
				quality: 20,
				overwrite: true
			}, function(zip) {
				camera_photos.push({
					path: zip.target
				});
				photos.push({
					path: zip.target
				});
				showPhotos();
			}, function(error) {
				console.log("压缩error");
			});

		}, function(e) {
			mui.toast("读取拍照文件错误" + e.message);
		});
	})
};

//确定还可以从相册中选择照片的最大数目  
var galleryPhotoNum;
var galleryFiles;

function clickGallery() {
	galleryPhotoNum = max - camera_photos.length;
	plus.gallery.pick(function(path) {
			galleryFiles = path.files; 
			plus.nativeUI.showWaiting();
		    compressImg(galleryFiles, 0);
		},
		function(e) {
			console.log("获取照片失败");
		}, {
			filter: "image",
			multiple: true,
			maximum: galleryPhotoNum,
			system: false,
			onmaxed: function() {
				mui.toast('最多选' + galleryPhotoNum + '个');
			},
			popover: true,
			selected: galleryFiles
		});
}
//递归压缩图片
function compressImg(files, file_index) {
	var file_length = files.length;
	var path = files[file_index];
	plus.zip.compressImage({
		src: path,
		dst: '_doc/' + path,
		quality: 20,
		overwrite: true
	}, function(zip) {
		var next_file_index = file_index + 1;
		if(file_index == 0) {
			gallery_photos = [];
		}
		gallery_photos.push({
			path: zip.target
		});
		addPhoto(zip.target, file_index);
		if(next_file_index < file_length) {
			compressImg(files, next_file_index);
		} else {
			showPhotos();
		}
	})
}

function addPhoto(imgPath, index) {
	if(index == 0) {
		photos = [];
		for(var i = 0; i < camera_photos.length; i++) {
			photos.push({
				path: camera_photos[i].path
			});
		}
	}
	photos.push({
		path: imgPath
	});
}

function showPhotos() {
	var table = document.body.querySelector('#photos');
	var len = photos.length; 
	if(len > max) {
		len = max;
	}
	table.innerHTML = "";
	for(var i = 0; i < len; i++) {
		var img = document.createElement('img');
		img.src = photos[i].path;
		table.appendChild(img)
	}
	plus.nativeUI.closeWaiting();
	if(len == max) {
		document.getElementById('pick').style.display = 'none';
	}
}
function getBase64Image(img) { 
            var canvas = document.createElement("canvas"); 
            var width = img.width; 
            var height = img.height; 
            // calculate the width and height, constraining the proportions 
            if (width > height) { 
                if (width > 100) { 
                    height = Math.round(height *= 100 / width); 
                    width = 100; 
                } 
            } else { 
                if (height > 100) { 
                    width = Math.round(width *= 100 / height); 
                    height = 100; 
                } 
            } 
            canvas.width = width;   /*设置新的图片的宽度*/ 
            canvas.height = height; /*设置新的图片的长度*/ 
            var ctx = canvas.getContext("2d"); 
            ctx.drawImage(img, 0, 0, width, height); /*绘图*/ 
            var dataURL = canvas.toDataURL("image/png", 0.8); 
            return dataURL.replace("data:image/png;base64,", ""); 
        }    
            