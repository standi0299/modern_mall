
//편집기 호출
function PodsCallEditor(pods_use, podskind, podsno, tsid, tid, mode, pod_signed) {
	
	if (!navigator.cookieEnabled){
		//alert({=__java("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.")}+"\n"+{=__java("쿠키가 허용되어야만 정상적인 이용이 가능하십니다.")});
		alert(_tsl("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.") + "\n" + _tsl("쿠키가 허용되어야만 정상적인 이용이 가능하십니다."));
		return;
	}

	if (!form_chk(document.fmView)) return;

	var goodsno = $j("input[name=goodsno]").val();

	//alert(podsno);
	/*
	var productid = podsno;
	if ($j("select[name='optno[]'] option:selected").attr("productid")) {//기존 옵션(상품코드 처리)
		var attr = $j("select[name='optno[]'] option:selected").attr("productid");

		productid = $j("input[name=productid]").val();
		if (!productid || productid==0){
			productid = podsno;
		}
	}
	*/
	
	productid = $j(":input:radio[name=optno[]_sub]:checked").attr("productid");
	
	if (typeof(productid) == "undefined" || !productid || productid==0){
		productid = $j(":input:radio[name=optno[]]:checked").attr("productid");

		if (typeof(productid) == "undefined" || !productid || productid==0){
			productid = podsno;
		}
	}

	var optionid = $j(":input:radio[name=optno[]_sub]:checked").attr("podoptno");
	
	if (typeof(optionid) == "undefined" || !optionid || optionid==0){
		optionid = $j(":input:radio[name=optno[]]:checked").attr("podoptno");
		if (typeof(optionid) == "undefined" || !optionid || optionid==0){
			optionid = 1;
		}
	}

	var optno = "";
	
	optno = $j(":input:radio[name=optno[]_sub]:checked").val();
	if(typeof(optno) == "undefined") optno = $j(":input:radio[name=optno[]]:checked").val();
	if(typeof(optno) == "undefined") optno = '';

	var addopt = [];
	var i = 0;
	$j("[id^='addopt_']").each(function(){
		if($j(this).is(':checked') == true){
			addopt[i] = $j(this).val();
			i++;
		}
	});
	addopt = addopt.join(",");
	//alert(addopt);

	if ($j("input[name=ea]").length){
		var ea = $j("input[name=ea]").val();
	} else {
		var ea = 1;
	}
	
	var cover_id = "";
	if ($j("input[name=cover_id").length){
		var cover_id = $j("input[name=cover_id]").val();
	}
	/*
	if ($j("select[name='cover_id']:last").length){
		cover_id = $j("select[name='cover_id']:last").val();
	}
	*/
	
	var startdate = "";
	if ($j("select[name=startdate]").length && podskind == "3060") {
		var startdate = $j("select[name=startdate]").val();
	}
	
	//form의 productid값을 옵션이나 편집기에 설정된 값으로 변경해준다. / 17.01.18 / kjm
	$j("input[name=productid]").val(productid);

	if ( $j("input[name=pods_use]").length > 0 ) {
		$j("input[name=pods_use]").val(pods_use);
		$j("input[name=podsno]").val(podsno);
		$j("input[name=podskind]").val(podskind); 
	}
	else {
		$j("form[name=fmView]").append("<input type='hidden' name='pods_use' value='"+ pods_use +"' />"); 
		$j("form[name=fmView]").append("<input type='hidden' name='podsno' value='"+ podsno +"' />");
		$j("form[name=fmView]").append("<input type='hidden' name='podskind' value='"+ podskind +"' />");	
	}

  	if (typeof (mode) == undefined || mode == null || mode == "") mode = "view";
	if ($j("input[name=skin]").val() == "pod_group") mode = "order";

	var url = "../module/popup_calleditor_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&templatesetid="+tsid+"&templateid="+tid+"&cover_id="+cover_id+"&startdate="+startdate;

  	if (typeof (pod_signed) != undefined && pod_signed != null && pod_signed != "") {
  		url += "&pod_signed=" + pod_signed;
  	}

  	//패키지 상품 관련.
  	var package_mode = $j("input[name=package_mode]").val();
  	if (typeof (package_mode) != undefined && package_mode != null && package_mode != "") {
  		url += "&package_mode=" + package_mode;
  	}  	
  	var cartno = $j("input[name=cartno]").val();
  	if (typeof (cartno) != undefined && cartno != null && cartno != "") {
  		url += "&cartno=" + cartno;
  	}
  	
	//alert(url);
	popupLayer(url,"","","",1);

	return;
}

