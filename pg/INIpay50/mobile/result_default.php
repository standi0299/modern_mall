<?
    /* ============================================================================== */
    /* =   PAGE : 결제 결과 출력 PAGE                                               = */
    /* = -------------------------------------------------------------------------- = */
    /* =   결제 요청 결과값을 출력하는 페이지입니다.                                = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do			        = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   환경 설정 파일 Include                                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ※ 필수                                                                  = */
    /* =   테스트 및 실결제 연동시 site_conf_inc.jsp 파일을 수정하시기 바랍니다.    = */
    /* = -------------------------------------------------------------------------- = */

    include_once "site_conf_inc.php";       // 환경설정 파일 include
    include_once "../../../_header.php";	// 쿼리관련 함수 include

    /* = -------------------------------------------------------------------------- = */
    /* =   환경 설정 파일 Include END                                               = */
    /* ============================================================================== */
?>
<?
  // 지불 정보
  $site_cd          = $_POST[ "site_cd"        ];      // 사이트 코드
  $req_tx           = $_POST[ "req_tx"         ];      // 요청 구분(승인/취소)
  $use_pay_method   = $_POST[ "use_pay_method" ];      // 사용 결제 수단
  $bSucc            = $_POST[ "bSucc"          ];      // 업체 DB 정상처리 완료 여부
  // 주문 정보
  $amount           = $_POST[ "amount"         ];      // 금액
  $tno              = $_POST[ "tno"            ];      // KCP 거래번호
  $ordr_idxx        = $_POST[ "ordr_idxx"      ];      // 주문번호
  $good_name        = $_POST[ "good_name"      ];      // 상품명
  $good_mny         = $_POST[ "good_mny"       ];      // 결제 금액
  $buyr_name        = $_POST[ "buyr_name"      ];      // 구매자명
  $buyr_tel1        = $_POST[ "buyr_tel1"      ];      // 구매자 전화번호
  $buyr_tel2        = $_POST[ "buyr_tel2"      ];      // 구매자 휴대폰번호
  $buyr_mail        = $_POST[ "buyr_mail"      ];      // 구매자 E-Mail
  // 결과 코드
  $res_cd           = $_POST[ "res_cd"         ];      // 결과 코드
  $res_msg          = $_POST[ "res_msg"        ];      // 결과 메시지
  $res_msg_bsucc    = "";
  // 공통
  $app_time         = $_POST[ "app_time"       ];      // 승인시간 (공통)
  $pnt_issue        = $_POST[ "pnt_issue"      ];      // 포인트 서비스사
  // 신용카드
  $card_cd          = $_POST[ "card_cd"        ];      // 카드 코드
  $card_name        = $_POST[ "card_name"      ];      // 카드명
  $app_no           = $_POST[ "app_no"         ];      // 승인번호
  $noinf            = $_POST[ "noinf"          ];      // 무이자 여부
  $quota            = $_POST[ "quota"          ];      // 할부개월
  $partcanc_yn      = $_POST[ "partcanc_yn"    ];      // 부분취소 여부
  // 계좌이체
  $bank_name        = $_POST[ "bank_name"      ];      // 은행명
  $bank_code        = $_POST[ "bank_code"      ];      // 은행코드
  // 가상계좌
  $bankname         = $_POST[ "bankname"       ];      // 입금할 은행
  $depositor        = $_POST[ "depositor"      ];      // 입금할 계좌 예금주
  $account          = $_POST[ "account"        ];      // 입금할 계좌 번호
  $va_date          = $_POST[ "va_date"        ];      // 입금마감시간
  // 포인트
  $add_pnt          = $_POST[ "add_pnt"        ];      // 발생 포인트
  $use_pnt          = $_POST[ "use_pnt"        ];      // 사용가능 포인트
  $rsv_pnt          = $_POST[ "rsv_pnt"        ];      // 적립 포인트
  $pnt_app_time     = $_POST[ "pnt_app_time"   ];      // 승인시간
  $pnt_app_no       = $_POST[ "pnt_app_no"     ];      // 승인번호
  $pnt_amount       = $_POST[ "pnt_amount"     ];      // 적립금액 or 사용금액
  // 휴대폰
  $commid           = $_POST[ "commid"		   ];      // 통신사 코드
  $mobile_no        = $_POST[ "mobile_no"      ];      // 휴대폰 번호
  // 상품권
  $tk_van_code      = $_POST[ "tk_van_code"    ];      // 발급사 코드
  $tk_app_no        = $_POST[ "tk_app_no"      ];      // 승인 번호
  // 현금영수증
  $cash_yn          = $_POST[ "cash_yn"        ];      // 현금 영수증 등록 여부
  $cash_authno      = $_POST[ "cash_authno"    ];      // 현금 영수증 승인 번호
  $cash_tr_code     = $_POST[ "cash_tr_code"   ];      // 현금 영수증 발행 구분
  $cash_id_info     = $_POST[ "cash_id_info"   ];      // 현금 영수증 등록 번호

  /* 기타 파라메터 추가 부분 - Start - */
  $param_opt_1     = $_POST[ "param_opt_1"     ];      // 기타 파라메터 추가 부분
  $param_opt_2     = $_POST[ "param_opt_2"     ];      // 기타 파라메터 추가 부분
  $param_opt_3     = $_POST[ "param_opt_3"     ];      // 기타 파라메터 추가 부분
  /* 기타 파라메터 추가 부분 - End -   */

  $req_tx_name     = "";

  if ( $req_tx == "pay" )
  {
    $req_tx_name = "지불" ;
  }
  else if ( $req_tx == "mod" )
  {
    $req_tx_name = "취소/매입" ;
  }

    /* ============================================================================== */
    /* =   가맹점 측 DB 처리 실패시 상세 결과 메시지 설정                           = */
    /* = -------------------------------------------------------------------------- = */

    if ( $req_tx == "pay" )
    {
        // 업체 DB 처리 실패
        if ( $bSucc == "false" )
        {
            if ( $res_cd == "0000" )
            {
                $res_msg_bsucc = "결제는 정상적으로 이루어졌지만 쇼핑몰에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였습니다.<br />쇼핑몰로 전화하여 확인하시기 바랍니다." ;
            }
            else
            {
                $res_msg_bsucc = "결제는 정상적으로 이루어졌지만 쇼핑몰에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였으나<br /><b>취소가 실패 되었습니다.</b><br />쇼핑몰로 전화하여 확인하시기 바랍니다.";
            }
        }
    }
	else
	{
		$res_msg_bsucc = "주문이 취소 되었습니다.<br />다시 주문해주시기 바랍니다.";
	}

    /* = -------------------------------------------------------------------------- = */
    /* =   가맹점 측 DB 처리 실패시 상세 결과 메시지 설정 끝                        = */
    /* ========0====================================================================== */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>*** KCP [AX-HUB Version] ***</title>
