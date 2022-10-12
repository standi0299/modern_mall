
/*
* @date : 20180706
* @author : kdk
* @brief : $.인식이 안되는 오류 발생.
* @request :
* @desc : $ => $j 임시 조치.
* @todo : 원인 파악이 필요함.
*/

var order_type;
var order_mode;
var cur_storageid = '';
//var url_file_upload = 'http://files.ilark.co.kr/portal_upload/estm/file/upload.aspx';
//var url_file_upload = {cfg.est_upload_url};

//var url_file_upload = 'http://192.168.1.199:8094/estm/file/upload.aspx';

//### step 0 견적의뢰처리 (의뢰인 정보)
function initEstOrder(orderType, init_mode, obj)
{	
	var orderInfo = {};

	if($j("#order_name").val() =="") {
		alert(tls("이름을 입력하세요."));
		$j("#order_name").focus();
		return false;
	}
	else {
		orderInfo["order_name"] = $j("#order_name").val();		
	}

	orderInfo["order_cname"] = $j("#order_cname").val();		
	orderInfo["order_phone"] = $j("#order_phone").val();		
	
	if($j("#order_mobile").val() =="") {
		alert(tls("휴대전화를 입력하세요."));
		$j("#order_mobile").focus();
		return false;
	}
	else {
		orderInfo["order_mobile"] = $j("#order_mobile").val();		
	}
	
	if($j("#order_email").val() =="") {
		alert(tls("이메일 주소를 입력하세요."));
		$j("#order_email").focus();
		return false;
	}
	else {
		orderInfo["order_email"] = $j("#order_email").val();		
	}			

	//orderInfo["order_sns"] = $j("#order_sns").val();
	
	if($j("#agreement2").length > 0) {
		if(!$j("#agreement2").is(":checked")) {
			alert(tls("개인정보 취급 방침에 동의해주세요."));
			$j("#agreement2").focus();
			return false;
		}		
	}
	if($j("#privacy").length > 0) {
		if($j("#privacy").is(":checked")) {
			orderInfo["privacy"] = "on";
		}
	}
	
	$j("#est_order_info").val(JSON.stringify(orderInfo));

	//initOrder(orderType, init_mode);
	
	//첨부파일 업로드 여부 확인 처리 / 16.12.23 / kdk
	if (confirm(tls("파일을 첨부 하시겠습니까?"))) {
		//견적의뢰 관련 의뢰인정보 레이어 아웃
    	$j("#dlayer-orderinfo").fadeOut();
		
		var obj = $j("img[code=_sys_btn_goods_estimate_request]");
		fileInfoOpenLayer(obj);	
	}
	else {
		initOrder(orderType, init_mode);
	}	
	
}

//### step 0 NFUpload 플래시 업로더 처리
function initNFOrder(orderType, init_mode) 
{
	//파일모드 - 에디터,업로드,파일없음 (EDITOR, UPLOAD, NOFILES)
	order_type = orderType;
	
	//EDITOR 상품에 UPLOAD 를 사용할 경우  est_order_type 를 UPLOAD 로 변경한다.
	if(order_type == "UPLOAD") {
		if($j("input[name=est_order_type]").val() == "EDITOR") {
			$j("input[name=est_order_type]").val("UPLOAD");
		}
	}
	
	//값이 없을 경우...버튼 배너화 작업으로 수정.
	if(order_type == "") order_type = $j("input[name=est_order_type]").val();	
	
	//수정모드 - 옵션만 수정 (new, update)
	order_mode = init_mode;
	
	//if(!isValidOrder()) return false;
	
	//최종 가격 갱신
	//initUpdateOptionPrice(); //성공시 procOrder로 이동
	
	//보관함 코드 할당.
	cur_storageid = $j("input[name=storageKey]").val();

	//템플릿셋ID 템플릿ID를 저장한다.
	//var orderJson = JSON.parse(fm.option_json.value);
	//alert(orderJson);	
	//orderJson["templateSetIdx"] = templateSetIdx;	    
	//orderJson["templateIdx"] = templateIdx;
	
	//파일전송.
	NfUpload.FileUpload();		
}

//### step 0 jquery-file-upload 업로더 처리
function initJQOrder(orderType, init_mode, storageKey) 
{
	//파일모드 - 에디터,업로드,파일없음 (EDITOR, UPLOAD, NOFILES)
	order_type = orderType;
	
	//EDITOR 상품에 UPLOAD 를 사용할 경우  est_order_type 를 UPLOAD 로 변경한다.
	/*if(order_type == "UPLOAD") {
		if($j("input[name=est_order_type]").val() == "EDITOR") {
			$j("input[name=est_order_type]").val("UPLOAD");
		}
	}*/
	
	$j("input[name=est_order_type]").val(order_type);
	
	//값이 없을 경우...버튼 배너화 작업으로 수정.
	if(order_type == "") order_type = $j("input[name=est_order_type]").val();	
	
	//수정모드 - 옵션만 수정 (new, update)
	order_mode = init_mode;
	
	//if(!isValidOrder()) return false;
	
	//보관함 코드 할당.
	cur_storageid = storageKey;

	submitOrder();		
}

