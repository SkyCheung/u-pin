(function($) {
	$(function() {
		
		//上传图片列表
		var multuploadDir='';
		var listfilePicker = $(".listfilePicker");
		if (listfilePicker.siblings("#uploadDir").length==1) {
			multuploadDir=listfilePicker.siblings("#uploadDir").val();
		}
		var multuploader = WebUploader.create({
			auto: true,
			pick: '.listfilePicker', 
			formData: {uploadDir:multuploadDir},
			accept: {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png'
            },
			server: '/admin.html?do=fileupload'
		}).on('fileQueued', function(file) {
			var start = +new Date();

			// 返回的是 promise 对象
			this.md5File(file, 0, 1 * 1024 * 1024)

			// 可以用来监听进度
			.progress(function(percentage) {
				// console.log('Percentage:', percentage);
			})

			// 处理完成后触发
			.then(function(ret) {
				// console.log('md5:', ret);
				//var end = +new Date();
			});				
		}).on('uploadAccept', function(obj, res) {
			if (res.err!='') {
    			error(res.err);	
    		}
			else
			{ 
				multuploader.makeThumb(obj.file, function(error, src) {
					listfilePicker.parent(".it").before('<div class="it"><i class="i-del"><i><div class="webuploader-pick"><img src="'+src+'"/></div><input type="text" maxlength="100" name="descipt[]" placeholder="描述"/><input type="hidden" value="'+res.data.img+'" name="imgs[]"/></div>');
				}, 150, 150);
			}    		
		});
		
	});
		
})(jQuery);


function com_upload(picker, callback) {
	var filename= picker.data("filename") || ''; //指定文件名
	var uploader = WebUploader.create({
			auto: true,
			multiple: false,
			duplicate: true,
			pick: "#"+picker.prop("id"),
			formData: {uploaddir: picker.data("uploaddir"), is_thumb: picker.data("isthumb"),filename:filename},
			accept: {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png'
            },
			server: '/admin.html?do=fileupload'
		})
		.on('fileQueued', function(file) {
			// 返回的是 promise 对象
			this.md5File(file, 0, 1 * 1024 * 1024);	
		})
		.on('uploadAccept', function(obj, res) {
			if (res.err!='') {
    			error(res.err);	
    		}
			else
			{ 
				if (typeof callback === "function"){
			        callback(res, picker); 
			    }
			}    		
		});	
}

//单文件上传
function upFile(picker) {
		var filename= picker.data("filename") || ''; //指定文件名
		var uploader = WebUploader.create({
			auto: true,
			multiple: false,
			duplicate: true,
			pick: "#"+picker.prop("id"),
			formData: {uploaddir: picker.data("uploaddir"), is_thumb: picker.data("isthumb"),filename:filename},
			accept: {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png'
            },
			server: '/admin.html?do=fileupload'
		})
		.on('fileQueued', function(file) {
			var start = +new Date();
			// 返回的是 promise 对象
			this.md5File(file, 0, 1 * 1024 * 1024)

			// 可以用来监听进度
			.progress(function(percentage) {
				// console.log('Percentage:', percentage);
			})

			// 处理完成后触发
			.then(function(ret) {
				// console.log('md5:', ret);

				var end = +new Date();
				//log('HTML5: md5 ' + file.name + ' cost ' + (end - start) + 'ms get value: ' + ret);
			});	
					
		})
		.on('uploadAccept', function(obj, res) {
			if (res.err!='') {
    			error(res.err);	
    		}
			else
			{ 
				uploader.makeThumb(obj.file, function(error, src) {
				$img = $('<img/>');
				
				if (error) {
					$img.replaceWith('<span>不能预览</span>');
					return;
				}
				$img.attr('src', src);
				picker.siblings("#img").val(res.data.img);
				if (res.data.thumb!='') {
					picker.siblings("#thumb").val(res.data.thumb);
				}						 
				picker.children("div").eq(0).html($img);	
				var txt= $.trim(picker.prop("title"));
				if (txt !='') {
					picker.children("div").eq(0).append('<i class="i-del" data-id="'+res.data.img+'" onclick="del_goodsimg(0,this);"></i><label class="txt">'+txt+'</label>');
				}				
				}, 150, 150);
			}    		
		});		
}

//多文件上传
function upFileList(picker) {
	var uploader = WebUploader.create({
			auto: true,
			multiple: true,
			duplicate: true,
			pick: "#"+picker.prop("id"),
			formData: {uploaddir: picker.data("uploaddir"), is_thumb: picker.data("isthumb")},
			accept: {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png'
            },
			server: '/admin.html?do=fileupload'
		})
		.on('fileQueued', function(file) {
			var start = +new Date();
			// 返回的是 promise 对象
			this.md5File(file, 0, 1 * 1024 * 1024)

			// 可以用来监听进度
			.progress(function(percentage) {
				// console.log('Percentage:', percentage);
			})

			// 处理完成后触发
			.then(function(ret) {
				// console.log('md5:', ret);

				var end = +new Date();
				//log('HTML5: md5 ' + file.name + ' cost ' + (end - start) + 'ms get value: ' + ret);
			});	
					
		})
		.on('uploadAccept', function(obj, res) {
			if (res.err!='') {
    			error(res.err);	
    		}
			else
			{ 
				uploader.makeThumb(obj.file, function(error, src) {
					var pre="",suffix="",thumb="";
					if (picker.data("pre")!=undefined) {
						pre = picker.data("pre")+'_';
					}
					if (picker.data("suffix")!=undefined) {
						suffix = '_'+picker.data("suffix");
					}
					if (picker.data("isthumb")!=undefined) {
						thumb ='<input type="hidden" value="'+res.data.thumb+'" name="'+pre+'thumbs'+suffix+'[]"/>';
					}
					picker.parent(".it").before('<div class="it imgbox"><i class="i-del" data-id="'+res.data.img+'" onclick="del_goodsimg(0,this);"></i><div class="webuploader-pick"><img src="'+src+'"/></div><input type="text" maxlength="100" name="'+pre+'descipt'+suffix+'[]" placeholder="描述"/><input type="hidden" value="'+res.data.img+'" name="'+pre+'imgs'+suffix+'[]"/>'+thumb+'</div>');				
				}, 150, 150);
			}    		
		});	
}