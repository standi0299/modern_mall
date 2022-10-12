<? session_start();
	include_once(dirname(__FILE__).'/../../lib/library.php');
	include_once(dirname(__FILE__).'/kakaopay.inc.php');
	//debug($_COOKIE);
	//debug($_SESSION);
	$payno = $_COOKIE[kakaopay_payno];	
	$pg_token = $_REQUEST[pg_token];
	//$sql = "select * from exm_pay where payno='$payno'";
	$m_order = new M_order();
	$payData = $m_order->getPayInfo($payno);
	if (!$payData)
	{
		msg("주문코드가 없습니다.", "close");
	}		
		
	$kReturn = kakaoPayRequest($payData[pgcode], $payData[payno], $payData[mid], $pg_token, "", $payData[payprice]);
	$_COOKIE[kakaopay_payno] = "";
	
	if ($kReturn['http_code'] == "200")
	{		
		$payResult = json_decode($kReturn['body'], true);
		
		$step		= 2;
		$orderInputRequest = true;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false
		$bankinfo = "";					
		
		$payno = $payData[payno];
		$pgcode		= $payData[pgcode];						
		$PG_Account = $payData[payprice];
		$pglog = "결제방법 : kakaopay / aid : " .$payResult["aid"]. "(" .date('Y:m:d H:i:s'). ") / 금액 : " .$payData[payprice];						
				 
		include_once "../pg_pay_seccess.php";		
		
		if ($pgPayReturnMsg == "OK")
		{
			popupMsgNLocation("결제가 완료되었습니다.", "/order/payend.php?payno={$payData[payno]}");
		} else {
			popupMsgNLocation("결제가 실패되었습니다.", "/order/payfail.php?payno={$payData[payno]}");
		}
/*
Content-type: application/json;charset=UTF-8
{
 "aid": "A5678901234567890123",
 "tid": "T1234567890123456789",
 "cid": "TC0ONETIME",
 "partner_order_id": "partner_order_id",
 "partner_user_id": "partner_user_id",
 "payment_method_type": "MONEY",
 "item_name": "초코파이",
 "quantity": 1,
 "amount": {
  "total": 2200,
  "tax_free": 0,
  "vat": 200,
  "point": 0
 },
 "created_at": "2016-11-15T21:18:22",
 "approved_at": "2016-11-15T21:20:47"
}
*/

		
	} else {		
		$payResult = json_decode($kReturn['body'], true);
		
		//$payResult[extras][method_result_code]
		//$payResult[extras][method_result_message]		

		ob_start();
		$fp = fopen("log/kpay_$pg_token","w");
		print_r($payResult);
		$ret = ob_get_contents();
		fwrite($fp,$ret);
		fclose($fp);
		ob_end_clean();
  	
		popupMsgNLocation("결제가 실패되었습니다.", "/order/payfail.php?payno={$payData[payno]}");
	}
	
	


	
	
	
?>