//wpod 편집기 호출
function PodsCallWpodEditor(pods_use, podskind, podsno, vdp, tsid, tid, pod_signed, mode, editor_type) {
//function CrossDomainMainOpenEditor(goodsno, podsno, vdp, tsid, tid){
  	if (typeof (vdp) == undefined || vdp == null) {
		vdp = "";
	}

  	if (typeof (tsid) == undefined || tsid == null) {
		tsid = "";
	}

	if (typeof (tid) == undefined || tid == null) {
		tid = "";
	}

	if (typeof (editor_type) == undefined || editor_type == null) {
		editor_type = "";
	}

	if (!navigator.cookieEnabled){
		alert(_tsl("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.") + "\n" + _tsl("쿠키가 허용되어야만 정상적인 이용이 가능하십니다."));
		return;
	}

	//WPOD 편집기 최소 해상도를 1024로 줄이고, 사용자 화면이 1024 x 768 보다 작은 경우 웹에서 경고 메시지 노출
	if($j(window).width() < 1024 || $j(window).height() < 768) {
		alert(_tsl("현재 고객님의 브라우저 화면 해상도가 너무 작습니다.") + "\n" + _tsl("화면 해상도가 최소 1024 x 768 이상 되어야만 정상적인 이용이 가능하십니다."));
	}

	if (!form_chk(document.fmView)) return;

	var goodsno = $j("input[name=goodsno]").val();

	var productid = podsno;
	$j("input[name=productid]").val(productid);
	
	var optionid = $j("input[name=podoptno]").val();
	if (!optionid){
		optionid = 1;
	}
	
	var optno = "";
	if ($j("select[name='optno[]']:last").length){
		optno = $j("select[name='optno[]']:last").val();
	}
	else if ($j("input[name=optno]").length) {
		optno = $j("input[name=optno]").val();
		//console.log(optno);
	}
	
	var addopt = [];
	if ($j("select[name='addopt[]']:last").length){
		var i = 0;
		$j("select[name='addopt[]']").each(function(){
			addopt[i] = $j(this).val();
			i++;
		});
		addopt = addopt.join(",");
	}
	else if ($j("input[name=addopt]").length) {
		addopt = $j("input[name=addopt]").val();
		//console.log(addopt);
	}	

	//제작옵션(임포지션옵션) 2014.11.27 by kdk
	var impoptno = "";
	if ($j("select[name='impopt[]']:last").length){
		impoptno = $j("select[name='impopt[]']:last").val();
	}

	if ($j("input[name=ea]").length){
		var ea = $j("input[name=ea]").val();
	} else {
		var ea = 1;
	}

	var cover_id = "";	
	if ($j("input[name=cover_id").length){
		var cover_id = $j("input[name=cover_id]").val();
	}

	if ( $j("input[name=pods_use]").length > 0 ) { 
		$j("input[name=pods_use]").val(pods_use);
		$j("input[name=podsno]").val(podsno);
		$j("input[name=podskind]").val(podskind); 
	} else {
		$j("form[name=fmView]").append("<input type='hidden' name='pods_use' value='"+ pods_use +"' />"); 
		$j("form[name=fmView]").append("<input type='hidden' name='podsno' value='"+ podsno +"' />");
		$j("form[name=fmView]").append("<input type='hidden' name='podskind' value='"+ podskind +"' />");	
	}
	
	if(editor_type != "web"){
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

			//스크롤이동.
			window.scrollTo( 0, 0 );

			//iframe 100 로 전체창으로 변경
			var divLayer = document.getElementById('editor_layer');
			divLayer.style.width = '100%';
			divLayer.style.height = '100%';
			divLayer.style.display = "block";
		//divLayer.style.marginTop = st + 'px';

		//20160127 / minks / 크롬에서 실행했을 경우 아래 판매사이트 출력되지 않도록 수정
		var hiddenLayer = document.getElementById('hidden_editor_layer');
		if (hiddenLayer != undefined) hiddenLayer.style.display = "none";
	}

	if (typeof (mode) == undefined || mode == null || mode == "") mode = "view";
	if ($j("input[name=skin]").val() == "pod_group") mode = "order";

  	//ajax로 호출해서 데이타 받아서 편집기 실행하기..
  	//20140821 / minks / 동일한 url로 접속할 경우 캐시 문제때문에 추가
  	  	
  	//미오디오용 피규어 에디터. 20180905 kdk
  	if (podskind == "99999") {
  		var url = "/miodio/figure_editor.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&impoptno="+impoptno+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&templatesetid="+tsid+"&templateid="+tid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date());
  	}
  	else {
  		// var url = "/module/_frame_main_wpod_edit_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&impoptno="+impoptno+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&templatesetid="+tsid+"&templateid="+tid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date());	

			if(editor_type == "web")
				var url = "/module/_frame_main_wpod_edit_v3.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&impoptno="+impoptno+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&templatesetid="+tsid+"&templateid="+tid+"&editdate="+escape(new Date())+"&editor_type="+editor_type+"&cover_id="+cover_id;
			else
				var url = "/module/_frame_main_wpod_edit_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&impoptno="+impoptno+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&templatesetid="+tsid+"&templateid="+tid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date())+"&editor_type="+editor_type;
  	}
  	
  	if (typeof (pod_signed) != undefined && pod_signed != null && pod_signed != "") {
  		url += "&pod_signed=" + pod_signed;
  	}

  	//패키지 상품 관련.
  	var package_mode = $j("input[name=package_mode]").val();
  	if (typeof (package_mode) != undefined && package_mode != null && package_mode != "") {
  		url += "&package_mode=" + package_mode;
  	}
  	var cartno = $j("input[name=cartno]").val();
  	if (typeof (cartno) != undefined && cartno != null && cartno != "") {
  		url += "&cartno=" + cartno;
  	}

  //alert(url);
	if(editor_type == "web") {
		var version = GetVersionOfIE();
		
		if ( version != "N/A" ) {
			var popup = window.open(url,'_blank', 'height=' + screen.height + ',width=' + screen.width + ',fullscreen=yes,resizable=yes');
			//popuplayer((url,'_blank', 'height=' + screen.height + ',width=' + screen.width + ',fullscreen=yes,resizable=yes');
		
			if(editor_type == "web") {
				if (popup == null) alert(tls("팝업 차단을 해제해 주세요"));
			}
		}
		else {
				var height = screen.height - 105;
				var width = screen.width - 20;
				var popup = window.open(url,'_blank', 'height=' + height + ',width=' + width + ',fullscreen=yes,resizable=yes');
		
			if(editor_type == "web") {
				if (popup == null) alert(tls("팝업 차단을 해제해 주세요"));
			}
		}
  }
  else PodsCallXHR(url);
}