//### step 1 주문처리
function initOrder(orderType, init_mode)
{	
	//파일모드 - 에디터,업로드,파일없음 (EDITOR, UPLOAD, NOFILES)
	order_type = orderType;
	
	//EDITOR 상품에 UPLOAD 를 사용할 경우  est_order_type 를 UPLOAD 로 변경한다.
	if(order_type == "UPLOAD") {
		if($j("input[name=est_order_type]").val() == "EDITOR") {
			$j("input[name=est_order_type]").val("UPLOAD");
		}
	}
	
	//값이 없을 경우...버튼 배너화 작업으로 수정.
	if(order_type == "") order_type = $j("input[name=est_order_type]").val();	
	
	//수정모드 - 옵션만 수정 (new, update)
	order_mode = init_mode;
		
	if(!isValidOrder()) return false;
	
	//최종 가격 갱신
	initUpdateOptionPrice(); //성공시 procOrder로 이동
}

//주문 체크
function isValidOrder()
{
	//alert(order_type);
	//alert(order_mode);
	var result = false;
	//파일이 있는지 체크 (신규 주문만 확인)
	if (order_type == "UPLOAD" && order_mode == "NEW")
	{
		var obj = document.getElementsByTagName("input");
		//alert(obj.length);
		
	    for (var i = 0; i < obj.length; i++) 
	    {	      
    		if(obj[i].getAttribute("type") == "file") 
    		{	   	
		      if (obj[i].value)
		      {
		      	result = true;
		      	break;
		      }
		  	}
	    }	
    
	    if (!result) 
	    {
	    	//alert('파일을 1개이상 등록해 주세요!');
	    	
	    	//파일이 없어도 주문이 가능하게 처리 2015.06.24 by kdk
	    	
	    	//메세지 출력 처리 2015.12.23 by kdk
	    	if (!confirm(tls("첨부된 파일이 없습니다.") + "\n" + tls("파일없이 주문하시겠습니까?"))) {
	    		return false;
	    	}
	    	
	    	order_type = "NOFILES";
	    	result = true;
	    }
	} 
	else 
	{
		result = true;	
	}
	return result;
}


