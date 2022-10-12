<?
    /* ============================================================================== */
    /* =   PAGE : 결과 처리 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://testpay.kcp.co.kr/pgsample/FAQ/search_error.jsp       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2010.02.   KCP Inc.   All Rights Reserved.                = */
    /* ============================================================================== */
?>
<?
	/* ============================================================================== */
    /* = 사이트 정보 include                                                        = */
    /* = -------------------------------------------------------------------------- = */
    include_once "../mobile/site_conf_inc.php";
	/* ============================================================================== */

	/* ============================================================================== */
    /* =   지불 결과                                                                = */
    /* = -------------------------------------------------------------------------- = */
    $req_tx     = $_POST[ "req_tx"     ];                             // 요청 종류
    $bSucc      = $_POST[ "bSucc"      ];                             // DB처리 여부
    $trad_time  = $_POST[ "trad_time"  ];                             // 원거래 시각
    /* = -------------------------------------------------------------------------- = */
    $ordr_idxx  = $_POST[ "ordr_idxx"  ];                             // 주문번호
    $buyr_name  = $_POST[ "buyr_name"  ];                             // 주문자 이름
    $buyr_tel1  = $_POST[ "buyr_tel1"  ];                             // 주문자 전화번호
    $buyr_mail  = $_POST[ "buyr_mail"  ];                             // 주문자 메일
	$good_name  = $_POST[ "good_name"  ];                             // 주문상품명
    $comment    = $_POST[ "comment"    ];                             // 비고
    /* = -------------------------------------------------------------------------- = */
	$corp_type     = $_POST[ "corp_type"      ];                      // 사업장 구분
	$corp_tax_type = $_POST[ "corp_tax_type"  ];                      // 과세/면세 구분
	$corp_tax_no   = $_POST[ "corp_tax_no"    ];                      // 발행 사업자 번호
	$corp_nm       = $_POST[ "corp_nm"        ];                      // 상호
	$corp_owner_nm = $_POST[ "corp_owner_nm"  ];                      // 대표자명
	$corp_addr     = $_POST[ "corp_addr"      ];                      // 사업장 주소
	$corp_telno    = $_POST[ "corp_telno"     ];                      // 사업장 대표 연락처
    /* = -------------------------------------------------------------------------- = */
    $tr_code    = $_POST[ "tr_code"    ];                             // 발행용도
    $id_info    = $_POST[ "id_info"    ];                             // 신분확인 ID
    $amt_tot    = $_POST[ "amt_tot"    ];                             // 거래금액 총 합
    $amt_sup    = $_POST[ "amt_sup"    ];                             // 공급가액
    $amt_svc    = $_POST[ "amt_svc"    ];                             // 봉사료
    $amt_tax    = $_POST[ "amt_tax"    ];                             // 부가가치세
    /* = -------------------------------------------------------------------------- = */
	$pay_type      = $_POST[ "pay_type"       ];                      // 결제 서비스 구분
	$pay_trade_no  = $_POST[ "pay_trade_no"   ];                      // 결제 거래번호
    /* = -------------------------------------------------------------------------- = */
	$mod_type   = $_POST[ "mod_type"   ];                             // 변경 타입
    $mod_value  = $_POST[ "mod_value"  ];                             // 변경 요청 거래번호
    $mod_gubn   = $_POST[ "mod_gubn"   ];                             // 변경 요청 거래번호 구분
    $mod_mny    = $_POST[ "mod_mny"    ];                             // 변경 요청 금액
    $rem_mny    = $_POST[ "rem_mny"    ];                             // 변경처리 이전 금액
    /* = -------------------------------------------------------------------------- = */
	$res_cd     = $_POST[ "res_cd"     ];                             // 응답코드
    $res_msg    = $_POST[ "res_msg"    ];                             // 응답메시지
    $cash_no    = $_POST[ "cash_no"    ];                             // 현금영수증 거래번호
    $receipt_no = $_POST[ "receipt_no" ];                             // 현금영수증 승인번호
    $app_time   = $_POST[ "app_time"   ];                             // 승인시간(YYYYMMDDhhmmss)
    $reg_stat   = $_POST[ "reg_stat"   ];                             // 등록 상태 코드
    $reg_desc   = $_POST[ "reg_desc"   ];                             // 등록 상태 설명
    /* ============================================================================== */

    $req_tx_name = "";

    if( $req_tx == "pay" )
    {
        $req_tx_name = "등록";
    }
    else if( $req_tx == "mod" )
    {
        $req_tx_name = "변경/조회";
    }


	/* ============================================================================== */
    /* =   가맹점 측 DB 처리 실패시 상세 결과 메시지 설정                           = */
    /* = -------------------------------------------------------------------------- = */

	if($req_tx == "pay")
	{
		//업체 DB 처리 실패
		if($bSucc == "false")
		{
			if ($res_cd == "0000")
            {
                echo "결제는 정상적으로 이루어졌지만 쇼핑몰에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였습니다. <br> 쇼핑몰로 전화하여 확인하시기 바랍니다.";
            }
            else
            {
                echo "결제는 정상적으로 이루어졌지만 쇼핑몰에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였으나, <br> <b>취소가 실패 되었습니다.</b><br> 쇼핑몰로 전화하여 확인하시기 바랍니다.";
            }
		}
	}

	/* = -------------------------------------------------------------------------- = */
    /* =   가맹점 측 DB 처리 실패시 상세 결과 메시지 설정 끝                        = */
    /* ============================================================================== */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <link href="css/sample.css" rel="stylesheet" type="text/css">
    <script type="text/javascript">

        /* 현금영수증 연동 스크립트 */
        function receiptView(cash_no)
        {
            receiptWin = receiptWin = "https://admin.kcp.co.kr/Modules/Service/Cash/Cash_Bill_Common_View.jsp?cash_no="+cash_no;
            window.open(receiptWin , "" , "width=360, height=647")
        }
    </script>
