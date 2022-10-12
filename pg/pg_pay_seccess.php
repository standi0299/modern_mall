<?
/*
* @date : 20180620
* @author : chunter
* @brief : pods10 연동안 삭제. pods20 도메인을 변수로 변경
* @desc : 일본 데파초 사이트 처리가 느린현상으로 발견.
*/
?>
<?
### 중복결제체크
$paydata	= $db->fetch("select * from exm_pay where payno='$payno'");

if ($paydata[payno] && !$paydata[paydt])
{
	$bDbProc = true;
	$bDbProcMsg = "";
  if ($PG_Account < 1)  {
		$bDbProc = false;                  // 결제 금액이 1보다 작은 경우 "false" 로 세팅      20150729    chunter
		$bDbProcMsg = "결제 금액이 1보다 작음";
	}

	if ($PG_Account != $paydata[payprice]) {
		$bDbProc = false;    // 결제 금액과 db 입력된 결제 금액이 다른경우 "false" 로 세팅      20150729    chunter
		$bDbProcMsg = "결제 금액이 다름";
	}

	//2019.11.14 kkwon / 가상계좌 처리시 불필요한 작업이라 주석 처리 함, 주문단에서 이미 처리를 다하기 때문에 입금완료 처리만 하면 됨
	/*if ($paydata[dc_emoney] > 0) {
		$result = setPayNUseEmoney($paydata[cid], $paydata[mid], $paydata[dc_emoney], $paydata[salePrice], '', true);
		if($result['code'] != "00") {
			$bDbProc = false;
			$bDbProcMsg = $result['msg'];
		}
	}*/

	if ($bDbProc)
	{
		if ($step==2) $add_fields .= ",paydt = now()";

    ### 실데이타 저장
		if ($pglog) $add_fields .= " ,pglog = '$pglog'";			//로그가 있을 경우만 update 처리			20160112		chunter
		if ($bankinfo) $add_fields .= " ,bankinfo = '$bankinfo'";			//가상계좌 정보가 있을 경우만 update 처리			20160913		chunter

		//$query = "update exm_pay set escrow = '$escrow', pglog = '$pglog', pgcode = '$pgcode', paystep = '$step' 	$add_fields where payno		= '$payno'";

		if ($vbankInputFlag)
			$query = "update exm_pay set paystep = '$step' 	$add_fields where payno		= '$payno'";
		else
			$query = "update exm_pay set escrow = '$escrow', pgcode = '$pgcode', paystep = '$step' 	$add_fields where payno		= '$payno'";
		$db->query($query);

		$paydata = $db->fetch("select * from exm_pay where payno='$payno'");  //2020.11.23 kkwon 가상계좌인 경우 위해 추가

		$db->query("update exm_ord_item set itemstep = '$step' where payno	=' $payno'");

		$query = "select * from exm_ord_item where payno = '$payno'";
		$ordItemData = $db->listArray($query);

		if ($step == 1)
		{
			$m_goods = new M_goods();

			foreach ($ordItemData as $key => $tmp) 
			{
				set_stock($tmp[goodsno],$tmp[optno],$tmp[ea]*-1);
				if ($tmp[storageid]){
					$g_data = $m_goods->getInfo($tmp[goodsno]);
					if (in_array($g_data[podskind], $r_podskind20) || $g_data[pods_use] == "3") {/* 2.0 상품 */
						$ret = readUrlWithcurl("http://" .PODS20_DOMAIN."/CommonRef/StationWebService/UpdateStorageDate.aspx?storageid=" . $tmp[storageid]);
					}
				}
			}
		}

		if ($step == 2)
		{
			foreach ($ordItemData as $key => $tmp) 
			{
				set_pod_pay($tmp[payno],$tmp[ordno],$tmp[ordseq]);
				//set_acc_desc($tmp[payno],$tmp[ordno],$tmp[ordseq],2);
				if (!$vbankInputFlag)
   				$db->query("delete from exm_cart where cartno = '$tmp[cartno]'");
				set_stock($tmp[goodsno],$tmp[optno],$tmp[ea]*-1);
   		}
			order_sms($payno);
		}

		### 적립금 소진		//가상계좌는 신청 시검에 소진 처리
 		if ($orderInputRequest) {
   		if ($paydata[dc_emoney] > 0) {
      	//set_emoney($paydata[mid],"상품구입시 사용",-$paydata[dc_emoney],$payno);
				//setAddEmoney($cid, $paydata[mid], $paydata[dc_emoney], "상품구입시 사용", $sess_admin[mid], $payno);
      	setPayNUseEmoney($paydata[cid], $paydata[mid], $paydata[dc_emoney], $paydata[salePrice], $paydata[payno], false);
   		}
		}

		//가상계좌 입금 처리가 아닌경우만 처리.
		if (!$vbankInputFlag)
		{
			foreach ($ordItemData as $key => $tmp) 
			{
				$paydata[item][] = $tmp;

				if ($orderInputRequest) 
				{
					if ($tmp[coupon_areservesetno])
					{
						$db->query("
							update exm_coupon_set set
								coupon_use		= 1,
							  payno			= $payno,
							  ordno			= $tmp[ordno],
							  ordseq			= $tmp[ordseq],
							  coupon_usedt	= now()
							where no = '$tmp[coupon_areservesetno]'
    				");
 					}

					if ($tmp[dc_couponsetno])
					{
						$db->query("
						update exm_coupon_set set
							coupon_use		= 1,
						  payno			= $payno,
						  ordno			= $tmp[ordno],
						  ordseq			= $tmp[ordseq],
						  coupon_usedt	= now()
						where no = '$tmp[dc_couponsetno]'
						");
					}
				}
			}
		}
		
		//20190118 / minks / 배송방법 추가
		list($ordershiptype) = $db->fetch("select order_shiptype from exm_ord where payno='$payno' order by ordno limit 1", 1);
		$paydata[ordershiptype] = ($cfg[skin_theme] == "P1" && $ordershiptype == 1) ? $r_order_shiptype[0] : $r_order_shiptype[$ordershiptype];
  
		//현금영수증 신청 시
		if($cash_authno)
		{
			$m_cash = new M_cash_receipt();
			$m_cash->setInsert($cid, $mid, $payno, $receipt_money, $status, $status_log);
			$m_cash->setOrderReceiptComplete($cid, $payno, $cash_authno);
		}

		if ($step==2)
		{
			autoMail("payment",$paydata[orderer_email],$paydata);

			//관리자에게 보내기
			autoMailAdmin("admin_payment",$cfg[email1],$paydata);
			autoSms("입금확인",$paydata[orderer_mobile],$paydata);
		} else {
			autoMail("order",$paydata[orderer_email],$paydata);

			//관리자에게 보내기
			autoMailAdmin("admin_order",$cfg[email1],$data);
			if($paydata[paymethod] == "v") {  //kkwon 2020.11.23 가상계좌인 경우 입금계좌 발송 처리
				autoSms("접수내역", $paydata[orderer_mobile], $paydata);
			}else {
				autoSms("주문접수",$paydata[orderer_mobile],$paydata);
			}
		}

		//if( 결제성공 상점처리결과 성공 ) 
		$pgPayReturnMsg = "OK";
	} else {
		$pgPayReturnMsg = "CA 주문 DB 처리중 오류 발생!![".$bDbProcMsg."]";
	}
} else {
	$pgPayReturnMsg = "CB 중복 주문번호로 결제한 이력이 존재함.";
}
?>