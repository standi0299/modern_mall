<?

include "../_header.php";
include "../lib/class.page.php";

chkMember();
$m_board = new M_board();
$m_order = new M_order();

$cfg[cstime] = getCfg('cstime');
$cfg[cstime] = str_replace("\n", "<br>", $cfg[cstime]);

$payno = $m_order->getMycsPaynoList($cid, $sess[mid]);

if ($_GET[no]) {
	$mode = "modCs";

	$addWhere = "where id = 'cs' and no = '$_GET[no]'";
	$data = $m_board->getMycsInfo($cid, $addWhere);
	$data[subject] = str_replace("\"", "&quot;", stripslashes($data[subject]));
	$data[content] = stripslashes($data[content]);

	if ($data[reply]) msg(_("이미 답변이 존재합니다."), -1);

	$selected[category][$data[category]] = "selected";
	$selected[payno][$data[payno]] = "selected";

	$tpl->assign($data);
} else {
	$mode = "addCs";
}

// 모던 기본테마 게시판 리스트 추가 210524 jtkim
$board_id_list = array();
if(!$cfg['skin_theme']){
	$m_board = new M_board();
	$board_list = $m_board->getBoardSetList($cid);
	$board_default_list = array(
		'notice',
		'bigorder',
		'qna',
		'gallery'
	);
	forEach($board_list as $k){
		if(!in_array($k['board_id'],$board_default_list)) array_push($board_id_list, array("id"=>$k['board_id'], "name"=>$k['board_name']));
	}
}


$tpl->assign('payno',$payno);
$tpl->assign('board_list',$board_id_list);
$tpl->print_('tpl');

?>