</head>

<body>
    <div align="center">
        <table width="589" cellspacing="0" cellpadding="0">
            <tr style="height:14px"><td style="background-image:url('./img/boxtop589.gif')"></td></tr>
            <tr>
                <td style="background-image:url('./img/boxbg589.gif') " align="center">
                    <table width="551" cellspacing="0" cellpadding="16">
                        <tr style="height:17px">
                            <td style="background-image:url('./img/ttbg551.gif');border:0px " class="white">
                                <span class="bold big">[결과출력]</span> 이 페이지는 결제 결과를 출력하는  페이지입니다.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-image:url('./img/boxbg551.gif');" >
                                결제 결과를 출력하는 페이지 입니다.<br/>
                                요청이 정상적으로 처리된 경우 결과코드(res_cd)값이 0000으로 표시됩니다.
                            </td>
                        </tr>
                        <tr style="height:11px"><td style="background:url('./img/boxbtm551.gif') no-repeat;"></td></tr>
                    </table>


<?
    /* ============================================================================== */
    /* =   결제 결과 코드 및 메시지 출력(결과페이지에 반드시 출력해주시기 바랍니다.)= */
    /* = -------------------------------------------------------------------------- = */
    /* =   결제 정상 : res_cd값이 0000으로 설정됩니다.                              = */
    /* =   결제 실패 : res_cd값이 0000이외의 값으로 설정됩니다.                     = */
    /* = -------------------------------------------------------------------------- = */
?>
                    <table width="85%" align="center" border="0" cellpadding="0" cellspacing="1" class="margin_top_20">
                        <tr><td colspan="2"  class="title">현금영수증 처리 결과</td></tr>
                        <!-- 결과 코드 -->
                        <tr><td class="sub_title1">결과코드</td><td class="sub_content1"><?=$res_cd?></td></tr>
                        <!-- 결과 메시지 -->
                        <tr><td class="sub_title1">결과 메세지</td><td class="sub_content1"><?=$res_msg?></td></tr>
<?
    // 처리 페이지(pp_cli_hub.jsp)에서 가맹점 DB처리 작업이 실패한 경우 상세메시지를 출력합니다.
    if( !$res_msg_bsucc == "")
    {
?>
                        <tr><td class="sub_title1">결과 상세 메세지</td><td><?=$res_msg_bsucc?></td></tr>
<?
    }
?>
                    </table>

<?
	/* = -------------------------------------------------------------------------- = */
    /* =   결제 결과 코드 및 메시지 출력 끝                                         = */
    /* ============================================================================== */
?>

<?
    if ($req_tx == "pay")                          // 거래 구분 : 등록
    {
        if (!$bSucc == "false")                    // 업체 DB 처리 정상
        {
            if ($res_cd == "0000")                 // 정상 승인
            {
?>
                <table width="85%" align="center" border="0" cellpadding="0" cellspacing="1" class="margin_top_10">
					<tr><td colspan="2"  class="title">현금영수증 정보</td></tr>
                    <!-- 현금영수증 거래번호 -->
                    <tr><td class="sub_title1">현금영수증 거래번호</td><td class="sub_content1"><?=$cash_no?></td></tr>
                    <!-- 현금영수증 승인번호 -->
                    <tr><td class="sub_title1">현금영수증 승인번호</td><td class="sub_content1"><?=$receipt_no?></td></tr>
                    <!-- 시간 -->
                    <tr><td class="sub_title1">승인시간</td><td class="sub_content1"><?=$app_time?></td></tr>
					<!-- 영수증 확인 -->
                    <tr>
                    <td class="sub_title1">현금 영수증 확인</td>
                    <td class="sub_content1"><a href="javascript:receiptView('<?=$cash_no?>')"><img src="./img/btn_receipt.gif" alt="영수증을 확인합니다." />
                    </td>
                    <tr><td colspan="2">※ 영수증 확인은 실제결제의 경우에만 가능합니다.</td></tr>
                    <tr class="line2"><td colspan="2" bgcolor="#bbcbdb"></td></tr>
                </table>             
<?
		    }
	    }
    }
