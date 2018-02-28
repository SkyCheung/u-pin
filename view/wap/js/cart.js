$(function() {
	$(".cart-tr:last-child").css("border-bottom", "none");
	$(".slide-like-pro .hd li:first-child").css("border-left", "none");
	$(".goods-title-pic:last-child").css("border-bottom", "none");
	$(".good-num:last-child").css("border-bottom", "none");
	$(".goods-price:last-child").css("border-bottom", "none");
	$(".xiaoji:last-child").css("border-bottom", "none");
	//购物数量改变时
	$("#list li .nr .mui-numbox .result").each(function() {
		$(this).change(function() {
			var n = parseInt($(this).val());
			var p = $(this).parents('li');
			p.find('.reconum').html(n);
			var chk = p.children(".mui-checkbox").children("#listCheckBox"); //checkbox
			var price = p.find(".g-price").html();
			p.find(".subtotal").val((n * price).toFixed(2)); //单个商品总价格
			updateCart(p.attr("id"), p.data("spec"), n);
			chk.prop("checked", "checked");
			if(chk.parents("li").hasClass("it-selected") == false) {
				chk.parents("li").addClass("it-selected");
			}
			sumShopping();
			//更新商品选择状态
			$("#list li").each(function() {
				st = $(this).children(".mui-checkbox").children("input[name='checkbox1']").attr("checked");
				if(st!="checked"){
					return;
				}
				setCheckbox('selectall', 'checkbox1');
			});
		});
	});		
	
	//移除商品
	$(".removeGoods").each(function() {
		$(this).click(function() {
			var p = $(this).parent().parent();
			var ids = p.attr("id"),
				spec = p.data("spec");
			layer.confirm('确定要删除吗', function(index) {
				removeGoods(ids, spec);
				top.layer.close(index);
			});
		});
	});
	//批量移除商品
	$("#removebatch").click(function() {
		var ids = '';
		var spec = '';
		$("#list input[name='chk_list']:checked").each(function() {
			ids += $(this).parent().parent().attr("id") + "@";
			spec += $(this).parent().parent().data("spec") + "@";
		});
		if(ids == '') {
			return;
		}
		ids = ids.substring(0, ids.length - 1);
		layer.confirm('确定要删除吗', function(index) {
			removeGoods(ids, spec);
			top.layer.close(index);
		});
	});	
	
	//更新商品选择状态
	$("#list li").each(function() {
		$(this).children(".mui-checkbox").children("input[name='checkbox1']").click(function() {
			var p = $(this).parent(".mui-checkbox").parent(".cart-tr");
			updateStatus(p);
			setCheckbox('selectall', 'checkbox1');
		});
	});
	
	$("#selectall").change(function(){
		$("#list li").each(function(){
			updateStatus($(this));
		});
	});
		
});

//商品加入收藏
	$(".addfav").each(function() {
		$(this).click(function() {
			var t =$(this);
			var p = $(this).parent().parent();
			var ids = p.attr("id"),
				spec = p.data("spec");
			$.getJSON("/user.html", {
				act: 'addfav',
				gid: ids,
				spec: spec
			}, function(res) {
				if(res.err != '') {
					mui.toast("收藏失败，" + res.err);
				} 
				else if( (res.url && res.url != '')) {
					mui.toast('操作失败，请先登陆。');
					setTimeout("window.location.href="+res.url, 3000);
				}
				else {
					mui.toast("收藏成功");
					t.replaceWith('<span style="color: #999;">已收藏</span>')
				}
			});
		});
	});
	
	function addfavs(gids, spec){
		$.getJSON("/user.html", {
			act: 'addfav',
			gid: gids,
			spec: spec
		}, function(res) {
			if(res.err != '') {
				mui.toast("收藏失败，" + res.err);
			} 
			else if( (res.url && res.url != '')) {
				mui.toast('操作失败，请先登陆。');
				setTimeout("window.location.href="+res.url, 3000);
			}
			else {
				mui.toast("收藏成功");
			}
		});
	}
	
	//取消收藏
	function delFav(gid, spec,th) {
		var t =$(th);
		$.getJSON("/user.html", {
				act: 'del_fav',
				gid: gid,
				spec: spec
			}, function(res) {
				if(res.err != '') {
					mui.toast("取消失败，" + res.err);
				} 
				else if( (res.url && res.url != '')) {
					mui.toast('操作失败，请先登陆。');
					setTimeout("window.location.href="+res.url, 3000);
				}
				else {
					mui.toast("取消成功");
					t.parents("li").eq(0).remove();
				}
		});
	};

