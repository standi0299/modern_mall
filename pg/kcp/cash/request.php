<?
include_once "../mobile/site_conf_inc.php";
include_once "../../../lib/library.php";

	$payno = $_GET[payno];


	if ($payno)
	{
		$m_order = new M_order();
		$oData = $m_order->getPayInfo($payno);
		
		//가상계좌와 무통장만 현금 영수증 처리
		if ($oData[paymethod] == "b" || $oData[paymethod] == "v")
		{
			$m_cash = new M_cash_receipt();	
			$cachInfo = $m_cash->getInfo($cid, $payno);
			
			//발금된 이력이 있음.
			if ($cachInfo[status] == "02")
			{
				msg("이미 발급처리되었습니다.");
			}
		} else {
			msg("무통장, 가상계좌 주문만 신청 가능합니다.");
		}
		
		$pay_price = $oData[payprice];
		$orderer_name = $oData[orderer_name];
		$orderer_email = $oData[orderer_email];
		$orderer_mobile = $oData[orderer_mobile];
		$paydt = date("YmdHis",  strtotime($oData[paydt]));	
	}

	$m_config = new M_config();
	$cData = $m_config->getConfigInfo($cid, "", "cash_supp");

	if ($cData[cash_supp_co_kind] == "0") $cash_supp_co_kind = "TG01";
	else $cash_supp_co_kind = "TG02";	
	if (!$cData[cash_supp_price_policy]) $cData[cash_supp_price_policy] = "R";			//기본값 반올림
	//$selct[tr_code][$request_kind] = "selected";			//개인, 법인
							 
	$good_name = "포토북";
	$amt_tot = $pay_price; 		//총액
	$amt_tax = getCuttingPrice($pay_price, $cData[cash_supp_price_policy], 1);		//부가기치세
	$amt_sup = $pay_price - $amt_tax;		//공급가액	(거래금액 총 합 - 봉사료 - 부가가치세)
	$amt_svc = 0;		//봉사료
                           
	if ($ici_admin) $textStyle = "";
	else $textStyle = " readonly='true' ";
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?
    /* ============================================================================== */
    /* =   PAGE : 등록 요청 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://testpay.kcp.co.kr/pgsample/FAQ/search_error.jsp       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2010.02.   KCP Inc.   All Rights Reserved.                = */
    /* ============================================================================== */
?>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>*** KCP Online Payment System [PHP Version] ***</title>
    <link href="css/sample.css" rel="stylesheet" type="text/css">
    <script type="text/javascript">

	

    // 현금영수증 MAIN FUNC
    function  jsf__pay_cash( form )
    {
        jsf__show_progress(true);

        if ( jsf__chk_cash( form ) == false )
        {
            jsf__show_progress(false);
            return;
        }

        form.submit();
    }

    // 진행 바
    function  jsf__show_progress( show )
    {
        if ( show == true )
        {
            window.show_pay_btn.style.display  = "none";
            window.show_progress.style.display = "inline";
        }
        else
        {
            window.show_pay_btn.style.display  = "inline";
            window.show_progress.style.display = "none";
        }
    }

	// 포맷 체크
    function  jsf__chk_cash( form )
    {
        if ( form.trad_time.value.length != 14 )
        {
            alert("원 거래 시각을 정확히 입력해 주시기 바랍니다.");
            form.trad_time.select();
            form.trad_time.focus();
            return false;
        }

		if ( form.corp_type.value == "1" )
        {
            if ( form.corp_tax_no.value.length != 10 )
            {
                alert("발행 사업자번호를 정확히 입력해 주시기 바랍니다.");
                form.corp_tax_no.select();
                form.corp_tax_no.focus();
                return false;
            }
        }

        if (  form.tr_code[0].checked )
        {
            if ( form.id_info.value.length != 10 &&
            	 form.id_info.value.length != 11 &&
            	 form.id_info.value.length != 13 )
            {
                alert("주민번호 또는 휴대폰번호를 정확히 입력해 주시기 바랍니다.");
                form.id_info.select();
                form.id_info.focus();
                return false;
            }
        }
        else if (  form.tr_code[1].checked )
        {
            if ( form.id_info.value.length != 10 )
            {
                alert("사업자번호를 정확히 입력해 주시기 바랍니다.");
                form.id_info.select();
                form.id_info.focus();
                return false;
            }
        }
        return true;
    }

    function  jsf__chk_tr_code( form )
    {
        var span_tr_code_0 = document.getElementById( "span_tr_code_0" );
        var span_tr_code_1 = document.getElementById( "span_tr_code_1" );

        if ( form.tr_code[0].checked )
        {
            span_tr_code_0.style.display = "block";
            span_tr_code_1.style.display = "none";
        }
        else if (form.tr_code[1].checked )
        {
            span_tr_code_0.style.display = "none";
            span_tr_code_1.style.display = "block";
        }
    }
    
    
    function auto_submit()
    {
    	document.cash_form.submit();
    }

