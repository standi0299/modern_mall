<?	
	list($orderPayStep, $orderPayMethod) = $db->fetch("select paystep, paymethod from exm_pay where payno	='$payno'",1);
	//KCP에서 중복 주문으로 넘어오는 경우가 있다. 기 결제 처리건인지 체크한다.
	//가상계좌 입금신청간일경우도 처리하지 않는다			20171205		chunter
	$bFailLogFlagProc = true;
	if ($orderPayStep == "2") $bFailLogFlagProc = false;
	if ($orderPayMethod == "v" && $orderPayStep !== "0") $bFailLogFlagProc = false;
	
	if ($bFailLogFlagProc != "2")			
	{
		$db->query("update exm_pay set escrow	= '$escrow', pglog	= '$pglog', paystep	= '$step' where payno	='$payno' and paydt is null");
		$db->query("update exm_ord_item set itemstep = '$step' where payno='$payno'");
	
		$query = "select * from exm_ord_item where payno = '$payno'";
		$res = $db->query($query);
		while ($tmp = $db->fetch($res))
		{
			if ($tmp[coupon_areservesetno]){
				$db->query("
				update exm_coupon_set set
					coupon_use		= 0,
					payno			= null,
					ordno			= null,
					ordseq			= null,
					coupon_usedt	= null
				where no = '$tmp[coupon_areservesetno]'
				");
			}
	
			if ($tmp[dc_couponsetno]){
				$db->query("
				update exm_coupon_set set
					coupon_use		= 0,
					payno			= null,
					ordno			= null,
					ordseq			= null,
					coupon_usedt	= null
				where no = '$tmp[dc_couponsetno]'
				");
			}
		}
	}
?>