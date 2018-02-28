
var iplocation = {"安徽": { id: "340000", root: 100000, c:340800 },"北京": { id: "110000", root: 100000, c:110100 },"重庆": { id: "500000", root: 100000, c:500100 },"福建": { id: "350000", root: 100000, c:350100 },"甘肃": { id: "620000", root: 100000, c:620400 },"广东": { id: "440000", root: 100000, c:445100 },"广西": { id: "450000", root: 100000, c:450500 },"贵州": { id: "520000", root: 100000, c:520400 },"海南": { id: "460000", root: 100000, c:469030 },"河北": { id: "130000", root: 100000, c:130600 },"河南": { id: "410000", root: 100000, c:410500 },"黑龙江": { id: "230000", root: 100000, c:230600 },"湖北": { id: "420000", root: 100000, c:422800 },"湖南": { id: "430000", root: 100000, c:430700 },"吉林": { id: "220000", root: 100000, c:220800 },"江苏": { id: "320000", root: 100000, c:320400 },"江西": { id: "360000", root: 100000, c:361000 },"辽宁": { id: "210000", root: 100000, c:210300 },"内蒙古": { id: "150000", root: 100000, c:152900 },"宁夏": { id: "640000", root: 100000, c:640400 },"青海": { id: "630000", root: 100000, c:632600 },"山东": { id: "370000", root: 100000, c:371600 },"山西": { id: "140000", root: 100000, c:140400 },"陕西": { id: "610000", root: 100000, c:610900 },"上海": { id: "310000", root: 100000, c:310100 },"四川": { id: "510000", root: 100000, c:513200 },"台湾": { id: "710000", root: 100000, c:710200 },"天津": { id: "120000", root: 100000, c:120100 },"西藏": { id: "540000", root: 100000, c:542500 },"香港": { id: "810000", root: 100000, c:810200 },"澳门": { id: "820000", root: 100000, c:820100 },"新疆": { id: "650000", root: 100000, c:652900 },"云南": { id: "530000", root: 100000, c:530500 },"浙江": { id: "330000", root: 100000, c:330100 }};
var provinceCityJson = {"340000":[{"id":340800,"name":"安庆"},{"id":340300,"name":"蚌埠"},{"id":341600,"name":"亳州"},{"id":341700,"name":"池州"},{"id":341100,"name":"滁州"},{"id":341200,"name":"阜阳"},{"id":340100,"name":"合肥"},{"id":340600,"name":"淮北"},{"id":340400,"name":"淮南"},{"id":341000,"name":"黄山"},{"id":341500,"name":"六安"},{"id":340500,"name":"马鞍山"},{"id":341300,"name":"宿州"},{"id":340700,"name":"铜陵"},{"id":340200,"name":"芜湖"},{"id":341800,"name":"宣城"}],"110000":[{"id":110100,"name":"北京"}],"500000":[{"id":500100,"name":"重庆"}],"350000":[{"id":350100,"name":"福州"},{"id":350800,"name":"龙岩"},{"id":350700,"name":"南平"},{"id":350900,"name":"宁德"},{"id":350300,"name":"莆田"},{"id":350500,"name":"泉州"},{"id":350400,"name":"三明"},{"id":350200,"name":"厦门"},{"id":350600,"name":"漳州"}],"620000":[{"id":620400,"name":"白银"},{"id":621100,"name":"定西"},{"id":623000,"name":"甘南"},{"id":620200,"name":"嘉峪关"},{"id":620300,"name":"金昌"},{"id":620900,"name":"酒泉"},{"id":620100,"name":"兰州"},{"id":622900,"name":"临夏"},{"id":621200,"name":"陇南"},{"id":620800,"name":"平凉"},{"id":621000,"name":"庆阳"},{"id":620500,"name":"天水"},{"id":620600,"name":"武威"},{"id":620700,"name":"张掖"}],"440000":[{"id":445100,"name":"潮州"},{"id":441900,"name":"东莞"},{"id":442101,"name":"东沙"},{"id":440600,"name":"佛山"},{"id":440100,"name":"广州"},{"id":441600,"name":"河源"},{"id":441300,"name":"惠州"},{"id":440700,"name":"江门"},{"id":445200,"name":"揭阳"},{"id":440900,"name":"茂名"},{"id":441400,"name":"梅州"},{"id":441800,"name":"清远"},{"id":440500,"name":"汕头"},{"id":441500,"name":"汕尾"},{"id":440200,"name":"韶关"},{"id":440300,"name":"深圳"},{"id":441700,"name":"阳江"},{"id":445300,"name":"云浮"},{"id":440800,"name":"湛江"},{"id":441200,"name":"肇庆"},{"id":442000,"name":"中山"},{"id":440400,"name":"珠海"}],"450000":[{"id":450500,"name":"北海"},{"id":451000,"name":"百色"},{"id":451400,"name":"崇左"},{"id":450600,"name":"防城港"},{"id":450800,"name":"贵港"},{"id":450300,"name":"桂林"},{"id":451200,"name":"河池"},{"id":451100,"name":"贺州"},{"id":451300,"name":"来宾"},{"id":450200,"name":"柳州"},{"id":450100,"name":"南宁"},{"id":450700,"name":"钦州"},{"id":450400,"name":"梧州"},{"id":450900,"name":"玉林"}],"520000":[{"id":520400,"name":"安顺"},{"id":522400,"name":"毕节"},{"id":520100,"name":"贵阳"},{"id":520200,"name":"六盘水"},{"id":522600,"name":"黔东南"},{"id":522700,"name":"黔南"},{"id":522300,"name":"黔西南"},{"id":522200,"name":"铜仁"},{"id":520300,"name":"遵义"}],"460000":[{"id":469030,"name":"白沙"},{"id":469035,"name":"保亭"},{"id":469031,"name":"昌江"},{"id":469027,"name":"澄迈"},{"id":469003,"name":"儋州"},{"id":469025,"name":"定安"},{"id":469007,"name":"东方"},{"id":460100,"name":"海口"},{"id":469033,"name":"乐东"},{"id":469028,"name":"临高"},{"id":469034,"name":"陵水"},{"id":469002,"name":"琼海"},{"id":469036,"name":"琼中"},{"id":460300,"name":"三沙"},{"id":460200,"name":"三亚"},{"id":469026,"name":"屯昌"},{"id":469006,"name":"万宁"},{"id":469005,"name":"文昌"},{"id":469001,"name":"五指山"}],"130000":[{"id":130600,"name":"保定"},{"id":130900,"name":"沧州"},{"id":130800,"name":"承德"},{"id":130400,"name":"邯郸"},{"id":131100,"name":"衡水"},{"id":131000,"name":"廊坊"},{"id":130300,"name":"秦皇岛"},{"id":130100,"name":"石家庄"},{"id":130200,"name":"唐山"},{"id":130500,"name":"邢台"},{"id":130700,"name":"张家口"}],"410000":[{"id":410500,"name":"安阳"},{"id":410600,"name":"鹤壁"},{"id":410800,"name":"焦作"},{"id":410881,"name":"济源"},{"id":410200,"name":"开封"},{"id":411100,"name":"漯河"},{"id":410300,"name":"洛阳"},{"id":411300,"name":"南阳"},{"id":410400,"name":"平顶山"},{"id":410900,"name":"濮阳"},{"id":411200,"name":"三门峡"},{"id":411400,"name":"商丘"},{"id":411500,"name":"信阳"},{"id":410700,"name":"新乡"},{"id":411000,"name":"许昌"},{"id":410100,"name":"郑州"},{"id":411600,"name":"周口"},{"id":411700,"name":"驻马店"}],"230000":[{"id":230600,"name":"大庆"},{"id":232700,"name":"大兴安岭"},{"id":230100,"name":"哈尔滨"},{"id":230400,"name":"鹤岗"},{"id":231100,"name":"黑河"},{"id":230800,"name":"佳木斯"},{"id":230300,"name":"鸡西"},{"id":231000,"name":"牡丹江"},{"id":230200,"name":"齐齐哈尔"},{"id":230900,"name":"七台河"},{"id":230500,"name":"双鸭山"},{"id":231200,"name":"绥化"},{"id":230700,"name":"伊春"}],"420000":[{"id":422800,"name":"恩施"},{"id":420700,"name":"鄂州"},{"id":421100,"name":"黄冈"},{"id":420200,"name":"黄石"},{"id":420800,"name":"荆门"},{"id":421000,"name":"荆州"},{"id":429005,"name":"潜江"},{"id":429021,"name":"神农架"},{"id":420300,"name":"十堰"},{"id":421300,"name":"随州"},{"id":429006,"name":"天门"},{"id":420100,"name":"武汉"},{"id":420600,"name":"襄阳"},{"id":421200,"name":"咸宁"},{"id":429004,"name":"仙桃"},{"id":420900,"name":"孝感"},{"id":420500,"name":"宜昌"}],"430000":[{"id":430700,"name":"常德"},{"id":430100,"name":"长沙"},{"id":431000,"name":"郴州"},{"id":430400,"name":"衡阳"},{"id":431200,"name":"怀化"},{"id":431300,"name":"娄底"},{"id":430500,"name":"邵阳"},{"id":430300,"name":"湘潭"},{"id":433100,"name":"湘西"},{"id":430900,"name":"益阳"},{"id":431100,"name":"永州"},{"id":430600,"name":"岳阳"},{"id":430800,"name":"张家界"},{"id":430200,"name":"株洲"}],"220000":[{"id":220800,"name":"白城"},{"id":220600,"name":"白山"},{"id":220100,"name":"长春"},{"id":220200,"name":"吉林"},{"id":220400,"name":"辽源"},{"id":220300,"name":"四平"},{"id":220700,"name":"松原"},{"id":220500,"name":"通化"},{"id":222400,"name":"延边朝鲜族"}],"320000":[{"id":320400,"name":"常州"},{"id":320800,"name":"淮安"},{"id":320700,"name":"连云港"},{"id":320100,"name":"南京"},{"id":320600,"name":"南通"},{"id":321300,"name":"宿迁"},{"id":320500,"name":"苏州"},{"id":321200,"name":"泰州"},{"id":320200,"name":"无锡"},{"id":320300,"name":"徐州"},{"id":320900,"name":"盐城"},{"id":321000,"name":"扬州"},{"id":321100,"name":"镇江"}],"360000":[{"id":361000,"name":"抚州"},{"id":360700,"name":"赣州"},{"id":360800,"name":"吉安"},{"id":360200,"name":"景德镇"},{"id":360400,"name":"九江"},{"id":360100,"name":"南昌"},{"id":360300,"name":"萍乡"},{"id":361100,"name":"上饶"},{"id":360500,"name":"新余"},{"id":360900,"name":"宜春"},{"id":360600,"name":"鹰潭"}],"210000":[{"id":210300,"name":"鞍山"},{"id":210500,"name":"本溪"},{"id":211300,"name":"朝阳"},{"id":210200,"name":"大连"},{"id":210600,"name":"丹东"},{"id":210400,"name":"抚顺"},{"id":210900,"name":"阜新"},{"id":211400,"name":"葫芦岛"},{"id":210700,"name":"锦州"},{"id":211000,"name":"辽阳"},{"id":211100,"name":"盘锦"},{"id":210100,"name":"沈阳"},{"id":211200,"name":"铁岭"},{"id":210800,"name":"营口"}],"150000":[{"id":152900,"name":"阿拉善"},{"id":150200,"name":"包头"},{"id":150800,"name":"巴彦淖尔"},{"id":150400,"name":"赤峰"},{"id":150600,"name":"鄂尔多斯"},{"id":150100,"name":"呼和浩特"},{"id":150700,"name":"呼伦贝尔"},{"id":150500,"name":"通辽"},{"id":150300,"name":"乌海"},{"id":150900,"name":"乌兰察布"},{"id":152500,"name":"锡林郭勒"},{"id":152200,"name":"兴安"}],"640000":[{"id":640400,"name":"固原"},{"id":640200,"name":"石嘴山"},{"id":640300,"name":"吴忠"},{"id":640100,"name":"银川"},{"id":640500,"name":"中卫"}],"630000":[{"id":632600,"name":"果洛"},{"id":632200,"name":"海北"},{"id":632100,"name":"海东"},{"id":632500,"name":"海南藏族"},{"id":632800,"name":"海西"},{"id":632300,"name":"黄南"},{"id":630100,"name":"西宁"},{"id":632700,"name":"玉树"}],"370000":[{"id":371600,"name":"滨州"},{"id":371400,"name":"德州"},{"id":370500,"name":"东营"},{"id":371700,"name":"菏泽"},{"id":370100,"name":"济南"},{"id":370800,"name":"济宁"},{"id":371200,"name":"莱芜"},{"id":371500,"name":"聊城"},{"id":371300,"name":"临沂"},{"id":370200,"name":"青岛"},{"id":371100,"name":"日照"},{"id":370900,"name":"泰安"},{"id":370700,"name":"潍坊"},{"id":371000,"name":"威海"},{"id":370600,"name":"烟台"},{"id":370400,"name":"枣庄"},{"id":370300,"name":"淄博"}],"140000":[{"id":140400,"name":"长治"},{"id":140200,"name":"大同"},{"id":140500,"name":"晋城"},{"id":140700,"name":"晋中"},{"id":141000,"name":"临汾"},{"id":141100,"name":"吕梁"},{"id":140600,"name":"朔州"},{"id":140100,"name":"太原"},{"id":140900,"name":"忻州"},{"id":140300,"name":"阳泉"},{"id":140800,"name":"运城"}],"610000":[{"id":610900,"name":"安康"},{"id":610300,"name":"宝鸡"},{"id":610700,"name":"汉中"},{"id":611000,"name":"商洛"},{"id":610200,"name":"铜川"},{"id":610500,"name":"渭南"},{"id":610100,"name":"西安"},{"id":610400,"name":"咸阳"},{"id":610600,"name":"延安"},{"id":610800,"name":"榆林"}],"310000":[{"id":310100,"name":"上海"}],"510000":[{"id":513200,"name":"阿坝"},{"id":511900,"name":"巴中"},{"id":510100,"name":"成都"},{"id":511700,"name":"达州"},{"id":510600,"name":"德阳"},{"id":513300,"name":"甘孜"},{"id":511600,"name":"广安"},{"id":510800,"name":"广元"},{"id":511100,"name":"乐山"},{"id":513400,"name":"凉山"},{"id":510500,"name":"泸州"},{"id":511400,"name":"眉山"},{"id":510700,"name":"绵阳"},{"id":511300,"name":"南充"},{"id":511000,"name":"内江"},{"id":510400,"name":"攀枝花"},{"id":510900,"name":"遂宁"},{"id":511800,"name":"雅安"},{"id":511500,"name":"宜宾"},{"id":510300,"name":"自贡"},{"id":512000,"name":"资阳"}],"710000":[{"id":710200,"name":"高雄"},{"id":712600,"name":"花莲"},{"id":710900,"name":"嘉义"},{"id":711900,"name":"嘉义"},{"id":710700,"name":"基隆"},{"id":710500,"name":"金门"},{"id":712800,"name":"连江"},{"id":711500,"name":"苗栗"},{"id":710600,"name":"南投"},{"id":712700,"name":"澎湖"},{"id":712400,"name":"屏东"},{"id":710100,"name":"台北"},{"id":712500,"name":"台东"},{"id":710300,"name":"台南"},{"id":710400,"name":"台中"},{"id":711400,"name":"桃园"},{"id":711100,"name":"新北"},{"id":710800,"name":"新竹"},{"id":711300,"name":"新竹"},{"id":711200,"name":"宜兰"},{"id":712100,"name":"云林"},{"id":711700,"name":"彰化"}],"120000":[{"id":120100,"name":"天津"}],"540000":[{"id":542500,"name":"阿里"},{"id":542100,"name":"昌都"},{"id":540100,"name":"拉萨"},{"id":542600,"name":"林芝"},{"id":542400,"name":"那曲"},{"id":542300,"name":"日喀则"},{"id":542200,"name":"山南"}],"810000":[{"id":810200,"name":"九龙"},{"id":810100,"name":"香港岛"},{"id":810300,"name":"新界"}],"820000":[{"id":820100,"name":"澳门市"}],"650000":[{"id":652900,"name":"阿克苏"},{"id":659002,"name":"阿拉尔"},{"id":654300,"name":"阿勒泰"},{"id":652800,"name":"巴音郭楞"},{"id":652700,"name":"博尔塔拉"},{"id":652300,"name":"昌吉"},{"id":652200,"name":"哈密"},{"id":653200,"name":"和田"},{"id":653100,"name":"喀什"},{"id":650200,"name":"克拉玛依"},{"id":653000,"name":"克孜勒苏柯尔克孜"},{"id":659001,"name":"石河子"},{"id":654200,"name":"塔城"},{"id":652100,"name":"吐鲁番"},{"id":659003,"name":"图木舒克"},{"id":659004,"name":"五家渠"},{"id":650100,"name":"乌鲁木齐"},{"id":654000,"name":"伊犁"}],"530000":[{"id":530500,"name":"保山"},{"id":532300,"name":"楚雄"},{"id":532900,"name":"大理"},{"id":533100,"name":"德宏"},{"id":533400,"name":"迪庆"},{"id":532500,"name":"红河"},{"id":530100,"name":"昆明"},{"id":530700,"name":"丽江"},{"id":530900,"name":"临沧"},{"id":533300,"name":"怒江"},{"id":530800,"name":"普洱"},{"id":530300,"name":"曲靖"},{"id":532600,"name":"文山"},{"id":532800,"name":"西双版纳"},{"id":530400,"name":"玉溪"},{"id":530600,"name":"昭通"}],"330000":[{"id":330100,"name":"杭州"},{"id":330500,"name":"湖州"},{"id":330400,"name":"嘉兴"},{"id":330700,"name":"金华"},{"id":331100,"name":"丽水"},{"id":330200,"name":"宁波"},{"id":330800,"name":"衢州"},{"id":330600,"name":"绍兴"},{"id":331000,"name":"台州"},{"id":330300,"name":"温州"},{"id":330900,"name":"舟山"}]};