</script>
</head>

<body onload="auto_submit();">
<form name="cash_form" action="./pp_cli_hub.php" method="post">
<input type="hidden" name="corp_type" value="0" />	
<input type="hidden" name="corp_tax_type" value="<?=$cash_supp_co_kind?>" />
	
	
	
<div align="center">

<table width="589" cellpadding="0" cellspacing="0">
            <tr style="height:14px"><td style="background-image:url('./img/boxtop589.gif');"></td></tr>
            <tr>
                <td style="background-image:url('./img/boxbg589.gif')">

                    <!-- 상단 테이블 Start -->
                    <table width="551px" align="center" cellspacing="0" cellpadding="16">
                        <tr style="height:17px">
                            <td style="background-image:url('./img/ttbg551.gif');border:0px " class="white">
                                <span class="bold big">[등록요청]</span> 이 페이지는 현금영수증 등록을 요청하는 페이지입니다.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-image:url('./img/boxbg551.gif') ;">
                                <p class="align_left">소스 수정시 가맹점의 상황에 맞게 적절히 수정 적용하시길 바랍니다.</p>
                                <p class="align_left">등록에 필요한 정보를 정확하게 입력하시어 등록을 진행하시기 바랍니다.</p>
                            </td>
                        </tr>
                        <tr style="height:11px"><td style="background:url('./img/boxbtm551.gif') no-repeat;"></td></tr>
                    </table>
                    <!-- 상단 테이블 End -->

                    <!-- 주문 정보 출력 테이블 Start -->
                    <table width="527" align="center" cellspacing="0" cellpadding="0" class="margin_top_20">
                        <tr><td colspan="2"  class="title">주 문 정 보</td></tr>
                        <!-- 주문 번호 -->
                        <tr>
                            <td class="sub_title1">주문 번호</td>
                            <td class="sub_content1"><input type="text" name="ordr_idxx" value="<?=$payno?>" size="30" maxlength="50" class="frminput" <?=$textStyle?>/></td>
                        </tr>
                        <!-- 상품명 -->
                        <tr>
                            <td class="sub_title1">상품명</td>
                            <td class="sub_content1"><input type="text" name="good_name" value="<?=$good_name?>" size="20" maxlength="100" class="frminput" <?=$textStyle?> /></td>
                        </tr>
                        <!-- 주문자 이름 -->
                        <tr>
                            <td class="sub_title1">주문자 이름</td>
                            <td class="sub_content1"><input type="text" name="buyr_name" value="<?=$orderer_name?>" size="20" maxlength="20" class="frminput" /></td>
                        </tr>
                        <!-- 주문자 E-Mail -->
                        <tr>
                            <td class="sub_title1">주문자 E-Mail</td>
                            <td class="sub_content1"><input type="text" name="buyr_mail" value="<?=$orderer_email?>" size="30" maxlength="40" class="frminput" /></td>
                        </tr>
                        <!-- 주문자 전화번호 -->
                        <tr>
                            <td class="sub_title1">주문자 전화번호</td>
                            <td class="sub_content1"><input type="text" name="buyr_tel1" value="<?=$orderer_mobile?>" size="20" maxlength="20" class="frminput" /></td>
                        </tr>

                    </table>
                    <!-- 주문 정보 출력 테이블 End -->

					<!-- 가맹점 정보 출력 테이블 Start -->
                    <table width="527" align="center" cellspacing="0" cellpadding="0" class="margin_top_20">
                        <tr><td colspan="2"  class="title">가맹점 정보</td></tr>
                        <!-- 사업장 구분 -->                        
                        <!-- 발행 사업자 번호 -->
                        <tr>
                            <td class="sub_title1">발행 사업자 번호</td>
                            <td class="sub_content1"><input type="text" name="corp_tax_no" value="<?=$cData[cash_supp_co_num]?>" size="20" maxlength="20" class="frminput" <?=$textStyle?> /></td>
                        </tr>
						<!-- 상호 -->
                        <tr>
                            <td class="sub_title1">상호</td>
                            <td class="sub_content1"><input type="text" name="corp_nm" value="<?=$cData[cash_supp_co_name]?>" size="20" maxlength="20" class="frminput" <?=$textStyle?>></td>
                        </tr>
						<!-- 대표자명 -->
                        <tr>
                            <td class="sub_title1">대표자명</td>
                            <td class="sub_content1"><input type="text" name="corp_owner_nm" value="<?=$cfg[nameCeo]?>" size="20" maxlength="20" class="frminput" <?=$textStyle?>/></td>
                        </tr>
						<!-- 사업장 주소 -->
                        <tr>
                            <td class="sub_title1">사업장 주소</td>
                            <td class="sub_content1"><input type="text" name="corp_addr" value="<?=$cfg[address]?>" size="60" maxlength="200" class="frminput" <?=$textStyle?>/></td>
                        </tr>
						<!-- 사업장 주소 -->
                        <tr>
                            <td class="sub_title1">사업장 대표자 연락처</td>
                            <td class="sub_content1"><input type="text" name="corp_telno" value="<?=$cfg[phoneComp]?>" size="20" maxlength="20" class="frminput" <?=$textStyle?>/></td>
                        </tr>
                    </table>
                    <!-- 가맹점 정보 출력 테이블 End -->
                    
					<!-- 현금영수증 발급 정보 테이블 Start -->
                    <table width="527" align="center" cellspacing="0" cellpadding="0" class="margin_top_20">
                        <tr><td colspan="2"  class="title">현금영수증 발급 정보</td></tr>
                        
                        <!-- 원 거래 시각 -->
                        <tr>
                            <td class="sub_title1">원 거래 시각</td>
                            <td class="sub_content1"><input type="text" name="trad_time" value="<?=$paydt?>" size="20" maxlength="20" class="frminput" <?=$textStyle?>/><br>(예: 2007년 12월 1일 1시 10분 30초의 경우 <br>"20071201011030" 와 같이 입력)</td>
                        </tr>
						<!-- 발행 용도 -->
                        <tr>
                            <td class="sub_title1">발행 용도</td>
							<td class="sub_content1">
                            <input type="radio" name="tr_code" value="0" onClick="jsf__chk_tr_code( this.form )" /> 소득공제용
							<input type="radio" name="tr_code" value="1" onClick="jsf__chk_tr_code( this.form )" /> 지출증빙용</td>
                        </tr>
						<!-- 주민(휴대폰) 번호 -->
                        <tr>
                            <td class="sub_title1">
					        <span id="span_tr_code_0" style="display:block;">주민(휴대폰)번호</span>
							<span id="span_tr_code_1" style="display:none;">사업자번호</span>
							</td>
                            <td class="sub_content1"><input type="text" name="id_info" value="<?=$request_code?>" size="16" maxlength="13" class="frminput" /></td>
                        </tr>
						<!-- 거래금액 총합 -->
                        <tr>
                            <td class="sub_title1">거래금액 총합</td>
                            <td class="sub_content1"><input type="text" name="amt_tot" value="<?=$amt_tot?>" size="20" maxlength="20" class="frminput" /></td>
                        </tr>
						<!-- 공급가액 -->
                        <tr>
                            <td class="sub_title1">공급가액</td>
                            <td class="sub_content1"><input type="text" name="amt_sup" value="<?=$amt_sup?>" class="frminput" />(거래금액 총 합 - 봉사료 - 부가가치세)</td>
                        </tr>
						<!-- 봉사료 -->
                        <tr>
                            <td class="sub_title1"> 봉사료 </td>
                            <td class="sub_content1"><input type="text" name="amt_svc" value="<?=$amt_svc?>" size="20" maxlength="20" class="frminput" /></td>
                        </tr>
						<!-- 부가가치세 -->
                        <tr>
                            <td class="sub_title1"> 부가기치세 </td>
                            <td class="sub_content1"><input type="text" name="amt_tax" value="<?=$amt_tax?>" size="20" maxlength="20" class="frminput" />공급가액의 10%</td>
                        </tr>
                        
                    </table>
                    <!-- 현금 영수증 발급 정보 테이블 End -->

					<!-- 등록 버튼 테이블 Start -->
                    <table width="527" align="center" cellspacing="0" cellpadding="0" class="margin_top_20">
                        <!-- 등록 요청/처음으로 이미지 버튼 -->
                        <tr id="show_pay_btn">
                            <td colspan="2" align="center">
                                <input type="image" src="./img/btn_pay.gif" onclick="return jsf__chk_cash( this.form )" width="108" height="37" alt="등록를 요청합니다" />
                                <a href="index.html"><img src="./img/btn_home.gif" width="108" height="37" alt="처음으로 이동합니다" /></a>
                            </td>
                        </tr>
                        <!-- 등록 진행 중입니다. 메시지 -->
                        <tr id="show_progress" style="display:none">
                            <td colspan="2" class="center red" >등록 진행 중입니다. 잠시만 기다려 주십시오...</td>
                        </tr>
                    </table>
                    <!-- 등록 버튼 테이블 End -->
                </td>
            </tr>
            <tr><td><img src="./img/boxbtm589.gif" alt="Copyright(c) KCP Inc. All rights reserved."/></td></tr>
        </table>
<!-- 요청종류 승인(pay)/변경(mod) 요청시 사용 -->
<input type="hidden" name="req_tx" value="pay">
</form>
</div>
</body>
</html>