(function (doc, win) {
    var docEl = doc.documentElement,
    resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
    recalc = function () {
        var clientWidth = docEl.clientWidth;
        if (!clientWidth) return; 
        if(clientWidth>=640){
            docEl.style.fontSize='80px';
        }else{
            docEl.style.fontSize = 40 * (clientWidth / 320) + 'px';
        }       
    };
    if (!doc.addEventListener) return;
        win.addEventListener(resizeEvt, recalc, false);
        doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);

 //将时间减去1秒，计算天、时、分、秒
function SetRemainTime(n) {
	n = n || '';
	var SysSecond = parseInt($("#time"+n).data("time"));
	if (SysSecond > 0) { 
   		SysSecond = SysSecond - 1; 
   		var second = Math.floor(SysSecond % 60);           // 计算秒     
   		var minute = Math.floor((SysSecond / 60) % 60);    //计算分 
   		var hour = Math.floor((SysSecond / 3600) % 24);    // 计算时
   		var day=Math.floor(SysSecond / 86400);      //计算天
   		$("#v_hour"+n).html( hour);
   		$("#v_minute"+n).html( minute);
   		$("#v_second"+n).html( second); 
   		$("#v_day"+n).html(day);
   		$("#time"+n).data("time", SysSecond);
  	} else {//剩余时间小于或等于0的时候，就停止间隔函数 
   		/* window.clearInterval(InterValObj+n);  */
   		//这里可以添加倒计时时间为0后需要执行的事件 
  	} 
}
//表单验证
function onlyNum(t) {
	t.value = t.value.replace(/[^\d]/g, '');
}
function is_tel(v) {
	return /^1[3|4|5|7|8]\d{9}$/.test(v);
}
function is_pass(v){
	return /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,12}$/.test(v);//六到十二位数字字母
}
//身份证号码
function is_cardid(v) {
	return /(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/.test(v);
}
function is_mobile(v) {
	return /^(13|14|15|17|18)[0-9]{9}$/.test(v);
}
function onlyAmount(th){
    var a = [
        ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
        ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
        ['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
        ['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
    ];
    for(i=0; i<a.length; i++){
        var reg = new RegExp(a[i][0]);
        th.value = th.value.replace(reg, a[i][1]);
    }
}
function onlyNum(t) {
	t.value = t.value.replace(/[^\d]/g, '');//  /[^\d\.]/g
}
//验证码倒数
function daoshu(even){
    var util = {
        wait: 60,
        hsTime: function (that) {
            var _this = this;
			if (_this.wait == 0) {
				$(even).removeAttr("disabled").val('获取验证码');
					_this.wait = 60;
			} else {
				$(that).attr("disabled", true).val( _this.wait + '秒后重发');
				_this.wait--;
				setTimeout(function () {
				_this.hsTime(that);
			}, 1000)
		}
	}
	}
	util.hsTime(even);
	$(even).click(function(){
		util.hsTime(even);
	});
}

//基于mui全选不选
function setCheckbox(checkallid, itemname) {
	(function(m) {
		var cbk = document.getElementById(checkallid);
		var listBox = m("input[name='"+itemname+"']");
		var n = listBox.length;
		cbk.checked = $("input[name='"+itemname+"']:checked").length == n;
		//全选
		cbk.addEventListener('change', function(e) {				
			if(e.target.checked) {
				listBox.each(function() {
					var ele = this;
					ele.checked = true
				})
			} else {
				listBox.each(function() {
					var ele = this;
					ele.checked = false;
				})
			}
		});	
		//子项选择
		$("input[name='"+itemname+"']").each(function() {
			$(this)[0].addEventListener('change', function(e) {	
				cbk.checked = $("input[name='"+itemname+"']:checked").length == n;
			});
		});
	})(mui)
}
//mui评分
function star(){
	mui('.icons').on('tap','i',function(){
	  	var index = parseInt(this.getAttribute("data-index"));
	  	var parent = this.parentNode;
	  	var children = parent.children;
	  	if(this.classList.contains("mui-icon-star")){
	  		for(var i=0;i<index;i++){
	 			children[i].classList.remove('mui-icon-star');
				children[i].classList.add('mui-icon-star-filled');
			}
		}else{
		  	for (var i = index; i < 5; i++) {
		  		children[i].classList.add('mui-icon-star')
		  		children[i].classList.remove('mui-icon-star-filled')
		  	}
		}
		starIndex = index;
		parent.children[5].value = index;
    });
}

//表单判断按钮
$(".inplab input").each(function(){
	$(this).keyup(function(){ 
		var is=true;
		$(".inplab input").each(function(){
			if($(this).val()==""){
				is=false;
				return false;
			}
	    });
	    if (is) {
	    	$("#save-btn").removeClass("btn-grey");
	    }else{
	    	$("#save-btn").addClass("btn-grey");
	    }
	});
});

//置顶
function totop(){
	$(document).scroll(function(){
		if($(document).scrollTop()>0 ){ 
			$('#totop').fadeIn(300);
		}else{
			$("#totop").fadeOut(300);
		}
	});
	$('#totop').click(function(){$('html,body').animate({scrollTop: '0px'}, 300);});
}

function loadLayer() {
	$("body").append('<script src="./static/js/layer/layer.min.js"></script>');
}

//字符串长度
function getStringLength(str) {
	if(!str) {
		return;
	}
	var bytesCount = 0;
	for(var i = 0; i < str.length; i++) {
		var c = str.charAt(i);
		if(/^[\u0000-\u00ff]$/.test(c)) {
			bytesCount += 1;
		} else {
			bytesCount += 2;
		}
	}
	return bytesCount;
}
//英文字母
function is_en(v) {
	return /^[A-Za-z]+$/.test(v);
}

//英文字母和数字 6到20位
function is_enAndnum(v) {
	return /^[A-Za-z0-9]{6,20}$/.test(v);
}

//手机号码
function is_mobile(v) {
	return /^(13|14|15|17|18)[0-9]{9}$/.test(v);
}

//email
function is_email(v) {
	return /^(\w-*\.*)+@(\w-?)+(\.\w{2,10})+$/.test(v);
}

//固定电话
function is_tel(v) {
	return /^[0-9]{1,4}(-|[0-9])\d{5,15}$/.test(v);
}
 
//中文
function is_chinese(v) {
	return /^([\u4e00-\u9fa5])([\u4e00-\u9fa5·]){0,23}([\u4e00-\u9fa5])$/i.test(v);
} 

function is_consignee(v) {
	return /^(([\u4e00-\u9fa5])([\u4e00-\u9fa5·]){0,10}([\u4e00-\u9fa5]))|([a-zA-Z]([a-zA-Z]|\s){2,20})$/i.test(v);
}
//发送验证码统一函数
function sendSms(t, act, mobile) {
	if (typeof(mobile)=="undefined") {
		var t = $(t);
		if(t.data("mobileid") != "undefined")
		{
			var	ipt_mobile = $("#"+t.data("mobileid"));
		}
		else
		{
			var ipt_mobile= t.siblings("#mobile");			
		}
		var mobile = $.trim(ipt_mobile.val());
	}
    
    var err = '';
    if (mobile == '') {
        err = "请输入手机号码"; 
    } else if (!is_mobile(mobile)) {
        err ="手机号码不正确";
    }
    
    if (err != '') {
    	mui.toast(err);
    	if(ipt_mobile) {
    		ipt_mobile.focus();
    	}
    }
    
    $.getJSON("user_api.html", {
        act: act,
        mobile: mobile
    },
    function(res) {
        if (res.err && res.err != '') {
            mui.toast("发送失败，" + res.err, 4000);
            return;
        } else {
            mui.toast("验证码已发送，10分钟内有效");
            return;
        }
    });
};


//修改支付密码
function editPaypwd() {
	var mobile= $.trim($("#mobile").val()),
		authcode= $.trim($("#authcode").val()),
		paypwd= $.trim($("#paypwd").val()),
		repaypwd= $.trim($("#repaypwd").val());
	if(authcode.length <4) {
		mui.toast("请填写验证码");return;
	}
	if(paypwd=='') {
		mui.toast("请填写支付密码");return;
	}
	if(!is_enAndnum(paypwd))
	{
		mui.toast("支付密码请使用数字和字母，6到16个字符",4000);return;
	} 
	if(repaypwd=='') {
		mui.toast("再输入支付密码");return;
	}
	if(paypwd !== repaypwd) {
		mui.toast("两次输入的支付密码不一致");return;
	}
	$.getJSON("/user.html", {act:'set_paypwd',mobile:mobile,authcode:authcode,paypwd:paypwd,repaypwd:repaypwd}, function(res) {
		if( (res.err && res.err != '')) {
			mui.toast('操作失败，' + res.err);return;
		}
		else if( (res.url && res.url != '')) {
			mui.toast('操作失败，您登陆超时了，请重新登陆。');
			setTimeout("window.location.href="+res.url,3000);
		}
		else
		{
			mui.toast('设置成功');
			$("#edit-paypwd,#setpaypwd").remove();
			$("#mask").hide();
			$("#userbalance,#userpoint").removeAttr("disabled");
		}
	});		
}

//添加到购物车
function addCart(gid, direct, num, spec) {
	num = num || $("#goods_num").val();
	var spec = spec==undefined ? '' : spec;
	if (spec==0) {
		var err ='';
		spec='';
		$("#goods-spec .it").each(function() {
			if ($(this).find(".checked").length==0) {
				err += "请选择 " + $(this).children(".spec-name").html()+"<br>";				
			}
			else{
				spec +=$(this).find(".checked").children("a").attr("id")+',';
			}
		});
		if (err !='') {
			mui.toast(err);
			return;
		}
		if (spec!='') {
			spec =spec.substr(0, spec.length-1);
		}
	}
	var data = {
		act: 'addtocart',
		gid: gid,
		spec: spec,
		type: 1,
		num: num,
		direct:direct
	};
	$.getJSON("/cart.html", data, function(res) {
		if(res && res.url) {
			window.location.href = res.url;
			return;
		}
		if(res.err && res.err != '') {
			mui.toast('加入购物车失败，' + res.err);
		} else {
			$(".cartinfo").html(res.res);
			mui.toast('已加入购物车');
			if(direct && direct ==1) {
				window.location.href = '/order.html?directbuy=1';
			}
		}
	});
}


function removeGoods(ids, spec) {
		$.getJSON("/cart.html", {
			act: 'remove_goods',
			gid: ids,
			spec: spec
		}, function(res) {
			if(res.err != '') {
				mui.toast("删除失败，" + res.err);
			} else {
				mui.toast("删除成功");
				$(".cartinfo").html(res.res);
				var list = ids.split("@");
				$.each(list, function(k, v) {
					$("#" + v).remove();
				});
			}
		});
}

//取消订单
function order_cancel(oid) {
	var btnArray = ['否', '是'];
	mui.confirm('是否取消订单，确认？', '温馨提示', btnArray, function(e) {
		if (e.index == 1) {
			$.getJSON("./order.html", {
				act: 'order_cancel',
				oid: oid
			}, function(res) {
				if (res.err && res.err != '') {
					mui.toast('取消失败，' + res.err);
					return;
				}
				if (res.url && res.url != '') {
					window.location.href = res.url;
					return;
				} else {
					mui.toast('取消成功');
					setTimeout(function() {
						window.location.reload();
					}, 1500);
				}
			});
		} else {
			//否
		}
	});
}

//添加多个商品到购物车   多个商品Id和规格分别用-分隔, 如    1-2-3   白色-64g
function addcartMult(gid, direct, num, spec)
{
	if (gid ==undefined || gid=='') {
		mui.toast("商品编号不能为空");
	}
	var gids = gid.split("-"), specs=spec.split("-");
	if (gids.length==0) {
		return;
	}
	for (var i = 0; i < gids.length; i++) {
		if ($.trim(gids[i])!='') {
			addCart($.trim(gids[i]), 0, num, specs[i]);
		}
	}
	if(direct && direct ==1) {
		window.location.href = '/order.html?directbuy=1';
	}
}

//确认收货
function confirm_receiving(oid) {
	if (!oid) {
		return;
	}
	
	var btnArray = ['否', '是'];
	mui.confirm('请确认是否已收到货？', '温馨提示', btnArray, function(e) {
		if (e.index == 1) {
			$.getJSON("/order.html", {act:'confirm_receiving', oid:oid}, function(res) {
				if(res.err && res.err != '') {
					mui.toast('操作失败，' + res.err);return;
				}
				if(res.url && res.url != '') {
					window.location.href = res.url; return;
				}
				else
				{
					mui.toast('您已确认收货');
					setTimeout(function() {
						window.location.reload();
					},2000);			
				}
			});
		} else {
			//否
		}
	});
}

$(function() {
	$(".select-mod ul li").each(function() {
		$(this).click(function() {
			$(this).addClass("checked").siblings().removeClass("checked");
			if ($(this).parents("#ym-item").length>0) {
				var spec ='';
				$("#goods-spec .it .select-mod ul .checked a").each(function() {
					spec += $(this).attr("id")+ ',';
				});
				if (spec !='') {
					spec = spec.substr(0, spec.length - 1);
				}
				$.getJSON("/item.html",{act:'get_specinfo', id:goods_id, spec:spec}, function(res) {
					$("#ym-price").html(res.data.price);
				});				
			}
			else{
				$("#ym-price").html(price);
			}
		});
	});
});

//价格
$("#btnprice").click(function() {
	var m = $.trim($("#price-min").val()),
		l = $.trim($("#price-max").val());
	m = m == '' ? 0 : parseInt(m);
	l = l == '' ? 0 : parseInt(l);
	if(m == 0 && l == 0) {
		return;
	}
	var href = window.location.href;
	var list = href.split('&');
	var newurl = '';
	if(href.indexOf("pr=") == -1) {
		newurl = href + (href.indexOf("?") == -1 ? "?" : "&") + "pr=" + m + "-" + l
	} else {
		$.each(list, function(k, v) {
			if(v.indexOf("pr=") == 0) {
				newurl += "pr=" + m + "-" + l;
			} else {
				newurl += list[k] + "&";
			}
		});
	}
	window.location.href = newurl;
});

//检测手机是否存在
function check_mobile(mobile, callback) {
	$.getJSON("user_api.html", {act:'check_mobile',mobile:mobile}, function(res){
		if (typeof callback === "function"){
			callback(res); 
		};							
	});
}

//验证当前手机
function check_cur_mobile(mobile, smscode, callback) {
	$.getJSON("user_api.html", {act:'check_cur_mobile',mobile:mobile,smscode:smscode}, function(res){
		if (typeof callback === "function"){
			callback(res); 
		};							
	});
}

//领取优惠券
function receive_coupon(coupon_id, btn) {
	$.getJSON("quan.html", {act:'receive_coupon',coupon_id:coupon_id}, function(res){
		if (typeof callback === "function"){
			callback(res); 
		}
		else
		{
			if(res.url) {
				window.location.href = res.url;return;
			}
			else if(res.err && res.err !='') {
				mui.toast("领取失败，" + res.err);return;
			}
			mui.toast("领取成功", 5000);
			btn.replaceWith('<i class="quan-ico quan-geted"></i>');
		}
	});
}

//领取优惠券
$(".couponlist").on("click", ".getcoupon", function() {
	receive_coupon($(this).data("id"), $(this));
});