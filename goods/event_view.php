<?

include "../_header.php";
include "../lib/class.page.php";

$m_etc = new M_etc();
$now = date("Y-m-d");
### 진행중인 이벤트 리스트
$addWhere2 = "where cid='$cid' and sdate <= '{$now}' and (!edate or edate >= '{$now}')";
$list2 = $m_etc->getEventList($cid, $addWhere2, $orderby);

//이벤트번호가 없을 경우 기본적으로 진행중인 이벤트번호를 조회
if (!$_GET[eventno]) $_GET[eventno] = $list2[0][eventno];

### 이벤트 내용
$data = $m_etc->getEventInfo($cid, $_GET[eventno]);

if($now > $data[edate]) $data[end_event] = "Y";

$selected[end_event][$_GET[eventno]+0] = "selected";
$selected[event][$_GET[eventno]+0] = "selected";

### 코멘트처리
if ($data[use_comment]) {
	### 코멘트추출
	$r_comment = array();
	$addWhere3 = "where a.cid = '$cid' and a.eventno = '$_GET[eventno]' and !hidden";
	$orderby2 = "order by a.no desc";
	$data2 = $m_etc->getEventCommentList($cid, $addWhere3, $orderby2);
	
	foreach ($data2 as $k=>$v) {
		$r_comment[] = $v;
	}
	
	$tpl->assign("comment",$r_comment);

	### 작성권한체크
	$permission_write = ($sess) ? 1 : 0;
	
	if ($sess) {
		$chk = $m_etc->getEventCommentCheck($cid, $sess[mid], $_GET[eventno]);
		if ($chk[eventno]) $permission_write = 0;
	}
}

//이벤트 코멘트
$m_etc = new M_etc();

$where[] = "eventno='$_GET[eventno]'";
$where[] = "!hidden";

$db_table = "exm_event_comment";

$pg = new Page($_GET[page], 10);
$pg->setQuery($db_table, $where, "regdt desc");
$pg->exec();

$res = $db->query($pg->query);
while ($tmp = $db->fetch($res)) {
   $loop[] = $tmp;
}

### 작성권한체크
$permission_write = ($sess) ? 1 : 0;

if ($sess) {
   $chk = $m_etc->getEventCommentCheck($cid, $sess[mid], $_GET[eventno]);
   if ($chk[eventno]) $permission_write = 0;
}

$tpl->assign($data);
$tpl->assign('loop',$loop);
$tpl->assign('list',$list2);
$tpl->assign('pg',$pg);
$tpl->print_('tpl');

?>