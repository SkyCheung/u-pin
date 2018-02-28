(function($) {
	$(function() {
		
		//上传图片列表
		var multuploadDir='';
		var listfilePicker = $(".listfilePicker");
		if (listfilePicker.siblings("#uploadDir").length==1) {
			multuploadDir=listfilePicker.siblings("#uploadDir").val();
		}
		var multuploader =WebUploader.create({
			auto: true,
			pick: '.listfilePicker', 
			formData: {uploadDir:multuploadDir},
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


//单文件上传
function upFile(picker, callback) {
		var filename= picker.data("filename") || '';
		var uploader = WebUploader.create({
			auto: true,
			multiple: false,
			duplicate: true,
			pick: "#"+picker.prop("id"),
			formData: {uploadDir: picker.data("uploaddir"), is_thumb: picker.data("isthumb"),filename:filename},
			accept: {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png'
            },
			server: '/upload_img.html'
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
			if (res.err && res.err!='') {
    			msg(res.err);	
    		}
			else if(res.url && res.url!='')
			{
				window.location.href = res.url;
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
				if (typeof callback === "function"){
			        callback(res); 
			    }
				else{
					picker.children("div").eq(0).html($img);
					var txt= $.trim(picker.prop("title"));
					if (txt !='') {
						picker.children("div").eq(0).append('<i class="i-del" data-id="'+res.data.img+'" onclick="del_img(this,\''+picker.prop("id")+'\');");"></i><label class="txt">'+txt+'</label>');
					}
				}
				}, 150, 150);
			}    		
		});		
}

//多文件上传
var imgNumLimit= 0;
var uploaderMult_arr =new Array() ;
function upFileList(picker,width,height,numLimit) {
	imgNumLimit = picker.data("numlimit") || 0;
	var uploader_name= 'var uploaderMult="uploaderMult"+picker.prop("id");';
		eval(uploader_name);
	 	uploaderMult = WebUploader.create({
			auto: true,
			multiple: true,
			duplicate: true,
			pick: "#"+picker.prop("id"),
			fileNumLimit:numLimit,
			fileSingleSizeLimit:3145728, //3*1024*1024
			formData: {uploadDir: picker.data("uploaddir"), is_thumb: picker.data("isthumb")},
			accept: {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png'
            },
			server: '/upload_img.html'
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
		.on('uploadFinished',function() {
			setPicker(picker.prop("id"));
		})
		.on('uploadAccept', function(obj, res) {
			if (res.err!='') {
    			msg(res.err);	
    		}
			else
			{ 
				uploaderMult.makeThumb(obj.file, function(error, src) {
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
					picker.parent().before('<li><i class="i-del" data-id="'+res.data.img+'" onclick="del_img(this,\''+picker.prop("id")+'\');"></i><div class="webuploader-pick"><img src="'+src+'"/></div><input type="hidden" value="'+res.data.img+'" name="'+pre+'imgs'+suffix+'[]"/>'+thumb+'</li>');				
				}, width, height);
			}    		
		});	
	uploaderMult_arr[picker.prop("id")]=uploaderMult;
}

//删除图片 
function del_img(t,picker_id) {
    var th = $(t);
    if (th.prop("src") != undefined) {
        id = th.prop("src");
    } else {
        id = th.data("id");
    }
    
    $.getJSON("upload_img.html", {act:'del_img', id:id}, function(res) {
		if(res.err && res.err != '') {
			msg('操作失败，' + res.err);return;
		}
		if(res.url && res.url != '') {
			window.location.href = res.url; return;
		}
		else {
			th.parents("li").eq(0).remove();
			if (imgNumLimit!=0) {
            	setPicker(picker_id);
            }
           
            msg('删除成功');            
        }
	});	    
};

function setPicker(picker_id) {	
	var picker=$("#"+picker_id);
	var len =picker.parents("ul").eq(0).children().length - 1;

	imgNumLimit =imgNumLimit || 0;	
	if (imgNumLimit!=0 &&  imgNumLimit!=undefined) {
		if (imgNumLimit-len>0) {
			uploaderMult_arr[picker_id].destroy();			
			upFileList(picker,68,68,imgNumLimit-len);
		}
		
		if (len>=imgNumLimit && picker.css("display")!='none') {
			picker.parents(".picker").hide();
		}
		else
		{	
			picker.parents(".picker").show();
		}		
	}	
}