function initUpdateOptionPrice()
{
	//var optionJson = makeOptionJson();
	//document.write(optionJson);
	//alert(optionJson);
	//return;
	
	//서버와 가격 설정이 맞는지 한번 더 확인을 위해		일단 처리하지 않는다.
	//callUpdateOptionPrice(optionJson,func);
	//updateOptionPrice();		
	procOrder();
}
	
	
//전체 주문 json 만들기.
function makeOptionJson()
{
    //좀더 필요한 옵션항목들을 구성한다. 
    //$result["est_order_cnt"] = 0; //주문수량
    //$result["est_supply_price"] = 0; //공급가
    //$result["est_price"] = 0; //판매가
    //$result["est_order_option_desc"] = ''; //옵션정보	

	$j("input[name='option_group_code']").val(optino_group_code.toString());
	
	var preset = "";
	
	var orderJson = {};
	var orderOption = {};
	var orderCnt = {};
	var orderAddPage = {};

	preset = $j("input[name='preset']").val();
		
	//select 상자의 값들을 취합.   
    var obj = document.getElementsByTagName("select");
    for (var i = 0; i < obj.length; i++) {    
    	orderOption[obj[i].name] = obj[i].value;

      	if (obj[i].name.indexOf('unit_order_cnt') > -1) {
        	orderJson["est_order_cnt"] = obj[i].value;
      	}
    }    

	orderOption["option_group_code"] = $j("input[name='option_group_code']").val();	
	
    //input tag들의 값들을 모조리 취합.
    var obj = document.getElementsByTagName("input");
    for (var i = 0; i < obj.length; i++) {
      if (obj[i].type == "hidden" || obj[i].type == "checkbox" || obj[i].type == "text") {
        if (obj[i].name.indexOf('select_option_supply_price') > -1) {
          orderJson["est_supply_price"] = obj[i].value;
        }                        
        else if (obj[i].name.indexOf('select_option_sum_price') > -1) {
          orderJson["est_price"] = obj[i].value;
        }        
        else if (obj[i].name.indexOf('print_x_') > -1) {
          orderOption[obj[i].name] = obj[i].value;
        }        
        else if (obj[i].name.indexOf('print_y_') > -1) {
          orderOption[obj[i].name] = obj[i].value;
        }        
        else if (obj[i].name.indexOf('chk_item_select_') > -1) {
          orderOption[obj[i].name] = obj[i].checked ? "Y" : "N";
        }        
        else if (obj[i].name.indexOf('option_group_code') > -1) { //추가 내지를 확인하기 위해
          orderJson[obj[i].name] = obj[i].value;
        }        
        else if (obj[i].name.indexOf('preset') > -1) { //프리셋
          orderJson[obj[i].name] = obj[i].value;
        }        
      }
    }     

    var strDocument = "";
    var strCover = "";
    var strPage = "";
    var strMpage = "";
    var strGpage = "";
    var strAdd = "";
    var strAfter = "";
    
    //옵션정보를 생성 한다. est_order_option_desc
    for (var i = 0; i < optino_group_code.length; i++) {    	
    	var code = optino_group_code[i];    	
    	if (code) {
    		
			if(code == "DOCUMENT") {
      			strDocument = tls("규격") + "::"+ $j("select[option_group_type='DOCUMENT']").val();
      			strDocument += "(" + $j("input[name='document_x']").val() + "x" + $j("input[name='document_y']").val() +")mm";
        	}
        	else {
	   			var div = $j('#option_item_select_'+ code);
				var selectOption = $j(div).find("select[code='"+code+"']");
				
			    for (var j = 0; j < selectOption.length; j++) {

			    	if($j(selectOption[j]).val()) {				
			    		
			    		var spl =",";
			    		var lbl = "";
			    		lbl = $j("span[name='"+ $j(selectOption[j]).attr("id") +"']").text();
			    		
			    		//추가내지 관련.
			    		if($j("span[name='"+ $j(selectOption[j]).attr("id") +"']").length > 1) {
			    			//console.log($j($j("span[name='"+ $j(selectOption[j]).attr("id") +"']")[0]).text());
			    			lbl = $j($j("span[name='"+ $j(selectOption[j]).attr("id") +"']")[0]).text();
			    		}			    		
			    		
			    		if(lbl !== "") {
			    			lbl = lbl + ":";
			    		}
			    		
			    		if($j(selectOption[j]).attr("index") == "1") spl ="|";
			    		if($j(selectOption[j]).attr("index") == "2") spl ="|";
			    		if($j(selectOption[j]).attr("index") == "3") spl =",";
			    		
				  		if(code == "C-PP" || code == "C-OP") {
				  			if(preset == "100112") { //책자 프리셋3(100112)
				  				if($j("#chk_item_select_" + code + "_0").is(":checked")) {
				  					strCover += lbl + $j(selectOption[j]).val() + spl;									
								}
				  			}
				  			else {
				  				strCover += lbl + $j(selectOption[j]).val() + spl;	
				  			}				  			
				    	}
				  		else if(code == "PP" || code == "OP") {
				  			strPage += lbl + $j(selectOption[j]).val() + spl;
				    	}
				  		else if(code == "M-PP" || code == "M-OP") {
							if($j("#chk_item_select_" + code + "_0").is(":checked")) {
				  				strMpage += lbl + $j(selectOption[j]).val() + spl;									
							}
				    	}
				  		else if(code == "G-PP" || code == "G-OP") {
							if($j("#chk_item_select_" + code + "_0").is(":checked")) {
				  				strGpage += lbl + $j(selectOption[j]).val() + spl;									
							}				  			
				    	}
				  		else if(code.indexOf('PP-') > -1 || code.indexOf('OP-') > -1) {
				  			strAdd += lbl + $j(selectOption[j]).val() + spl;
				  			
				  			var s = code.split("-");
				  			orderAddPage[s[1]] += lbl + $j(selectOption[j]).val() + spl;
				    	}
				  		else if(code == "F-OP") { //책자 프리셋3(100112) 표지 필수 옵션
				  			strCover += "," + lbl + $j(selectOption[j]).val() + spl;
				    	}				    	
				  		else if(code == "D-OP") { //책자 프리셋3(100112) 제본방식 JB,제본형식 HS
				  			strDocument += "," + lbl + $j(selectOption[j]).val() + spl;
				    	}
				    	else {
							if($j("#chk_item_select_" + code + "_0").is(":checked")) {
				  				strAfter += lbl + $j(selectOption[j]).val() + spl;									
							}				    		
				    	}			    	
			    	}
			    }
      		}
      		
      		//페이지 수량 
			var selectCntOption = $j(div).find("select[name^='order_cnt_select']");
		    for (var j = 0; j < selectCntOption.length; j++) {
		    	//console.log($j(selectCntOption[j]).attr("name") +":"+ $j(selectCntOption[j]).val());
		    	
		    	var lbl = $j("span[name='order_cnt_select_"+ $j(selectCntOption[j]).attr("code") +"']").text();
		    	if($j(selectCntOption[j]).attr("code").indexOf('OCNT-') > -1) { //추가내지면...
		    		lbl = $j("span[name='order_cnt_select_OCNT']").text();
		    	}
	    		if(lbl !== "") lbl += ":";
	    	
		    	var label = $j("span[name='"+ $j(selectCntOption[j]).attr("code") +"']").text();
		    	if(label == "") label = tls("매(장)");
		    	
		    	orderCnt[$j(selectCntOption[j]).attr("code")] = lbl +""+ $j(selectCntOption[j]).val() + ""+ label + ","; 
      		}
      	}
    }

	//console.log(orderCnt);
	//console.log(orderCnt["C-OCNT"]);
	//console.log(orderAddPage);

	//undefined replace 필요!

	orderJson["est_order_option_desc"] = "";

	//수량
   	var label = $j("span[name='unit_order_cnt']").text();
   	if(label == "") label = tls("건(부)");	
	orderJson["est_order_option_desc"] += tls("수량") + "::"+ orderJson["est_order_cnt"] + " " + label + "<br>";

	//프리셋 100112 표지수량 없음.	
	if(preset == "100112") {
		orderCnt["C-OCNT"] = "";
	}

	//표지,내지,추가내지,면지,간지	
	if(preset == "100104" || preset == "100108" || preset == "100112") { //책자 견적
		if(strDocument != "") strDocument = strDocument.replace("undefined","");
		if(strCover != "") strCover = strCover.replace("undefined","");
		if(strPage != "") strPage = strPage.replace("undefined","");
		if(strMpage != "") strMpage = strMpage.replace("undefined","");
		if(strGpage != "") strGpage = strGpage.replace("undefined","");	
		if(strAfter != "") strAfter = strAfter.replace("undefined","");
		
		if(strDocument != "") orderJson["est_order_option_desc"] += strDocument + "<br>";
		if(strCover != "") orderJson["est_order_option_desc"] += tls("표지") + "::["+ orderCnt["C-OCNT"] + strCover.substr(0, strCover.length -1) + "]<br>";
		if(strPage != "") orderJson["est_order_option_desc"] += tls("내지") + "::["+ orderCnt["OCNT"] + strPage.substr(0, strPage.length -1) + "]<br>";
		if(strMpage != "") orderJson["est_order_option_desc"] += tls("면지") + "::["+ orderCnt["M-OCNT"] + strMpage.substr(0, strMpage.length -1) + "]<br>";
		if(strGpage != "") orderJson["est_order_option_desc"] += tls("간지") + "::["+ orderCnt["G-OCNT"] + strGpage.substr(0, strGpage.length -1) + "]<br>";
		
		for (key in orderAddPage) {
			if(orderAddPage[key] != "") {
				orderAddPage[key] = orderAddPage[key].replace("undefined",""); 
				orderJson["est_order_option_desc"] += tls("추가내지") + "::["+ orderCnt["OCNT-"+key] + orderAddPage[key].substr(0, orderAddPage[key].length -1) + "]<br>";
			}
		}		
	}
	else if(preset == "100110") { //스튜디오 견적
		if(strPage != "") orderJson["est_order_option_desc"] += tls("옵션") + "::["+ orderCnt["OCNT"] + strPage.substr(0, strPage.length -1) + "]<br>";
		if(strCover != "") orderJson["est_order_option_desc"] += tls("표지") + "::["+ strCover.substr(0, strCover.length -1) + "]<br>";		
	}
	else { //낱장 견적
		if(strDocument != "") orderJson["est_order_option_desc"] += strDocument + "<br>";
		if(strPage != "") orderJson["est_order_option_desc"] += tls("옵션") + "::["+ orderCnt["OCNT"] + strPage.substr(0, strPage.length -1) + "]<br>";
	}
	
	//후가공 옵션
	if(strAfter != "") orderJson["est_order_option_desc"] += tls("후가공") + "::["+ strAfter.substr(0, strAfter.length -1) + "]";
	
	//console.log(orderJson["est_order_option_desc"]);
	//$j("#txtMakeOptionJsonS2").val(JSON.stringify(orderJson));
	
	orderJson["est_order_option"] = JSON.stringify(orderOption);	    
    
    return JSON.stringify(orderJson);
}
	
	
function updateOptionPrice()
{
	if(ingOrder){
		ingOrder=false;
		if(befor_t_s == getNumByPrice($j('#total_orgprice_obj').text()) )
			procOrder();
		else
			alert(tls("가격이 다릅니다!"));
	}
}

	

