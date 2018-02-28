
$(function() {
	
	//注册处理
	var username= $("#username"),
 		mobile= $("#mobile"),
 		smscode= $("#smscode"),
 		password= $("#password"),
 		repassword= $("#repassword"),
 		agree = $("#agree");
 	
 	username.change(function() { 		
 		checkUserName(false);
 	});	
 	mobile.change(function() { 		
 		checkPhone(false);
 	});	
 	password.change(function(){
 		checkPwd(false);
 	});
 	repassword.change(function(){
 		checkRePwd(false);
 	});
 	agree.click(function(){
 		checkAgree();
 	});
 	
 	//验证用户名
 	function checkUserName(issubmit) {
 		var isValid = false;
 		var v=$.trim(username.val());
 		var i= username.siblings(".Validform_checktip").children();
 		if (v=='') {
 			if (issubmit) {
 				i.attr('class',"i-err").children("label").text('请输入用户名');
 			} else{
 				i.attr('class',"i-tip").children("label").text('支持中文、字母、数字、“-”“_”的组合');
 			}  			
 		}
 		else if (getStringLength(v) < 4) {
 			i.attr('class',"i-err").children("label").text('长度只能在4-20个字符之间');
 		}
 		else if (/^[0-9]+$/.test(v)) {
 			i.attr('class',"i-err").children("label").text('用户名不能是纯数字，请重新输入！');
 		}
 		else if (!/^[A-Za-z0-9_\-\u4e00-\u9fa5]+$/.test(v)) {
 			i.attr('class',"i-err").children("label").text('请使用中文、字母、数字、“-”“_”的组合');
 		}
 		else
 		{
 			i.attr('class',"i-suc").children("label").text('');
 			isValid = true;
 		} 	
 		return isValid;
 	}
 	
 	//验证手机
 	function checkPhone(issubmit) {
 		var isValid = false;
 		var v= $.trim(mobile.val());
 		if (v=='') {
 			mui.toast('请输入手机号码');			
 		}
 		else if(!is_mobile(v))
 		{
 			mui.toast('手机号码格式不正确');
 		}
 		else
 		{
 			isValid =true;
 		}
 		return isValid;
 	}
 	
 	//验证密码
 	function checkPwd(issubmit) {
 		var isValid = false;
 		var v=$.trim(password.val()),u=$.trim(username.val());
 		if (v=='') {
 			mui.toast('请输入密码');
 		}
 		else if (getStringLength(v) < 6) {
 			mui.toast('长度只能在6-20个字符之间');
 		}
 		else if (v==u) {
 			mui.toast('密码与用户名相似，有被盗风险，请更换密码');
 		}
 		else
	 	{
	 		if($.trim(repassword.val())!='' && v !=$.trim(repassword.val()))
	 		{
	 			mui.toast('两次密码输入不一致');
	 		}
 			isValid = true;
 		} 	
 		return isValid;
 	}
 	
 	//验证密码
 	function checkRePwd(issubmit) {
 		var isValid = false;
 		var v=$.trim(repassword.val()),u=$.trim(password.val());
 		if (v=='') {
 			mui.toast('请输入密码');
 		}
 		else if(v!=u)
 		{
 			mui.toast('两次密码输入不一致');
 		}
 		else
 		{
 			isValid = true;
 		} 	
 		return isValid;
 	}
 	
 	//协议
 	function checkAgree() {
 		if (agree.prop("checked")==false) {
 			$("#agree").siblings(".Validform_checktip").children().attr('class',"i-err").children("label").text('请同意协议并勾选');
 			return false;
 		}
 		else
 		{
 			$("#agree").siblings(".Validform_checktip").children().attr('class','').children("label").text('');
 			return true;
 		} 
 	} 
 	
 	function checkSmscode()
 	{
 		var v=$.trim(smscode.val());
 		if (v=='') {
 			mui.toast('请填写手机验证码');
 			return false;
 		}
 		else
 		{
 			return true;
 		} 
 	} 	 	
 	
 	//用户注册
 	$("#reg").click(function() { 
 		if (!checkPhone(true) || !checkSmscode() || !checkPwd(true) || !checkRePwd(true)) {
 			return;
 		}
 		var isOauth = $("#isOauth").length; //是否绑定第三方
 		$.ajax({
			type: "post",
			url: "user_api.html?return_url="+(typeof(return_url) != "undefined"?return_url:""),
			data: {
				act:'reg',
				tel:$.trim(mobile.val()),
				smscode: $.trim(smscode.val()),
				password: $.trim(password.val()),
				repassword: $.trim(repassword.val()),
				agree:	1,
				isOauth: isOauth
			},
			dataType: "json",
			success: function(data) {
				if (data.err != '') {
					mui.toast(data.err);
				}
				else{
					mui.toast("注册成功，欢迎您的加入！");
					setTimeout(function() {
						window.location.href= return_url ? return_url :'/';
					},3000);					
				}
			},
			error:function(data,t){
				mui.toast('注册失败，请您稍后再试');
			} ,
			complete: function(XMLHttpRequest, textStatus){}
		});
 	});
 	
 	
 	//登陆处理
 	$("#loginname").change(function() { 		
 		checkLoginname(false);
 	});
 	
 	function checkLoginname(issubmit) {
 		var isValid=true;	
 		var v = $.trim($("#loginname").val());
 		var i = $("#loginname").siblings(".Validform_checktip");
		if (v=='') {
			i.children().attr('class',"i-err").children("label").text('请填写账户名');
			isValid = false;
		}
		else if (getStringLength(v) < 4) {
 			i.children().attr('class',"i-err").children("label").text('账户名不正确');
 			isValid = false;
 		}
		else
		{
			i.children().attr('class',"").children("label").text('');
		}
		
		return isValid;
 	}
 	
 	function checkLoginPwd(issubmit) {
 		var isValid=true;	
 		var v = $.trim($("#passw").val());
 		var i = $("#passw").siblings(".Validform_checktip");
		if (v=='') {
			i.children().attr('class',"i-err").children("label").text('请填写密码');
			isValid = false;
		}
		else if (getStringLength(v) < 6) {
 			i.children().attr('class',"i-err").children("label").text('密码不正确');
 			isValid = false;
 		}
		else
		{
			i.children().attr('class',"").children("label").text('');
		}
		
		return isValid;
 	}
 	
 	
 	//用户登陆
	$("#login").click(function() {		
		var login_name=$.trim($("#loginname").val()),
			passw=$.trim($("#passw").val()),
			authcode=''
			isOauth = $("#isOauth").length; //是否绑定第三方
 
		if (!checkLoginname(true) || !checkLoginPwd(true)) {
			return;
		}

		if ($('.yanm-div').css("display")!='none') {
			authcode = $.trim($("#authcode").val());
		}
 
 		$.ajax({
			type: "post",
			url: "user_api.html?return_url="+(typeof(return_url) != "undefined"?return_url:""),
			data: {
				act: 'login',
				username:login_name,
				password: passw,
				authcode:authcode,
				autologin: $.trim($("#autologin:checked").val()),
				isOauth: isOauth
			},
			dataType: "json",
			success: function(data) {
				if (data.err != '') {
					mui.toast(data.err,10000);
					if(data.data.authcode!=undefined) {
						$(".yanm-div").show();
					}
				}
				else{
					window.location.href= return_url ? return_url :'/';
				}
			},
			error:function(data,t){
				mui.toast('登陆失败，请您稍后再试');
			} ,
			complete: function(XMLHttpRequest, textStatus){}
		});
 	});
 	
   loadLayer();
})

