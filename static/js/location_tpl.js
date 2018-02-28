
var iplocation = {ym_iplocation};
var provinceCityJson = {ym_provinceCityJson};


var cName = "ipLocation";
var curLocation = "北京";
var curProvinceId = 110000;

//根据省份ID获取名称
function getNameById(provinceId){
	for(var o in iplocation){
		if (iplocation[o]&&iplocation[o].id==provinceId){
			return o;
		}
	}
	return "北京";
}

var provinceHtml = '<div class="content"><div data-widget="tabs" class="m JD-stock" id="JD-stock">'
								+'<div class="mt">'
								+'    <ul class="tab">'
								+'        <li data-index="0" data-widget="tab-item" class="curr"><a href="#none" class="hover"><em>请选择</em><i></i></a></li>'
								+'        <li data-index="1" data-widget="tab-item" style="display:none;"><a href="#none" class=""><em>请选择</em><i></i></a></li>'
								+'        <li data-index="2" data-widget="tab-item" style="display:none;"><a href="#none" class=""><em>请选择</em><i></i></a></li>'
								+'        <li data-index="3" data-widget="tab-item" style="display:none;"><a href="#none" class=""><em>请选择</em><i></i></a></li>'
								+'    </ul>'
								+'    <div class="stock-line"></div>'
								+'</div>'
								+'<div class="mc" data-area="0" data-widget="tab-content" id="stock_province_item">'
								+'    <ul class="area-list">ym_area_list</ul>'
								+'</div>'
								+'<div class="mc" data-area="1" data-widget="tab-content" id="stock_city_item"></div>'
								+'<div class="mc" data-area="2" data-widget="tab-content" id="stock_area_item"></div>'
								+'<div class="mc" data-area="3" data-widget="tab-content" id="stock_town_item"></div>'
								+'</div></div>';
function getAreaList(result){
	var html = ["<ul class='area-list'>"];
	var longhtml = [];
	var longerhtml = [];
	if (result&&result.length > 0){
		for (var i=0,j=result.length;i<j ;i++ ){
			result[i].name = result[i].name.replace(" ","");
			if(result[i].name.length > 12){
				longerhtml.push("<li class='longer-area'><a href='#none' data-value='"+result[i].id+"'>"+result[i].name+"</a></li>");
			}
			else if(result[i].name.length > 5){
				longhtml.push("<li class='long-area'><a href='#none' data-value='"+result[i].id+"'>"+result[i].name+"</a></li>");
			}
			else{
				html.push("<li><a href='#none' data-value='"+result[i].id+"'>"+result[i].name+"</a></li>");
			}
		}
	}
	else{
		html.push("<li><a href='#none' data-value='"+curArea.curFid+"'> </a></li>");
	}
	html.push(longhtml.join(""));
	html.push(longerhtml.join(""));
	html.push("</ul>");
	return html.join("");
}
function cleanKuohao(str){
	if(str&&str.indexOf("(")>0){
		str = str.substring(0,str.indexOf("("));
	}
	if(str&&str.indexOf("（")>0){
		str = str.substring(0,str.indexOf("（"));
	}
	return str;
}

function getStockOpt(id, name){
	if(curArea.curLevel==3){
		curArea.curAreaId = id;
		curArea.curAreaName = name;
		if(!page_load){
			curArea.curTownId = 0;
			curArea.curTownName = "";
		}
	}
	else if(curArea.curLevel==4){
		curArea.curTownId = id;
		curArea.curTownName = name;
	}
	//添加20140224
	$('#store-selector').removeClass('hover');
	//setCommonCookies(curArea.curProvinceId,curLocation,curArea.curCityId,curArea.curAreaId,curArea.curTownId,!page_load);
	if(page_load){
		page_load = false;		
	}
	//替换gSC
	var address = curArea.curProvinceName+curArea.curCityName+curArea.curAreaName+curArea.curTownName;
	var ids= curArea.curProvinceId+"-"+curArea.curCityId+"-"+curArea.curAreaId+"-"+curArea.curTownId;
	$("#district").val(ids);
	$("#store-selector .text div").html(curArea.curProvinceName+cleanKuohao(curArea.curCityName)+cleanKuohao(curArea.curAreaName)+cleanKuohao(curArea.curTownName)).attr("title",address);
}

