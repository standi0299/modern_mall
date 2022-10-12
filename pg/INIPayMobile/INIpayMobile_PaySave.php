<? session_start();
include_once "../../lib/library.php";
include_once "./INIPayUtil.php";

//REQUEST ************************************
$P_STATUS = $_POST[P_STATUS];
$P_REQ_URL = $_POST[P_REQ_URL];
$P_TID = $_POST[P_TID];
$P_MID = $_POST[P_MID];
$P_RMESG1 = $_POST[P_RMESG1];
$P_NOTI = $_POST['P_NOTI'];

/*
 $P_CARD_INTEREST = $_REQUEST[P_CARD_INTEREST];			//무이자 할부여부  0 : 일반, 1 : 무이자
 $P_RMESG2 = $_REQUEST[P_RMESG2];			//신용카드 할부 개월 수
 $P_VACT_NUM = $_REQUEST[P_VACT_NUM];		//입금할 계좌 번호  char(20)
 $P_VACT_DATE = $_REQUEST[P_VACT_DATE];			//입금마감일자  char(8) : yyyymmdd
 $P_VACT_TIME = $_REQUEST[P_VACT_TIME];			//입금마감시간  char(6) hhmmss
 $P_VACT_NAME = $_REQUEST[P_VACT_NAME];			//계좌주명
 $P_VACT_BANK_CODE = $_REQUEST[P_VACT_BANK_CODE];			//은행코드  char(2)
 */

$value = array("file_name" => "pay_save.php", "P_AUTH_DT" => $P_NOTI, "P_STATUS" => $P_STATUS, "P_REQ_URL" => $P_REQ_URL, "P_TID" => $P_TID, "P_MID" => $P_MID, "P_NOTI" => $P_NOTI, "P_RMESG1" => $P_RMESG1, "P_POST" => json_encode($_POST), );
// 결제처리에 관한 로그 기록
INIPayWriteLog($value, "_paysave_start");

$pg_log = "";
$page_return_url = "/order/cart.php";