//编辑个人信息
function edit_userinfo() {
	var img = $.trim($("#img").val()),
	sex = $(".checksex .mui-selected").val(),
	birthday = $.trim($("#birthday").val()),
	email = $.trim($("#email").val()),
	username = $.trim($("#username").val()),
	realname = $.trim($("#realname").val()),
	memo = $('#memo').val();
	
 	if (email!='' && !is_email(email)) {
		msg("邮箱格式不正确");
		return ;
	}
 	if (username =='') {
		msg("请填写用户名");
		return ;
	}
	if (realname!='') {
		if (getStringLength(realname)<2) {
			msg("真实姓名不正确");
			return;
		} 
		else if(!is_en(realname) && !is_chinese(realname)){			
			msg("真实姓名不正确");
			return ;
		}		
	}

	$.getJSON("/user.html", {act:'edit_userinfo', username:username,img:img,sex:sex,birthday:birthday,email:email,realname:realname,memo:memo}, function(res) {
		if(res.err && res.err != '') {
			msg('操作失败，' + res.err);return;
		}
		if(res.url && res.url != '') {
			window.location.href = res.url; return;
		}
		else
		{
			msg('更新成功');
			$("#mobile").data("old",mobile);
		}
	});	
}

//绑定手机号码
function bind_mobile(mobile, smscode) {	
	if (mobile == '') {
		msg("请输入手机号码");
		return ;
	}
	else if(!is_mobile(mobile))
 	{
 		msg("手机号码不正确");
		return ;
 	}
 	if(smscode =='')
 	{
 		msg("请填写短信验证码");
		return ;
 	}  

	$.getJSON("/user.html", {act:'bind_mobile', mobile:mobile,smscode:smscode}, function(res) {
		if(res.err && res.err != '') {
			msg('操作失败，' + res.err);return;
		}
		if(res.url && res.url != '') {
			window.location.href = res.url; return;
		}
		else
		{
			msg('绑定成功');
			$("#mobile").data("old",mobile);
			setTimeout(function(){window.location.href ="userinfo.html";}, 1500);
		}
	});	
}