//장바구니(편집리스트)에서 호출
function PodsCallWpodEditorCart(pods_use, podskind, podsno, goodsno,optno,storageid,productid,optionid,addopt,ea,vdp,mode,adminmode,mid,cover_id,editor_type){
	if (!navigator.cookieEnabled){
		alert(_tsl("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.") + "\n" + _tsl("쿠키가 허용되어야만 정상적인 이용이 가능하십니다."));
		return;
	}

	if(editor_type != "web"){
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
	}
	//ajax로 호출해서 데이타 받아서 편집기 실행하기..
	//alert("/goods/_frame_main_wpod_edit_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode=edit&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&storageid="+storageid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date()));

	//PodsCallXHR("/module/_frame_main_wpod_edit_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&admin_mode="+adminmode+"&mid="+mid+"&storageid="+storageid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date()));

  	//미오디오용 피규어 에디터. 20180905 kdk
  	if (podskind == "99999") {
  		var url = "/miodio/figure_editor.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&admin_mode="+adminmode+"&mid="+mid+"&storageid="+storageid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date());
  	}
  	else {
  		// var url = "/module/_frame_main_wpod_edit_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&admin_mode="+adminmode+"&mid="+mid+"&storageid="+storageid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date());	

			if(editor_type == "web")
				var url = "/module/_frame_main_wpod_edit_v3.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&admin_mode="+adminmode+"&mid="+mid+"&storageid="+storageid+"&editdate="+escape(new Date())+"&editor_type="+editor_type+"&cover_id="+cover_id;
			else
					var url = "/module/_frame_main_wpod_edit_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&ea="+ea+"&vdp="+vdp+"&admin_mode="+adminmode+"&mid="+mid+"&storageid="+storageid+"&rurl="+escape(location.href)+"&editdate="+escape(new Date());
  	}

  	//패키지 상품 관련.
  	var package_mode = $j("input[name=package_mode]").val();
  	if (typeof (package_mode) != undefined && package_mode != null && package_mode != "") {
  		url += "&package_mode=" + package_mode;
  	}
  	var cartno = $j("input[name=cartno]").val();
  	if (typeof (cartno) != undefined && cartno != null && cartno != "") {
  		url += "&cartno=" + cartno;
  	}

  	//alert(url);
		if(editor_type == "web") {
			var version = GetVersionOfIE();
			
				if ( version != "N/A" ) {
				window.open(url,'_blank', 'height=' + screen.height + ',width=' + screen.width + ',fullscreen=yes,resizable=yes');
				//popuplayer((url,'_blank', 'height=' + screen.height + ',width=' + screen.width + ',fullscreen=yes,resizable=yes');
			}
			else {
				var height = screen.height - 105;
				var width = screen.width - 20;
				window.open(url,'_blank', 'height=' + height + ',width=' + width + ',fullscreen=yes,resizable=yes');
			}
		}
		else PodsCallXHR(url);
}

