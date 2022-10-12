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
  
  include_once "../../lib/library.php";
	include_once "./INIPayUtil.php";

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
				$page_return_url = "/order/payfail.php?payno=".$ordr_idxx;
      }
      else
      {
        $res_msg_bsucc = "결제는 정상적으로 이루어졌지만 쇼핑몰에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였으나<br /><b>취소가 실패 되었습니다.</b><br />쇼핑몰로 전화하여 확인하시기 바랍니다.";
				$page_return_url = "/order/payfail.php?payno=".$ordr_idxx;
      }
    }
  }
	else
	{
		$res_msg_bsucc = "주문이 취소 되었습니다.<br />다시 주문해주시기 바랍니다.";
	}
	
	
	if ($res_msg_bsucc == "")
	{
		$page_return_url = "/order/payend.php?payno=".$ordr_idxx;								
	} 
	
	inipayComplete($res_msg_bsucc, $page_return_url);					
		
?>