//更改密码    f=0为用旧密码修改密码，f=1为用手机验证码修改密码，f=2为找回密码
function updatePwd(f) {
	var f= f || 0;
    var oldpwd = $.trim($("#oldpwd").val()),
    pwd = $.trim($("#password").val()),
    repwd = $.trim($("#repassword").val());
    smscode = '';

    if (f==0 && oldpwd =='') {
        $("#oldpwd").focus();
        msg("请输入旧密码");
        return false;
    }
    else if(f==1) {
    	smscode = $("#smscode").val();
    	if (smscode =='') {
    		msg("请输入手机验证码");
        	return false;
    	}
    }
    if (pwd == '') {
        $("#password").focus();
        msg("请输入新密码");
        return false;
    }
    else if (getStringLength(pwd) < 6) {
    	msg("新密码长度为6-20个字符");
        return false;
    }
    
    if (repwd == '') {
        $("#repassword").focus();
        msg("请输入确认密码");
        return false;
    }
    if (pwd != repwd) {
        msg("两次输入的密码不一致");
        return false;
    }
    
    $.getJSON("/user.html", {act:'updatepwd', oldpwd:oldpwd,pwd:pwd,repwd:repwd, smscode:smscode, authtype:f}, function(res) {
		if(res.err && res.err != '') {
			msg('操作失败，' + res.err);return;
		}
		if(res.url && res.url != '') {
			window.location.href = res.url; return;
		}
		else
		{
			msg('修改密码成功');
			if (f !=0) {
				window.location.href ="user.html";
			}			
		}
	});
}

//取消服务单
function cancel_service(id, th) {
    if (!id) {
    	mui.toast("操作异常");return;
    }
    var t= $(th);
 
    $.getJSON("/user.html", {act:'cancel_service', id:id}, function(res) {
		if(res.err && res.err != '') {
			mui.toast('操作失败，' + res.err);return;
		}
		if(res.url && res.url != '') {
			window.location.href = res.url; return;
		}
		else
		{
			mui.toast('取消成功');
			t.parent().siblings(".status").children().html("已取消");
			t.remove();
		}
	});
}