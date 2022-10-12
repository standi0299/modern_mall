  
  function forwardOrderCntAction() {
  	//console.log("forwardOrderCntAction:시작!");
    //초기화
    initPrice();
    //console.log("extra_auto_pay_flag:"+$("#extra_auto_pay_flag").val());
    
    if($("#extra_auto_pay_flag").val() == "1") { //견적의뢰는 금액 호출을 하지 않는다.(속도개선)
	    //각 옵션별로 마지막 옵션을 찾아서 forwardAction 를 호출한다.
	    //console.log("optino_group_code:" + optino_group_code);
	    for (var i = 0; i < optino_group_code.length; i++) {
	      if (optino_group_code[i])
	      {
	      	if(optino_group_code[i] != "DOCUMENT") {
	      		//console.log("optino_group_code[]:" + optino_group_code[i]);
	        	priceAction(optino_group_code[i]);
	        }  
	      }
	    }    	
    }
    
  }

  function documentOnchange(sender) {   
    //규격을 변경한경우 규격 사이즈 출력.
    var option_group_type = $(sender).attr('option_group_type');
    var code = $(sender).attr('code');
    //console.log("documentOnchange().code:" + code);
    
	if (option_group_type == 'DOCUMENT') {
		
		var document_x = $("input[id='document_x'][option_group_type='"+option_group_type+"'][code='"+code+"']");
		var document_y = $("input[id='document_y'][option_group_type='"+option_group_type+"'][code='"+code+"']");
		
		//console.log(document_x);
		//console.log(document_y);
		//console.log($(document_x).is("input"));
		//console.log($(document_y).is("input"));

		if (!$(document_x).is("input")) document_x = $("input[id='document_x'][option_group_type='"+option_group_type+"']");
		if (!$(document_y).is("input")) document_y = $("input[id='document_y'][option_group_type='"+option_group_type+"']");
		
    	$(document_x).attr("readonly",false);
    	$(document_y).attr("readonly",false);

    	for(key in document_size) {

			if(sender.options[sender.selectedIndex].text == '<?echo _("비규격")?>') {
				$(document_x).val("");
				$(document_y).val("");
			}    		
    						
			if (key == sender.options[sender.selectedIndex].text) {
				
    			$(document_x).val(document_size[key].substr(0, document_size[key].indexOf('x')));
    			$(document_y).val(document_size[key].substr(document_size[key].indexOf('x')+1, document_size[key].length));
				
				if ($(document_x).val())
					$(document_x).attr("readonly",true);
				
				if ($(document_y).val())
					$(document_y).attr("readonly",true);
			}
		}
	}
	
	forwardOrderCntAction();
  }

  function orderCntChange(sender) {
  	//페이지를 변경한 경우
    var div = $(sender).closest("div");
    div = $(div).parent();
    //console.log(div.html());
    
	var selectOption = $(div).find("select");
	var code = new Array();
	
    for (var i = 0; i < selectOption.length; i++) {
    	//console.log($(selectOption[i]).attr("code"));
    	if($(sender).attr('code') != $(selectOption[i]).attr("code")) {
    		if(code.indexOf($(selectOption[i]).attr("code")) == -1)
    			code.push($(selectOption[i]).attr("code"));
    	}    	
    }
    
    //console.log(code);
    for (var i = 0; i < code.length; i++) {
    	//console.log(code[i]);
    	priceAction(code[i]);
    }
    
    //책자 프리셋 내지 페이지를 변경할 경우 후가공옵션 가격 갱신을 위해 2015.06.08 by kdk
    forwardOrderCntAction();
  }

  function forwardAction(sender) {
    var code = $(sender).attr('code');
	var group_code = $(sender).attr('option_group_type');

	//console.log("forwardAction : code : "+ code +" , group_code : "+ group_code);

    var nextSelect = getNextSelect(sender);
    if (nextSelect != null)
    {
      	//자신의 하위 옵션까지 항목을 변경해야 한다.          
      	//console.log("forwardAction : nextSelect.name:"+nextSelect.name);
    
	    //옵션에 따라 옵션 ID 를 다르게 처리한다.
	    var optionID = $(sender).val();
	    
	    //용지 수정 모드일 경우 값 세팅.장바구니에서 사용.(/order/cart_extra_option_update_pop_100102~100112.php)
	    if (typeof(updateObject) != "undefined") {
	    	//console.log("item_ID : "+ updateObject[$(sender).attr('id')]);
	    	optionID = updateObject[$(sender).attr('id')];
		}
		
	    var addParam = "&goodsno=" + document.fmView.goodsno.value;
    	addParam += "&option_group_type=" + group_code;
    	
    	//option_kind_code
	    var optionKindCode = $(sender).find('option:selected').attr('option_kind_code');
	    
	    if(optionKindCode != null && optionKindCode != "undefined")
	    	addParam += "&option_kind_code=" + optionKindCode;
	    
	    //console.log("optionKindCode : "+optionKindCode);
	    //console.log("option_ID : "+ optionID);
	    //console.log("option : "+ $('option:selected', sender).attr('option_kind_code'));
	    //console.log("addParam="+ addParam);
	    
		$.get("/lib/extra_option/get_extra_option_item.php?option_ID=" + encodeURI(optionID) + addParam, function(data, status){
			//console.log("data : "+ data + ", status : " + status + ", sender : " + sender);
			//console.log(data);
		    actionOnReadyStateChange(sender, data);
		});	    
    }
    else
    {
    	//console.log("forwardAction : nextSelect 없음!");
    	//console.log("forwardAction : code : " + code);
    	
		//다음 select 가 없다면 가격을 가져와서 뿌려준다.
		if (code != undefined)
		{
			priceAction(code);
		}    	
	}
  }
  
  function actionOnReadyStateChange(sender, data)
  {
    //자신 다음 select 를 찾아서 데이타를 뿌려준다. 
    var nextSelect = getNextSelect(sender);
    //console.log("sender.name:"+sender.name);
    
    if (nextSelect != null)
    {
      $(nextSelect).html(data);                    
      //자신의 하위 옵션까지 항목을 변경해야 한다.          
      //console.log(xmlhttp.responseText);
      
	    //용지 수정 모드일 경우 값 세팅.장바구니에서 사용.(/order/cart_extra_option_update_pop_100102~100112.php)
		if (typeof(updateObject) != "undefined") {
			//값 설정.
			$("#"+nextSelect.name).val(updateObject[nextSelect.name]);
		}      
      
      forwardAction(nextSelect);
    }
  }

  function priceAction(code) {
	//console.log("priceAction().code : "+ code);
	var group_code = "";
	
	var div = $('#option_item_select_'+ code);
	//console.log(div.html());
	var selectOption = $(div).find("select[code='"+code+"']");

	if(selectOption.length > 0) {
		group_code = $(selectOption[0]).attr("option_group_type");
	}
	if(group_code == "") return false;

	//console.log("code="+ code +", group_code="+ group_code);
	//console.log(div.html()); 
	//console.log(selectOption);

    var goodskind = $("input[name=goodskind]").val();
    var preset = $("input[name=preset]").val();   
	//console.log(preset);
	
    //매수와 건수를 낱장 상품과 책자 상품에 따라 처리한다.
    var order_cnt = $("#order_cnt_select").val();
    var order_cnt_page = $("#unit_order_cnt").val();

	if (group_code == "C-FIXOPTION") gcode = "C-OCNT";
	else if (group_code == "C-SELOPTION") gcode = "C-OCNT";
	else if (group_code == "FIXOPTION") gcode = "OCNT";
	else if (group_code == "SELOPTION") gcode = "OCNT";				
	else if (group_code == "M-FIXOPTION") gcode = "M-OCNT";
	else if (group_code == "M-SELOPTION") gcode = "M-OCNT";
	else if (group_code == "G-FIXOPTION") gcode = "G-OCNT";
	else if (group_code == "G-SELOPTION") gcode = "G-OCNT";

	if(preset == "100112") {
		//표지 또는 후가공이 아닌 경우 각 그룹 수량을 사용한다.
		if (group_code == "C-FIXOPTION" || group_code == "AFTEROPTION") {
			order_cnt = $("#unit_order_cnt").val();
		}
		else {
			if ($("select[id='order_cnt_select_" + gcode + "']").is("select")) {
				order_cnt = $("select[id='order_cnt_select_" + gcode + "']").val();
			}		
		}
	}
	else {
		if(group_code == "AFTEROPTION") {
			if(preset == "100104" || preset == "100108") {
				//후가공옵션,책자 프리셋2이면 내지 페이지를 기준으로 사용한다.
				//order_cnt = $("select[id='order_cnt_select_PP']").val();
				if($("select[id='order_cnt_select_OCNT']").is("select")) {
					order_cnt = Number($("select[id='order_cnt_select_OCNT']").val()); //order_cnt_select_OCNT
				}
				//console.log("order_cnt : " + order_cnt);
				
				//#책자프리셋 후가공옵션 계산 방식 수정 2015.06.10 by kdk
				//2.추가된 모든 내지 수량을 합산한 총 페이지수에 따른 후가공 가격테이블을 계산합니다.
				$("select[id^='order_cnt_select_OCNT-']").each(function(){
					order_cnt += Number($(this).val());
				});
				//console.log("후가공 옵션 : group_code : "+ group_code + ", order_cnt : " + order_cnt);
				//#책자프리셋 후가공옵션 계산 방식 수정 2015.06.10 by kdk
			}
		}
		else {
			if($("select[id='order_cnt_select_"+gcode+"']").is("select")) {
				order_cnt = $("select[id='order_cnt_select_"+gcode+"']").val();
			}
		}		
	}

	//console.log("order_cnt : " + order_cnt);

	//추가내지
	if (code.indexOf('PP-') > -1 || code.indexOf('OP-') > -1) {
		//gcode = gcode.replace("OP","PP");
		div = $(div).parent();

		var addCounts = code.split("-");
		var addCount = addCounts[1];
		//code.replace("PP-","");

		if ($(div).find("select[id^='order_cnt_select_OCNT-" + addCount + "']").is("select")) {
			order_cnt = $(div).find("select[id^='order_cnt_select_OCNT-" + addCount + "']").val();
			//order_cnt_select_OCNT-1
			//console.log("추가내지 order_cnt : " + order_cnt);
		}
	}

	//2.추가된 모든 내지 수량을 합산한 총 페이지수에 따른 후가공 가격테이블을 계산합니다.
	//$("select[id^='order_cnt_select_OCNT-']").each(function() {
		//order_cnt += Number($(this).val());
	//});

	var optionItem = "";
	var selectParam = "";
    for (var i = 0; i < selectOption.length; i++) {
    	//console.log(selectOption);
    	//console.log($(selectOption[i]).attr("option_group_type"));
    	if($(selectOption[i]).attr("option_group_type") != "DOCUMENT" ) {   	
    	   	optionItem += $(selectOption[i]).val() + "|";
    	   	//console.log($(selectOption[i]).val());
    	}
    	
   	   	selectParam += "&" + $(selectOption[i]).attr("name") + "=" + encodeURI($(selectOption[i]).val());
   	   	
   	   	//같은 가격 설정을 찾기 위해 code,option_group_type을 넘긴다.
   	   	//selectParam += "&code_type_" + $(selectOption[i]).attr("name").replace('item_select_','') + "_" + $(selectOption[i]).attr("code") + "_" + $(selectOption[i]).attr("option_group_type") + "=";

   	   	var addCodes = $(selectOption[i]).attr("code").split("-");
   	   	//console.log(addCodes);
		addCode = addCodes[0];   	   	
		//console.log(addCode);
		selectParam += "&code_type_" + addCode + "_" + addCode + "_" + $(selectOption[i]).attr("option_group_type") + "=";
    }	

	//alert(selectParam);
	//console.log(selectParam);

    var addParam = "&print_x=" + getPrintSize(code, "x") + "&print_y=" + getPrintSize(code, "y");
    addParam += "&document_x=" + getDocumentSize("x") + "&document_y=" + getDocumentSize("y");
    addParam += "&preset=" + preset;
   	addParam += "&option_group_type=" + group_code;
	//console.log("addParam : " + addParam);

	//후가공 옵션 
	if(group_code == "AFTEROPTION") addParam += "&option_kind_code=" + code;

	//규격 추가
	if($("select[option_group_type='DOCUMENT']").is("select")) addParam += "&document=" + encodeURI($("select[option_group_type='DOCUMENT']").val());
	
	//optionItem = $("select[option_group_type='DOCUMENT']").val() + "|" + optionItem;
	//console.log("optionItem : " + optionItem);

	var param = "goodsno=" + document.fmView.goodsno.value + "&order_cnt=" + order_cnt + "&order_cnt_page=" + order_cnt_page + addParam + selectParam;
    //console.log("param : " + param);        
    
	$.get("/lib/extra_option/get_extra_option_item.php?"+param, function(data, status){
		//console.log("data : "+ data + ", status : " + status + "code : "+ code + ", group_code : " + group_code);
		//console.log("parseInt(data) : " + parseInt(data));
	    if (parseInt(data) > -1)
	    {
	    	//원가|판매가 형태로 넘어온다.			20140328
	    	var price_arr = data.split("|");
	    	
	    	if (price_arr)
	    	{
	          	document.getElementById("price_text_" + code).innerHTML = commify(price_arr[1]) + '<?echo _("원")?>';	          
	          	document.getElementById("option_supply_price_" + code).value = price_arr[0];
	          	document.getElementById("option_price_" + code).value = price_arr[1];
	         
	         	//console.log("data[1] : "+ document.getElementById("option_price_" + code).value + ", data[0] : " + document.getElementById("option_supply_price_" + code).value);
	         
	          	//가격 합치기.
	          	priceSum();
	       	}
	    }		
	});    
    	
  }

  function priceSum()
  {
    var price_sum = 0;
    var price_sum_c = 0;
    
    //추가내지가 있을 경우 값 초기화
    $("#price_text_PPP").html("0" + '<?echo _("원")?>');
    $("#option_supply_price_PPP").val("0");
    $("#option_price_PPP").val("0");    
    $("#price_text_OPP").html("0" + '<?echo _("원")?>');
    $("#option_supply_price_OPP").val("0");
    $("#option_price_OPP").val("0");    
    //console.log("$('#preset').val() : "+ $('#preset').val());
    var obj = document.getElementsByTagName("input");
    for (var i = 0; i < obj.length; i++) {
      if (obj[i].type == "text" || obj[i].type == "hidden") {
        if (obj[i].name.indexOf('option_price_') > -1) {
          //alert(price_sum);          
          //alert(obj[i].value);
          var option_code = obj[i].name.replace("option_price_","");          
          var chkBox = document.getElementById("chk_item_select_" + option_code + "_0");		//chk_item_select_DM_0
          
          if (chkBox == null || chkBox.checked)
          {
	          if (obj[i].value) {
	          	//console.log(obj[i].name +" : "+ obj[i].value);
	            price_sum += parseInt(obj[i].value);
	            
	            if ($('#preset').val() == "100112") {
	            	//책자 표지옵션 + 표지후가공
					if (obj[i].name == "option_price_C-PP" || obj[i].name.indexOf('option_price_OP') > -1)
						price_sum_c += parseInt(obj[i].value);
	            }
	            else {
	            	//책자 표지옵션
	            	if(obj[i].name == "option_price_C-PP" || obj[i].name == "option_price_C-OP")
	              		price_sum_c += parseInt(obj[i].value);
	            }  
	            //console.log("price_sum : "+ price_sum);	            
	            //console.log("price_sum_c : "+ price_sum_c);
	          }  
	      }
        }
      }
    }

    //명 선택 상자가 있을 경우
    unitOrderCnt =  $('#unit_order_cnt').val();

	//console.log("price_sum : "+ price_sum);	            
	//console.log("price_sum_c : "+ price_sum_c);

	if ($('#preset').val() == "100112") {
		//책자 표지옵션 + 표지후가공 금액 뿌려주기.
		document.getElementById("price_text_C-PP").innerHTML = commify(price_sum_c) + '<?echo _("원")?>';
   	}
   
    //#책자프리셋 표지옵션 계산 방식 수정 2015.06.15 by kdk
    //#표지옵션은 수량에 곱하지 않고 합하여 계산.
    if ($('#preset').val() == "100104" || $('#preset').val() == "100108") {
		price_sum = price_sum - price_sum_c;
		
    	if (unitOrderCnt)
    		price_sum = price_sum * unitOrderCnt;
    		
		price_sum = price_sum + price_sum_c;    				
    }
    else {
    	if (unitOrderCnt)
    		price_sum = price_sum * unitOrderCnt;
	}

    //if (unitOrderCnt)
    	//price_sum = price_sum * unitOrderCnt;

	//console.log("price_sum : "+price_sum);

    //할인율 계산.
    unitPrice = getUnitPrice();
    //console.log("unitPrice : "+unitPrice);
    if (unitPrice > 0) {   	
    	//price_unit = Math.round(price_sum * (100-unitPrice) / 100); //소수점 반올림
    	//price_unit = Math.floor(price_sum * (100-unitPrice) / 100); //소수점 절사
    	price_unit = Math.floor(price_sum * unitPrice);    	
    	//console.log("price_unit : "+price_unit);
    	
    	//price_u = (price_sum * unitPrice) / 100;
    	price_u = Math.floor(price_sum - price_unit);    	
    	//console.log("price_u : "+price_u);
    	
    	var price_tag2 = '<?echo _("공급가")?>' + " : " + commify(price_u) + '<?echo _("원")?>' + " = (" + '<?echo _("원가")?>' + " : " + commify(price_sum) + '<?echo _("원")?>' + " - " + '<?echo _("할인")?>' + " : " + commify(price_unit) + '<?echo _("원")?>' + ")";
    	
    	//console.log("공급가:" + price_sum + ",할인율:" + unitPrice + ",할인가격 : "+ price_unit + ",할인가 : "+ price_u);
    	//price_sum = price_unit;
    	price_sum = price_u;
    	//console.log("price_sum : "+price_sum);    	
    }    
    
    //부가세(별도) 계산.
    //alert($('#extra_price_vat_flag').val());
    //alert(price_sum);    
    //console.log("extra_price_vat_flag : "+$('#extra_price_vat_flag').val());
    //console.log("price_sum : "+price_sum);

    var price_tax = Math.floor(price_sum * 0.1);
    //console.log("price_tax : "+price_tax);

	if($('#extra_price_vat_flag').val() == "1") { //부가세(포함) 계산.
		//console.log("#price_sum : "+price_sum);
		
		//price_sum = Math.floor(price_sum / 1.1); //소수점 버림, 정수형 반환
		//price_sum = Math.round(price_sum / 1.1); //소수점 반올림, 정수형 반환
		//console.log("#price_sum : "+price_sum);
		
		//price_tax = Math.ceil(price_sum * 0.1); //소수점 올림, 정수형 반환
		price_tax = Math.round(price_sum * 0.1); //소수점 반올림, 정수형 반환
		price_sum = Math.floor(price_sum - price_tax);
		
		//console.log("#price_sum : "+price_sum);				
		//console.log("#price_tax : "+price_tax);
	}
    
    //주문 금액 뿌려주기
    //var price_tag = "결제금액  : " + (price_sum + price_tax)+ " 원 (공급가 : )" + price_sum + "원 + 부가세 : " + price_tax + "원)";
    var price_tag = "(" + '<?echo _("공급가")?>' + " : " + commify(price_sum) + '<?echo _("원")?>' + " + " + '<?echo _("부가세")?>' + " : " + commify(price_tax) + '<?echo _("원")?>' + ")";
    
    if (unitPrice > 0) {
    	price_tag += "&nbsp;&nbsp;<br/>"+price_tag2;
    }
    
    document.getElementById("select_option_sum").innerHTML = '<?echo _("결제금액")?>' + " : " + commify(price_sum + price_tax) + " " + '<?echo _("원")?>';
    document.getElementById("select_option_sum_desc").innerHTML = price_tag;
    
    $('#select_option_supply_price').val(commify(price_sum));
    $('#select_option_sum_price').val(commify(price_sum + price_tax));
  }
  
  // 수량(건수) 할인율 가져오기
  // Ajax 비동기 방식으로 처리시 data 누락으로 동기 방식으로 처리 / 17.03.15 / kdk 
  function getUnitPrice()
  {
  	var returnPrice = 0;
  	unitOrderCnt =  $('#unit_order_cnt').val();  	
  	
    var addParam = "goodsno=" + document.fmView.goodsno.value +"&unit_cnt=" + unitOrderCnt;
    //console.log("addParam : " + addParam);
	
	/*$.get("/lib/extra_option/get_extra_option_unit_price.php?"+addParam, function(data, status){
		//console.log("data : "+ data + ", status : " + status);
		//console.log("parseInt(data) : " + parseInt(data));
	    if (parseInt(data) > -1)
	    {
	    	//원가|판매가 형태로 넘어온다.			20140328
	    	var price_arr = data.split("|");
	    	
	    	if (price_arr)
	    	{
	          	returnPrice = price_arr[1];
	       	}
	    }
	});*/
	
  	$.ajax({
    	type: 'POST',
       	url: '/lib/extra_option/get_extra_option_unit_price.php',
       	data: addParam,
       	async: false, // 동기 방식으로 처리
       	success: function(data, status) {
			//console.log("data : "+ data + ", status : " + status);
			//console.log("parseInt(data) : " + parseInt(data));
		    if (parseInt(data) > -1)
		    {
		    	//원가|판매가 형태로 넘어온다.			20140328
		    	var price_arr = data.split("|");
		    		    	
		    	if (price_arr)
		    	{
		    		//console.log("data[0] : "+ price_arr[0] + ", data[1] : " + price_arr[1]);		    		
		          	returnPrice = price_arr[1];
		       	}
		    }            
       	}
  	});	
	
	//console.log("returnPrice : " + returnPrice);
	return returnPrice;
  }  
  
  function getPrintSize(gcode, sizeCode)
  {
    var returnSize = 0;
    if (document.getElementById("print_" + sizeCode + "_" + gcode))
      returnSize = document.getElementById("print_" + sizeCode + "_" + gcode).value;    
    return returnSize;
    //var addParam = "&print_x=" + print_x + "&print_y=" + print_y;    
  }
  
  function getDocumentSize(sizeCode)
  {
    var returnSize = 0;
    if (document.getElementById("document_" + sizeCode))
      returnSize = document.getElementById("document_" + sizeCode).value;    
    return returnSize;        
  }
  
  function initPrice()
  {
  	//console.log("initPrice:시작!");
    var obj = document.getElementsByTagName("input");
    for (var i = 0; i < obj.length; i++) {
      if (obj[i].type == "text" || obj[i].type == "hidden") {
        if (obj[i].name.indexOf('option_price_') > -1) {
          obj[i].value = '';
        }
      }
    }
    var price_tag = document.getElementById("select_option_sum");
    if (price_tag)
    	price_tag.innerHTML = "0 " + '<?echo _("원")?>';
    	
    var price_tag_desc = document.getElementById("select_option_sum_desc");
    if (price_tag_desc)
    	price_tag_desc.innerHTML = "";	
  }
  
  function getNextSelect(sender)
  {
  	//alert(sender.name);
    var selectOption = null;
    var gcode = $(sender).attr('code');        
    var selectID = sender.id.replace("item_select_", "");
    selectID = selectID.replace(gcode + "_", "");
    var selectNextID = parseInt(selectID) + 1;
    var subSelectName = 'item_select_'+ gcode + '_' + selectNextID;
    
    if ($('#'+ subSelectName).is("select"))
    {
      selectOption =  document.getElementById(subSelectName);
      //alert(subSelectName);
    }
      
    return selectOption;    
  }
  
  function getLastSelect(option_code)
  {
    //마지막 select 를 찾는다
    var selectID = -1;
    var selectOption = null;
    
    for(i=0; i < 9; i++)
    {  
      var subSelectName = 'item_select_'+ option_code + '_' + i;        
      var selectOptionTemp =  document.getElementById(subSelectName);          
      if (selectOptionTemp != undefined && $('#'+ subSelectName).is("select"))
      { 
        //같은 그룹 코드일 경우만
        var next_gcode = $(selectOptionTemp).attr('code');
        if (option_code == next_gcode)
          selectID = i;
      }
    }
    
    if (selectID > -1)
      selectOption =  document.getElementById('item_select_'+ option_code + '_' + selectID);
    return selectOption;
  }
  
  //표지,내지,면지,간지 규격 통일시킨다.
  function setDucumentSizeBook(sender) {
  	//alert(sender.options[sender.selectedIndex].text);
  	
	$("[id='document_x']").attr("readonly",false);
	$("[id='document_y']").attr("readonly",false);

	for(key in document_size) {				
		if (key == sender.options[sender.selectedIndex].text) {				
			$("[id='document_x']").val(document_size[key].substr(0, document_size[key].indexOf('x')));
			$("[id='document_y']").val(document_size[key].substr(document_size[key].indexOf('x')+1, document_size[key].length));
			
			if ($("[id='document_x']").val())
				$("[id='document_x']").attr("readonly",true);
			
			if ($("[id='document_y']").val())
				$("[id='document_y']").attr("readonly",true);
		}
	}
	
	$("[group_type='DOCUMENT']").val(sender.options[sender.selectedIndex].value).attr("selected","selected");
  }
    
  //내지, 표지 규격을 통일시킨다.
  function setDocumentSize(sender)
  {
    var senderText = sender.options[sender.selectedIndex].text;
    //alert(senderText);
    
    for (var i = 0; i < optino_group_code.length; i++) {
      if (optino_group_code[i])
      {
        //표지, 내지 옵션중에서만 처리한다.               
        if ((optino_group_code[i].indexOf('C-') > -1) || (optino_group_code[i].indexOf('P-') > -1))
        {           
          //9차 옵션까지 있을수 있으므로 해당 옵션 코드표를 전체 검색
          for (var k = 0; k < 9; k++)
          {
            var searchSelectName = "item_select_" + optino_group_code[i] + "_" + k;
            
            if ($('#'+ searchSelectName).is("select"))
            {                          
              var targetSelectedText = $("#" + searchSelectName + ":selected").text();
              var selectCnt = $("#" + searchSelectName + " option").length;              

              //원본과 대상이 다를경우.
              if (targetSelectedText !=  senderText)
              {
                for (var j = 0; j < selectCnt; j++)
                {
                  //alert($("#" + searchSelectName + ":eq(0)").text())
                  if (senderText == $("#" + searchSelectName + " option:eq(" + j + ")").text())
                  {
                    $("#" + searchSelectName + " option:eq(" + j + ")").attr("selected", "selected");
                                        
                    if (optino_group_code[i].indexOf('C-') > -1)
                      forwardAction(document.getElementById(searchSelectName));
                    else if (optino_group_code[i].indexOf('P-') > -1)
                      forwardActionPage(document.getElementById(searchSelectName));
                    //var selectOption = getLastSelect(optino_group_code[i]);
                    //forwardActionPage(selectOption);              
                  } 
                }
              }
            }
          }
        }
      }
    }
  }

