<?
include "../_header.php";
$m_attend = new M_attend_event();

$sAttend = "N";
$year = date("Y");
$month = date("m");
//$month = "08";
$day = $year ."-". $month ."-01";
//debug($day);

# 로그인체크.
chkMember();

# 출석체크 이벤트 진행중인지 체크
$attend = $m_attend->getInfo($cid);
//debug($attend);

if ($attend) {
	if ($attend[sdate] <= $day && $attend[edate] >= $day) 
		$sAttend = "Y";
	else
		$sAttend = "N";
}
else {
	$sAttend = "N";
}
//debug($sAttend);

# 현재 달 출석 스탬프수 
$userTakeCnt = $m_attend->getUserTakeCntThisMonth($cid, $sess[mid]);
//debug($userTakeCnt);

# 출석한 날짜 체크
$a_list = $m_attend->getUserTakeListThisMonth($cid, $sess[mid]);
//debug($a_list);
$user_attend_list = array();
foreach ($a_list as $key => $value) {
	//$aDay = date('d', strtotime($value[regist_date]));
	$aDay = date('j', strtotime($value[regist_date]));
	$user_attend_list[$aDay] = 'Y';
}
//debug($user_attend_list);

$loop = array();

$startIndex = date('w', strtotime($day));
$endDay = date("t", strtotime($day));
//debug($startIndex);
//debug($endDay);

for ($i=0; $i < $startIndex; $i++) { 
	$loop[$i] = "NOT";
}
//debug($loop);

for ($i=1; $i <= $endDay; $i++) {
	if ($user_attend_list[$i] == 'Y')
		$loop[] = "CHK";
	else
		$loop[] = $i;
}
//debug($loop);

/*
$blankIndex = count($loop) % 7;
for ($i=$blankIndex; $i < 7; $i++) { 
	$loop[$i] = "NOT";
}
debug($loop);
*/

//$tpl->assign("pg",$pg);
$tpl->assign("loop",$loop);
$tpl->print_('tpl');

?>