<?

include "../_header.php";

if (!$_GET[category]) $_GET[category] = "quotation";
$checked[category][$_GET[category]] = "checked";

//이용약관 : agreement
//개인정보수집동의 : policy1
$cfg[agreement] = getCfg('agreement');
$cfg[policy] = getCfg('policy');

if ($_GET[category] == "marketing") $tpl->define('tpl', 'mypage/request_marketing.htm');


$tpl->print_('tpl');

?>