<meta name="viewport" content="width=device-width, user-scalable=1.0, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/style.css" rel="stylesheet" type="text/css" id="cssLink"/>
<link href="../../../skin/m_basic/css/m.css" rel="stylesheet">
<script type="text/javascript">
  var controlCss = "css/style_mobile.css";
  var isMobile = {
    Android: function() {
      return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
      return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
      return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
      return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
      return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
      return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
  };

  if( isMobile.any() )
    document.getElementById("cssLink").setAttribute("href", controlCss);
</script>
</head>

<body>
<form name="cancel" method="post" style="width: 100%; height: 100%;">
 <section id="wrap">
  <section class="contents bgArea">		
   <div class="orderArea">
    <div class="part">
     <p class="tit">결제완료</p>		
<?
    /* ============================================================================== */
    /* =   결제 결과 코드 및 메시지 출력(결과페이지에 반드시 출력해주시기 바랍니다.)= */
    /* = -------------------------------------------------------------------------- = */
    /* =   결제 정상 : res_cd값이 0000으로 설정됩니다.                              = */
    /* =   결제 실패 : res_cd값이 0000이외의 값으로 설정됩니다.                     = */
    /* = -------------------------------------------------------------------------- = */
?>

<?
    /* = -------------------------------------------------------------------------- = */
    /* =   결제 결과 코드 및 메시지 출력 끝                                         = */
    /* ============================================================================== */
?>

<?
    /* ============================================================================== */
    /* =   01. 결제 결과 출력                                                       = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
        /* ============================================================================== */
        /* =   01-1. 업체 DB 처리 정상(bSucc값이 false가 아닌 경우)                     = */
        /* = -------------------------------------------------------------------------- = */
        if ( $bSucc != "false" )
        {
            /* ============================================================================== */
            /* =   01-1-1. 정상 결제시 결제 결과 출력 ( res_cd값이 0000인 경우)             = */
            /* = -------------------------------------------------------------------------- = */
            if ( $res_cd == "0000" )
            {
            	//20150907 / minks / 모바일 제작기간, 배송기간 안내 문구 출력
    			$data = $db->fetch("select * from exm_pay where payno = '$ordr_idxx'");
    
    			$query = "select * from exm_ord where payno = '$data[payno]'";
				$res = $db->query($query);
				while ($ord = $db->fetch($res)){
					$data['ord'][$ord[ordno]] = $ord;
					$query = "select * from exm_ord_item where payno = '$data[payno]' and ordno = '$ord[ordno]'";
					$res2 = $db->query($query);
					while ($item = $db->fetch($res2)){
						list($item[leadtime]) = $db->fetch("select leadtime from exm_goods where goodsno='$item[goodsno]'",1);
						$item[leadtime] = str_split(preg_replace("/[^0-9]*/s", "", $item[leadtime]));
						if (!$data[leadtime] || $item[leadtime][sizeof($item[leadtime])-1] > $data[leadtime][sizeof($data[leadtime])-1]) {
							$data[leadtime] = $item[leadtime];
							$data[deliverytime] = $data[leadtime][sizeof($data[leadtime])-1]+2;
						}
      
						$data['ord'][$ord[ordno]][item][$item[ordseq]] = $item;
						$db->fetch("delete from exm_cart where cartno = '$item[cartno]'");
					}
				}
	
				$data[leadtime] = ($data[leadtime][1]) ? $data[leadtime][0]."~".$data[leadtime][1] : $data[leadtime][0];
?>
				<div class="paymentResult">
					<strong>주문하신 상품이 성공적으로 결제 되었습니다.</strong><br />
					<span>상품 배송기간은 제작기간(<?=$data[leadtime]?>일)과<br />배송기간을 포함하여<br />약 <?=$data[deliverytime]?>일 정도 소요될 예정입니다.</span><br />
					<a href="../../../mypage/orderlist.php?guest=1&skin_type=m_basic" target="_parent">주문확인</a>
				</div>	
<?
            }
            /* = -------------------------------------------------------------------------- = */
            /* =   01-1-1. 정상 결제시 결제 결과 출력 END                                   = */
            /* ============================================================================== */
			else
			{
?>
				<div class="paymentResult">
					<strong>오류코드 : <?=$res_cd?></strong><br />
<?
					if ( $use_pay_method == "100000000000" )
					{
?>
					<span>카드 결제에 실패하였습니다.<br />해당 카드사에 문의하시기 바랍니다.</span><br />
<?
					}
					else if ( $use_pay_method == "000010000000" )
					{
?>
					<span>핸드폰 결제에 실패하였습니다.<br />해당 통신사에 문의하시기 바랍니다.</span><br />
<?
					}
?>
					<a href="../../../order/cart.php" target="_parent">다시결제</a>
				</div>
<?
			}
        }
        /* = -------------------------------------------------------------------------- = */
        /* =   01-1. 업체 DB 처리 정상 END                                              = */
        /* ============================================================================== */
		else
		{
?>
			<div class="paymentResult">
				<strong>오류코드 : <?=$res_cd?></strong><br />
<?
				// 처리 페이지(pp_ax_hub.jsp)에서 가맹점 DB처리 작업이 실패한 경우 상세메시지를 출력합니다.
    			if( !$res_msg_bsucc == "" )
    			{
?>
					<span><?=$res_msg_bsucc?></span><br />
<?
				}				
?>
				<a href="../../../order/cart.php" target="_parent">다시결제</a>
			</div>
<?
		}
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   01. 결제 결과 출력 END                                                   = */
    /* ============================================================================== */
	else 
	{
?>
		<div class="paymentResult">
			<strong>오류코드 : <?=$res_cd?></strong><br />
<?
    			if( !$res_msg_bsucc == "" )
    			{
?>
				<span><?=$res_msg_bsucc?></span><br />
<?
				}				
?>
			<a href="../../../order/cart.php" target="_parent">다시결제</a>
		</div>
<?	
	}
?>            
    </div>
   </section>
  </section>
 </form>
</body>
</html>