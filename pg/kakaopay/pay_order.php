<? session_start();
	//include_once(dirname(__FILE__).'/../../lib/library.php');
	include_once(dirname(__FILE__).'/kakaopay.inc.php');
	
	$_COOKIE[kakaopay_payno] = "";
	$payno = $_POST[payno];	
	$m_order = new M_order();
	$payData = $m_order->getPayInfo($payno);
	
	if (!$payData)
	{
		$response[code] = "400";
		$response[err_code] = "404";
		$response[err_msg] = "주문코드가 없습니다.";
	} else {
		
		$kReturn = kakaoPayReady($payno, $payData[mid], $goods_name, $qty, $payData[payprice]);
		//debug($kReturn);
		
		if ($kReturn['http_code'] == "200")
		{			
			$payResult = json_decode($kReturn['body'], true);			
			//$sql ="update exm_pay set pgcode='{$payResult[tid]}' where payno='$payno'";		
			$m_order->setPGCodeUpdate($payno, $payResult[tid]);
			
			
			$response[code] = "200";
			//$response[redirect_url] = $payResult[next_redirect_pc_url] ."?pg_token=$payno";
			$response[redirect_url] = $payResult[next_redirect_pc_url];			
			setcookie("kakaopay_payno", $payno, time()+3600, "/");  /* expire in 1 hour */			
			$_COOKIE["kakaopay_payno"] = $payno;
			//$_SESSION["kakaopay_payno"] = $payno;
			//debug($_COOKIE);
			//debug($_SESSION);
			
		} else {
			
			$payResult = json_decode($kReturn['body'], true);		
			//$payResult[extras][method_result_code]
			//$payResult[extras][method_result_message]
			//전달받는 데이터 로그 / 15.11.03 / kjm
	
			ob_start();
			$fp = fopen("log/".TODAY_YMD."_Kpay","w");
			print_r($payResult);
			$ret = ob_get_contents();
			fwrite($fp,$ret);
			fclose($fp);
			ob_end_clean();
	  	
			$response[code] = "400";
			$response[err_code] = $payResult[code];
			$response[err_msg] = $payResult[msg];
			
		}
	}
	
	//echo json_encode($response);
	
?>