function initUploadDiv(podskind)
{			
	//==================================
	if(podskind == '' || podskind == '0'){
		//수동업로드
		$('#inputfile_ul').children().hide();
		$('#inputfile_ul li:first-child').show();
		$('#fileupload_panel').show();
		$('#btn_editopen_update').hide();	
		
		$('.btn_inputfile_add').bind("click",function(){
			var lis=$('#inputfile_ul').children(":hidden").length;
			if (lis > 0) {
				var flg = true;
				$('#inputfile_ul').children(":hidden").each(function(){
					if ($(this).is(":hidden") && flg) {
						$(this).children('input').attr('disabled',false);
						$(this).show();
						flg = false;
						return;
					}
				});
			}
			else {alert('<?echo _("최대 10개까지 가능합니다.")?>');}
		});	
		$('.btn_inputfile_del').bind("click",function(){
			var li = $(this).parent();
			$(li).children('input').val('');
			$(li).children('input').attr('disabled',true);
			$(li).hide();
		});
	}
	else $('#file_upload_holder').hide();
}
	
//지류 면지,간지 옵션 div 보이기 / 감추기
function onVisiblePageOption(optionName)
{
	//alert($('#preset').val());
	if ($('#preset').val() == "100108") { //책자 프리셋2 용지,옵션
		var optionName2 = optionName.replace("PP","OP");
		if($("#chk_item_select_" + optionName + "_0").is(":checked")) {
			$("#chk_item_select_" + optionName2 + "_0").attr("checked", true);	
		}
		else {
			$("#chk_item_select_" + optionName2 + "_0").attr("checked", false);
		}		
	}
	
	var div_name = "page_option_" + optionName;
	var div_pannel =  document.getElementById(div_name);
	if (div_pannel)
	{
		if ( div_pannel.style.display != 'none' ) { // 보여지는 상태이면
  			div_pannel.style.display = 'none';   // seogd를 숨기고
		} 
		else 
		{
			div_pannel.style.display = ''; // seogd이 숨김 상태이면 나타내라
		}
		
		priceSum();
	}
}

