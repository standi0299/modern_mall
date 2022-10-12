			
  function forwardAction(sender) {
    //alert(sender.name);
    
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
    }
    else {// code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }    
    
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        actionOnReadyStateChange(sender, "NOR");        
      }
    };

    var gcode = $(sender).attr('code');
    
    //프리셋에 따라 code를 다르게 처리한다.
    var preSet =  $('#preset_div').attr("preset");
    if (preSet == "100112") {
    	if(gcode != "DOCUMENT") {
    		//option_kind_code
    		if (typeof($(sender).find('option:selected').attr('option_kind_code'))!="undefined" || $(sender).find('option:selected').attr('option_kind_code')!=null) {
    			gcode = $(sender).find('option:selected').attr('option_kind_code');    			
    		}
    	}
    }
        
    var gtype = $(sender).attr('option_group_type');
    //옵션에 따라 옵션 ID 를 다르게 처리한다.
    var item_value = $(sender).val();

	//alert(preSet +" , "+ gcode +" , "+ gtype);

    //규격을 변경한경우 규격 사이즈 출력.
    if (gcode == 'DOCUMENT')
    {
    	$("[id='document_x'][option_group_type='"+gtype+"']").attr("readonly",false);
    	$("[id='document_y'][option_group_type='"+gtype+"']").attr("readonly",false);
    	
		//document.getElementById("document_x").readOnly = false;
		//document.getElementById("document_y").readOnly = false;

    	for(key in document_size)
		{				
			//alert(key);
			
			if (key == sender.options[sender.selectedIndex].text)
			{				
    			$("[id='document_x'][option_group_type='"+gtype+"']").val(document_size[key].substr(0, document_size[key].indexOf('x')));
    			$("[id='document_y'][option_group_type='"+gtype+"']").val(document_size[key].substr(document_size[key].indexOf('x')+1, document_size[key].length));
				
				if ($("[id='document_x'][option_group_type='"+gtype+"']").val())
					$("[id='document_x'][option_group_type='"+gtype+"']").attr("readonly",true);
				
				if ($("[id='document_y'][option_group_type='"+gtype+"']").val())
					$("[id='document_y'][option_group_type='"+gtype+"']").attr("readonly",true);
				
				/*
				document.getElementById("document_x").value = document_size[key].substr(0, document_size[key].indexOf('x'));
	    		document.getElementById("document_y").value = document_size[key].substr(document_size[key].indexOf('x')+1, document_size[key].length);
	    			    		
	    		if (document.getElementById("document_x").value)
	    			//document.getElementById("document_x").setAttribute("disabled", 'false');	    		
	    			document.getElementById("document_x").readOnly=true;
	    		if (document.getElementById("document_y").value)
					//document.getElementById("document_y").setAttribute("disabled", 'false');
					document.getElementById("document_y").readOnly=true;
				*/	
			}
		}
    }

    var addParam = "&print_x=" + getPrintSize(gcode, "x") + "&print_y=" + getPrintSize(gcode, "y");
    addParam += "&document_x=" + getDocumentSize("x", gtype) + "&document_y=" + getDocumentSize("y", gtype);
    addParam += "&goodsno=" + document.fmView.goodsno.value;
    addParam += "&option_kind_code=" + gcode +"&option_group_type=" + gtype;
    
    //alert(addParam);
    xmlhttp.open("GET", "/lib/extra_option/get_admin_extra_option_item.php?item_value=" + encodeURI(item_value) + addParam, false);
    xmlhttp.send();
    //document.getElementById("select_course").innerHTML = "";
    
    //규격을 변경한경우 내지와 표지 규격을 통일시켜야 한다.
    //setDocumentSize(sender);
  }
  
  
  function actionOnReadyStateChange(sender, actionKind)
  {
    //alert(xmlhttp.responseText);        
    //자딘 다음 select 를 찾아서 데이타를 뿌려준다. 
    var nextSelect = getNextSelect(sender);
    if (nextSelect != null)
    {
      $(nextSelect).html(xmlhttp.responseText);                    
      //자신의 하위 옵션까지 항목을 변경해야 한다.          
      //alert(nextSelect.name);
      //alert(xmlhttp.responseText);
      
      if (actionKind == "PAGE")
        forwardActionPage(nextSelect);
      else
        forwardAction(nextSelect);
    }
    else        
    {
      //다음 select 가 없다면 가격을 가져와서 뿌려준다.
      var gcode = $(sender).attr('code'); 
      if (gcode != undefined)
      {
        if (parseInt(xmlhttp.responseText) > -1)
        {
        	//alert(gcode);
        	//원가|판매가 형태로 넘어온다.			20140328
        	var price_arr = xmlhttp.responseText.split("|");
        	
        	if (price_arr)
        	{        	
	          document.getElementById("price_text_" + gcode).innerHTML = commify(price_arr[1]) + '<?echo _("원")?>';
	          
	          document.getElementById("option_supply_price_" + gcode).value = price_arr[0];
	          document.getElementById("option_price_" + gcode).value = price_arr[1];
	          	          
	          priceSum();
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
    
	if (code == 'DOCUMENT') {
		
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

  }    
  
  function getPrintSize(gcode, sizeCode)
  {
    var returnSize = 0;
    if (document.getElementById("print_" + sizeCode + "_" + gcode))
      returnSize = document.getElementById("print_" + sizeCode + "_" + gcode).value;    
    return returnSize;
    //var addParam = "&print_x=" + print_x + "&print_y=" + print_y;    
  }
  
  
  function getDocumentSize(sizeCode, gtype)
  {
    var returnSize = 0;
    
    if ($("[id='document_"+sizeCode+"'][option_group_type='"+gtype+"']"))
    	returnSize = $("[id='document_"+sizeCode+"'][option_group_type='"+gtype+"']").val();
    
    //if (document.getElementById("document_" + sizeCode))
    //  returnSize = document.getElementById("document_" + sizeCode).value;    
      
    return returnSize;        
  }
  
  
  function initPrice()
  {
    var obj = document.getElementsByTagName("input");
    for (var i = 0; i < obj.length; i++) {
      if (obj[i].type == "text") {
        if (obj[i].name.indexOf('option_price_') > -1) {
          obj[i].value = '';
        }
      }
    }
    var price_tag = document.getElementById("select_option_sum");
    if (price_tag)
    	price_tag.innerHTML = "0 " + '<?echo _("원")?>';
  }
  
  
  function getNextSelect(sender)
  {
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
  
    
	
	//, 넣기.
	function commify(n) {
  	var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
  	n += '';                          // 숫자를 문자열로 변환

  	while (reg.test(n))
    	n = n.replace(reg, '$1' + ',' + '$2');

  	return n;
	}



