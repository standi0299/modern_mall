<? session_start();
	include_once(dirname(__FILE__).'/../../lib/library.php');

	$_COOKIE[kakaopay_payno] = "";
	msg("결제를 취소하셨습니다.", "close");
?>