//결과처리
function PodsCallXHRResult(xhr)
{
	var div = document.getElementById('editor_layer');
	div.innerHTML = div.innerHTML + xhr.responseText;

	//var frameForm = document.wpod_editor_form;
	//frameForm.target="wpod_editor_frame";
  	//frameForm.submit();

	$j("form[name=wpod_editor_form]").attr("target", "wpod_editor_frame");
	$j("form[name=wpod_editor_form]").submit(); 
}

//결과처리
function PodsCallXHR(url) {
	if(window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			PodsCallXHRResult(xmlhttp);
		}
	};

	xmlhttp.open("GET", url, false);
	xmlhttp.send();
}

//수정하기 호출 (장바구니,편집리스트)
//복수 편집기 처리 pods_use, podskind, podsno 장바구니 편집기 호출 함수 수정 2016.03.16 by kdk
function PodsCallEditorUpdate(pods_use,podskind, podsno,goodsno,optno,storageid,productid,optionid,addopt,ea,vdp,mode,adminmode,payno,ordno,ordseq,mid,cover_id,editor_type){
  	if (typeof (vdp) == undefined || vdp == null) vdp = "";
  	if (typeof (mode) == undefined || mode == null) mode = "edit";
  	if (typeof (adminmode) == undefined || adminmode == null) adminmode = "";
  	if (typeof (payno) == undefined || payno == null) payno = "";
  	if (typeof (ordno) == undefined || ordno == null) ordno = "";
  	if (typeof (ordseq) == undefined || ordseq == null) ordseq = "";
  	if (typeof (mid) == undefined || mid == null) mid = "";
  	if (typeof (cover_id) == undefined || cover_id == null) cover_id = "";
  	if (typeof (editor_type) == undefined || editor_type == null) editor_type = "";

	if (!optionid){
		optionid = 1;
	}

	if(pods_use == "3") {
		PodsCallWpodEditorCart(pods_use, podskind, podsno,goodsno,optno,storageid,productid,optionid,addopt,ea,vdp,mode,adminmode,mid);
		return;
	} else if(pods_use == "2" && editor_type == "web"){
		PodsCallWpodEditorCart(pods_use, podskind, podsno,goodsno,optno,storageid,productid,optionid,addopt,ea,vdp,mode,adminmode,mid,cover_id,editor_type);
		return;
	} else {
		//"../module/popup_calleditor_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&addopt="+addopt+"&productid="+productid+"&optionid="+optionid+"&storageid="+storageid+"&payno="+payno+"&ordno="+ordno+"&ordseq="+ordseq+"&admin_mode="+adminmode;
		//popupLayer("/module/popup_calleditor_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&productid="+productid+"&optionid="+optionid+"&addopt="+addopt+"&storageid="+storageid+"&payno="+payno+"&ordno="+ordno+"&ordseq="+ordseq+"&adminmode="+adminmode+"&mid="+mid,"","","",1);

		var url = "/module/popup_calleditor_v2.php?pods_use="+pods_use+"&podskind="+podskind+"&podsno="+podsno+"&mode="+mode+"&goodsno="+goodsno+"&optno="+optno+"&productid="+productid+"&optionid="+optionid+"&addopt="+addopt+"&storageid="+storageid+"&payno="+payno+"&ordno="+ordno+"&ordseq="+ordseq+"&adminmode="+adminmode+"&mid="+mid+"&cover_id="+cover_id;

	  	//패키지 상품 관련.
	  	var package_mode = $j("input[name=package_mode]").val();
	  	if (typeof (package_mode) != undefined && package_mode != null && package_mode != "") {
	  		url += "&package_mode=" + package_mode;
	  	}
	  	var cartno = $j("input[name=cartno]").val();
	  	if (typeof (cartno) != undefined && cartno != null && cartno != "") {
	  		url += "&cartno=" + cartno;
	  	}

		//alert(url);
		popupLayer(url,"","","",1);

	}
	return;
}