//후가공 옵션 div 보이기 / 감추기
function onVisibleAfterOption(optionName)
{
	if ($('#preset').val() == "100112") {
		var div = $("#after_option");
		if (div) {
			if($("#chk_after").is(":checked")) {
				$(div).show();
				$(div).find('input').attr('checked', true);
			}
			else {
				$(div).hide();
				$(div).find('input').attr('checked', false);
			}	
			
			priceSum();
		}
	}
	else {
		var div_name = "after_option_" + optionName;
		var div_pannel =  document.getElementById(div_name);
		if (div_pannel)
		{
			if ( div_pannel.style.display != 'none' ) { // 보여지는 상태이면
	  			div_pannel.style.display = 'none';   // seogd를 숨기고
			} 
			else 
			{
				div_pannel.style.display = ''; // seogd이 숨김 상태이면 나타내라
			}
			
			priceSum();
		}
	}	
}

//후가공 옵션 checkbok 선택시 가격계산
function onClickChkAfterOption() {
	priceSum();
}
	
//, 넣기.
function commify(n) {
var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
n += '';                          // 숫자를 문자열로 변환

while (reg.test(n))
	n = n.replace(reg, '$1' + ',' + '$2');

return n;
}


//수량 select box max min
function getRangeSelectCnt(sender) {
	//console.log(sender);
	var cnt = $(sender).val();
    var selectOption = null;
    var selectID = sender.id.replace("input_", "");
    
    if ($('#'+ selectID).is("select"))
    {
      $('#'+ selectID).val(cnt);
      selectOption =  document.getElementById(selectID);
      //console.log($('#'+ selectID).val());
    }	
	
	var options = selectOption.childNodes;
	var min = 999999999;
	var max = 0;
	for(var i = 0; i < options.length; i++) {
		if(Number(options[i].value) > max) max = Number(options[i].value);
	    if(Number(options[i].value) < min) min = Number(options[i].value);
	}
	//console.log('min:'+min+',max:'+max+',cnt:'+cnt);
	
	if(cnt >= min && cnt <= max) {
		if(selectID == "unit_order_cnt") {
  			priceSum();			
		} 
		else {		
			//마지막 항목 가격을 기본적으로 뿌려준다.
  			forwardOrderCntAction();
  		}
	}
	else {
		alert("[" + '<?echo _("최소")?>' + ":"+min+", " + '<?echo _("최대")?>' + ":"+max+"] " + '<?echo _("사이에 수량을 입력하시기 바랍니다.")?>');
		//if(cnt < min) $(sender).val(min);
		//else if(cnt > max) $(sender).val(max);
		$(sender).val(1);
	}
}

