<?
include_once dirname(__FILE__)."/../conf/language_locale.php";				//다국어 파일 처리. 다국어 처리하는곳에만 실제 처리 로직을 넣는다.
?>
String.prototype.trim = function(){ return this.replace(/(^\s*)|(\s*$)/g, ""); }
String.prototype.str_replace = function(str1,str2){ return this.split(str1).join(str2); }
String.prototype.josa = function(nm) {
	var nm1 = nm.trim().substring(0, nm.trim().indexOf("/"));
	var nm2 = nm.trim().substring(nm.trim().indexOf("/") + 1, nm.trim().length);
	var a = this.substring(this.length - 1, this.length).charCodeAt();
	a = a - 44032;
	var jongsung = a % 28;
	return (jongsung) ? nm1 : nm2;
}

function getNodeIndex(obj){
	var k = 0;
	while(obj.previousSibling){
		k++;
		obj = obj.previousSibling;
	}
	return k;
}
function removeNode(o){ return o.parentNode.removeChild(o); }

function popup(src,width,height) {
	var scrollbars = "1";
	var resizable = "no";
	if (typeof(arguments[3])!="undefined") scrollbars = arguments[3];
	if (arguments[4]) resizable = "yes";
	window.open(src,arguments[5],'width='+width+',height='+height+',scrollbars='+scrollbars+',toolbar=no,status=no,resizable='+resizable+',menubar=no');
}

function vLayer(obj,mode) {
	if (typeof(obj)!="object") obj = $(obj);
	if (typeof(obj)=="undefined" || obj==null) return;
	if (!mode) obj.style.display = (obj.style.display!="block") ? "block" : "none";
	else obj.style.display = mode;
}

/*** 본문 이미지 크기 리사이징 ***/
function innerImgResize(id)
{
	var objContents = $(id);
	if (!objContents) return;
	var innerWidth = objContents.clientWidth;
	var img = objContents.getElementsByTagName('img');
	for (var i=0;i<img.length;i++){
		if (img[i].width>innerWidth){
			img[i].height = img[i].height * innerWidth / img[i].width;
			img[i].width = innerWidth;

			img[i].title = "View Original Image!!";
			img[i].style.cursor = "pointer";

			img[i].onclick = function(){
				imgbox(this);
				//$j(this).lightBox({fixedNavigation:true}); 
			};
		}
	}
}
function _gourl(url){
	window.opener.location.href = url;
	window.close();
}

function gourl(url){
	location.href = url;
}


/*** 게시판 스팸 체크 ***/
function chkSpam(fm,time){
	fm.skey.value = time;
}

/* 이메일 Select Box 선택 함수 */
function set_email(obj,id){
	var ret = document.getElementsByName(id)[1];
	ret.value = obj.value;
}

/*** 우편번호 팝업 ***/
function popupZipcodenothing(rfunc,mode){
	popupZipcode(rfunc,mode);
}

function popupZipcode(rfunc,mode){
	if (!rfunc)	{
		rfunc = "zipcode_return";
	}
	
	if (mode == '800')
		window.open('/module/zipcode_5digit.php?rfunc='+rfunc,'','width=800,height=600,scrollbars=0');
	else
		window.open('/module/zipcode_5digit.php?rfunc='+rfunc,'','width=400,height=600,scrollbars=0');
}

/*** 중국 우편번호 중국 팝업 ***/
function popupZipcodezh_CN(rfunc,mode){
	if (!rfunc)
	{
		rfunc = "zipcode_return_ch";
	}	
	window.open('/module/zipcodezh_CN.php?rfunc='+rfunc,'','width=800,height=600,scrollbars=0');	
}

/*** 20140410 / minks / 데이터 정보 팝업 ***/
function popupDataInfo(kind,rfunc){
	window.open('/module/datainfo.php?kind='+kind+'&rfunc='+rfunc,'','width=445,height=150,scrollbars=0');
}

/*** 20140530 / minks / 비밀번호 변경 팝업 ***/
function popupPassword(kind,rfunc){
	window.open('/member/changepassword.php?kind='+kind+'&rfunc='+rfunc,'','width=445,height=225,scrollbars=0');
}

function zipcode_return(){
	var fm = document.fm;
	
	var zipcode = arguments[0];
	/*
	fm["zipcode[]"][0].value = zipcode[0];
	fm["zipcode[]"][1].value = zipcode[1];
	*/
	fm["zipcode"].value = arguments[0];
	fm["address"].value = arguments[1];
	if (fm.address_sub){
		fm.address_sub.value = "";
		fm.address_sub.focus();
	} else {
		fm["address"].focus();
		fm["address"].value += " ";
	}
}