if ($P_STATUS != "00") {
	$P_RMESG1 = iconv("EUC-KR", "UTF-8", $P_RMESG1);
	$pg_log = '오류 : ' . $P_RMESG1 . ', 코드 : ' . $P_STATUS;
} else {
	//$param = makeParam($P_TID, $cfg[pg][mid]);
	//$return = socketPostSend($P_REQ_URL, $param);
	//if($_SERVER['SERVER_ADDR'] == "210.220.150.35"){
		if($_SERVER['SERVER_ADDR'] == "115.68.51.154"){
		//미오디오만
		//SERVER_ADDR 맞는지 확인 필요 / 19.07.26 / kjm
		$post_data = "P_MID={$cfg[pg][mid]}&P_TID={$P_TID}";
		$cmd = "curl -d '{$post_data}' {$P_REQ_URL}";
		$return = shell_exec($cmd);
	} else {
		//미오디오 이외의 사이트에서는 이쪽 함수를 탄다.
		$post_data = array('P_MID' => $cfg[pg][mid], 'P_TID' => $P_TID);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $P_REQ_URL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$return = curl_exec($ch);
	}
	
	$return = iconv("EUC-KR", "UTF-8", $return);
	//debug($return);
	if (!$return) {
		$pg_log = 'KG이니시스와 통신 오류로 결제등록 요청을 완료하지 못했습니다.\\n결제등록 요청을 다시 시도해 주십시오.';
	} else {

		// 결과를 배열로 변환
		parse_str($return, $ret);
		$PAY = array_map('trim', $ret);
		//$PAY = array_map('strip_tags', $PAY);
		//$PAY = array_map('get_search_string', $PAY);
		//debug($PAY);

		if ($PAY['P_STATUS'] != '00') {
			$pg_log = '오류 : ' . $PAY['P_RMESG1'] . ', 코드 : ' . $PAY['P_STATUS'];

			//실패 오류를 남기자...
			$value = array("file_name" => "INIpayMobile_PaySave_statusfail.php", "P_REQ_URL" => $P_REQ_URL . '?P_MID=' . $cfg[pg][mid] . '&P_TID' . $P_TID, "P_TID" => $P_TID, "P_LOG" => $pg_log, "P_RET" => $return, "post_data" => json_encode($post_data), );
			INIPayWriteLog($value, "_statusfail");

			//자동 취소 처리			//구현은 하였으나 테스트 부족 및 운영방안 미 확정으로 주석처리			20161006		chunter
			$iniPay_tid = $P_TID;
			//이니시스 거래번호
			$iniPay_cancel_msg = "P_STATUS 결과값 없음";
			//취소사유
			include_once "./INIpayMobile_Cancel.php";
		} else {
			$P_AMT = $PAY[P_AMT];

			//기본값 설정
			$step = 2;
			$orderInputRequest = true;
			//주문처리 신청 여부,
			$vbankInputFlag = false;
			//가상계좌 입금처리
			$payno = $PAY[P_OID];
			$pgcode = $PAY[P_TID];
			//거래번호
			$namePayer = $PAY[P_UNAME];
			$escrow = 0;
			$pglog = "$payno (" . date('Y:m:d H:i:s') . ")거래번호 : " . $P_TID . ",승인금액 : " . $P_AMT;
			$PG_Account = $P_AMT;

			if ($PAY[P_TYPE] == "CARD")//카드
			{
				$pglog .= " / 결제방법 : 카드결제 - 승인번호 : " . $PAY[P_AUTH_NO] . ", 카드회사 : " . $PAY[P_FN_NM];
			}

			//WEB 방식의 경우 가상계좌 채번 결과 무시 처리
			//(APP 방식의 경우 해당 내용을 삭제 또는 주석 처리 하시기 바랍니다.)
			if ($PAY[P_TYPE] == "VBANK")//결제수단이 가상계좌이며
			{
				$step = 1;
				$bankinfo = $r_inicis_bank_name[$PAY[P_VACT_BANK_CODE]] . " / " . $PAY[P_VACT_NUM] . " / " . $P_AMT . "원 / " . $PAY[P_VACT_NAME];
				//$add_fields	.= ",bankinfo	= '$bankinfo'";

				$pglog .= "결제방법 : 가상계좌 - " . $bankinfo;
			}

			$PageCall_time = date("H:i:s");

			$value = array("file_name" => "INIpayMobile_PaySave.php", "PageCall time" => $PageCall_time, "P_TID" => $PAY[P_TID], "P_MID" => $PAY[P_MID], "P_AUTH_DT" => $PAY[P_AUTH_DT], "P_STATUS" => $PAY[P_STATUS], "P_TYPE" => $PAY[P_TYPE], "P_OID" => $PAY[P_OID], "P_FN_CD1" => $PAY[P_FN_CD1], "P_FN_CD2" => $PAY[P_FN_CD2], "P_FN_NM" => $PAY[P_FN_NM], "P_AMT" => $PAY[P_AMT], "P_UNAME" => $PAY[P_UNAME], "P_RMESG1" => $PAY[P_RMESG1], "P_RMESG2" => $PAY[P_RMESG2], "P_NOTI" => $PAY[P_NOTI], "P_AUTH_NO" => $PAY[P_AUTH_NO], "P_VACT_NUM" => $PAY[P_VACT_NUM]);

			// 결제처리에 관한 로그 기록
			INIPayWriteLog($value, "_PaySave");

			include "../pg_pay_seccess.php";
			if ($pgPayReturnMsg == "OK") {
				$page_return_url = "/order/payend.php?payno=" . $payno;
			} else {
				$step = -1;
				$pglog = "거래번호 : $pgcode [" . $PAY[P_RMESG1] . "/" . $PAY[P_RMESG2] . "]";
				//include "../pg_pay_fail.php";

				//실패 오류를 남기자...
				$value = array("file_name" => "INIpayMobile_PaySave.php", "P_TID" => $PAY[P_TID], "P_MID" => $PAY[P_MID], "P_LOG" => $pglog, "MSG" => $pgPayReturnMsg);
				INIPayWriteLog($value, "_paysavefail");

				//자동 취소 처리			//구현은 하였으나 테스트 부족 및 운영방안 미 확정으로 주석처리			20161006		chunter
				$iniPay_tid = $PAY[P_TID];
				//이니시스 거래번호
				$iniPay_cancel_msg = $pgPayReturnMsg;
				//취소사유
				//include_once "./INIpayMobile_Cancel.php";

				$pg_log = "오류 : 이니시스 결제는 정상적으로 처리되었으나 주문처리중 오류가 발생되었습니다.\r\n고객센터로 문의주세요.\r\n주문번호 [" . $payno . "]\r\n오류내용 : " . $pgPayReturnMsg;
				$page_return_url = "/order/payfail.php?payno=" . $payno;
			}
		}
	}
}

echo inipayComplete($pg_log, $page_return_url);
?>

