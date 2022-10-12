<?

include "../_header.php";
include "../lib/class.page.php";

# 분류 추출
$query = "select distinct(catnm) from exm_faq where cid = '$cid'";
$res = $db->query($query);
$r_faq_catnm = array();
while ($data = $db->fetch($res)){
	$r_faq_catnm[$data[catnm]] = $data[catnm];
}

if ($_POST[sword]) $where[] = "concat(q,a) like '%$_POST[sword]%'";
if ($_POST[catnm]) $where[] = "catnm = '$_POST[catnm]'";

$db_table = "exm_faq";
$where[] = "cid = '$cid'";

//20150723 / minks / 모바일에서 사용
if ($_POST[mobile_type] == "Y") {
	foreach ($where as $k => $v) {
		if ($v) $where_v .= $v." and ";
	}

	list($faq_cnt) = $db->fetch("select count(*) from ".$db_table." where ".substr($where_v,0,-4),1);
}

$pg = new Page($_GET[page],$faq_cnt);
$pg->setQuery($db_table,$where);
$pg->exec();

$res = &$pg->resource;
while ($data = $db->fetch($res)){
	$loop[] = $data;
}

if ($_POST[catnm]) {
	$selected['catnm'][$_POST[catnm]] = "selected";
	$checked['catnm'][$_POST[catnm]] = "checked";
}
else {
	$selected['catnm'][_('전체')] = "selected";
	$checked['catnm'][_('전체')] = "checked";
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

$tpl->assign("pg",$pg);
$tpl->assign("loop",$loop);
$tpl->assign("faq_catnm",$r_faq_catnm);
$tpl->assign("faq_selected",$selected);
$tpl->assign("faq_checked",$checked);
$tpl->assign("faq_seach",$_POST[sword]);
$tpl->assign('board_list',$board_id_list);
$tpl->print_('tpl');

?>
