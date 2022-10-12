<?

include "../_header.php";
include "../lib/class.page.php";

$m_etc = new M_etc();

### 종료된 이벤트 리스트
$now = date("Y-m-d");
$addWhere = "where cid='$cid' and edate and edate < '{$now}'";
$orderby = "order by eventno desc";
$list = $m_etc->getEventList($cid, $addWhere, $orderby);
### 진행중인 이벤트 리스트
$addWhere2 = "where cid='$cid' and sdate <= '{$now}' and (!edate or edate >= '{$now}')";
$list2 = $m_etc->getEventList($cid, $addWhere2, $orderby);

//이벤트번호가 없을 경우 기본적으로 진행중인 이벤트번호를 조회
if (!$_GET[eventno]) $_GET[eventno] = $list2[0][eventno];

### 이벤트 내용
$data = $m_etc->getEventInfo($cid, $_GET[eventno]);

if (!$_GET[eventno]) $data['header'] = "";
else if (!$data) $data['header'] = _("존재하지 않는 이벤트");
else if ($data[edate] != "0000-00-00" && $data[edate] < date("Y-m-d")) $data['header'] = _("종료된 이벤트");
else if ($data[sdate] != "0000-00-00" && $data[sdate] > date("Y-m-d")) $data['header'] = _("진행예정인 이벤트");
else if ($data[sdate] <= date("Y-m-d") && ($data[edate] == "0000-00-00" || $data[edate] >= date("Y-m-d"))) $data['header'] = _("진행중인 이벤트");

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

    foreach ($list2 as $k=>$v) {
        $r_ct = $v ;
        if ($r_ct[eventno] == $_GET[eventno])
            $Event_contents = $list2[$k][contents];
        }

$tpl->assign('contents',$Event_contents);

$tpl->assign('list',$list2);
$tpl->print_('tpl');
//debug($tpl);
?>