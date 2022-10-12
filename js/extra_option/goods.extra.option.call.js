	
	
	function callUpdateOptionPrice(optionJson)
	{
		//alert("callUpdateOptionPrice");
		$.ajax({
		   type: "post"
		   ,dataType:"json"
		   ,data:{"json":optionJson}
		   ,url: "/lib/extra_option/get_extra_option_item.php?mode=totalPrice"
		   ,success: updateOptionPrice
		   //,beforeSend : showReq
		   ,error: callRstError
		 }); 
	}
	


	//외부 업로드를 위한 path key 생성
	function getCreateKey(nextfunc){
		$.ajax({
		   type: "get"
		   ,url: "/lib/extra_option/estimate_order_proc.php?mode=get_createkey"
		   ,success: nextfunc
		   ,error: callRstError
		 }); 
	}
	

	function callRstError(xhr, ajaxOptions, thrownError)
	{
		//hideLoading();
		if(thrownError.message)
      alert(thrownError.message);
		else alert(thrownError);
		//alert(xhr.responseText); 
	}
	

//### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk 
//### /js/pods_editor.js call_exec로 통합 20171019 by kdk.
/*
function call_exec(pods_use,podskind,podsno,templateSetIdx,templateIdx){

	var fm = document.fmView;	
	if (!form_chk(fm)) return;
	
	if(fm.goods_group_code.value=="30") { //view_wpod.htm 견적정보가 없을 경우...
		//자동견적 관련 옵션 정보를 넣어준다.	
		fm.option_json.value = makeOptionJson();
		//alert(fm.option_json.value);
		
		//템플릿셋ID 템플릿ID를 저장한다.
		var orderJson = JSON.parse(fm.option_json.value);
		//alert(orderJson);	
		
		var order_title = $j("input[name=est_title]").val();
		var order_memo = $j("textarea[name=est_order_memo]").val();
	}
	else {
		//템플릿셋ID 템플릿ID를 저장한다.
		var orderJson = {};
		//alert(orderJson);	
		
		var order_title = "";
		var order_memo = "";		
	}

	orderJson["templateSetIdx"] = templateSetIdx;	    
	orderJson["templateIdx"] = templateIdx;
	    
    fm.option_json.value = JSON.stringify(orderJson);
	//alert(fm.option_json.value);	
	
	//var pod_signed = "{_pod_signed}";
	//alert(pod_signed);

	var bCheck = true;
	
	
	if (document.getElementById("select_option_sum_price") != null ) {
		//결제금액이 0원 체크.
		if(fm.select_option_sum_price.value == "" || fm.select_option_sum_price.value == "0") {
			alert("결제금액을 확인하시고 다시 시도하시기 바랍니다.");	
			bCheck = false;		
		}
	}
	
	//브라우저 체크		
	var agent = navigator.userAgent.toLowerCase();
	if ((navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agent.indexOf("msie") != -1)) {
		var rv = get_version_of_IE();
		//alert("인터넷 익스플로러 브라우저 입니다." + rv);
	}

	//전체화면 처리..
	//requestFullScreen();		
	
	if(bCheck) {
		//ajax 전송
	    var url="indb.php";
	    var params="mode=ajax_option_json&pod_signed="+pod_signed+"&option_json="+fm.option_json.value+"&order_title="+order_title+"&order_memo="+order_memo;
	    $j.ajax({
	        type:"POST",
	        url:url,
	        data:params,
	        success:function(args){
	            if(args == "OK") {
					//alert("완료되었습니다.");
					if (pods_use == "3") {
						//alert("인터넷 익스플로러 브라우저 입니다." + rv);
						//WPOD 편집기 IE 10 이하인 경우 메세지 출력.
						if(rv < 10) {
							//alert("본 편집기는 HTML5 기반으로 제작되었습니다.\n인터넷 익스플로러의 경우 버전 10 이상 혹은 다른 브라우저를 이용해 주세요.");
							//ie브라우저 버전 안내 레이어 오픈
							ieInfoOpenLayer();
						}
						else {
							PodsCallWpodEditor(pods_use, podskind, podsno, '', templateSetIdx, templateIdx, pod_signed);	
						}							
					} else {
						PodsCallEditor(pods_use, podskind, podsno, templateSetIdx, templateIdx, "", pod_signed);
					}
	            }
	            else {
					alert("실패하였습니다."+args);
	            }
	        },   
	        error:function(e){
	            alert(e.responseText);
	        }
	    });		
	}
}
*/

function requestFullScreen() {
 	var docElm = document.getElementById("editor_layer");
 	if (docElm.requestFullscreen) {
    	docElm.requestFullscreen();
 	}
	else if (docElm.msRequestFullscreen) {
		docElm.msRequestFullscreen();
	} 	
 	else if (docElm.mozRequestFullScreen) {
    	docElm.mozRequestFullScreen();
 	}
 	else if (docElm.webkitRequestFullScreen) {
    	docElm.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
	}
}

function get_version_of_IE () { 
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

//### 관리자 wpod 편집기 실행시 IE 확인 / 16.08.19 / kdk
function call_wpod(url){
	var bCheck = true;
	//alert(url);
	//브라우저 체크		
	var agent = navigator.userAgent.toLowerCase();
	if ((navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agent.indexOf("msie") != -1)) {
		
		var rv = get_version_of_IE();
		//alert("인터넷 익스플로러 브라우저 입니다." + rv);
		if(rv < 10) {
			//alert("본 편집기는 HTML5 기반으로 제작되었습니다.\n인터넷 익스플로러의 경우 버전 10 이상 혹은 다른 브라우저를 이용해 주세요.");
			//ie브라우저 버전 안내 레이어 오픈
			popupLayer(url+"&ie_info=Y",520,790);
			
			bCheck = false;
		}
	}
	
	if(bCheck) {
		window.open(url);
   }
}
