<?
include_once dirname(__FILE__)."/../conf/language_locale.php";				//다국어 파일 처리. 다국어 처리하는곳에만 실제 처리 로직을 넣는다.
?>
//상품 리스트, 상세보기 페이지에서 호출
function CrossDomainMainOpenEditor(goodsno, podsno, vdp, tsid, tid, extra_data, addoptno, mode, cart_seq, pay_method, mid, ea){
	if(mode == null || mode != "outside") mode = "view";
	
  	if (typeof (vdp) == undefined || vdp == null) {
		vdp = "";
	}

  	if (typeof (tsid) == undefined || tsid == null) {
		tsid = "";
	}
	
	if (typeof (tid) == undefined || tid == null) {
		tid = "";
	}	
	
	if (typeof (mid) == undefined || mid == null) {
		mid = "";
	}
	if (typeof (cart_seq) == undefined || cart_seq == null) {
		cart_seq = "";
	}
	if (typeof (pay_method) == undefined || pay_method == null) {
		pay_method = "";
	}
	if (typeof (mid) == undefined || mid == null) {
		mid = "";
	}
	if (typeof (extra_data) == undefined || extra_data == null) {
		extra_data = "";
	}	

	if (!navigator.cookieEnabled){
		alert('<?echo _("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.")?>' + "\n" + '<?echo _("쿠키가 허용되어야만 정상적인 이용이 가능하십니다.")?>');
		return;
	}

	if (!form_chk(document.fmView)) return;

	var productid = $j("input[name=productid]").val();
	if (!productid || productid==0){
		productid = podsno;
	}
	var optionid = $j("input[name=podoptno]").val();
	if (!optionid){
		optionid = 1;
	}
	var optno = "";
	if ($j("select[name='optno[]']:last").length){
		optno = $j("select[name='optno[]']:last").val();
	}
	if(addoptno) addopt = addoptno;
	else {
		var addopt = [];
		var i = 0;
		$j("select[name='addopt[]']").each(function(){
			addopt[i] = $j(this).val();
			i++;
		});
		addopt = addopt.join(",");
	}

	//제작옵션(임포지션옵션) 2014.11.27 by kdk
	var impoptno = "";
	if ($j("select[name='impopt[]']:last").length){
		impoptno = $j("select[name='impopt[]']:last").val();
	}

	if(ea) ea = ea;
	else {
		if ($j("input[name=ea]").length){
			var ea = $j("input[name=ea]").val();
		} else {
			var ea = 1;
		}
	}
	
	//부모창 스크롤바 비 활성화 처리하기.	
	var html_dom = document.getElementsByTagName('html')[0];
  	var overflow = '';
  	if(html_dom.style.overflow=='') overflow='hidden';
  	else overflow='';
  	html_dom.style.overflow = overflow;

  	var st = document.documentElement.scrollTop;
  	if (typeof (st) == undefined || st == null || st == 0) {
  		st = document.body.scrollTop;
  	}
  
  	//iframe 100 로 전체창으로 변경
  	var divLayer = document.getElementById('editor_layer');  
  	divLayer.style.width = '100%';
  	divLayer.style.height = '100%';
  	divLayer.style.display = "block";
	divLayer.style.marginTop = st + 'px';
	//20160127 / minks / 크롬에서 실행했을 경우 아래 판매사이트 출력되지 않도록 수정
	var hiddenLayer = document.getElementById('hidden_editor_layer');
	if (hiddenLayer != undefined) hiddenLayer.style.display = "none";

  	//ajax로 호출해서 데이타 받아서 편집기 실행하기..
  	//20140821 / minks / 동일한 url로 접속할 경우 캐시 문제때문에 추가
	CrossDomainMainOpenEditorCallXHR("/module/popup_calleditor.php?mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&impoptno="+impoptno+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&templatesetid="+tsid+"&templateid="+tid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date())+"&extra_data="+extra_data+"&cart_seq="+cart_seq+"&pay_method="+pay_method+"&mid="+mid);
}

//장바구니에서 호출
function CrossDomainCartOpenEditor(goodsno,optno,storageid,productid,optionid,addopt,ea,vdp){  
  	if (typeof (vdp) == undefined || vdp == null) {
		vdp = "";
	}

	if (!navigator.cookieEnabled){
		alert('<?echo _("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.")?>' + "\n" + '<?echo _("쿠키가 허용되어야만 정상적인 이용이 가능하십니다.")?>');
		return;
	}
	
	//부모창 스크롤바 비 활성화 처리하기.	
	var html_dom = document.getElementsByTagName('html')[0];
  	var overflow = '';
  	if(html_dom.style.overflow=='') overflow='hidden';
  	else overflow='';
  	html_dom.style.overflow = overflow;
  	
  	var st = document.documentElement.scrollTop;
  	if (typeof (st) == undefined || st == null || st == 0) {
  		st = document.body.scrollTop;
  	}
  	
  	//iframe 100 로 전체창으로 변경
  	var divLayer = document.getElementById('editor_layer');  
  	divLayer.style.width = '100%';
  	divLayer.style.height = '100%';
  	divLayer.style.display = "block";
  	divLayer.style.marginTop = st + 'px';
  	//20160127 / minks / 크롬에서 실행했을 경우 아래 판매사이트 출력되지 않도록 수정
  	var hiddenLayer = document.getElementById('hidden_editor_layer');
	if (hiddenLayer != undefined) hiddenLayer.style.display = "none";

  	//var frame = document.getElementById('wpod_editor_frame');
  	////frame.src = "http://pods2.bluepod.kr/open_edit.aspx?cid=bpm&edit_code=1232131";  
  	//frame.src = "../goods/_frame_main_wpod_edit.php?mode=view&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&storageid="+storageid;
  	
	//ajax로 호출해서 데이타 받아서 편집기 실행하기..
	CrossDomainMainOpenEditorCallXHR("/goods/_frame_main_wpod_edit.php?mode=edit&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&storageid="+storageid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date()));
}

