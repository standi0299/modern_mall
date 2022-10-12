  //********************
  //관리자 프리셋 옵션 항목 입력 자바스크립트들
  //********************

	function callXHR(url, param, opt) {
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();
		
		if(window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				callXHRResult(xmlhttp, opt);                
			}
		};
		
		//xmlhttp.open("GET", url + "?" + param, false);
    	//xmlhttp.send();
    	showLoading();
    	
		xmlhttp.open("POST", url, true);
        //-- 여기부분을 안넣었더니 서버 페이지에서 POST를 받지 못함 
	 	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8");
	 	xmlhttp.setRequestHeader("Cache-Control","no-cache, must-revalidate");
	 	xmlhttp.setRequestHeader("Pragma","no-cache");
        //-----------------------------------------------------
        
        if(chk) {
	 		xmlhttp.send(param);
	 	}	
	 	else {
	 		hideLoading();
	 		return false;
	 	}	
	}

	function callXHRResult(xhr, opt)
	{
		hideLoading();
		
		if(opt == "EDI_OPTION_ITEM") {
			//alert(xhr.responseText);
			$("#selectedResourceList").html(xhr.responseText);
		}
		else if(opt == "SAME_ITEM") {
			//alert(xhr.responseText);
			$("#f_same_item").find('option').each(function() {
   				$(this).remove();
  			});
			$("#f_same_item").append("<option value=''>" + tls("없음") + "</option>");
			$("#f_same_item").append(xhr.responseText);
		}
		else {
			if(xhr.responseText == "OK") {
				//alert(xhr.responseText);
				alert(tls("완료되었습니다.")); 
				document.location.reload();
			}
			else {
				alert(tls("실패하였습니다. 다시 시도하시기 바랍니다.") + "[" + xhr.responseText + "]");
			}
		}
	}	

	//레이어 오픈
	function OpenLayer(name, pos_y, obj) {
		var el = "dlayer-" + name; 
		
	    var temp = $('#' + el);
		
	    //var bg = temp.prev().hasClass('bg'); //dimmed 레이어를 감지하기 위한 boolean 변수
		temp.fadeIn();
	
		if(obj) {
			// 클릭한 버튼 위치에 레이어를 띄운다.
			var o = $(obj).position();

			temp.css('top', (o.top) + 'px');
			temp.css('left', (o.left - 500) + 'px');

			//레이어 위에 레이어 호출 시 this 위치를 잘못 찾을경우 첫번째 위치를 사용한다....top : 86px, left : -373px
			if((o.top) <= 86 || (o.left - 500) <= -373) {
				//어트리뷰트 추가 
				temp.css('top', $('#preset_div').attr("top") + 'px');
				temp.css('left', $('#preset_div').attr("left") + 'px');
			}
			else {
				$('#preset_div').attr("top", (o.top));
				$('#preset_div').attr("left", (o.left - 500));				
			}
			
			//console.log(obj);
			//console.log("top : " + (o.top) + "px, left : " + (o.left - 500) + "px");			
			//console.log("preset_div top : " + $('#preset_div').attr("top") + "px, preset_div left : " + $('#preset_div').attr("left") + "px");
		}
		else {
		    // 화면의 중앙에 레이어를 띄운다.
			if(pos_y) {
				temp.css('top', pos_y + 'px');
			}
			else {	    	   	
		    	if (temp.outerHeight() < $(document).height()) temp.css('margin-top', '-' + temp.outerHeight() / 2 + 'px');
		    	else temp.css('top', '0px');
			}
			
		    if (temp.outerWidth() < $(document).width()) temp.css('margin-left', '-' + temp.outerWidth() / 2 + 'px');
		    else temp.css('left', '0px');			
		}		
	
	    temp.find('a.cbtn').click(function (e) {
	        temp.fadeOut();
	        e.preventDefault();
	    });
	}
	
	//항목 추가  
	function openAddOption(optionKindIndex,optionGroupType,haveChild,displayName,extraData1,extraData2,subOptionKindIndex, obj) {
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();
		if(!chk) return false;

		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var preSet =  $('#preset_div').attr("preset");
		
		//어트리뷰트 추가
		$('#optionDataList').attr("optionKindIndex", optionKindIndex);
		$('#optionDataList').attr("optionGroupType", optionGroupType);
		$('#optionDataList').attr("haveChild", haveChild);
		$('#optionDataList').attr("displayName", displayName);
		$('#optionDataList').attr("extraData1", extraData1);
		$('#optionDataList').attr("extraData2", extraData2);
		$('#optionDataList').attr("subOptionKindIndex", subOptionKindIndex);
		
		//2~5차 항목 숨기기
		$(".itemChild").hide();
		
		//타이틀 지정
		$("#title").text(displayName);
		
		//규격이면 "실제 항목 입력" 보이기
		if(optionGroupType == "DOCUMENT")
			$("#extra").show();
		else
			$("#extra").hide();
		
		//책자 프리셋3 (100112) 이고 optionKindIndex가 2 (제본방식) 이면 "실제 항목 입력" 보이기
		if(preSet == "100112" && optionGroupType == "AFTEROPTION" && optionKindIndex == "2") {
			$("#ex1").html(tls("최소 페이지") + " ");
			$("#ex2").html(" / " + tls("최대 페이지"));
			$("#ex3").html('');
			$("#extra").show();
		}
		else {
			$("#ex1").html('');
			$("#ex2").html('x');
			$("#ex3").html('(mm)');			
		}
		
		//후가공이면 "같은 가격 항목 설정" 숨기기 
		if(optionGroupType == "AFTEROPTION") {
			$("#same").hide();
		}
		else { //data 불러오기
			var url = "/lib/extra_option/get_extra_option_item_same.php";
			var param = "&option_kind_index=" + optionKindIndex + "&option_group_type=" + optionGroupType + "&goodsno=" + goodsNo;
			callXHR(url, param, "SAME_ITEM");
		}
		
		//haveChild 에 따라 2~5차 항목 보이기
		if(haveChild != "") {
			var idx = Number(haveChild);
			if(idx > 0){
				for(i = 1; i <= idx; i++) {
					$("#item" + Number(i+1)).show();
				}
			}			
		}
		
		OpenLayer("addoptiondata", "", obj);		
	}

	//항목 관리
	function openEdiOption(opt, idx, obj) {
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();
		if(!chk) return false;		
		
		OpenLayer("edioptionitem", "", obj);
		
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var preSet =  $('#preset_div').attr("preset");
		
		$('#selectedResourceList').attr("option_kind_index", idx);				
	  	var addParam = "&option_kind_index=" + idx;   	
		var url = "/lib/extra_option/get_extra_option_item_tag.php";
		var param = "&goodsno=" + goodsNo + "&preset=" + preSet + addParam;
		
		callXHR(url, param, "EDI_OPTION_ITEM");    	
	}

	//페이지 옵션 추가 
	function openAddPageOption(name, obj, optionKindCode, optionPriceType){
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();
		if(!chk) return false;
			
		//alert(name);		
		$("#page_cnt1").show();
		$("#page_cnt2").hide();		
		$("#page_cnt1-after").show();
		$("#page_cnt2-after").hide();		
		$("#page_unit_cnt1").show();
		$("#page_unit_cnt2").hide();
		$("input:radio[name='cnt_kind']:radio[value='1']").attr("checked",true);
		$("input:radio[name='unit_cnt_kind']:radio[value='1']").attr("checked",true);		
		$("#f_cnt_rule").val("");
		$("#after_f_cnt_rule").val("");
		$("#f_unit_cnt_rule").val("");
		
		//사용자 수량 단위
		$("#user_cnt_rule_name").val("");
    	$("#user_unit_cnt_rule_name").val("");
    	//사용자 수량 입력 여부 
		$("#user_cnt_input_flag").attr("checked", false);
		
		$("#d_cnt_rule").val("");
		$('#div_d_cnt_rule').hide();
		
    	//책자(표지) 수량의 한계치 //고객이 옵션으로 선택하게 되는 수량의 한계치를 입력하세요.
    	if($('#goodskind').val() == "BOOK") {
    		$('#div_d_cnt_rule').show();    		
    		if($("span[id='cnt_rule_"+ optionKindCode +"']")) {
    			$("#d_cnt_rule").val($("span[id='d_cnt_rule_"+ optionKindCode +"']").text());
    		}
    	}		
		
		//구간초기화
		var fi = $(".addcntbox input[name^='cnt_start']");
		if($(fi).length > 1) {
			for(i=$(fi).length;i>1;i--) {
				var rt = $(".addcntbox input[name='cnt_start["+ i +"]']");
				remove_addcnt(rt);
			}
		}
		//구간초기화
		var ai = $(".addcntbox_after input[name^='after_cnt_start']");
		if($(ai).length > 1) {
			for(i=$(ai).length;i>1;i--) {
				var rt = $(".addcntbox_after input[name='after_cnt_start["+ i +"]']");
				remove_addcnt_after(rt);
			}			
		}
		
		if(optionPriceType) {
			//alert(optionPriceType);
			$("input:radio[name='price_type']:radio[value='"+optionPriceType+"']").attr("checked",true);							
			$("input:radio[name='after_price_type']:radio[value='"+optionPriceType+"']").attr("checked",true);
		}
		
		//alert(name);
		//alert(optionKindCode);
		//alert(optionPriceType);		
		//alert("name:"+name+",optionKindCode:"+optionKindCode+",optionPriceType:"+optionPriceType);
		//user_cnt_rule_name_OCNT
    	//user_unit_cnt_rule_name_OCNT
    	
    	//사용자 수량 단위
		$("#user_cnt_rule_name").val($("span[id='user_cnt_rule_name_"+ optionKindCode +"']").text());
    	$("#user_unit_cnt_rule_name").val($("span[id='user_unit_cnt_rule_name_"+ optionKindCode +"']").text()); 
    	//사용자 수량 입력 여부
    	if($("span[id='user_cnt_input_flag_"+ optionKindCode +"']").text() == "Y") {
    		$("#user_cnt_input_flag").attr("checked", true);
    	}
    	    		
		//입력된 정보 출력
		if(name == "page" || name == "page-after") { //페이지설정이면...
			var rule = $("span[id='cnt_rule_"+ optionKindCode +"']").text();
			
			if(rule.indexOf('~') > -1) {
				//각 구간별 수량을 수식으로 설정하여 자동으로 입력
				var ruleArr = rule.split(";");
				//alert(ruleArr.length);
				if(name == "page") {
					$("input:radio[name='cnt_kind']:radio[value='2']").attr("checked",true);
					$("#page_cnt2").show();
					$("#page_cnt1").hide();

					//구간추가
					for(var i=1;i<ruleArr.length-1;i++) {
						add_addcnt();
					}					
					
					//구간값입력
					var input = $(".addcntbox input[name^='cnt_start']");
					if($(input).length > 0) {
						for(i=1;i<=input.length;i++) {
							if(ruleArr[i-1]) {
								//alert(ruleArr[i-1]);
								var rArr = ruleArr[i-1].split("~");
								$(".addcntbox input[name='cnt_start["+ i +"]']").val(rArr[0]);
								$(".addcntbox input[name='cnt_end["+ i +"]']").val(rArr[1]);
								$(".addcntbox input[name='cnt_step["+ i +"]']").val(rArr[2]);
							}							
						}
					}
				}
				else {
					$("input:radio[name='after_cnt_kind']:radio[value='2']").attr("checked",true);
					$("#page_cnt2-after").show();
					$("#page_cnt1-after").hide();					
					
					//구간추가
					for(var i=1;i<ruleArr.length-1;i++) {
						add_addcnt_after();
					}
					
					//구간값입력
					var input = $(".addcntbox_after input[name^='after_cnt_start']");
					if($(input).length > 0) {
						for(i=1;i<=input.length;i++) {
							if(ruleArr[i-1]) {
								//alert(ruleArr[i-1]);
								var rArr = ruleArr[i-1].split("~");
								$(".addcntbox_after input[name='after_cnt_start["+ i +"]']").val(rArr[0]);
								$(".addcntbox_after input[name='after_cnt_end["+ i +"]']").val(rArr[1]);
								$(".addcntbox_after input[name='after_cnt_step["+ i +"]']").val(rArr[2]);
							}							
						}
					}
				}
			}
			else {
				//각 구간별 수량을 직접 하나 하나 입력
				if(name == "page") {
					$("input:radio[name='cnt_kind']:radio[value='1']").attr("checked",true);
					$("#f_cnt_rule").val(rule);
				}
				else {
					$("input:radio[name='after_cnt_kind']:radio[value='1']").attr("checked",true);
					$("#after_f_cnt_rule").val(rule);
				}
			}			
		}
		else { //수량,부수,권수,명수 설정이면...
			var rule = $("span[id='unit_cnt_rule_"+ optionKindCode +"']").text();
			if(rule.indexOf('~') > -1) {
				$("input:radio[name='unit_cnt_kind']:radio[value='2']").attr("checked",true);
				$("#page_unit_cnt2").show();
				$("#page_unit_cnt1").hide();
				
				//구간값입력
				/*
				rule = rule.replace(";","");
				var ruleArr = rule.split("~");
				$("#unit_cnt_start").val(ruleArr[0]);	
				$("#unit_cnt_end").val(ruleArr[1]);
				$("#unit_cnt_step").val(ruleArr[2]);
				*/
				
				//각 구간별 수량을 수식으로 설정하여 자동으로 입력
				var ruleArr = rule.split(";");
				//구간추가
				for(var i=1;i<ruleArr.length-1;i++) {
					add_addunitcnt();
				}					
				
				//구간값입력
				var input = $(".addunitcntbox input[name^='unit_cnt_start']");
				if($(input).length > 0) {
					for(i=1;i<=input.length;i++) {
						if(ruleArr[i-1]) {
							//alert(ruleArr[i-1]);
							var rArr = ruleArr[i-1].split("~");
							$(".addunitcntbox input[name='unit_cnt_start["+ i +"]']").val(rArr[0]);
							$(".addunitcntbox input[name='unit_cnt_end["+ i +"]']").val(rArr[1]);
							$(".addunitcntbox input[name='unit_cnt_step["+ i +"]']").val(rArr[2]);
						}							
					}
				}				
				
			}
			else {
				$("#f_unit_cnt_rule").val(rule);
			}
		}		

		//어트리뷰트 추가
		$('#preset_div').attr("optionKindCode", optionKindCode);
		OpenLayer(name, "", obj);
	}

	//가격항목엑셀파일불러오기 
	function openFile(e, obj){
		if(!e) e = window.Event;
    	pos = abspos(e);
		OpenLayer('openfile', pos.y, obj);
	}

	function abspos(e){
	    this.x = e.clientX + (document.documentElement.scrollLeft?document.documentElement.scrollLeft:document.body.scrollLeft);
	    this.y = e.clientY + (document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop);
	    return this;
	}
	
	/*
	//가격항목창불러오기
	function openPrice(mode, kind, filename){
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

        if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "extra_option_price.check.php?mode=" + mode + "&kind=" + kind + "&goodsno=" + goodsNo +"&filename=" + filename;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}	
	 	else {
	 		return false;
	 	}		
	}

	//가격항목창불러오기
	function openPriceAfter(kind, kindUse, filename){
		//사용여부에 따라 처리
		if(kindUse == "N") {
			alert("사용여부를 확인해 주세요.");
			return false;	
		}		
		
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

        if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "extra_option_price.check.php?mode=AFTER&kind=" + kind + "&goodsno=" + goodsNo +"&filename=" + filename;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}	
	 	else {
	 		return false;
	 	}		
	}
	
	
	function openPriceS2(mode, kind, filename, presetcode){
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

		//테이블 타입
		var priceTableType = $('input[name="priceTableType"]:checked').val();		

   		if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "extra_option_price.check_s2.php?mode=" + mode + "&kind=" + kind + "&goodsno=" + goodsNo +"&presetcode=" + presetcode + "&ptType=" + priceTableType +"&docUse=&filename=" + filename;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}	
	 	else {
	 		return false;
	 	}		
	}

	//가격항목창불러오기
	function openPriceAfterS2(kind, kindUse, filename, presetcode){
		//사용여부에 따라 처리
		if(kindUse == "N") {
			alert("사용여부를 확인해 주세요.");
			return false;	
		}		
		
		//테이블 타입
		var priceTableType = $('input[name="priceTableType"]:checked').val();
		var documentUse = $('#documentUse').is(":checked") ? "Y" : "";
				
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

   		if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "extra_option_price.check_s2.php?mode=AFTER&kind=" + kind + "&goodsno=" + goodsNo + "&ptType=" + priceTableType + "&docUse=" + documentUse +"&filename=" + filename;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}	
	 	else {
	 		return false;
	 	}		
	}

	function openPriceS3(mode, kind, filename, presetcode){
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

		//테이블 타입
		var priceTableType = $('input[name="priceTableType"]:checked').val();		

   		if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "extra_option_price.check_s3.php?mode=" + mode + "&kind=" + kind + "&goodsno=" + goodsNo +"&presetcode=" + presetcode + "&ptType=" + priceTableType +"&docUse=&filename=" + filename;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}
	 	else {
	 		return false;
	 	}
	}

	//가격항목창불러오기
	function openPriceAfterS3(kind, kindUse, filename, presetcode){
		//사용여부에 따라 처리
		if(kindUse == "N") {
			alert("사용여부를 확인해 주세요.");
			return false;	
		}		
		
		//테이블 타입
		var priceTableType = $('input[name="priceTableType"]:checked').val();
		var documentUse = $('#documentUse').is(":checked") ? "Y" : "";
				
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

   		if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "extra_option_price.check_s3.php?mode=AFTER&kind=" + kind + "&goodsno=" + goodsNo + "&ptType=" + priceTableType + "&docUse=" + documentUse +"&filename=" + filename;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}	
	 	else {
	 		return false;
	 	}
	}
	*/

	//a관리자로 링크 수정 / 20170901 / kdk
	function openPrice(mode, kind, filename){
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

		//테이블 타입
		var priceTableType = $('input[name="priceTableType"]:checked').val();		

   		if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "/a/goods/extra_option_price.check.php?mode=" + mode + "&kind=" + kind + "&goodsno=" + goodsNo + "&ptType=" + priceTableType +"&docUse=&filename=" + filename;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}
	 	else {
	 		return false;
	 	}
	}

	//가격항목창불러오기
	function openPriceAfter(kind, kindUse, filename){
		//사용여부에 따라 처리
		if(kindUse == "N") {
			alert(tls("사용여부를 확인해 주세요."));
			return false;	
		}		
		
		//테이블 타입
		var priceTableType = $('input[name="priceTableType"]:checked').val();
		var documentUse = $('#documentUse').is(":checked") ? "Y" : "";
				
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

   		if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "/a/goods/extra_option_price.check.php?mode=AFTER&kind=" + kind + "&goodsno=" + goodsNo + "&ptType=" + priceTableType + "&docUse=" + documentUse +"&filename=" + filename;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}	
	 	else {
	 		return false;
	 	}
	}


	//수량(건수)가격항목창불러오기 2014.12.23 by kdk
	function openPriceUnit(){
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

        if(chk) {
			var goodsNo = $('#goodsno').val();		
			var src = "/a/goods/extra_option_unit_price.check.php?goodsno=" + goodsNo;
			window.open(src,'','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
	 	}	
	 	else {
	 		return false;
	 	}		
	}
	
	//항목 저장 
	function saveOptionData() {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var preSet =  $('#preset_div').attr("preset");
		
		//어트리뷰트
		var optionKindIndex = $('#optionDataList').attr("optionKindIndex");
		var optionGroupType = $('#optionDataList').attr("optionGroupType");
		var optionKindCode = $('#optionDataList').attr("optionKindCode");
		if(typeof(optionKindCode) =="undefined") optionKindCode = "";
		
		//var optionSubKindCode = $('#optionDataList').attr("optionSubKindCode");
		var haveChild = $('#optionDataList').attr("haveChild");
		var displayName = $('#optionDataList').attr("displayName");
		var extraData1 = $('#optionDataList').attr("extraData1");
		var extraData2 = $('#optionDataList').attr("extraData2");
		var subOptionKindIndex = $('#optionDataList').attr("subOptionKindIndex");
		//alert(optionKindIndex+","+optionGroupType+","+optionKindCode+","+optionSubKindCode+","+haveChild+","+displayName+","+extraData1+","+extraData2);
		
		var f_item_name = $('#f_item_name').val();
		
		//규격이면
		if(optionGroupType == "DOCUMENT") {
			extraData1 = $('#f_extra_data1').val();
			extraData2 = $('#f_extra_data2').val();
		}
	
		//책자 프리셋3 (100112) 이고 optionKindIndex가 2 (제본방식) 이면 "실제 항목 입력" 보이기
		if(preSet == "100112" && optionGroupType == "AFTEROPTION" && optionKindIndex == "2") {
			extraData1 = $('#f_extra_data1').val();
			extraData2 = $('#f_extra_data2').val();
		}
		
		var f_same_item = $('#f_same_item').val();
		var f_item_name2 = $('#f_item_name2').val();
		var f_item_name3 = $('#f_item_name3').val();
		var f_item_name4 = $('#f_item_name4').val();
		var f_item_name5 = $('#f_item_name5').val();
		
		if(f_item_name == "") {
			alert(tls("항목을 입력하세요."));
			return false;
		}
		
		//alert(f_item_name+","+extraData1+","+extraData2+","+f_same_item+","+f_item_name2+","+f_item_name3+","+f_item_name4+","+f_item_name5);
		
		//전송
		var addParam = "&option_kind_index=" + optionKindIndex + "&option_group_type=" + optionGroupType;
		//addParam += "&option_kind_code=" + optionKindCode + "&option_sub_kind_code=" + optionSubKindCode;
		addParam += "&option_kind_code=" + optionKindCode;
		addParam += "&have_child=" + haveChild + "&display_name=" + displayName;
		addParam += "&extra_data1=" + extraData1 + "&extra_data2=" + extraData2;
		addParam += "&f_item_name=" + f_item_name + "&f_same_item=" + f_same_item;
		addParam += "&f_item_name2=" + f_item_name2 + "&f_item_name3=" + f_item_name3;
		addParam += "&f_item_name4=" + f_item_name4 + "&f_item_name5=" + f_item_name5;
		addParam += "&sub_option_kind_index="+subOptionKindIndex;
		//alert(addParam);
		
		var url = "/lib/extra_option/set_extra_option_data.php";
		var param = "&goodsno=" + goodsNo + addParam;
		
		callXHR(url, param, "");
	}
	
	//기타 후가공 추가 
	function saveEtcOptionData(obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var goods_kind = $(':radio[name="goods_kind"]:checked').val();
		//var baseOptionID = $("#item_select_PP_0 option:eq(0)").val(); //기준이 되는 option_id 하위 데이타를 조회시 사용한다.
		//var preset = $("#preset_div").attr("preset");
		var addoptbox_idx = $(obj).closest(".addoptbox_div").attr("addoptbox_idx");

		var addopt_group_view = "";
		var addopt_group_name = "";
		var addopt_price_view = "TIME";
		var addopt = "";
		
		var tb1 = $(obj).closest(".addoptbox_div").children(".addoptbox:first");
		if($(tb1).length > 0) {
			addopt_group_name = $($(tb1).find("input[name^='addopt_group_']")[0]).val();	
			addopt_group_view = $($(tb1).find("select[name^='addopt_group_']")[0]).val();
			//addopt_price_view = $($(tb1).find("select[name^='addopt_price_']")[0]).val();			
		}

		var tb2 = $(obj).closest(".addoptbox_div").children(".addoptbox:last");
		var tr = $(tb2).find("tr");
		if(tr.length > 0) {
			for(i=0;i<tr.length;i++) {
				//addopt += $(tr[i]).find("select").val()+"|"+$(tr[i]).find("input").val() +",";
				addopt += $(tr[i]).find("input").val() +",";
			}			
		}		
		
		if(addopt_group_name == "") {
			alert(tls("후가공옵션명을 입력하세요."));
			return false;
		}
		
		if(addopt == ",") {
			alert(tls("후가공항목명을 입력하세요."));
			return false;
		}
		
	  var addParam = "&imode=AFTER_NEW&option_group_type=AFTEROPTION&use_flag=" + addopt_group_view + "&display_name=" + addopt_group_name + "&f_item_name=" + addopt + "&item_price_type=" + addopt_price_view;
		
		var url = "/lib/extra_option/set_extra_option_data.php";
		var param = "&goodsno=" + goodsNo + addParam;
		//alert(param);
		
		callXHR(url, param, "");
	}	
		  	
	//페이지 설정 savePageOptionData
	function savePageOptionData(mode) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var cnt_rule = "";
		var cnt_start = "";
		var cnt_end = "";
		var cnt_step = "";
						
		if(mode == "cnt") {
			var cnt_kind = $(':radio[name="cnt_kind"]:checked').val();
			if(cnt_kind == "1") {
				cnt_rule = $("#f_cnt_rule").val();	
			}
			else {
				var input = $(".addcntbox input[name^='cnt_end']");
				if($(input).length > 0) {
					for(i=0;i<input.length;i++) {
						cnt_start = $(".addcntbox input[name^='cnt_start']:eq("+ i +")").val();
						cnt_end = $(".addcntbox input[name^='cnt_end']:eq("+ i +")").val();
						cnt_step = $(".addcntbox input[name^='cnt_step']:eq("+ i +")").val();
						if(cnt_start == "") continue;
						
						////cnt_start = $(".addcntbox span[name='cnt_start["+ i +"]']").text();	
						//cnt_start = $(".addcntbox input[name='cnt_start["+ i +"]']").val();
						//cnt_end = $(".addcntbox input[name='cnt_end["+ i +"]']").val();
						//cnt_step = $(".addcntbox input[name='cnt_step["+ i +"]']").val();
						
						cnt_rule += cnt_start + "~" + cnt_end + "~" + cnt_step + ";";
					}
				}
			}
		}
		else {
			var cnt_kind = $(':radio[name="unit_cnt_kind"]:checked').val();
			if(cnt_kind == "1") {
				cnt_rule = $("#f_unit_cnt_rule").val();	
			}
			else {
				var input = $(".addunitcntbox input[name^='unit_cnt_end']");
				if($(input).length > 0) {
					for(i=0;i<input.length;i++) {
						cnt_start = $(".addunitcntbox input[name^='unit_cnt_start']:eq("+ i +")").val();
						cnt_end = $(".addunitcntbox input[name^='unit_cnt_end']:eq("+ i +")").val();
						cnt_step = $(".addunitcntbox input[name^='unit_cnt_step']:eq("+ i +")").val();
						if(cnt_start == "") continue;
						
						cnt_rule += cnt_start + "~" + cnt_end + "~" + cnt_step + ";";
					}
				}
								
				/*
				cnt_start = $("#unit_cnt_start").val();	
				cnt_end = $("#unit_cnt_end").val();
				cnt_step = $("#unit_cnt_step").val();

				cnt_rule += cnt_start + "~" + cnt_end + "~" + cnt_step + ";";
				*/				
			}
		}
		//alert(cnt_rule);
		
		var optionKindCode = ""; 
		if($('#preset_div').attr("optionKindCode")) {
			optionKindCode = $('#preset_div').attr("optionKindCode");
		}
		
		var priceType = $(':radio[name="price_type"]:checked').val();
		
	    var addParam = "&mode=" + mode + "&rule=" + cnt_rule + "&option_kind_code=" + optionKindCode + "&price_type=" + priceType;
    	
    	//책자(표지) 수량의 한계치 //고객이 옵션으로 선택하게 되는 수량의 한계치를 입력하세요.
    	if($('#goodskind').val() == "BOOK") {
    		//alert($('#d_cnt_rule').val());
    		addParam += "&d_cnt_rule="+ $('#d_cnt_rule').val();
    	}    	

		//사용자 수량 단위
    	addParam += "&user_cnt_rule_name="+ $('#user_cnt_rule_name').val();
    	addParam += "&user_unit_cnt_rule_name="+ $('#user_unit_cnt_rule_name').val();
    	//사용자 수량 입력 여부
    	var user_cnt_input_flag = $('#user_cnt_input_flag').is(":checked") ? "Y" : "N";
		addParam += "&user_cnt_input_flag="+ user_cnt_input_flag;
    	
		var url = "/lib/extra_option/set_extra_option_order_cnt.php";
		var param = "&goodsno=" + goodsNo + addParam;
		//alert(param);
		callXHR(url, param, "PAGE");    	
	}  	
	
	//후가공 페이지 설정 savePageOptionDataAfter
	function savePageOptionDataAfter() {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var cnt_rule = "";
		var cnt_start = "";
		var cnt_end = "";
		var cnt_step = "";

		var cnt_kind = $(':radio[name="after_cnt_kind"]:checked').val();
		if(cnt_kind == "1") {
			cnt_rule = $("#after_f_cnt_rule").val();	
		}
		else {
			var input = $(".addcntbox_after input[name^='after_cnt_end']");
			if($(input).length > 0) {
				for(i=0;i<input.length;i++) {					
					cnt_start = $(".addcntbox_after input[name^='after_cnt_start']:eq("+ i +")").val();
					cnt_end = $(".addcntbox_after input[name^='after_cnt_end']:eq("+ i +")").val();
					cnt_step = $(".addcntbox_after input[name^='after_cnt_step']:eq("+ i +")").val();
					if(cnt_start == "") continue;
									
				//for(i=1;i<=input.length;i++) {
					//cnt_start = $(".addcntbox_after input[name='after_cnt_start["+ i +"]']").val();
					//cnt_end = $(".addcntbox_after input[name='after_cnt_end["+ i +"]']").val();
					//cnt_step = $(".addcntbox_after input[name='after_cnt_step["+ i +"]']").val();
					
					cnt_rule += cnt_start + "~" + cnt_end + "~" + cnt_step + ";";
				}
			}
		}
		//alert(cnt_rule);
		
		var optionKindCode = ""; 
		if($('#preset_div').attr("optionKindCode")) {
			optionKindCode = $('#preset_div').attr("optionKindCode");
		}
		
		var priceType = $(':radio[name="after_price_type"]:checked').val();
		
	    var addParam = "&imode=after&mode=cnt&rule=" + cnt_rule + "&option_kind_code=" + optionKindCode + "&price_type=" + priceType;
    	
		var url = "/lib/extra_option/set_extra_option_order_cnt.php";
		var param = "&goodsno=" + goodsNo + addParam;
		//alert(param);
		callXHR(url, param, "PAGE");
	}
	
	//노출 여부 처리
	function saveDisplayFlag(obj, idx) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var flag = $('select[name="' + obj + '"] option:selected').val();
		
	    var addParam = "&option_kind_index=" + idx + "&flag=" + flag;
    	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		var param = "&mode=display_flag&goodsno=" + goodsNo + addParam;
		//alert(param);
		callXHR(url, param, "AFTEROPTION");     	
	}
	
    //출력 순처 처리
    function saveOrderIndex() {
        var vals = "";
        $('#selectedResourceList').find("input[type='hidden']").each(function () {
            vals += $(this).val() + "|";
        });
        
        //var goodsNo = document.fmView.goodsno.value;
        var goodsNo = $('#goodsno').val();
        var idx = $('#selectedResourceList').attr("option_kind_index");
		var opt = $('#selectedResourceList').attr("option_group_type");
		
	    var addParam = "&option_kind_index=" + idx + "&option_group_type=" + opt + "&vals=" + vals;
    	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		var param = "&mode=order_index&goodsno=" + goodsNo + addParam;
		
		callXHR(url, param, "");    	
    }	

	//사용 여부 처리
	function saveUseFlag(obj) {
		var sFlag = false;		
		var val = "";
	    if ($(obj).length == 1) {
	        var li = $(obj).closest("li");
	        //var val = $(li).find("input[type='hidden']").val();
	        var val = $(li).find("input[name='item_name']").val();
	        var flag = $(li).find("select[name='regist_flag']").val();
	        var optkc = $(li).find("input[name='option_kind_code']").val();
	    }

		//같은 가격 항목 설정이 있는지 확인 2015.01.21 by kdk	    
	    if($("input[name='same_price_item_name']").length > 1) {
	    	for(i=0;i<$("input[name='same_price_item_name']").length;i++) {					
				if(val == $("input[name='same_price_item_name']:eq("+ i +")").val()) sFlag = true;
			}    	
	    }

		if(sFlag && flag == "N") {
			alert("'"+ val +"' " + tls("을(를) 같은 가격으로 설정한 항목이 있습니다.") + "\n\n" + tls("해당 항목을 먼저 삭제해야 합니다."));
		}
		else {
	        //var goodsNo = document.fmView.goodsno.value;
	        var goodsNo = $('#goodsno').val();
	        var idx = $('#selectedResourceList').attr("option_kind_index");
			var opt = $('#selectedResourceList').attr("option_group_type");
			
		    var addParam = "&option_kind_index=" + idx + "&option_group_type=" + opt + "&option_kind_code=" + optkc + "&val=" + val + "&flag=" + flag;
	    	
			var url = "/lib/extra_option/set_extra_option_item_update.php";
			var param = "&mode=use_flag&goodsno=" + goodsNo + addParam;
			
			callXHR(url, param, "");
		}
	}
	
	//삭제 처리
	function saveRegistFlag(obj) {
		var sFlag = false;
		var val = "";
	    if ($(obj).length == 1) {
	        var li = $(obj).closest("li");
	        //var val = $(li).find("input[type='hidden']").val();
	        var val = $(li).find("input[name='item_name']").val();
	        var flag = $(li).find("select[name='regist_flag']").val();
	        var optkc = $(li).find("input[name='option_kind_code']").val();
	    }
		
		//최소 1개를 유지하기 위해 수량 체크 후 메시지 출력
		if($("input[name='item_name']").length <= 1) {
			alert(tls("마지막 항목은 삭제할 수 없습니다."));
			return false;
		}    	

		//같은 가격 항목 설정이 있는지 확인 2015.01.21 by kdk	    
	    if($("input[name='same_price_item_name']").length > 1) {
	    	for(i=0;i<$("input[name='same_price_item_name']").length;i++) {					
				if(val == $("input[name='same_price_item_name']:eq("+ i +")").val()) sFlag = true;
			}    	
	    }
	    
		if(sFlag) {
			alert("'"+ val +"' " + tls("을(를) 같은 가격으로 설정한 항목이 있습니다.") + "\n\n" + tls("해당 항목을 먼저 삭제해야 합니다."));
		}
		else {
			if (confirm(tls("삭제하시겠습니까?")))
			{				
		        //var goodsNo = document.fmView.goodsno.value;
		        var goodsNo = $('#goodsno').val();
		        var idx = $('#selectedResourceList').attr("option_kind_index");
				var opt = $('#selectedResourceList').attr("option_group_type");
				
			    var addParam = "&option_kind_index=" + idx + "&option_group_type=" + opt + "&option_kind_code=" + optkc + "&val=" + val + "&flag=" + flag;
		    	
				var url = "/lib/extra_option/set_extra_option_item_update.php";
				var param = "&mode=regist_flag&goodsno=" + goodsNo + addParam;
				
				callXHR(url, param, "");    	
			}
		}
	}

	//노출 여부 & 후가공가격타입 처리
	function saveDisplayFlagNitemPriceType(obj, obj2, idx) {				
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var flag = $('select[name="' + obj + '"] option:selected').val();
		var type = $('select[name="' + obj2 + '"] option:selected').val();
		
	    var addParam = "&option_kind_index=" + idx + "&flag=" + flag + "&type=" + type;    	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		var param = "&mode=displayflagNitemPriceType&goodsno=" + goodsNo + addParam;
		
		//alert(param);
		
		callXHR(url, param, "AFTEROPTION");     	
	}

	//가격 엑셀 파일 불러오기
	function saveFile() {
		if($("#excelFile").val() == "") {
			alert(tls("파일을 선택하세요."));
			return false;
		}
		else {
			//alert($("#excelFile").val());
			document.forms.myForm.submit();
		}
		
	}

	//가격 엑셀 파일 불러오기 파일 체크
	/*$("#excelFile").change(function() {
	    var val = $(this).val();
		var ext = val.substring(val.lastIndexOf('.') + 1).toLowerCase();
	
		//alert(val);
		//alert(ext);
	
		if(ext != "xls") {
			alert("엑셀 파일을 선택하세요.[not an excel]");
			//alert("not an excel");
			//$(this).val("");
			$(this).replaceWith('<input type="file" id="excelFile" name="excelFile" />');
		}
	});*/

	//같은 가격 항목 설정시 2~5차 항목 비활성화
  	function SameItemSelected(sender) {
  		if($(sender).val() == "") {
  			$(".fItemName").attr("disabled",false);
  		}
  		else {
  			$(".fItemName").val("");
  			$(".fItemName").attr("disabled",true);
  		}
  	}

	function CntKindChange(name) {
		//alert(name);
		$(".page_cnt").hide();
		$("#" + name).show();
	}

	function UnitCntKindChange(name) {
		//alert(name);
		$(".page_cnt").hide();
		$("#" + name).show();
	}	 

	//위로이동
	function MoveUpItem(obj) {
	    if ($(obj).length == 1) {
	        var li=$(obj).closest("li");
	        selIdx = $(li).index();
	        if (selIdx > 0) {
	            var p = $(li).parent();
	            $(li).remove();
	            $(p).find('li').eq(selIdx - 1).before(li); 
	        }
	    }
	}

	function MoveDownItem(obj) {
	    if ($(obj).length == 1) {
	        var li = $(obj).closest("li");
	        selIdx = $(li).index();
	        if (selIdx < $(li).parent().children().length - 1) {
	            var p = $(li).parent();
	            $(li).remove();
	            $(p).find('li').eq(selIdx).after(li); 
	        }
	    }
	}

    function showLoading() {
        if ($('#loading_div').is(':hidden')) {
            $('#loading_back').show();
            $('#loading_div').show();
        }
    }

    function hideLoading() {
        $('#loading_div').hide();
        $('#loading_back').hide();
    }
    
    //display name 팝업  
	function openDisplayName(optionKindIndex,displayName,obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		//어트리뷰트 추가
		$('#optionDataList').attr("optionKindIndex", optionKindIndex);
		$('#optionDataList').attr("display_name", displayName);
		
		//타이틀 지정
		$("#title").text(displayName);
				
		OpenLayer("displayname", "", obj);		
	}
	
	//display name 처리
	function saveDisplayName(obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();		
    	var idx = $('#optionDataList').attr("optionKindIndex");
    	var old_display_name = $('#optionDataList').attr("display_name");
    	var input = $('#f_display_name').val();
    		
	  	var addParam = "&option_kind_index=" + idx + "&update_name=" + input + "&old_display_name=" + old_display_name;    	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		var param = "&mode=displaynameUpdate&goodsno=" + goodsNo + addParam;
		
		//alert(param);
		
		callXHR(url, param, "AFTEROPTION");     	
	}

    //cnt display name 팝업  
	function openCntDisplayName(optionKindCode,displayName,obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		//어트리뷰트 추가
		$('#optionDataList').attr("optionKindCode", optionKindCode);
		$('#optionDataList').attr("display_name", displayName);
		
		//타이틀 지정
		$("#title").text(displayName);
				
		OpenLayer("cntdisplayname", "", obj);		
	}
	
	//cnt display name 처리
	function saveCntDisplayName(obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();		
    	var code = $('#optionDataList').attr("optionKindCode");
    	var old_display_name = $('#optionDataList').attr("display_name");
    	var input = $('#f_cnt_display_name').val();
    		
	  	var addParam = "&option_kind_code=" + code + "&update_name=" + input + "&old_display_name=" + old_display_name;    	
		var url = "/lib/extra_option/set_extra_option_order_cnt.php";
		var param = "&mode=displaynameUpdate&goodsno=" + goodsNo + addParam;
		
		//alert(param);
		
		callXHR(url, param, "AFTEROPTION");     	
	}
	
	function openItemNameUpdate(optionKindIndex, itemIndex, subOptionKindIndex, itemName, obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		//어트리뷰트 추가
		$('#optionDataList').attr("optionKindIndex", optionKindIndex);
		$('#optionDataList').attr("optionItemIndex", itemIndex);
		$('#optionDataList').attr("subOptionKindIndex", subOptionKindIndex);
		
		$('#optionDataList').attr("item_name", itemName);
		
		//타이틀 지정
		$("#title").text(itemName);
				
		OpenLayer("itemnameupdate", "", obj);		
	}

    //display name 팝업  
	function openItemNameUpdateS2(optionKindIndex, optionItemIndex, itemName, obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
				
		//어트리뷰트 추가
		$('#optionDataList').attr("optionKindIndex", optionKindIndex);
		$('#optionDataList').attr("optionItemIndex", optionItemIndex);
		$('#optionDataList').attr("item_name", itemName);
		
		//타이틀 지정
		$("#title").text(itemName);
		
		//값지정
		$("#f_update_item_nameS2").val(itemName);

		OpenLayer("itemnameupdateS2", "", obj);		
	}

    //display name 팝업  
	function openItemNameUpdateS3(optionKindIndex, optionItemIndex, itemName, optionGroupType, extData1, extData2, obj) {
		//alert(optionKindIndex +","+ optionItemIndex +","+ itemName +","+ optionGroupType +","+ extData1 +","+ extData2);
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var preSet =  $('#preset_div').attr("preset");
				
		//어트리뷰트 추가
		$('#optionDataList').attr("optionKindIndex", optionKindIndex);
		$('#optionDataList').attr("optionItemIndex", optionItemIndex);
		$('#optionDataList').attr("item_name", itemName);
		$('#optionDataList').attr("extra_data1", extData1);
		$('#optionDataList').attr("extra_data2", extData2);
		
		//타이틀 지정
		$("#title").text(itemName);
		
		//값지정
		$("#f_update_item_nameS3").val(itemName);
		$("#f_extra_data1S3").val(extData1);
		$("#f_extra_data2S3").val(extData2);
		
		//책자 프리셋3 (100112) 이고 optionKindIndex가 2 (제본방식) 이면 "실제 항목 입력" 보이기
		if(preSet == "100112" && optionGroupType == "AFTEROPTION" && optionKindIndex == "2") {
			$("#ex1S3").html(tls("최소 페이지") + " ");
			$("#ex2S3").html(" / " + tls("최대 페이지"));
			$("#ex3S3").html('');			
		}
		else {
			$("#ex1S3").html('');
			$("#ex2S3").html('x');
			$("#ex3S3").html('(mm)');						
		}			 
		
		OpenLayer("itemnameupdateS3", "", obj);		
	}

	//책자 프리셋3 (100112)
	function saveItemNameUpdateS3(obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();		
    	var idx = $('#optionDataList').attr("optionKindIndex");
    	var item_idx = $('#optionDataList').attr("optionItemIndex");
    	var old_item_name = $('#optionDataList').attr("item_name");
    	var input = $('#f_update_item_nameS3').val();
    	var extra_data1 = $('#f_extra_data1S3').val();
    	var extra_data2 = $('#f_extra_data2S3').val();
    	    		
	  	var addParam = "&option_kind_index=" + idx + "&option_item_index=" + item_idx + "&update_name=" + input + "&old_item_name=" + old_item_name + "&extra_data1=" + extra_data1 + "&extra_data2=" + extra_data2;
	  	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		var param = "&mode=itemNameUpdateS3&goodsno=" + goodsNo + addParam;
		
		//alert(param);
		
		callXHR(url, param, "AFTEROPTION");     	
	}
	
	function saveItemNameUpdateS2(obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();		
    	var idx = $('#optionDataList').attr("optionKindIndex");
    	var item_idx = $('#optionDataList').attr("optionItemIndex");
    	var old_item_name = $('#optionDataList').attr("item_name");
    	var input = $('#f_update_item_nameS2').val();
    		
	  	var addParam = "&option_kind_index=" + idx + "&option_item_index=" + item_idx + "&update_name=" + input + "&old_item_name=" + old_item_name;    	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		var param = "&mode=itemNameUpdateS2&goodsno=" + goodsNo + addParam;
		
		//alert(param);
		
		callXHR(url, param, "AFTEROPTION");     	
	}
	
	//노출 여부 & 후가공가격타입 처리
	function saveItemNameUpdate(obj) {
		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();		
    	var idx = $('#optionDataList').attr("optionKindIndex");
    	var item_idx = $('#optionDataList').attr("optionItemIndex");
    	var sub_kind_idx = $('#optionDataList').attr("subOptionKindIndex");    
    
    	var old_item_name = $('#optionDataList').attr("item_name");
    	var input = $('#f_update_item_name').val();
    		
	  	var addParam = "&option_kind_index=" + idx + "&option_item_index=" + item_idx + "&sub_kind_index=" + sub_kind_idx + "&update_name=" + input + "&old_item_name=" + old_item_name;    	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		var param = "&mode=itemNameUpdate&goodsno=" + goodsNo + addParam;
		
		//alert(param);
		
		callXHR(url, param, "AFTEROPTION");     	
	}
	
	//판매중인 몰 확인 2014.08.26 kdk
	function checkGoodsMallUseFlag() {		
		if(goods_mall_use_flag == "N") 
			return true;
		else {
			var msg = tls("본 상품은 1개 이상의 분양몰에서 이미 상품으로 연결되고 진열되어 있기에 판매정보 외에는 가격설정을 등을 포함한 모든 상품설정을 수정할 수가 없습니다.");
			
		  	if (confirm(msg)) {
				return true;
			}
			else {
				return false;
			}			
			
			return false;
		}
	}

	//책자 프리셋3 (100112) 페이지 트리 오픈.   
	function openPageTree(optionGroupType, displayData) {
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();
		if(!chk) return false;

		//var goodsNo = document.fmView.goodsno.value;
		var goodsNo = $('#goodsno').val();
		var preSet =  $('#preset_div').attr("preset");

		popup('./extra_option_preset_page_tree.php?goodsno='+goodsNo+'&preset='+preSet+'&optgt='+optionGroupType+'&displayData='+displayData,580,700);				
	}
