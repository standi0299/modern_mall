var Class = {
  create: function() {
    return function() {
      this.initialize.apply(this, arguments);
    }
  }
}

/*** 문자열 치환 ***/
function replaceAll(str, searchStr, replaceStr) {
    return str.split(searchStr).join(replaceStr);
}

function $(o){ return document.getElementById(o); }

/*** 이벤트 추가 ***/
function bindEvent(element, event, callback){
	if (element.addEventListener) element.addEventListener(event, callback, false); 
	else if (element.attachEvent) element.attachEvent("on" + event, callback);
}

function getInnerText(o){
  return o.textContent ? o.textContent : o.innerText;
}

function evalJSON(str){
	return eval('(' + str + ')');
}


//####################################
// .net 4.0 설치여부 확인			20150608		chunter
function checkFrameWork(runtimeVersion)
{
	if (runtimeVersion == "")	runtimeVersion = "4.0.0";
	var checkClient = false;
		
	if (HasRuntimeVersion(runtimeVersion, false) || (checkClient && HasRuntimeVersion(runtimeVersion, checkClient)))
  	{
  		//프레임 워크가 설치되어 있다.    
    	//BootstrapperSection.style.display = "none";
    	return true;
  	} else
	  	return false;  
}


function HasRuntimeVersion(v, c)
{
  	var va = GetVersion(v);
  	var i;

	var browser = checkBrowser();
	if (browser.isIE)
	{	  
	  	var a = navigator.userAgent.match(/\.NET CLR [0-9.]+/g);
	  
	  	if(va[0]==4)
	    	a = navigator.userAgent.match(/\.NET[0-9.]+E/g);
	  	if (c)
	  	{
	    	a = navigator.userAgent.match(/\.NET Client [0-9.]+/g);
	    	if (va[0]==4)
	       	a = navigator.userAgent.match(/\.NET[0-9.]+C/g);
	  	}
	  
	  	if (a != null)
			for (i = 0; i < a.length; ++i)
	      	if (CompareVersions(va, GetVersion(a[i])) <= 0)
					return true;
	  	return false;
	} else {
		return true;
	}
}
function GetVersion(v)
{
  var a = v.match(/([0-9]+)\.([0-9]+)\.([0-9]+)/i);
  if(a==null)
     a = v.match(/([0-9]+)\.([0-9]+)/i);
  return a.slice(1);
}
function CompareVersions(v1, v2)
{
   if(v1.length>v2.length)
   {
      v2[v2.length]=0;
   }  
   else if(v1.length<v2.length)
   {
      v1[v1.length]=0;
   }

  for (i = 0; i < v1.length; ++i)
  {
    var n1 = new Number(v1[i]);
    var n2 = new Number(v2[i]);
    if (n1 < n2)
      return -1;
    if (n1 > n2)
      return 1;
  }
  return 0;
}
//####################################


//클릭원스 어플 실행(유치원 시즌2) / 15.04.06 / kjm
function uploadAppExecuteClickOnce(domain, param, storage_date_notice, storage_size_notice) 
{
	//스토리지 사용기간에 따른 메세지창
	if (storage_date_notice == "Y")
		alert(tls("웹하드 사용기간이 만료되었습니다. 본사에 문의 하세요."));
	//스토리지 사용량에 따른 메세지
	else if (storage_size_notice == "Y")
		alert(tls("웹하드 사용량이 초과되었습니다. 본사에 문의 하세요."));
	else
	{
		//net fw 4.0 설치여부 체크한다.			20150609		chunter		
		if (checkFrameWork('4.0.0'))
		{
			if (jsLocale == 'zh_CN')
			{
				location.href="http://download.bluepoto.com/clickonce_kids/Oasis_Kids_cha.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
			}
			else
			{
				//location.href="http://podmanage.bluepod.kr/ilark/pretty/Oasis_Kids.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
				location.href="http://studio.ilark.co.kr/clickonce_kids/Oasis_Kids.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
		 		//SetActiveLoaderInit("Oasis_CustomizeOrder.exe http://studio.ilark.co.kr/user/application_run.aspx" + param);
		 	}
		} else {
			//설치 체크하는 applicatino 으로 링크를 바꾼다.		20150609		chunter
			//location.href="http://studio.ilark.co.kr/clickonce_launcher/Oasis_kids_launcher.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
			//설치 안내창 띄우기.
			popup('/service/pop_netfw_install_help.php','660px', '570px', 'yes', 'yes','install_help');
		}
		//location.href="http://studio.ilark.co.kr/clickonce_launcher/Oasis_kids_launcher.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
	 }
}


