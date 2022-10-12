<?

include "../_header.php";
include "../lib/class.page.php";

$m_board = new M_board();

$data = $m_board->getMycsInfo($cid, "where cid = '$cid' and no = '$_GET[no]'");

//debug($data);

$file_list = $m_board->getMycsFileInfo($data[no]);
//debug($file_list);

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

$tpl->assign($data);
$tpl->assign('file_list',$file_list);
$tpl->assign('board_list',$board_id_list);
$tpl->print_('tpl');

?>
