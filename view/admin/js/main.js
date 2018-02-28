if(self.frameElement !=null && self.frameElement.id == 'ym_main') {
	parent.document.getElementById('ym_main').height = 100;
}

function dosub(ff) {
	document.getElementById(ff).submit();
}
var parentlayer = parent.document.getElementById('ym_main')!=null? parent.document.getElementById('ym_main').contentDocument:null;

//自适应高度
function autoResizeHeight(id) {
	id = id || "ym_main";
	top.document.getElementById(id).height = top.document.getElementById(id).contentWindow.document.documentElement.scrollHeight;
}

function error(tip) {
	top.layer.alert(tip, {
		opacity: 0.4,
		time: 3000,
		icon: 2
	})
}

function msg(tip, t) {
	t = t || 3000;
	top.layer.msg(tip, {
		opacity: 0.4,
		time: t,
		skin: 'layer-msg'
	});
}

function win(url, title, width, height) {
	title = title || '窗口操作';
	width = width || 800;
	height = height || 500;
	top.layer.open({
		id: 1,
		lock: true,
		type: 2,
		//skin: 'layer-ext-moon',
		title: title,
		shadeClose: true,
		area: [width + "px", height + "px"],
		//skin: 'layui-layer-rim', //加上边框
		content: [url]
	});
}

function hwin(ff) {
	top.layer.open({
		type: 2,
		title: '窗口操作',
		area: ['900px', '97%'],
		shadeClose: true,
		//skin: 'layui-layer-rim', //加上边框
		content: [ff]
	});
}

function wwin(ff, xxt) {
	top.layer.open({
		type: 2,
		title: xxt,
		area: ['1200px', '730px'],
		shadeClose: true,
		//skin: 'layui-layer-rim', //加上边框
		content: [ff]
	});
}

function fwin(ff) {
	/*
	top.$.layer({
            type : 2,
            title: '窗口操作',
				//shade: [0], //阴影
            shadeClose: false,
            maxmin: false,
            fix : false,  
				
            area: ['95%', 720],                     
            iframe: {
				//scrolling: 'no',
                src : ff
            } 
        });

		*/
	top.layer.open({
		type: 2,
		title: '窗口操作',
		shadeClose: true,
		area: ['95%', '720px'],
		//skin: 'layui-layer-rim', //加上边框
		content: [ff]
	});
}

function isok(tip, xxf) {
	top.layer.confirm(tip, function(index) {
		document.getElementById(xxf).submit();
		top.layer.close(index);
	});
}

function isdel(tip, xxf) {
	var msg = (tip == undefined || tip == '') ? '确定要删除吗' : tip;
	top.layer.confirm(msg, {
		icon: 3,
		shadeClose: true,
		skin: 'layer-ext-moon'
	}, function(index) {
		top.layer.close(index);
		toTop();
		location.href = xxf;
	});
}

function toTop() {
	top.scroll(0, 0);
}

function checkall(form, prefix, checkall) {
	var checkall = checkall ? checkall : 'chkall';
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name != checkall && (!prefix || (prefix && e.name.match(prefix)))) {
			e.checked = form.elements[checkall].checked;
		}
	}
}

function delrows(act, is_true) {
	var list = '';
	var istrue = is_true == undefined ? '' : '&istrue=' + is_true;
	$("input[name='ids[]']").each(function() {
		if($(this).prop("checked")) {
			list += $(this).val() + ",";
		}
	});
	if(list != '') {
		list = list.substr(0, list.length - 1);
		isdel('', '/admin.html?do=' + act + '&id=' + list + istrue);
	} else {
		msg('请先选择要删除的记录');
	}
}

function patchop(act, pm, tip) {
	var list = '';
	var param = pm != undefined ? pm : '';
	$("input[name='ids[]']").each(function() {
		if($(this).prop("checked")) {
			list += $(this).val() + ",";
		}
	});
	if(list != '') {
		list = list.substr(0, list.length - 1);
		isdel(tip == undefined ? '确定要这样操作？' : tip, '/admin.html?do=' + act + '&id=' + list + param);
	} else {
		msg('请先选择要操作的记录');
	}
}

function onlyAmount(th) {
	var a = [
		['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
		['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
		['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
		['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
	];
	for(i = 0; i < a.length; i++) {
		var reg = new RegExp(a[i][0]);
		th.value = th.value.replace(reg, a[i][1]);
	}
}

function onlyNum(t) {
	t.value = t.value.replace(/[^\d]/g, '');
}
window.onload = function() {
	if(typeof(jQuery) != "undefined") {
		$(".tabhd li").click(function() {
			$(this).addClass("cur").siblings().removeClass("cur");
			$(".tabbd .tabit").eq($(this).index()).show().siblings().hide();
			top.setIframeHeight();
		});
		//状态
		$(".i-status").click(function() {
			$(this).toggleClass("i-status-on").data('val', $(this).hasClass("i-status-on") ? '1' : '0').blur();
			$(this).siblings("input").val($(this).hasClass("i-status-on") ? '1' : '0');
		});
	}
};
//打印
function print_form(act, el, cname) {
	if(!act || !el) {
		return;
	}
	var list = '';
	$(el + " input[name='ids[]']").each(function() {
		if($(this).prop("checked")) {
			list += $(this).val() + ",";
		}
	});
	if(list != '') {
		list = list.substr(0, list.length - 1);
		setCookie(cname, list, 1);
		window.location.href = '/admin.html?do=' + act;
	} else {
		msg('请先选择要操作的记录');
	}
}
//设置cookie
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	exdays = exdays || 1;
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires=" + d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}
//获取cookie
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while(c.charAt(0) == ' ') c = c.substring(1);
		if(c.indexOf(name) != -1) return c.substring(name.length, c.length);
	}
	return "";
}
//删除数组里指定元素
Array.prototype.removeByVal = function(val) {
	for(var i = 0; i < this.length; i++) {
		if(this[i] == val) {
			this.splice(i, 1);
			break;
		}
	}
}

$(document).ready(function() {
	//选择条
	$(".ladder-mod .it-list span").click(function() {
		var mod = $(this).parents(".ladder-mod");
		var ismult = mod.data("ismult") == 1;
		if(ismult) {
			$(this).toggleClass("selected");
		} else {
			mod.children(".it-list").children("span").removeClass("selected");
			$(this).addClass("selected");
		}
		if($(this).hasClass("all")) {
			mod.children(".it-list").children("span").removeClass("selected");
			$(this).addClass("selected");
		} else {
			mod.children(".it-list").children("span.all").removeClass("selected");
		}
		var ids = '';
		mod.children(".it-list").children("span.selected").each(function() {
			ids += $(this).data("id") + ",";
		});
		mod.children("input").val(ids != '' ? ids.substr(0, ids.length - 1) : '');
	});
	$(".select-mod #cur").click(function(e) {
		$(".select-mod .it-list").hide(); //将其它隐藏
		var itList = $(this).siblings("div");
		if(itList.is(":hidden")) {
			itList.slideDown(200);
		} else {
			itList.hide();
		}
		e.stopPropagation();
	});
	$(".select-mod .it-list p").click(function() {
		$(this).parent().siblings("span").html($(this).html()).attr("data-id", $(this).attr("id")).siblings("input").val($(this).attr("id"));
	});
	$(document).click(function() {
		$(".select-mod .it-list").hide();
	});
});