//### step 2
function procOrder()
{	
  	//alert(order_type);
  	//alert(cur_storageid);
  	//alert(order_mode);
  	//alert(o_initMode);
  
  	//옵션만 수정하는 경우
  	if(order_mode=='UPDATE')
  	{
		submitOrder();
	}
	else if(order_type=='UPLOAD')
	{
		//showLoading('');
		//파일 별도 전송
		if(!cur_storageid && order_mode=='NEW') 
			getCreateKey(uploadFile); // 주문번호와 path key 얻기 - 신규 주문인 경우		
		else 
			submitFileForm(); // 재주문 인 경우
	}
	else if(order_type=='NOFILES') //파일이 없어도 주문이 가능하게 처리 2015.06.24 by kdk
	{
		submitOrder();
	}	
	else if(order_type=='EDITOR' && cur_storageid=='' && order_mode=='E_UPDATE') //편집기 수정모드
	{ 
		getNewStorageId();
	}
	else
	{ 
    	//alert('callEditorOpen()');
    	callEditorOpen();
  	}
}

//### step 5 파일업로드 경로 키 세팅(재주문,수정인 경우 생략)
function uploadFile(response){
	var rtn=response.split('|');
	if (rtn.length > 2) {
		if ('success' == rtn[0]) {
			orderid = rtn[1];
			cur_storageid = rtn[2];
			submitFileForm();
		}
		else alert(tls("보관함 코드 받기 실패!!") + " (" + rtn[1] + ")");	
	}
	else alert(tls("보관함 코드 받기 실패!") + " (" + response + ")"); 
}
//### step 5-1 파일 폼 전송 
function submitFileForm(){
	
	var upPortFrm=$j("#uploadPortFrame");	
	if (!upPortFrm || upPortFrm.length<1) {
		upPortFrm = $j("<iframe id='uploadPortFrame' name='uploadPortFrame' frameborder='0' width='0' height='0'/>").appendTo('body');
	}	
	setFormExternalUpload(cur_storageid);
	
	var filefrm = $j('#filefrm');
	$j(filefrm).attr("action",url_file_upload);
	$j(filefrm).attr("target","uploadPortFrame");
	$j(filefrm).submit();
}