function zipcode_return_cust(){
	var fm = document.fm;
	var zipcode = arguments[0];
	/*
	fm["cust_zipcode[]"][0].value = zipcode[0];
	fm["cust_zipcode[]"][1].value = zipcode[1];
	*/
	fm["cust_zipcode"].value = arguments[0];
	fm["cust_address"].value = arguments[1];
	if (fm.cust_address_sub){
		fm.cust_address_sub.value = "";
		fm.cust_address_sub.focus();
	} else {
		fm["cust_address"].focus();
		fm["cust_address"].value += " ";
	}
}


function zipcode_return_ch(){
	var fm = document.fm;		
	fm.address.value = arguments[0];
	
	if (fm.address_sub){
		fm.address_sub.value = "";
		fm.address_sub.focus();
	} else {
		fm["address"].focus();
		fm["address"].value += " ";
	}
}

/* 자동 콤마 사용 */
function autoComma(obj){
	if (event.keyCode!=9) obj.value=comma(obj.value);
}

/* 숫자키만 사용 */
function onlynumber() {
	var e = event.keyCode;
	window.status = e;
	if (e>=48 && e<=57) return;
	if (e==8 || e==13 || e==45 || e==46) return;
	event.returnValue = false;
}


/* php number_format() script버전 / 14.11.19 / kjm */
function number_format(fn){
	var str = fn.value;
    var Re = /[^0-9]/g;
    var ReN = /(-?[0-9]+)([0-9]{3})/;
    str = str.replace(Re,'');
    while (ReN.test(str)) {
       str = str.replace(ReN, "$1,$2");
       }
    fn.value = str;
 }


/*** 셀렉트박스 자동선택 함수  ***/
function selectOptions(fm,arr){
	for (var i=0;i<arr.length;i++){
		for (var j=0;j<fm[arr[i]].options.length;j++){
			fm[arr[i]].options[j].selected = true;
		}
	}
}

function isChked(El,msg) {
	if (!El) return;
	if (typeof(El)!="object") El = document.getElementsByName(El);
	if (El) for (i=0;i<El.length;i++) if (El[i].checked) var isChked = true;
	if (isChked) return (msg) ? confirm(msg) : true;
	else {
		alert('<?echo _("선택된 사항이 없습니다")?>');
		return false;
	}
}

function isChkedWpod(El,msg) {
	var chkCount = 0;
	if (!El) return;
	if (typeof(El)!="object") El = document.getElementsByName(El);
	
	if (El) { 
		for (i=0;i<El.length;i++) { 
			if (El[i].checked) { 
				var isChked = true; 
				chkCount++;
			}
		}
	}
	
	if (isChked) {
		if(chkCount > 30) {
			alert('<?echo _("한번에 30건까지 주문이 가능합니다.")?>');
			return false;
		}
		return (msg) ? confirm(msg) : true;
	}
	else {
		alert('<?echo _("선택된 사항이 없습니다")?>');
		return false;
	}
}

function chkrev(objname){
	var chk;
	$j("[name="+objname+"]").each(function(){
		if ($j(this).attr("checked")==true) chk = 1;
	});

	$j("[name="+objname+"]").each(function(){
		if (chk) $j(this).attr("checked",false);
		else $j(this).attr("checked",true);
	});
}

//main에서 출고완료메뉴 클릭시 기간(1주일) 검색  & 출고완료 날짜 검색
function regdt_main(mode,field){
	var todays = new Date();	
	
	var today = new Date(Date.parse(todays) - 1 * 1000 * 60 * 60 * 24);
	var tdays = new Date(Date.parse(todays) - 3 * 1000 * 60 * 60 * 24 );
	var week = new Date(Date.parse(todays) - 7 * 1000 * 60 * 60 * 24);
	var month = new Date(Date.parse(todays) - 30 * 1000 * 60 * 60 * 24);
	var tmonth = new Date(Date.parse(todays) - 90 * 1000 * 60 * 60 * 24);
	
	var regdt = document.getElementsByName(field);

	if (mode=='today'){
		regdt[0].value = today.getFullYear()+"-"+getRegdt(today.getMonth()+1)+"-"+getRegdt(today.getDate());
	}
	
	else if (mode=='tdays')
	{
		regdt[0].value = tdays.getFullYear()+"-"+getRegdt(tdays.getMonth()+1)+"-"+getRegdt(tdays.getDate());
	}
	
	else if (mode=='week')
	{
		regdt[0].value = week.getFullYear()+"-"+getRegdt(week.getMonth()+1)+"-"+getRegdt(week.getDate());
	}
	else if (mode=="month")
	{
		regdt[0].value = month.getFullYear()+"-"+getRegdt(month.getMonth()+1)+"-"+getRegdt(month.getDate());
	}
	else if (mode="tmonth")
	{
		regdt[0].value = tmonth.getFullYear()+"-"+getRegdt(tmonth.getMonth()+1)+"-"+getRegdt(tmonth.getDate());
	}
	
	else{
		regdt[0].value = month.getFullYear()+"-"+getRegdt(month.getMonth()+1)+"-"+getRegdt(month.getDate());
	}
	regdt[1].value = todays.getFullYear()+"-"+getRegdt(todays.getMonth()+1)+"-"+getRegdt(todays.getDate());
}

