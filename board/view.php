<?

include "../_header.php";
include "../lib/class.page.php";

$m_board = new M_board();
$m_member = new M_member();

if ($_POST[board_id]) $_GET[board_id] = $_POST[board_id];
if ($_POST[no]) $_GET[no] = $_POST[no];
if ($_POST[password]) {
   $password = passwordCommonEncode($_POST[password]);
   $_GET[password] = $password;
}

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

if ($board[permission_read] > $level+0) {
   msg(_("열람권한이 없습니다."), -1);
   exit;
}

$r_file = array();
$r_file = $m_board->getBoardFileData($cid, $_GET[no], $_GET[board_id]);

$data = $m_board->getBoardInfo($cid, $_GET[board_id], $_GET[no]);
$data[writer] = $data[$board[writer_type]];
if (!$data[writer]) $data[writer] = $data[name];
$data[r_file] = $r_file;

## 패스워드 인증
$r_password = array();
$r_mid = array();
$r_password[] = $data[password];
$r_mid[] = $data[mid];

if ($data[thread]) {
	$depth = strlen($data[thread])/3;

	for ($i=0; $i<$depth; $i++) {
		$thread = substr($data[thread], 0, $i*3);

		$tableName = "exm_board";
		$addWhere = "where cid = '$cid' and board_id = '$_GET[board_id]' and main = '$data[main]' and thread like '$thread%'";
		$tmp = $m_board->getCustomerServiceInfo($cid, $tableName, $addWhere);

		if ($tmp[password]) $r_password[] = $tmp[password];
		if ($tmp[mid]) $r_mid[] = $tmp[mid];
	}
}

if (!$ici_admin && $data[secret]) {
	if (!$_GET[password] && $sess[mid]) {
		if (!in_array($sess[mid], $r_mid)) {
			msg(_("회원님은 열람권한이 없습니다."), -1);
			exit;
		}
	} else if ($data[password] && !in_array($_GET[password], $r_password)) {
		if (strpos($_SERVER[HTTP_REFERER], "board/list.php")) {
			msg(_("패스워드가 일치하지 않습니다."), -1);
		} else {
			msg(_("비밀글입니다.")."\\n"._("패스워드가 일치하지 않습니다."), "list.php?board_id=$data[board_id]");
		}
		exit;
	}
}

if ($board[permission_list] > $level+0) $deny['list'] = 1;
if ($board[permission_write] > $level+0) $deny[write] = 1;
if ($board[permission_reply] > $level+0 || $data[notice]) $deny[reply] = 1;

### 수정 및 삭제권한
if ($ici_admin || ($data[mid] && $data[mid] == $sess[mid])) {
	$data[link_del]= "<a href='indb.php?mode=del&board_id=$data[board_id]&no=$data[no]' class='btn btn-primary' onclick='return confirm(\""._("정말 삭제하시겠습니까?")."\")'>"._("삭제")."</a>";
} else if (!$data[mid]) {
	$data[link_del]= "<a href='javascript:void(0)' class='btn btn-primary' onclick='layerPw(this,\"".$data[no]."\")'>"._("삭제")."</a>";
} else {
	$deny[del] = 1;
}

$tableName2 = "exm_board";
$addColumn = "set hit = hit+1";
$addWhere2 = "where cid='$cid' and board_id='$_GET[board_id]' and no='$_GET[no]'";
$m_board->setCustomerService("update", $tableName2, $addColumn, $addWhere2);

$data[hit]++;

$tpl->assign($data);
$tpl->assign('board',$board);
$tpl->define('tpl',"board/{$board[board_skin]}/view.htm");
$tpl->assign('board_list',$board_id_list);
$tpl->assign('board_id',$board[board_id]);
$tpl->print_('tpl');

?>