//去结算
function gotoOrder() {
	var total=$.trim($("#selectnum").val());
	if (total=='' || parseInt(total)==0) {
		mui.toast("请选择要结算的商品");
		return;
	}
	window.location.href = '/order.html';
};
	

//更新已选数目
function sumShopping() {
	var n = 0,
		p = 0;
	$("#list li").each(function() {
		tt = $(this).children(".mui-checkbox").children("input[name='checkbox1']:checked");
		if(tt.val()){
			n = n + parseFloat(tt.parent(".mui-checkbox").siblings(".nr").find(".result").val());
			p += parseFloat(tt.parent(".mui-checkbox").siblings(".nr").find(".subtotal").val());
		}
	});
	$("#selectnum").val(n);
	$("#total").html(p.toFixed(2));	
}

function updateStatus(t) {
	var gid = t.attr("id"),
		spec = t.data("spec"),
		status = t.children(".mui-checkbox").children("input[name='checkbox1']:checked").val() ? 1 : 0;
	var data = {
		act: 'set_cart_status',
		gid: gid,
		spec: spec,
		status: status
	}
	$.getJSON("/cart.html", data, function(res) {
		if(res.err && res.err != '') {
			msg('操作失败，' + res.err);
		}
		
	});
	if(status == 1) {
		t.addClass("it-selected");
	} else {
		t.removeClass("it-selected");
	}
	sumShopping();
}

//更新购物车
function updateCart(gid, spec, total) {
	var data = {
		act: 'addtocart',
		gid: gid,
		spec: spec,
		total: total
	}
	$.getJSON("/cart.html", data, function(res) {
		if(res.err && res.err != '') {
			msg('操作失败，' + res.err);
		} else {
			$(".cartinfo").html($.cookie("cnum"));
		}
	});
}


//设置默认地址
function saveconsignees() {
	var isdefault = isdefaults? 1:0;
	if (name=='') {
		mui.toast("请填写收货人");
		return;
	}
	else
	{
		var errorFlag = false;
		if(!is_consignee(name)){
			errorFlag = true;
		}else if(name.search(/·{2,}/) > -1){
			errorFlag = true;
		}
		if(errorFlag){
			mui.toast("收件人姓名仅支持中文或英语");
			return;
		}
	}
	if (mobile=='') {
		mui.toast("请填写手机号码");
		return;
	}
	else if (!is_mobile(mobile)) {
		mui.toast("手机号码格式不正确");
		return;
	}
	if (tel!='' && !is_tel(tel)) {
		mui.toast("固定电话格式不正确");
		return;
	}
	if (district=='' || district.length < 3) {
		mui.toast("请选择所在地区");
		return;
	}
	if (address =='') {
		mui.toast("请填写详细地址");
		return;
	}
	var data={
		act:"edit_consignee",
		id:id,
		name:name,
		mobile:mobile,
		tel:tel,
		district:district,
		address:address,
		isdefault:isdefault
	}
	$.getJSON("/order.html", data, function(res) {
		if(res.err && res.err != '') {
			mui.toast('操作失败，' + res.err);return;
		}
		else if( (res.url && res.url != '')) {
			mui.toast('操作失败，您登陆超时了，请重新登陆。');
			setTimeout("window.location.href="+res.url,3000);
		}
		else
		{
			mui.toast("保存成功");
			window.location.href="order.html";
		}		
	});
	 
}