//외부 업로드 서버로 보낼 form에 세팅
function setFormExternalUpload(path_key){
	setValToHidden("storage_code",path_key);
	setValToHidden("center_id",center_id);
	setValToHidden("mall_id",cid);
	setValToHidden("toss_url", "http://" + location.host + "/goods/estimate_c_service.php?mode=tossUploadFrame");
}

function setValToHidden(name,value){
	var ctrl = $j("#filefrm input[name='"+name+"']");
	if(ctrl.length>0)
	{
		$j(ctrl).val(value);
	}
	else
	{
		$j("#filefrm").append('<input type="hidden" name="'+name+'" value="'+value+'">');
	}
}


//### step 5-2 파일 저장 결과에 따른 분기처리
function continueFileOrder(response){
	//hideLoading();
	//clog("continueFileOrder ->"+response);
	
	var rtn=response.split('|');
	if (rtn.length > 1) {
		if ('success' == rtn[0]) {
			submitOrder();
		}
		else alert(tls("파일 업로드 실패!!") + "(" + rtn[1] + ")");	
	}
	else alert(tls("파일 업로드 실패!") + "(" + response + ")"); 
}


//### step 3 - 재주문
function getNewStorageId()
{
	alert(MSG_COPY_WAIT);
	showLoading('');
	callCopyPodsStorage(re_storageid,continueReOrder);
}

//### step 3-1 
function continueReOrder(response)
{
	hideLoading();
	var rtn=response.split('|');
	if (rtn.length > 1) {
		if ('success' == rtn[0]) {
			cur_storageid = rtn[1];
			if (confirm(MSG_OK_COPY)) 
				callEditorOpen();
			else 
				submitOrder();
		}
		else alert(MSG_FAIL_COPY+"(" + rtn[1] + ")");
	}
	else alert(MSG_FAIL_COPY+"(" + response + ")"); 
}

//### step 4 - 편집기를 통한 주문
function callEditorOpen()
{
	if(pods_productid=='')
	{
		alert(MSG_NONE_PODS);
		return;
	}

	//### 신규 저장/3.5 편집기 사용하는 경우 order_temp에 우선 저장
	if(order_mode=='NEW' && order_type=='EDITOR' && pods_ver=='2'){
		showLoading('');
		o_isOrderFromTemp=true;
		saveOrderTemp(getPostOrderJson(),continueCallEditorOpen);
	}
	else callEditorOpenCore();
}

