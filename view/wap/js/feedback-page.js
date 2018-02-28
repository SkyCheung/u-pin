(function(mui, window, document, undefined) {
	mui.init();
	var get = function(id) {
		return document.getElementById(id);
	};
	var qsa = function(sel) {
		return [].slice.call(document.querySelectorAll(sel));
	};
	var ui = {
		question: get('question'),
		contact: get('contact'),
		imageList: get('image-list'),
		submit: get('submit'),
		total:get('image-list').getAttribute("data-limit")
	};
	ui.clearForm = function() {
		ui.question.value = '';
		ui.contact.value = '';
		ui.imageList.innerHTML = '';
		ui.newPlaceholder();
	};
	ui.getFileInputArray = function() {
		return [].slice.call(ui.imageList.querySelectorAll('input[type="file"]'));
	};
	ui.getFileInputIdArray = function() {
		var fileInputArray = ui.getFileInputArray();
		var idArray = [];
		fileInputArray.forEach(function(fileInput) {
			if (fileInput.value != '') {
				idArray.push(fileInput.getAttribute('id'));
			}
		});
		return idArray;
	};
	var imageIndexIdNum = 0;
	ui.newPlaceholder = function() {
		var fileInputArray = ui.getFileInputArray();
		if (fileInputArray &&
			fileInputArray.length > 0 &&
			fileInputArray[fileInputArray.length - 1].parentNode.classList.contains('space')) {
			return;
		}
		imageIndexIdNum++;
		
		var placeholder = document.createElement('div');
		placeholder.setAttribute('class', 'image-item space');
		var closeButton = document.createElement('div');
		closeButton.setAttribute('class', 'image-close');
		closeButton.innerHTML = 'X';
		closeButton.addEventListener('click', function(event) {
			event.stopPropagation();
			event.cancelBubble = true;
			var sdf = this;
			var imgsrc = sdf.parentNode.children[2].value;
			
			$.getJSON("upload_img.html", {act:'del_img', id:imgsrc}, function(res) {
				if(res.err && res.err != '') {
					mui.toast('操作失败，' + res.err);return;
				}
				if(res.url && res.url != '') {
					window.location.href = res.url; return;
				}
				else {
					mui.toast('删除成功');            
				}
			});	
			
			setTimeout(function() {
				ui.imageList.removeChild(placeholder);
				ui.newPlaceholder();
			}, 0);
			return false;
		}, false);
		var fileInput = document.createElement('input');
		var fileInputimg = document.createElement('input');
		var fileInputthumb = document.createElement('input');
		fileInputimg.setAttribute('type', 'hidden');
		fileInputthumb.setAttribute('type', 'hidden');
		fileInput.setAttribute('type', 'file');
		fileInput.setAttribute('accept', 'image/jpg,image/jpeg,image/png');
		fileInput.setAttribute('id', 'thumbs-' + imageIndexIdNum);
		fileInput.addEventListener('change', function(event) {
			var file = fileInput.files[0];
			var jf = this;
			var goodsid = jf.parentNode.parentNode.parentNode.getAttribute('id');
			if (file) {
				var reader = new FileReader();
				reader.onload = function() {
					//处理 android 4.1 兼容问题
					var base64 = reader.result.split(',')[1];
					var dataUrl = 'data:image/png;base64,' + base64;
					if(dataUrl == "") {
						mui.toast('null');
					}
					$.post("upload_img.html", {img: dataUrl,is_h5: 1,uploadDir:'avatar', filename : 'fi'}, function (data) {
						if (data.err && data.err !='') {
							mui.toast("上传失败");return
						}
						else
						{
							fileInputthumb.setAttribute('value', data.data.thumb);
							fileInputimg.setAttribute('value', data.data.img);
						}
					}, 'json');
					placeholder.style.backgroundImage = 'url(' + dataUrl + ')';
					fileInputthumb.setAttribute('name', 'thumbs_'+goodsid+'[]');
					fileInputimg.setAttribute('name', 'imgs_'+goodsid+'[]');
				}
				reader.readAsDataURL(file);
				placeholder.classList.remove('space');
				
				if (ui.total && ui.total >ui.imageList.childNodes.length) {
					ui.newPlaceholder();
				}				
			}
		}, false);
		placeholder.appendChild(closeButton);
		placeholder.appendChild(fileInput);
		placeholder.appendChild(fileInputimg);
		placeholder.appendChild(fileInputthumb);
		ui.imageList.appendChild(placeholder);
	};
	ui.newPlaceholder();
	//自行注释了留言板块
//	ui.submit.addEventListener('tap', function(event) {
//		if (ui.question.value == '' ||
//			(ui.contact.value != '' &&
//				ui.contact.value.search(/^(\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+)|([1-9]\d{4,9})$/) != 0)) {
//			return mui.toast('信息填写不符合规范');
//		} 
//		plus.nativeUI.showWaiting();
//		feedback.send({
//			question: ui.question.value,
//			contact: ui.contact.value,
//			images: ui.getFileInputIdArray()
//		}, function() {
//			plus.nativeUI.closeWaiting();
//			mui.toast('感谢您的建议~');
//			ui.clearForm();
//			mui.back();
//		});
//	}, false);
})(mui, window, document, undefined);

