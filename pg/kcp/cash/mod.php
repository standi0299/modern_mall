<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?
    /* ============================================================================== */
    /* =   PAGE : 변경 요청 PAGE                                                    = */
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
    function  jsf__mod_cash( form )
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

	
    function  jsf__chk_cash( form )
    {
    	len_mod_value = form.mod_value.value.length;
    	len_trad_time = form.trad_time.value.length;
    	
	    if ( form.mod_gubn[0].checked )
	    {
	    	if ( len_mod_value != 14 )
	    	{
		        alert("현금영수증 거래번호를 정확히 입력해 주시기 바랍니다.");
		        form.mod_value.select();
		        form.mod_value.focus();
		        return false;
	        }
	    }
	    else if (form.mod_gubn[1].checked )
	    {
	    	if ( len_mod_value != 9 )
	    	{
		        alert("현금영수증 승인번호를 정확히 입력해 주시기 바랍니다.");
		        form.mod_value.select();
		        form.mod_value.focus();
		        return false;
	        }
	    }
	    else if ( form.mod_gubn[2].checked )
	    {
	    	if ( len_mod_value != 10 && len_mod_value != 11 && len_mod_value != 13 )
	    	{
		        alert("등록 된 신분확인 아이디를 정확히 입력해 주시기 바랍니다.");
		        form.mod_value.select();
		        form.mod_value.focus();
		        return false;
	        }
	    }
	    else if (form.mod_gubn[3].checked )
	    {
	    	if ( len_mod_value != 14 )
	    	{
		        alert("PG등록 거래번호를 정확히 입력해 주시기 바랍니다.");
		        form.mod_value.select();
		        form.mod_value.focus();
		        return false;
	        }
	    }

        if ( len_trad_time != 14 )
        {
            alert("원 거래 시각을 정확히 입력해 주시기 바랍니다.");
            form.trad_time.select();
            form.trad_time.focus();
            return false;
        }
        
        return true;
    }
    
    function  jsf__chk_mod_gubn( form )
    {
        var span_mod_value_0 = document.getElementById( "span_mod_value_0" );
        var span_mod_value_1 = document.getElementById( "span_mod_value_1" );
        var span_mod_value_2 = document.getElementById( "span_mod_value_2" );
        var span_mod_value_3 = document.getElementById( "span_mod_value_3" );

        if ( form.mod_gubn[0].checked )
        {
            span_mod_value_0.style.display = "block";
            span_mod_value_1.style.display = "none";
            span_mod_value_2.style.display = "none";
            span_mod_value_3.style.display = "none";
        }
        else if (form.mod_gubn[1].checked )
        {
            span_mod_value_0.style.display = "none";
            span_mod_value_1.style.display = "block";
            span_mod_value_2.style.display = "none";
            span_mod_value_3.style.display = "none";
        }
        else if ( form.mod_gubn[2].checked )
        {
            span_mod_value_0.style.display = "none";
            span_mod_value_1.style.display = "none";
            span_mod_value_2.style.display = "block";
            span_mod_value_3.style.display = "none";
        }
        else if (form.mod_gubn[3].checked )
        {
            span_mod_value_0.style.display = "none";
            span_mod_value_1.style.display = "none";
            span_mod_value_2.style.display = "none";
            span_mod_value_3.style.display = "block";
        }
    }
    
    function  jsf__chk_mod_type( form )
    {
    	var div_division_cancel = document.getElementById( "div_division_cancel" );
    	
        if ( form.mod_type[0].checked )
        {
            div_division_cancel.style.display = "none";
        }
        else if ( form.mod_type[1].checked )
        {
            div_division_cancel.style.display = "block";
        }
        else if ( form.mod_type[2].checked )
        {
            div_division_cancel.style.display = "none";
        }
    }

</script>
</head>


<body>
<form name="cash_form" action="./pp_cli_hub.php" method="post">
<div align="center">