//### step 4-1
function continueCallEditorOpen(response){
	hideLoading();
	
	var rtn=response.split('|');
	if (rtn.length > 1) {
		if ('success' == rtn[0]) {
			//로컬보관함을 위한 임시 주문번호
			pods_temp_orderid = rtn[1];	
			callEditorOpenCore();
		}
		else alert(ERR_TEMP_ORDER+"(" + rtn[1] + ")");
	}
	else alert(ERR_TEMP_ORDER+"(" + response + ")"); 
	
}

//### step 4-2
function callEditorOpenCore()
{
	if(pods_ver=='2') callEditorOpenVer2();
	else  callEditorOpenVer1();
}

//### step last
function submitOrder(){
	if(((order_type=='EDITOR' && cur_storageid!='' ) || order_type=='UPLOAD') && order_mode=='NEW')
	{
		if(order_type=='UPLOAD')
			procSubmit(cur_storageid);
			
		else if (o_isOrderFromTemp) {
			//주문정보 처리 loop
			var arr_sid = cur_storageid.split('&');
			var edit_desc_json = jQuery.parseJSON(pods_edit_result);//편집기의 리턴 json 문자열
			for (var i = 0; i < arr_sid.length; i++) {
				if (arr_sid[i]) {
					++o_submitSidCnt;
					procSubmit(arr_sid[i], getTempOrderIdFromEditJson(edit_desc_json, arr_sid[i]));
				}
			}
		}
		else {
			var arr_sid = cur_storageid.split('&');
			for (var i = 0; i < arr_sid.length; i++) {
				if (arr_sid[i]) {
					++o_submitSidCnt;
					procSubmit(arr_sid[i]);
				}
			}
		}
	}
	else if(order_mode=='UPDATE')
	{
		//alert('procUpdateSubmit');
		procUpdateSubmit();
	}
	else if(order_type=='NOFILES' && cur_storageid=='') //파일이 없어도 주문이 가능하게 처리 2015.06.24 by kdk
	{
		procSubmit("");	
	}
	
	else 
		alert(tls("주문 보관함 코드 누락"));
}

function procSubmit(sid){	
	//mode, goodsno, storageid //데이타 설정
	//장바구니 이동	
	var orderfrm = $j('#fmView');
	$j(orderfrm).attr("action",'/order/cart.php');
	
	$j(orderfrm).find('input[name="mode"]').val('cart');		
	$j(orderfrm).find('input[name="storageid"]').val(sid);
	//$j(orderfrm).find('input[name="option_json"]').val(makeOptionJson());

	if ($j(orderfrm).find('input[name="goods_group_code"]').val() == "30")
	{
		$j(orderfrm).find('input[name="option_json"]').val(makeOptionJson());
		//템플릿셋ID 템플릿ID를 저장한다.
		var orderJson = JSON.parse($j(orderfrm).find('input[name="option_json"]').val());
		//alert(orderJson);	
			
	}
	else {
		//템플릿셋ID 템플릿ID를 저장한다.
		var orderJson = {};
	}

	orderJson["templateSetIdx"] = $j("input[name=templateSetIdx]").val();
	orderJson["templateIdx"] = $j("input[name=templateIdx]").val();
	orderJson["catno"] = $j("input[name='catno']").val();	
	
    $j(orderfrm).find('input[name="option_json"]').val(JSON.stringify(orderJson));

	//$j(orderfrm).find('input[name="est_order_type"]').val(order_type);
	
	if($j('#extra_auto_pay_flag').val() == "0") //견적의뢰
		$j(orderfrm).find('input[name="mode"]').val('cart_extra');
		
	$j(orderfrm).submit();	
	//console.log(orderJson);
	//console.log($j(orderfrm).find('input[name="option_json"]').val());
	/*
	$j('#orderfrm').ajaxSubmit({
		url: ,
		type: 'post',
		data: (o_isOrderFromTemp?getOrderTempJson(sid,temp_oid) :getPostOrderJson(sid)),
		success: function(response){
			loopChkSubmit(response);
		},
		fail: callRstError
	});
	*/
}


function procUpdateSubmit(){	
	//mode, goodsno, storageid //데이타 설정
	//장바구니 이동	
	var orderfrm = $j('#fmView');
	$j(orderfrm).attr("action",'/order/cart.php?mode=extra_option_update');
	$j(orderfrm).find('input[name="option_json"]').val(makeOptionJson());
	//$j(orderfrm).find('input[name="est_order_type"]').val(order_type);	
	$j(orderfrm).submit();
}

