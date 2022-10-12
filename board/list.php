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

if ($board[on_category]) {
	$board[category] = explode(",", $board[category]);
}

if ($sess[mid]) {
	$member = $m_member->getInfoWithGrp($cid, $sess[mid]);
	$sess[email] = $member[email];
	$level = $member[grplv];
}

//$level = $sess[level];
if ($ici_admin) $level = 99;

if ($board[permission_list] > $level+0) {
	msg(_("리스트 열람권한이 없습니다."), -1);
	exit;
}

if ($board[permission_write] > $level+0) $deny[write] = 1;
if ($board[permission_read] > $level+0) $deny[read] = 1;
if ($sess_admin[super]) {
	$deny[write] = 0;
	$deny[read] = 0;
}

$where[] = "cid = '$cid'";
$where[] = "board_id = '$board[board_id]'";

if ($_GET[category]) $where[] = "category = '$_GET[category]'";
$selected[category][$_GET[category]] = "selected";

if ($_GET[search][word]) {
	$searchflds = array();

	foreach ($_GET[search] as $k=>$v) {
		if ($k == "word") continue;
		$searchflds[] = $k;
		$checked[search][$k] = "checked";
	}

	if (!$searchflds) $searchflds = array($board[writer_type], "subject", "content");
	$searchflds = implode(",", $searchflds);
	$where[] = "concat($searchflds) like '%{$_GET[search][word]}%'";

	//검색 조건이 select 로 skin 작업이 된경우 (pretty 의 dbp 공지사항 게시판)     20150810    chunter
	if ($_GET[search_select]) $where[] = "$_GET[search_select] like '%{$_GET[search][word]}%'";
}

$db_table = "exm_board a";

//20150721 / minks / 모바일에서 사용
if ($_GET[mobile_type] == "Y") {
	foreach ($where as $k => $v) {
		if ($v) $where_v .= $v." and ";
	}

	list($board[num_per_page]) = $db->fetch("select count(*) from ".$db_table." where ".substr($where_v, 0, -4), 1);
	$board[board_skin] = "default";
}

// 답글별 순서 정렬 추가 210705 jtkim
$pg = new Page($_GET[page], $board[num_per_page], "default", "board-list-pagination");
// $pg->field = "*,(select fileno from exm_board_file where pno = a.no limit 1) file";
// $pg->setQuery($db_table, $where, " regdt desc");
$pg->field = "*,(select fileno from exm_board_file where pno = a.no limit 1) file, if (length(thread) > 1,(select no from exm_board where main = a.main and thread = left(a.thread,length(a.thread)-3) limit 1),'') pno";
$pg->setQuery($db_table,$where,"notice,main,thread");
$pg->exec();
$res = &$pg->resource;

$loop = array();

while ($data = $db->fetch($res)) {
	if ($data[thread]) $data[depth] = strlen($data[thread])/3;

	$data[link_view]= "<a href='view.php?board_id=$board[board_id]&no=$data[no]'>";

	if ($data[secret] && !$ici_admin) {
		$r_mid = array();
		$r_mid[] = $data[mid];

		if ($data[thread]) {
			$depth = strlen($data[thread])/3;

			for ($i=0; $i<$depth; $i++) {
				$thread = substr($data[thread], 0, $i*3);

				$tableName = "exm_board";
				$addWhere = "where cid = '$cid' and board_id = '$board[board_id]' and main = '$data[main]' and thread like '$thread%'";
				$tmp = $m_board->getCustomerServiceInfo($cid, $tableName, $addWhere);

				if ($tmp[mid]) $r_mid[] = $tmp[mid];
			}
		}

		if ($sess[mid] && in_array($sess[mid], $r_mid)) { //상위작성자인 회원의 경우 무조건 읽음

		} else if ($data[mid] && $data[mid] != $sess[mid]) { //회원이 쓴 비밀글 & 내가 쓴 비밀글이 아니면
			$data[link_view]= "<a href='javascript:void(0)' onclick='alert(\""._("회원비밀글입니다.")."\\n"._("작성자와 관리자만 확인이 가능합니다.")."\")'>";
		} else if (!$data[mid]) { //비회원이쓴비밀글
			$data[link_view]= "<a href='javascript:void(0)' onclick='layerPw(this,\"".$data[no]."\")'>";
		}
	}

	if ($deny[read]) $data[link_view]= "<a href='javascript:void(0)' onclick='alert(\""._("열람권한이 없습니다.")."\")'>";
	$data[writer] = $data[$board[writer_type]];
	if (!$data[writer]) $data[writer] = $data[name];

	if ($board[subject_length]) $data[subject] = strcut($data[subject], $board[subject_length]);

	$data[summary] = str_replace("\r\n", "<BR>", $data[summary]);
	$loop[] = $data;
}
//debug($loop);
$subject = $loop[0][subject];
//debug($subject);
$tpl->assign('loop',$loop);
$tpl->assign('search',$_GET[search]);
$tpl->assign('subject',$subject);
$tpl->assign('pg',$pg);
$tpl->assign('board',$board);
$tpl->assign('board_list',$board_id_list);
$tpl->assign('board_id',$board[board_id]);
$tpl->define('tpl',"board/{$board[board_skin]}/list.htm");
$tpl->print_('tpl');

?>
