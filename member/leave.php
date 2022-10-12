<?

include "../_header.php"; 

chkMember();
$m_member = new M_member();

$data = $m_member->getInfo($cid, $sess[mid]);

$tpl->assign($data);
$tpl->print_('tpl');

?>