//保存收货地址
function saveconsignee() {
	var id = $.trim($("#id").val()),
	name = $.trim($("#cadd-name").val()),
	mobile = $.trim($("#cadd-tel").val()),
	tel = $.trim($("#cadd-tel").val()),
	district = $.trim($("#addressid").val()),
	address = $.trim($("#cadd-add").val()),
	isdefault = $("#isdefault").val()? 1:0;
	if (name=='') {
		mui.toast("请填写收货人");
		return;
	}
	else
	{
		var errorFlag = false;
		if(!is_consignee(name)){
			errorFlag = true;
		}else if(name.search(/·{2,}/) > -1){
			errorFlag = true;
		}
		if(errorFlag){
			mui.toast("收件人姓名仅支持中文或英语");
			return;
		}
	}
	if (mobile=='') {
		mui.toast("请填写手机号码");
		return;
	}
	else if (!is_mobile(mobile)) {
		mui.toast("手机号码格式不正确");
		return;
	}
	if (tel!='' && !is_tel(tel)) {
		mui.toast("固定电话格式不正确");
		return;
	}
	if (district=='' || district.length < 3) {
		mui.toast("请选择所在地区");
		return;
	}
	if (address =='') {
		mui.toast("请填写详细地址");
		return;
	}
	var data={
		act:"edit_consignee",
		id:id,
		name:name,
		mobile:mobile,
		tel:tel,
		district:district,
		address:address,
		isdefault:isdefault
	}
	$("#saveconsignee").attr("disabled", "disabled");
	$.getJSON("/order.html", data, function(res) {
		$("#saveconsignee").removeAttr("disabled");
		if(res.err && res.err != '') {
			mui.toast('操作失败，' + res.err);return;
		}
		else if( (res.url && res.url != '')) {
			mui.toast('操作失败，您登陆超时了，请重新登陆。');
			setTimeout("window.location.href="+res.url,3000);
		}
		else
		{
			mui.toast("保存成功");
			$("#ma.edit-add").hide();
			address = $("#cityResult3").html()+address;
			if (isdefault==1) {
				$("li.other-add").find(".setdefault").html('设为默认');
				$("li.other-add").find(".setdefault").removeClass('word-yellow');
			}
			if (id=='' || id=='0') {
				id = res.res;
				$("li.other-add").removeClass("default-add");
				var html= '<li class="other-add default-add" id="'+ id+'" data-cityid="'+curArea.curCityId+'" '+(isdefault==1?'style="background-color:#fff;float: none;display: block;"':'')+'>';
				html += '<div class="username-phonen"><div class="up"><p class="username" id="cadd-name">'+name+'</p><p class="phonen" id="cadd-tel">'+mobile+'</p></div>';
				html += '<div class="add-detail">'+address+'</div></div><div class="btn-editadd">'+(isdefault==1?'<button class="default-add setdefault word-yellow">默认地址</button>':'<button class="default-add setdefault">设为默认</button>')+'<div class="btn-edit-dele"><button class=" mui-btn edit-btn" onclick="editAddr('+id +');">编辑</button><button value="'+id +'" type="button" class="mui-btn mui-btn-blue mui-btn-outlined dele">删除</button></div></div></li>';
				$(".add-add").before(html);
			} else{
				$("#"+id).data("cityid", curArea.curCityId).find("#cadd-names").html(name);
				$("#"+id).find("#cadd-tels").html(mobile);
				if (isdefault==1) {
					$("#"+id).find(".setdefault").html('设为默认');
					$("#"+id).find(".setdefault").removeClass('word-yellow');
				}
				$("#"+id).find(".add-detail").html(address);
			}
			setadd();			
		}
		window.location.href="order.html";
	});
	 
}

//关闭窗口
$("i.close-modbox").click(function(){
	$("#mask,.modbox").hide();
});
//新增地址
function addAddr(){
	$("#mask").show();$(".edit-address").slideDown(200);
	$("#id").val('');
	$("#cadd-name").val('');
	$("#cadd-tel").val($("#user_mobile").val());
	$("#cityResult3").html('');
	$("#cadd-add").val('');	
	location_init();
};

//编辑地址
function editAddr(id){
	if (id ==undefined || id==0) {
		return;
	}
	$.getJSON("/order.html", {id:id,act:'get_consignee'}, function(res) {
		if( (res.err && res.err != '') || res.data.length==0) {
			msg('加载地址失败，' + res.err);return;
		}
		else if( (res.url && res.url != '')) {
			msg('操作失败，您登陆超时了，请重新登陆。');
			setTimeout("window.location.href="+res.url,3000);
		}
		else
		{
			$(".adrs").html("编辑地址");
			$("#id").val(id);
			$("#cadd-name").val(res.data[0].name);
			$("#cadd-tel").val(res.data[0].mobile);
			$("#cityResult3").html(res.data[0].province_name + " " + res.data[0].city_name + " " + res.data[0].area_name + " " + res.data[0].town_name);
			$("#cadd-add").val(res.data[0].address);
			$("#addressid").val(res.data[0].province + "-" + res.data[0].city + "-" + res.data[0].area + "-" + res.data[0].town);
		}
	});
};

//删除地址
function removeAddr(id) {
	$.getJSON("/order.html", {id:id,act:'del_consignee'}, function(res) {
		if( (res.err && res.err != '')) {
			mui.toast('操作失败，' + res.err);return;
		}
		else if( (res.url && res.url != '')) {
			mui.toast('操作失败，您登陆超时了，请重新登陆。');
			setTimeout("window.location.href="+res.url,3000);
		}
		else
		{
			mui.toast('删除成功');
			$("#"+id).remove();
		}
	});	
}
//选择地址
$(".sh-address ul li.other-add").each(function() {
	$(this).click(function() {
		$(this).addClass("default-add").siblings().removeClass("default-add");
	});
});

//支付密码
$("#setpaypwd a").click(function() {
	$("#mask").show(); $("#edit-paypwd").slideDown(200);
});

