<?

include "../_header.php";

chkMember();
$m_member = new M_member();
$m_order = new M_order();

if (!$_GET[document_type]) $_GET[document_type] = "CRD";
$checked[document_type][$_GET[document_type]] = "checked";

// 이미 신청한 현금영수증,세금계산서 payno의 경우 가져오지 않음 210419 jtkim
$documentListWhere = " and a.mid = '$sess[mid]'";
$documentList = $m_order->getDocumentList($cid,$documentListWhere);

$notInWhere = "";
if(count($documentList) > 0){
	$documentPayno = "";
	forEach($documentList as $k => $v){
		if($k != 0) $documentPayno .= ",";
		$documentPayno .= "'$v[payno]'";
	}
	$notInWhere = "and payno not in ($documentPayno)";
}

$list_payno = $m_order->getDocumentPayno($cid, $sess[mid], $notInWhere);

$tpl->assign('list_payno', $list_payno);
$tpl->print_('tpl');

?>
