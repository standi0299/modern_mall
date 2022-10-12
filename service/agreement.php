<?
include "../_header.php";

$cfg[agreement] = getCfg('agreement');
//$agreement = nl2br($cfg[agreement]);
$agreement = $cfg[agreement];

$tpl->assign('agreement',$agreement);
$tpl->print_('tpl');
?>