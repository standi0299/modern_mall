<?php
	include_once "../../lib/library.php";
	
	include_once "./SmartXPayUtil.php";
	
    /*
     * [최종결제요청 페이지(STEP2-2)]
     *
     * LG유플러스으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
     */
	
	/* ※ 중요
	* 환경설정 파일의 경우 반드시 외부에서 접근이 가능한 경로에 두시면 안됩니다.
	* 해당 환경파일이 외부에 노출이 되는 경우 해킹의 위험이 존재하므로 반드시 외부에서 접근이 불가능한 경로에 두시기 바랍니다. 
	* 예) [Window 계열] C:\inetpub\wwwroot\lgdacom ==> 절대불가(웹 디렉토리)
	*/
	
	//$configPath = "C:/lgdacom"; //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정. 
	$configPath 				= dirname(__FILE__) ."/lgdacom"; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

  /*
   *************************************************
   * 1.최종결제 요청 - BEGIN
   *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
   *************************************************
  */
  $CST_PLATFORM               = $HTTP_POST_VARS["CST_PLATFORM"];
  $CST_MID                    = $HTTP_POST_VARS["CST_MID"];
  $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
  $LGD_PAYKEY                 = $HTTP_POST_VARS["LGD_PAYKEY"];

  require_once("./lgdacom/XPayClient.php");
  $xpay = &new XPayClient($configPath, $CST_PLATFORM);
  $xpay->Init_TX($LGD_MID);    
    
  $xpay->Set("LGD_TXNAME", "PaymentByKey");
  $xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
    
  //금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
	//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
	//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
	//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);
	    
  /*
   *************************************************
   * 1.최종결제 요청(수정하지 마세요) - END
   *************************************************
   */

	/*
   * 2. 최종결제 요청 결과처리
   *
   * 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
   */
	$pglog = "";   
	$page_return_url = "/order/cart.php";  
	 
  if ($xpay->TX()) {
  	//1)결제결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
    //echo "결제요청이 완료되었습니다.  <br>";
    //echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
    //echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
        
    //echo "거래번호 : " . $xpay->Response("LGD_TID",0) . "<br>";
    //echo "상점아이디 : " . $xpay->Response("LGD_MID",0) . "<br>";
    //echo "상점주문번호 : " . $xpay->Response("LGD_OID",0) . "<br>";
    //echo "결제금액 : " . $xpay->Response("LGD_AMOUNT",0) . "<br>";
    //echo "결과코드 : " . $xpay->Response("LGD_RESPCODE",0) . "<br>";
    //echo "결과메세지 : " . $xpay->Response("LGD_RESPMSG",0) . "<p>";
        
    $keys = $xpay->Response_Names();
    foreach($keys as $name) {
    	//echo $name . " = " . $xpay->Response($name, 0) . "<br>";
    }
      
    
		$xpayCode = $xpay->Response_Code();				
    $xpayMsg = $xpay->Response_Msg();
        
    $xpayLGD_TID = $xpay->Response("LGD_TID",0);
    $xpayLGD_MID = $xpay->Response("LGD_MID",0);
    $xpayLGD_OID = $xpay->Response("LGD_OID",0);
    $xpayLGD_AMOUNT = $xpay->Response("LGD_AMOUNT",0);
    $xpayLGD_RESPCODE = $xpay->Response("LGD_RESPCODE",0);
    $xpayLGD_RESPMSG = $xpay->Response("LGD_RESPMSG",0);
    		
		$xpayLGD_ESCROWYN = $xpay->Response("LGD_ESCROWYN",0);
		$xpayLGD_PAYTYPE = $xpay->Response("LGD_PAYTYPE",0);			//결제 수단		
		
		$xpayLGD_FINANCENAME = $xpay->Response("LGD_FINANCENAME",0);			//결제기관명 
		$xpayLGD_ACCOUNTNUM  = $xpay->Response("LGD_ACCOUNTNUM",0);			//입금할 계좌번호
		$xpayLGD_CASFLAG   = $xpay->Response("LGD_CASFLAG",0);			//가상계좌 거래종류(R:할당,I:입금,C:취소) 
    
    
    $log_msg = array("xpayCode" => $xpayCode,
    "xpayMsg" => $xpayMsg,
    "xpayLGD_TID" => $xpayLGD_TID,
    "xpayLGD_MID" => $xpayLGD_MID,
    "xpayLGD_OID" => $xpayLGD_OID,
    "xpayLGD_AMOUNT" => $xpayLGD_AMOUNT,
    "xpayLGD_RESPCODE" => $xpayLGD_RESPCODE,
    "xpayLGD_RESPMSG" => $xpayLGD_RESPMSG,
    "xpayLGD_ESCROWYN" => $xpayLGD_ESCROWYN,
    "xpayLGD_PAYTYPE" => $xpayLGD_PAYTYPE,
    "xpayLGD_FINANCENAME" => $xpayLGD_FINANCENAME,
    "xpayLGD_ACCOUNTNUM" => $xpayLGD_ACCOUNTNUM,
    "xpayLGD_CASFLAG" => $xpayLGD_CASFLAG,
    );
    writeLog($xpayLGD_OID, $log_msg);
		   
    if( $xpayCode == "0000" ) 
    {		
     	//최종결제요청 결과 성공 DB처리

     	      //기본값 설정
  		$step		= 2;
			$orderInputRequest = true;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false
			$vbankInputFlag = false;				//가상계좌 입금처리 
			$payno = $xpayLGD_OID;     
			$pgcode		= $xpayLGD_TID;			
			$escrow		= ($xpayLGD_ESCROWYN=="Y") ? 1 : 0;
			$pglog		= "$payno (".date('Y:m:d H:i:s').")거래번호 : ".$pgcode."승인금액 : ".$xpayLGD_AMOUNT;
			$PG_Account = $xpayLGD_AMOUNT;
	
			switch ($xpayLGD_PAYTYPE)
			{
				case "SC0010": //신용카드
					$pglog .= " / 결제방법 : 카드결제 - 승인번호 : " .$pgcode. ", 카드회사 : " .$xpayLGD_FINANCENAME;
					break;
				case "SC0030": //계좌이체		
					$pglog .= " / 결제방법 : 계좌이체 - ".$xpayLGD_FINANCENAME;
					break;
		
				case "SC0040": //가상계좌(무통장)
					$bankinfo	= $xpayLGD_FINANCENAME." / " .$xpayLGD_ACCOUNTNUM . " / ".$xpayLGD_AMOUNT;						
					$pglog .= " / 결제방법 : 가상계좌 - ".$bankinfo;
						
					if ($xpayLGD_CASFLAG=="R"){
						$step = 1;						
					}
					
					//무통장 입금 성공 
					if ($LGD_CASFLAG=="I"){
						$orderInputRequest = false;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false
						$vbankInputFlag = true;						
					}
					
					//무통장 입금취소 
					if ($LGD_CASFLAG=="C"){
						$step = -9;
						$pg_log = "가상계좌 입금 취소 기능 제공안됨.";       						
						//$page_return_url = "/order/payend.php?payno=".$payno;
						$page_return_url = "/mypage/index.php";
						
					}
		
					break;		
			}

			include "../pg_pay_seccess.php";
					
			if ($pgPayReturnMsg == "OK")			
			{
				$isDBOK = true; //DB처리 실패시 false로 변경해 주세요.
				$page_return_url = "/order/payend.php?payno=".$payno;
				
				$pg_log = "완료되었습니다.";
			}
			else
			{
				$pg_log = "오류 : 주문처리중 오류가 발생되었습니다.\r\n고객센터로 문의주세요.\r\n주문번호 [".$payno."]\r\n오류내용 : ".$pgPayReturnMsg;
				$isDBOK = false;
				$page_return_url = "/order/payfail.php?payno=".$payno;
			}

      //최종결제요청 결과 성공 DB처리 실패시 Rollback 처리    	
    	if( !$isDBOK ) 
    	{     		
     		$xpay->Rollback("상점 DB처리 실패로 인하여 Rollback 처리 [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");            		            		
      		
        //echo "TX Rollback Response_code = " . $xpay->Response_Code() . "<br>";
        //echo "TX Rollback Response_msg = " . $xpay->Response_Msg() . "<p>";
      		
        if( "0000" == $xpay->Response_Code() ) {
          	//echo "자동취소가 정상적으로 완료 되었습니다.<br>";
        }else{
    			$pg_log = "오류 : LG 결제는 정상적으로 처리되었으나 주문처리중 오류가 발생되었습니다.\r\n고객센터로 문의주세요.\r\n주문번호 [".$payno."]\r\n오류내용 : ".$pgPayReturnMsg;
       	}
    	}
    } else {
      	//최종결제요청 결과 실패 DB처리
     	//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
     	$xpayCode = $xpay->Response_Code();				
    	$xpayMsg = $xpay->Response_Msg();
		
     	
     	//최종결제요청 결과 실패 DB처리
    	$step = -1;					
			$pglog = "거래번호 : $xpayLGD_TID [" .$xpayCode. "/".$xpayMsg."]";		
			include "../pg_pay_fail.php";
			$page_return_url = "/order/payfail.php?payno=".$xpayLGD_OID;	
    }
	} else {
		$xpayCode = $xpay->Response_Code();				
    $xpayMsg = $xpay->Response_Msg();
				
    //2)API 요청실패 화면처리
    //echo "결제요청이 실패하였습니다.  <br>";
    //echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
    //echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
        
    //최종결제요청 결과 실패 DB처리
    //$step = -1;					
		//$pglog = "거래번호 : $pgcode [" .$xpayCode. "/".$xpayMsg."]";		
		//include "../pg_pay_fail.php";
		//$page_return_url = "/order/payend.php?payno=".$xpayLGD_OID;
		
		$pg_log = "오류 : 결제요청 실패  [" .$xpayCode. "/".$xpayMsg."]";		
	}
	
	echo smartXPayComplete($pg_log, $page_return_url);  

?>
