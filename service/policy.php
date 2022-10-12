<?
include "../_header.php";

$cfg[policy] = getCfg('policy');
$cfg[agreement2] = getCfg('agreement2');
$cfg[agreement_thirdparty] = getCfg('agreement_thirdparty');
$cfg[nonmember_agreement] = getCfg('nonmember_agreement');
$cfg[privacy_agreement] = getCfg('privacy_agreement');

$policy = $cfg[policy];

$tpl->assign('policy',$policy);
$tpl->print_('tpl');
?>