function getAreaListcallback(r){
	curDom.html(getAreaList(r));
	if (curArea.curLevel >= 2){
		curDom.find("a").click(function(){
			if(page_load && !edit_init){
				page_load = false;
			}
			if(curDom.attr("id")=="stock_area_item"){
				curArea.curLevel=3;
			}
			else if(curDom.attr("id")=="stock_town_item"){
				curArea.curLevel=4;
			}
			getStockOpt($(this).attr("data-value"),$(this).html());
		});
		if(page_load || edit_init){ //初始化加载
			curArea.curLevel = curArea.curLevel==2?3:4;
			 
			 if(curArea.curAreaId && new Number(curArea.curAreaId)>0 && curArea.curLevel==3){
				getStockOpt(curArea.curAreaId,curDom.find("a[data-value='"+curArea.curAreaId+"']").html());
			}
			else if (curArea.curTownId && new Number(curArea.curTownId)>0 && curArea.curLevel==4) {
				getStockOpt(curArea.curTownId,curDom.find("a[data-value='"+curArea.curTownId+"']").html());
				areaTabContainer.eq(3).find("em").html(curArea.curTownName);
				edit_init=false;
			}
			else{
				getStockOpt(curDom.find("a").eq(0).attr("data-value"),curDom.find("a").eq(0).html());
			}
		}
	}
}

function chooseProvince(provinceId){
	provinceContainer.hide();
	curArea.curLevel = 1;
	curArea.curProvinceId = provinceId;
	curArea.curProvinceName = getNameById(provinceId);
	if(!page_load){
		curArea.curCityId = 0;
		curArea.curCityName = "";
		curArea.curAreaId = 0;
		curArea.curAreaName = "";
		curArea.curTownId = 0;
		curArea.curTownName = "";
	}
	areaTabContainer.eq(0).removeClass("curr").find("em").html(curArea.curProvinceName);
	areaTabContainer.eq(1).addClass("curr").show().find("em").html("请选择");
	areaTabContainer.eq(2).hide();
	areaTabContainer.eq(3).hide();
	cityContainer.show();
	areaContainer.hide();
	townaContainer.hide();
	if(provinceCityJson[""+provinceId]){
		cityContainer.html(getAreaList(provinceCityJson[""+provinceId]));
		cityContainer.find("a").click(function(){
			if(page_load){
				page_load = false;
			}
			$("#store-selector").unbind("mouseout");
			chooseCity($(this).attr("data-value"),$(this).html());
		});
		if(page_load){ //初始化加载
			if(curArea.curCityId&&new Number(curArea.curCityId)>0){
				chooseCity(curArea.curCityId,cityContainer.find("a[data-value='"+curArea.curCityId+"']").html());
			}
			else{
				chooseCity(cityContainer.find("a").eq(0).attr("data-value"),cityContainer.find("a").eq(0).html());
			}
		}
	}	
}

function chooseCity(cityId,cityName){
	provinceContainer.hide();
	cityContainer.hide();
	curArea.curLevel = 2;
	curArea.curCityId = cityId;
	curArea.curCityName = cityName;
	if(!page_load){
		curArea.curAreaId = 0;
		curArea.curAreaName = "";
		curArea.curTownId = 0;
		curArea.curTownName = "";
	}
	areaTabContainer.eq(1).removeClass("curr").find("em").html(cityName);
	areaTabContainer.eq(2).addClass("curr").show().find("em").html("请选择");
	areaTabContainer.eq(3).hide();
	areaContainer.show().html("<div class='iloading'>正在加载中，请稍候...</div>");
	townaContainer.hide();
	curDom = areaContainer;
	$.getJSON("index.html?rd=" + Math.random(), {action: 'cart',act: 'get_area',pid: cityId}, function (r) {
		getAreaListcallback(r);
		$("#stock_area_item a").click(function () {
			chooseArea($(this).data("value"),$(this).html());
			$('#store-selector').addClass('hover');
			townaContainer.show();
		});
		if(page_load || edit_init){
			chooseArea(curArea.curAreaId,areaContainer.find("a[data-value='"+curArea.curAreaId+"']").html());
		}
	});
}

function chooseArea(areaId,areaName){
	provinceContainer.hide();
	cityContainer.hide();
	areaContainer.hide();
	curArea.curLevel = 3;
	curArea.curAreaId = areaId;
	curArea.curAreaName = areaName;
	if(!page_load && !edit_init){
		curArea.curTownId = 0;
		curArea.curTownName = "";
	}
	areaTabContainer.eq(2).removeClass("curr").find("em").html(areaName);
	areaTabContainer.eq(3).addClass("curr").show().find("em").html("请选择");
	townaContainer.show().html("<div class='iloading'>正在加载中，请稍候...</div>");
	curDom = townaContainer;
	$.getJSON("index.html?rd=" + Math.random(), {action: 'cart',act: 'get_area',pid: areaId}, getAreaListcallback);
}

