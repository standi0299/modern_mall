
/*
 * 자동 견적 낱장 기본 프리셋 100114/ 인터프로 전용 view_interpro.htm
 * */

//### step 1 jquery-file-upload 업로더 처리
function initJQOrder(orderType, init_mode, storageKey) 
{
	//장바구니 이동	
	var orderfrm = $('#frmView');
	$(orderfrm).attr("action",'/order/cart.php');
	
	$(orderfrm).find('input[name="mode"]').val('cart');		
	$(orderfrm).find('input[name="storageid"]').val(storageKey);
	$(orderfrm).find('input[name="est_order_type"]').val(orderType);

	if ($(orderfrm).find('input[name="goods_group_code"]').val() == "30")
	{
		$(orderfrm).find('input[name="option_json"]').val(makeOptionJson());
		//템플릿셋ID 템플릿ID를 저장한다.
		var orderJson = JSON.parse($(orderfrm).find('input[name="option_json"]').val());
		//alert(orderJson);	
			
	}
	else {
		//템플릿셋ID 템플릿ID를 저장한다.
		var orderJson = {};
	}

	orderJson["templateSetIdx"] = $("input[name=templateSetIdx]").val();
	orderJson["templateIdx"] = $("input[name=templateIdx]").val();
	orderJson["catno"] = $("input[name='catno']").val();	
	
    $(orderfrm).find('input[name="option_json"]').val(JSON.stringify(orderJson));

	//$(orderfrm).find('input[name="est_order_type"]').val(order_type);
	
	if($('#extra_auto_pay_flag').val() == "0") //견적의뢰
		$(orderfrm).find('input[name="mode"]').val('cart_extra');
		
	$(orderfrm).submit();		
}

//, 넣기.
function commify(n) {
	var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
	n += '';                          // 숫자를 문자열로 변환
	
	while (reg.test(n))
		n = n.replace(reg, '$1' + ',' + '$2');
	
	return n;
}

function orderCntChange() {
	forwardAction();
}

function forwardAction() {
	//console.log("forwardAction:시작!");
	var selectParam = "";
	var optionItem = [];

	//select.
	$j("select[name*='item_select_']").each(function(index){
		if ($j(this).val() && $j(this).val()!=""){
			//param
			selectParam += "&" + $j(this).attr("name") + "=" + encodeURI($j(this).children("option:selected").text());
			//option_item + "|".
			optionItem[index] = $j(this).children("option:selected").text();
		}
	});

	var order_cnt = $j("select[name='order_cnt_select']").val();
	var unit_order_cnt = $j("select[name='unit_order_cnt']").val();

	//price.
    var addParam = "&print_x=&print_y=&document_x=&document_y=";
    addParam += "&preset=" + $j("#preset").val();
   	addParam += "&option_group_type=FIXOPTION";

	var param = "goodsno=" + $j("#goodsno").val() + "&order_cnt=" + order_cnt + "&order_cnt_page=" + unit_order_cnt + addParam + selectParam;

	$j.get("/lib/extra_option/get_extra_option_item.php?"+param, function(data, status){
	    if (parseInt(data) > -1)
	    {
	    	//원가|판매가 형태로 넘어온다.
	    	var price_arr = data.split("|");
	    	
	    	if (price_arr)
	    	{
	          	//document.getElementById("price_text").innerHTML = commify(price_arr[1]) + "원";
	          	$j('#price_text').html(commify(price_arr[1]) + '<?echo _("원")?>');
			    $j('#select_option_supply_price').val(price_arr[1]);
			    $j('#select_option_sum_price').val(price_arr[1]);
	       	}
	    }
	});
	
	//option image.
	if ($j("#imageToSwap").length) {	
		var option_item = optionItem.join("|");
		var optionImages = JSON.parse(opt_img);
		//console.log(optionImages);
		
		//선택한 옵션과 등록된 옵션 이미지를 검색하여 해당 이미지(src)를 변경한다.
		if(option_item != "") {
			if(optionImages.length > 0) {
			    for (i = 0; i < optionImages.length; i++) {
			    	if(option_item == optionImages[i].option_item) {
			    		//주문제한.
			    		$j("#limit_flag").val(optionImages[i].limit_flag);
			    		
		            	var img = "http://"+center_host+"/data/goods/"+cid+"/l/"+$j("#goodsno").val()+"/"+optionImages[i].option_img;
						$j("#imageToSwap").attr("src",img);
		            }
			    }
			}
		}
	}
}

//전체 주문 json 만들기.
function makeOptionJson()
{
	//console.log("makeOptionJson:");
	var orderJson = {};
	var orderOption = {};

	orderJson["est_order_option_desc"] = "";

	//select.
	$j("select[name*='item_select_']").each(function(index){
		if ($j(this).val() && $j(this).val()!=""){
			//orderOption
			orderOption[$j(this).attr("name")] = $j(this).children("option:selected").text();

	    	var name_arr = $j(this).attr("name").split("_");
			orderJson["est_order_option_desc"] += display_name[name_arr[3]] + ":" + $j(this).children("option:selected").text() + "<br>";
		}
	});

	//페이지 수량
	orderJson["est_order_option_desc"] += cnt_display_name + ":" + $j("select[name='order_cnt_select']").val() + "<br>";
	
	//수량
   	var label = $j("span[name='unit_order_cnt']").text();
   	if(label == "") label = '<?echo _("건(부)")?>';
   	
	orderJson["est_order_option_desc"] += '<?echo _("수량")?>' + "::"+ $j("select[name='unit_order_cnt']").val() + " " + label + "<br>";
	
	//order_cnt_select.
	orderOption["order_cnt_select"] = $j("select[name='order_cnt_select']").val();
	orderOption["unit_order_cnt"] = $j("select[name='unit_order_cnt']").val();

	orderJson["est_order_cnt"] = $j("select[name='unit_order_cnt']").val();
	orderJson["est_supply_price"] = $j("#select_option_supply_price").val();
	orderJson["est_price"] = $j("#select_option_sum_price").val();
	orderJson["preset"] = $j("#preset").val();

	//catno 추가
	orderJson["catno"] = $j("#catno").val();
	
	orderJson["est_order_option"] = JSON.stringify(orderOption);
	//console.log(JSON.stringify(orderJson));
    //return Base64.encode(JSON.stringify(orderJson));
    return JSON.stringify(orderJson);
}

function procUpdateSubmit(){	
	//mode, goodsno, storageid //데이타 설정
	//장바구니 이동	
	var orderfrm = $('#fmView');
	$(orderfrm).attr("action",'/order/cart.php?mode=extra_option_update');
	$(orderfrm).find('input[name="option_json"]').val(makeOptionJson());
	//$(orderfrm).find('input[name="est_order_type"]').val(order_type);	
	$(orderfrm).submit();
}

//수동 파일업로드 레이어 오픈
function fileInfoOpenLayer(obj) {
	//view_interpro.htm 레이어 닫기.
	$('.dim_layer').fadeOut();
	
	popupLayerFileUpload("/fileupload/upload_popup.php");
}