//클릭원스 어플 실행(2016개발) / 16.08.02 / kjm
function uploadAppExecuteClickOnce_s2(domain, param, storage_date_notice, storage_size_notice) 
{
	//스토리지 사용기간에 따른 메세지창
	if (storage_date_notice == "Y")
		alert(tls("웹하드 사용기간이 만료되었습니다. 본사에 문의 하세요."));
	//스토리지 사용량에 따른 메세지
	else if (storage_size_notice == "Y")
		alert(tls("웹하드 사용량이 초과되었습니다. 본사에 문의 하세요."));
	else
	{
		//net fw 4.0 설치여부 체크한다.			20150609		chunter		
		if (checkFrameWork('4.0.0'))
		{
			if (jsLocale == 'zh_CN')
			{
				location.href="http://download.bluepoto.com/clickonce_kids/Oasis_Kids_cha.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
			}
			else
			{
				//location.href="http://podmanage.bluepod.kr/ilark/pretty/Oasis_Kids.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
				location.href="http://studio.ilark.co.kr/clickonce_v2/Oasis_kids_New.application?param1=http://" + domain + "/_pretty/app_s2_config.php&" + param;
		 		//SetActiveLoaderInit("Oasis_CustomizeOrder.exe http://studio.ilark.co.kr/user/application_run.aspx" + param);
		 	}
		} else {
			//설치 체크하는 applicatino 으로 링크를 바꾼다.		20150609		chunter
			//location.href="http://studio.ilark.co.kr/clickonce_launcher/Oasis_kids_launcher.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
			//설치 안내창 띄우기.
			popup('/service/pop_netfw_install_help.php','660px', '570px', 'yes', 'yes','install_help');
		}
		//location.href="http://studio.ilark.co.kr/clickonce_launcher/Oasis_kids_launcher.application?param1=http://" + domain + "/_pretty/app_config.php&" + param;
	 }
}


//exe 분류 (유치원 시즌2) / 15.10.28		chunter
function uploadAppExecuteExe(param, storage_date_notice, storage_size_notice, failCb) 
{
	//스토리지 사용기간에 따른 메세지창
	if (storage_date_notice == "Y")
		alert(tls("웹하드 사용기간이 만료되었습니다. 본사에 문의 하세요."));
	//스토리지 사용량에 따른 메세지
	else if (storage_size_notice == "Y")
		alert(tls("웹하드 사용량이 초과되었습니다. 본사에 문의 하세요."));
	else {
		$.ajax({
			type : "POST",
			url : "indb.php",
			data : param,

			success : function(html) {
				checkExeAppInstall(html, failCb);

			/*
			try{
				form = document.sendurl;
				form.action = html;
				form.target = "hiddenIfrm";
				form.submit();
			} catch (e) {
				alert('adfa');
			}
			*/
			//window.location = html;
			}
		});
	}
}