<table width="589" cellpadding="0" cellspacing="0">
            <tr style="height:14px"><td style="background-image:url('./img/boxtop589.gif');"></td></tr>
            <tr>
                <td style="background-image:url('./img/boxbg589.gif')">

                    <!-- 상단 테이블 Start -->
                    <table width="551px" align="center" cellspacing="0" cellpadding="16">
                        <tr style="height:17px">
                            <td style="background-image:url('./img/ttbg551.gif');border:0px " class="white">
                                <span class="bold big">[취소요청]</span> 이 페이지는 현금영수증 등록를 요청하는 샘플(예시) 페이지입니다.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-image:url('./img/boxbg551.gif') ;">
                                <p class="align_left">소스 수정시 가맹점의 상황에 맞게 적절히 수정 적용하시길 바랍니다.</p>
                                <p class="align_left">등록에 필요한 정보를 정확하게 입력하시어 등록를 진행하시기 바랍니다.</p>
                            </td>
                        </tr>
                        <tr style="height:11px"><td style="background:url('./img/boxbtm551.gif') no-repeat;"></td></tr>
                    </table>
                    <!-- 상단 테이블 End -->

					<!-- 변경 정보 입력 테이블 Start -->
                    <table width="527" align="center" cellspacing="0" cellpadding="0" class="margin_top_20">
                        <tr><td colspan="2"  class="title">요 청 정 보</td></tr>
                        <!-- 주문 번호 -->
                        <tr>
                            <td class="sub_title1">요청 정보</td>
                            <td class="sub_content1">
							<input type="radio" name="mod_type" onClick="jsf__chk_mod_type( this.form )" value="STSC" checked>취소요청
                            <input type="radio" name="mod_type" onClick="jsf__chk_mod_type( this.form )" value="STPC">부분취소요청
                    	    
							</td>
                        </tr>
						<!-- 변경 요청 거래번호 구분 -->
                        <tr>
                            <td class="sub_title1">변경 요청 거래번호 구분</td>
							<td class="sub_content1">
                            <input type="radio" name="mod_gubn" value="MG01" onClick="jsf__chk_mod_gubn( this.form )" checked>현금 영수증 거래번호<br>&nbsp;&nbsp;
                    	    <input type="radio" name="mod_gubn" value="MG02" onClick="jsf__chk_mod_gubn( this.form )">현금 영수증 승인번호<br>&nbsp;&nbsp;
                    	    <input type="radio" name="mod_gubn" value="MG03" onClick="jsf__chk_mod_gubn( this.form )">신분확인 ID (휴대폰번호/주민번호/사업자번호) <br>&nbsp;&nbsp;
                    	    <input type="radio" name="mod_gubn" value="MG04" onClick="jsf__chk_mod_gubn( this.form )">KCP 결제 건 거래번호(tno)
							</td>
                        </tr>
						<!-- 거래번호 확인 -->
                        <tr>
						    
                            <td class="sub_title1">
							<span id="span_mod_value_0" style="display:block;">현금영수증 거래번호</span>
                            <span id="span_mod_value_1" style="display:none;">현금영수증 승인번호</span>
                            <span id="span_mod_value_2" style="display:none;">신분확인 ID <br> (휴대폰번호/주민번호/사업자번호)</span>
                            <span id="span_mod_value_3" style="display:none;">KCP 결제 건 거래번호</span>
							</td>
                            <td class="sub_content1"><input type="text" name="mod_value" value="" size="20" maxlength="20" class="frminput" /></td>
                        </tr>
                    
                        <!-- 원 거래 시각 -->
                        <tr>
                            <td class="sub_title1">원 거래 시각</td>
                            <td class="sub_content1"><input type="text" name="trad_time" value="" size="20" maxlength="20" class="frminput" /></td>
                        </tr>
                    </table>
		                <div id = div_division_cancel style="display:none;">
			                <table width="527" align="center" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="sub_title1">변경요청 금액</td>
                                    <td class="sub_content1"><input type="text" name="mod_mny" value="" size="20" maxlength="12" class="frminput" /></td>
                                </tr>
								<tr>
                                    <td class="sub_title1">변경처리 이전 금액</td>
                                    <td class="sub_content1"><input type="text" name="rem_mny" value="" size="20" maxlength="12" class="frminput" /></td>
                                </tr>
				            </table>
					    </div>
				<!-- 변경 정보 입력 테이블 End -->
			    
                <!-- 등록 버튼 테이블 Start -->
                    <table width="527" align="center" cellspacing="0" cellpadding="0" class="margin_top_20">
                        <!-- 등록 요청/처음으로 이미지 버튼 -->
                        <tr id="show_pay_btn">
                            <td colspan="2" align="center">
                                <input type="image" src="./img/btn_cancel.gif" onclick="return jsf__chk_cash( this.form )" width="108" height="37" alt="등록를 요청합니다" />
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
<input type="hidden" name="req_tx" value="mod">
</form>
</body>
</html>