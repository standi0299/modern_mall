<?

include "../_header.php";
include "../lib/class.page.php";

$m_board = new M_board();
$m_member = new M_member();

$board = $m_board->getBoardSetInfo($cid, $_GET[board_id]);

// 모던 기본테마 게시판 리스트 추가 210524 jtkim
$board_id_list = array();
if(!$cfg['skin_theme']){
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

if (!$board[board_id]) {
    msg(_("존재하지 않는 게시판입니다."), -1);
    exit;
}

if ($sess[mid]) {
	$member = $m_member->getInfoWithGrp($cid, $sess[mid]);
	$sess[email] = $member[email];
	$level = $member[grplv];
}

//$level = $sess[level];
if ($ici_admin) $level = 99;

if ($board[permission_write] > $level+0) {
	if ($board[permission_write] < 99 && $board[permission_write] > 0) {
		msg(_("로그인 하신 후에 등록하실 수 있습니다."), "../member/login.php?rurl=$_SERVER[REQUEST_URI]");
	} else {
		msg(_("작성권한이 없습니다."), -1);
	}
	exit;
}

if (!$_GET[no]) {
    $mode = "write";
    if($board[content_html]) {
        $data[content] = $board[content_html];
    }
}
else $mode = $_GET[mode];

if ($_GET[mode] == "reply") {
	$data = $m_board->getBoardInfo($cid, $_GET[board_id], $_GET[no]);
	$data[subject] = "[RE] ".$data[subject];
	$data[content] = "<p>====================================="._("원글")."=====================================<br/>".$data[subject]."<br>".$data[content]."</br>====================================="._("원글")."=====================================</p>";
}

if ($board[on_category]) {
	$board[category] = array_notnull(explode(",", $board[category]));
	if (!$board[category]) $board[on_category] = 0;
}

$deco[color] = array("red" => _("적색"),"green" => _("녹색"),"pink" => _("분홍"),"blue" => _("파랑"));

$deco['font-size'] = array("8pt","9pt","10pt","11pt","12pt","13pt","14pt");

if ($_GET[mode] == "reply") {

} else {
    $data[secret] = ($board[on_secret] == "1") ? "1":"0";
}

if ($data[secret]) $checked[secret] = "checked";

$tpl->assign($data);
$tpl->assign('board',$board);
$tpl->define('tpl',"board/{$board[board_skin]}/write.htm");
$tpl->assign('board_list',$board_id_list);
$tpl->assign('board_id',$board[board_id]);
$tpl->print_('tpl');

?>