/*** 플래시 보정 ***/
function embed(src,width,height,vars){
	/*
	document.write('\
	<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="'+width+'" HEIGHT="'+height+'">\
	<PARAM NAME=movie VALUE="'+src+'">\
	<PARAM NAME=quality VALUE=high>\
	<PARAM NAME=wmode VALUE=transparent>\
	<PARAM NAME=bgcolor VALUE=#FFFFFF>\
	<PARAM NAME=allowScriptAccess VALUE="always">\
	<param name=flashvars value="' + vars + '">\
	<EMBED src="'+src+'" quality=high bgcolor=#FFFFFF wmode=transparent allowScriptAccess=always WIDTH="'+width+'" HEIGHT="'+height+'" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer" flashvars="' + vars + '"></EMBED>\
	</OBJECT>\
	');
	*/
	document.write('\
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" class="F1280898684667534794_undefined" style="position: relative ! important;" id="'+src+'" align="middle" height="'+height+'" width="'+width+'">\
	<param name="movie" value="'+src+'" />\
	<param name="quality" value="high" />\
	<param name="bgColor" value="#FFFFFF" />\
	<param name="allowScriptAccess" value="always" />\
	<param name="wmode" value="transparent" />\
	<param name="menu" value="false" />\
	<param name="flashVars" value="'+vars+'" />\
	<EMBED src="'+src+'" quality=high bgcolor=#FFFFFF wmode=transparent allowScriptAccess=always WIDTH="'+width+'" HEIGHT="'+height+'" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer" flashvars="' + vars + '"></EMBED>\
	</object>\
	');
}

/*** 콤마 출력 ***/
function comma(x){
	var temp = "", co = 3;
	var x = String(uncomma(x));
	var num_len = x.length;
	while (num_len>0){
		num_len = num_len - co;
		if (num_len<0){
			co = num_len + co;
			num_len = 0;
		}
		temp = ","+x.substr(num_len,co)+temp;
	}
	return temp.substr(1);
}

/*** 콤마 미출력 ***/
function uncomma(x){
	var reg = /(,)*/g;
	x = parseInt(String(x).replace(reg,""),10);
	return (isNaN(x)) ? 0 : x;
}

/*** 오브젝트 위치 ***/
function get_objectTop(obj) {
	if (obj.offsetParent==document.body || obj==document.body) return obj.offsetTop;
	else return obj.offsetTop + get_objectTop(obj.offsetParent);
}

function get_objectLeft(obj) {
	if (obj.offsetParent==document.body) return obj.offsetLeft;
	else return obj.offsetLeft + get_objectLeft(obj.offsetParent);
}

function vLayer(obj,mode) {
	if (typeof(obj)!="object") obj = _ID(obj);
	if (typeof(obj)=="undefined" || obj==null) return;
	if (!mode) obj.style.display = (obj.style.display!="block") ? "block" : "none";
	else obj.style.display = mode;
}

function _ID(id) {
	return document.getElementById(id);
}

function chkBox(El) {
	var chk;
	$j("[name="+El+"]").each(function(){
		if ($j(this).attr("checked")==true) chk = 1;
	});
	
	$j("[name="+El+"]").each(function(){
		if($j(this).attr("disabled")==false){
			if (chk) $j(this).attr("checked",false);
			else $j(this).attr("checked",true);
		}
	});
}

function chkBox_kids(El) {
	var chk;
	$j("[name="+El+"]").each(function(){
		if ($j(this).attr("checked")==true) chk = 1;
	});
	
	$j("[name="+El+"]").each(function(){
		if($j(this).attr("disabled")==false){
			if (chk) $j(this).attr("checked",false);
			else $j(this).attr("checked",true);
		}
	});
}

//rev값이 1이면 해제만 2이면 선택만 / 15.06.29 / kjm
function chkBox_pretty(El, rev) {
	var chk;
	$j("[name="+El+"]").each(function(){
		if ($j(this).attr("checked")==true) chk = 1;
	});
	
	$j("[name="+El+"]").each(function(){
		if($j(this).attr("disabled")==false){
			if (rev == 1) $j(this).attr("checked",false);
			else $j(this).attr("checked",true);
		}
	});
}

function resizeHeight(obj) {
	var frbody = obj.contentWindow.document.body;
	var height = frbody.scrollHeight + ( frbody.offsetHeight - frbody.clientHeight + 30);
	//$j(obj).css("height",height);
	$j(obj).height(height);
}

