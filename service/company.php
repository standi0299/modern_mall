<?
include "../_header.php";

$cfg[company] = getCfg('company');
//$company = nl2br($cfg[company]);
$company = $cfg[company];

$tpl -> assign('company', $company);
$tpl -> print_('tpl');
?>