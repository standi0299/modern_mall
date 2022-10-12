<?
    /* ============================================================================== */
    /* =   PAGE : 지불 요청 및 결과 처리 PAGE                                       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2006   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   01. 지불 데이터 셋업 (업체에 맞게 수정)                                  = */
    /* = -------------------------------------------------------------------------- = */
    $g_conf_home_dir    = dirname(__FILE__)."/payplus";			// BIN 절대경로 입력
    $g_conf_log_level   = "3";                                  // 변경불가
    $g_conf_pa_url  = "paygw.kcp.co.kr";						// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
    //$g_conf_pa_url  = "testpaygw.kcp.co.kr";            // real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
    $g_conf_pa_port = "8090";                                   // 포트번호 , 변경불가
    $g_conf_mode    = 0;                                        // 변경불가

    require "pp_ax_hub_lib.php";                                // library [수정불가]
    /* ============================================================================== */

    include_once "../../lib/library.php";
    include_once "../../models/m_member.php";

    $gourl = "/member/payment_result.php?payno=$_POST[ordr_idxx]";
    $p_date = $_POST["p_date"];


    ### 중복결제체크
    $data = $db->fetch("select * from tb_fix_member_history where payno='$_POST[ordr_idxx]'");
    $payno = $data[payno];
    $p_date = substr($data["process_month_day"], 0, strpos($data["process_month_day"], "/"));
    

    if (!$data[payno]){
	   $gourl = "/member/payfail.php?payno='$_POST[ordr_idxx]'";
    }
    
    if ($data[payno] && !$data[t_id])
    {

      /* ============================================================================== */
      /* =   02. 지불 요청 정보 설정                                                  = */
      /* = -------------------------------------------------------------------------- = */
      $site_cd        = $_POST[  "site_cd"         ];             // 사이트 코드
      $site_key       = $_POST[  "site_key"        ];             // 사이트 키
      $req_tx         = $_POST[  "req_tx"          ];             // 요청 종류
      $cust_ip        = getenv(  "REMOTE_ADDR"     );             // 요청 IP
      $ordr_idxx      = $_POST[  "ordr_idxx"       ];             // 쇼핑몰 주문번호
      $good_name      = $_POST[  "good_name"       ];             // 상품명
      /* = -------------------------------------------------------------------------- = */
      $good_mny       = $_POST[  "good_mny"        ];             // 결제 총금액
      $tran_cd        = $_POST[  "tran_cd"         ];             // 처리 종류
      /* = -------------------------------------------------------------------------- = */
      $res_cd         = "";                                       // 응답코드
      $res_msg        = "";                                       // 응답메시지
      $tno            = $_POST[  "tno"             ];             // KCP 거래 고유 번호
      /* = -------------------------------------------------------------------------- = */
      $buyr_name      = $_POST[  "buyr_name"       ];             // 주문자명
      $buyr_tel1      = $_POST[  "buyr_tel1"       ];             // 주문자 전화번호
      $buyr_tel2      = $_POST[  "buyr_tel2"       ];             // 주문자 핸드폰 번호
      $buyr_mail      = $_POST[  "buyr_mail"       ];             // 주문자 E-mail 주소
      /* = -------------------------------------------------------------------------- = */
      $bank_name      = "";                                       // 은행명
      $bank_issu      = $_POST[  "bank_issu"       ];             // 계좌이체 서비스사
      /* = -------------------------------------------------------------------------- = */
      $mod_type       = $_POST[  "mod_type"        ];             // 변경TYPE VALUE 승인취소시 필요
      $mod_desc       = $_POST[  "mod_desc"        ];             // 변경사유
      /* = -------------------------------------------------------------------------- = */
      $use_pay_method = $_POST[  "use_pay_method"  ];             // 결제 방법
      $bSucc          = "";                                       // 업체 DB 처리 성공 여부
      $acnt_yn        = $_POST[  "acnt_yn"         ];             // 상태변경시 계좌이체, 가상계좌 여부
      /* = -------------------------------------------------------------------------- = */
      $card_cd        = "";                                       // 신용카드 코드
      $card_name      = "";                                       // 신용카드 명
      $app_time       = "";                                       // 승인시간 (모든 결제 수단 공통)
      $app_no         = "";                                       // 신용카드 승인번호
      $noinf          = "";                                       // 신용카드 무이자 여부
      $quota          = "";                                       // 신용카드 할부개월
      $bankname       = "";                                       // 은행명
      $depositor      = "";                                       // 입금 계좌 예금주 성명
      $account        = "";                                       // 입금 계좌 번호
      /* = -------------------------------------------------------------------------- = */
      $escw_used      = $_POST[  "escw_used"       ];             // 에스크로 사용 여부
      $pay_mod        = $_POST[  "pay_mod"         ];             // 에스크로 결제처리 모드
      $deli_term      = $_POST[  "deli_term"       ];             // 배송 소요일
      $bask_cntx      = $_POST[  "bask_cntx"       ];             // 장바구니 상품 개수
      $good_info      = $_POST[  "good_info"       ];             // 장바구니 상품 상세 정보
      $rcvr_name      = $_POST[  "rcvr_name"       ];             // 수취인 이름
      $rcvr_tel1      = $_POST[  "rcvr_tel1"       ];             // 수취인 전화번호
      $rcvr_tel2      = $_POST[  "rcvr_tel2"       ];             // 수취인 휴대폰번호
      $rcvr_mail      = $_POST[  "rcvr_mail"       ];             // 수취인 E-Mail
      $rcvr_zipx      = $_POST[  "rcvr_zipx"       ];             // 수취인 우편번호
      $rcvr_add1      = $_POST[  "rcvr_add1"       ];             // 수취인 주소
      $rcvr_add2      = $_POST[  "rcvr_add2"       ];             // 수취인 상세주소
      /* ============================================================================== */
  
  
      /* ============================================================================== */
      /* =   03. 인스턴스 생성 및 초기화 (단, 계좌이체 및 교통카드는 제외)            = */
      /* = -------------------------------------------------------------------------- = */
      /* =       결제에 필요한 인스턴스를 생성하고 초기화 합니다. 단, 계좌이체 및     = */
      /* =       교통카드의 경우는 결제 모듈을 통한 전문통신을 하지 않기 때문에       = */
      /* =       결제 모듈을 사용하는 과정에서 제외됩니다.                            = */
      /* = -------------------------------------------------------------------------- = */
      $c_PayPlus = new C_PP_CLI;
      /* ============================================================================== */
  
  
      /* ============================================================================== */
      /* =   04. 처리 요청 정보 설정, 실행                                            = */
      /* = -------------------------------------------------------------------------- = */
  
      /* = -------------------------------------------------------------------------- = */
      /* =   04-1. 승인 요청                                                          = */
      /* = -------------------------------------------------------------------------- = */
      if ( $req_tx == "pay" )
      {
          if ( ( $use_pay_method == "000000000100" ) || ( $bank_issu == "SCOB" ) ) // 동방시스템 계좌이체, 교통카드의 경우
          {
              $tran_cd = "00200000";
  
              $c_PayPlus->mf_set_modx_data( "tno",           $tno       ); // KCP 원거래 거래번호
              $c_PayPlus->mf_set_modx_data( "mod_type",      "STAQ"     ); // 원거래 변경 요청 종류
              $c_PayPlus->mf_set_modx_data( "mod_ip",        $cust_ip   ); // 변경 요청자 IP
              $c_PayPlus->mf_set_modx_data( "mod_ordr_idxx", $ordr_idxx ); // 주문번호
          }
          else
          {
              $c_PayPlus->mf_set_encx_data( $_POST[ "enc_data" ], $_POST[ "enc_info" ] );
          }
      }
    
    /* = -------------------------------------------------------------------------- = */
    /* =   04-2. 매입 요청                                                          = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod" )
    {
        $tran_cd = "00200000";

        $c_PayPlus->mf_set_modx_data( "tno",        $tno            );          // KCP 원거래 거래번호
        $c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );          // 원거래 변경 요청 종류
        $c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );          // 변경 요청자 IP
        $c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );          // 변경 사유
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   04-3. 에스크로 상태변경 요청                                              = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod_escrow" )
    {
        $tran_cd = "00200000";

        $c_PayPlus->mf_set_modx_data( "tno",        $tno            );          // KCP 원거래 거래번호
        $c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );          // 원거래 변경 요청 종류
        $c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );          // 변경 요청자 IP
        $c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );          // 변경 사유
        if ($mod_type == "STE1")                                                // 상태변경 타입이 [배송요청]인 경우
        {
            $c_PayPlus->mf_set_modx_data( "deli_numb",   $_POST[ "deli_numb" ] );          // 운송장 번호
            $c_PayPlus->mf_set_modx_data( "deli_corp",   $_POST[ "deli_corp" ] );          // 택배 업체명
        }
        else if ($mod_type == "STE2" || $mod_type == "STE4")                    // 상태변경 타입이 [즉시취소] 또는 [취소]인 계좌이체, 가상계좌의 경우
        {
            if ($acnt_yn == "Y")
            {
                $c_PayPlus->mf_set_modx_data( "refund_account",   $_POST[ "refund_account" ] );      // 환불수취계좌번호
                $c_PayPlus->mf_set_modx_data( "refund_nm",        $_POST[ "refund_nm"      ] );      // 환불수취계좌주명
                $c_PayPlus->mf_set_modx_data( "bank_code",        $_POST[ "bank_code"      ] );      // 환불수취은행코드
            }
        }
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   04-4. 실행                                                               = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tran_cd != "" )
    {
        $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $site_cd, $site_key, $tran_cd, "",
                              $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx,
                              $cust_ip, $g_conf_log_level, 0, $g_conf_mode );
    }
    else
    {
        $c_PayPlus->m_res_cd  = "9562";
        $c_PayPlus->m_res_msg = "연동 오류 TRAN_CD[" . $tran_cd . "]";
    }

    $res_cd    = $c_PayPlus->m_res_cd;
    $res_msg   = iconv("euc-kr","utf-8",$c_PayPlus->m_res_msg);
    /* ============================================================================== */

  	### 승인처리 시작..
  	$pgcode = $c_PayPlus->m_res_data['tno'];
  	$paytype = ($c_PayPlus->m_res_data[escw_yn]=="Y") ? "e" : "n";
  	if ($paytype=="e") $escrow = 1;
  	$pglog = "거래번호 : $pgcode";
  	$step = 2;

    /* ============================================================================== */
    /* =   05. 승인 결과 처리                                                       = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
      if( $res_cd == "0000" )
      {
        $tno    = $c_PayPlus->mf_get_res_data( "tno"    ); // KCP 거래 고유 번호
        $amount = $c_PayPlus->mf_get_res_data( "amount" ); // KCP 실제 거래 금액

    /* = -------------------------------------------------------------------------- = */
    /* =   05-1. 신용카드 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
        if ( $use_pay_method == "100000000000" )
        {
          $card_cd   = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드 코드
          $card_name = $c_PayPlus->mf_get_res_data( "card_name" ); // 카드 종류
          $app_time  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
          $app_no    = $c_PayPlus->mf_get_res_data( "app_no"    ); // 승인 번호
          $noinf     = $c_PayPlus->mf_get_res_data( "noinf"     ); // 무이자 여부 ( 'Y' : 무이자 )
          $quota     = $c_PayPlus->mf_get_res_data( "quota"     ); // 할부 개월

				  $pglog .= "
                  결제방법 : 신용카드
                  카드정보 : [$card_cd] $card_name
                  승인시간 : $app_time
                  승인번호 : $app_no
                  무이자여부 : $noinf
                  할부개월 : $quota
                  결제금액 : {$c_PayPlus->m_res_data[amount]}원";
        }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-2. 계좌이체 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
        if ( $use_pay_method == "010000000000" )
        {
          $bank_name = iconv("euc-kr","utf-8",$c_PayPlus->mf_get_res_data( "bank_name"  ));  // 은행명
          $bank_code = iconv("euc-kr","utf-8",$c_PayPlus->mf_get_res_data( "bk_code"  ));  // 은행코드

		      ### escw_yn 값이 안 넘어오는 현상에 의해 값 보정
		      if ($_POST[pay_mod]=="Y") $settletype = "escrow";				
		
		      $pglog .= "
                  결제방법 : 계좌이체
                  은행정보 : [$bank_code] $bank_name
                  결제금액 : {$c_PayPlus->m_res_data[amount]}원
                  ";
        }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-3. 가상계좌 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
        if ( $use_pay_method == "001000000000" )
        {
          $bankname  = iconv("euc-kr","utf-8",$c_PayPlus->mf_get_res_data( "bankname"  )); // 입금할 은행 이름
          $depositor = iconv("euc-kr","utf-8",$c_PayPlus->mf_get_res_data( "depositor" )); // 입금할 계좌 예금주
          $account   = $c_PayPlus->mf_get_res_data( "account"   ); // 입금할 계좌 번호
				
				  $step = 1;
				  $pglog .= "
                  결제방법 : 가상계좌
                  가상계좌 : $bankname $account $depositor
                  결제금액 : {$c_PayPlus->m_res_data[amount]}원
              ";
				  $bankinfo = $bankname." ".$account." ".$depositor;
        }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-4. 휴대폰 승인 결과 처리                                              = */
    /* = -------------------------------------------------------------------------- = */
        if ( $use_pay_method == "000010000000" )
        {
          $app_time = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // 승인 시간
        }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-5. 상품권 승인 결과 처리                                              = */
    /* = -------------------------------------------------------------------------- = */
        if ( $use_pay_method == "000000001000" )
        {
          $app_time = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // 승인 시간
        }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-6. ARS 승인 결과 처리                                                 = */
    /* = -------------------------------------------------------------------------- = */
        if ( $use_pay_method == "000000000010" )
        {
          $app_time = $c_PayPlus->mf_get_res_data( "ars_app_time"  ); // 승인 시간
        }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-6. 승인 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
    /* = -------------------------------------------------------------------------- = */
    /* =         승인 결과를 DB 작업 하는 과정에서 정상적으로 승인된 건에 대해      = */
    /* =         DB 작업을 실패하여 DB update 가 완료되지 않은 경우, 자동으로       = */
    /* =         승인 취소 요청을 하는 프로세스가 구성되어 있습니다.                = */
    /* =         DB 작업이 실패 한 경우, bSucc 라는 변수(String)의 값을 "false"     = */
    /* =         로 세팅해 주시기 바랍니다. (DB 작업 성공의 경우에는 "false" 이외의 = */
    /* =         값을 세팅하시면 됩니다.)                                           = */
    /* = -------------------------------------------------------------------------- = */
	
        if ($step==2) $qr = "paydt = now(),";
      
        ### 실데이타 저장
        $bSucc = "false";
        //print_r($data);
        //echo $c_PayPlus->m_res_data[amount];
        
        
        //결재 금액이 다를경우 오류로 처리함.
        if ($data[account_price] == $c_PayPlus->m_res_data[amount])
        {
          $query = "update tb_fix_member_history set 
              paydt = now(),
              account_flag='Y',
              t_id   = '$pgcode',
              pglog   = '$pglog',
              pgcode    = '$pgcode',
              paystep   = '$step',
              bankinfo  = '$bankinfo'
            where payno='$payno'";
          $db->query($query);
          
          //echo $query;
        
          //유효기간 연장
          $m_member = new M_member();
          $m_member->update_fix_date($cid, $data[m_id], $p_date);
      	
          $bSucc = "";             // DB 작업 실패일 경우 "false" 로 세팅
        }
      
        //exit;

    /* = -------------------------------------------------------------------------- = */
    /* =   05-7. DB 작업 실패일 경우 자동 승인 취소                                 = */
    /* = -------------------------------------------------------------------------- = */
        if ( $bSucc == "false" )
        {
            $c_PayPlus->mf_clear();
  
            $tran_cd = "00200000";
  
            $c_PayPlus->mf_set_modx_data( "tno",      $tno                  );         // KCP 원거래 거래번호
            $c_PayPlus->mf_set_modx_data( "mod_type", "STE2"                );         // 원거래 변경 요청 종류
            $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip              );         // 변경 요청자 IP
            $c_PayPlus->mf_set_modx_data( "mod_desc", "결과 처리 오류 - 자동 취소" );  // 변경 사유
  
            $c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $site_cd,
                                  $site_key,  $tran_cd,    "",
                                  $g_conf_pa_url,  $g_conf_pa_port,  "payplus_cli_slib",
                                  $ordr_idxx, $cust_ip,    $g_conf_log_level,
                                  0,    $g_conf_mode );
  
            $res_cd  = $c_PayPlus->m_res_cd;
            $res_msg = iconv("euc-kr","utf-8",$c_PayPlus->m_res_msg);
        }
      }    // End of [res_cd = "0000"]

    /* = -------------------------------------------------------------------------- = */
    /* =   05-8. 승인 실패를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
    /* = -------------------------------------------------------------------------- = */
      else
      {
        
        
          $db->query("update tb_fix_member_history set
            pglog = '$pglog',
            paystep = '$step'
          where payno='$payno'"
          );
          $gourl = "/member/payfail.php?payno=$payno";          
      }
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   06. 매입 결과 처리                                                       = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod" )
    {
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   07. 에스크로 상태변경 결과 처리                                          = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod_escrow" )
    {
    } // End of Process
    /* ============================================================================== */

  }

?>

<script>
function goResult(url){
	var openwin = window.open( 'proc_win.html', 'proc_win', '' );
	if (openwin) openwin.close();
	//location.replace(url);
		
	parent.location.href=url;
}
goResult("<?=$gourl?>");
</script>