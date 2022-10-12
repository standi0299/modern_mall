<? session_start();
	include_once "../../lib/library.php";
	
	
	//사용하지 않음			20161004		chunter
	
	// 세션비교
	$hash = md5($_SESSION['P_TID'].$cfg[pg][mid].$_SESSION['P_AMT']);
	
	//debug($_SESSION['P_TID'].$cfg[pg][mid].$_SESSION['P_AMT']);
	//debug($hash);
	//debug($_POST['P_HASH']);
	//exit;
	if($hash != $_POST['P_HASH'])
    msg('결제 정보가 일치하지 않습니다. 올바른 방법으로 이용해 주십시오.', '/order/cart.php');

	$tno             = $_SESSION['P_TID'];
	$amount          = $_SESSION['P_AMT'];


	$PGIP = $_SERVER['REMOTE_ADDR'];
	$PGIP = $_SERVER['HTTP_REFERER'];
  
  
	// 이니시스 NOTI 서버에서 받은 Value
	 $P_TID;                // 1 거래번호
	 $P_MID;                // 2 상점아이디
	 $P_AUTH_DT;            // 3 승인일자
	 $P_STATUS;            // 4 거래상태 (00:성공, 01:실패)
	 $P_TYPE;            // 5 지불수단
	 $P_OID;                // 6 상점주문번호
	 $P_FN_CD1;            // 7 금융사코드1
	 $P_FN_CD2;            // 8 금융사코드2
	 $P_FN_NM;            // 9 금융사명 (은행명, 카드사명, 이통사명)
	 $P_AMT;                // 10 거래금액
	 $P_UNAME;            // 11 결제고객성명
	 $P_RMESG1;            // 12 결과코드
	 $P_RMESG2;            // 13 결과메시지
	 $P_NOTI;            // 14 노티메시지(상점에서 올린 메시지)
	 $P_AUTH_NO;            // 15 승인번호   
	
	 $P_TID = $_REQUEST[P_TID];
	 $P_MID = $_REQUEST[P_MID];
	 $P_AUTH_DT = $_REQUEST[P_AUTH_DT];
	 $P_STATUS = $_REQUEST[P_STATUS];
	 $P_TYPE = $_REQUEST[P_TYPE];
	 $P_OID = $_REQUEST[P_OID];
	 $P_FN_CD1 = $_REQUEST[P_FN_CD1];
	 $P_FN_CD2 = $_REQUEST[P_FN_CD2];
	 $P_FN_NM = $_REQUEST[P_FN_NM];
	 $P_AMT = $_REQUEST[P_AMT];
	 $P_UNAME = $_REQUEST[P_UNAME];
	 $P_RMESG1 = $_REQUEST[P_RMESG1];
	 $P_RMESG2 = $_REQUEST[P_RMESG2];
	 $P_NOTI = $_REQUEST[P_NOTI];
	 $P_AUTH_NO = $_REQUEST[P_AUTH_NO];
	 
	 $P_CARD_INTEREST = $_REQUEST[P_CARD_INTEREST];			//무이자 할부여부  0 : 일반, 1 : 무이자
	 $P_RMESG2 = $_REQUEST[P_RMESG2];			//신용카드 할부 개월 수 
	 $P_VACT_NUM = $_REQUEST[P_VACT_NUM];		//입금할 계좌 번호  char(20)
	 $P_VACT_DATE = $_REQUEST[P_VACT_DATE];			//입금마감일자  char(8) : yyyymmdd 
	 $P_VACT_TIME = $_REQUEST[P_VACT_TIME];			//입금마감시간  char(6) hhmmss 
	 $P_VACT_NAME = $_REQUEST[P_VACT_NAME];			//계좌주명   
	 $P_VACT_BANK_CODE = $_REQUEST[P_VACT_BANK_CODE];			//은행코드  char(2) 
 	
 	$P_VACCT_NUM = $_REQUEST[P_VACCT_NUM];		//입금할 계좌 번호  char(20)
 	
 	$P_FN_NM = iconv("EUC-KR", "UTF-8", $P_FN_NM);
	$P_UNAME = iconv("EUC-KR", "UTF-8", $P_UNAME);
	$P_RMESG2 = iconv("EUC-KR", "UTF-8", $P_RMESG2);
	

	//기본값 설정
	$step		= 2;
	$orderInputRequest = true;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false 
	$vbankInputFlag = false;				//가상계좌 입금처리
	$payno = $P_OID;     
	$pgcode		= $P_TID;			//거래번호
	$namePayer	= $P_UNAME;
	$escrow		= ($LGD_ESCROWYN=="Y") ? 1 : 0;
	$pglog		= "$payno (".date('Y:m:d H:i:s').")거래번호 : ".$P_TID."승인금액 : ".$P_AMT;
	$PG_Account = $P_AMT;

	
	//WEB 방식의 경우 가상계좌 채번 결과 무시 처리
	//(APP 방식의 경우 해당 내용을 삭제 또는 주석 처리 하시기 바랍니다.)
	if($P_TYPE == "VBANK")    //결제수단이 가상계좌이며
	{
		//$bankinfo	= $P_FN_NM ." / ". $P_VACT_NUM. " / ".$P_AMT;
		$bankinfo	= $P_VACCT_NUM. " / ".$P_AMT;
		//$add_fields	.= ",bankinfo	= '$bankinfo'";
	
		$pglog .= "결제방법 : 가상계좌 - ".$bankinfo;
		        	
		if ($P_STATUS=="00" || $P_STATUS=="01"){
			$orderInputRequest = false;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false
			$step = 1;						
		}
		
		//무통장 입금 성공 
		if ($P_STATUS == "02"){
			$vbankInputFlag = true;				//가상계좌 입금처리
		}
	}

 $PageCall_time = date("H:i:s");

 $value = array(
 		"file_name" => "INIpayMobile_return.php",
         "PageCall time" => $PageCall_time,
         "P_TID"            => $P_TID,  
         "P_MID"     => $P_MID,  
         "P_AUTH_DT" => $P_AUTH_DT,      
         "P_STATUS"  => $P_STATUS,
         "P_TYPE"    => $P_TYPE,     
         "P_OID"     => $P_OID,  
         "P_FN_CD1"  => $P_FN_CD1,
         "P_FN_CD2"  => $P_FN_CD2,
         "P_FN_NM"   => $P_FN_NM,  
         "P_AMT"     => $P_AMT,  
         "P_UNAME"   => $P_UNAME,  
         "P_RMESG1"  => $P_RMESG1,  
         "P_RMESG2"  => $P_RMESG2,
         "P_NOTI"    => $P_NOTI,  
         "P_AUTH_NO" => $P_AUTH_NO,
         "P_VACT_NUM" => $P_VACT_NUM,
         "P_VACCT_NUM" => $P_VACCT_NUM,
				 
         );

  // 결제처리에 관한 로그 기록
  writeLog($value);

 /***********************************************************************************
  ' 위에서 상점 데이터베이스에 등록 성공유무에 따라서 성공시에는 "OK"를 이니시스로 실패시는 "FAIL" 을
  ' 리턴하셔야합니다. 아래 조건에 데이터베이스 성공시 받는 FLAG 변수를 넣으세요
  ' (주의) OK를 리턴하지 않으시면 이니시스 지불 서버는 "OK"를 수신할때까지 계속 재전송을 시도합니다
  ' 기타 다른 형태의 echo "" 는 하지 않으시기 바랍니다
 '***********************************************************************************/
	include "../pg_pay_seccess.php";
	
	$resultMSG = $pgPayReturnMsg;	
	if ($resultMSG == "OK")
		go('/order/payend.php?'.$payno);	
	else {
		echo "FAIL";
		go('/order/payfail.php?'.$payno);
	}					
   

 	function writeLog($msg)
	{
 		$path = "./log/";
		if (!is_dir($path)){
			mkdir($path,0757);
			chmod($path,0757);
		}
 		$file = "noti_input_".date("Ymd").".log";

    try{
	    if(!($fp = fopen($path.$file, "a+"))) return 0;
	                 
	    ob_start();
	    print_r($msg);
	    $ob_msg = ob_get_contents();
	    ob_clean();
	         
	    if(fwrite($fp, " ".$ob_msg."\n") === FALSE)
	    {
	    	fclose($fp);
				chmod($path.$file, 0757);
	      return 0;
	   	}
	    fclose($fp);
			chmod($path.$file, 0757);
	    return 1;
		} catch (Exception $e) {
			return 0;
		}
	}
 ?>