function loopChkSubmit(response){
	--o_submitSidCnt;
	var rtn=response.split('|');
	if (rtn.length > 2) {
		if ('success' == rtn[0])
			o_arrOkSubmit.push(rtn[2] + (rtn[1] ? "(" + rtn[1] + ")" : ""));
		else
			o_arrErrSubmit.push(rtn[2] + (rtn[1] ? "(" + rtn[1] + ")" : ""));
	}
	else o_arrErrSubmit.push(response);
	
	if(o_submitSidCnt<1)
	{
		if (o_arrErrSubmit.length > 0) {
			var errmsg=tls("성공") + ":" + o_arrOkSubmit.length + "/" + tls("실패") + ":" + o_arrErrSubmit.length;
			for(var i=0;i<o_arrErrSubmit.length;i++){
				errmsg+='\n\n'+o_arrErrSubmit[i];
			}
			
			alert(errmsg);
			if (confirm(tls("장바구니로 이동하겠습니까?"))) 
				location.href = '/order/cart.php';
		}
		else {
			//alert("성공:" + o_arrOkSubmit.length);
			location.href = '/order/cart.php';
		}
	}
}

function getTempOrderIdFromEditJson(json,findsid){
	if(json && json.uploaded_list){
		var rtnid="";
		$.each(json.uploaded_list, function(key,state){ 
   			obj = state; 
            if(obj.rsid == findsid){
				var s =obj.session_param;
				var sidx =  s.indexOf('ptoid:');
				if ( sidx> -1) {
					sidx=sidx+6;
					var eidx =  s.indexOf(',',sidx);
					var tempoid = s.substring(sidx,eidx);
					if (!isNaN(tempoid)) {
						rtnid = tempoid;
						return;
					}
				}
				else{
					sidx =  s.indexOf('ptoid%3a');
					if ( sidx> -1) {
						sidx=sidx+8;
						var eidx =  s.indexOf('%2c',sidx);
						var tempoid = s.substring(sidx,eidx);
						if (!isNaN(tempoid)) {
							rtnid = tempoid;
							return;
						}
					}
				}
			}
        });
		return rtnid;
	}
	return '';
}

//견적의뢰 관련 의뢰인정보 레이어 오픈
function orderInfoOpenLayer(obj) {
	var el = "dlayer-orderinfo"; 
	
    var temp = $j('#' + el);
	
    //var bg = temp.prev().hasClass('bg'); //dimmed 레이어를 감지하기 위한 boolean 변수
	temp.fadeIn();

	if(obj) {
		// 클릭한 버튼 위치에 레이어를 띄운다.
		var o = $j(obj).position();

		temp.css('top', (o.top) + 'px');
		temp.css('left', (o.left - 500) + 'px');

		//레이어 위에 레이어 호출 시 this 위치를 잘못 찾을경우 첫번째 위치를 사용한다....top : 86px, left : -373px
		if((o.top) <= 86 || (o.left - 500) <= -373) {
			//어트리뷰트 추가 
			temp.css('top', $j('#preset_div').attr("top") + 'px');
			temp.css('left', $j('#preset_div').attr("left") + 'px');
		}
		else {
			$j('#preset_div').attr("top", (o.top));
			$j('#preset_div').attr("left", (o.left - 500));
		}
		
		//console.log(obj);
		//console.log("top : " + (o.top) + "px, left : " + (o.left - 500) + "px");			
		//console.log("preset_div top : " + $j('#preset_div').attr("top") + "px, preset_div left : " + $j('#preset_div').attr("left") + "px");
	}
	else {
	    // 화면의 중앙에 레이어를 띄운다.
    	if (temp.outerHeight() < $j(document).height()) temp.css('margin-top', '-' + temp.outerHeight() / 2 + 'px');
    	else temp.css('top', '0px');
		
	    if (temp.outerWidth() < $j(document).width()) temp.css('margin-left', '-' + temp.outerWidth() / 2 + 'px');
	    else temp.css('left', '0px');			
	}		

    temp.find('a.cbtn').click(function (e) {
        temp.fadeOut();
        e.preventDefault();
    });
}