function regdt(mode,field){
	var today = new Date();
	var yesterday = new Date(Date.parse(today) - 1 * 1000 * 60 * 60 * 24);
	var tdays = new Date(Date.parse(today) - 3 * 1000 * 60 * 60 * 24);
	var week = new Date(Date.parse(today) - 7 * 1000 * 60 * 60 * 24);
	var month = new Date(Date.parse(today) - 30 * 1000 * 60 * 60 * 24);
	var tmonth = new Date(Date.parse(today) - 90 * 1000 * 60 * 60 * 24);
	var regdt = document.getElementsByName(field);

	if (mode=='yesterday'){
		regdt[0].value = yesterday.getFullYear()+"-"+getRegdt(yesterday.getMonth()+1)+"-"+getRegdt(yesterday.getDate());
	}else if (mode=='today')
	{
		regdt[0].value = today.getFullYear()+"-"+getRegdt(today.getMonth()+1)+"-"+getRegdt(today.getDate());
	}else if (mode=='tdays')
	{
		regdt[0].value = tdays.getFullYear()+"-"+getRegdt(tdays.getMonth()+1)+"-"+getRegdt(tdays.getDate());
	}else if (mode=='month')
	{
		regdt[0].value = month.getFullYear()+"-"+getRegdt(month.getMonth()+1)+"-"+getRegdt(month.getDate());
	}else if (mode=='tmonth')
	{
		regdt[0].value = tmonth.getFullYear()+"-"+getRegdt(tmonth.getMonth()+1)+"-"+getRegdt(tmonth.getDate());
	}else{
		regdt[0].value = week.getFullYear()+"-"+getRegdt(week.getMonth()+1)+"-"+getRegdt(week.getDate());
	}
	regdt[1].value = today.getFullYear()+"-"+getRegdt(today.getMonth()+1)+"-"+getRegdt(today.getDate());
}


function regdtOnlyOne(mode,field, date_type){
	var today = new Date();
	var yesterday = new Date(Date.parse(today) - 1 * 1000 * 60 * 60 * 24);
	var tdays = new Date(Date.parse(today) - 3 * 1000 * 60 * 60 * 24);
	var week = new Date(Date.parse(today) - 7 * 1000 * 60 * 60 * 24);
	var month = new Date(Date.parse(today) - 30 * 1000 * 60 * 60 * 24);
	var tmonth = new Date(Date.parse(today) - 90 * 1000 * 60 * 60 * 24);
	var regdt = document.getElementsByName(field);
	
	if (date_type)
	{
		var date_buton_type = document.getElementById("date_buton_type");
		if (date_buton_type != null && date_buton_type != undefined) date_buton_type.value = date_type;
	}

	if (mode=='yesterday'){
		regdt[0].value = yesterday.getFullYear()+"-"+getRegdt(yesterday.getMonth()+1)+"-"+getRegdt(yesterday.getDate());		
	}
	else if (mode=='today'){
		regdt[0].value = today.getFullYear()+"-"+getRegdt(today.getMonth()+1)+"-"+getRegdt(today.getDate());
	}
	else if (mode=='tdays'){
		regdt[0].value = tdays.getFullYear()+"-"+getRegdt(tdays.getMonth()+1)+"-"+getRegdt(tdays.getDate());
	}
	else if (mode=='month'){
		regdt[0].value = month.getFullYear()+"-"+getRegdt(month.getMonth()+1)+"-"+getRegdt(month.getDate());
	}
	else if (mode=='tmonth'){
		regdt[0].value = tmonth.getFullYear()+"-"+getRegdt(tmonth.getMonth()+1)+"-"+getRegdt(tmonth.getDate());
	}
	else if (mode=='all'){
		regdt[0].value = "";
	}
	else{
		regdt[0].value = week.getFullYear()+"-"+getRegdt(week.getMonth()+1)+"-"+getRegdt(week.getDate());
	}
}

