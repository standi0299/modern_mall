<?

include "../_header.php";

chkMember();
$m_emoney = new M_emoney();

### 회원정보추출
$data = f_getMemeberData();

//20170628 / minks / 적립금 조회를 위해 추가
$myemoney = $m_emoney->getSumEmoneyLog($cid, $sess[mid]);

//현재 날짜를 2013-12-12 형태로 받아와서 년 월 일 별로 나눠서 저장합니다.
$NowTime = date("Ymd");

/*
$nowYear   = substr($NowTime, 0, 4);
$nowMonth  = substr($NowTime, 5, 2);
$nowDay    = substr($NowTime, 8, 2);

//이용갱신일 날짜를 년 월 일 별로 나눠서 저장합니다.
$upYear    = substr($data[fix_member_update_date], 0, 4);
$upMonth   = substr($data[fix_member_update_date], 5, 2);
$upDay     = substr($data[fix_member_update_date], 8, 2);
//년 월 일 별로 나눈 값을 붙여서 2013년 12월 12일 형태로 만듭니다.
$updateDay = $upYear."년 ".$upMonth."월 ".$upDay."일";

//이용만료일 날짜를 년 월 일 별로 나눠서 저장합니다.
$exYear    = substr($data[fix_member_expire_date], 0, 4);
$exMonth   = substr($data[fix_member_expire_date], 5, 2);
$exDay     = substr($data[fix_member_expire_date], 8, 2);
//년 월 일 별로 나눈 값을 붙여서 2013년 12월 12일 형태로 만듭니다.
$expireDay = $exYear."년 ".$exMonth."월 ".$exDay."일";

//strtotime함수를 사용해서 만료일과 현재날짜의 차이값을 구합니다.
$RemainTime = floor((strtotime($data[fix_member_expire_date]) - strtotime($NowTime) )/86400);
*/

//무한정액회원 확인 2014.11.24 by kdk
if (substr($data[fix_member_expire_date], 0, 4) == "9999") $data[fix_member_unlimited] = "Y";

$fix_member_update_date = "-";
if ($data[fix_member_update_date]) $fix_member_update_date = date("Y"._("년")." m"._("월")." d"._("일"), strtotime($data[fix_member_update_date]));

$RemainTime = "0";
$fix_member_expire_date = "-";
if ($data[fix_member_expire_date]) {
	$RemainTime = floor((strtotime($data[fix_member_expire_date]) - strtotime($NowTime)) / 86400);
	$fix_member_expire_date = date("Y"._("년")." m"._("월")." d"._("일"), strtotime($data[fix_member_expire_date]));
}

//최근 결제내역 관련된 DB값 추출
//13.12.31 kjm
$query = "select * from tb_fix_member_history where cid = '$cid' and m_id='$sess[mid]' and account_flag = 'Y'";
$db->query("set names utf8");
$res = $db->query($query);

while ($Payment_data = $db->fetch($res)) {
	$date = explode('/', $Payment_data[process_month_day]);
	
	$loop[] = array(
		'all_data' => $Payment_data,
		'month' => $date[0],
		'day' => $date[1]);
}

$tpl->assign('RemainTime', $RemainTime);
$tpl->assign('updateDay',  $fix_member_update_date);
$tpl->assign('expireDay', $fix_member_expire_date);
$tpl->assign('myemoney',$myemoney);
if ($data) $tpl->assign($data);
if ($loop) $tpl->assign('loop',$loop);
$tpl->print_('tpl');

?>