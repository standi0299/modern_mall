//피오디그룹 정액회원 결제창에서 사용

function couponResult(state,msg) {
    try {
		$j("#coupon_state").val(state);
		$j("#msg1").show();
		$j("#msg").html(msg);
    } catch (ex) {
        alert(ex.name + ';' + ex.messge);
    }
}

function couponSearch() {
	$j("#msg1").hide();
	$j('input:radio[name=p_kind]:input[value=coupon]').attr("checked", true);

	if($j("#coupon_code").val() == "") {
		alert('<?echo _("쿠폰번호를 입력 후 적용 버튼을 클릭하세요.")?>');
		$j("#coupon_code").focus();
	}
	else {
		$j("#coupon_state").val("search");
		$j("#form1").submit();
	}
}

function payment() {
	var kind = $j(":input:radio[name=p_kind]:checked").val();
	
	if(kind == "") {
		alert('<?echo _("결제 유형을 선택하세요.")?>');
	}
	else if(kind == "account") {
		if($j("#optionValue").val() == "") {
			alert('<?echo _("이용 기간을 선택하세요.")?>');
			$j("#p_date").focus();
		}
		else {
			$j("#form1").submit();
		}
	}
	else if(kind == "coupon") {
		if($j("#coupon_code").val() == "") {
			alert('<?echo _("쿠폰번호를 입력 후 적용 버튼을 클릭하세요.")?>');
			$j("#coupon_code").focus();
			return false;
		}
		
		if($j("#coupon_state").val() == "OK") {
			$j("#form1").submit();
		}
		else {
			alert('<?echo _("쿠폰번호를 확인해주세요.")?>');
			$j("#coupon_code").focus();
			return false;
		}
	}
}

function getSelectValue() {
	$j('input:radio[name=p_kind]:input[value=account]').attr("checked", true);
	
	if($j("#p_date option:selected").val() != "") {
		var selectData = $j("#p_date option:selected").text();
		selectData = selectData.replace($j("#p_date option:selected").val() + " ","");
		selectData = selectData.replace('<?echo _("원")?>',"");
		selectData = selectData.replace("元", "");
		selectData = selectData.replace("円", "");
		selectData = selectData.replace("(","");
		selectData = selectData.replace(")","");
		$j("#optionValue").val(selectData);
	}
	else {
		$j("#optionValue").val("");
	}
}

function serviceHelpOpen(url) {
	window.open(url);
}

function resultClose() {
	opener.location.href = '/mypage/';
	window.close();
}
