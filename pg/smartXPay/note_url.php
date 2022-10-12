<?php
	include_once "../../lib/library.php";
    /*
     * 공통결제결과 정보 
     */
    $LGD_RESPCODE = "";           			// 응답코드: 0000(성공) 그외 실패
    $LGD_RESPMSG = "";            			// 응답메세지
    $LGD_MID = "";                			// 상점아이디 
    $LGD_OID = "";                			// 주문번호
    $LGD_AMOUNT = "";             			// 거래금액
    $LGD_TID = "";                			// LG유플러스에서 부여한 거래번호
    $LGD_PAYTYPE = "";            			// 결제수단코드
    $LGD_PAYDATE = "";            			// 거래일시(승인일시/이체일시)
    $LGD_HASHDATA = "";           			// 해쉬값
    $LGD_FINANCECODE = "";        			// 결제기관코드(카드종류/은행코드/이통사코드)
    $LGD_FINANCENAME = "";        			// 결제기관이름(카드이름/은행이름/이통사이름)
    $LGD_ESCROWYN = "";           			// 에스크로 적용여부
    $LGD_TIMESTAMP = "";          			// 타임스탬프
    $LGD_FINANCEAUTHNUM = "";     			// 결제기관 승인번호(신용카드, 계좌이체, 상품권)
	
    /*
     * 신용카드 결제결과 정보
     */
    $LGD_CARDNUM = "";            			// 카드번호(신용카드)
    $LGD_CARDINSTALLMONTH = "";   			// 할부개월수(신용카드) 
    $LGD_CARDNOINTYN = "";        			// 무이자할부여부(신용카드) - '1'이면 무이자할부 '0'이면 일반할부
    $LGD_TRANSAMOUNT = "";        			// 환율적용금액(신용카드)
    $LGD_EXCHANGERATE = "";       			// 환율(신용카드)

    /*
     * 휴대폰
     */
    $LGD_PAYTELNUM = "";          			// 결제에 이용된전화번호

    /*
     * 계좌이체, 무통장
     */
    $LGD_ACCOUNTNUM = "";         			// 계좌번호(계좌이체, 무통장입금) 
    $LGD_CASTAMOUNT = "";         			// 입금총액(무통장입금)
    $LGD_CASCAMOUNT = "";         			// 현입금액(무통장입금)
    $LGD_CASFLAG = "";            			// 무통장입금 플래그(무통장입금) - 'R':계좌할당, 'I':입금, 'C':입금취소 
    $LGD_CASSEQNO = "";           			// 입금순서(무통장입금)
    $LGD_CASHRECEIPTNUM = "";     			// 현금영수증 승인번호
    $LGD_CASHRECEIPTSELFYN = "";  			// 현금영수증자진발급제유무 Y: 자진발급제 적용, 그외 : 미적용
    $LGD_CASHRECEIPTKIND = "";    			// 현금영수증 종류 0: 소득공제용 , 1: 지출증빙용
		$LGD_PAYER = "";			//입금자
    /*
     * OK캐쉬백
     */
    $LGD_OCBSAVEPOINT = "";       			// OK캐쉬백 적립포인트
    $LGD_OCBTOTALPOINT = "";      			// OK캐쉬백 누적포인트
    $LGD_OCBUSABLEPOINT = "";     			// OK캐쉬백 사용가능 포인트

    /*
     * 구매정보
     */
    $LGD_BUYER = "";              			// 구매자
    $LGD_PRODUCTINFO = "";        			// 상품명
    $LGD_BUYERID = "";            			// 구매자 ID
    $LGD_BUYERADDRESS = "";       			// 구매자 주소
    $LGD_BUYERPHONE = "";         			// 구매자 전화번호
    $LGD_BUYEREMAIL = "";         			// 구매자 이메일
    $LGD_BUYERSSN = "";           			// 구매자 주민번호
    $LGD_PRODUCTCODE = "";        			// 상품코드
    $LGD_RECEIVER = "";           			// 수취인
    $LGD_RECEIVERPHONE = "";      			// 수취인 전화번호
    $LGD_DELIVERYINFO = "";       			// 배송지
    

    $LGD_RESPCODE            = $HTTP_POST_VARS["LGD_RESPCODE"];
    $LGD_RESPMSG             = $HTTP_POST_VARS["LGD_RESPMSG"];
    $LGD_MID                 = $HTTP_POST_VARS["LGD_MID"];
    $LGD_OID                 = $HTTP_POST_VARS["LGD_OID"];
    $LGD_AMOUNT              = $HTTP_POST_VARS["LGD_AMOUNT"];
    $LGD_TID                 = $HTTP_POST_VARS["LGD_TID"];
    $LGD_PAYTYPE             = $HTTP_POST_VARS["LGD_PAYTYPE"];
    $LGD_PAYDATE             = $HTTP_POST_VARS["LGD_PAYDATE"];
    $LGD_HASHDATA            = $HTTP_POST_VARS["LGD_HASHDATA"];
    $LGD_FINANCECODE         = $HTTP_POST_VARS["LGD_FINANCECODE"];
    $LGD_FINANCENAME         = $HTTP_POST_VARS["LGD_FINANCENAME"];
    $LGD_ESCROWYN            = $HTTP_POST_VARS["LGD_ESCROWYN"];
    $LGD_TRANSAMOUNT         = $HTTP_POST_VARS["LGD_TRANSAMOUNT"];
    $LGD_EXCHANGERATE        = $HTTP_POST_VARS["LGD_EXCHANGERATE"];
    $LGD_CARDNUM             = $HTTP_POST_VARS["LGD_CARDNUM"];
    $LGD_CARDINSTALLMONTH    = $HTTP_POST_VARS["LGD_CARDINSTALLMONTH"];
    $LGD_CARDNOINTYN         = $HTTP_POST_VARS["LGD_CARDNOINTYN"];
    $LGD_TIMESTAMP           = $HTTP_POST_VARS["LGD_TIMESTAMP"];
    $LGD_FINANCEAUTHNUM      = $HTTP_POST_VARS["LGD_FINANCEAUTHNUM"];
    $LGD_PAYTELNUM           = $HTTP_POST_VARS["LGD_PAYTELNUM"];
    $LGD_ACCOUNTNUM          = $HTTP_POST_VARS["LGD_ACCOUNTNUM"];
    $LGD_CASTAMOUNT          = $HTTP_POST_VARS["LGD_CASTAMOUNT"];
    $LGD_CASCAMOUNT          = $HTTP_POST_VARS["LGD_CASCAMOUNT"];
    $LGD_CASFLAG             = $HTTP_POST_VARS["LGD_CASFLAG"];
    $LGD_CASSEQNO            = $HTTP_POST_VARS["LGD_CASSEQNO"];
    $LGD_CASHRECEIPTNUM      = $HTTP_POST_VARS["LGD_CASHRECEIPTNUM"];
    $LGD_CASHRECEIPTSELFYN   = $HTTP_POST_VARS["LGD_CASHRECEIPTSELFYN"];
    $LGD_CASHRECEIPTKIND     = $HTTP_POST_VARS["LGD_CASHRECEIPTKIND"];
    $LGD_OCBSAVEPOINT        = $HTTP_POST_VARS["LGD_OCBSAVEPOINT"];
    $LGD_OCBTOTALPOINT       = $HTTP_POST_VARS["LGD_OCBTOTALPOINT"];
    $LGD_OCBUSABLEPOINT      = $HTTP_POST_VARS["LGD_OCBUSABLEPOINT"];

    $LGD_BUYER               = $HTTP_POST_VARS["LGD_BUYER"];
    $LGD_PRODUCTINFO         = $HTTP_POST_VARS["LGD_PRODUCTINFO"];
    $LGD_BUYERID             = $HTTP_POST_VARS["LGD_BUYERID"];
    $LGD_BUYERADDRESS        = $HTTP_POST_VARS["LGD_BUYERADDRESS"];
    $LGD_BUYERPHONE          = $HTTP_POST_VARS["LGD_BUYERPHONE"];
    $LGD_BUYEREMAIL          = $HTTP_POST_VARS["LGD_BUYEREMAIL"];
    $LGD_BUYERSSN            = $HTTP_POST_VARS["LGD_BUYERSSN"];
    $LGD_PRODUCTCODE         = $HTTP_POST_VARS["LGD_PRODUCTCODE"];
    $LGD_RECEIVER            = $HTTP_POST_VARS["LGD_RECEIVER"];
    $LGD_RECEIVERPHONE       = $HTTP_POST_VARS["LGD_RECEIVERPHONE"];
    $LGD_DELIVERYINFO        = $HTTP_POST_VARS["LGD_DELIVERYINFO"];
		
		$LGD_PAYER     			 = $HTTP_POST_VARS["LGD_PAYER"];      			// 입금자명

    //$LGD_MERTKEY = "95160cce09854ef44d2edb2bfb05f9f3";  //LG유플러스에서 발급한 상점키로 변경해 주시기 바랍니다.
    $LGD_MERTKEY = $cfg[pg][lgd_mertkey];  //LG유플러스에서 발급한 상점키로 변경해 주시기 바랍니다.
    $LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY); 
		//$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$LGD_MERTKEY);		//$LGD_RESPCODE 는 제거되어야 한다.


	### 로그저장
	ob_start();
	$fp = fopen("log/$LGD_OID","w");	
	print_r($HTTP_POST_VARS);
	$ret = ob_get_contents();
	fwrite($fp,$ret);
	fclose($fp);
	chmod("log/$LGD_OID", 0757);
	ob_end_clean();


  /*
   * 상점 처리결과 리턴메세지
   *
   * OK   : 상점 처리결과 성공
   * 그외 : 상점 처리결과 실패
   *
   * ※ 주의사항 : 성공시 'OK' 문자이외의 다른문자열이 포함되면 실패처리 되오니 주의하시기 바랍니다.
   */    
  $resultMSG = "결제결과 상점 DB처리 결과값을 입력해 주시기 바랍니다.";
  
  if ($LGD_HASHDATA2 == $LGD_HASHDATA) {      //해쉬값 검증이 성공하면
		if($LGD_RESPCODE == "0000"){            //결제가 성공이면
    	/*
       * 거래성공 결과 상점 처리(DB) 부분
       * 상점 결과 처리가 정상이면 "OK"
       */
       
           
      //기본값 설정
  		$step		= 2;
			$orderInputRequest = true;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false 
			$payno = $LGD_OID;     
			$pgcode		= $LGD_TID;
			$namePayer	= $LGD_BUYER;
			$escrow		= ($LGD_ESCROWYN=="Y") ? 1 : 0;
			$pglog		= "$payno (".date('Y:m:d H:i:s').")거래번호 : ".$pgcode."승인금액 : ".$LGD_AMOUNT;
			$PG_Account = $LGD_AMOUNT;
	
			switch ($LGD_PAYTYPE)
			{
				case "SC0010": //신용카드
				
					$pglog .= "
						결제방법 : 카드결제
						승인번호 : $LGD_FINANCEAUTHNUM
						카드회사 : $LGD_FINANCENAME
						카드번호 : $LGD_CARDNUM
						할부개월수 : $LGD_CARDINSTALLMONTH
						무이자할부여부 : $LGD_CARDNOINTYN
						환율적용금액 : $LGD_TRANSAMOUNT
						환율 : $LGD_EXCHANGERATE
						";
		
						break;
		
				case "SC0030": //계좌이체
		
					$pglog .= "
						결제방법 : 계좌이체
						계좌이체 : $LGD_FINANCENAME / $LGD_ACCOUNTNUM / $LGD_PAYER;
						";
		
						break;
		
				case "SC0040": //가상계좌(무통장)
					$bankinfo	= "$LGD_FINANCENAME / $LGD_ACCOUNTNUM / $LGD_PAYER";
					$add_fields	.= ",bankinfo	= '$bankinfo'";
	
					$pglog .= "
					결제방법 : 가상계좌
					가상계좌 : ".$bankinfo;
						
					if ($LGD_CASFLAG=="R"){
						$step = 1;						
					}
					
					//무통장 입금 성공 
					if ($LGD_CASFLAG=="I"){
						$orderInputRequest = false;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false
						$pglog = "";
					}
					
					//무통장 입금취소 
					if ($LGD_CASFLAG=="C"){
						$step = -9;
						echo "가상계좌 입금 취소 기능 제공안됨.";
						exit;						
					}
		
					break;
		
				case "SC0060": //가상계좌(휴대폰)
			
					$pglog .= "
						결제방법 : 핸드폰결제
						결제전화번호 : $LGD_PAYTELNUM
						";
		
					break;
		
			}

			include "../pg_pay_seccess.php";
					
			$resultMSG = $pgPayReturnMsg;
             
		} 
		else 
		{                                 //결제가 실패이면
        /*
         * 거래실패 결과 상점 처리(DB) 부분
         * 상점결과 처리가 정상이면 "OK"
         */  
       //if( 결제실패 상점처리결과 성공 ) 
       
			$step = -1;
			$pglog = "거래번호 : $pgcode
				[$LGD_RESPCODE] $LGD_RESPMSG
			";

			include "../pg_pay_fail.php";
			$resultMSG = "OK";    
		}
  } 
  else 
  {                                    //해쉬값 검증이 실패이면
    /*
    * hashdata검증 실패 로그를 처리하시기 바랍니다. 
    */  
		$resultMSG = "결제결과 상점 DB처리 해쉬값 검증이 실패하였습니다.";         
  }

	echo $resultMSG;        
?>
