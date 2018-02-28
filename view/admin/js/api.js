$(function() {
	//ajax编辑
	$(".ajax").blur(function() {
		postact($(this));
	});
	
	function postact (m, callback) {
		var v=m.is("a")? $.trim(m.data("val")) : $.trim(m.val());
		if (m.data("orival")==v && !m.is("a")) {
			return;
		}
		var o=m;
		
		$.ajax({
			type: "post",
			url: "./admin.html?do="+ m.data("do"),
			data: {
				act: m.data("act"),
				val: v,
				id: m.data("id")
			},
			dataType: "json",
			success: function(data) {
				if (data.err != '') {
					msg(data.err);
				}
				else{
					o.data("orival", o.val());
					msg(data.res);
				}
				
				var cbname = m.data("callback");
				if(callback) {										
					if (typeof callback === "function"){
				        callback(data); 
				    }
				}
				else if(cbname) {
					window[cbname](m, data);
				}
			},
			error:function(data,t){
				msg('操作失败');
			} ,
			complete: function(XMLHttpRequest, textStatus){}
		});
	}

})