//관리자에서 호출 2014.06.16 by kdk
function CrossDomainAdminOpenEditor(goodsno,optno,storageid,productid,optionid,addopt,ea,vdp,mid){
  	if (typeof (vdp) == undefined || vdp == null) {
		vdp = "";
	}

	if (!navigator.cookieEnabled){
		alert('<?echo _("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.")?>' + "\n" + '<?echo _("쿠키가 허용되어야만 정상적인 이용이 가능하십니다.")?>');
		return;
	}
	
	//부모창 스크롤바 비 활성화 처리하기.	
	var html_dom = document.getElementsByTagName('html')[0];
  	var overflow = '';
  	if(html_dom.style.overflow=='') overflow='hidden';
  	else overflow='';
  	html_dom.style.overflow = overflow;
  	
  	var st = document.documentElement.scrollTop;
  	if (typeof (st) == undefined || st == null || st == 0) {
  		st = document.body.scrollTop;
  	}
  	
  	//iframe 100 로 전체창으로 변경
  	var divLayer = document.getElementById('editor_layer');  
  	divLayer.style.width = '100%';
  	divLayer.style.height = '100%';
  	divLayer.style.display = "block";
  	divLayer.style.marginTop = st + 'px';
  	//20160127 / minks / 크롬에서 실행했을 경우 아래 판매사이트 출력되지 않도록 수정
  	var hiddenLayer = document.getElementById('hidden_editor_layer');
	if (hiddenLayer != undefined) hiddenLayer.style.display = "none";

	//alert(opener.location.href);

	//ajax로 호출해서 데이타 받아서 편집기 실행하기..
	CrossDomainMainOpenEditorCallXHR("/goods/_frame_main_wpod_edit.php?mode=edit&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&rurl=&admin_mode=Y&storageid="+storageid+"&mid="+escape(mid)+"&editdate="+escape(new Date()));	
}

function CrossDomainMainOpenEditorCallXHRResult(xhr)
{	
	//$("#editor_layer").append(xhr.responseText);
	var div = document.getElementById('editor_layer');
	div.innerHTML = div.innerHTML + xhr.responseText;

	var frameForm = document.wpod_editor_form;
  	frameForm.target="wpod_editor_frame";
  	frameForm.submit();	
}

function CrossDomainMainOpenEditorCallXHR(url) {
	if(window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			CrossDomainMainOpenEditorCallXHRResult(xmlhttp);
		}
	};
	
	xmlhttp.open("GET", url, false);
	xmlhttp.send();
   	    	
	//xmlhttp.open("POST", url, true);     
	//xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8");
	//xmlhttp.setRequestHeader("Cache-Control","no-cache, must-revalidate");
	//xmlhttp.setRequestHeader("Pragma","no-cache");
	//xmlhttp.send(param);
}