?>


<?
    if ($req_tx == "mod")                     // 거래 구분 : 취소 요청
    {
		if ($mod_type == "STSC")
		{
		    if ($res_cd == "0000")
            {
?>
                <table width="85%" align="center" border="0" cellpadding="0" cellspacing="1" class="margin_top_10">
				    <tr><td colspan="2"  class="title">현금영수증 정보</td></tr>
                    <!-- 현금영수증 거래번호 -->
                    <tr><td class="sub_title1">현금영수증 거래번호</td><td class="sub_content1"><?=$cash_no?></td></tr>
                    <!-- 현금영수증 승인번호 -->
                    <tr><td class="sub_title1">현금영수증 승인번호</td><td class="sub_content1"><?=$receipt_no?></td></tr>
                    <!-- 승인시간 -->
                    <tr><td class="sub_title1">승인시간</td><td class="sub_content1"><?=$app_time?></td></tr>
					<!-- 영수증 확인 -->
                    <tr>
                    <td class="sub_title1">현금 영수증 확인</td>
                    <td class="sub_content1"><a href="javascript:receiptView('<?=$cash_no?>')"><img src="./img/btn_receipt.gif" alt="영수증을 확인합니다." />
                    </td>
                    <tr><td colspan="2">※ 영수증 확인은 실제결제의 경우에만 가능합니다.</td></tr>
                    <tr class="line2"><td colspan="2" bgcolor="#bbcbdb"></td></tr>
                </table>

<?
            }
        }
    }
?>

<?
    if ($req_tx == "mod")                     // 거래 구분 : 부분 취소 요청
    {
		if ($mod_type == "STPC")
		{
		    if ($res_cd == "0000")
            {
?>
                <table width="85%" align="center" border="0" cellpadding="0" cellspacing="1" class="margin_top_10">
				    <tr><td colspan="2"  class="title">현금영수증 정보</td></tr>
                    <!-- 현금영수증 거래번호 -->
                    <tr><td class="sub_title1">현금영수증 거래번호</td><td class="sub_content1"><?=$cash_no?></td></tr>
                    <!-- 현금영수증 승인번호 -->
                    <tr><td class="sub_title1">현금영수증 승인번호</td><td class="sub_content1"><?=$receipt_no?></td></tr>
                    <!-- 승인시간 -->
                    <tr><td class="sub_title1">승인시간</td><td class="sub_content1"><?=$app_time?></td></tr>
					<!-- 영수증 확인 -->
                    <tr>
                    <td class="sub_title1">현금 영수증 확인</td>
                    <td class="sub_content1"><a href="javascript:receiptView('<?=$cash_no?>')"><img src="./img/btn_receipt.gif" alt="영수증을 확인합니다." />
                    </td>
                    <tr><td colspan="2">※ 영수증 확인은 실제결제의 경우에만 가능합니다.</td></tr>
                    <tr class="line2"><td colspan="2" bgcolor="#bbcbdb"></td></tr>
                </table>

<?
            }
        }
    }
?>






<?
    if ($req_tx == "mod")                     // 거래 구분 : 조회 요청
    {
		if ( $mod_type == "STSQ" )
		{
            if ($res_cd == "0000")
            {
?>
                <table width="85%" align="center" border="0" cellpadding="0" cellspacing="1" class="margin_top_10">
				    <tr><td colspan="2"  class="title">현금영수증 정보</td></tr>
                    <!-- 현금영수증 거래번호 -->
                    <tr><td class="sub_title1">현금영수증 거래번호</td><td class="sub_content1"><?=$cash_no?></td></tr>
                    <!-- 현금영수증 승인번호 -->
                    <tr><td class="sub_title1">현금영수증 승인번호</td><td class="sub_content1"><?=$receipt_no?></td></tr>
                    <!-- 조회시간 -->
                    <tr><td class="sub_title1">조회시간</td><td class="sub_content1"><?=$app_time?></td></tr>
					<!-- 영수증 확인 -->
                    <tr>
                    <td class="sub_title1">현금 영수증 확인</td>
                    <td class="sub_content1"><a href="javascript:receiptView('<?=$cash_no?>')"><img src="./img/btn_receipt.gif" alt="영수증을 확인합니다." />
                    </td>
                    <tr><td colspan="2">※ 영수증 확인은 실제결제의 경우에만 가능합니다.</td></tr>
                    <tr class="line2"><td colspan="2" bgcolor="#bbcbdb"></td></tr>
                </table>

<?
             }
        }
    }
?>


                <table width="85%" align="center" class="margin_top_10">
					<tr><td style="text-align:center"><a href="index.html"><img src="./img/btn_home.gif" width="108" height="37" alt="처음으로 이동합니다" /></a></td></tr>
                </table>
                </td>
            </tr>
            <tr><td><img src="./img/boxbtm589.gif" alt="Copyright(c) KCP Inc. All rights reserved."/></td></tr>
		</table>
    </div>
</html>