function editPaypwd() {
	var authcode= $.trim($("#authcode").val()),
		paypwd= $.trim($("#paypwd").val()),
		repaypwd= $.trim($("#repaypwd").val());
	if(authcode.length <4) {
		msg("请填写验证码");return;
	}
	if(paypwd=='') {
		msg("请填写支付密码");return;
	}
	if(!is_enAndnum(paypwd))
	{
		msg("支付密码请使用数字和字母，6到16个字符",4000);return;
	} 
	if(repaypwd=='') {
		msg("再输入支付密码");return;
	}
	if(paypwd !== repaypwd) {
		msg("两次输入的支付密码不一致");return;
	}
	$.getJSON("/user.html", {act:'set_paypwd',authcode:authcode,paypwd:paypwd,repaypwd:repaypwd}, function(res) {
		if( (res.err && res.err != '')) {
			msg('操作失败，' + res.err);return;
		}
		else if( (res.url && res.url != '')) {
			msg('操作失败，您登陆超时了，请重新登陆。');
			setTimeout("window.location.href="+res.url,3000);
		}
		else
		{
			msg('设置成功');
			$("#edit-paypwd,#setpaypwd").remove();
			$("#mask").hide();
			$("#userbalance,#userpoint").removeAttr("disabled");
		}
	});		
}

//计算订单金额   使用顺序  优惠券>积分>余额
function calcTotal() {
	var sum =0;	
	var expfee = parseFloat($("#expfee").html());
	var payAmount = goods_amount + expfee; //应付金额
	var point_use = 0;//使用积分 
	var point = parseFloat($("#curpoint").html()); //现有积分
	var balance_use = 0;//使用余额
	var balance = parseFloat($("#curbalance").html()); //余额
	var point_amount = 0,balance_amount =0,coupon_amount = 0; //抵用的金额
	
	if (point_amount > payAmount * mostpoint * 0.01) { 
		point_amount = payAmount * mostpoint * 0.01;
		use_point = point_amount * pointpay;
		mui.toast("积分抵用金额最多不超过每笔订单结算金额的"+ mostpoint +"%",4000);		
	}
	
	//优惠券
	coupon_amount = getCouponAmount();
	payAmount = payAmount - coupon_amount;
	
	//显示使用的情况
	//优惠券
	if (coupon_amount >0) {		
		$("#coupon").html(coupon_amount.toFixed(2));
	}
	else
	{
		$("#coupon").html("0.00");
	}
	
	//积分 
	if($("#userpoint").attr("isuse") =="1") {
		var point_amount = parseInt(payAmount * mostpoint * 0.01),
		    point_use = point_amount * pointpay;
		if(point_use > point) //小于现有积分
		{
			point_use = point - point % pointpay; //使用积分
			point_amount = point_use/pointpay;
		}
		point_amount = point_amount.toFixed(2);		
		payAmount = payAmount - point_amount;
		$("#point,#pointamount").html(point_amount);
		$("#pointval").html(point_use);
	}
	else
	{
		$("#point").html('0.00');
	}

	//余额
	if($("#userbalance").attr("isuse") =="1" && payAmount>0) {
		balance_use = payAmount > balance ? balance : payAmount;//使用余额
		payAmount = payAmount - balance_use;
		balance_use = balance_use.toFixed(2);		
		$("#balance").html(balance_use);
		$("#balanceval").html(balance_use);
	}
	else
	{
		$("#balance").html('0.00');
	}
	
	$("#payAmount").html(payAmount.toFixed(2));	
	if (sum == 0)
	{
	  $("#paylist").parent().hide(200);
	}
	else
	{
		$("#paylist").parent().slideDown(200);
	}
}

//获得优惠券金额
function getCouponAmount() {
	var coupon_amount = 0;	
	if($("#couponlist").length>0)
	{
		$("#couponlist>.selected").each(function() {
			coupon_amount += parseInt($.trim($(this).data("amount")));
		});
	}
	return coupon_amount;
}

$(".zhifu-box ul.zfb li").each(function() {
	$(this).click(function() {
		$(this).find("a").addClass("selected").end().siblings().find("a").removeClass("selected");
		$(this).children("i.icon-check-zf").css("display", "block").end().siblings().children("i").css("display", "none");
	});
});

//获取运费
$("#expresslist li a").click(function() {
	$.getJSON("/order.html", {act:'get_expfee',id:$(this).attr("id"), cityid: $(".sh-address .default-add").data("cityid") }, function(res) {
		if(res.err && res.err != '') {
			msg('操作失败，' + res.err);return;
		}
		else
		{
			$("#expfee").html(res.res);
			calcTotal();
		}
	});	
});