//템플릿 리스트에서 호출 list_template.php list_templateset.php //복수 편집기 호출
function pods_editor_call(templateName,pods_use,podskind,podsno,tsid,tid) {
	//alert($j("input[name=ret]").val());
	var template = JSON.parse($j("input[name=ret]").val());	

	//alert('{_ret}');
	//alert(template.data[podsno].length);
	//alert("templateName:"+templateName+",pods_use:"+pods_use+",podskind:"+podskind+",podsno:"+podsno+",tsid:"+tsid+",tid:"+tid);

	//템플릿셋,템플릿 ID가 없을 경우 템플릿명으로 검색한다.
	if(tsid == "" || tid == "") {
		for (i = 0; i < template.data[podsno].length; i++) {
			if(templateName == template.data[podsno][i].templateName) {
      		tsid = template.data[podsno][i].templateSetIdx;
      		tid = template.data[podsno][i].templateIdx;
			}
		}
	}

	//브라우저 체크		
	var agent = navigator.userAgent.toLowerCase();
	if ((navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agent.indexOf("msie") != -1)) {
		var rv = GetVersionOfIE();
		//alert("인터넷 익스플로러 브라우저 입니다." + rv);
	}

	//alert("tsid:"+tsid+",tid:"+tid);
	if (pods_use == "3") {
		//alert("인터넷 익스플로러 브라우저 입니다." + rv);
		//WPOD 편집기 IE 10 이하인 경우 메세지 출력.
		if(rv < 10) {
			//alert("본 편집기는 HTML5 기반으로 제작되었습니다.\n인터넷 익스플로러의 경우 버전 10 이상 혹은 다른 브라우저를 이용해 주세요.");
			//ie브라우저 버전 안내 레이어 오픈
			OpenLayerIeInfo();
		} else {
			PodsCallWpodEditor(pods_use, podskind, podsno, '', tsid, tid);
		}
	} else {
		PodsCallEditor(pods_use, podskind, podsno, tsid, tid);
	}
}