var cName = "ipLocation";
var curLocation = "";
var curProvinceId = 0;

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
								+'    <ul class="area-list"><li><a href="#none" data-value="340000">安徽</a></li><li><a href="#none" data-value="110000">北京</a></li><li><a href="#none" data-value="500000">重庆</a></li><li><a href="#none" data-value="350000">福建</a></li><li><a href="#none" data-value="620000">甘肃</a></li><li><a href="#none" data-value="440000">广东</a></li><li><a href="#none" data-value="450000">广西</a></li><li><a href="#none" data-value="520000">贵州</a></li><li><a href="#none" data-value="460000">海南</a></li><li><a href="#none" data-value="130000">河北</a></li><li><a href="#none" data-value="410000">河南</a></li><li><a href="#none" data-value="230000">黑龙江</a></li><li><a href="#none" data-value="420000">湖北</a></li><li><a href="#none" data-value="430000">湖南</a></li><li><a href="#none" data-value="220000">吉林</a></li><li><a href="#none" data-value="320000">江苏</a></li><li><a href="#none" data-value="360000">江西</a></li><li><a href="#none" data-value="210000">辽宁</a></li><li><a href="#none" data-value="150000">内蒙古</a></li><li><a href="#none" data-value="640000">宁夏</a></li><li><a href="#none" data-value="630000">青海</a></li><li><a href="#none" data-value="370000">山东</a></li><li><a href="#none" data-value="140000">山西</a></li><li><a href="#none" data-value="610000">陕西</a></li><li><a href="#none" data-value="310000">上海</a></li><li><a href="#none" data-value="510000">四川</a></li><li><a href="#none" data-value="710000">台湾</a></li><li><a href="#none" data-value="120000">天津</a></li><li><a href="#none" data-value="540000">西藏</a></li><li><a href="#none" data-value="810000">香港</a></li><li><a href="#none" data-value="820000">澳门</a></li><li><a href="#none" data-value="650000">新疆</a></li><li><a href="#none" data-value="530000">云南</a></li><li><a href="#none" data-value="330000">浙江</a></li></ul>'
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
	$.getJSON("index.html?p=cart&rd=" + Math.random(), {act: 'get_area',pid: cityId}, function (r) {
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
	$.getJSON("index.html?p=cart&rd=" + Math.random(), {act: 'get_area',pid: areaId}, getAreaListcallback);
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


