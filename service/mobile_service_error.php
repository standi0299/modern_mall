<?

include "../_header.php";

if (!$_GET[code]) $_GET[code] = "101";

if ($_GET[code] == "101") {
	$message[msg_title] = _("운영중지");
	$message[msg1] = _("운영이 중지된 사이트입니다.");
	$message[msg2] = _("고객센터에 문의해 주세요.");
} else if ($_GET[code] == "201") {
	$message[msg_title] = "Service Error";
	$message[msg1] = _("등록되지 않은 도메인 입니다.");
	$message[msg2] = _("정확한 도메인 주소를 확인해 주세요.");
} else {
	$message[msg_title] = _("서비스 오류");
	$message[msg1] = _("서비스중 오류가 발견되었습니다.");
	$message[msg2] = _("고객센터에 문의해 주세요.");
}

$message[code] = $_GET[code];

$tpl->assign('message', $message);
$tpl->print_('tpl');

?>