//ie브라우저 버전 안내 레이어 오픈
function OpenLayerIeInfo() {
	popupLayer("/service/pop_ie_info.php", 550, 780);
}

function GetVersionOfIE () {
	 var word;
	 var version = "N/A";

	 var agent = navigator.userAgent.toLowerCase();
	 var name = navigator.appName;

	 // IE old version ( IE 10 or Lower )
	 if ( name == "Microsoft Internet Explorer" ) word = "msie ";
	 else {
		 // IE 11
		 if ( agent.search("trident") > -1 ) word = "trident/.*rv:";

		 // Microsoft Edge
		 else if ( agent.search("edge/") > -1 ) word = "edge/";
	 }

	 var reg = new RegExp( word + "([0-9]{1,})(\\.{0,}[0-9]{0,1})" );
	 if (  reg.exec( agent ) != null  ) version = RegExp.$1 + RegExp.$2;

	 return version;
}

//### view.php 편집기 종료 시 호출.
function exec(mode){
	var fm = document.fmView;
	if (!form_chk(fm)) return;
	fm.action = (mode!="wish") ? "../order/cart.php" : "../mypage/wishlist.php";
	fm.mode.value = mode;
	fm.submit();
}

//미오디오 피규어 편집기 리턴처리.
function exec2(mode, sid, appResult){
	var fm = document.fmView;
	if (!form_chk(fm)) return;
	fm.action = (mode!="wish") ? "../order/cart.php" : "../mypage/wishlist.php";	
	
	//alert("mode:"+mode+",sid:"+sid+",appResult:"+appResult);
  	if (typeof (sid) == undefined || sid == null) sid = "";	
  	if (typeof (appResult) == undefined || appResult == null) appResult = "";
  	
  	if (sid != "") fm.storageid.value = sid;
  	
  	//장바구니에는 ext_json_data 항목이 없음.
  	if ($j("input[name=ext_json_data]").length > 0 ) { 
		if (appResult != "") fm.ext_json_data.value = appResult; 
	} else {
		$j("form[name=fmView]").append("<input type='hidden' name='ext_json_data' value='"+ appResult +"' />"); 
	}
	
	fm.mode.value = mode;
	fm.submit();
}

//### list.php, view.pho 복수 편집기 호출. 
//### /js/extra_option/goods.extra.option.call.js call_exec와 통합 20171019 by kdk.
/*function call_exec(pods_use,podskind,podsno,tsid,tid){
	if (pods_use == "3") {
		PodsCallWpodEditor(pods_use, podskind, podsno, '', tsid, tid, '');
	} else {
		PodsCallEditor(pods_use, podskind, podsno, tsid, tid);
	}
}*/

function call_exec2(pods_use, podskind, podsno, tsid, tid, pod_signed, editor_type) {
	//console.log("call_exec2:");

	var vdp = "";
	var mode = "";

	if (pods_use == "3") {
		PodsCallWpodEditor(pods_use, podskind, podsno, vdp, tsid, tid, pod_signed, mode);
	} else if(pods_use == "2" && editor_type == "web"){
		PodsCallWpodEditor(pods_use, podskind, podsno, vdp, tsid, tid, pod_signed, mode, editor_type);
  } else {
		PodsCallEditor(pods_use, podskind, podsno, tsid, tid, mode, pod_signed);
	}
}

//### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk ### /js/pods_editor.js call_exec로 통합 20171019 by kdk.
function call_exec(pods_use, podskind, podsno, templateSetIdx, templateIdx, editor_type) {
	//console.log("call_exec:");
	var bCheck = false;

	//템플릿셋ID 템플릿ID를 추가 저장한다.
	var orderJson = {};
	var order_title = "";
	var order_memo = "";
	var json = "";

	if ($j("input[name='goods_group_code']").length) {
		if ($j("input[name='goods_group_code']").val() == "30") {
			if ($j("input[name='option_json']").length) {
				//var json_data = unescape($j("input[name='option_json']").val());
				//var json_data = Base64.decode($j("input[name='option_json']").val());
				var json_data = $j("input[name='option_json']").val();
				//console.log(json_data);
				//json_data = json_data.replace(/\\"/g,'"');
				//json_data = json_data.replace(/\\\"/g,'"');
				//console.log(json_data);

				orderJson = JSON.parse(json_data);
				//console.log(orderJson);

				//catno 추가
				if ($j("input[name='catno']").length) {
					orderJson["catno"] = $j("input[name='catno']").val();
				}

				bCheck = true;
			}
		}
	}
	//console.log(orderJson);
	
	//템플릿셋ID 템플릿ID를 추가 저장한다.
	orderJson["templateSetIdx"] = templateSetIdx;
	orderJson["templateIdx"] = templateIdx;

	if ($j("input[name='option_json']").length) {
    	$j("input[name='option_json']").val(JSON.stringify(orderJson));
    	//console.log($j("input[name='option_json']").val());
	
		json = encodeURIComponent(Base64.encode($j("input[name='option_json']").val())); 
		//console.log(json);	
	}	
	
	if ($j("input[name='est_title']").length) {
		order_title = $j("input[name=est_title]").val();
		orderJson["est_title"] = order_title;
	}
	if ($j("textarea[name='est_order_memo']").length) {
		order_memo = $j("textarea[name=est_order_memo]").val();
		orderJson["est_order_memo"] = order_memo;
	}
	
	if ($j("input[name='select_option_sum_price']").length) {
		if ($j("input[name='select_option_sum_price']").val() == "" || $j("input[name='select_option_sum_price']").val() == "0") {
			alert(_tsl("결제금액을 확인하시고 다시 시도하시기 바랍니다."));	
			return false;
		}
	}

	if(bCheck) {
		//ajax 전송
	    var url="indb.php";
	    var params="mode=ajax_option_json&pod_signed="+pod_signed+"&option_json="+json+"&order_title="+order_title+"&order_memo="+order_memo;
	    $j.ajax({
	        type:"POST",
	        url:url,
	        data:params,
	        success:function(args){
	            if(args == "OK") {
						//alert("완료되었습니다.");
						call_exec2(pods_use, podskind, podsno, templateSetIdx, templateIdx, pod_signed, editor_type);
	            }
	            else {
						alert(_tsl("실패하였습니다.")+args);
	            }
	        },
	        error:function(e){
	            alert(e.responseText);
	        }
	    });
	}
	else {
		call_exec2(pods_use, podskind, podsno, templateSetIdx, templateIdx, pod_signed, editor_type);
	}
}

//### 배너 에디터에서 호출
function call_exec_editor(pods_use, podskind, podsno, goodsno, optno, addopt, productid, optionid, tsid, tid) {
	//console.log("call_exec_editor:");

	if ($j("form[name='fmView']").length) {
		$j("input[name=goodsno]").val(goodsno);
		$j("input[name=pods_use]").val(pods_use);
		$j("input[name=podsno]").val(podsno);
		$j("input[name=podskind]").val(podskind); 
	}
	else {
	     var form = $j("<form name='fmView'></form>");
	     $j(form).appendTo('body');
	     
	     var imode = "<input type='hidden' name='mode' value='view' />";
	     var igoodsno = "<input type='hidden' name='goodsno' value='"+ goodsno +"' />";
	     var istorageid = "<input type='hidden' name='storageid' />";
	 
		 var ipods_use = "<input type='hidden' name='pods_use' value='"+ pods_use +"' />";
		 var ipodsno = "<input type='hidden' name='podsno' value='"+ podsno +"' />";
		 var ipodskind = "<input type='hidden' name='podskind' value='"+ podskind +"' />";
	
	     $j(form).append(imode).append(igoodsno).append(istorageid).append(ipods_use).append(ipodsno).append(ipodskind);
	}

	var pod_signed = "";
	call_exec2(pods_use, podskind, podsno, tsid, tid, pod_signed);
}
 