//SNS 로그인 연동 / 2017.02.23 / kdk	
function fnSnsLoginPop(snsCode) {
	//var gsWin = window.open('about:blank','snsLoginOpen','width=500,height=500');

	//$j("input[name=sns_code]").val(snsCode);
	//$j("#login").attr("action","/_auth/connectSns.php");
	//$j("#login").attr("method","post");	
	//$j("#login").attr("target","snsLoginOpen");
	//$j("#login").submit();
	
	var url = "/_oauth/connectSns.php?sns_code="+snsCode;
	//alert(url);
	popupLayer(url,"","","",1);	
}

//화면 불투명 마스크 처리 & 문구 출력
function wrapWindowByMask() {
	//화면의 높이와 너비를 구한다.
	var maskHeight = $j(document).height();
	//var maskWidth = $j(document).width();
	var maskWidth = window.document.body.clientWidth;

  	var mask = "<div id='mask' style='position:absolute; z-index:9000; background-color:#000000; display:none; left:0; top:0;'></div>";
  	var loadingImg = '';

  	loadingImg += "<div id='loadingImg' style='position:absolute; left:42%; top:160%; display:none; z-index:10000;'>";
  	loadingImg += "</div>";

  	//화면에 레이어 추가 
  	$j('body').append(mask);
  	$j('body').append(loadingImg);

  	//마스크의 높이와 너비를 화면 것으로 만들어 전체 화면을 채운다.
  	$j('#mask').css({
		'width' : maskWidth,
		'height': maskHeight,
		'opacity' : '0.3'
  	});

  	//마스크 표시
  	$j('#mask').show();

  	//로딩중 이미지 표시
  	$j('#loadingImg').show();
}

function set_goods_like(goodsno, element){
	$j.ajax({
		type : "POST",
		url : "/goods/indb.php",
		data : "mode=goods_like&goodsno="+goodsno+"&element="+element,
		success : function(data) {
			if(data == "not_mid"){
				alert("로그인을 해주시기 바랍니다.");
				$j('.btn_wish').removeClass("on");
			} else {
				if(data == "reload")
					window.location.reload();
				else if (element == "view"){
					$j('#top_like').text(data);
					$j('#big_like').text(data);
					$j('#mem_like').text(data);
				}
			}
		}
	});
}

function form_encode_submit(obj){	
   if(form_chk(obj))
   {
   	  if(typeof(obj.mid) != "undefined"){
   	  	 obj.mid_en.value = Base64.encode(obj.mid.value);
      	 obj.mid.value = '';
   	  }
      if(typeof(obj.password) != "undefined"){
         obj.password_en.value = Base64.encode(obj.password.value);
         obj.password.value = '';
      }
      if(typeof(obj.password2) != "undefined"){
         obj.password2_en.value = Base64.encode(obj.password2.value);
         obj.password2.value = '';
      }
      if(typeof(obj.old_password) != "undefined"){
      	 obj.old_password_en.value = Base64.encode(obj.old_password.value);
      	 obj.old_password.value = '';
      }
      if(typeof(obj.email) != "undefined"){
      	 obj.email_en.value = Base64.encode(obj.email.value);
      	 obj.email.value = '';
      }
      return true;
   }
   else return false;
}

function move_goods_view(goodsno, catno){
	var add_catno = "";
	if(typeof(catno) != "undefined") add_catno = "&catno="+catno;
	window.location.href = "/goods/view.php?goodsno="+goodsno+add_catno;
}

function editTitle(storageid,id,mode){
	var storageid = storageid;
	var title = document.getElementById(id).value;

	if(!title){
		alert(tls("내용이 없습니다."));
		return;
	}

	var word = "";	
	if(mode == "modify") word = tls("수정");
	else word = tls("추가");

	if(confirm(tls("편집타이틀을")+' '+word+tls("하시겠습니까?")) == true){
		location.href = "?mode=edittitle&storageid="+storageid+"&title="+title;
	} else {
		return;
	}
}