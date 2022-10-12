<?

$login_offset = true;
include "../_header.php";

$m_member = new M_member();

$data = $m_member->getInfo($cid, $_GET[mid]);

if ($sess[mid])	go("../main/index.php");

//포토큐브 네이버프리미엄로그분석용.
$acecounter_mode = "register";

$tpl->assign($data);
$tpl->print_('tpl');

?>