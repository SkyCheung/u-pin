(function($) {
	$(function() {
		var uploadDir='',is_thumb=0;
		var filePicker = $(".filePicker");
		if (filePicker.siblings("#uploadDir").length==1) {
			//uploadDir=filePicker.siblings("#uploadDir").val();
		}
		if ($("#is_thumb")!=undefined) {
			is_thumb=$("#is_thumb").val();
		}
		
		//单个图片
		var uploader =WebUploader.create({
			auto: true,
			multiple: false,
			pick: '.filePicker',
			formData: {uploadDir:filePicker.data("uploadDir"),is_thumb:is_thumb},
			accept: {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/*'
            },
            pickerid: filePicker.prop("id"),
			server: '/admin.html?do=fileupload'
		})
		.on('fileQueued', function(file) {
			var start = +new Date();
			/*uploader.option('formData', {
    			uploadDir:'goods', is_thumb:is_thumb
			});	*/
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
				$("#img").val(res.data.img);
				if (res.data.thumb!='') {
					$("#thumb").val(res.data.thumb);
				}						 
				filePicker.children("div").eq(0).html($img);	
				var txt= $.trim(filePicker.prop("title"));
				if (txt !='') {
					filePicker.children("div").eq(0).append('<label class="txt">'+txt+'</label>');
				}				
				}, 150, 150);
			}    		
		});
		
		
		//上传图片列表
		var multuploadDir='';
		var listfilePicker = $(".listfilePicker");
		if (listfilePicker.siblings("#uploadDir").length==1) {
			multuploadDir=listfilePicker.siblings("#uploadDir").val();
		}
		var multuploader =WebUploader.create({
			auto: true,
			pick: '.listfilePicker', 
			formData: {uploadDir:uploadDir},
			accept: {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,png',
                 mimeTypes: 'image/*'
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