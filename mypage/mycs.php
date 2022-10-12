<?

include "../_header.php";
include "../lib/class.page.php";

chkMember();

$cfg[cstime] = getCfg('cstime');
$cfg[cstime] = str_replace("\n", "<br>", $cfg[cstime]);

$db_table = "exm_mycs";
$where[] = "cid = '$cid' and mid = '$sess[mid]' and id = 'cs' and category != '9'"; //20190107 / minks / 문의유형이 캐시백인 경우 제외

//20160530 / minks / 모바일에서 사용
if ($_GET[mobile_type] == "Y") {
	foreach ($where as $k => $v) {
		if ($v) $where_v .= $v." and ";
	}

	list($mycs_cnt) = $db->fetch("select count(*) from ".$db_table." where ".substr($where_v, 0, -4), 1);
}

$pg = new Page($_GET[page], $mycs_cnt);
$pg->setQuery($db_table, $where, "regdt desc");
$pg->exec();

$res = &$pg->resource;

while ($data = $db->fetch($res)) {
	$data[category] = $r_cs_category[$data[category]];
	$data[subject] = stripslashes($data[subject]);
	$data[content] = stripslashes($data[content]);
	$loop[] = $data;
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

$tpl->assign('pg',$pg);
$tpl->assign('loop',$loop);
$tpl->assign('cs_category',$r_cs_category);
$tpl->assign('board_list',$board_id_list);
$tpl->print_('tpl');

?>
