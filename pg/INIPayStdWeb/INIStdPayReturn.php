<?
	include_once("../../lib/library.php");

	require_once($_SERVER["DOCUMENT_ROOT"].'/pg/INIPayStdWeb/libs/INIStdPayUtil.php');
  require_once($_SERVER["DOCUMENT_ROOT"].'/pg/INIPayStdWeb/libs/HttpClient.php');
  require_once($_SERVER["DOCUMENT_ROOT"].'/pg/INIPayStdWeb/libs/sha256.inc.php');
  require_once($_SERVER["DOCUMENT_ROOT"].'/pg/INIPayStdWeb/libs/json_lib.php');

	$util = new INIStdPayUtil();

	try {
            
		// 인증이 성공일 경우만
    if (strcmp("0000", $_REQUEST["resultCode"]) == 0) {
    	$mid 				= $_REQUEST["mid"];     						// 가맹점 ID 수신 받은 데이터로 설정
			//$signKey 			= "SU5JTElURV9UUklQTEVERVNfS0VZU1RS"; 			// 가맹점에 제공된 키(이니라이트키) (가맹점 수정후 고정) !!!절대!! 전문 데이터로 설정금지
			$signKey 			= $cfg[pg][inipay_sign_key];
						
			$timestamp 			= $util->getTimestamp();   						// util에 의해서 자동생성
			$charset 			= "UTF-8";        								// 리턴형식[UTF-8,EUC-KR](가맹점 수정후 고정)
			$format 			= "JSON";        								// 리턴형식[XML,JSON,NVP](가맹점 수정후 고정)
			$authToken 			= $_REQUEST["authToken"];   					// 취소 요청 tid에 따라서 유동적(가맹점 수정후 고정)
			$authUrl 			= $_REQUEST["authUrl"];    						// 승인요청 API url(수신 받은 값으로 설정, 임의 세팅 금지)
			$netCancel 			= $_REQUEST["netCancelUrl"];   					// 망취소 API url(수신 받은f값으로 설정, 임의 세팅 금지)
			$mKey 				= hash("sha256", $signKey);						// 가맹점 확인을 위한 signKey를 해시값으로 변경 (SHA-256방식 사용)
      
    	// 2.signature 생성
      $signParam["authToken"] = $authToken;  		// 필수
      $signParam["timestamp"] = $timestamp;  		// 필수
      // signature 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
      $signature = $util->makeSignature($signParam);


      // 3.API 요청 전문 생성
      $authMap["mid"] 		= $mid;   			// 필수
      $authMap["authToken"] 	= $authToken; 		// 필수
      $authMap["signature"] 	= $signature; 		// 필수
      $authMap["timestamp"] 	= $timestamp; 		// 필수
      $authMap["charset"] 	= $charset;  		// default=UTF-8
      $authMap["format"] 		= $format;  		// default=XML

      try {
				$httpUtil = new HttpClient();
				// 4.API 통신 시작
        
        $authResultString = "";
        if ($httpUtil->processHTTP($authUrl, $authMap)) {
          $authResultString = $httpUtil->body;
          //echo "<p><b>RESULT DATA :</b> $authResultString</p>";			//PRINT DATA
      	} else {
          //echo "Http Connect Error\n";
          //echo $httpUtil->errormsg;										
					//msg("/order/cart.php", "오류가 발생되었습니다.[Http Connect Error]");
          throw new Exception("Http Connect Error");
      	}

        //############################################################
        //5.API 통신결과 처리(***가맹점 개발수정***)
        //############################################################
        //echo "## 승인 API 결과 ##";
        
        $resultMap = json_decode($authResultString, true);
								
				### 로그저장
				ob_start();
				$fp = fopen("./log/" .$_REQUEST[orderNumber]."_start.log","a");
				print_r($_REQUEST);				
				$ret = ob_get_contents();
				fwrite($fp,$ret);
				fclose($fp);
				ob_end_clean();
				//chmod("./log/" .$_REQUEST[orderNumber], 0757);				
				//exit;	
								
        /*************************  결제보안 추가 2016-05-18 START ****************************/ 
        $secureMap["mid"]		= $mid;							//mid
        $secureMap["tstamp"]	= $timestamp;					//timestemp
        $secureMap["MOID"]		= $resultMap["MOID"];			//MOID
        $secureMap["TotPrice"]	= $resultMap["TotPrice"];		//TotPrice
        
        // signature 데이터 생성 
        $secureSignature = $util->makeSignatureAuth($secureMap);
        /*************************  결제보안 추가 2016-05-18 END ****************************/

				if ((strcmp("0000", $resultMap["resultCode"]) == 0) && (strcmp($secureSignature, $resultMap["authSignature"]) == 0) ){	//결제보안 추가 2016-05-18
				
				   /*****************************************************************************
			       * 여기에 가맹점 내부 DB에 결제 결과를 반영하는 관련 프로그램 코드를 구현한다.  
				   
					 [중요!] 승인내용에 이상이 없음을 확인한 뒤 가맹점 DB에 해당건이 정상처리 되었음을 반영함
							  처리중 에러 발생시 망취소를 한다.
			       ******************************************************************************/
			       
			    ### 로그저장
					ob_start();
					$fp = fopen("./log/" .$resultMap[MOID]."_success.log","a");					
					print_r($resultMap);
					$ret = ob_get_contents();
					fwrite($fp,$ret);
					fclose($fp);
					ob_end_clean();				
				
  
					$step		= 2;
					$orderInputRequest = true;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false
					$bankinfo = "";					
					if (isset($resultMap["payMethod"]) && strcmp("VBank", $resultMap["payMethod"]) == 0) { //가상계좌					
						$step		= 1;
						//$orderInputRequest = false;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false
						$bankinfo = $resultMap["vactBankName"]."/".$resultMap["VACT_Num"]."/".$resultMap["VACT_InputName"];
					}
					
					$payno = $resultMap["MOID"];
					$pgcode		= $resultMap["tid"];
					$namePayer	= $_REQUEST["buyername"];
					$escrow		= 0;					
					$PG_Account = $resultMap["TotPrice"];
					$pglog = "결제방법 : inpaystd / 승인번호 : " .$resultMap["tid"]. "(" .date('Y:m:d H:i:s'). ") / 금액 : " .$resultMap["TotPrice"]." / 결과코드 : " .$resultMap["resultCode"];
						
					include "../pg_pay_seccess.php";					
					
					if ($pgPayReturnMsg == "OK")
					{										
						go("/order/payend.php?payno=$payno");
					}
					else 
					{
						//결제 취소처리.
						$httpUtil->processHTTP($netCancel, $authMap);
						go("/order/payfail.php?payno=$payno");
					}
            
				} else {
					$step = -1;
					$payno = $resultMap["MOID"];
					$pglog = "거래번호 : $pgcode [" .$resultMap["resultCode"]. "/".$resultMap["resultMsg"];
					
					$fp = fopen("./log/" .$_REQUEST[orderNumber]."_auth_fail.log","a");
					fwrite($fp,$pglog);
					fclose($fp);
			
					include "../pg_pay_fail.php";					
					go("/order/payfail.php?payno=$payno");
       	}

      // 수신결과를 파싱후 resultCode가 "0000"이면 승인성공 이외 실패
      // 가맹점에서 스스로 파싱후 내부 DB 처리 후 화면에 결과 표시
      // payViewType을 popup으로 해서 결제를 하셨을 경우
      // 내부처리후 스크립트를 이용해 opener의 화면 전환처리를 하세요
      //throw new Exception("강제 Exception");
      } catch (Exception $e) {
        //    $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
        //####################################
        // 실패시 처리(***가맹점 개발수정***)
        //####################################
        //---- db 저장 실패시 등 예외처리----//
        $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
        //echo $s;
        
        $fp = fopen("./log/" .$_REQUEST[orderNumber]."_fail.log","a");
				fwrite($fp,$s);
				fclose($fp);
				

        //#####################
        // 망취소 API
        //#####################

        $netcancelResultString = ""; // 망취소 요청 API url(고정, 임의 세팅 금지)
        if ($httpUtil->processHTTP($netCancel, $authMap)) {
            $netcancelResultString = $httpUtil->body;
        } else {
            //echo "Http Connect Error\n";
            //echo $httpUtil->errormsg;

            throw new Exception("Http Connect Error");
        }

        //echo "<br/>## 망취소 API 결과 ##<br/>";
        
        /*##XML output##*/
        //$netcancelResultString = str_replace("<", "&lt;", $$netcancelResultString);
        //$netcancelResultString = str_replace(">", "&gt;", $$netcancelResultString);

        // 취소 결과 확인
        //echo "<p>". $netcancelResultString . "</p>";
				
				msg("오류가 발생되었습니다.[".$s."]", "/order/cart.php");
				
			}
		} else {

	    //#############
	    // 인증 실패시
	    //#############	    
	    //echo "####인증실패####";	
			//echo "<pre>" . var_dump($_REQUEST) . "</pre>";
						
			msg("오류가 발생되었습니다.[인증실패 - 0034]", "/order/cart.php");
		}
	} catch (Exception $e) {
  	$s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
    //echo $s;				
    $fp = fopen("./log/" .$_REQUEST[orderNumber]."_fail2.log","a");
		fwrite($fp,$s);
		fclose($fp);
				
		msg("오류가 발생되었습니다.[".$s."]", "/order/cart.php");
	}
?>
  
<script>
function goResult(url){
   location.replace(url);
}
//goResult("<?=$gourl?>");
</script>