//면적 document size input box max min
function getRangeInputCnt(sender) {
	//console.log(sender);

	var cnt = $(sender).val();
	var min = 1;
	var max = 99999;
	//console.log('min:'+min+',max:'+max+',cnt:'+cnt);
	
	if(cnt >= min && cnt <= max) {
		//마지막 항목 가격을 기본적으로 뿌려준다.
		//forwardOrderCntAction();
		if($('#document_x').val() != "" && $('#document_y').val() != "") {
			forwardOrderCntAction();
		}
	}
	else {
		alert("[" + '<?echo _("최소")?>' + ":"+min+", " + '<?echo _("최대")?>' + ":"+max+"] " + '<?echo _("사이에 수량을 입력하시기 바랍니다.")?>');
		$(sender).val(1);
	}
}


//내지(+추가내지)/간지/면지의 전체 페이지 합이 정해진 제본방식의 최소,최대 페이지 수에 초과 하지 않도록 처리.
function getRangeSelectCntTotal(sender) {
	//console.log("getRangeInputCntTotal:");
	//console.log(sender);

	var min = 1;
	var max = 99999;
	var page = 0;
	
	$("input[id^='input_order_cnt_select_']").each(function(){
		if($(this).attr("id") != "input_order_cnt_select_ACNT") {
			page += parseInt($(this).val());	
		}
	});	
	
	//id="item_select_JB_2"
	//console.log($("#item_select_JB_2").val());
	//console.log(page_size);
	for (key in page_size) {
		if (key == $("#item_select_JB_2").val()) {
			min = page_size[key].substr(0, page_size[key].indexOf('x'));
			max = page_size[key].substr(page_size[key].indexOf('x') + 1, page_size[key].length);
		}
	}

	//console.log('min:'+min+',max:'+max+',page:'+page);

	if (page >= min && page <= max) {
		//최소,최대 페이지 범위안에 있다.
	} else {
		alert('<?echo _("내지(+추가내지)/간지/면지의 전체 페이지 합이")?>' + " [" + '<?echo _("최소")?>' + ":" + min + ", " + '<?echo _("최대")?>' + ":" + max + "] " + '<?echo _("사이에 수량을 입력하시기 바랍니다.")?>');
		$(sender).val(1);
		
		//숨겨져있는 select box 값도 변경한다.order_cnt_select_OCNT
		var selectID = $(sender).attr("id").replace("input_", "");
		if ($('#' + selectID).is("select")) {
			$('#' + selectID).val(1);
		}		
		
		var id = $(sender).parents("div").attr("id");
		if (typeof(id)!="undefined" || id!=null) {
			var code = id.replace("option_item_select_","");
			priceAction(code);
		}
	}	
}

//숫자만 입력받기.
function number_filter(str_value){
	return str_value.replace(/[^0-9]/gi, ""); 
}

//견적상품(spring) 수동 파일업로드 레이어 오픈
function initUploadDiv2()
{			
	//==================================
	//수동업로드
	$('#inputfile_ul2').children().hide();
	$('#inputfile_ul2 li:first-child').show();
	
	$('.btn_inputfile_add2').bind("click",function(){
		var lis=$('#inputfile_ul2').children(":hidden").length;
		if (lis > 0) {
			var flg = true;
			$('#inputfile_ul2').children(":hidden").each(function(){
				if ($(this).is(":hidden") && flg) {
					$(this).children('input').attr('disabled',false);
					$(this).show();
					flg = false;
					return;
				}
			});
		}
		else {alert('<?echo _("최대 10개까지 가능합니다.")?>');}
	});	
	$('.btn_inputfile_del2').bind("click",function(){
		var li = $(this).parent();
		$(li).children('input').val('');
		$(li).children('input').attr('disabled',true);
		$(li).hide();
	});
}
