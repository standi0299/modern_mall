<?

include "../_header.php";
include "../lib/class.page.php";

chkMember();
$m_board = new M_board();
$m_order = new M_order();

$list_payno = $db->listArray("select a.payno from exm_pay a
		                      inner join exm_ord_item b on a.payno = b.payno
		                      where a.cid = '$cid' and a.mid ='$sess[mid]'
		                      and a.payno not in (select payno from exm_mycs where cid = '$cid' and mid ='$sess[mid]' and id = 'cs' and category = '9' and payno != '')
		                      and b.itemstep = '5' group by a.payno order by a.payno desc");

if ($_GET[no]) {
	$mode = "modCs";
	
	$addWhere = "where id = 'cs' and no = '$_GET[no]'";
	$data = $m_board->getMycsInfo($cid, $addWhere);
	$data[subject] = str_replace("\"", "&quot;", stripslashes($data[subject]));
	$data[content] = stripslashes($data[content]);
	
	if ($data[reply]) msg(_("이미 답변이 존재합니다."), -1);
	
	$selected[payno][$data[payno]] = "selected";
	
	$tpl->assign($data);
} else {
	$mode = "addCs";
}

$tpl->assign('list_payno',$list_payno);
$tpl->print_('tpl');

?>