//견적상품(spring) 수동 파일업로드 레이어 오픈
function fileInfoOpenLayer(obj) {
	
	//popup("/fileupload/upload_popup.php",1000,800);
	popupLayerFileUpload("/fileupload/upload_popup.php");
	
	/*
	//obj = false;
	//initUploadDiv2();
	
	var el = "dlayer-fileinfo"; 
	
    var temp = $j('#' + el);
	
    //var bg = temp.prev().hasClass('bg'); //dimmed 레이어를 감지하기 위한 boolean 변수
	temp.fadeIn();

	if(obj) {
		// 클릭한 버튼 위치에 레이어를 띄운다.
		var o = $j(obj).position();

		temp.css('top', (o.top) + 'px');
		temp.css('left', (o.left - 500) + 'px');

		//레이어 위에 레이어 호출 시 this 위치를 잘못 찾을경우 첫번째 위치를 사용한다....top : 86px, left : -373px
		if((o.top) <= 86 || (o.left - 500) <= -373) {
			//어트리뷰트 추가 
			temp.css('top', $j('#preset_div').attr("top") + 'px');
			temp.css('left', $j('#preset_div').attr("left") + 'px');
		}
		else {
			$j('#preset_div').attr("top", (o.top));
			$j('#preset_div').attr("left", (o.left - 500));
		}
		
		//console.log(obj);
		//console.log("top : " + (o.top) + "px, left : " + (o.left - 500) + "px");			
		//console.log("preset_div top : " + $j('#preset_div').attr("top") + "px, preset_div left : " + $j('#preset_div').attr("left") + "px");
	}
	else {
	    // 화면의 중앙에 레이어를 띄운다.
    	if (temp.outerHeight() < $j(document).height()) temp.css('margin-top', '-' + temp.outerHeight() / 2 + 'px');
    	else temp.css('top', '0px');
		
	    if (temp.outerWidth() < $j(document).width()) temp.css('margin-left', '-' + temp.outerWidth() / 2 + 'px');
	    else temp.css('left', '0px');			
	}		

    temp.find('a.cbtn').click(function (e) {
        temp.fadeOut();
        e.preventDefault();
    });
    */
}

//ie브라우저 버전 안내 레이어 오픈
function ieInfoOpenLayer() {

	//function slide(){
		$j("#dlayer-ieinfo").slideToggle("fast"); 
	//}
	
	//function exe_install(){
	//	popupLayer("/service/exe_info.php", 900, 780);
	//}

	/*
	var el = "dlayer-ieinfo"; 
    var temp = $j('#' + el);

	temp.fadeIn();

    // 화면의 중앙에 레이어를 띄운다.
	if (temp.outerHeight() < $j(document).height()) temp.css('margin-top', '-' + temp.outerHeight() / 2 + 'px');
	else temp.css('top', '0px');
	
    if (temp.outerWidth() < $j(document).width()) temp.css('margin-left', '-' + temp.outerWidth() / 2 + 'px');
    else temp.css('left', '0px');		
	*/

    $j('#dlayer-ieinfo').find('a.cbtn').click(function (e) {
        $j('#dlayer-ieinfo').fadeOut();
        e.preventDefault();
    });
}

//후가공 옵션 도움말 이미지 레이어 오픈
function afterHelpImgOpenLayer(imgSrc) {
	var el = "dlayer-afterinfo"; 
	
    var temp = $j('#' + el);

	//var imgWidth = 0;
	//var imgHeight = 0;	
	$j("#afterHelp").attr('src',imgSrc);	
 	$j("#afterHelp").each(function() {
		$j(this).load(function(){ //이미지를 다 불러온후 확인하기 위해
	       	var imgWidth = this.naturalWidth; //이미지 크키가 정해져 있지 않을때
			var imgHeight = this.naturalHeight; //이미지 크키가 정해져 있지 않을때
			temp.css('width', imgWidth + 50);
   			temp.css('height', imgHeight + 100);			
		});
    });	
	
    //var bg = temp.prev().hasClass('bg'); //dimmed 레이어를 감지하기 위한 boolean 변수
	temp.fadeIn();

    // 화면의 중앙에 레이어를 띄운다.
	if (temp.outerHeight() < $j(document).height()) temp.css('margin-top', '-' + temp.outerHeight() / 2 + 'px');
	else temp.css('top', '0px');
	
    if (temp.outerWidth() < $j(document).width()) temp.css('margin-left', '-' + temp.outerWidth() / 2 + 'px');
    else temp.css('left', '0px');		

    temp.find('a.cbtn').click(function (e) {
        temp.fadeOut();
        //e.preventDefault();
    });
}

//### 견적서 출력
function openExtraBillPrint() {
    window.open('','popupView','width=750,height=850,scrollbars=yes');

	// form
	var form = document.createElement("form");     
	form.setAttribute("method","post");                    
	form.setAttribute("action","/module/bill_proc_extra_cart_print.php");        
	form.setAttribute("target","popupView");
	document.body.appendChild(form);                         
	
	//input
	var input1 = document.createElement("input");  
	input1.setAttribute("type", "hidden");                  
	input1.setAttribute("name", "mode");
	input1.setAttribute("value", "preview");                          
	form.appendChild(input1); 
	
	var input2 = document.createElement("input");  
	input2.setAttribute("type", "hidden");                  
	input2.setAttribute("name", "no");
	input2.setAttribute("value", "-1");                          
	form.appendChild(input2);
	
	var input3 = document.createElement("input");  
	input3.setAttribute("type", "hidden");                  
	input3.setAttribute("name", "option_json");
	input3.setAttribute("value", JSON.stringify(JSON.parse(makeOptionJson())));                          
	form.appendChild(input3);
	
	//폼전송
	form.submit();
}
