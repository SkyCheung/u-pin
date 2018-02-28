$(function() {
	$(".cart-tr:last-child").css("border-bottom", "none");
	$(".slide-like-pro .hd li:first-child").css("border-left", "none");
	$(".goods-title-pic:last-child").css("border-bottom", "none");
	$(".good-num:last-child").css("border-bottom", "none");
	$(".goods-price:last-child").css("border-bottom", "none");
	$(".xiaoji:last-child").css("border-bottom", "none");
	//购物数量改变时
	$("#list .result").each(function() {
		$(this).change(function() {
			var n = parseInt($(this).val());
			var p = $(this).parent();
			var chk = p.siblings(".cart-one").children("input");
			var price = p.prev().children(".g-price").html();
			p.next().children(".subtotal").html((n * price).toFixed(2));
			updateCart(p.parent().attr("id"), p.parent().data("spec"), n);
			chk.prop("checked", "checked");
			if(chk.parents(".cart-tr").hasClass("it-selected") == false) {
				chk.parents(".cart-tr").addClass("it-selected");
			}
			sumShopping();
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
	$("#list input[name='chk_list']").each(function() {
		$(this).click(function() {
			updateStatus($(this));
			setall(".btn-checkall", "#list");
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
					msg("收藏失败，" + res.err);
				} 
				else if( (res.url && res.url != '')) {
					msg('操作失败，请先登陆。');
					setTimeout("window.location.href="+res.url, 3000);
				}
				else {
					msg("收藏成功");
					t.replaceWith('<span style="color: #999;">已收藏</span>')
				}
			});
		});
	});
	
	//取消收藏
	function delFav(gid, spec,th) {
		var t =$(th);
		$.getJSON("/user.html", {
				act: 'del_fav',
				gid: gid,
				spec: spec
			}, function(res) {
				if(res.err != '') {
					msg("取消失败，" + res.err);
				} 
				else if( (res.url && res.url != '')) {
					msg('操作失败，请先登陆。');
					setTimeout("window.location.href="+res.url, 3000);
				}
				else {
					msg("取消成功");
					t.parents("li").eq(0).remove();
				}
		});
	};

//去结算
function gotoOrder() {
	var total=$.trim($("#selectnum").html());
	if (total=='' || parseInt(total)==0) {
		msg("请选择要结算的商品");
		return;
	}
	window.location.href = '/order.html';
};
	

//更新已选数目
function sumShopping() {
	var n = 0,
		p = 0;
	$("#list input[name='chk_list']:checked").each(function() {
		n = n + parseFloat($(this).parent().siblings(".btn-add-reduce").children(".result").val());
		p += parseFloat($(this).parent().siblings(".cart-five").children(".subtotal").html())
	});
	$("#selectnum").html(n);
	$("#total").html(p.toFixed(2));	
}

function updateStatus(t) {
	var p = t.parents(".cart-tr");
	var gid = p.attr("id"),
		spec = p.data("spec"),
		status = t.prop("checked") ? 1 : 0;
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
		p.addClass("it-selected");
	} else {
		p.removeClass("it-selected");
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
	};
	$.getJSON("/cart.html", data, function(res) {
		if(res.err && res.err != '') {
			msg('操作失败，' + res.err);
		} else {
			$(".cartinfo").html($.cookie("cnum"));
		}
	});
}

//保存收货地址
function saveconsignee() {
	var id = $.trim($("#id").val()),
	name = $.trim($("#sh-name").val()),
	mobile = $.trim($("#sh-phone").val()),
	tel = $.trim($("#sh-tel").val()),
	district = $.trim($("#district").val()),
	address = $.trim($("#sh-address").val()),
	isdefault = $("#isdefault").is(":checked")? 1:0;

	if (name=='') {
		msg("请填写收货人");
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
			msg("收件人姓名仅支持中文或英语");
			return;
		}
	}
	if (mobile=='') {
		msg("请填写手机号码");
		return;
	}
	else if (!is_mobile(mobile)) {
		msg("手机号码格式不正确");
		return;
	}
	if (tel!='' && !is_tel(tel)) {
		msg("固定电话格式不正确");
		return;
	}
	if (district=='' || district.length < 3) {
		msg("请选择所在地区");
		return;
	}
	if (address =='') {
		msg("请填写详细地址");
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
			msg('操作失败，' + res.err);return;
		}
		else if( (res.url && res.url != '')) {
			msg('操作失败，您还未登陆');
			//setTimeout("window.location.href='"+res.url+"'",3000);
		}
		else
		{
			msg("保存成功");			
			$("#mask,.edit-address").hide();
			address = $("#store-selector .text>div").html()+address;
			if (isdefault==1) {
				$("li.other-add").find(".moblie").siblings("span").html('');
			}
			if (id=='' || id=='0') {
				id = res.res;
				$(".sh-address ul li.other-add").removeClass("default-add");
				var html= '<li class="other-add default-add" id="'+ id+'" data-cityid="'+curArea.curCityId+'">';
				html += '<div class="add-box"><span class="remove vivi-blue" onclick="removeAddr('+id+');">X</span>';
				html += '<div class="name-add"><span class="name">'+name+'</span><span class="add-rside"></span></div>';
				html += '<div class="elli add-detail"><p title="'+address+'">'+address+'</p></div>';
				html += '<div><span class="moblie">'+mobile+'</span><span style="margin-left: 30px;">'+(isdefault==1?'默认地址':'')+'</span>';
				html += '<a href="javascript:void(0);" onclick="editAddr('+id +');" class="chang-default change vivi-blue">修改</a></div></div><i class="icon-check"></i></li>';
				$(".sh-address .add-add").before(html);
			} else{				
				$("#"+id).data("cityid", curArea.curCityId).find(".name").html(name);
				$("#"+id).find(".moblie").html(mobile);
				if (isdefault==1) {
					$("#"+id).find(".moblie").siblings("span").html('默认地址');
				}
				
				$("#"+id).find(".add-detail").children().attr("title", address).html(address);
			}			
		}		
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
	$("#sh-name").val('');
	$("#sh-phone").val($("#user_mobile").val());
	$("#sh-tel").val('');
	$("#district").val('');
	$("#sh-address").val('');
	$("#isdefault").removeAttr("checked");	
	location_init();
};

//编辑地址
function editAddr(id){
	if (id ==undefined || id==0) {
		return;
	}
	$("#mask").show();$(".edit-address").slideDown(200);
	$("#saveconsignee").attr("disabled", "disabled");
	
	$.getJSON("/order.html", {id:id,act:'get_consignee'}, function(res) {
		if( (res.err && res.err != '') || res.data.length==0) {
			msg('加载地址失败，' + res.err);return;
		}
		else if( (res.url && res.url != '')) {
			msg('操作失败，您还未登陆');
			setTimeout("window.location.href="+res.url,3000);
		}
		else
		{
			$("#id").val(id);
			$("#sh-name").val(res.data[0].name);
			$("#sh-phone").val(res.data[0].mobile);
			$("#sh-tel").val(res.data[0].tel);
			$("#district").val(res.data[0].name);
			$("#sh-address").val(res.data[0].address);
			$("#isdefault").attr("checked",res.data[0].is_default==1);	
			curArea.curProvinceId = res.data[0].province;
			curArea.curCityId = res.data[0].city;
			curArea.curAreaId = res.data[0].area;
			curArea.curTownId= res.data[0].town;
			page_load=true, edit_init=true;
			chooseProvince(curArea.curProvinceId);
		}
		$("#saveconsignee").removeAttr("disabled");
	});
};

//删除地址
function removeAddr(id) {
	layer.confirm('确定要删除吗', function(index) {	
		var d= $("li.default-add");
		$.getJSON("/order.html", {id:id,act:'del_consignee'}, function(res) {
			if( (res.err && res.err != '')) {
				msg('操作失败，' + res.err);return;
			}
			else if( (res.url && res.url != '')) {
				msg('操作失败，您登陆超时了，请重新登陆。');
				setTimeout("window.location.href="+res.url,3000);
			}
			else
			{
				msg('删除成功');
				$("#"+id).remove();
			}
			d.addClass("default-add");
			$("#saveconsignee").removeAttr("disabled");
		});	
		top.layer.close(index);
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

//计算订单金额   使用顺序  优惠券>积分>余额
function calcTotal() {
	var sum =0;	
	var expfee = parseFloat($("#expfee").html());
	var payAmount = goods_amount + expfee; //应付金额
	var point_use = 0;//使用积分 
	var point = parseFloat($("#point").siblings().children("b").html()); //现有积分
	var balance_use = 0;//使用余额
	var balance = parseFloat($("#balance").siblings().children("b").html()); //现有余额
	var point_amount = 0,balance_amount =0,coupon_amount = 0; //抵用的金额
	
	//优惠券
	coupon_amount = getCouponAmount();
	payAmount = payAmount - coupon_amount;
	
	//积分 
	if($("#userpoint").is(":checked")) {
		var point_amount = parseInt(payAmount * mostpoint * 0.01),
		    point_use = point_amount * pointpay;
		if(point_use > point) //小于现有积分
		{
			point_use = point - point % pointpay; //使用积分
			point_amount = point_use/pointpay;
		}
		point_amount = point_amount.toFixed(2);		
		payAmount = payAmount - point_amount;
		$("#point").val(point_use);
	}
	else
	{
		$("#point").val('');
	}

	//余额
	if($("#userbalance").is(":checked") && payAmount>0) {
		balance_use = payAmount > balance ? balance : payAmount;//使用余额
		payAmount = payAmount - balance_use;
		$("#balance").val(balance_use);
	}
	else
	{
		$("#balance").val('');
	}
	
	//显示使用的情况
	//优惠券
	if (coupon_amount >0) {
		if ($("#coupon-offset").length==0) {
			$(".calu-box").append('<p class="slivergrey"><span>优惠券抵用：</span><span class="txtmoney">￥<b id="coupon-offset">-'+ coupon_amount+'</b></span></p>');
		}
		else
		{
			$("#coupon-offset").html("-"+coupon_amount);
		}
	}
	else
	{
		$("#coupon-offset").parents(".slivergrey").remove();
	}
		
	if ($("#userpoint").is(":checked")) {
		if ($("#point-offset").length==0) {
			$(".calu-box").append('<p class="slivergrey"><span>积分抵用：</span><span class="txtmoney">￥<b id="point-offset">-'+point_amount +'</b></span></p>');
		} else{
			$("#point-offset").html("-"+point_amount);
		}							
	}
	else{
		$("#point-offset").parents(".slivergrey").remove();
	}
	if ($("#userbalance").is(":checked")) {
		if ($("#balance-offset").length==0) {
			$(".calu-box").append('<p class="slivergrey"><span>余额抵用：</span><span class="txtmoney">￥<b id="balance-offset">-'+ balance_use+'</b></span></p>');
		} else{
			$("#balance-offset").html("-"+balance_use);
		}							
	}
	else{
		$("#balance-offset").parents(".slivergrey").remove();
	}	
		
	$(".txtmoney b").each(function() {
		sum+= parseFloat($.trim($(this).html()));
	});
	$("#payAmount").html(sum);
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