$("#store-selector .text").after(provinceHtml);
var areaTabContainer=$("#JD-stock .tab li");
var provinceContainer=$("#stock_province_item");
var cityContainer=$("#stock_city_item");
var areaContainer=$("#stock_area_item");
var townaContainer=$("#stock_town_item");
var curDom = provinceContainer;
//当前地域信息
var curArea;
//初始化当前地域信息
function curAreaInit(countyId,provinceId, cityId, areaId, townId){
	curArea =  {"curLevel": 1,"curProvinceId": 0,"curProvinceName":"","curCityId": 0,"curCityName":"","curAreaId": 0,"curAreaName":"","curTownId":0,"curTownName":""};
	/*var ipLoc ='440000-440600-440604-440604012'; //getCookie("ipLoc-djd");
	ipLoc = ipLoc?ipLoc.split("-"):[1,72,0,0];
	if(ipLoc.length>0&&ipLoc[0]){
		curArea.curProvinceId = ipLoc[0];
		curArea.curProvinceName = getNameById(ipLoc[0]);
	}
	if(ipLoc.length>1&&ipLoc[1]){
		curArea.curCityId = ipLoc[1];
	}
	if(ipLoc.length>2&&ipLoc[2]){
		curArea.curAreaId = ipLoc[2];
	}
	if(ipLoc.length>3&&ipLoc[3]){
		curArea.curTownId = ipLoc[3];
	}*/
}
function location_init() {
	curArea.curProvinceId = 0;
	curArea.curCityId = 0;
	curArea.curAreaId = 0;
	curArea.curTownId= 0;
	$("#district").val('');
	$("#store-selector .text div").html(" - 请选择 - ");
	areaTabContainer.eq(0).addClass("curr").show().find("em").html("请选择");
	areaTabContainer.eq(0).siblings().removeClass("curr").hide();
	provinceContainer.show();
	cityContainer.hide();
	areaContainer.hide();
	townaContainer.hide();
}

var page_load = true,edit_init = false;
(function(){
	$("#store-selector .text").unbind("click").bind("click",function(){
		$('#store-selector').addClass('hover');
		$("#store-selector .content,#JD-stock").show();		
	}).find("dl").remove();
	$(document).click(function(e) {
		if ($(e.target).parents("#store-selector").length !=1) {
			$('#store-selector').removeClass('hover');
		}		
	});
	
	curAreaInit();
	areaTabContainer.eq(0).find("a").click(function(){
		areaTabContainer.removeClass("curr");
		areaTabContainer.eq(0).addClass("curr").show();
		provinceContainer.show();
		cityContainer.hide();
		areaContainer.hide();
		townaContainer.hide();
		areaTabContainer.eq(1).hide();
		areaTabContainer.eq(2).hide();
		areaTabContainer.eq(3).hide();
	});
	areaTabContainer.eq(1).find("a").click(function(){
		areaTabContainer.removeClass("curr");
		areaTabContainer.eq(1).addClass("curr").show();
		provinceContainer.hide();
		cityContainer.show();
		areaContainer.hide();
		townaContainer.hide();
		areaTabContainer.eq(2).hide();
		areaTabContainer.eq(3).hide();
	});
	areaTabContainer.eq(2).find("a").click(function(){
		areaTabContainer.removeClass("curr");
		areaTabContainer.eq(2).addClass("curr").show();
		provinceContainer.hide();
		cityContainer.hide();
		areaContainer.show();
		townaContainer.hide();
		areaTabContainer.eq(3).hide();
	});
	provinceContainer.find("a").click(function() {
		if(page_load){
			page_load = false;
		}
		$("#store-selector").unbind("mouseout");
		chooseProvince($(this).attr("data-value"));
	}).end();
	$("#store-selector a").click(function () {
		edit_init=false;
	});
	
	//chooseProvince(curArea.curProvinceId);
})();

function getCookie(name) {
	var start = document.cookie.indexOf(name + "=");
	var len = start + name.length + 1;
	if ((!start) && (name != document.cookie.substring(0, name.length))) {
		return null;
	}
	if (start == -1) return null;
	var end = document.cookie.indexOf(';', len);
	if (end == -1) end = document.cookie.length;
	return unescape(document.cookie.substring(len, end));
};