function getRegdt(num){
	if (num<10) return '0'+num;
	else return num;
}
function totPage(){
	document.searchFm.submit();
}

/* pod 호출 layer */
function set_view_active(){
	var div = document.createElement("div");
	$j(div).css("height",$j(document).height()-5);
	$j(div).css("width","100%");
	$j(div).css("background","#FFFFFF");
	$j(div).css("left","0");
	$j(div).css("top","0");
	$j(div).css("display","block");
	$j(div).html("<table height='100%' width='100%'><tr><td align='center' style='font:bold 100pt 돋움;'>" + '<?echo _("편집중입니다")?>' + "</td><tr></table>");
	$j(div).fadeTo(0,0.5);
	$j(div).appendTo("body");
	$j(div).css("position","absolute");
	$j(div).css("z-index","9999999");
	return div;
}

function pods_editor(storageid,productid,optionid,podsid,mid){
	if (!optionid){
		optionid = 1;
	}
	if (arguments[5]=="28"){
		var ret = CallBusinessCardEditor(productid,optionid,podsid,mid,'','open','pods','','');
	} else {
		var ret = CallEditor30A(storageid,productid,optionid,podsid,mid,'','','pods','');
	}
	return ret;
}

/*** 앵커포커스 ***/
$j(function(){
	$j("a").focus(function(){
		this.blur();
	});
});

/*** 쿠키 ***/
function getCookie( name ){
	var nameOfCookie = name + "=";
	var x = 0;
	while ( x <= document.cookie.length ){
		var y = (x+nameOfCookie.length);
		if ( document.cookie.substring( x, y ) == nameOfCookie ) {
			if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 )
			endOfCookie = document.cookie.length;
			return unescape( document.cookie.substring( y, endOfCookie ) );
		}
		x = document.cookie.indexOf( " ", x ) + 1;
		if ( x == 0 ) break;
	}
	return "";
}

