<? session_start();
	include_once "../../lib/library.php";
	
	//REQUEST ************************************ 
	$P_STATUS = $_POST[P_STATUS]; 
	$P_REQ_URL = $_POST[P_REQ_URL]; 
	$P_TID = $_POST[P_TID]; 
	$P_MID = $_POST[P_MID]; 
	
	
	/*
	$P_CARD_INTEREST = $_REQUEST[P_CARD_INTEREST];			//무이자 할부여부  0 : 일반, 1 : 무이자
	$P_RMESG2 = $_REQUEST[P_RMESG2];			//신용카드 할부 개월 수 
	$P_VACT_NUM = $_REQUEST[P_VACT_NUM];		//입금할 계좌 번호  char(20)
	$P_VACT_DATE = $_REQUEST[P_VACT_DATE];			//입금마감일자  char(8) : yyyymmdd 
	$P_VACT_TIME = $_REQUEST[P_VACT_TIME];			//입금마감시간  char(6) hhmmss 
	$P_VACT_NAME = $_REQUEST[P_VACT_NAME];			//계좌주명   
	$P_VACT_BANK_CODE = $_REQUEST[P_VACT_BANK_CODE];			//은행코드  char(2) 
	*/			 
 	
	//$P_STATUS = "00";
	//$P_REQ_URL = "https://drmobile.inicis.com/smart/pay_req_url.php";
	//$P_TID = "INIMX_AUTHINIpayTest20160122111636071578"; 
		
	$page_return_url = "/pg/INIPayMobile/result_default.php";
	$page_return_url = "/order/payfail.php";
	
	
	function makeParam($P_TID, $P_MID)
	{   
		return "P_TID=".$P_TID."&P_MID=".$P_MID; 
	} 
	
	
	function socketPostSend($urlStr, $param)
	{
		$url = parse_url($urlStr);
		$fp = @fsockopen ("ssl://".$url[host], 443, $errno, $errstr, 2);
		if ($fp) 
    {
    	@fputs($fp,"POST $url[path] HTTP/1.1\r\n");
      @fputs($fp,"Host: $url[host]\r\n");
      //fputs($fp,"Referer: http://$fileHost$filePath\r\n");
      @fputs($fp,"User-Agent: ".$_SERVER["HTTP_USER_AGENT"]."\r\n"); 
      @fputs($fp,"Content-type: application/x-www-form-urlencoded\n");
      @fputs($fp,"Content-length: " .strlen($param)."\n");
			
			//@fputs($fp,"Accept: */*\r\n");
			//@fputs($fp,"Accept-Language: en-us,en;q=0.5\r\n");  
			//@fputs($fp,"Accept-Charset: ISO-8859-1, utf-8;q=0.66, *;q=0.66\r\n"); 
			
      @fputs($fp,"Connection: Close\r\n\r\n");
      @fputs($fp,"$param\r\n"); 
      @fputs($fp,"\r\n");
            
      while (!feof($fp)) $data = $data.fgets($fp,4096);
   	}
    fclose ($fp);
		
		$data = explode("\r\n\r\n", $data);
    array_shift($data);
    $data = implode("", $data);
		
		return $data;
		//debug($data);		
	}
		
	$pg_log = "";
	
	if($P_STATUS != "00")	
	{
		$pg_log = '오류 : '.iconv("euc-kr", "utf-8",$_REQUEST['P_RMESG1']).' 코드 : '.$_REQUEST['P_STATUS'];	
	} 
	else 
	{		
		$param = makeParam($P_TID, $cfg[pg][mid]);
		$return = socketPostSend($P_REQ_URL, $param);
		$return = iconv("EUC-KR", "UTF-8", $return);
//debug($return);			
    if(!$return)
		{
    	$pg_log = 'KG이니시스와 통신 오류로 결제등록 요청을 완료하지 못했습니다.\\n결제등록 요청을 다시 시도해 주십시오.';
   	}
   	
    // 결과를 배열로 변환
    parse_str($return, $ret);
    $PAY = array_map('trim', $ret);	
    $hash = md5($PAY['P_TID'].$cfg[pg][mid].$PAY['P_AMT']);
//debug($PAY);
		
    if($PAY['P_STATUS'] != '00')
		{
			$pg_log = '오류 : '.iconv("euc-kr", "utf-8",$PAY['P_RMESG1']).' 코드 : '.$PAY['P_STATUS'];
		} 

		$_SESSION['P_TID'] = $PAY['P_TID'];
  	$_SESSION['P_AMT'] = $PAY['P_AMT'];
  	$_SESSION['P_HASH'] = $hash;
			
		$exclude = array('res_cd', 'P_HASH', 'P_TYPE', 'P_AUTH_DT', 'P_AUTH_NO', 'P_HPP_CORP', 'P_APPL_NUM', 'P_VACT_NUM', 'P_VACT_NAME', 'P_VACT_BANK', 'P_CARD_ISSUER', 'P_UNAME');

		echo "<body onload=\"setPAYResult();\">".PHP_EOL;
		echo "<div id=\"show_progress\">".PHP_EOL;
		echo "<span style=\"display:block; text-align:center;margin-top:120px\"><img src='/js/jquery.tabs/loading.gif' alt=''></span>".PHP_EOL;
		echo "<span style='display:block; text-align:center;margin-top:10px; font-size:14px'>주문완료 중입니다. 잠시만 기다려 주십시오.</span>".PHP_EOL;
		echo "</div>".PHP_EOL;
	}	

	if ($pg_log)
	{
		//결제 실패 처리...				20160921		chunter
		$step = -1;
		$payno = $PAY[P_OID];     
		$pglog = $pg_log . " / 거래번호 : " .$PAY[P_TID];			
		include "../pg_pay_fail.php";
		
		$page_return_url="/order/payfail.php?payno=".$PAY[P_OID];
	} else {
		$page_return_url="/order/payend.php?payno=".$PAY[P_OID];
  }
	
	echo "<script>window.close();";
	echo "opener.parent.location.href='".$page_return_url."';</script>";		
		
?>