function setCookie (name, value) { 
	var argv = setCookie.arguments;
	var argc = setCookie.arguments.length;
	var expires = (2 < argc) ? argv[2] : null;
	var path = (3 < argc) ? argv[3] : null;
	var domain = (4 < argc) ? argv[4] : null;
	var secure = (5 < argc) ? argv[5] : false;	
	document.cookie = name + "=" + escape (value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + ((path == null) ? "" : ("; path=" + path)) + ((domain == null) ? "" : ("; domain=" + domain)) + ((secure == true) ? "; secure" : "");
}

function in_array(val,arr){
	for (var i=0;i<arr.length;i++){ if (arr[i]==val) return true; }
	return false;
}


/*** SMS 관련 함수 ***/
function chkByte(str){
	var length = 0;
	for(var i = 0; i < str.length; i++){
		if(escape(str.charAt(i)).length >= 4) length += 2;
		else if(escape(str.charAt(i)) != "%0D") length++;
	}
	return length;
}

function strCut(str, max_length){
	var str, msg;
	var length = 0;
	var tmp;
	var count = 0;
	length = str.length;

	for (var i = 0; i < length; i++){
		tmp = str.charAt(i);
		if(escape(tmp).length > 4) count += 2;
		else if(escape(tmp) != "%0D") count++;
		if(count > max_length) break;
	}
	return str.substring(0, i);
}

function chkSmsByte(obj){
	var str = obj.value;
	var span = obj.parentNode.getElementsByTagName('span')[0];
	span.innerHTML = chkByte(str);
	if (chkByte(str)>80){
		alert('<?echo _("80byte까지만 입력이 가능합니다")?>');
		obj.value = strCut(str,80);
		span.innerHTML = chkByte(obj.value);
	}
}

function chkTextByte(obj,length){
	var str = obj.value;
	if (chkByte(str)>length){
		alert(length+'<?echo _("byte까지만 입력이 가능합니다")?>');
		obj.value = strCut(str,length);
	}
}

/* 가격조회 ajax */
function get_goods_price(goodsno,optno,addopt,rfunction,cover_id){
	/*
	goodsno		: 상품번호
	optno		: 옵션번호
	addopt		: 추가옵션번호(복수의 경우 쉼표로 구분)
	rfunction	: 리턴함수
	*/
	$j.post("/module/indb.php", {mode:"get_optprice", goodsno:goodsno, optno:optno, addopt:addopt, cover_id:cover_id}, function(data){
		var ret = parseInt(data);
		eval(rfunction)(ret);
	});
}

/* 소비자가 ajax */
function get_goods_cprice(goodsno,optno,addopt,rfunction){
	/*
	goodsno		: 상품번호
	optno		: 옵션번호
	addopt		: 추가옵션번호(복수의 경우 쉼표로 구분)
	rfunction	: 리턴함수
	*/
	$j.post("/module/indb.php", {mode:"get_opt_cprice", goodsno:goodsno, optno:optno, addopt:addopt}, function(data){
		var ret = parseInt(data);
		eval(rfunction)(ret);
	});
}

/* 적립금 ajax */
function get_goods_reserve(goodsno,optno,addopt,rfunction){
	/*
	goodsno		: 상품번호
	optno		: 옵션번호
	addopt		: 추가옵션번호(복수의 경우 쉼표로 구분)
	rfunction	: 리턴함수
	*/
	$j.post("/module/indb.php", {mode:"get_opt_reserve", goodsno:goodsno, optno:optno, addopt:addopt}, function(data){
		var ret = parseInt(data);
		eval(rfunction)(ret);
	});
}

function xls_case(mode,query,addquery)
{
	popupLayer("/admin/xls/xls.case.php?mode="+mode+"&query="+query+"&addquery="+addquery,550,500);
}

function xls_case_details(mode,query,addquery)
{
	popup("/admin/xls/xls.case.details.php?mode="+mode+"&query="+query+"&addquery="+addquery,1400,800);
}

/*
//기존 사업자 등록번호 체크 함수 / 14.11.07 / kjm
function check_biz_regist_no(no){
	// 자릿수 체크
	if (no.length!=10){
		return false;
	}

	// 사업자등록번호를 증명하기 위한 키 배열
	var _key = new Array(1,3,7,1,3,7,1,3,5);

	// 사업자등록번호 끝자리를 제외하고 각 한자리씩 _key 와 * 연산하여 그 총합을 구함
	var sum = 0;
	for (var i=0;i<9;i++){
		sum += parseInt(no[i]) * _key[i];
		// 9번째의 값을 10으로 나누어 몫(버림연산)을 별도로 저장
		if (i==8){
			var _seed = Math.floor(parseInt(no[i]) * _key[i]/10);
		}
	}
	// 위에서 구한 총합과 _seed 를 sum 하고 이를 10 으로 나누어 나머지(버림연산)을 별도로 저장
	_seed = (_seed + sum)%10;
	// 10 - _seed 가 끝자리와 일치하면 유효
	if (10 - _seed == no[9]){
		return true;
	} else {
		return false;
	}
}
*/

//새로운 사업자 등록번호 체크 함수 / 14.11.07 / kjm
function check_biz_regist_no(vencod) {

	 var sum = 0;
	 var getlist =new Array(10);
	 var chkvalue =new Array("1","3","7","1","3","7","1","3","5");
	
	for(var i=0; i<10; i++) {
		 getlist[i] = vencod.substring(i, i+1);
	}

	 for(var i=0; i<9; i++) {
		 sum += getlist[i]*chkvalue[i]; 
	 }

	 sum = sum + parseInt((getlist[8]*5)/10);
	 sidliy = sum % 10;
	 sidchk = 0;

	 if(sidliy != 0) { 
		 sidchk = 10 - sidliy; 
	 }else { 
		 sidchk = 0; 
	 }

	 if(sidchk != getlist[9]) { return false; }

	 return true;
}

/*** 20140903 / kdk / 팝업윈도우 ***/
function popupWindowOpen(url){
	window.open(url);
}


//모바일 브루아져인치 체크		20150116	chunter
function is_mobile(location_url)
{
	var result = false;
	//var mobileInfo = new Array('Android', 'iPhone', 'iPod', 'BlackBerry', 'Windows CE', 'SAMSUNG', 'LG', 'MOT', 'SonyEricsson');
	var mobileKeyWords = new Array('Android', 'iPhone', 'iPod', 'BlackBerry', 'Windows CE', 'LG', 'MOT', 'SAMSUNG', 'SonyEricsson');
	
	for (var word in mobileKeyWords)
	{
    if (navigator.userAgent.match(mobileKeyWords[word]) != null)
    {
    	if (location_url != "")
      	location.href = location_url;
    	else 
    		result = true;
     	break;
    }
	}
	return result;

}
